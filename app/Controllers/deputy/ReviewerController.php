<?php

namespace App\Controllers\deputy;

use App\Libraries\PhpMail;
use App\Models\AbstractEventsModel;
use App\Models\DivisionsModel;
use App\Models\EmailLogsModel;
use App\Models\EmailTemplatesModel;
use App\Models\EventsModel;
use App\Models\InstitutionModel;
use App\Models\LogsModel;
use App\Models\PanelistPaperSubModel;
use App\Models\PaperAssignedReviewerModel;
use App\Models\PaperAuthorsModel;
use App\Models\PapersDeputyAcceptanceModel;
use App\Models\PaperTypeModel;
use App\Models\PaperUploadsModel;
use App\Models\PaperUploadsViewsModel;
use App\Models\ReviewerPaperUploadsModel;
use App\Models\SiteSettingModel;
use App\Models\UsersProfileModel;
use CodeIgniter\Controller;
use App\Models\Core\Api;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Database\BaseConnection;

use App\Models\UserModel;
use App\Models\PapersModel;
use App\Models\PaperAuthorModel;
use App\Models\ReviewerModel;
use App\Models\AbstractTopicsModel;
// use App\Models\PopulationModel;
use App\Models\AbstractReviewModel;
use App\Models\AbstractFileUploadModel;
// use App\Models\LearningObjectivesModel;

use App\Controllers\admin\Abstracts\AbstractController;
use stdClass;
use CodeIgniter\Database\BaseBuilder;

class ReviewerController extends Controller
{
    private BaseConnection $db;

    public function __construct()
    {
        $this->db = db_connect();
        if (empty(session('user_type')) || session('user_type') !== 'reviewer') {
            header('Location:' . base_url() . 'deputy/logout');
            exit;
        }

        if (empty(session('email')) || session('email') == '') {
            header('Location:' . base_url() . 'deputy');
            exit;
        }
    }


    public function index()
    {

        $event = (new EventsModel())->first();


        $header_data = [
            'title' => 'Deputy Review'
        ];
        $data = [
            'event' => $event,
        ];
        return
            view('deputy/common/header', $header_data) .
            view('deputy/reviewer_menu', $data) .
            view('deputy/common/footer');
    }

    public function papers_list()
    {

        $event = (new EventsModel())->first();
        $divisions = (new UsersProfileModel())->where('author_id', session('user_id'))->findAll();

        $divisions_array = array();
        foreach ($divisions as $division) {
            $divisions_array = (new DivisionsModel())->whereIn('division_id', json_decode($division['division_id']))->findall();
        }

        $div = array_map(function ($e) {
            return $e->name;
        }, $divisions_array);

        $divisions = implode(", ", $div);

        $header_data = [
            'title' => 'Paper List'
        ];
        $data = [
            'event' => $event,
            'divisions' => $divisions
        ];
        return
            view('deputy/common/header', $header_data) .
            view('deputy/papers_list', $data) .
            view('deputy/common/footer');
    }

    public function panels_list()
    {

        $event = (new EventsModel())->first();

        $header_data = [
            'title' => 'Panel List'
        ];
        $data = [
            'event' => $event,
        ];
        return
            view('deputy/common/header', $header_data) .
            view('deputy/panels_list', $data) .
            view('deputy/common/footer');
    }

    public function reviewers_and_progress()
    {

        $event = (new EventsModel())->first();
        $UserModel = (new UserModel());
        $ProfileModel = (new UsersProfileModel());
        $user = $UserModel->find(session('user_id'));

        $deputyUserDivision = (new UsersProfileModel())->select('division_id')->where('author_id', $user['id'])->first();
        $UsersProfileModel = (new UsersProfileModel());
        $AbstractReviewModel = (new AbstractReviewModel());
        $regularReviewersInDeputyDivision = array();
        if ($deputyUserDivision) {
            $deputyUserDivision = json_decode($deputyUserDivision['division_id']);

            //todo: get all regular reviewers on divisions
            $regularDivisionUsers = $ProfileModel
                ->join($UserModel->getTable() . ' as users', $ProfileModel->getTable() . '.author_id = users.id', 'left')
                ->findAll();

            foreach ($regularDivisionUsers as $divisionUser) {
                // Decode the JSON encoded division IDs for the current user
                $usersDivisions = json_decode($divisionUser['division_id']);

                // Check if the user's divisions and the deputy user's divisions are both valid
                if ($usersDivisions && $deputyUserDivision) {
                    foreach ($usersDivisions as $userDivision) {
                        // Check if the user's division is in the deputy user's divisions
                        if (in_array($userDivision, $deputyUserDivision)) {
                            // Ensure the user is not already in the array
                            $userId = $divisionUser['id'];
                            $isAlreadyInArray = false;

                            foreach ($regularReviewersInDeputyDivision as $reviewer) {
                                if ($reviewer['id'] === $userId) {
                                    $isAlreadyInArray = true;
                                    break;
                                }
                            }

                            // Add the user to the array if they are not already in it
                            if (!$isAlreadyInArray) {
                                $regularReviewersInDeputyDivision[] = $divisionUser;
                            }

                            // Break the loop since we found a match and added the user
                            break;
                        }
                    }
                }
            }


            $divisionReviewerReviews = array();
            if ($regularReviewersInDeputyDivision) {
                foreach ($regularReviewersInDeputyDivision as $item) {
//                  print_r($item);exit;
                    $review = $AbstractReviewModel->where('reviewer_id', $item['id'])->findAll();
                    $assignedPaper = (new PaperAssignedReviewerModel())->where('reviewer_id', $item['id'])->findAll();

                    $item['review'] = $review;
                    $item['assignedCount'] = $assignedPaper ? count($assignedPaper) : 0;
                    $item['reviewedCount'] = ($review) ? count($review) : 0;
                    $divisionReviewerReviews[] = $item;
                }
            }
        }

//        print_r($divisionReviewerReviews);exit;

        $header_data = [
            'title' => 'Reviewers and Progress'
        ];
        $data = [
            'event' => $event,
            'divisionReviewerReviews' => ($divisionReviewerReviews) ?: ''
        ];
        return
            view('deputy/common/header', $header_data) .
            view('deputy/reviewers_and_progress', $data) .
            view('deputy/common/footer');
    }


    public function getAllReviewerPapers()
    {
        $reviewerModel = (new ReviewerModel())->getReviewerAbstracts(session('user_id'), 'deputy');
        $reviewer_abstracts = array();
        try {
            foreach ($reviewerModel as $reviewer) {
                $reviewer['papers'] = (new PapersModel())->where('active_status', 1)->where('submission_type', 'paper')->find($reviewer['paper_id']);

                if (isset($reviewer['papers'])) {
                    $reviewer['paper_submitter'] = (new UserModel())->find($reviewer['papers']->id);
                    $reviewer['division'] = (new DivisionsModel())->where('division_id', $reviewer['papers']->division_id)->first();
                    $reviewer['type'] = (new PaperTypeModel())->where('type', $reviewer['papers']->type_id)->first();
                }
                $reviewer['rating'] = (new AbstractReviewModel())->where(['abstract_id' => $reviewer['paper_id'], 'reviewer_id' => session('user_id')])->first();
                if ($reviewer['papers'])
                    $reviewer_abstracts[] = $reviewer;
            }


//            $reviewer_abstracts[] =

            return json_encode(['status' => 200, 'msg' => 'success', 'data' => $reviewer_abstracts]);
        } catch (\Exception $e) {
            return json_encode(['status' => 500, 'msg' => $e->getMessage(), 'data' => '']);
        }

    }

    public function getAllDeputyReviewerPapersByDivision()
    {
        $DivisionModel = (new DivisionsModel());
        $UsersProfileModel = (new UsersProfileModel());
        $PapersModel = (new PapersModel());
        $PaperAssignedReviewerModel = (new PaperAssignedReviewerModel());
        $UsersModel = (new UserModel());
        $reviewer_abstracts = array();
        $AbstractReviewModel = (new AbstractReviewModel());
        $PapersDeputyAcceptanceModel = (new PapersDeputyAcceptanceModel());
        $LogsModel = (new LogsModel());

        try {
            $deputyDivison = $UsersProfileModel
                ->select($UsersProfileModel->getTable() . '.*,' . $DivisionModel->getTable() . '.name as division_name')
                ->join($DivisionModel->getTable(), $UsersProfileModel->getTable() . '.division_id = ' . $DivisionModel->getTable() . '.id', 'left')
                ->where('author_id', session('user_id'))
                ->first();

            $division_ids = json_decode($deputyDivison['division_id']);

            if (!empty($division_ids)) {
                $deputyDivison['papers'] = $PapersModel->whereIn('division_id', $division_ids)->where('submission_type', 'paper')->findAll();

                foreach ($deputyDivison['papers'] as $papers) {
//                    print_r($papers);exit;
                    $papers->paper_submitter = (new UserModel())->join('papers', 'users.id = papers.user_id')->where('users.id', $papers->user_id)->first(); // Fetching the user who submitted the paper
                    $papers->division = (new DivisionsModel())->where('division_id', $papers->division_id)->first(); // Fetching division information of paper
                    $papers->reviewer_division = (new DivisionsModel())->whereIn('division_id', $division_ids)->first(); // Fetching division information of reviewer
                    $papers->type = (new PaperTypeModel())->where('type', $papers->type_id)->first(); // Fetching paper type information
                    $papers->uploads = (new PaperUploadsModel())->where(['paper_id' => $papers->id])->findAll();
                    $papers->upload_views = (new PaperUploadsViewsModel())->where('viewer_id', session('user_id'))->findAll();
                    $papers->submitter_logs = $LogsModel->getCompiledUnseenLogs(
                        'submitter', $papers->id, $papers->user_id,
                        [
                            'submitter_comment',
                            'upload_comment',
                            'upload_presentation'
                        ],
                        []
                    );

                    // Fetching reviewers assigned to the paper
                    $papers->reviewers = $PaperAssignedReviewerModel
                        ->join($UsersModel->getTable(), $PaperAssignedReviewerModel->getTable() . '.reviewer_id = ' . $UsersModel->getTable() . '.id', 'left')
                        ->where(['paper_id' => $papers->id, 'reviewer_type' => 'regular', 'is_deleted' => 0])->findAll();

                    // Checking if each reviewer has submitted a review
                    foreach ($papers->reviewers as $index => $rev) {
                        $reviews = $AbstractReviewModel->where(['reviewer_id' => $rev['id'], 'abstract_id' => $papers->id])->first();
                        if (!empty($reviews)) {
                            $papers->reviewers[$index]['review'] = $reviews;
                        }

                        $papers->reviewers[$index]['logs'] = $LogsModel->getCompiledUnseenLogs(
                            'regular', $papers->id, $rev['id'],
                            [
                                'suggested_revision_comment_added',
                                'review',
                                'decline_assigned_abstract',
                                're_review_comment_added',
                                'upload',
                            ],
                            []
                        );;
                    }

                    $uploadsWithViews = [];
                    foreach ($papers->uploads as $upload) {
                        foreach ($papers->upload_views as $view) {
                            if ($upload['id'] == $view['paper_upload_id']) {
                                $uploadsWithViews[] = $upload['id'];
                                break; // No need to continue checking other views
                            }
                        }
                    }

                    $papers->uploadsWithViews = $uploadsWithViews;

                    $papers->deputyAcceptance = ($PapersDeputyAcceptanceModel
                        ->where(['paper_id' => $papers->id, 'reviewer_id' => session('user_id')]))->first() ?? new StdClass();
                    // Adding the processed paper to an array
                    $reviewer_abstracts[] = $papers;
                }

            }
            return json_encode(['status' => 200, 'msg' => 'success', 'data' => $reviewer_abstracts]);
        } catch (\Exception $e) {
            return json_encode(['status' => 500, 'msg' => $e->getMessage(), 'data' => '']);
        }
    }



    public function getAllDeputyReviewerPanelsByDivision(){
        $DivisionModel = (new DivisionsModel());
        $UsersProfileModel = (new UsersProfileModel());
        $PapersModel = (new PapersModel());
        $reviewer_abstracts = array();
        try{
            $deputyDivison = $UsersProfileModel
                ->select($UsersProfileModel->getTable().'.*,'.$DivisionModel->getTable().'.name as division_name')
                ->join($DivisionModel->getTable(), $UsersProfileModel->getTable().'.division_id = '.$DivisionModel->getTable().'.id', 'left')
                ->where('author_id', session('user_id'))->first();

            $division_ids = json_decode($deputyDivison['division_id']);

            if(!empty($division_ids)) {
                $deputyDivison['papers'] = $PapersModel->whereIn('division_id', $division_ids)->where('submission_type', 'panel')->findAll();

//                print_r($deputyDivison);exit;
                foreach ($deputyDivison['papers'] as $papers) {
//                    print_r($papers);exit;
                    $papers->paper_submitter = (new UserModel())->join('papers', 'users.id = papers.user_id')->where('users.id', $papers->user_id)->first();
                    $papers->division = (new DivisionsModel())->where('division_id', $papers->division_id)->first(); // Fetching division information of paper
                    $papers->reviewer_division = (new DivisionsModel())->whereIn('division_id', $division_ids)->first(); // Fetching division information of reviewer
                    $papers->type   = (new PaperTypeModel())->where('type', $papers->type_id)->first();
                    $papers->individual_panel = (new PanelistPaperSubModel())->where(['paper_id'=> $papers->id])->findAll();

//                  $papers['rating'] = (new AbstractReviewModel())->where(['abstract_id' => $papers['paper_id'], 'reviewer_id' => session('user_id')])->first();
                    $reviewer_abstracts[] = $papers;
                }
            }

//            print_r($reviewer_abstracts);exit;

            return json_encode(['status'=>200, 'msg'=>'success', 'data'=>$reviewer_abstracts]);
        }catch (\Exception $e){
            return json_encode(['status'=>500, 'msg'=>$e->getMessage(), 'data'=>'']);
        }
    }


    public function getAllReviewerPanels(){
        $reviewerModel = (new ReviewerModel())->getReviewerAbstracts(session('user_id'), 'deputy');
        $reviewer_abstracts = array();
        try {
            foreach ($reviewerModel as $reviewer) {
                $reviewer['papers'] = (new PapersModel())->where('active_status', 1)->where('submission_type', 'panel')->find($reviewer['paper_id']);
                if (isset($reviewer['papers'])) {
                    $reviewer['paper_submitter'] = (new UserModel())->find($reviewer['papers']->id);
                    $reviewer['division'] = (new DivisionsModel())->where('division_id', $reviewer['papers']->division_id)->first();
                }
                $reviewer['rating'] = (new AbstractReviewModel())->where(['abstract_id' => $reviewer['paper_id'], 'reviewer_id' => session('user_id')])->first();
                if($reviewer['papers'] )
                    $reviewer_abstracts[] = $reviewer;
            }
//            print_r($reviewer_abstracts);exit;
            return json_encode(['status'=>200, 'msg'=>'success', 'data'=>$reviewer_abstracts]);
        }catch (\Exception $e){
            return json_encode(['status'=>500, 'msg'=>$e->getMessage(), 'data'=>'']);
        }

    }

    function getNextReviewAbstract($event_uri, $current_abstract_id){
        $reviewerModel = (new ReviewerModel())->getReviewerAbstracts(session('user_id'),  'deputy');
        
        $reviewer_abstracts = array();
        if(!empty($reviewerModel)){
            foreach ($reviewerModel as $reviewer){
               $reviewer_abstracts[] = $reviewer['abstract_id'];
            }
        }

        $nextBiggerValue = $this->getNextBiggerValue($reviewer_abstracts, $current_abstract_id);
       echo ($nextBiggerValue);
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


    public function reviewAbstract($event_uri, $abstract_id){
        $event = $this->api->getRequest("event/details/{$event_uri}");
        $abstracts = (new PapersModel())->where('active_status', 1)->find($abstract_id);
        $abstractReviewData =(new AbstractReviewModel())->where(['abstract_id'=>$abstract_id, 'reviewer_id'=>session('user_id')])->findAll();
            $abstract_array = array();
        
        $abstracts->primary_topic = (new AbstractTopicsModel())->whereIn('id', json_decode($abstracts->primary_topic))->findAll();
        $abstracts->secondary_topic = (new AbstractTopicsModel())->whereIn('id', json_decode($abstracts->secondary_topic))->findAll();
        $abstracts->topics = (new AbstractTopicsModel())->get();
        
        $abstracts->image_uploads = (new AbstractFileUploadModel())->where('abstract_id', $abstract_id)->get();
        // if(empty($abstractReviewData) || empty($abstracts)){
        //     echo 'This abstract is incomplete';
        // }
        // print_r($abstractReviewData);exit;
        if(!$event){    
            return (new ErrorHandler($event))->errorPage();
        }

        

        $header_data = [
            'title' => ''
        ];
        $data = [
            'event'=> $event,
            'abstracts' => $abstracts,
            'abstract_id' => $abstract_id,
            'reviewer_id'=>session('user_id'),
            'abstract_topics'=> $abstracts->topics
        ];

        // echo '<pre>';
        // print_r($abstractReviewData);exit;

        if(!empty($abstractReviewData)){
            $data['abstract_review_data'] = $abstractReviewData[0];
        }
        
        return
            view('deputy/common/header', $header_data).
            view('deputy/review_abstract', $data).
            view('deputy/common/footer')
            ;
    }

    public function addReviewData(){
      
        // print_r($_POST['comments_for_committee']);exit;
        $field_array = array(
            'abstract_id'=> $_POST['abstract_id'],
            'reviewer_id'=>$_POST['reviewer_id'],
            'methodology_score' => $_POST['methodology_score'],
            'data_analysis_score' => $_POST['data_analysis_score'],
            'interpretation_score' => $_POST['interpretation_score'],
            'clarity_score' => $_POST['clarity_score'],
            'significance_score' => $_POST['significance_score'],
            'originality_score' => $_POST['originality_score'],
            'opinion_topic_selected' => $_POST['opinion_topic_selected'],
            'opinion_topic_selected2' => $_POST['opinion_topic_selected2'],
            'other_topic' => $_POST['other_topic'],
            'total_score' => $_POST['total_score'],
           
            'other_topic2' => $_POST['other_topic2'],
            'is_case_report' => $_POST['caseReportRadio'],
            'with_conflict_of_interest' => $_POST['coiRadio'],
            'is_abstract_qualified' => $_POST['qualifiedDiversityRadio'],
            'is_requirements_meet' => $_POST['requirementMeetRadio'],
            'comments_for_committee' => $_POST['commentsForCommittee'],
            'comments_for_author' => $_POST['commentsForAuthor'],

            'date_time'=>date('Y-m-d H:i:s')


            
        );

        // other_topic2 total_score is_case_report with_conflict_of_interest is_abstract_qualified is_requirements_meet comments_for_committee comments_for_author 

        if(isset($_POST['session_type_and_length']) && $_POST['session_type_and_length'] == "Yes"){
            $field_array['stl_explanation'] = $_POST['stl_explanation'];
        }
        if(isset($_POST['topic_track']) && $_POST['topic_track'] == "Yes"){
            $field_array['tr_explanation'] = $_POST['tr_explanation'];
        }


        $abstractReviewModel = (new AbstractReviewModel());
        if (!empty($field_array)) {
                if(!empty($abstractReviewModel->where(array('abstract_id'=>$_POST['abstract_id'], 'reviewer_id'=>$_POST['reviewer_id']))->get())){
                    $where = ['abstract_id'=>$_POST['abstract_id'], 'reviewer_id'=>$_POST['reviewer_id']];
                    $abstractReviewModel->where($where)->set($field_array)->update();
                    echo json_encode(array('status'=>200, 'msg'=>'Review successfully updated.'));
                }else{
                    $abstractReviewModel->insert($field_array);
                    echo json_encode(array('status'=>200, 'msg'=>'Review successfully added.'));
                }
            } else {
                echo "No data to insert.";
            }
    }

    public function getRegularReviewersByDivision(){
        $post = $this->request->getPost();


        $UsersModel = (new UserModel());
        $PaperAssignedReviewerModel = (new PaperAssignedReviewerModel());
        $UserProfileModel = (new UsersProfileModel());
        $DivisionModel = (new DivisionsModel());
        $Papers = (new PapersModel());
        $paper = (new PapersModel())->where('id', $post['paper_id'])->first();
        $InstitutionModel = (new InstitutionModel());
        $AbstractReviewModel = (new AbstractReviewModel());

        $regularReviewers = $UsersModel ->select('*,'.$UsersModel->getTable().'.name as user_name, '. $UsersModel->getTable().'.id as user_id')
            ->join($UserProfileModel->getTable(), $UsersModel->getTable().'.id ='.$UserProfileModel->getTable().'.author_id')
            ->where('is_regular_reviewer', 1)
            ->orderBy($UsersModel->getTable().'.name', 'asc')
            ->findAll();
        $reviewerData = [];

        if(!$paper){
            return json_encode(['status'=>500, 'msg'=>'No paper', 'data'=>'']);
        }
//        print_r($post['paper_id']);exit;

        foreach ($regularReviewers as $reviewer){
            $reviewer['is_assigned'] =  $PaperAssignedReviewerModel
                ->where('reviewer_type', 'regular')
                ->where('reviewer_id', $reviewer['user_id'])
                ->where('paper_id', $post['paper_id'])
                ->where('is_deleted', 0)
                ->first();

            $reviewer['reviews'] = ($AbstractReviewModel->where(['abstract_id'=> $post['paper_id'], 'reviewer_id'=>$reviewer['user_id']]))->first() ?? [];

            $reviewerDivision = json_decode($reviewer['division_id']);

//            print_r($reviewerDivision);exit;
            if(is_array($reviewerDivision)) {
                foreach ($reviewerDivision as $div) {
                    if ($div == $paper->division_id) {
                        $reviewer['division'][] = $DivisionModel->where('division_id', $div)->first();
                    }
                }

            }else{
                $reviewer['division'][] = '';
            }

            $reviewer['emailLog'][] = (new EmailLogsModel())
                ->where('add_to', $reviewer['email'])
                ->where('paper_id', $post['paper_id'])
                ->where('send_from', 'PC')
                ->where('send_to', 'Regular')
                ->findAll();

            if(!empty($reviewer['division'])){
                $reviewerData[] = $reviewer;
            }

        }

        if($regularReviewers){
            return json_encode(['status'=>200, 'msg'=>'Found'.Count($reviewerData), 'data'=>$reviewerData]);
        }

    }


    public function assignPaperToRegularReviewer(){
        $post = $this->request->getPost();
        $SiteSettingModel = (new SiteSettingModel());
        $maxAssignedRegular = $SiteSettingModel->where('name', 'regular_reviewer_max_assigned_paper')->first();
        $PaperAssignedReviewerModel = new PaperAssignedReviewerModel();

        $sendMail = new PhpMail();
        $assignedRegular = $PaperAssignedReviewerModel
            ->where('reviewer_type', 'regular')
            ->where('paper_id' , $post['paperID'])
            ->where('is_deleted' , 0)
            ->where('is_declined' , 0)
            ->findAll();

//        print_r($assignedRegular);exit;

        $alreadyAssignedReviewer = $PaperAssignedReviewerModel
            ->where('reviewer_type', 'regular')
            ->where('paper_id' , $post['paperID'])
            ->where('reviewer_id' , $post['reviewerID'])
            ->first();

//        print_r($alreadyAssignedReviewer);exit;

//        print_r($post['isChecked'] === "true");exit;
        $insertArray = [
            'paper_id'=>$post['paperID'],
            'reviewer_id'=>$post['reviewerID'],
            'reviewer_type'=>'regular',
            'is_deleted' => $post['isChecked'] === "true"? 0:1
        ];

        if($post['isChecked'] === "true"){
            if(!$maxAssignedRegular){
                return json_encode(['status'=> 500, 'message'=> "Site Setting for Regular Reviewer Max Assigned Paper not found!", 'data'=>'']);
            }elseif(count($assignedRegular) >= $maxAssignedRegular['value']){
                return json_encode(['status'=> 500, 'message'=> "Only 6 reviewers are allowed!", 'data'=>'']);
            }
        }
        $EmailTemplates = (new EmailTemplatesModel())->find(7);
//        print_r($MailTemplates);exit
        if(empty($alreadyAssignedReviewer)){
            //insert if not existing
            if($PaperAssignedReviewerModel->set($insertArray)->insert()) {
                $user = (new UserModel())->find($post['reviewerID']);

                $email_body = $EmailTemplates['email_body'];
                $email_body = str_replace('##ABSTRACT_ID##', $post['paperID'], $email_body);
                $email_body = str_replace('##RECIPIENTS_FULL_NAME##', ucFirst($user['name']) . ' ' . ucFirst($user['surname']), $email_body);
                $email_body = str_replace('##REVIEW_USERNAME##', ($user['email']), $email_body);
                $email_body = str_replace('##REVIEW_PASSWORD##', 'Please reset your password in case forgotten. Thank you!', $email_body);

                $from = ['name' => 'AFS', 'email' => 'afs@owpm2.com'];
                $addTo = $user['email'];
                $subject = $EmailTemplates['email_subject'];
                $addContent = $email_body;
                $mailResult = $sendMail->send($from, $addTo, $subject, $addContent);

                // ###################  Save to Email logs #####################
                $email_logs_array = [
                    'user_id' => session('user_id'),
                    'add_to' => ($addTo),
                    'subject' => $subject,
                    'ref_1' => 'assign_paper',
                    'add_content' => $addContent,
                    'send_from' => "PC",
                    'send_to' => "Regular",
                    'level' => "Info",
                    'template_id' => $EmailTemplates['id'],
                    'paper_id' => $post['paperID'],
                    'user_agent' => $this->request->getUserAgent()->getBrowser(),
                    'ip_address' => $this->request->getIPAddress(),
                ];

                if ($mailResult->statusCode == 200) {
                    $email_logs_array['status'] = 'Success';
                    (new EmailLogsModel())->saveToMailLogs($email_logs_array);
                } else {
                    $email_logs_array['status'] = 'Failed';
                    (new EmailLogsModel())->saveToMailLogs($email_logs_array);
                }

                $this->saveDeputyLog($post['paperID'], 'INFO', 'Save Success', 'Save', 'assign_regular_reviewer');
                return json_encode(['status' => 200, 'message' => "success", 'data' => '']);
            }
        }else{
            //update if existing
            $PaperAssignedReviewerModel->where('id', $alreadyAssignedReviewer['id'])->set($insertArray)->update();
            $this->saveDeputyLog($post['paperID'], 'INFO', 'Update Success', 'Update', 'assign_regular_reviewer');
            return json_encode(['status'=> 200, 'message'=> "success", 'data'=>'']);
        }

    }

    public function addPaperUploadViews(){
        $post = $this->request->getPost();
        $paperUploadViewsModel = (new PaperUploadsViewsModel());
        $result = $paperUploadViewsModel->where([
            'paper_upload_id'=> $post['upload_id'],
            'viewer_id'=>session('user_id')])->findAll();

        if(!$result) {
            $paperUploadViewsModel->insert([
                'paper_upload_id' => $post['upload_id'],
                'viewer_id' => session('user_id'),
                'date_time' => date('Y-m-d H:i:s')
            ]);
        }

        return json_encode(['status'=> 200, 'message'=> "success", 'data'=>'']);
    }

    public function review_details($paper_id){
        $event = (new EventsModel())->first();

//        print_r($paper_id);exit;
        $PaperModel = (new PapersModel());
        $PaperUploadModel = (new PaperUploadsModel());
        $AbstractReview = (new AbstractReviewModel());
        $UserModel = (new UserModel());
        $DeputyAcceptanceModel = (new PapersDeputyAcceptanceModel());
        $paper = $PaperModel->find($paper_id);
        $ReviewerPaperUploadModel = (new ReviewerPaperUploadsModel());
        $LogsModel = (new LogsModel());

        $paper->uploads = ($PaperUploadModel->where('paper_id', $paper->id))->findAll()??[];
        $paper->reviews = ($AbstractReview
            ->select($AbstractReview->getTable().'.*,'.$UserModel->getTable().'.name as user_name, '.$UserModel->getTable().'.surname as user_surname' )
            ->join($UserModel->getTable(), $AbstractReview->getTable().'.reviewer_id = '.$UserModel->getTable().'.id', 'left')
            ->where('abstract_id', $paper->id))->findAll()??[];

        $paper->declined = (new PaperAssignedReviewerModel())
            ->join($UserModel->getTable().' as users', 'paper_assigned_reviewer.reviewer_id = users.id', 'left')
            ->where('paper_id', $paper->id)
            ->where('is_declined', 1)
            ->findAll();

        $paper->acceptance = $DeputyAcceptanceModel->where(['paper_id'=>$paper_id, 'reviewer_id'=>session('user_id')])->first();
        $paper->division = (new DivisionsModel())->find($paper->division_id);

        foreach ($paper->reviews as &$review) { // Use reference &$review to modify the original array
            $uploads = $ReviewerPaperUploadModel
                ->where(['paper_id' => $review['abstract_id'], 'reviewer_id' => $review['reviewer_id']])
                ->findAll();
            $review['paper_uploads'] = $uploads;

            $review['suggested_revision_comment_added_log'] = $LogsModel->getCompiledLogs(
                'regular', $review['abstract_id'], $review['reviewer_id'],
                [
                    'suggested_revision_comment_added'
                ],
                ['save', 'update'],
                '',
                '',
                [
                    ['column' => 'logs.id', 'value' => 'DESC'],
                ],

            );

            $review['required_revision_comment_added_log'] = $LogsModel->getCompiledLogs(
                'regular', $review['abstract_id'], $review['reviewer_id'],
                [
                    'required_revision_comment_added'
                ],
                ['save', 'update'],
                '',
                '',
                [
                    ['column' => 'logs.id', 'value' => 'DESC'],
                ],
            );

            $review['re_review_comment_added_log'] = $LogsModel->getCompiledLogs(
                'regular', $review['abstract_id'], $review['reviewer_id'],
                [
                    're_review_comment_added'
                ],
                ['save', 'update'],
                '',
                '',
                [
                    ['column' => 'logs.id', 'value' => 'DESC'],
                ],
            );
        }

        $divisions = (new DivisionsModel())->findAll();


//        print_R($paper->acceptance);exit;
        $header_data = [
            'title' => 'Review Details for Paper'
        ];

        $data = [
            'event'=> $event,
            'paper'=>$paper,
            'divisions'=>$divisions
        ];
        return
            view('deputy/common/header', $header_data).
            view('deputy/review_details', $data).
            view('deputy/common/footer')
            ;
    }

    public function acceptance(){
        $post = $this->request->getPost();
        $PaperDeputyAcceptanceModel = (new PapersDeputyAcceptanceModel());

        $result = $PaperDeputyAcceptanceModel
            ->where(['paper_id'=> $post['paper_id'], 'reviewer_id'=>session('user_id')])->first();

        $insertArray = [
          'acceptance_status' => isset($post['acceptance'])?json_encode($post['acceptance']) : '',
          'comments' => $post['comments_program'],
          'date_time' =>  date('Y-m-d H:i:s'),
            'paper_id' => $post['paper_id'],
            'reviewer_id'=>session('user_id')
        ];

        if(isset($post['recommendedPublication'])){
            $insertArray['is_recommended_for_publications'] = $post['recommendedPublication'];
        }

        if(!empty($result)){
            $result = $PaperDeputyAcceptanceModel->where(['paper_id'=> $post['paper_id'], 'reviewer_id'=>session('user_id')])->set($insertArray)->update();
            if($result) {
                $this->saveDeputyLog($post['paper_id'], 'INFO', 'Update Success', 'Update', 'acceptance');
                return json_encode(['status' => 200, 'message' => "success", 'data' => '']);
            }
            else
                return json_encode(['status'=> 500, 'message'=> "failed", 'data'=>'']);
        }else{
            $result = $PaperDeputyAcceptanceModel->insert($insertArray);
            if($result) {
                $this->saveDeputyLog($post['paper_id'], 'INFO', 'Save Success', 'Save', 'acceptance');
                return json_encode(['status' => 200, 'message' => "success", 'data' => '']);
            }
            else
                return json_encode(['status'=> 500, 'message'=> "failed", 'data'=>'']);
        }
    }

    public function allPaperDetails($paper_id){
        $event = (new AbstractEventsModel())->first();

        $UsersProfileModel = (new UsersProfileModel());
        $PapersModel = (new PapersModel());
        $PaperAuthorsModel = (new PaperAuthorsModel());
        $PaperUploadsModel = (new PaperUploadsModel());
        $UsersModel = (new UserModel());
        $PanelistPaperSubModel = new PanelistPaperSubModel();
        $InstitutionModel = new InstitutionModel();
        $papers = $PapersModel
            ->select('papers.*, divisions.name as division_name, paper_type.name as paper_type_name')
            ->join('divisions', 'papers.division_id = divisions.division_id', 'left')
            ->join('paper_type', 'papers.type_id = paper_type.type', 'left')
            ->where(['papers.id'=>$paper_id])->first();

        $papers->submitter = $UsersModel->find($papers->user_id);

        $papers->uploads = $PaperUploadsModel->where('paper_id', $paper_id)->findAll();

        $papers->authors = $PaperAuthorsModel
            ->join($UsersProfileModel->table, 'paper_authors.author_id = users_profile.author_id', 'left')
            ->join($UsersModel->getTable(), $UsersProfileModel->table.'.author_id = '.$UsersModel->getTable().'.id', 'left')
            ->where('paper_authors.paper_id', $paper_id)
            ->whereNotIn('paper_authors.id', function ($builder) {
                $builder->select('paper_author_id')->from('removed_paper_authors');
            })
            ->findAll();

        $papers->individual_panel = $PanelistPaperSubModel
            ->join($UsersModel->getTable(). ' as users', $PanelistPaperSubModel->getTable().'.panelist_id = users.id', 'left')
            ->join($UsersProfileModel->getTable() .' as profile', $PanelistPaperSubModel->getTable().'.panelist_id = profile.id', 'left')
            ->where(['paper_id'=> $papers->id])->findAll();


        return json_encode(['status'=> 200, 'message'=> "success", 'data'=>$papers]);
    }

    public function updatePaperSuitableStatus(){
        $post = $this->request->getPost();
        $AbstractReviewModel = (new AbstractReviewModel());
        $PaperDeputyAcceptanceModel = (new PapersDeputyAcceptanceModel());
        $result = $PaperDeputyAcceptanceModel
            ->where(['paper_id'=> $post['paper_id'], 'reviewer_id'=>session('user_id')])->first();

        if($post['paper_id']){
            if($result){
                $updateResult = $PaperDeputyAcceptanceModel->where(['paper_id'=>$post['paper_id'], 'reviewer_id'=>session('user_id')])->set('is_suitable', $post['is_suitable'])->update();
                if($updateResult)
                    return json_encode(['status'=> 200, 'message'=> "success", 'data'=>'']);
                else
                    return json_encode(['status'=> 500, 'message'=> "failed", 'data'=>'']);
            }else{
                $insertResult = $PaperDeputyAcceptanceModel->set(['paper_id'=>$post['paper_id'], 'reviewer_id'=>session('user_id'), 'is_suitable'=>$post['is_suitable']])->insert();
                if($insertResult)
                    return json_encode(['status'=> 200, 'message'=> "success", 'data'=>'']);
                else
                    return json_encode(['status'=> 500, 'message'=> "failed", 'data'=>'']);
            }
        }
        return json_encode(['status'=> 500, 'message'=> "failed to fetch data", 'data'=>'']);
    }

    public function getAcceptanceDetails($paper_id){
        $post = $this->request->getPost();
        $AcceptanceModel = (new PapersDeputyAcceptanceModel());

        $acceptanceResult = $AcceptanceModel->where(['paper_id'=>$paper_id, 'reviewer_id'=>session('user_id')])->first();

        if($acceptanceResult){
            return json_encode(['status'=> 200, 'message'=> "success", 'data'=>$acceptanceResult]);
        }
        return json_encode(['status'=> 500, 'message'=> "failed to fetch data", 'data'=>'']);

    }

    public function updateReviewerComments(){
        $post = $this->request->getPost();

        $AbstractReviewModel = (new AbstractReviewModel());

        if($post['type'] == 'suggestedComment'){
            $result = $AbstractReviewModel->set('suggested_revision_comment', $post['comment'])->where('id', $post['review_id'])->update();
        }else{
            $result =  $AbstractReviewModel->set('required_revision_comment', $post['comment'])->where('id', $post['review_id'])->update();
        }

       if($result){
           return json_encode(['status'=> 200, 'message'=> "success", 'data'=>'']);
       }
        return json_encode(['status'=> 500, 'message'=> "failed to update data", 'data'=>'']);
    }

    public function displayReview(){
        $post = $this->request->getPost();

        $AbstractReviewModel = (new AbstractReviewModel());
        if($post['type'] == "suggestedComment") {
            $result = $AbstractReviewModel->set('display_suggestion', $post['status'])->where('id', $post['review_id'])->update();
            $template_id = 5;
            return $this->emailSubmitterReviewComment($post, $template_id);
        }else{
            $result = $AbstractReviewModel->set('display_required', $post['status'])->where('id', $post['review_id'])->update();
            $template_id = 6;
            return $this->emailSubmitterReviewComment($post, $template_id);
        }
    }

    function emailSubmitterReviewComment($post, $template_id){
        $AbstractReviewModel = (new AbstractReviewModel());
        $paper = (new PapersModel())->find($post['paper_id']);
        $sendMail = new PhpMail();
        $EmailTemplates = (new EmailTemplatesModel())->find($template_id);
        $email_body = $EmailTemplates['email_body'];
        $user = (new UserModel())->find($post['submitter_id']); // should be submitter ID not the reviewer

//        print_r($user);exit;
        if(!empty($email_body)) {
            if($post['status'] == 1) {
                $PaperTemplates = $email_body;
                $PaperTemplates = str_replace('##ABSTRACT_ID##', $post['paper_id'], $PaperTemplates);
                $PaperTemplates = str_replace('##RECIPIENTS_FULL_NAME##', ucFirst($user['name']) . ' ' . ucFirst($user['surname']), $PaperTemplates);
                $PaperTemplates = str_replace('##ABSTRACT_TITLE##', strip_tags($paper->title), $PaperTemplates);

                $from = ['name' => 'AFS', 'email' => 'afs@owpm2.com'];
                $addTo = $user['email'];
                $subject = $EmailTemplates['email_subject'];
                $addContent = $PaperTemplates;
                $emailResult = $sendMail->send($from, $addTo, $subject, $addContent);


                // ###################  Save to Email logs #####################
                $email_logs_array = [
                    'user_id' => session('user_id'),
                    'add_to' => ($addTo),
                    'subject' => $subject,
                    'ref_1' => 'review_comment',
                    'add_content' => $addContent,
                    'send_from' => "PC",
                    'send_to' => "Regular",
                    'level' => "Info",
                    'template_id' => $EmailTemplates['id'],
                    'paper_id' => $post['paper_id'],
                    'user_agent' => $this->request->getUserAgent()->getBrowser(),
                    'ip_address' => $this->request->getIPAddress(),
                ];

                if($emailResult->statusCode == 200){
                    $email_logs_array['status'] = 'Success';
                    (new EmailLogsModel())->saveToMailLogs($email_logs_array);
                    return json_encode(['status'=> 200, 'message'=> "Email successfully sent!", 'data'=>'']);
                }else{
                    $email_logs_array['status'] = 'Failed';
                    (new EmailLogsModel())->saveToMailLogs($email_logs_array);
                }
            }
        }

        if($post['status'] == 1){
            if($emailResult->statusCode == 200){
                return json_encode(['status' => 200, 'message' => "success", 'data' => '']);
            }else{
                return json_encode(['status'=> 401, 'message'=> "unable to send email to reviewer. Please contact administrator.", 'data'=>'']);
            }
        }else{
            return json_encode(['status' => 200, 'message' => 'success', 'data' => '']);
        }

    }

    public function saveDeadline(){
        $post = $this->request->getPost();

        $AbstractReviewModel = (new AbstractReviewModel());
        if($post['date']) {
            $result = $AbstractReviewModel->set('submitter_deadline', $post['date'])->where('id', $post['review_id'])->update();
        }

        if($result){
            return json_encode(['status'=> 200, 'message'=> "success", 'data'=>'']);
        }
        return json_encode(['status'=> 500, 'message'=> "failed to update data", 'data'=>'']);
    }

    public function sendReReviewEmailToReviewers(){
        $post = $this->request->getPost();
        $AbstractReview = (new AbstractReviewModel());
        $UserModel = (new UserModel());
        $paper = (new PapersModel())->find($post['paper_id']);

        $sendMail = new PhpMail();
        $paper->reviews = ($AbstractReview
            ->select($AbstractReview->getTable().'.*,'.$UserModel->getTable().'.name as user_name, '.$UserModel->getTable().'.surname as user_surname' )
            ->join($UserModel->getTable(), $AbstractReview->getTable().'.reviewer_id = '.$UserModel->getTable().'.id', 'left')
            ->where('abstract_id', $paper->id))->findAll()??[];

        $EmailTemplates = (new EmailTemplatesModel())->find(3);
        $email_body = $EmailTemplates['email_body'];

        if(!empty($paper->reviews)) {
            foreach ($paper->reviews as $reviews) {
                $user = (new UserModel())->find($reviews['reviewer_id']);
                if(!empty($email_body)) {
                    $PaperTemplates = $email_body;
                    $PaperTemplates = str_replace('##ABSTRACT_ID##', $post['paper_id'], $PaperTemplates);
                    $PaperTemplates = str_replace('##RECIPIENTS_FULL_NAME##', ucFirst($user['name']) . ' ' . ucFirst($user['surname']), $PaperTemplates);
                    $PaperTemplates = str_replace('##ABSTRACT_TITLE##', strip_tags($paper->title), $PaperTemplates);
                    $PaperTemplates = str_replace('##REVIEW_USERNAME##', ($user['email']), $PaperTemplates);
                    $PaperTemplates = str_replace('##REVIEW_PASSWORD##', 'Please reset your password in case forgotten. Thank you!', $PaperTemplates);

                    $from = ['name' => 'AFS', 'email' => 'afs@owpm2.com'];
                    $addTo = $user['email'];
                    $subject = $EmailTemplates['email_subject'];
                    $addContent = $PaperTemplates;
                    $emailResult = $sendMail->send($from, $addTo, $subject, $addContent);

                    // ###################  Save to Email logs #####################
                    $email_logs_array = [
                        'user_id' => session('user_id'),
                        'add_to' => ($addTo),
                        'subject' => $subject,
                        'ref_1' => 'review_email',
                        'add_content' => $addContent,
                        'send_from' => "PC",
                        'send_to' => "Regular",
                        'level' => "Info",
                        'template_id' => $EmailTemplates['id'],
                        'paper_id' => $post['paper_id'],
                        'user_agent' => $this->request->getUserAgent()->getBrowser(),
                        'ip_address' => $this->request->getIPAddress(),
                    ];

                    if($emailResult->statusCode == 200){
                        $email_logs_array['status'] = 'Success';
                        (new EmailLogsModel())->saveToMailLogs($email_logs_array);
                        return json_encode(['status'=> 200, 'message'=> "Email successfully sent!", 'data'=>'']);
                    }else{
                        $email_logs_array['status'] = 'Failed';
                        (new EmailLogsModel())->saveToMailLogs($email_logs_array);
                    }

                    return json_encode(['status'=> 500, 'message'=> "Failed sending email", 'data'=>'']);

                }

            }
        }
    }

    public function emailDeadlineToSubmitter(){
        $post = $this->request->getPost();
        $user = (new UserModel())->find($post['submitter_id']);
        $sendMail = new PhpMail();
        $EmailTemplates = (new EmailTemplatesModel())->find(13);
        $email_body = $EmailTemplates['email_body'];
        $paper = (new PapersModel())->find($post['paper_id']);

        if($post['date']) {
            $PaperTemplates = $email_body;
            $PaperTemplates = str_replace('##ABSTRACT_ID##', $post['paper_id'], $PaperTemplates);
//            $PaperTemplates = str_replace('##RECIPIENTS_FULL_NAME##', ucFirst($user['name']) . ' ' . ucFirst($user['surname']), $PaperTemplates);
            $PaperTemplates = str_replace('##ABSTRACT_TITLE##', strip_tags($paper->title), $PaperTemplates);
//            $PaperTemplates = str_replace('##REVIEW_USERNAME##', ($user['email']), $PaperTemplates);
//            $PaperTemplates = str_replace('##REVIEW_PASSWORD##', 'Please reset your password in case forgotten. Thank you!', $PaperTemplates);

            $from = ['name' => 'AFS', 'email' => 'afs@owpm2.com'];
            $addTo = $user['email'];
            $subject = $EmailTemplates['email_subject'];
            $addContent = $PaperTemplates;
            $emailResult = $sendMail->send($from, $addTo, $subject, $addContent);

            // ###################  Save to Email logs #####################
            $email_logs_array = [
                'user_id' => session('user_id'),
                'add_to' => ($addTo),
                'subject' => $subject,
                'ref_1' => 'paper_deadline',
                'add_content' => $addContent,
                'send_from' => "PC",
                'send_to' => "Submitter",
                'level' => "Info",
                'template_id' => $EmailTemplates['id'],
                'paper_id' => $post['paper_id'],
                'user_agent' => $this->request->getUserAgent()->getBrowser(),
                'ip_address' => $this->request->getIPAddress(),
            ];

            if($emailResult->statusCode == 200){
                $email_logs_array['status'] = 'Success';
                (new EmailLogsModel())->saveToMailLogs($email_logs_array);
                return json_encode(['status'=> 200, 'message'=> "Email successfully sent!", 'data'=>'']);
            }else{
                $email_logs_array['status'] = 'Failed';
                (new EmailLogsModel())->saveToMailLogs($email_logs_array);
            }

            return json_encode(['status'=> 500, 'message'=> "Failed sending email!", 'data'=>'']);
        }
    }

    function saveDeputyLog($ref2, $level, $message, $action, $context){
        try {
            $logs = (new LogsModel());
            $logArray = [
                'user_id' => session('user_id'),
                'ref_1' => 'deputy',
                'ref_2' => $ref2 ?? '',
                'action' => $action,
                'level' => $level ?? '',
                'message' => $message ?? '',
                'ip_address' => $this->request->getIPAddress(),
                'user_agent' => $this->request->getUserAgent()->getBrowser(),
                'context' => $context,
            ];


            return $logs->save($logArray);
        }catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
