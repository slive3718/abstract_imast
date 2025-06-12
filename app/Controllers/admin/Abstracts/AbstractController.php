<?php

namespace App\Controllers\admin\Abstracts;

use App\Controllers\User;
use App\Libraries\PhpMail;
use App\Models\AbstractEventsModel;
use App\Models\AdminAbstractCommentModel;
use App\Models\AdminAcceptanceModel;
use App\Models\AdminIndividualPanelAcceptanceModel;
use App\Models\AuthorAcceptanceModel;
use App\Models\DivisionsModel;
use App\Models\EmailLogsModel;
use App\Models\EmailTemplatesModel;
use App\Models\EventsModel;
use App\Models\IndividualPanelUploads;
use App\Models\InstitutionModel;
use App\Models\PanelistPaperSubModel;
use App\Models\PaperAssignedReviewerModel;
use App\Models\PaperAuthorsModel;
use App\Models\PapersDeputyAcceptanceModel;
use App\Models\PaperTypeModel;
use App\Models\PaperUploadsModel;
use App\Models\PaperUploadsViewsModel;
use App\Models\ReviewerPaperUploadsModel;
use App\Models\RoomsModel;
use App\Models\SchedulerDatesModel;
use App\Models\SchedulerModel;
use App\Models\SchedulerSessionTalksModel;
use App\Models\SiteSettingModel;
use App\Models\UsersProfileModel;
use CodeIgniter\Controller;
use App\Models\UserModel;
use App\Models\PapersModel;
use App\Models\ReviewerModel;
use App\Models\AbstractReviewModel;
use App\Controllers\ExcelController;
class AbstractController extends Controller
{

    public function __construct()
    {
        helper('url');
        if(session('user_id')){
            $this->user_id = session('user_id');
        }
        if(empty(session('email')) || session('email') == ''){
            return redirect()->to(base_url().'afs/admin/login');
        }
    }


    public function index(){

        print_r('index');

    }

    public function papers_list(){
       $event = (new EventsModel())->first();

        $header_data = [
            'title' => ''
        ];
        $data = [
            'event'=> $event,
        ];
        return
            view('admin/common/header', $header_data).
            view('admin/papers_list',$data).
            view('admin/common/footer')
            ;
    }

    public function panels_list(){
        $event = (new EventsModel())->first();

        $header_data = [
            'title' => 'Panelist'
        ];
        $data = [
            'event'=> $event
        ];
        return
            view('admin/common/header', $header_data).
            view('admin/panels_list',$data).
            view('admin/common/footer')
            ;
    }

    public function view_presentation_upload($individual_panel_id){

        $UsersModel = (new UserModel());
        $event = (new AbstractEventsModel())->first();

        $header_data = [
            'title' => "Individual Presentation Upload"
        ];
        $data = [
            'event'=> $event,
            'individual_panel_id' => $individual_panel_id
        ];

        return
            view('admin/common/header', $header_data).
            view('admin/panel/presentation_upload',$data).
            view('admin/common/footer')
            ;
    }

    public function view_paper_presentation_upload($paper_id){

        $event = (new AbstractEventsModel())->first();

        $header_data = [
            'title' => "Presentation Upload"
        ];
        $data = [
            'event'=> $event,
            'paper_id' => $paper_id
        ];

        return
            view('admin/common/header', $header_data).
            view('admin/presentation_upload',$data).
            view('admin/common/footer')
            ;
    }


    function get_paper_uploads(){
        $post = $this->request->getPost();
        $PaperUploadModel = (new PaperUploadsModel());
        $paperUploads = $PaperUploadModel->where('paper_id', $post['paper_id'])->orderBy('id', 'desc')->findAll();


        if(!empty($paperUploads)){
            return json_encode(['status' => 200, 'message' => 'success', 'data' => $paperUploads]);
        }else{
            return json_encode(['status' => 200, 'message' => 'success', 'data' => $paperUploads]);
        }
    }



    public function getIndividualUploads(){
        $post = $this->request->getPost();
        $IndividualPanelUploads = (new IndividualPanelUploads());
        $paperUploads = $IndividualPanelUploads->where('individual_panel_id', $post['individual_panel_id'])->findAll();


        if(!empty($paperUploads)){
            return json_encode(['status' => 200, 'message' => 'success', 'data' => $paperUploads]);
        }else{
            return json_encode(['status' => 200, 'message' => 'success', 'data' => $paperUploads]);
        }
    }
    public function presentation_do_upload(){

        $PaperUploadsModel = (new PaperUploadsModel());
        $PapersModel = (new PapersModel());
        $IndividualPanelUploads = (new IndividualPanelUploads());
        $post = $this->request->getPost();

//        print_r($post);exit;

        $siteSettings = (new SiteSettingModel())->first();
        $allowed = explode(",", $siteSettings['value']);
        $filename = $_FILES['file']['name'];
        $file_type = $_FILES['file']['type'];
        $file_size = $_FILES['file']['size']; // Corrected: Used 'size' instead of 'type'
        $file_extension = pathinfo($filename, PATHINFO_EXTENSION);
        // Get the file extension


        if (!in_array($file_extension, $allowed)) {
            return json_encode(['status' => 401, 'message' => 'File type not allowed!']);
        }

        $customName = $post['individual_panel_id'].'_'.date('mdY').'_'.$filename;

        $filePath = "/uploads/individual_panel/presentation/".$post['individual_panel_id']."/";
        $savePath = FCPATH . $filePath;
        $files = $this->request->getFiles('file');
        $uploadResult = $this->doUpload($files, $filePath, $savePath, $customName);

        if(!empty($uploadResult)) {
            try {
                    $result = $IndividualPanelUploads
                        ->insert([
                            'individual_panel_id' => $post['individual_panel_id'],
                            'file_preview_name' => $customName,
                            'file_format' => $file_type,
                            'file_size' => $file_size,
                            'file_path' => $filePath,
                            'file_extension' => $file_extension,
                            'file_name' => $uploadResult['new_name'],
                            'created_at' => date('Y-m-d H:i:s')
                        ]);
                    return json_encode(['status' => 200, 'message' => 'success', 'data' => '']);
            } catch (\Exception $e) {
                $this->response->setStatusCode(401);
                return json_encode(['status' => 500, 'message' => $e->getMessage(), 'data' => '']);
            }
        }
        return json_encode(['status' => 500, 'message' => 'error', 'data' => '']);
    }

    public function doUpload($files, $filePath, $savePath, $fileName){
        if($files) {
            foreach ($files as $file) {
                if ($file->isValid() && !$file->hasMoved()) {
                    // Generate a new name for the file
                    $newName = $file->getRandomName();

                    // Check if the upload directory exists. If it does, move the uploaded file to that directory
                    if (is_dir(FCPATH . $filePath)) {
                        if ($file->move($savePath, $newName)) {
                            return array('new_name' => $newName, 'file_name' => $fileName, 'savePath' => $savePath, 'filePath' => $filePath);
                        }
                    } else {
                        // If the directory does not exist, create the directory and move the uploaded file to it
                        if (mkdir(FCPATH . $filePath, 0777, true)) {
                            if ($file->move($savePath, $newName)) {
                                return array('new_name' => $newName, 'file_name' => $fileName, 'savePath' => $savePath, 'filePath' => $filePath);
                            }
                        } else {
                            return 'error';
                        }
                    }

                } else {
                    // If the uploaded file is not valid, return an error message
                    return 'invalid';
                }
            }
        }
        return 'Missing File';
    }

    public function getPaperCoordinators(){

    }


    public function view_abstract($paper_id){
        $event = (new EventsModel())->first();
        if(!$event){
            return (new ErrorHandler($event))->errorPage();
        }

        $post = $this->request->getPost();
        $event = (new AbstractEventsModel())->first();

        $UsersProfileModel = (new UsersProfileModel());
        $PapersModel = (new PapersModel());
        $PaperAuthorsModel = (new PaperAuthorsModel());
        $PaperUploadsModel = (new PaperUploadsModel());
        $UsersModel = (new UserModel());
        $PapersDeputyAcceptanceModel = (new PapersDeputyAcceptanceModel());
        $AdminAbstractCommentModel = new AdminAbstractCommentModel();

        $papers = $PapersModel
            ->select('papers.*, divisions.name as division_name, paper_type.name as paper_type_name')
            ->join('divisions', 'papers.division_id = divisions.division_id', 'left')
            ->join('paper_type', 'papers.type_id = paper_type.type', 'left')
            ->where('papers.id',$paper_id)->first();

//        print_r($papers);exit;
        $authorInfo = $PaperAuthorsModel
            ->join($UsersModel->table, 'paper_authors.author_id = users.id')
            ->join($UsersProfileModel->table, 'paper_authors.author_id = users_profile.author_id')
            ->whereNotIn('paper_authors.id', function ($builder) {
                $builder->select('paper_author_id')->from('removed_paper_authors');
            })
            ->where('paper_id', $paper_id)
            ->orderBy('author_order', 'asc')
            ->findAll();

        $userInfo = $UsersModel->find($papers->user_id);
        $paper_uploads = $PaperUploadsModel->where('paper_id', $paper_id)->orderBy('id', 'desc')->findAll();
        $paper_reviewer_uploads = (new ReviewerPaperUploadsModel())->where('paper_id', $paper_id)->findAll();

        $authors = $PaperAuthorsModel
            ->join($UsersProfileModel->table, 'paper_authors.author_id = users_profile.author_id', 'left')
            ->join($UsersModel->getTable(), $UsersProfileModel->table.'.author_id = '.$UsersModel->getTable().'.id', 'left')
            ->where('paper_authors.paper_id', $paper_id)
            ->whereNotIn('paper_authors.id', function ($builder) {
                $builder->select('paper_author_id')->from('removed_paper_authors');
            })
            ->findAll();

        foreach ($authorInfo as &$author){
            $author['acceptance'] = (new AuthorAcceptanceModel())->where(['author_id'=> $author['author_id'], 'abstract_id'=>$paper_id])->first();
        }

        $deputy_acceptance = $PapersDeputyAcceptanceModel->where('paper_id', $paper_id)->first();

        $admin_acceptance = (new AdminAcceptanceModel())->where(['user_id'=>session('user_id'), 'abstract_id'=>$paper_id])->first();

        $paper = $PapersModel->find($paper_id);
        $paperUploads = (new PaperUploadsModel())->where('paper_id', $paper_id)->findAll();

        $reviewDetails = $this->review_details($paper_id);
        $header_data = [
            'title' => ''
        ];

        $email_templates = (new EmailTemplatesModel())->findAll();

        $adminComment = $AdminAbstractCommentModel->where(['paper_id'=>$paper_id, 'admin_id'=>session('user_id')])->first();

        $data = [
            'event'=> $event,
            'papers'=> $papers,
            'authorInfo'=> $authorInfo,
            'paper_id'=> $paper_id,
            'userInfo'=> $userInfo,
            'paper_uploads' => $paper_uploads,
            'deputy_acceptance' => $deputy_acceptance,
            'authors'=>$authors,
            'review_details'=>$reviewDetails,
            'email_templates'=>$email_templates,
            'admin_acceptance'=>$admin_acceptance,
            'adminComment' => $adminComment,
            'paper_reviewer_uploads'=>$paper_reviewer_uploads
        ];


//        print_r($data);exit;


        return
            view('admin/common/header', $header_data).
            view('admin/view_abstract',$data).
            view('admin/common/footer')
            ;
    }

    public function view_individual_panel($individual_panel_id){
        $event = (new EventsModel())->first();
        if(!$event){
            return (new ErrorHandler($event))->errorPage();
        }

        $post = $this->request->getPost();
        $event = (new AbstractEventsModel())->first();
        $individual_panel = (new PanelistPaperSubModel())->find($individual_panel_id);
        $paper_id = $individual_panel['paper_id'];
        $UsersProfileModel = (new UsersProfileModel());
        $PapersModel = (new PapersModel());
        $PanelistPaperSub = (new PanelistPaperSubModel());
        $PaperAuthorsModel = (new PaperAuthorsModel());
        $PaperUploadsModel = (new PaperUploadsModel());
        $UsersModel = (new UserModel());
        $PapersDeputyAcceptanceModel = (new PapersDeputyAcceptanceModel());
        $AdminAbstractCommentModel = new AdminAbstractCommentModel();
        $AdminIndividualPanelAcceptanceModel = new AdminIndividualPanelAcceptanceModel();

        $papers = $PapersModel
            ->select('papers.*, divisions.name as division_name, paper_type.name as paper_type_name')
            ->join('divisions', 'papers.division_id = divisions.division_id', 'left')
            ->join('paper_type', 'papers.type_id = paper_type.type', 'left')
            ->where('papers.id',$paper_id)
            ->first();

        $panel_details = $PanelistPaperSub->find($individual_panel['id']);
        $panel_admin_individual_acceptance = $AdminIndividualPanelAcceptanceModel->where('individual_panel_id',$individual_panel['id'])->first();

//        print_r($papers);exit;
        $authorInfo = $PaperAuthorsModel
            ->join($UsersModel->table, 'paper_authors.author_id = users.id')
            ->join($UsersProfileModel->table, 'paper_authors.author_id = users_profile.author_id')
            ->whereNotIn('paper_authors.id', function ($builder) {
                $builder->select('paper_author_id')->from('removed_paper_authors');
            })
            ->where('paper_id', $paper_id)
            ->where('paper_authors.author_id', $individual_panel['panelist_id'])
            ->where('author_type', 'panelist')
            ->orderBy('author_order', 'asc')
            ->findAll();

        $userInfo = $UsersModel->find($papers->user_id);
        $paper_uploads = $PaperUploadsModel->where('paper_id', $paper_id)->findAll();
        $paper_reviewer_uploads = (new ReviewerPaperUploadsModel())->where('paper_id', $paper_id)->findAll();

        $authors = $PaperAuthorsModel
            ->join($UsersProfileModel->table, 'paper_authors.author_id = users_profile.author_id', 'left')
            ->join($UsersModel->getTable(), $UsersProfileModel->table.'.author_id = '.$UsersModel->getTable().'.id', 'left')
            ->where('paper_authors.paper_id', $paper_id)
            ->whereNotIn('paper_authors.id', function ($builder) {
                $builder->select('paper_author_id')->from('removed_paper_authors');
            })
            ->findAll();

        foreach ($authorInfo as &$author) {
            $author['acceptance'] = (new AuthorAcceptanceModel())->where(['abstract_id'=> $paper_id, 'author_id'=>$individual_panel['panelist_id']])->first();
        }

        $deputy_acceptance = $PapersDeputyAcceptanceModel->where('paper_id', $paper_id)->findAll();

        $admin_acceptance = (new AdminAcceptanceModel())->where(['user_id'=>session('user_id'), 'abstract_id'=>$paper_id])->first();

        $paper = $PapersModel->find($paper_id);
        $paperUploads = (new PaperUploadsModel())->where('paper_id', $paper_id)->findAll();

        $reviewDetails = $this->review_details($paper_id);
        $header_data = [
            'title' => ''
        ];

        $email_templates = (new EmailTemplatesModel())->findAll();

        $adminComment = $AdminAbstractCommentModel->where(['paper_id'=>$paper_id, 'admin_id'=>session('user_id')])->first();

        $data = [
            'event'=> $event,
            'papers'=> $papers,
            'authorInfo'=> $authorInfo,
            'paper_id'=> $paper_id,
            'userInfo'=> $userInfo,
            'paper_uploads' => $paper_uploads,
            'deputy_acceptance' => $deputy_acceptance,
            'authors'=>$authors,
            'review_details'=>$reviewDetails,
            'email_templates'=>$email_templates,
            'admin_acceptance'=>$admin_acceptance,
            'adminComment' => $adminComment,
            'paper_reviewer_uploads'=>$paper_reviewer_uploads,
            'panel_details'=>$panel_details,
            'panel_admin_individual_acceptance'=>$panel_admin_individual_acceptance
        ];

//        print_r($data);exit;



        return
            view('admin/common/header', $header_data).
            view('admin/panel/individual_panel',$data).
            view('admin/common/footer')
            ;
    }

    public function view_abstract_panel($paper_id){
        $event = (new EventsModel())->first();
        if(!$event){
            return (new ErrorHandler($event))->errorPage();
        }

        $post = $this->request->getPost();
        $event = (new AbstractEventsModel())->first();

        $UsersProfileModel = (new UsersProfileModel());
        $PapersModel = (new PapersModel());
        $PanelistPaperSub = (new PanelistPaperSubModel());
        $PaperAuthorsModel = (new PaperAuthorsModel());
        $PaperUploadsModel = (new PaperUploadsModel());
        $UsersModel = (new UserModel());
        $PapersDeputyAcceptanceModel = (new PapersDeputyAcceptanceModel());
        $AdminAbstractCommentModel = new AdminAbstractCommentModel();

        $papers = $PapersModel
            ->select('papers.*, divisions.name as division_name, paper_type.name as paper_type_name')
            ->join('divisions', 'papers.division_id = divisions.division_id', 'left')
            ->join('paper_type', 'papers.type_id = paper_type.type', 'left')
            ->where('papers.id',$paper_id)
            ->first();

        $panel_details = $PanelistPaperSub->where(['paper_id'=>$paper_id])->first();

        $coordinators = $PaperAuthorsModel
            ->join($UsersModel->table, 'paper_authors.author_id = users.id')
            ->join($UsersProfileModel->table, 'paper_authors.author_id = users_profile.author_id')
            ->whereNotIn('paper_authors.id', function ($builder) {
                $builder->select('paper_author_id')->from('removed_paper_authors');
            })
            ->where('paper_id', $paper_id)
            ->where('author_type', 'coordinator')
            ->orderBy('author_order', 'asc')
            ->findAll();

//        print_r($papers);exit;
        $authorInfo = $PaperAuthorsModel
            ->join($UsersModel->table, 'paper_authors.author_id = users.id')
            ->join($UsersProfileModel->table, 'paper_authors.author_id = users_profile.author_id')
            ->whereNotIn('paper_authors.id', function ($builder) {
                $builder->select('paper_author_id')->from('removed_paper_authors');
            })
            ->where('paper_id', $paper_id)
            ->where('author_type', 'panelist')
            ->orderBy('author_order', 'asc')
            ->findAll();


        $userInfo = $UsersModel->find($papers->user_id);
        $paper_uploads = $PaperUploadsModel->where('paper_id', $paper_id)->findAll();
        $paper_reviewer_uploads = (new ReviewerPaperUploadsModel())->where('paper_id', $paper_id)->findAll();

        $authors = $PaperAuthorsModel
            ->join($UsersProfileModel->table, 'paper_authors.author_id = users_profile.author_id', 'left')
            ->join($UsersModel->getTable(), $UsersProfileModel->table.'.author_id = '.$UsersModel->getTable().'.id', 'left')
            ->where('paper_authors.paper_id', $paper_id)
            ->whereNotIn('paper_authors.id', function ($builder) {
                $builder->select('paper_author_id')->from('removed_paper_authors');
            })
            ->findAll();

        $deputy_acceptance = $PapersDeputyAcceptanceModel->where('paper_id', $paper_id)->findAll();

        $admin_acceptance = (new AdminAcceptanceModel())->where(['user_id'=>session('user_id'), 'abstract_id'=>$paper_id])->first();

        $paper = $PapersModel->find($paper_id);
        $paperUploads = (new PaperUploadsModel())->where('paper_id', $paper_id)->findAll();

        $reviewDetails = $this->review_details($paper_id);
        $header_data = [
            'title' => ''
        ];

        $email_templates = (new EmailTemplatesModel())->findAll();

        $adminComment = $AdminAbstractCommentModel->where(['paper_id'=>$paper_id, 'admin_id'=>session('user_id')])->first();

        $data = [
            'event'=> $event,
            'papers'=> $papers,
            'authorInfo'=> $authorInfo,
            'paper_id'=> $paper_id,
            'userInfo'=> $userInfo,
            'paper_uploads' => $paper_uploads,
            'deputy_acceptance' => $deputy_acceptance,
            'authors'=>$authors,
            'review_details'=>$reviewDetails,
            'email_templates'=>$email_templates,
            'admin_acceptance'=>$admin_acceptance,
            'adminComment' => $adminComment,
            'paper_reviewer_uploads'=>$paper_reviewer_uploads,
            'panel_details'=>$panel_details,
            'coordinators'=>$coordinators
        ];

//        print_r($data);exit;



        return
            view('admin/common/header', $header_data).
            view('admin/panel/view_abstract_panel',$data).
            view('admin/common/footer')
            ;
    }



    function review_details($paper_id){
        $PaperModel = (new PapersModel());
        $PaperUploadModel = (new PaperUploadsModel());
        $AbstractReview = (new AbstractReviewModel());
        $UserModel = (new UserModel());
        $DeputyAcceptanceModel = (new PapersDeputyAcceptanceModel());
        $paper = $PaperModel->find($paper_id);
        $ReviewerPaperUploadModel = (new ReviewerPaperUploadsModel());
        $paper->uploads = ($PaperUploadModel->where('paper_id', $paper->id))->findAll()??[];
        $paper->reviews = ($AbstractReview
            ->select($AbstractReview->getTable().'.*,'.$UserModel->getTable().'.name as user_name, '.$UserModel->getTable().'.surname as user_surname' )
            ->join($UserModel->getTable(), $AbstractReview->getTable().'.reviewer_id = '.$UserModel->getTable().'.id', 'left')
            ->where('abstract_id', $paper->id))->findAll()??[];

        $paper->acceptance = $DeputyAcceptanceModel->where(['paper_id'=>$paper_id])->findAll();

        foreach ($paper->reviews as &$review) { // Use reference &$review to modify the original array
            $uploads = $ReviewerPaperUploadModel
                ->where(['paper_id' => $review['abstract_id'], 'reviewer_id' => $review['reviewer_id']])
                ->findAll();
            $review['paper_uploads'] = $uploads;
        }
        return $paper;
    }


    function paper_full_details($paper_id){
//        $PaperModel = (new PapersModel());
//        $paper = $PaperModel->first();
//        $PaperAssignedReviewerModel = (new PaperAssignedReviewerModel());
//        $UsersModel = (new UserModel());
//        $PaperAuthorsModel = (new PaperAuthorsModel());
//        if($paper){
//                $paper->paper_submitter = (new UserModel())->find($paper->id); // Fetching the user who submitted the paper
//                $paper->division = (new DivisionsModel())->where('division_id', $paper->division_id)->first(); // Fetching division information of paper
////                $papers->reviewer_division = (new DivisionsModel())->whereIn('division_id', $division_ids)->first(); // Fetching division information of reviewer
//                $paper->type = (new PaperTypeModel())->where('type', $paper->type_id)->first(); // Fetching paper type information
//                $paper->uploads = (new PaperUploadsModel())->where(['paper_id'=>$paper->id])->findAll();
//                $paper->upload_views = (new PaperUploadsViewsModel())->where('viewer_id', session('user_id'))->findAll();
//                $paper->authors = (new PaperAuthorsModel())->where('paper_id',$paper->id)->findAll();
//
//                $paper->authors = $PaperAuthorsModel
//                    ->select('*, '.$PaperAuthorsModel->getTable().'.author_id as author_id, users.name as author_name, users.surname as author_surname, users.id as users_id, users.middle_name as author_middle')
//                    ->join($UsersModel->getTable().' as users', $PaperAuthorsModel->getTable().'.author_id = users.id', 'left')
//                    ->where('paper_id',$paper->id)->findAll();
//
//                $paper->reviewers = $PaperAssignedReviewerModel
//                    ->join($UsersModel->getTable(), $PaperAssignedReviewerModel->getTable() . '.reviewer_id = ' . $UsersModel->getTable() . '.id', 'left')
//                    ->where(['paper_id' => $paper->id, 'reviewer_type' => 'regular', 'is_deleted'=>0])->findAll();
//
//        }


        $user_id = session('user_id');
        $post = $this->request->getPost();
        $event = (new AbstractEventsModel())->first();

        $UsersProfileModel = (new UsersProfileModel());
        $PapersModel = (new PapersModel());
        $PaperAuthorsModel = (new PaperAuthorsModel());
        $PaperUploadsModel = (new PaperUploadsModel());
        $UsersModel = (new UserModel());
        $papers = $PapersModel
            ->select('papers.*, divisions.name as division_name, paper_type.name as paper_type_name')
            ->join('divisions', 'papers.division_id = divisions.division_id', 'left')
            ->join('paper_type', 'papers.type_id = paper_type.type', 'left')
            ->where(['user_id'=> session('user_id'), 'papers.id'=>$paper_id])->first();

//        print_r($papers);exit;
        $authorInfo = $PaperAuthorsModel
            ->join($UsersModel->table, 'paper_authors.author_id = users.id')
            ->join($UsersProfileModel->table, 'paper_authors.author_id = users_profile.author_id')
            ->whereNotIn('paper_authors.id', function ($builder) {
                $builder->select('paper_author_id')->from('removed_paper_authors');
            })
            ->where('paper_id', $paper_id)
            ->orderBy('author_order', 'asc')
            ->findAll();

        $userInfo = $UsersModel->find(session('user_id'));

        $paper_uploads = $PaperUploadsModel->where('paper_id', $paper_id)->findAll();


        $authors = $PaperAuthorsModel
            ->join($UsersProfileModel->table, 'paper_authors.author_id = users_profile.author_id', 'left')
            ->join($UsersModel->getTable(), $UsersProfileModel->table.'.author_id = '.$UsersModel->getTable().'.id', 'left')
            ->where('paper_authors.paper_id', $paper_id)
            ->whereNotIn('paper_authors.id', function ($builder) {
                $builder->select('paper_author_id')->from('removed_paper_authors');
            })
            ->findAll();

        $paper = $PapersModel->find($paper_id);

        $authorDetailsRequiredFields = [
            'electronic_signature', 'is_copyright_agreement_accepted', 'institution', 'country', 'city', 'province', 'zipcode', 'address', 'deg'
        ];

        //         print_r($authors);exit;
        $paperUploads = (new PaperUploadsModel())->where('paper_id', $paper_id)->findAll();

        $header_data = [
            'title' => "Preview"
        ];


//        print_r($paper);exit;

        return $paper;

    }

    public function getUsers(){
        $userModel = new UserModel();
        $result  = ($userModel->Get());

        // print_r($result->getResult());
        echo json_encode($result->getResult());
    }

    public function getUserById(){
        $user_id = $this->request->getPost('user_id');
        $userModel = new UserModel();
        $result = $userModel->find($user_id);
        $result['profile']= (new UsersProfileModel())->where('author_id', $user_id)->first();

        return json_encode($result);
    }

    public function getAllPapers(){
        $post = $this->request->getPost();
        return json_encode(['status' => 200, "message" => 'success', 'data' => $this->getAllPapersArray($post['submission_type'])]??[]);
    }

    public function getAllPanels(){
        $post = $this->request->getPost();
        echo json_encode($this->getAllPanelsArray($post['submission_type']));
    }

    public function getAllPanelsWithId(){
        $post = $this->request->getPost();

        $abstract_panel_ids = $this->request->getPost('abstract_panel_ids'); // Assuming it's a POST request

        if (!empty($abstract_panel_ids)) {
            // Ensure it's an array
            if (!is_array($abstract_panel_ids)) {
                $abstract_panel_ids = [$abstract_panel_ids]; // Convert single value to array
            }

            $admin_accepted_panels = (new PapersModel())
                ->select("papers.*, pps.id as panel_id, u.name as user_name, u.surname as user_surname")
                ->join('panelist_paper_sub pps', 'pps.paper_id = papers.id', 'left')
                ->join('admin_individual_panel_acceptance aipa', 'pps.id = aipa.individual_panel_id', 'inner')
                ->join('users u', 'papers.user_id = u.id', 'left')
                ->where('aipa.acceptance_confirmation', '1')
                ->where('aipa.presentation_preference !=', '2')
                ->whereIn('papers.id', $abstract_panel_ids) // Filter by given abstract_panel_ids
                ->groupBy('papers.id')->asArray()->findAll();

            foreach ($admin_accepted_panels as &$admin_accepted_panel) {
                // Fetch all the panelist abstracts that are already accepted by admin
                $admin_accepted_panel['panelist_abstract'] = (new PanelistPaperSubModel())
                    ->join('users u', 'panelist_paper_sub.panelist_id = u.id', 'left')
                    ->join('admin_individual_panel_acceptance aipa', 'panelist_paper_sub.id = aipa.individual_panel_id', 'left')
                    ->where('aipa.acceptance_confirmation', '1')
                    ->where('aipa.presentation_preference !=', '2')
                    ->where('panelist_paper_sub.paper_id', $admin_accepted_panel['id'])
                    ->findAll();
            }

            return $this->response->setJSON(['status'=> 'success', 'data'=>$admin_accepted_panels]);
        } else {
            return $this->response->setJSON(['error' => 'No panel IDs provided']);
        }
    }


    public function getAllPapersArray($submission_type){
        $PapersModel = new PapersModel();
        $papers = (object) $PapersModel->GetJoinedUser($submission_type)->getResult();
        $PaperAssignedReviewerModel = (new PaperAssignedReviewerModel());
        $UserModel = (new UserModel());
        $paper_array = array();
        try{
            foreach($papers as $paper){
                $user_array = array();
                $reviewer_array = array();
                $paper->authors = $this->getPaperAuthors($paper->id)->getResult();

                foreach($paper->authors as $user){
                    $user->details= (new UsersProfileModel())->where('author_id', $user->author_id)->first();
                    $user->acceptance = (new AuthorAcceptanceModel())->where(['abstract_id'=> $paper->id, 'author_id'=>$user->author_id])->first();
                    if(!empty($user->details))
                        $user_array[] = $user;
                }

                $paper->authors =   $user_array ;

                $paper->reviewers = $PaperAssignedReviewerModel->where(['paper_id'=> $paper->id, 'reviewer_type'=>'regular', 'is_deleted'=>0])->findAll();
                if(!empty($paper)) {
                    foreach ($paper->reviewers as $reviewer) {

                        $reviewer['details'] = (new UsersProfileModel())->where('author_id', $reviewer['reviewer_id'])
                            ->select('users_profile.* , users.name, users.surname, users.middle_name as user_middle, users.email as user_email')
                            ->join($UserModel->getTable() .' as users', 'users_profile.author_id = users.id', 'left')
                            ->first();
                        $reviewer['review'] = (new AbstractReviewModel())->where(['abstract_id'=>$paper->id, 'reviewer_id'=> $reviewer['reviewer_id']])->first();

                        $reviewer_array[] = $reviewer;
                    }
                }

                $paper->dpc = (new PapersDeputyAcceptanceModel())->where(['paper_id'=>$paper->id])->findAll();

                $paper->reviewers = $reviewer_array;
                $paperType = (new PaperTypeModel())->where('id', ($paper->type_id))->first();
                $paperDivision = (new DivisionsModel())->where('id', ($paper->division_id))->first();
                $paper->division = ($paperDivision)?:[];
                $paper->type = ($paperType)?:[];

                $paper->adminOption = (new AdminAcceptanceModel())->where(['abstract_id'=>$paper->id])->first();
                $paper->adminComment = (new AdminAbstractCommentModel())->where(['paper_id'=>$paper->id])->first();
                $paper->uploads = (new PaperUploadsModel())->where(['paper_id'=>$paper->id])->findAll();

                $paper->schedule =   (new SchedulerSessionTalksModel())
                    ->join('scheduler_events se', 'scheduler_session_talks.scheduler_event_id = se.id', 'left')
                    ->where('abstract_id', $paper->id)
                    ->first();

                $paper_array[] = $paper;
            }
//            print_r(array_slice($paper_array, 150, 50));exit;
            // print_r($abstract_array);exit;

        }catch(\Exception $e){
             return ($e->getMessage());
        }

        return $paper_array;
    }

    public function getAllPanelsArray($submission_type){
        $PapersModel = new PapersModel();
        $papers = (object) $PapersModel->GetJoinedUser($submission_type)->getResult();
        $PaperAssignedReviewerModel = (new PaperAssignedReviewerModel());
        $PaperAuthorModel = (new PaperAuthorsModel());
        $UserModel = (new UserModel());
        $AdminIndividualPanelAcceptanceModel = new AdminIndividualPanelAcceptanceModel();
        try{
            foreach($papers as $paper){
                $user_array = array();
                $reviewer_array = array();
                $paper->authors = $this->getPaperAuthors($paper->id)->getResult();
                $paper->panelists = $PaperAuthorModel->getPanelists()->where('paper_id', $paper->id)->get()->getResult();

                if(!empty($paper->panelists)) {
                    foreach ($paper->panelists as $panelist) {

                        $panelist->details = (new PanelistPaperSubModel())->where(['paper_id'=> $panelist->paper_id, 'panelist_id' => $panelist->author_id])->first();
                        // Check if details is not null and has 'id' key before accessing it
                        if (!is_null($panelist->details) && isset($panelist->details['id'])) {
                            $panelist->admin_individual_acceptance = $AdminIndividualPanelAcceptanceModel->where('individual_panel_id', $panelist->details['id'])->first();
                            $panelist->acceptance = (new AuthorAcceptanceModel())->where(['abstract_id'=> $panelist->paper_id, 'author_id'=>$panelist->author_id])->first();
                        } else {
                            $panelist->admin_individual_acceptance = null;
                        }
                    }
                }
                $paper->coordinators = $PaperAuthorModel->getCoordinators($paper->id)->getResult();

                foreach($paper->authors as $user){
                    $user->details= (new UsersProfileModel())->where('author_id', $user->author_id)->first();
                    $user->acceptance = (new AuthorAcceptanceModel())->where(['abstract_id'=> $paper->id, 'author_id'=>$user->author_id])->first();
                    if(!empty($user->details))
                        $user_array[] = $user;
                }

                $paper->authors =   $user_array ;

                $paper->reviewers = $PaperAssignedReviewerModel->where(['paper_id'=> $paper->id, 'reviewer_type'=>'regular'])->findAll();
                if(!empty($paper)) {
                    foreach ($paper->reviewers as $reviewer) {

                        $reviewer['details'] = (new UsersProfileModel())->where('author_id', $reviewer['id'])
                            ->select('users_profile.* , users.name, users.surname')
                            ->join($UserModel->getTable() .' as users', 'users_profile.author_id = users.id', 'left')
                            ->first();
                        $reviewer['review'] = (new AbstractReviewModel())->where(['abstract_id'=>$paper->id, 'reviewer_id'=> $reviewer['reviewer_id ']])->first();

                        $reviewer_array[] = $reviewer;
                    }
                }

                $paper->dpc = (new PapersDeputyAcceptanceModel())->where(['paper_id'=>$paper->id])->findAll();

                $paper->reviewers = $reviewer_array;
                $paperType = (new PaperTypeModel())->where('id', ($paper->type_id))->first();
                $paperDivision = (new DivisionsModel())->where('id', ($paper->division_id))->first();
                $paper->division = ($paperDivision)?:[];
                $paper->type = ($paperType)?:[];

                $paper->adminOption = (new AdminAcceptanceModel())->where(['abstract_id'=>$paper->id])->first();
                $paper->adminComment = (new AdminAbstractCommentModel())->where(['paper_id'=>$paper->id])->first();
                $paper_array[] = $paper;
            }
        }catch(\Exception $e){
            return ($e->getMessage());
        }

        return $paper_array;
    }


    function getPaperAuthors($paper_id){
        $PaperAuthorsModel = new PaperAuthorsModel();
        return $PaperAuthorsModel->GetJoinedUser($paper_id);
    }

    function assign_reviewer_view($abstract_id){


        $userModel = new UserModel();
        $disclosureAuthor  = (object)($userModel->Get());

        $event = (new EventsModel())->first();
        $assignedReviewer = (new ReviewerModel())->get();
        // $author_institution->authors_institution = (new InstitutionsModel())->get();
        $user_array = array();
        if(!empty($disclosureAuthor)){
            foreach($disclosureAuthor as $user){
                $user['details'] = (new UsersProfileModel())->where('author_id', $user['id'])->first();
                $user_array[] = $user;
            }
        }

        $header_data = [
            'title' => ''
        ];
        $data = [
            'event'=> $event,
            'authors' => $user_array,
            'abstract_id' => $abstract_id,
        ];

        if(!empty($assignedReviewer)){
            $data['assigned_reviewer'] = $assignedReviewer;
        }
        return
            view('admin/common/header', $header_data).
            view('admin/assign_reviewer',$data).
            view('admin/common/footer')
            ;
    }

    public function searchUser() {
        $searchValue = $this->request->getPost('searchValue');
        if (empty($searchValue)) {
            return [];
        }
        $userModel = new UserModel();
        $results =
            $userModel->groupStart()
            ->like('name', $searchValue)
            ->orLike('email', $searchValue)
            ->orLike('surname', $searchValue)
            ->groupEnd()
            ->findAll();

        return $this->response->setJSON($results);
    }

    function assign_reviewer(){
        $post = $this->request->getPost();
//        print_r($post);exit;
        $reviewerModel = new ReviewerModel();
        $PaperAssignedReviewer = (new PaperAssignedReviewerModel());
        $insert_field = array(
            'abstract_id' => $_POST['abstract_id'],
            'reviewers' => isset($_POST['selected']) ?$_POST['selected']:''
        );
        echo $reviewerModel->AddReviewerTransaction($insert_field);
    }

    public function getRegularReviewersByDivision(){
        $post = $this->request->getPost();


        $UsersModel = (new UserModel());
        $PaperAssignedReviewerModel = (new PaperAssignedReviewerModel());
        $UserProfileModel = (new UsersProfileModel());
        $DivisionModel = (new DivisionsModel());
        $paper = (new PapersModel())->where('id', $post['paper_id'])->first();

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
//                ->orWhereIn('add_to', json_decode($reviewer['email']))
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

    public function getDivisions(){
        return json_encode((new DivisionsModel())->findAll());
    }

    public function delete_abstract(){
        $abstract_id = $_POST['abstract_id'];
        try{
            $abstractModel = (new PapersModel())->update($abstract_id, ['active_status'=> 0]);
            if($abstractModel == 1){
                return json_encode(['status'=>'success', 'msg'=> 'Abstract deleted successfully']);
            }
        }catch(\Exception $e){
            return json_encode(['status'=>'error', 'msg'=> $e->getMessage()]);
        }
       
    }
 
    public function getReviewerList(){
       $UsersModel = (new UserModel());
       $UsersProfileModelTable = (new UsersProfileModel())->getTable();
        $UserDivisionTable = (new DivisionsModel());
       try{
           $reviewer_array = array();
           $reviewers = $UsersModel
               ->select('*, users.id as user_id')
               ->join($UsersProfileModelTable. ' as profile', 'users.id = profile.author_id', 'left')
               ->where('is_deputy_reviewer', 1)
               ->orWhere('is_regular_reviewer', 1)
               ->findAll();

           if($reviewers) {
               foreach ($reviewers as $reviewer) {
                   $division_array = array();
                   $division_ids = json_decode($reviewer['division_id'], true); // decode JSON as associative array

                   // Ensure $division_ids is an array before proceeding
                   if(is_array($division_ids)) {
                       foreach ($division_ids as $division) {
                           $division_data = $UserDivisionTable->where('division_id', $division)->first();
                           if($division_data) {
                               $division_array[] = $division_data;
                           }
                       }
                   }
                   $reviewer['divisions'] = $division_array;
                   $reviewer_array[] = $reviewer;
               }
           }

           if($reviewer_array){
               return json_encode(['status' => 200, "message" => "", 'data' => $reviewer_array]);
           }
       }catch (\Exception $e){
           return json_encode(['status' => 500, "message" => $e->getMessage(), 'data' => '']);
       }
        return json_encode(['status' => 500, "message" => 'Failed to fetch reviewer', 'data' => '']);
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
            $PaperAssignedReviewerModel->set($insertArray)->insert();

            $user = (new UserModel())->find($post['reviewerID']);

            $email_body = $EmailTemplates['email_body'];
            $email_body = str_replace('##ABSTRACT_ID##', $post['paperID'], $email_body);
            $email_body = str_replace('##RECIPIENTS_FULL_NAME##', ucFirst($user['name']).' '.ucFirst($user['surname']), $email_body);
            $email_body = str_replace('##REVIEW_USERNAME##', ($user['email']), $email_body);
            $email_body = str_replace('##REVIEW_PASSWORD##', 'Please reset your password in case forgotten. Thank you!', $email_body);

            $from = ['name'=>'AFS', 'email'=>'afs@owpm2.com'];
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

            if($mailResult->statusCode == 200) {
                $email_logs_array['status'] = 'Success';
                (new EmailLogsModel())->saveToMailLogs($email_logs_array);
            }else{
                $email_logs_array['status'] = 'Failed';
                (new EmailLogsModel())->saveToMailLogs($email_logs_array);
            }

            return json_encode(['status'=> 200, 'message'=> "success", 'data'=>'']);
        }else{
            //update if existing
            $PaperAssignedReviewerModel->where('id', $alreadyAssignedReviewer['id'])->set($insertArray)->update();
            return json_encode(['status'=> 200, 'message'=> "success", 'data'=>'']);
        }

    }

//    public function getReviewerList(){
//        $reviewerModel = (new ReviewerModel())->get();
//        $reviewerList = ((new ReviewerModel())->getDistinctArray());
//        $reviewer_array = array();
//
////        print_r($reviewerModel);exit;
//
//        if(!empty($reviewerList)){
//            foreach($reviewerList as $reviewer){
//                $reviewer['primary_details'] = (new UserModel())->find($reviewer['reviewer']);
//                $reviewer['total_assigned'] = (new ReviewerModel())->where(['reviewer'=>$reviewer['reviewer']])->find();
//                if(!empty($reviewer['total_assigned'])){
//                    foreach($reviewer['total_assigned'] as $index=>$assigned){
//                        // print_r($assigned);exit;
//                        if(!empty((new AbstractReviewModel())->where(['reviewer_id'=>$assigned['reviewer'], 'abstract_id'=>$assigned['abstract_id']])->get())){
//                            $reviewer['total_assigned'][$index]['is_submitted'] = "1";
//                        }else{
//                            $reviewer['total_assigned'][$index]['is_submitted'] = "0";
//                        }
//                    }
//                }
//
//
//                $reviewer_array[] = $reviewer;
//            }
//        }
//        echo json_encode($reviewer_array);
//    }
    public function reviewer_list(){
        $event = (new EventsModel())->first();

        $reviewerModel =
        $header_data = [
            'title' => ''
        ];
        $data = [
            'event'=> $event,
            'divisions' => (new DivisionsModel())->findAll()
        ];

        if(!empty($assignedReviewer)){
            $data['assigned_reviewer'] = $assignedReviewer;
        }

        return
            view('admin/common/header', $header_data).
            view('admin/reviewer_list',$data).
            view('admin/common/footer')
            ;
    }

    public function exportScores($event_uri){
        $abstracts = (new PapersModel())->get();
     
        if(isset($abstracts) && !empty($abstracts)){
            $abstract_array = array();
            foreach($abstracts as $abstract){
                $abstract->author  = (new PaperAuthorModel())->where('abstract_id', $abstract->id)->get();
                    if(!empty($abstract->author)){
                        $author_details = array();
                        foreach($abstract->author as $author){
                            
                            // print_R($author['author_id']);exit;
                            // print_r($author);exit;
                            $author['details']= (new UserModel())->where('id', $author['author_id'])->first();
                            $author_details[] = $author;
                            // print_r($author);exit;
                        }

                        $abstract->author = $author_details;
                    }
                $abstract->lead_presenter  = (new PaperAuthorModel())->where(['abstract_id'=> $abstract->id, 'is_presenting_author'=> "Yes"])->get();
                // print_r($abstract->lead_presenter[0]);exit;
                 if( $abstract->lead_presenter  ){
                        $abstract->lead_presenter= (new UserModel())->where('id', $abstract->lead_presenter[0]['author_id'])->get();

                        // $author_array = 
                    }
                $abstract->reviewers = (new ReviewerModel())->where('abstract_id', $abstract->id)->get();
                $abstract->reviewersTotalScore =  (new AbstractReviewModel())->select('total_score, reviewer_id')->where('abstract_id', $abstract->id)->get();
                $abstract->overallVote =  (new AbstractReviewModel())->select('overall_vote, reviewer_id')->where('abstract_id', $abstract->id)->get();
                $abstract->reviewComments =  (new AbstractReviewModel())->select('comments_for_committee, abstract_id, reviewer_id, total_score, with_conflict_of_interest, methodology_score, data_analysis_score, interpretation_score, 
                                                                                    clarity_score, significance_score, originality_score, opinion_topic_selected, opinion_topic_selected2, is_case_report,
                                                                                    is_requirements_meet, is_abstract_qualified')->where('abstract_id', $abstract->id)->get();
                   $reviewComments =array();
                if(isset($abstract->reviewComments)){
                    // print_R($abstract->reviewComments);exit;
                        foreach($abstract->reviewComments as $reviewComment){
                            $reviewComment['userDetails'] = ((new UserModel())->select('name, surname')->where('id', $reviewComment['reviewer_id'])->get());
                            $reviewComments[] = $reviewComment;
                        }
                    }

                $abstract->reviewComments = $reviewComments;
                $abstract_array[]= $abstract;
            }
        }
        //     echo '<pre>';
        // print_r($abstract_array);exit;
        // print_R($abstract_array);exit;
      

        $excelController = (new ExcelController());
        $excelController->export('prism',$abstract_array);
    }
    
    public function abstract_acceptance_view($event_uri, $abstract_id){
        $event = $this->api->getRequest("event/details/{$event_uri}");
        $userModel = new UserModel();
        $abstractModel = (new PapersModel())->where('id', $abstract_id)->get();
        $acceptanceRooms = (new AcceptanceRoomsModel())->get();
        if(!$event){
            return (new ErrorHandler($event))->errorPage();
        }

        $header_data = [
            'title' => ''
        ];
        $data = [
            'event'=> $event,
            'abstract_id' => $abstract_id,
        ];

        if(!empty($abstractModel)){
            $data['abstracts'] = $abstractModel;
        }
        if(!empty($abstractModel)){
            $data['acceptanceRooms'] = $acceptanceRooms;
        }
        return
            view('admin/common/header', $header_data).
            view('admin/abstract_acceptance',$data).
            view('admin/common/footer')
            ;
    }

    // Moved to scheduler
    // public function save_admin_acceptance(){  
    //     // print_r($_POST);
    //     $abstractModel = (new AbstractModel());
    //     $field_set = array(
    //         'admin_acceptance_status'=>(isset($_POST['adminAcceptanceOption'])?$_POST['adminAcceptanceOption']:''),
    //         'admin_acceptance_preference'=>(isset($_POST['adminAcceptancePreference'])?$_POST['adminAcceptancePreference']:'0'),
    //         'admin_acceptance_comments'=>(isset($_POST['adminAcceptanceComments'])?$_POST['adminAcceptanceComments']:''),
    //         'presentation_date' => (isset($_POST['presentationDate'])? date("Y-m-d", strtotime($_POST['presentationDate'])):''),
    //         'presentation_start_time' => (isset($_POST['presentationStartTime'])?  date("H:i:s", strtotime($_POST['presentationStartTime'])):''),
    //         'presentation_end_time' => (isset($_POST['presentationEndTime'])?  date("H:i:s", strtotime($_POST['presentationEndTime'])):''),
    //         'presentation_room_id' => (isset($_POST['presentationRoom'])?  $_POST['presentationRoom']:'')
    //     );

    //     try{
    //        $result = ($abstractModel->where('id', $_POST['abstract_id'])->set($field_set)->update());
    //     }catch(\Exception $e){
    //         echo json_encode(['status'=>'error', 'msg'=> $error]);
    //         exit;
    //     }

    //    if($abstractModel->affectedRows()> 0){
    //         echo json_encode(['status'=>'success', 'msg'=>'Update Successful']);
    //    }else{
    //         echo json_encode(['status'=>'noChanges', 'msg'=>'No changes.']);
    //    }
        
    // }

    public function edit_papers_submission( $paper_id = null){

        $paper = (new PapersModel())->where('id', $paper_id)->first();
        $divisions = (new DivisionsModel())->findAll();
        $paper_type = (new PaperTypeModel())->findAll();
        $event = (new EventsModel())->first();
        if(!$event){
            return 'error';
        }

        if(!$paper){
            return 'error';
        }

        $header_data = [
            'title' => "Paper Details"
        ];
        $data = [
            'event'=> $event,
            'paper' => $paper,
            'paper_id'=>$paper_id,
            'divisions' => $divisions ?? '',
            'paper_type' => $paper_type ?? '',
            'is_edit' => 1
        ];
        return
            view('admin/common/header', $header_data).
            view('admin/papers_submission',$data).
            view('admin/common/footer')
            ;
    }
    public function edit_panel_submission( $paper_id = null){

        $paper = (new PapersModel())->where('id', $paper_id)->first();
        $divisions = (new DivisionsModel())->findAll();
        $paper_type = (new PaperTypeModel())->findAll();
        $event = (new EventsModel())->first();
        if(!$event){
            return 'error';
        }

        if(!$paper){
            return 'error';
        }

        $header_data = [
            'title' => "Panel Overview"
        ];
        $data = [
            'event'=> $event,
            'paper' => $paper,
            'paper_id'=>$paper_id,
            'divisions' => $divisions ?? '',
            'paper_type' => $paper_type ?? '',
            'is_edit' => 1
        ];
        return
            view('admin/common/header', $header_data).
            view('admin/panel/panel_submission',$data).
            view('admin/common/footer')
            ;
    }

    public function edit_individual_panel_submission($panel_paper_id){
        $panelPaperSub = (new PanelistPaperSubModel());
        $panelist = $panelPaperSub->find($panel_paper_id);
        $paper_id = $panelist['paper_id'];
        $paper  = (new PapersModel())->where('id', $paper_id)->first();
        $divisions = (new DivisionsModel())->findAll();
        $paper_type = (new PaperTypeModel())->findAll();
        $event = (new EventsModel())->first();
        if(!$event){
            return 'error';
        }

        if(!$paper){
            return 'error';
        }

        $header_data = [
            'title' => "Panel Overview"
        ];
        $data = [
            'event'=> $event,
            'paper' => $paper,
            'paper_id'=>$paper_id,
            'divisions' => $divisions ?? '',
            'paper_type' => $paper_type ?? '',
            'panelist'=>$panelist,
            'is_edit' => 1
        ];
        return
            view('admin/common/header', $header_data).
            view('admin/panel/edit_individual_panel_submission',$data).
            view('admin/common/footer')
            ;
    }

    public function update_abstract_ajax(){

        // Get the POST data
        $post = $this->request->getPost();
        $papersModel = (new PapersModel());
        $update_array = array(
            'division_id' => intVal($post['division']) ?? null,
            'type_id' => isset($post['paper_type']) ? intVal($post['paper_type']) ?? null: null,
            'title' => $post['title'] ?? null,
            'summary' => $post['summary'] ?? null,
            'is_ijmc_interested' => intVal($post['is_interested']) ?? null,
        );

        try {
            $affectedRows = $papersModel->where(['id' => $post['paper_id']])->set($update_array)->update();
        }catch (\Exception $e){
            session()->setFlashdata('status', 'error');
            session()->setFlashdata(['notification' => $e->getMessage()]);
            return json_encode(['status' => '500', 'msg' => "Paper Updated Failed", 'data' =>'']);
        }
        // Check if update was successful
        if ($affectedRows > 0) {
            // Update was successful
            session()->setFlashdata('status', 'success');
            session()->setFlashdata(['notification' => 'Submission Updated Successfully.']);
            return json_encode(['status' => '200', 'msg' => "Paper Updated Successfully", 'data' => ['insert_id'=>$post['paper_id']]]);
        }
    }

    public function update_individual_panel_ajax(){
        $post = $this->request->getPost();
        $PanelistPaperSubModel = (new PanelistPaperSubModel());

        $update_array = [
            'individual_panel_title'=>$post['individual_panel_title']
        ];

        try {
            $affectedRows = $PanelistPaperSubModel->where(['id'=>$post['panelist_paper_sub_id']])->set($update_array)->update();
        }catch (\Exception $e){
            session()->setFlashdata('status', 'error');
            session()->setFlashdata(['notification' => $e->getMessage()]);
            return json_encode(['status' => '500', 'msg' => "Paper Updated Failed", 'data' =>'']);
        }
        // Check if update was successful
        if ($affectedRows > 0) {
            // Update was successful
            session()->setFlashdata('status', 'success');
            session()->setFlashdata(['notification' => 'Submission Updated Successfully.']);
            return json_encode(['status' => '200', 'msg' => "Paper Updated Successfully", 'data' => ['insert_id'=>$post['paper_id'],'individual_panel_id'=>$post['panelist_paper_sub_id']]]);
        }
    }

    public function saveIndividualPanelAdminAcceptance(){
        $post = $this->request->getPost();
        $AdminIndividualPanelAcceptanceModel = new AdminIndividualPanelAcceptanceModel();
        $existAcceptance = $AdminIndividualPanelAcceptanceModel->where(['user_id'=>session('user_id'), 'individual_panel_id'=>$post['individual_panel_id']])->findAll();

        if(!empty($post) && empty($existAcceptance)){
            try {
                $insertArray = [
                    'user_id' => (session('user_id')),
                    'individual_panel_id' => $post['individual_panel_id'],
                    'acceptance_confirmation' => $post['presStatus'],
                    'comment_to_submitter' => $post['st_comments_to_submitter'],
                    'presentation_preference' => $post['presType'],
                    'email_template_used' => $post['template'],
                    'acceptance_confirmation_date' => date('Y-m-d H:i:s'),
                ];
                $AdminIndividualPanelAcceptanceModel->set($insertArray)->insert();

                return json_encode(['status' => 200, 'message' => "Data inserted successfully", 'data' => '']);
            }catch (\Exception $e){
                return json_encode(['status' => 500, 'message' => $e->getMessage(), 'data' => '']);
            }
        }else{
            try {
                $insertArray = [
                    'acceptance_confirmation' => $post['presStatus'],
                    'comment_to_submitter' => $post['st_comments_to_submitter'],
                    'presentation_preference' => $post['presType'],
                    'email_template_used' => $post['template'],
                    'acceptance_confirmation_date' => date('Y-m-d H:i:s'),
                ];
                $AdminIndividualPanelAcceptanceModel->set($insertArray)->where(['user_id' => session('user_id'), 'individual_panel_id' => $post['individual_panel_id']])->update();

                return json_encode(['status' => 200, 'message' => "Data updated successfully", 'data' => '']);
            }catch (\Exception $e){
                return json_encode(['status' => 500, 'message' => $e->getMessage(), 'data' => '']);
            }

        }
    }

    public function saveAndEmailIndividualPanelAdminAcceptance(){
        $post = $this->request->getPost();
        $AdminIndividualPanelAcceptanceModel = new AdminIndividualPanelAcceptanceModel();
        $existAcceptance = $AdminIndividualPanelAcceptanceModel->where(['user_id'=>session('user_id'), 'individual_panel_id'=>$post['individual_panel_id']])->findAll();

        if(!empty($post) && empty($existAcceptance)){
            try {
                $insertArray = [
                    'user_id' => (session('user_id')),
                    'individual_panel_id' => $post['individual_panel_id'],
                    'acceptance_confirmation' => $post['presStatus'],
                    'comment_to_submitter' => $post['st_comments_to_submitter'],
                    'presentation_preference' => $post['presType'],
                    'email_template_used' => $post['template'],
                    'acceptance_confirmation_date' => date('Y-m-d H:i:s'),
                ];
                $AdminIndividualPanelAcceptanceModel->set($insertArray)->insert();

                return json_encode(['status' => 200, 'message' => "Data inserted successfully", 'data' => '']);
            }catch (\Exception $e){
                return json_encode(['status' => 500, 'message' => $e->getMessage(), 'data' => '']);
            }
        }else{
            try {
                $insertArray = [
                    'acceptance_confirmation' => $post['presStatus'],
                    'comment_to_submitter' => $post['st_comments_to_submitter'],
                    'presentation_preference' => $post['presType'],
                    'email_template_used' => $post['template'],
                    'acceptance_confirmation_date' => date('Y-m-d H:i:s'),
                ];
                $AdminIndividualPanelAcceptanceModel->set($insertArray)->where(['user_id' => session('user_id'), 'individual_panel_id' => $post['individual_panel_id']])->update();

                return json_encode(['status' => 200, 'message' => "Data updated successfully", 'data' => '']);
            }catch (\Exception $e){
                return json_encode(['status' => 500, 'message' => $e->getMessage(), 'data' => '']);
            }

        }
    }

    public function authors_and_copyright($paper_id){
        $post = $this->request->getPost();

        $UsersModel = (new UserModel());
        $event = (new AbstractEventsModel())->first();
        $papersModel = (new PapersModel());
        $papers = $papersModel->find($paper_id);
        $UsersProfileModel = (new UsersProfileModel());
        $recentAuthors = (new PaperAuthorsModel())
            ->join($UsersModel->table, 'paper_authors.author_id = users.id')
            ->where('paper_id', $paper_id)
            ->where('author_type', 'author')
            ->findAll();

        $header_data = [
            'title' => "Authors and Copyright"
        ];
        $data = [
            'id' => $this->request->uri->getSegment(4),
            'event'=> $event,
            'paper_id' => $paper_id,
//            'disclosure_data' => $papers,
            'abstract_details'=>($papers)?:'',
            'recentAuthors'=>$recentAuthors
        ];
        return
            view('admin/common/header', $header_data).
            view('admin/author_and_copyright',$data).
            view('admin/common/footer')
            ;
    }

    public function panel_coordinators($paper_id){

        $post = $this->request->getPost();

        $event = (new AbstractEventsModel())->first();
        $papersModel = (new PapersModel());
        $papers = $papersModel->find($paper_id);
        $UsersProfileModel = (new UsersProfileModel());
        $UsersModel = (new UserModel());
        $recentAuthors = (new PaperAuthorsModel())
            ->join($UsersModel->table, 'paper_authors.author_id = users.id')
            ->where('paper_id', $paper_id)
            ->where('author_type', 'coordinator')
            ->findAll();

        $header_data = [
            'title' => "Panel Coordinators"
        ];

        $data = [
            'event'=> $event,
            'paper_id' => $paper_id,
            'abstract_details'=>($papers)?:'',
            'recentAuthors'=>$recentAuthors
        ];

        return
            view('admin/common/header', $header_data).
            view('admin/panel/panel_coordinators',$data).
            view('admin/common/footer')
            ;
    }

    public function panelist($paper_id)
    {
        $post = $this->request->getPost();


        $event = (new AbstractEventsModel())->first();
        $papersModel = (new PapersModel());
        $papers = $papersModel->find($paper_id);
        $UsersModel = (new UserModel());

        $recentAuthors = (new PaperAuthorsModel())
            ->join($UsersModel->table, 'paper_authors.author_id = users.id')
            ->where('paper_id', $paper_id)
            ->where('author_type', 'panelist')
            ->findAll();

        $header_data = [
            'title' => "Panelist"
        ];
        $data = [
            'id' => $this->request->uri->getSegment(4),
            'event' => $event,
            'paper_id' => $paper_id,
//            'disclosure_data' => $papers,
            'abstract_details' => ($papers) ?: '',
            'recentAuthors' => $recentAuthors
        ];
        return
            view('admin/common/header', $header_data) .
            view('admin/panel/panelist', $data) .
            view('admin/common/footer');
    }


    public function permissions($event_uri, $abstract_id){

        $_POST['abstract_id'] = $abstract_id;
        // $_POST['user_id'] = session('user_id');
        $event = (new AbstractEventsModel())->first();
        $abstract_details = (new PapersModel())->where('id', $abstract_id)->get();

        if (!$event) {
            return (new ErrorHandler($event))->errorPage();
        }
        if (!$abstract_details) {
           exit;
        }

        $header_data = [
            'title' => $event->short_name
        ];
        $data = [
            'event'=> $event,
            'abstract_id'=> $abstract_id,
            'abstract_details'=> $abstract_details[0],
            // 'permissions' =>($permissions->data)?$permissions->data[0]:array(),
        ];
        return
            view('admin/common/header', $header_data).
            view('admin/permissions',$data).
            view('admin/common/footer')
            ;
    }

    function assignPapersToProgramChair(){
        $post = $this->request->getPost();

        $SiteSettingModel = (new SiteSettingModel());
        $maxAssignedPaper = $SiteSettingModel->where('name', 'program_chair_max_assigned_paper')->first();

        $PaperAssignedReviewerModel = (new PaperAssignedReviewerModel());

        if(!$maxAssignedPaper){
            return json_encode(['status'=> 500, 'message'=> "Site Setting for Program Chair Max Assigned Paper not found!", 'data'=>'']);
        }elseif(Count($post['selectedPapers']) > ($maxAssignedPaper['value'])){
            return json_encode(['status'=> 500, 'message'=> "Selected paper exceed limit to assign!", 'data'=>'']);
        }



        try {
            foreach ($post['selectedPapers'] as $selectedPaper) {

                $data_array = [
                    'paper_id' => $selectedPaper,
                    'reviewer_id' => $post['reviewerID'],
                    'reviewer_type' => 'deputy'
                ];

                $existAssignedReviewer = $PaperAssignedReviewerModel
                    ->where('paper_id', $selectedPaper)
                    ->where('reviewer_id', $post['reviewerID'])
                    ->where('reviewer_type', 'deputy')
                    ->findAll();

                if (empty($existAssignedReviewer)) {
                    $PaperAssignedReviewerModel->set($data_array)->insert();
                }
            }
        }catch (\Exception $e){
            return json_encode(['status'=> 500, 'message'=> $e->getMessage(), 'data'=>'']);
        }

        return json_encode(['status'=> 200, 'message'=> "success", 'data'=>'']);

    }

    public function saveAdminAcceptance(){
        $post = $this->request->getPost();
        $AdminAcceptanceModel = new AdminAcceptanceModel();

        $existAcceptance = $AdminAcceptanceModel->where(['user_id'=>session('user_id'), 'abstract_id'=>$post['paper_id']])->findAll();

        if(!empty($post) && empty($existAcceptance)){
            try {
                $insertArray = [
                    'user_id' => (session('user_id')),
                    'abstract_id' => $post['paper_id'],
                    'acceptance_confirmation' => $post['presStatus'],
                    'comment_to_submitter' => $post['st_comments_to_submitter'],
                    'presentation_preference' => $post['presType'],
                    'email_template_used' => $post['template'],
                    'acceptance_confirmation_date' => date('Y-m-d H:i:s'),
                ];
                $AdminAcceptanceModel->set($insertArray)->insert();

                return json_encode(['status' => 200, 'message' => "Data inserted successfully", 'data' => '']);
            }catch (\Exception $e){
                return json_encode(['status' => 500, 'message' => $e->getMessage(), 'data' => '']);
            }
        }else{
            try {
                $insertArray = [
                    'acceptance_confirmation' => $post['presStatus'],
                    'comment_to_submitter' => $post['st_comments_to_submitter'],
                    'presentation_preference' => $post['presType'],
                    'email_template_used' => $post['template'],
                    'acceptance_confirmation_date' => date('Y-m-d H:i:s'),
                ];
                $AdminAcceptanceModel->set($insertArray)->where(['user_id' => session('user_id'), 'abstract_id' => $post['paper_id']])->update();

                return json_encode(['status' => 200, 'message' => "Data updated successfully", 'data' => '']);
            }catch (\Exception $e){
                return json_encode(['status' => 500, 'message' => $e->getMessage(), 'data' => '']);
            }

        }
    }

    public function emailAdminAcceptance()
    {
        $post = $this->request->getPost();
        $Users = new UserModel();
        $user = (array) (new PapersModel())->join($Users->getTable() . ' as users', 'papers.user_id = users.id', 'left')->first();
        $paper = (new PapersModel())->find($post['paper_id']);
        $MailTemplates = (new EmailTemplatesModel())->find($post['template']);
        $email_body = $MailTemplates['email_body'];
        $sendMail = new PhpMail();

        try {
            if (!empty($email_body)) {
                $PaperTemplates = $email_body;
                $PaperTemplates = str_replace('##ABSTRACT_ID##', $post['paper_id'], $PaperTemplates);
                $PaperTemplates = str_replace('##RECIPIENTS_FULL_NAME##', ucFirst($user['name']) . ' ' . ucFirst($user['surname']), $PaperTemplates);
                $PaperTemplates = str_replace('##ABSTRACT_TITLE##', strip_tags($paper->title), $PaperTemplates);

                $from = ['name' => 'AFS', 'email' => 'afs@owpm2.com'];
                $addTo = $user['email'];
                $subject = $MailTemplates['email_subject'];
                $addContent = $PaperTemplates;
                $emailResult = $sendMail->send($from, $addTo, $subject, $addContent);

                // ###################  Save to Email logs #####################
                $email_logs_array = [
                    'user_id' => session('user_id'),
                    'add_to' => ($addTo),
                    'subject' => $subject,
                    'ref_1' => 'admin_acceptance',
                    'add_content' => $addContent,
                    'send_from' => "Admin",
                    'send_to' => "Submitter",
                    'level' => "Info",
                    'template_id' => $MailTemplates['id'],
                    'paper_id' => $post['paper_id'],
                    'user_agent' => $this->request->getUserAgent()->getBrowser(),
                    'ip_address' => $this->request->getIPAddress(),
                ];
                if($emailResult->statusCode == 200){
                    $email_logs_array['status'] = 'Success';
                    $emailLogsModel = (new EmailLogsModel())->saveToMailLogs($email_logs_array);
                    return json_encode(['status' => 200, 'message' => 'success', 'data' => '']);
                }else{
                    $email_logs_array['status'] = 'Failed';
                    $emailLogsModel = (new EmailLogsModel())->saveToMailLogs($email_logs_array);
                }
            }
        }catch (\Exception $e){
            return json_encode(['status' => 500, 'message' => $e->getMessage(), 'data' => '']);
        }
    }

    public function saveAdminCommentOnPaper(){
        $post = $this->request->getPost();
        $AdminAbstractCommentModel = new AdminAbstractCommentModel();
        $existAdminComment = $AdminAbstractCommentModel->where(['paper_id'=>$post['abstract_id'], 'admin_id'=>session('user_id')])->findAll();
        if($post) {
            if (empty($existAdminComment)) {
                try {
                    $insertArray = [
                        'comment' => $post['adminComment'],
                        'paper_id' => $post['abstract_id'],
                        'admin_id' => session('user_id'),
                        'is_flag' => $post['adminFlag'],
                        'date_time' => date('Y-m-d H:i:s'),
                    ];
                    $AdminAbstractCommentModel->set($insertArray)->insert();
                    return json_encode(['status' => 200, 'message' => 'Data inserted successfully!', 'data' => '']);
                }catch (\Exception $e){
                    return json_encode(['status' => 500, 'message' => 'error', 'data' => '']);
                }
            } else {
                try {
                    $insertArray = [
                        'comment' => $post['adminComment'],
                        'is_flag' => $post['adminFlag'],
                        'date_time' => date('Y-m-d H:i:s'),
                    ];
                    $AdminAbstractCommentModel->set($insertArray)->where(['paper_id'=>$post['abstract_id'], 'admin_id'=>session('user_id')])->update();
                    return json_encode(['status' => 200, 'message' => 'Data updated successfully!', 'data' => '']);
                }catch (\Exception $e){
                    return json_encode(['status' => 500, 'message' => 'error', 'data' => '']);
                }
            }
        }else{
            return json_encode(['status' => 500, 'message' => 'empty submission', 'data' => '']);
        }
    }

    public function saveIndividualPanelComment(){
        $post = $this->request->getPost();
        $AdminIndividualPanelAcceptanceModel = new AdminIndividualPanelAcceptanceModel();
        $existAdminComment = $AdminIndividualPanelAcceptanceModel->where('individual_panel_id', $post['individual_panel_id'])->first();
        if($post) {
            if (empty($existAdminComment)) {
                try {
                    $insertArray = [
                        'administration_comment' => $post['adminComment'],
                        'user_id' => session('user_id'),
                        'is_flag' => $post['adminFlag'],
                        'date_time' => date('Y-m-d H:i:s'),
                    ];
                    $AdminIndividualPanelAcceptanceModel->set($insertArray)->insert();
                    return json_encode(['status' => 200, 'message' => 'Data inserted successfully!', 'data' => '']);
                }catch (\Exception $e){
                    return json_encode(['status' => 500, 'message' => 'error', 'data' => '']);
                }
            } else {
                try {
                    $insertArray = [
                        'administration_comment' => $post['adminComment'],
                        'is_flag' => $post['adminFlag'],
                        'administration_comment_date_time' => date('Y-m-d H:i:s'),
                    ];
                    $AdminIndividualPanelAcceptanceModel->set($insertArray)->where('id', $existAdminComment['id'])->update();
                    return json_encode(['status' => 200, 'message' => 'Data updated successfully!', 'data' => '']);
                }catch (\Exception $e){
                    return json_encode(['status' => 500, 'message' => 'error', 'data' => '']);
                }
            }
        }else{
            return json_encode(['status' => 500, 'message' => 'empty submission', 'data' => '']);
        }
    }

    public function savePaperTracks() {
        $post = $this->request->getPost();
        $PapersModel = new PapersModel();

        if(!empty($post['selectedTracks'])) {
            try {
                $result = $PapersModel->update($post['paper_id'], ['tracks' => json_encode($post['selectedTracks'])]);
                return json_encode(['status' => 200, 'message' => 'Tracks updated successfully!', 'data' => '']);
            } catch (\Exception $e) {
                return json_encode(['status' => 500, 'message' => 'Error saving tracks: ' . $e->getMessage(), 'data' => '']);
            }
        }

        return json_encode(['status' => 400, 'message' => 'No tracks selected!', 'data' => '']);
    }

    function delete_presentation_upload(){
        $post = $this->request->getPost();
        $PaperUploadsModel = (new PaperUploadsModel());
        $result = $PaperUploadsModel->find($post['upload_id']);

        if($result)
            $filePath = FCPATH.$result['file_path'].$result['file_name']; // Specify the file path
        else
            return json_encode(['status' => 500, 'message' => 'file missing', 'data' => '']);

        try {
            if (file_exists($filePath)) {
                if (unlink($filePath)) {
                    $deleteResult = $PaperUploadsModel->delete($post['upload_id']);
                    if($deleteResult){
                        return json_encode(['status' => 200, 'message' => 'success', 'data' => '']);
                    }
                }
            }
        }catch (\Exception $e){
            return json_encode(['status' => 500, 'message' => $e->getMessage(), 'data' => '']);
        }
    }

    public function getAllRooms(){
        $this->response->setStatusCode(200);
        return $this->response->setJson((new RoomsModel())->findAll());
    }

    public function getAllSessionChair(){
        $this->response->setStatusCode(200);
        return $this->response->setJson((new UserModel())->orderBy('surname', 'ASC')
            ->where('is_deputy_reviewer', 1)
            ->orWhere('is_session_moderator', 1)
            ->findAll());
    }

    public function getAllPaperType(){
        $this->response->setStatusCode(200);
        return $this->response->setJson((new PaperTypeModel())->findAll());
    }


}
