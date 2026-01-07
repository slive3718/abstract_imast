<?php

namespace App\Controllers\cme;

use App\Controllers\admin\EmailController;
use App\Controllers\Author;
use App\Controllers\BaseController;
use App\Libraries\Upload;
use App\Models\AbstractCategoriesModel;
use App\Models\AbstractSubCategoriesModel;
use App\Models\AffiliationsModel;
use App\Models\CMEAssignedPapersModel;
use App\Models\CMEReviewersModel;
use App\Models\CMEReviewsModel;
use App\Models\DesignationsModel;
use App\Models\DivisionsModel;
use App\Models\OrganizationsModel;
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
use App\Services\CMEServices;

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

    public function reviewCMEAbstract($abstract_id){

        (new CMEAssignedPapersModel())->where(['cme_reviewer_id'=>session('user_id'), 'paper_id'=>$abstract_id])->find() || die('Access denied.');

        $data = (new AbstractServices())->view_abstract_data($abstract_id);
        $header_data = [
            'title' => 'Review Submission Detail'
        ];

        $abstractReviewData = (new CMEReviewsModel())->where(['paper_id'=>$abstract_id, 'cme_reviewer_id'=>session('user_id')])->first();
        $data['reviewer_id'] = session('user_id');
        $data['abstract_id'] = $abstract_id;
        $data['reviews'] = $abstractReviewData ?: [];
        $data['organizations'] = (new OrganizationsModel())->findAll();
        $data['affiliations'] = (new AffiliationsModel())->findAll();

        foreach ($data['authors'] as $key => $author) {
            $organizations = (new OrganizationsModel())->get_selected_org($author['author_id']);
            $data['authors'][$key]['selectedOrganizations'] = $organizations;
        }

        return
            view('cme/common/header', $header_data).
            view('cme/review_abstract', $data).
            view('cme/common/footer')
            ;
    }

    public function checkAbstractReviewsCount(){
        return true;

    }

    public function addCMEReviewData(){
        $post = $this->request->getPost();
        $field_array = (new CMEServices())->reviewFieldEntities($post);
        $cmeReviewsModel = (new CMEReviewsModel());
        $SiteSettingsModel = (new SiteSettingModel());
        $paper_id = $post['abstract_id'];
        if (!empty($field_array) && ($paper_id)) {
            if(!empty($cmeReviewsModel->where(array('paper_id'=> $paper_id, 'cme_reviewer_id'=>session('user_id')))->findAll())){
                $where = ['paper_id'=> $paper_id, 'cme_reviewer_id'=> session('user_id')];
                $cmeReviewsModel->where($where)->set($field_array)->update();
                return json_encode(array('status'=>200, 'message'=>'Review successfully updated.'));
            }else{
                $siteSettings = $SiteSettingsModel->where('name', 'reviewers_reviews_to_close')->first();
                $cmeReviews = ($cmeReviewsModel->where('paper_id', $paper_id))->findAll();
                if(count($cmeReviews) >= $siteSettings['value']){
                    return json_encode(array('status' => 201, 'message' => "Regular Review Task Closed – Paper has been reviewed three times."));
                }else {
                    $field_array['paper_id'] = $paper_id;
                    $field_array['cme_reviewer_id'] = session('user_id');
                    $cmeReviewsModel->insert($field_array);
                    return json_encode(array('status' => 200, 'message' => 'Review successfully added.'));
                }
            }
        } else {
            return json_encode(array('status'=>500, 'message'=>'Error!'));
        }
    }

    function getNextCMEAbstract($current_abstract_id){
        $activePapers = (new PapersModel())->select('id')->where('active_status', 1)->findAll();
        $activePapersID = $activePapers ? array_column($activePapers, 'id') : array();
        $reviewedPapers = (new CMEReviewsModel())->select('paper_id')->where('cme_reviewer_id', session('user_id'))->findAll();
        $reviewedPapersID = $reviewedPapers ? array_column($reviewedPapers, 'paper_id') : array();
        $unreviewedAbstracts = (new CMEAssignedPapersModel())
            ->where(['cme_reviewer_id'=>session('user_id')])
            ->whereNotIn('paper_id', $reviewedPapersID)
            ->whereIn('paper_id', $activePapersID)
            ->findAll();
        $ids = !empty($unreviewedAbstracts) ? array_column($unreviewedAbstracts, 'paper_id') : array();
        return array_diff($ids, $reviewedPapersID)[0] ?? '';
    }
}
