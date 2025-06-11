<?php

namespace App\Controllers\reviewer;

use App\Controllers\admin\EmailController;
use App\Controllers\User;
use App\Libraries\MailGunEmail;
use App\Libraries\PhpMail;
use App\Libraries\Upload;
use App\Models\DivisionsModel;
use App\Models\EmailTemplatesModel;
use App\Models\EventsModel;
use App\Models\PaperAssignedReviewerModel;
use App\Models\PaperTypeModel;
use App\Models\PaperUploadsModel;
use App\Models\ReviewerPaperUploadsModel;
use App\Models\SiteSettingModel;
use CodeIgniter\Controller;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Database\BaseConnection;

use App\Models\UserModel;
use App\Models\PapersModel;
use App\Models\PaperAuthorsModel;
use App\Models\ReviewerModel;
use App\Models\AbstractTopicsModel;
// use App\Models\PopulationModel;
use App\Models\AbstractReviewModel;
use App\Models\AbstractFileUploadModel;
// use App\Models\LearningObjectivesModel;

use App\Controllers\admin\Abstracts\AbstractController;
use Config\Mailgun;
use Matrix\Operators\Division;

class ReviewerController extends Controller
{

    private BaseConnection $db;

    public function __construct()
    {
        $this->db = db_connect();
        $this->event_uri = session('event_uri');
        if(session('user_id')){
            $this->user_id = session('user_id');
        }

        if(empty(session('user_type')) || session('user_type') !== 'reviewer'){
            header('Location:'.base_url().'reviewer/logout');
            exit;
        }

        if(empty(session('email')) || session('email') == ''){
            header('Location:'.base_url().'reviewer');
            exit;
        }
    }


    public function index(){
        $event = (new EventsModel())->first();
        $reviewerModel = (new ReviewerModel())->getReviewerAbstracts(session('user_id'), 'regular');
        $divisions = (new DivisionsModel())->findAll();
        $reviewer_abstracts = array();
        foreach($reviewerModel as $reviewer){
             $reviewer['abstracts'] = (new PapersModel())->where('active_status', 1)->find($reviewer['paper_id']);
             $reviewer_abstracts[] = $reviewer;
        }
//        print_r($reviewer_abstracts);exit;
        $header_data = [
            'title' => 'Reviewer'
        ];

        $data = [
            'event'=> $event,
            'reviewer_abstracts' => $reviewer_abstracts,
            'divisions'=>$divisions
        ];
        return
            view('reviewer/common/header', $header_data).
            view('reviewer/abstract_list', $data).
            view('reviewer/common/footer')
            ;
    }

    public function getAllReviewerAbstracts(){
        $reviewerModel = (new ReviewerModel())->getReviewerAbstracts(session('user_id'), 'regular', 0);

        $reviewer_abstracts = array();
        foreach($reviewerModel as $reviewer){
            $reviewer['abstracts'] = (new PapersModel())->where('active_status', 1)->find($reviewer['paper_id']);
//
            if(isset($reviewer['abstracts'])){
                $reviewer['abstracts_submitter']= (new UserModel())->find($reviewer['abstracts']->id);
                $reviewer['division'] = (new DivisionsModel())->where('division_id', $reviewer['abstracts']->division_id)->first();
            }
            $reviewer['reviews'] = (new AbstractReviewModel())->where(array('abstract_id'=> $reviewer['paper_id'], 'reviewer_id'=>session('user_id')))->first();
            $reviewer_abstracts[] = $reviewer;
        }

        return json_encode(['status'=>200, 'message'=>'', 'data'=>$reviewer_abstracts]);
    }



    function getNextBiggerValue($array, $target) {
    $nextBigger = null;
    foreach ($array as $value) {
        if ($value > $target && ($nextBigger === null || $value < $nextBigger)) {
            $nextBigger = $value;
        }
    }
        return $nextBigger;
    }

    public function getAbstractReview($event_uri, $review_id){
        // print_r($review_id);exit;
        $abstractReviewModel = (new AbstractReviewModel());
        echo json_encode($abstractReviewModel->find($review_id));
    }


    public function reviewAbstract($abstract_id){

        $event = (new EventsModel())->first();
        $PaperTypeModel = (new PaperTypeModel());
        $PaperModel = (new PapersModel());
        $DivisionModel = (new DivisionsModel());
        $PaperUploadsModel = (new PaperUploadsModel());
        $AbstractReviewModel = (new AbstractReviewModel());
        $ReviewerPaperUploads = (new ReviewerPaperUploadsModel());

        $abstracts = $PaperModel
            ->select($PaperModel->getTable().'.*, '.$PaperTypeModel->getTable().'.name as type_name,'. $DivisionModel->getTable().'.name as division_name')
            ->join($PaperTypeModel->getTable(), $PaperModel->getTable().'.type_id = '.$PaperTypeModel->getTable().'.type', 'left')
            ->join($DivisionModel->getTable(), $PaperModel->getTable().'.division_id = '.$DivisionModel->getTable().'.division_id', 'left')
            ->where('active_status', 1)->find($abstract_id);

        $abstracts->file_uploads = $PaperUploadsModel->where('paper_id', $abstract_id)->orderBy('id', 'desc')->findAll();

        $abstractReviewData =(new AbstractReviewModel())
            ->where(['abstract_id'=>$abstract_id, 'reviewer_id'=>session('user_id')])
            ->findAll();

        $header_data = [
            'title' => 'Review Submission Detail'
        ];

        $abstract_reviews = $AbstractReviewModel->where(['reviewer_id'=>session('user_id'),'abstract_id'=>$abstract_id])->first();
        $abstract_reviewer_uploads = $ReviewerPaperUploads->where(['paper_id'=>$abstract_id, 'reviewer_id'=> session('user_id')])->first();

        $data = [
            'event'=> $event,
            'abstracts' => $abstracts,
            'abstract_id' => $abstract_id,
            'reviewer_id'=>session('user_id'),
            'abstract_reviews'=>$abstract_reviews,
            'abstract_reviewer_uploads'=>!empty($abstract_reviewer_uploads)?$abstract_reviewer_uploads:[]
        ];

        if(!empty($abstractReviewData)){
            $data['abstract_review_data'] = $abstractReviewData[0];
        }
        
        return
            view('reviewer/common/header', $header_data).
            view('reviewer/review_abstract', $data).
            view('reviewer/common/footer')
            ;
    }

    public function addReviewData(){

      $SiteSettingsModel = (new SiteSettingModel());
        $sendMail = (new PhpMail());
        $isApproved = 0;
        if(isset($_POST['final_approval'])){
            $isApproved = $_POST['final_approval'];
        }
        $field_array = array(
            'abstract_id'=> $_POST['abstract_id'],
            'reviewer_id'=>$_POST['reviewer_id'],
            'commercialism'=>$_POST['commercialism'],
            'commercialism_editable'=>$_POST['commercialismEdit'],
            'operations'=>$_POST['operations'],
            'marketing_score'=>$_POST['marketingScore'],
            'research_score'=>$_POST['researchScore'],
            'professional_level'=>$_POST['professionalLevel'],
            'originality_score'=>$_POST['originalityScore'],
            'sufficiency_score'=>$_POST['sufficiencyScore'],
            'readability_score'=>$_POST['readabilityScore'],
            'artwork_score'=>$_POST['artworkScore'],
            'composite_score'=>$_POST['compositeScore'],
            'average_score'=>$_POST['averageScore'],
            'suggested_revision_comment'=>$_POST['suggested_revision_comment'],
            'required_revision_comment'=>$_POST['required_revision_comment'],
            're_review_comment'=>$_POST['re_review_comment'],
            'is_approved' =>$isApproved,
            'date_time'=>date('Y-m-d H:i:s')
        );
        // other_topic2 total_score is_case_report with_conflict_of_interest is_abstract_qualified is_requirements_meet comments_for_committee comments_for_author 

        $abstractReviewModel = (new AbstractReviewModel());
        $PaperModel = (new PapersModel());
        $paper = $PaperModel->find($_POST['abstract_id']);
        $paperAssignedReviewerModel = (new PaperAssignedReviewerModel());
        $ReviewModel = (new AbstractReviewModel());
        $UsersModel = (new UserModel());
        $paperReviewers = $paperAssignedReviewerModel
            ->select($paperAssignedReviewerModel->getTable().'.*, '.$UsersModel->getTable().'.email as reviewer_email')
            ->join($UsersModel->getTable(), $paperAssignedReviewerModel->getTable().'.reviewer_id = '. $UsersModel->getTable().'.id', 'left')
            ->where([
                'paper_id' => $_POST['abstract_id'],
                'reviewer_type' => 'regular',
                'is_deleted' => '0'
            ])->findAll();

        if (!empty($field_array)) {
                if(!empty($abstractReviewModel->where(array('abstract_id'=>$_POST['abstract_id'], 'reviewer_id'=>$_POST['reviewer_id']))->get())){
                    $where = ['abstract_id'=>$_POST['abstract_id'], 'reviewer_id'=>$_POST['reviewer_id']];
                    $abstractReviewModel->where($where)->set($field_array)->update();
                    return json_encode(array('status'=>200, 'message'=>'Review successfully updated.'));
                }else{
                    $siteSettings = $SiteSettingsModel->where('name', 'reviewers_reviews_to_close')->first();
                    $abstractReviews = ($abstractReviewModel->where('abstract_id',$_POST['abstract_id']))->findAll();
                    if(count($abstractReviews) >= $siteSettings['value']){
                        return json_encode(array('status' => 201, 'message' => "Regular Review Task Closed – Paper has been reviewed three times."));
                    }else {
                        $abstractReviewModel->insert($field_array);
                        $siteSettings = $SiteSettingsModel->where('name', 'reviewers_reviews_to_close')->first();
                        $abstractReviews = ($abstractReviewModel->where('abstract_id',$_POST['abstract_id']))->findAll();
                        if(count($abstractReviews) >= $siteSettings['value']){

                            $emailController = New EmailController();
                            foreach ($paperReviewers as $reviewers){
                                $reviewed = $ReviewModel->where(['reviewer_id'=> $reviewers['reviewer_id'], 'abstract_id'=>$_POST['abstract_id']])->findAll();
                                if(!$reviewed){
                                    $emailController->sendCustomEmailReviewer(8, $reviewers['reviewer_id'], $_POST['abstract_id'], strip_tags($paper->title));
                                }
                            }
                        }
                        return json_encode(array('status' => 200, 'message' => 'Review successfully added.'));
                    }
                }
            } else {
                return json_encode(array('status'=>500, 'message'=>'Error!'));
            }
    }


    function uploadReviewerFile(){
        $post = $this->request->getPost();
        $ReviewerPaperUploadsModel = new ReviewerPaperUploadsModel();

// Ensure file is uploaded
        if (!$this->request->getFile('uploadFile')->isValid()) {
            return json_encode(['status' => 500, 'message' => 'No file uploaded.', 'data' => '']);
        }

// Get file details
        $uploadFile = $this->request->getFile('uploadFile');
        $filename = $uploadFile->getName();
        $file_type = $uploadFile->getClientMimeType();
        $file_size = $uploadFile->getSize();
        $file_extension = $uploadFile->getExtension();

// Define allowed file extensions
        $allowed = ['pdf', 'doc', 'docx', 'pptx'];

// Check if file extension is allowed
        if (!in_array($file_extension, $allowed)) {
            return json_encode(['status' => 401, 'message' => 'File type not allowed!']);
        }

// Generate custom file name
        $customName = $post['paper_id'] . '_' . date('mdY') . '_' . $filename;

// Define file paths
        $filePath = "/uploads/review/" . $post['paper_id'] . "/";
        $savePath = FCPATH . $filePath;

// Perform file upload
        $upload = new Upload();
        $uploadResult = $upload->doUpload($uploadFile, $filePath, $savePath, $customName);

// Check if upload was successful
        if ($uploadResult !== 'error' && $uploadResult !== 'invalid' && $uploadResult !== 'Missing File') {
            try {
                // Insert file details into database
                $insertArray = [
                    'paper_id' => $post['paper_id'],
                    'file_preview_name' => $customName,
                    'file_format' => $file_type,
                    'file_size' => $file_size,
                    'reviewer_id' => session('user_id'),
                    'file_path' => $filePath,
                    'file_extension' => $file_extension,
                    'file_name' => $uploadResult['new_name'], // Assuming this is the file name saved during upload
                    'created_at' => date('Y-m-d H:i:s')
                ];
                $result = $ReviewerPaperUploadsModel
                    ->insert($insertArray);

                if(is_int($result)){
                return json_encode(['status' => 200, 'message' => 'Success', 'data' => $insertArray]);
                }
            } catch (\Exception $e) {
                $this->response->setStatusCode(500); // Internal Server Error
                return json_encode(['status' => 500, 'message' => $e->getMessage(), 'data' => '']);
            }
        }

// Handling file upload errors
        return json_encode(['status' => 500, 'message' => 'Error uploading file.', 'data' => '']);

    }

    function getNextReviewAbstract($current_abstract_id){
        $abstractReviews = (new AbstractReviewModel())->where(['reviewer_id'=>session('user_id')])->findAll();

        $reviewer_abstracts = array();
        if(!empty($abstractReviews)){
            foreach ($abstractReviews as $reviews){
                $reviewer_abstracts[] = $reviews['abstract_id'];
            }
        }

        $nextBiggerValue = $this->getNextBiggerValue($reviewer_abstracts, $current_abstract_id);
        echo ($nextBiggerValue);
    }

    public function DeclineReviewerAbstract(){
        $post= $this->request->getPost();
        $PaperAssignedReviewerModel = (new PaperAssignedReviewerModel());
        $result = $PaperAssignedReviewerModel
            ->set('is_declined', "1")
            ->where(['paper_id'=>$post['abstract_id'], 'reviewer_id'=>session('user_id'), 'reviewer_type'=>'regular'])
            ->update();

        if ($result !== false) {
            return json_encode(['status' => 200, 'message' => 'Update success!', 'data' => '']);
        } else {
            return json_encode(['status' => 500, 'message' => 'Update failed!', 'data' => '']);
        }

    }

    public function checkAbstractReviewsCount(){
        $SiteSettingsModel = (new SiteSettingModel());
        $abstractReviewModel = (new AbstractReviewModel());

        try {
            $siteSettings = $SiteSettingsModel->where('name', 'reviewers_reviews_to_close')->first();
            $abstractReviews = ($abstractReviewModel->where('abstract_id', $_POST['abstract_id']))->findAll();
            if (count($abstractReviews) >= $siteSettings['value']) {
                return json_encode(array('status' => 201, 'message' => "Regular Review Task Closed – Paper has been reviewed three times."));
            } else {
                return json_encode(array('status' => 200, 'message' => "Regular Review Task Closed – Paper has been reviewed three times."));
            }
        }catch (\Exception $e){
            return json_encode(array('status' => 500, 'message' => "Something went wrong!"));
        }

    }


}
