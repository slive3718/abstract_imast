<?php

namespace App\Controllers\cme;

use App\Controllers\admin\EmailController;
use App\Controllers\BaseController;
use App\Libraries\Upload;
use App\Models\AbstractCategoriesModel;
use App\Models\AbstractSubCategoriesModel;
use App\Models\CMEAssignedPapersModel;
use App\Models\CMEReviewersModel;
use App\Models\CMEReviewsModel;
use App\Models\DesignationsModel;
use App\Models\DivisionsModel;
use App\Models\PaperAssignedReviewerModel;
use App\Models\PaperAuthorsModel;
use App\Models\PaperTypeModel;
use App\Models\PaperUploadsModel;
use App\Models\ReviewerPaperUploadsModel;
use App\Models\SiteSettingModel;
use App\Models\UserModel;
use App\Models\PapersModel;
use App\Models\ReviewerModel;
use App\Models\AbstractReviewModel;
use App\Services\AbstractServices;

class CMEController extends BaseController
{

    public function __construct()
    {
        helper(['form', 'general_helpers']);
        $this->validateCMEUser();
    }

    protected function validateCMEUser()
    {
        $user_id = session()->get('user_id');
        if (!$user_id) {
            return redirect()->to(base_url('cme/login'))->with('error', 'Please login first');
        }

        $user_type = session()->get('user_type');
        if ($user_type !== 'cme') {
            return redirect()->to(base_url('cme'))->with('error', 'Access denied');
        }

        $email = session()->get('email');
        if (empty($email)) {
            return redirect()->to(base_url('cme'))->with('error', 'Invalid session');
        }
    }


    public function index(){
        $CMEAssignedPapersModel = (new CMEAssignedPapersModel())->where('cme_reviewer_id', session('user_id'))->findAll();
        $assignedPaperIDs = array_column($CMEAssignedPapersModel, 'paper_id');

        if(!$assignedPaperIDs){

        }

        $reviewer_abstracts = array();
        foreach($assignedPaperIDs as $assignedPaperID){
             $reviewer['abstracts'] = (new PapersModel())->where('active_status', 1)->find($assignedPaperID);
             $reviewer_abstracts[] = $reviewer;
        }


        $header_data = [
            'title' => 'Reviewer'
        ];

        $data = [
            'reviewer_abstracts' => $reviewer_abstracts,
        ];

        return
            view('cme/common/header', $header_data).
            view('cme/abstract_list', $data).
            view('cme/common/footer')
            ;
    }

    public function getCMEPapersToReview()
    {
        $CMEAssignedPapersModel = (new CMEAssignedPapersModel())->where('cme_reviewer_id', session('user_id'))->findAll();
        $assignedPaperIDs = array_column($CMEAssignedPapersModel, 'paper_id');

        if(!$assignedPaperIDs){
            echo json_encode(array('data'=>[]));
            return;
        }

        $reviewer_abstracts = array();
        foreach($assignedPaperIDs as $assignedPaperID){
             $reviewer['abstracts'] = (new PapersModel())->where('active_status', 1)->asArray()->find($assignedPaperID);
             $reviewer_abstracts[] = $reviewer;
        }

        $response = array();
        foreach($reviewer_abstracts as $item){
            $paper = $item['abstracts'];

            if(!$paper){
                continue;
            }

            $existing_review = (new CMEReviewsModel())->where('paper_id', $paper['id'])->where('cme_reviewer_id', session('user_id'))->first() ?: [];
            $is_rated = $existing_review ? 'Yes' : 'No';

            $response[] = array(
                'abstracts' => $paper,
                'reviews' => $existing_review,
                'is_rated' => $is_rated,
            );
        }

        return $this->response->setJSON(['data' => $response]);
    }

    public function reviewAbstract($abstract_id){

        $data = (new AbstractServices())->view_abstract_data($abstract_id);
        $header_data = [
            'title' => 'Review Submission Detail'
        ];

        if(!empty($abstractReviewData)){
            $data['reviewer_id'] = 1;
            $data['abstract_review_data'] = $abstractReviewData[0];
        }
        $data['reviewer_id'] = 1;
        return
            view('cme/common/header', $header_data).
            view('cme/review_abstract', $data).
            view('cme/common/footer')
            ;
    }

    public function checkAbstractReviewsCount(){
        return true;

    }

    public function addReviewData(){

        $SiteSettingsModel = (new SiteSettingModel());
        $isApproved = 0;
        if(isset($_POST['final_approval'])){
            $isApproved = $_POST['final_approval'];
        }
        $field_array = array(
            'abstract_id'=> $_POST['abstract_id'],
            'reviewer_id'=>$_POST['reviewer_id'],
            'review_question_1'=>$_POST['review_question_1'],
            'review_question_2'=>$_POST['review_question_2'],
            'review_question_3'=>$_POST['review_question_3'],
            'total_score'=> isset($_POST['total_score']) ? intVal($_POST['total_score']) : NULL,
            'reviewer_comment'=>$_POST['reviewer_comment'],
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
            ->join($this->shared_db_name .'.users', $paperAssignedReviewerModel->getTable().'.reviewer_id = '. 'users.id', 'left')
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

}
