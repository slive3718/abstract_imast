<?php
namespace App\Models;

use CodeIgniter\Database\ConnectionInterface;
use CodeIgniter\Model;
use CodeIgniter\Validation\ValidationInterface;
use PhpOffice\PhpWord\Style\Paper;

class ModeratorAcceptanceModel extends Model
{
    protected $table = 'moderator_acceptance';

    protected $allowedFields;
    protected $primaryKey = 'id';
    // protected $allowedFields = ['title', 'description'];
    protected $returnType = 'object'; 
    private $error;

    public function __construct()
    {
        parent::__construct();
        $this->allowedFields = $this->db->getFieldNames($this->table);
    }


    public function presentation_do_upload($file)
    {
        $abstract_id = $_POST['abstract_id'] ?? null;
        $author_id = $_SESSION['user_id'] ?? null;

        if (!$abstract_id || !$author_id) {
            return (['status' => 'error', 'msg' => 'Invalid abstract or author ID.']);
        }

        $allowed_file_type = ['ppt', 'pptx'];

        if(!in_array(strtolower( $file->getExtension()), $allowed_file_type)){
            return (['status' => 'error', 'msg' => 'Invalid file type. Only PDF files are allowed.']);
        }

        if (!$file->isValid() || $file->hasMoved()) {
            return (['status' => 'error', 'msg' => 'Invalid or already moved file.']);
        }

        $abstract = (new PapersModel())->asArray()->find($abstract_id);

        // Construct file and path details
        $file_name = $file->getName();
        $uploader_name = $_SESSION['name'] ?? 'unknown';
        $uploader_surname = $_SESSION['surname'] ?? 'unknown';
        $newName = $uploader_surname."_".$abstract['custom_id']."_" . str_replace(' ', '_', $file_name);
        $filePath = "/uploads/acceptance/presentation/{$abstract_id}/author/{$author_id}/";
        $savePath = FCPATH . $filePath;

        // Create directory if it does not exist
        if (!is_dir($savePath) && !mkdir($savePath, 0777, true)) {
            return (['status' => 'error', 'msg' => 'Failed to create directory for upload.']);
        }else{
            $this->clearDirectory($savePath); // Ensure only one file exists
        }

        // Move the uploaded file
        if (!$file->move($savePath, $newName)) {
            return (['status' => 'error', 'msg' => 'File upload failed.']);
        }

        try {
            $updateFields = [
                'presentation_original_name' => $file_name,
                'presentation_saved_name' => $newName,
                'presentation_save_path' => $savePath,
                'presentation_file_path' => $filePath,
            ];

            $query = $this->db->table($this->table);
            $result = $query->where(['author_id' => $author_id, 'abstract_id' => $abstract_id])
                ->set($updateFields)
                ->update();

            if ($result) {
                return json_encode(['status' => 'success', 'msg' => 'Presentation uploaded successfully.', 'data' => $updateFields]);
            } else {
                return json_encode(['status' => 'error', 'msg' => 'Database update failed.']);
            }
        } catch (\CodeIgniter\Database\Exceptions\DatabaseException $e) {
            return json_encode(['status' => 'error', 'msg' => 'Database error: ' . $e->getMessage()]);
        }
    }

    private function clearDirectory($directory)
    {
        if (!is_dir($directory)) {
            return false;
        }

        $files = array_diff(scandir($directory), ['.', '..']);
        foreach ($files as $file) {
            $filePath = $directory . DIRECTORY_SEPARATOR . $file;

            if (is_dir($filePath)) {
                $this->clearDirectory($filePath); // Recursive call for subdirectories
                rmdir($filePath); // Remove the empty directory
            } else {
                unlink($filePath); // Remove the file
            }
        }

        return true;
    }

    function checkAcceptance($scheduler_id){
        try {
             $result = $this->where(['scheduler_id'=>$scheduler_id, 'author_id'=>session('user_id')])->first();
             if(!$result)
                 return ['status'=>'failed', 'msg'=> 'No result found!'];
             if($result->breakfast_attendance == '')
                 return  ['status'=>'failed', 'msg'=> 'breakfast status empty'];
            if($result->is_session_previewed == '')
                return  ['status'=>'failed', 'msg'=> 'session details preview incomplete'];
            if($result->acceptance_confirmation !== ''){
                return ['status'=>'success', 'msg'=> 'Finalized'];
            }else{
                return ['status'=>'failed', 'msg'=> 'No acceptance found!'];
            }

        } catch (\CodeIgniter\Database\Exceptions\DatabaseException $e) {
            // Log the error or display an error message
            $error = json_encode('Database error: ' . $e->getMessage());
            return $error;
        }
    }


    function get_accepted_abstracts(){
        $author_papers = $this->db->table('paper_authors')
            ->where('paper_authors.author_id', session('user_id'))
            ->where('paper_authors.author_type', 'author')
            ->where('paper_authors.is_presenting_author', 'Yes')
            ->whereNotIn('paper_authors.id', function ($builder) {
                $builder->select('paper_author_id')->from('removed_paper_authors');
            })
            ->get()
            ->getResultArray();

        foreach ($author_papers as &$paper){
            $paper['author_details'] = (new UserModel())
                ->where('users.id', $paper['author_id'])
                ->join('users_profile up', 'users.id = up.author_id', 'left')
                ->findAll();
            $paper['paper_data'] = (new PapersModel())->find($paper['paper_id']);
            $paper['acceptance_data'] = (new AdminAcceptanceModel())->where('abstract_id', $paper['paper_id'])->first();
            $paper['schedule'] = (new SchedulerSessionTalksModel())->get_talk_schedule_query($paper['paper_id'])->first();
            $paper['room'] = [];
            if($paper['schedule']) {
                $paper['room'] = (new RoomsModel())->find($paper['schedule']['room_id']);
            }
        }
        return $author_papers;
    }

    function get_accepted_panelist_abstracts(){
        $author_papers = $this->db->table('paper_authors')
            ->where('paper_authors.author_id', session('user_id'))
            ->where('paper_authors.author_type', 'panelist')
            ->whereNotIn('paper_authors.id', function ($builder) {
                $builder->select('paper_author_id')->from('removed_paper_authors');
            })
            ->get()
            ->getResultArray();

        foreach ($author_papers as &$paper){
            $paper['author_details'] = (new UserModel())
                ->where('users.id', $paper['author_id'])
                ->where('users.id', $paper['author_id'])
                ->join('users_profile up', 'users.id = up.author_id', 'left')
                ->findAll();
            $paper['paper_data'] = (new PanelistPaperSubModel())->join('papers p', 'panelist_paper_sub.paper_id = p.id', 'left')
            ->where('panelist_id', session('user_id'))
            ->where('paper_id', $paper['paper_id'])
                ->first();
            $paper['acceptance_data'] = (new PanelistPaperSubModel())->join('admin_individual_panel_acceptance aipa', 'panelist_paper_sub.id = aipa.individual_panel_id', 'left')
                ->where('panelist_paper_sub.paper_id', $paper['paper_id'])
                ->where('panelist_paper_sub.panelist_id', $paper['author_id'])
                ->first();
            $paper['schedule'] = (new SchedulerSessionTalksModel())->get_talk_schedule_query($paper['paper_id'])->first();
            $paper['room'] = [];
            if($paper['schedule']) {
                $paper['room'] = (new RoomsModel())->find($paper['schedule']['room_id']);
            }
        }
        return $author_papers;
    }

    function get_merged_papers(){
        $panelists = $this->get_accepted_panelist_abstracts();
        $papers = $this->get_accepted_abstracts();
        $merged = array_merge($panelists, $papers);
        return $merged;
    }


}