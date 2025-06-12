<?php

namespace App\Controllers\acceptance;

use App\Controllers\admin\Abstracts\SchedulerController;
use App\Libraries\PhpMail;
use App\Models\AdminAcceptanceModel;
use App\Models\EmailLogsModel;
use App\Models\EmailTemplatesModel;
use App\Models\EventsModel;
use App\Models\LogsModel;
use App\Models\ModeratorAcceptanceModel;
use App\Models\PanelistPaperSubModel;
use App\Models\PaperAuthorsModel;
use App\Models\RoomsModel;
use App\Models\SchedulerModel;
use App\Models\SchedulerSessionTalksModel;
use App\Models\UsersProfileModel;
use CodeIgniter\Controller;
use App\Models\Core\Api;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Database\BaseConnection;

use App\Models\UserModel;
use App\Models\PapersModel;
use App\Models\PopulationModel;
use App\Models\AbstractReviewModel;
use App\Models\AuthorAcceptanceModel;
use App\Models\InstitutionModel;
use App\Models\UserDetailsModel;
use App\Models\RemovedPaperAuthorModel;
use App\Models\InstitutionCitiesModel;
use App\Models\InstitutionCountriesModel;
use App\Models\InstitutionStatesModel;
use App\Models\AuthorPresentationUploadModel;

use App\Controllers\admin\Abstracts\AbstractController;
class ModeratorAcceptanceController extends Controller
{
    private Api $api;
    private BaseConnection $db;

    public function __construct()
    {
       
        $this->db = db_connect();
        if(session('user_id')){
            $this->user_id = session('user_id');
        }else{
            header('Location:'.base_url().'acceptance/logout');
            exit;
        }

        if(empty(session('user_type')) || session('user_type') !== 'acceptance'){
            header('Location:'.base_url().'acceptance/logout');
            exit;
        }
      
        $this->api = new Api();
        if(empty(session('email')) || session('email') == ''){
            header('Location:'.base_url().'acceptance');
            exit;
        }

        helper('general_helpers');
    }


    public function schedules(){
        $schedules = (new SchedulerModel())
            ->select('scheduler_events.*, sr.name as room_name')
            ->select("DATE_FORMAT(session_date, '%Y-%m-%d') as session_date", false)
            ->select("DATE_FORMAT(session_start_time, '%h:%i %p') as session_start_time", false)
            ->select("DATE_FORMAT(session_end_time, '%h:%i %p') as session_end_time", false)
            ->join('scheduler_rooms sr', 'scheduler_events.room_id = sr.id', 'left')
            ->findAll();
        $moderator_event = [];
        foreach ($schedules as $schedule){
            $moderators = json_decode($schedule['session_chair_ids']);
            if(is_array($moderators) && in_array(session('user_id'), $moderators)){
                $moderator_event[] = $schedule;
            }
        }
        if(!$moderator_event)
            return false;
        return $this->response->setJSON(['status'=>'success', 'data'=>$moderator_event]);
    }

    public function acceptance_menu($scheduler_id){
        $event = (new EventsModel())->first();
        if(!$event)
            exit;

        $abstract_details= (new PapersModel())->find($scheduler_id);
        $moderator_acceptance = (new ModeratorAcceptanceModel())->where(['scheduler_id'=>$scheduler_id, 'author_id'=>session('user_id')])->first();
        $abstract_preference =  (new AdminAcceptanceModel())->where('abstract_id', $scheduler_id)->first();
        $header_data = [
            'title' => 'Acceptance Finalize'
        ];
        // print_R($abstract_details);exit;
        $header_data = [
            'title' => 'Acceptance Menu'
        ];

        $data = [
            'event'=> $event,
            'scheduler_id' => $scheduler_id,
            'moderator_acceptance' => $moderator_acceptance,
            'abstract_details' => $abstract_details,
            'abstract_preference' => $abstract_preference,
            'presentation_data_view' => $this->moderator_scheduler_details($scheduler_id)
        ];


        return
            view('acceptance/common/header', $header_data).
            view('acceptance/moderator/acceptance_menu', $data).
            view('acceptance/common/footer')
            ;
    }


    public function acceptance($scheduler_id){
        if(!$this->validate_moderator_event($scheduler_id))
            exit;

        $event = (new EventsModel())->first();
        $acceptanceDetails = (new ModeratorAcceptanceModel())->where(['scheduler_id'=>$scheduler_id, 'author_id'=>session('user_id')])->asArray()->first();

        $header_data = [
            'title' => 'Session Chair Participation'
        ];

        $data = [
            'event'=> $event,
            'scheduler_id' => $scheduler_id,
            'acceptanceDetails' => $acceptanceDetails,
            'abstract_preference' => presentation_preferences(),
            'presentation_data_view' => $this->moderator_scheduler_details($scheduler_id)
        ];
        return
            view('acceptance/common/header', $header_data).
            view('acceptance/moderator/moderator_acceptance', $data).
            view('acceptance/common/footer')
            ;
    }


    public function moderator_scheduler_details($scheduler_id){
        $scheduler_data = (new SchedulerModel())->find($scheduler_id);
        $data = [
            'scheduler_data' => $scheduler_data,
            'room' => (new RoomsModel())->find($scheduler_data['room_id']),
        ];

        return  view('acceptance/common/scheduler_details', $data);
    }

    public function presentation_do_upload(){
        $file = $this->request->getFile('presentation_file');
        return $this->response->setJSON((new AuthorAcceptanceModel())->presentation_do_upload($file));
    }

    public function presentation_upload_delete(){
        $post = $this->request->getPost();
        $author_id = session('user_id');
        $abstract_id = $post['abstract_id'];

        $update_array = [
            'presentation_original_name'=> '',
            'presentation_saved_name'=> '',
            'presentation_save_path'=> '',
            'presentation_file_path'=> '',
        ];

        // Check if a record exists for the given author and abstract
        $authorAcceptanceModel = new AuthorAcceptanceModel();
        $existingRecord = $authorAcceptanceModel->where('author_id', $author_id)
            ->where('abstract_id', $abstract_id)
            ->asArray()->first();

        if(!$existingRecord){
            exit;
        }

        if ($existingRecord) {
            // Construct the absolute file path
            $filePath = FCPATH . $existingRecord['presentation_file_path'] . '/' . $existingRecord['presentation_saved_name'];

            // Check if the file exists
            if (file_exists($filePath)) {
                // Attempt to delete the file
                if($authorAcceptanceModel->update($existingRecord['id'], $update_array)){
                    if (unlink($filePath)) {
                        return $this->response->setJSON(['status'=> 'success', 'message' => 'Upload Deleted successfully']);
                    }
                }
            }else{
                $authorAcceptanceModel->update($existingRecord['id'], $update_array);
                return $this->response->setJSON(['status'=> 'success', 'message' => 'Upload Deleted successfully']);
            }
        }
    }

    public function presentation_upload($abstract_id){
        $event = (new EventsModel())->first();

        $header_data = [
            'title' => 'CV Upload'
        ];

        $acceptanceDetails = (new AuthorAcceptanceModel())->where(['abstract_id'=>$abstract_id, 'author_id'=>session('user_id')])->asArray()->first();
        $data = [
            'event'=> $event,
            'abstract_id' => $abstract_id,
            'acceptanceDetails' => $acceptanceDetails,
            'presentation_data_view' => $this->presentation_data_view($abstract_id)
        ];

        return
            view('acceptance/common/header', $header_data).
            view('acceptance/presentation_upload', $data).
            view('acceptance/common/footer')
            ;
    }



    public function save() {
        $post = $this->request->getPost();
        $author_id = session('user_id');
        $scheduler_id = $post['scheduler_id'];
        $acceptance_confirmation = $post['participation'];

        define('ACCEPTED', 1);
        define('REJECTED', 0);

        // Prepare the data for insertion or update
        $date_now = date("Y-m-d H:i:s");
        $data = [
            'acceptance_confirmation' => $acceptance_confirmation,
            'acceptance_confirmation_date' => $date_now,
            'author_id' => $author_id,
            'scheduler_id' => $scheduler_id
        ];

        // Check if a record already exists for the given author and abstract
        $moderatorAcceptance = new ModeratorAcceptanceModel();
        $existingRecord = $moderatorAcceptance->where('author_id', $author_id)
            ->where('scheduler_id', $scheduler_id)
            ->asArray()->first();

        try {
            if ($existingRecord) {
                // If the record exists, update it
                $moderatorAcceptance->update($existingRecord['id'], $data);
                return $this->response->setJSON(['status' => 'success', 'message' => 'Updated successfully']);
            } else {
                // If the record does not exist, insert a new one
                $moderatorAcceptance->insert($data);
                return $this->response->setJSON(['status' => 'success', 'message' => 'Inserted successfully']);
            }
        }catch (\Exception $e){
            return $this->response->setJSON(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function breakfast_attendance($scheduler_id){
//        print_r('test');exit;
        if(!$this->validate_moderator_event($scheduler_id))
            exit;

        $event = (new EventsModel())->first();
        $acceptanceDetails = (new ModeratorAcceptanceModel())->where(['scheduler_id'=>$scheduler_id, 'author_id'=>session('user_id')])->asArray()->first();
        $header_data = [
            'title' => 'Breakfast Attendance'
        ];
        $data = [
            'event'=> $event,
            'scheduler_id' => $scheduler_id,
            'acceptanceDetails' => $acceptanceDetails,
            'abstract_preference' => presentation_preferences(),
            'presentation_data_view' => $this->moderator_scheduler_details($scheduler_id)
        ];
        return
            view('acceptance/common/header', $header_data).
            view('acceptance/moderator/breakfast_attendance', $data).
            view('acceptance/common/footer')
            ;
    }

    public function session_details($scheduler_id){
        if(!$this->validate_moderator_event($scheduler_id))
            exit;

        $event = (new EventsModel())->first();
        $acceptanceDetails = (new ModeratorAcceptanceModel())->where(['scheduler_id'=>$scheduler_id, 'author_id'=>session('user_id')])->asArray()->first();
        $scheduler_event = (new SchedulerModel())->find($scheduler_id);
        $talksModel = (new SchedulerSessionTalksModel());
        $paperModel = (new PapersModel());
        $panelistPaperSubModel = (new PanelistPaperSubModel());
        $scheduler_event['talks'] = $talksModel
            ->join('papers p', 'scheduler_session_talks.abstract_id = p.id', 'left')
            ->where('scheduler_event_id', $scheduler_id)
            ->findAll();

        if(!$scheduler_event['talks'])
            exit;

        foreach ($scheduler_event['talks'] as &$talk){
            if($talk['submission_type'] == 'paper'){
                $talk['info'] = $paperModel
                    ->join('paper_authors pa', 'papers.id = pa.paper_id', 'left')
                    ->join('users u', 'pa.author_id = u.id', 'left')
                    ->join('users_profile up', 'u.id = up.author_id', 'left')
                    ->join('author_abstract_acceptance aaa', 'u.id = aaa.author_id', 'left')
                    ->asArray()->find($talk['abstract_id']);
            }else if($talk['submission_type'] == 'panel'){
                $talk['info'] = $panelistPaperSubModel
                    ->join('papers p', 'panelist_paper_sub.paper_id = p.id', 'left')
                    ->join('users u', 'panelist_paper_sub.panelist_id = u.id', 'left')
                    ->join('users_profile up', 'u.id = up.author_id', 'left')
                    ->join('author_abstract_acceptance aaa', 'u.id = aaa.author_id', 'left')
                    ->find($talk['paper_sub_id'] ?? '');
            }
        }

        $header_data = [
            'title' => 'Biography'
        ];
        $data = [
            'event'=> $event,
            'scheduler_id' => $scheduler_id,
            'acceptanceDetails' => $acceptanceDetails,
            'scheduler_event' => $scheduler_event,
            'abstract_preference' => presentation_preferences(),
            'presentation_data_view' => $this->moderator_scheduler_details($scheduler_id)
        ];

//        print_r($scheduler_event);exit;
        return
            view('acceptance/common/header', $header_data).
            view('acceptance/moderator/session_details', $data).
            view('acceptance/common/footer')
            ;
    }


    public function update_acceptance(){
        $post = $this->request->getPost();
        $author_id = session('user_id');
        $scheduler_id = $post['scheduler_id'];

        $update_array = [];

        if(!empty($post['breakfast_attendance'])){
            $update_array['breakfast_attendance'] = $post['breakfast_attendance'];
        }

        if(!empty($post['author_bio'])){
            $update_array['author_bio'] = $post['author_bio'];
        }

        if(!empty($post['confirm_previewed'])){
            $update_array['is_session_previewed'] = $post['confirm_previewed'];
        }

        // Check if a record already exists for the given author and abstract
        $moderatorAcceptance = new ModeratorAcceptanceModel();
        $existingRecord = $moderatorAcceptance->where('author_id', $author_id)
            ->where('scheduler_id', $scheduler_id)
            ->asArray()->first();

        if(!$existingRecord){
            exit;
        }

        $moderatorAcceptance->update($existingRecord['id'], $update_array);
        return $this->response->setJSON(['status'=> 'success', 'message' => 'Updated successfully']);

    }

    public function finalize($scheduler_id){
        $event = (new EventsModel())->first();
        $moderator_acceptance = (new ModeratorAcceptanceModel())
            ->join('users u', 'moderator_acceptance.author_id = u.id', 'left')
            ->where(['scheduler_id'=> $scheduler_id, 'author_id'=>session('user_id')])
            ->asArray()->first();
        $header_data = [
            'title' => 'Acceptance Finalize'
        ];


        $data = [
            'event'=> $event,
            'scheduler_id' => $scheduler_id,
            'moderator_acceptance' => $moderator_acceptance,
            'presentation_data_view' => $this->moderator_scheduler_details($scheduler_id)
        ];
        return
            view('acceptance/common/header', $header_data).
            view('acceptance/moderator/moderator_acceptance_finalize', $data).
            view('acceptance/common/footer')
            ;

    }

    public function send_acceptance_confirmation($scheduler_id){
        $sendMail = new PhpMail();
        $email = (new UserModel())->find(session('user_id'));
        try {
            $from = ['name'=>'AFS', 'email'=>'afs@owpm2.com'];
            $addTo = [$email['email']];
            $subject = 'AFS 2025 Participation Confirmation.';
            $addContent = "Thank you for confirming participation in the 129th AFS Metalcasting Congress held in Atlanta, Georgia, April 12-15, 2025. We look forward to seeing you in Atlanta! If you have any questions, please contact Kimberly Perna at <a href='mailto:kperna@afsinc.org'>kperna@afsinc.org </a>. ";

            $response = $sendMail->send($from, $addTo, $subject, $addContent);

            $email_logs_array = [
                'user_id' => session('user_id'),
                'add_to' => ($addTo),
                'subject' => $subject,
                'add_content' => $addContent,
                'send_from' => "Acceptance",
                'send_to' => "Moderator",
                'level' => "Info",
                'template_id' => null,
                'paper_id' => '0',
                'user_agent' => $this->request->getUserAgent()->getBrowser(),
                'ip_address' => $this->request->getIPAddress(),
            ];

            if ($response->statusCode == 200) {
                $logs = new LogsModel();
                $emailLogs = [
                    'user_id' => session('user_id'),
                    'ref_1' => $scheduler_id,
                    'action' => 'email',
                    'ip_address' => $this->request->getIPAddress(),
                    'user_agent' => $this->request->getUserAgent()->getBrowser(),
                    'level'=> 'INFO',
                    'message' => 'sent',
                    'context' => 'copyright'
                ];

                ($logs->save($emailLogs));

                $email_logs_array['status'] = 'Success';
                $emailLogsModel = (new EmailLogsModel())->saveToMailLogs($email_logs_array);

                return $this->response->setJSON(['status' => 'success', 'msg'=> 'Acceptance Email Sent Successfully, <br> We look forward to seeing you in Atlanta!.']);
            } else {
                // Email sending failed
                $email_logs_array['status'] = 'Failed';
                $emailLogsModel = (new EmailLogsModel())->saveToMailLogs($email_logs_array);
                return $this->response->setJSON(['status' => 'failed', 'msg'=> 'Failed to send email']);
            }
            // Send the email
        }catch (\Exception $e){
            return $e->getMessage();
        }

    }
    
    public function acceptance_message($abstract_id){
        $acceptanceModel =  (new AuthorAcceptanceModel())->where(['abstract_id'=>$abstract_id, 'author_id'=>session('user_id')])->get();
        if($acceptanceModel[0]->acceptance_confirmation == '1'){
            $acceptance_status = "Acceptance Form Successfully Submitted";
            $acceptance_message = "Thank you for confirming your participation in AFS 129th Annual Meeting, 
            being held in Denver, Colorado For more information regarding the AFS 129th Annual Meeting ,
            please visit our website.
            <br>AFS Acceptance <a href=".base_url()."/acceptance> Login Page. </a> ";
            $data['acceptance_status'] = $acceptance_status;
            $data['acceptance_message'] = $acceptance_message;
        }else{
            $acceptance_status = "Acceptance Form Successfully Submitted";
            $acceptance_message = "It is unfortunate that you cannot participate in the year's meeting. We look forward to receiving your proposal again next year.
            <br>AFS Acceptance <a href=".base_url()."/acceptance> Login Page. </a> ";
            $data['acceptance_status'] = $acceptance_status;
            $data['acceptance_message'] = $acceptance_message;
        }
        return view('acceptance/email_templates/acceptance_agree', $data);
    }


    public function acceptance_data($scheduler_id){
        return $this->response->setJSON((new ModeratorAcceptanceModel())->where(['scheduler_id'=> $scheduler_id, 'author_id'=>session('user_id')])->findAll());
    }


    public function check_finalize_acceptance($scheduler_id){
        $checkAcceptance = (new ModeratorAcceptanceModel())->checkAcceptance($scheduler_id);
//        print_R($checkAcceptance);exit;
        if($checkAcceptance['status'] == 'success'){
            return $this->save_finalized_acceptance($scheduler_id);
        }else{
            return $this->response->setJSON($checkAcceptance);
        }
    }

    public function save_finalized_acceptance($scheduler_id){
        $acceptanceModel = (new ModeratorAcceptanceModel());
        try{
            $acceptanceModel->where(['scheduler_id'=> $scheduler_id, 'author_id'=>session('user_id')])->set(['is_finalized'=>1])->update();
        }catch(\Exception $e){
            return $this->response->setJSON(['status'=>'failed', 'msg'=>$e->getMessage()]);
        }

        return $this->send_acceptance_confirmation($scheduler_id);

    }


    public function validate_moderator_event($event_id){ // this will validate if the abstract id is belong to the logged in person
        $schedule = (new SchedulerModel())
            ->find($event_id);
        $moderators = json_decode($schedule['session_chair_ids']);
        if(is_array($moderators) && in_array(session('user_id'), $moderators)){
            return true;
        }
        return false;
    }
}
