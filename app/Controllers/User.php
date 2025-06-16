<?php

namespace App\Controllers;

use App\Libraries\PhpMail;
use App\Models\AbstractCategoriesModel;
use App\Models\AbstractReviewModel;
use App\Models\CitiesModel;
use App\Models\CountriesModel;
use App\Models\DesignationsModel;
use App\Models\EmailLogsModel;
use App\Models\EmailTemplatesModel;
use App\Models\PanelistPaperSubModel;
use App\Models\PaperAssignedReviewerModel;
use App\Models\PapersDeputyAcceptanceModel;
use App\Models\UsersProfileModel;
use App\Models\DivisionsModel;
use App\Models\AbstractEventsModel;
use App\Models\Core\Api;
use App\Models\EventsModel;
use App\Models\InstitutionModel;
use App\Models\LogsModel;
use App\Models\PaperAuthorsModel;
use App\Models\PapersModel;
use App\Models\PaperTypeModel;
use App\Models\PaperUploadsModel;
use App\Models\RemovedPaperAuthorModel;
use App\Models\SiteSettingModel;
use App\Models\UserModel;
use CodeIgniter\HTTP\ResponseInterface;
use PhpOffice\PhpWord\Style\Paper;


class User extends BaseController
{

    private $db;
    public function __construct()
    {

        if(session('user_id')){
            $this->user_id = session('user_id');
        }else{
            return redirect()->to(base_url().'login');
        }

        if(empty(session('email')) || session('email') == ''){
            return redirect()->to(base_url().'logout');
        }

        helper('text', 'database', 'form', 'url');
        $this->db = db_connect();
    }

    public function index()
    {
        print_r('Index User');exit;
        $header_data = [
            'title' => 'Asia Pacific Submission'
        ];

        $data = [];

        return
            view('event/common/header', $header_data).
            view('event/login',$data).
            view('event/common/footer')
            ;
    }

    function setSegment($segment)
    {
        if (ENVIRONMENT === 'production') {
            $segment =  $segment - 1;
        }

        return $segment;
    }
     public function submission_menu($paper_id){

         $this->validate_user_access($paper_id);

         $PapersModel = (new PapersModel());
         $AuthorsModel = (new PaperAuthorsModel());
         $UsersProfileModel = (new UsersProfileModel());
         $PaperAuthorsModel = (new PaperAuthorsModel());
         $RemovedPaperAuthorsModel = (new RemovedPaperAuthorModel());
         $authors = $AuthorsModel
             ->join($UsersProfileModel->db->database.'.users_profile', 'paper_authors.author_id = users_profile.author_id', 'left')
             ->join('removed_paper_authors', 'paper_authors.author_id = removed_paper_authors.paper_author_id', 'left')
             ->whereNotIn($PaperAuthorsModel->table . '.id', function ($builder) use ($RemovedPaperAuthorsModel) {
                 $builder->select('paper_author_id')->from($RemovedPaperAuthorsModel->table);
             })
             ->where('paper_authors.paper_id', $paper_id)
             ->findAll();

         $paper = $PapersModel->asArray()->find($paper_id);

         $authorDetailsRequiredFields = [
             'institution_id' => 'Institution',
             'designations' => 'Designations',
             'signature_signed_date'=> 'Disclosure Signature'
         ];


         $paperRequiredFields = [];

         $incomplete = [];
         foreach ($authors as $author) {
             foreach ($authorDetailsRequiredFields as $index=>$required) {
                 if($author[$index] == '' ||$author[$index] == null ){
                     $incomplete['author'][]= 'required ' . $required . ' for author: ' . $author['author_id'];
                 }
             }
         }

         if($paper['image_upload_finished'] !== '1'){
             $incomplete['paperUpload'][] = 'Incomplete Paper Upload.';
         }

         if($paper){
             foreach ($paperRequiredFields as $index => $required) {
                 if($paper->$index == '' ||$paper->$index == null ){
                     $incomplete['paper'][] = 'required ' . $required . ' for paper: ' . $paper->id;
                 }
             }
         }

         if($paper['is_finalized'] !== '1'){
             $incomplete['finalized'][] = 1;
         }

        $header_data = [
            'title' => "Submission Menu"
        ];

        $data = [
            'paper_id'=>$paper_id,
            'paper' => $paper ?? [],
            'authors' => $authors,
            'incompleteStatus' => !empty($incomplete)?$incomplete:[]
        ];


        return
            view('event/common/header', $header_data).
            view('event/submission_menu',$data).
            view('event/common/footer')
            ;
    }

    public function papers_submission(){

        $categories = (new AbstractCategoriesModel())->orderBy('name', 'asc')->findAll();
        $paper_type = (new PaperTypeModel())->findAll();

        $header_data = [
            'title' => "Submission"
        ];

        $data = [
            'categories' => $categories ?? '',
            'paper_type' => $paper_type ?? '',
            'notification' => session()->getFlashdata('notification')
        ];
        return
            view('event/common/header', $header_data).
            view('event/papers_submission',$data).
            view('event/common/footer')
            ;
    }

    public function edit_papers_submission($paper_id = null){
        $this->validate_user_access($paper_id);

        $paper = (new PapersModel())->where('id', $paper_id)->asArray()->first();
        $categories = (new AbstractCategoriesModel())->orderBy('name', 'asc')->findAll();
        $paper_type = (new PaperTypeModel())->findAll();

        if(!$paper){
            return 'error';
        }

        $header_data = [
            'title' => "Abstract Details"
        ];
        $data = [
            'paper' => $paper,
            'paper_id'=>$paper_id,
            'paper_type' => $paper_type ?? '',
            'categories' => $categories ?? '',
            'is_edit' => 1,
            'previous_url' => previous_url(),
            'previous_page' => service('uri')->setURI(previous_url())->getSegment($this->setSegment(3))?? '',
        ];
        return
            view('event/common/header', $header_data).
            view('event/papers_submission',$data).
            view('event/common/footer')
            ;
    }

    public function submit_paper_ajax()
    {
        $post = $this->request->getPost();

        if (!$post) {
            return $this->response->setJSON(['status' => 400, 'msg' => 'No post data received']);
        }

        // Define validation rules
        $validationRules = [
            'previous_presentation'  => 'required',
            'basic_science_format'  => 'required',
            'abstract_category'      => 'required',
            'abstract_title'         => 'required',
            'hypothesis'             => 'required',
            'study_design'           => 'required',
            'introduction'           => 'required',
            'methods'                => 'required',
            'results'                => 'required',
            'conclusions'            => 'required',
            'type_id'            => 'required',
        ];

        if (!$this->validate($validationRules)) {
            return $this->response->setJSON(['status' => 'failed', 'msg' => $this->validator->getErrors()]);
        }

        // Prepare data for insertion
        $insert_array = [
            'user_id'               => session('user_id'),
            'type_id'            => $post['paper_type'] ?? null,
            'previous_presentation' => $post['previous_presentation'] ?? null,
            'basic_science_format'   => $post['basic_science_format'] ?? null,
            'abstract_category'      => $post['abstract_category'] ?? null,
            'title'                  => $post['abstract_title'] ?? null,
            'hypothesis'             => $post['hypothesis'] ?? null,
            'study_design'           => $post['study_design'] ?? null,
            'introduction'           => $post['introduction'] ?? null,
            'methods'                => $post['methods'] ?? null,
            'results'                => $post['results'] ?? null,
            'conclusions'            => $post['conclusions'] ?? null,
            'additional_notes'       => $post['additional_notes'] ?? null,
            'abstract_body_count'       => $post['abstract_body_count'] ?? null,
        ];

        try {
            $papersModel = new PapersModel();
            $papersModel->insert($insert_array);
            $insert_id = $papersModel->getInsertID();

            // Generate and update custom ID
            $newCustomId = $this->generateCustomID($insert_id);
            $papersModel->update($insert_id, ['custom_id' => $newCustomId]);

            return $this->response->setJSON([
                'status' => 200,
                'msg' => 'Data Inserted Successfully',
                'data' => ['abstract_id' => $insert_id]
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 500,
                'msg' => 'Database Insertion Failed',
                'error' => $e->getMessage()
            ]);
        }
    }


    public function update_paper_ajax()
    {
        $post = $this->request->getPost();

        // Ensure paper_id is provided
        if (empty($post['paper_id'])) {
            return $this->response->setJSON(['status' => 400, 'msg' => "Paper ID is required", 'data' => '']);
        }

        $papersModel = new PapersModel();

        // Fetch existing data to prevent overwriting with null
        $existingPaper = $papersModel->asArray()->find($post['paper_id']);
        if (!$existingPaper) {
            return $this->response->setJSON(['status' => 404, 'msg' => "Paper not found", 'data' => '']);
        }

        // Prepare the update array
        $update_array = [
            'type_id'            => isset($post['paper_type']) ? trim($post['paper_type']) : $existingPaper['paper_type'],
            'previous_presentation'  => isset($post['previous_presentation']) ? trim($post['previous_presentation']) : $existingPaper['previous_presentation'],
            'basic_science_format'   => isset($post['basic_science_format']) ? trim($post['basic_science_format']) : $existingPaper['basic_science_format'],
            'abstract_category'      => isset($post['abstract_category']) ? trim($post['abstract_category']) : $existingPaper['abstract_category'],
            'title'                  => isset($post['abstract_title']) ? trim($post['abstract_title']) : $existingPaper['title'],
            'hypothesis'             => isset($post['hypothesis']) ? trim($post['hypothesis']) : $existingPaper['hypothesis'],
            'study_design'           => isset($post['study_design']) ? trim($post['study_design']) : $existingPaper['study_design'],
            'introduction'           => isset($post['introduction']) ? trim($post['introduction']) : $existingPaper['introduction'],
            'methods'                => isset($post['methods']) ? trim($post['methods']) : $existingPaper['methods'],
            'results'                => isset($post['results']) ? trim($post['results']) : $existingPaper['results'],
            'conclusions'            => isset($post['conclusions']) ? trim($post['conclusions']) : $existingPaper['conclusions'],
            'additional_notes'       => isset($post['additional_notes']) ? trim($post['additional_notes']) : $existingPaper['additional_notes'],
            'abstract_body_count'      => isset($post['abstract_body_count']) ? trim($post['abstract_body_count']) : $existingPaper['abstract_body_count'],
            'min_follow_up_period'   => isset($post['min_follow_up_period']) ? trim($post['min_follow_up_period']) : $existingPaper['min_follow_up_period'],
            'is_srs_funded'          => isset($post['is_srs_funded']) ? trim($post['is_srs_funded']) : $existingPaper['is_srs_funded'],
            'primary_investigator'   => isset($post['primary_investigator']) ? trim($post['primary_investigator']) : $existingPaper['primary_investigator'],
            'grant_year'             => isset($post['grant_year']) ? trim($post['grant_year']) : $existingPaper['grant_year'],
            'image_caption'          => isset($post['image_caption']) ? trim($post['image_caption']) : $existingPaper['image_caption'],
            'author_q_1'          => isset($post['author_q_1']) ? trim($post['author_q_1']) : $existingPaper['author_q_1'],
            'author_q_2'          => isset($post['author_q_2']) ? trim($post['author_q_2']) : $existingPaper['author_q_2'],
            'image_upload_finished'  => isset($post['image_upload_finished']) ? trim($post['image_upload_finished']) : $existingPaper['image_upload_finished'],
        ];


        // Remove fields that haven't changed
        $update_array = array_diff_assoc($update_array, $existingPaper);

        // If no changes, return a message
        if (empty($update_array)) {
            return $this->response->setJSON(['status' => 200, 'msg' => "No changes made", 'data' => ['abstract_id' => $post['paper_id']]]);
        }

        // Perform the update operation
        try {
            $updated = $papersModel->update($post['paper_id'], $update_array);

            if ($updated) {
                return $this->response->setJSON([
                    'status' => 200,
                    'msg' => "Paper updated successfully",
                    'data' => ['abstract_id' => $post['paper_id']]
                ]);
            } else {
                return $this->response->setJSON(['status' => 500, 'msg' => "Update failed", 'data' => '']);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 500,
                'msg' => "Paper update failed: " . $e->getMessage(),
                'data' => ''
            ]);
        }
    }


    function generateCustomID($paper_id){
        $nextYear = date('Y') < 2026 ? 2026 : date('Y');
        $newCustomId = sprintf('%s-%03d', $nextYear, $paper_id);
        return $newCustomId;
    }

    function generatePanelistCustomID($paper_id){
        $nextYear = date('Y') < 2026 ? 2026 : date('Y');
        $newCustomId = sprintf('%s-%03d', $nextYear, $paper_id);
        return $newCustomId;
    }


    public function authors_and_copyright($paper_id){
        $post = $this->request->getPost();

        $this->validate_user_access($paper_id);

        $UsersModel = (new UserModel());
        $papersModel = (new PapersModel());
        $paper = $papersModel->asArray()->find($paper_id);
        $UsersProfileModel = (new UsersProfileModel());
        $recentAuthors = (new PaperAuthorsModel())
            ->select('paper_authors.*, u.name, u.surname')
            ->join($UsersModel->db->database.'.users u', 'paper_authors.author_id = u.id', 'left')
            ->join($papersModel->table. ' p', 'paper_authors.paper_id = p.id', 'left')
            ->where('p.user_id', session('user_id'))
            ->where('author_type', 'author')
            ->findAll();

        $disclosure_current_date = (new SiteSettingModel())->where('name', 'disclosure_current_date')->first()['value'];

        $header_data = [
            'title' => "Authors and Disclosure Panel"
        ];
        $data = [
            'id' => $this->request->uri->getSegment(4),
            'paper_id' => $paper_id,
            'paper'=> $paper ? :'',
            'recentAuthors'=>$recentAuthors,
            'disclosure_current_date'=>$disclosure_current_date,
            'previous_url' => previous_url(),
            'previous_page' => service('uri')->setURI(previous_url())->getSegment($this->setSegment(3))?? '',
        ];
        return
            view('event/common/header', $header_data).
            view('event/author_and_copyright',$data).
            view('event/common/footer')
            ;
    }

    public function get_study_groups(){
        $study_groups = $this->db->table($this->shared_db_name . '.users')->where('is_study_group', 1)->get()->getResultArray();
        return $this->response->setJSON([
            'status'=> 200,
            'message'=>'success',
            'data'=> $study_groups ?? []
        ]);
    }

    public function level_of_evidence($paper_id){
        $this->validate_user_access($paper_id);
        $paper = (new PapersModel())->asArray()->find($paper_id);

        $header_data = [
            'title' => "Level of Evidence"
        ];

        $data = [
            'paper_id' => $paper_id,
            'paper'=> $paper ?:'',
            'previous_url' => previous_url(),
            'previous_page' => service('uri')->setURI(previous_url())->getSegment($this->setSegment(3))?? '',
        ];
//        print_r($paper);exit;
        return
            view('event/common/header', $header_data).
            view('event/level_of_evidence',$data).
            view('event/common/footer')
            ;
    }

    public function search_author_ajax(){
        $post = $this->request->getpost();
        $UsersModel = (new UserModel());
        $authorModel = (new UsersProfileModel());
        if($post){
            try {
                $authors = $authorModel
                    ->select('
                        users_profile.*, 
                        u.id as user_id,
                        u.email, 
                        u.surname, 
                        u.name, 
                        u.middle_name, 
                        u.is_study_group, 
                        i.name as institution_name, 
                        ci.name as institution_city, 
                        co.name as institution_country
                    ')
                    ->join($UsersModel->db->database.'.users u','users_profile.author_id = u.id', 'right')
                    ->join($UsersModel->db->database.'.users_profile up', 'u.id = up.author_id', 'right')
                    ->join((new InstitutionModel())->db->database . '.institution i', 'up.institution_id = i.id', 'left')
                    ->join((new CitiesModel())->db->database . '.cities ci', 'i.city_id = ci.id', 'left')
                    ->join((new CountriesModel())->db->database . '.countries co', 'ci.country_id = co.id', 'left')
                    ->like('LOWER(u.surname)', strtolower($post['searchValue']['authorName']))
                    // ->where('u.id !=', session('user_id')) // Uncomment if needed
                    ->findAll();

                if(($authors))
                    echo json_encode(array('status'=>'200', 'message'=>'Match found', 'data'=>$authors));
                else{
                    echo json_encode(array('status'=>'204', 'message'=>'No match found','data'=>$authors));
                }
            }catch (\Exception $e){
                echo json_encode(array('status'=>'204', 'message'=>$e->getMessage(),'data'=>''));
            }
        }else{
            echo json_encode(array('status'=>'204', 'message'=>'Missing search value','data'=>''));
        }
    }

//
    public function get_paper_authors(){
        $post = $this->request->getPost();
        $LogsModel = (new LogsModel());
        if(!empty($post['author_type']))
            $author_type = trim($post['author_type']);
        else
            $author_type = 'author';
        $UsersModel = (new UserModel());
        $UsersProfileModel = (new UsersProfileModel());
        $PaperAuthorsModel= (new PaperAuthorsModel());

        $query = $PaperAuthorsModel->select('*, paper_authors.id as id, users.id as author_id, users.name, users.surname, users.middle_name')
            ->join($UsersModel->db->database.'.users', 'paper_authors.author_id = users.id', 'left')
            ->join($UsersProfileModel->db->database.'.users_profile', 'users.id = users_profile.author_id', 'left')
            ->join('removed_paper_authors', 'paper_authors.id = removed_paper_authors.paper_author_id', 'left')
            ->where('paper_authors.paper_id', $post['paper_id'])
            ->where('author_type', $author_type)
            ->whereNotIn('paper_authors.id', function ($builder) {
                $builder->select('paper_author_id')->from('removed_paper_authors');
            })
            ->orderBy('paper_authors.author_order', 'asc')
            ->orderBy('paper_authors.id', 'asc');

// Execute the query
        $paperAuthors = $query->findAll();


        $paperAuthorsArray = array();
        foreach ($paperAuthors as $author){
            $mailLogs = $LogsModel
                ->where('ref_1', $author['author_id'])
                ->where('user_id', session('user_id'))
                ->where('context', 'copyright')
                ->where('action', 'email')
                ->where('message', 'sent')
                ->orderBy('id', 'desc')
                ->first();

            $author['mailLogs'] = $mailLogs;

            $paperAuthorsArray[] = $author;
        }


        if($paperAuthors){
            return json_encode(array('status'=>'200', 'message'=>'success','data'=>$paperAuthorsArray));
        }
    }

    public function assign_abstract_author($post = null) {

        if($post == null){
            $post = $this->request->getPost();
        }

        $this->validate_user_access($post['paper_id']);

        $PaperAuthorModel = new PaperAuthorsModel();
        $message = array();
        $duplicate = 0;
        $duplicateAuthor = [];
        $author_type = (isset($post['author_type']) ? $post['author_type'] : null);
        try {
            if (!empty($post['author_ids']) && $post['paper_id'] !== '') {
                foreach ($post['author_ids'] as $index => $author_id) {
                    $checkAbstractAuthor = $this->checkAbstractAuthor($author_id, $post['paper_id'], $author_type); //Todo: fix cant add multiple author at once

                    if (empty($checkAbstractAuthor)) {

                        $assignedAuthorExceed = $this->checkAssignedAuthorExceed($post['paper_id']);

                        if (!$assignedAuthorExceed) {
                            $resultID = $this->assignToPaper($author_id, $post['paper_id'], $author_type);
                            if (is_int($resultID)) {
//                                if ($this->send_author_email($resultID) !== 'success') {
//                                    $message[] = json_encode(['status' => '500', 'message' => 'Error sending author email', 'data' => '']);
//                                }
                            } else {
                                $message[] = json_encode(['status' => '500', 'message' => 'Error assigning to paper', 'data' => $resultID]);
                            }
                        } else {
                            $message[] = json_encode(['status' => '500', 'message' => 'Author count exceeds or database setting not configured', 'data' => '']);
                        }
                    }
                    else {
                        $duplicate = $duplicate + 1;
                        foreach ($checkAbstractAuthor as $val){
                            $duplicateAuthor[] = (new UserModel())->find($val['author_id']);;
                        }
                    }
                }
            } else {
                $message[] = json_encode(['status' => '500', 'message' => 'Error: Empty author ID or paper ID', 'data' => '']);
            }
        } catch (\Exception $e) {
            return json_encode(['status' => '500', 'message' => $e->getMessage(), 'data' => '']);
        }

        if($message)
            return json_encode($message);
        else
            return json_encode(['status' => '200', 'message' => 'Success', 'data' => ['duplicate'=>$duplicateAuthor]]);
    }


    function checkAbstractAuthor($author_id, $paper_id, $author_type = null){

        $PaperAuthorsModel = (new PaperAuthorsModel());
        $RemovedPaperAuthorsModel = (new RemovedPaperAuthorModel());

        if(empty($author_type)){
            $author_type = 'author';
        }
        try{
            $result = $PaperAuthorsModel
                ->select('*')
                ->where('author_id', $author_id)
                ->where('paper_id', $paper_id)
                ->where('author_type', $author_type)
                ->findAll();

            if($result){
                foreach ($result as $val){
                    $RemovedPaperAuthorsModel
                        ->where('paper_author_id', $val['id'])
                        ->delete();
                }
            }

            return $result;
        } catch (\Exception $e) {
            return  $e->getMessage();
        }
    }

    private function checkAssignedAuthorExceed($paper_id){
       $SiteSettingsModel = (new SiteSettingModel());
       $siteSetting = $SiteSettingsModel->where('name', 'number_of_authors')->first();

       $PaperAuthorsModel = (new PaperAuthorsModel());
       $paperAuthorsCount = ($PaperAuthorsModel->where('paper_id', $paper_id)->findAll());

        if(count($siteSetting) > 0){
            if(count($paperAuthorsCount) >= $siteSetting['value']){
                return true;
            }else{
                return false;
            }
        }
        return true;
    }

    private function assignToPaper($author_id, $paper_id , $author_type = null)
    {
        helper('text');
        $paperAuthorsModel = (new PaperAuthorsModel());
        $insertArray = [
            'paper_id'=> $paper_id,
            'author_id'=> $author_id,
            'date_time'=> date('Y-m-d H:i:s')
        ];



        if(!empty($author_type)){
            $insertArray['author_type'] = $author_type;
        }

        try {
            $paperAuthors = $paperAuthorsModel->set($insertArray)->insert();
            if($paperAuthors){
                return $paperAuthorsModel->getInsertID();
            }else{
                return '';
            }
        }catch (\Exception $e) {
            return json_encode(array('status'=>'500', 'message'=> $e->getMessage(),'data'=>''));
        }
    }

    public function add_author_ajax(){

        $db = db_connect();
        $post = $this->request->getPost();

        $UsersProfileModel = (new UsersProfileModel());
        $UserModel = (new UserModel());
        $UsersProfileModel = (new UsersProfileModel());
        $users = $UserModel->where('email', $post['authorEmail'])->first();

        if(!empty($users) ){
            return json_encode(array('status'=>'400', 'message'=>'User Already Exist','data'=>$users));
        }else{
            $insertUsersArray = [
                'name' => $post['authorFName'],
                'surname' => $post['authorLName'],
                'middle_name' => $post['authorMName'],
                'author_type'=> $post['author_type'] ?? 'author',
                'email' => $post['authorEmail'],
                'is_study_group' => isset($post['is_study_group']) ? 1: 0
            ];

            try {
                $db->transBegin();

                $userResult = $UserModel->set($insertUsersArray)->insert();

                if ($userResult) {
                    $insertAuthorDetailsArray = [
//                        'deg' => $post['authorDegree']?:'',
                        'phone' => $post['authorPhone']?:'',
                        'cellphone' => $post['cellphone']?:'',
                        'institution' => $post['authorInstitution']?:'',
                        'institution_id' => $post['authorInstitutionId']?:'',
//                        'address' => $post['authorAddress']?:'',
//                        'city' => $post['authorCity']?:'',
//                        'country' => $post['authorCountry']?:'',
//                        'province' => $post['authorProvince']?:'',
//                        'zipcode' => $post['authorZipcode']?:'',
                        'author_id' => $userResult,
                        'designations' => !empty($post['designations']) ? json_encode($post['designations']) : '',
                        'other_designation' => $post['other_designation'] ?? '',
                    ];

                    $UsersProfileModel->set($insertAuthorDetailsArray)->insert();
                }

                $db->transCommit();
                return json_encode(array('status'=>'200', 'message'=>'Author Added Successfully','data'=>$userResult));
            } catch (\Exception $e) {
                // Handle the database error
                $db->transRollback();
                return json_encode(array('status'=>'500', 'message'=>'Error:','data'=>$e->getMessage()));
            }
        }

    }

    public function get(){
        $post = $this->request->getPost();
        $UsersModel = (new UserModel());
        $user = $UsersModel->find( $post['user_id']);

        if($user){
            echo json_encode(array('status'=>'200', 'message'=>'Database Error','data'=>$user));
        }else{
            echo json_encode(array('status'=>'500', 'message'=>'Empty User','data'=>''));
        }
    }

//
    public function get_institution(){
        $post = $this->request->getPost();
        $institutionModel = (new InstitutionModel());
        $institutions = $institutionModel->like("name", trim($post['name']))->findAll();

        if($institutions) {
            echo(json_encode($institutions));
        }
        return '';
    }

    public function get_author_info(){

        $post = $this->request->getPost();
//        print_r($post);exit;
        $UsersProfileModel = (new UsersProfileModel());
        $InstitutionModel = (new InstitutionModel());
        $UserModel = (new UserModel());
        $authorDetails = $this->db->table($this->shared_db_name . '.users')
            ->select('users.*, up.*, i.name as institution_name')
            ->join($this->shared_db_name . '.users_profile up', 'users.id = up.author_id', 'left')
            ->join($InstitutionModel->getTable() . ' i', 'up.institution_id = i.id', 'left')
            ->where('users.id', $post['author_id'])
            ->get()
            ->getRowArray();
        if($authorDetails){
            echo json_encode(array('status'=>'200', 'message'=>'success','data'=>$authorDetails));
        }else{
            echo json_encode(array('status'=>'500', 'message'=>'Empty User','data'=>''));
        }
    }

    public function get_designations(){
        return $this->response->setJSON((new DesignationsModel())->findAll());
    }
//
    public function update_author_details(){

        $UserModel = (new UserModel());
        $UsersProfileModel = (new UsersProfileModel());

        $post = $this->request->getPost();

        // filter if the user is trying to update an author with same existing author on the database/
        $existingUser = $UserModel
            ->where('email',$post['authorEmail'])
            ->where('id !=',$post['author_id'])
            ->first();

        $existingAuthorDetails = $UsersProfileModel
            ->where('author_id',$post['author_id'])
            ->first();

        $insertUsersArray = [
            'name' => $post['authorFName'],
            'surname' => $post['authorLName'],
            'middle_name' => $post['authorMName'],
            'email' => $post['authorEmail'],
            'is_study_group' => isset($post['is_study_group']) ? 1 : 0
        ];

        $insertAuthorDetailsArray = [
//            'deg' => $post['authorDegree']?:'',
            'institution' => $post['authorInstitution']?:'',
            'institution_id' => $post['authorInstitutionId']?:'',
            'phone' => $post['authorPhone']?:'',
            'cellphone' => $post['cellphone']?:'',
//            'address' => $post['authorAddress']?:'',
//            'city' => $post['authorCity']?:'',
//            'country' => $post['authorCountry']?:'',
//            'province' => $post['authorProvince']?:'',
//            'zipcode' => $post['authorZipcode']?:'',
            'designations' => !empty($post['designations']) ? json_encode($post['designations']): '',
            'other_designation' => $post['other_designation'] ?? '',
        ];


        if($existingUser && $existingAuthorDetails){
            return json_encode(array('status'=>'400', 'message'=>'Email already used by other user','data'=>$existingUser));
        }


        // Start the transaction for UserModel
        $UserModel->db->transBegin();

        try {
            // Perform your database operations using the model
            $res1 = $UserModel->where('id', $post['author_id'])->set($insertUsersArray)->update();

            // Start the transaction for UsersProfileModel
            $UsersProfileModel->db->transBegin();

            try {
                // Perform your database operations using the model
                if(!$existingUser && !$existingAuthorDetails){
                    $insertAuthorDetailsArray['author_id'] = $post['author_id'];
                    $UsersProfileModel->set($insertAuthorDetailsArray)->insert();
                }else {
                    $UsersProfileModel->where('author_id', $post['author_id'])->set($insertAuthorDetailsArray)->update();
                }

                // Commit both transactions if all operations are successful
                $UsersProfileModel->db->transCommit();
                $UserModel->db->transCommit();

                return json_encode(array('status'=>'200', 'message'=>'Success','data'=>''));
            } catch (\Exception $e) {
                // Rollback the transaction for UsersProfileModel if any operation fails
                $UsersProfileModel->db->transRollback();

                return json_encode(array('status'=>'500', 'message'=>'Transaction failed:','data'=>$e->getMessage()));
            }
        } catch (\Exception $e) {
            // Rollback the transaction for UserModel if any operation fails
            $UserModel->db->transRollback();

            return json_encode(array('status'=>'500', 'message'=>'Transaction failed:','data'=>$e->getMessage()));
        }
    }
//
//    public function uploadHeadShot($abstract_id, $author_id){
//        //  print_r($_FILES);exit;
//         if ($this->request->getMethod() == 'post') {
//
//            $file = $this->request->getFile('headShot');
//            $file_name = $file->getName();
//
//            if ($file->isValid() && ! $file->hasMoved()) {
//                $newName = $file->getRandomName();
//                $filePath = "/uploads/abstract/".$abstract_id."/author/".$author_id.'/';
//                $savePath = FCPATH.$filePath;
//
//                if(is_dir(FCPATH.$filePath)) {
//                if($file->move($savePath, $newName)){
//                    return array('new_name'=>$newName, 'file_name'=>$file_name,  'savePath'=>$savePath, 'filePath'=>$filePath);
//                }
//                }else{
//                    if(mkdir(FCPATH.$filePath, 0777, true)){
//                        if($file->move($savePath, $newName)){
//                            return array('new_name'=>$newName, 'file_name'=>$file_name,  'savePath'=>$savePath, 'filePath'=>$filePath);
//                        }
//                    }else{
//                        return 'error';
//                    }
//                }
//            }else{
//                return 'invalid file';
//            }
//        }
//
//    }

    public function update_paper_authors(){
        $post = $this->request->getPost();
        $this->validate_user_access($post['paper_id']);

        $PaperAuthorsModel = new PaperAuthorsModel();
        $LogsModel = new LogsModel();

        $sendToId = array();
//        print_r($post['presenting_authors']);exit;
        try {
            // Begin transaction
            $PaperAuthorsModel->db->transBegin();

            // Validate paper_id
            if (empty($post['paper_id'])) {
                throw new \Exception('Paper ID is missing');
            }

            // Update all authors' roles to 'No' and 'null'
            $PaperAuthorsModel
                ->set('is_presenting_author', 'No')
                ->set('is_correspondent', 'No')
                ->set('is_senior_author', 'No')
                ->set('author_order', null)
                ->where('paper_id', $post['paper_id'])
                ->update();

            // Update author orders
            if (isset($post['author_orders'])) {
                foreach ($post['author_orders'] as $index => $author_order) {
                    if ($author_order !== '') {
                        $PaperAuthorsModel
                            ->set('author_order', $index + 1)
                            ->where('author_id', $author_order)
                            ->where('paper_id', $post['paper_id'])
                            ->update();
                    }
                }
            }

            // Update Senior Author
            if (isset($post['senior_author_id']) && !empty($post['senior_author_id'])) {
                $PaperAuthorsModel
                    ->set('is_senior_author', 'Yes')
                    ->set('update_date_time', date('Y-m-d H:i:s'))
                    ->where('author_id', $post['senior_author_id'])
                    ->where('paper_id', $post['paper_id'])
                    ->update();
            }


            // Update correspondents
            if (isset($post['selectedCorrespondents']) && !empty($post['selectedCorrespondents'])) {
                foreach ($post['selectedCorrespondents'] as $selectedCorrespondent) {
//                    $logs = $LogsModel
//                        ->where('ref_1', $selectedCorrespondent)
//                        ->where('user_id', session('user_id'))
//                        ->where('context', 'copyright')
//                        ->where('action', 'email')
//                        ->where('message', 'sent')
//                        ->findAll();
//
////                    print_r($selectedCorrespondent);exit;
//                    if (empty($logs)) {
//                        $sendToId[] = $selectedCorrespondent;
//                    }

                    $PaperAuthorsModel
                        ->set('is_correspondent', 'Yes')
                        ->set('update_date_time', date('Y-m-d H:i:s'))
                        ->where('author_id', $selectedCorrespondent)
                        ->where('paper_id', $post['paper_id'])
                        ->update();
                }
            }

            // Update presenting authors
            if (!empty($post['presenting_authors'])) {
                foreach ($post['presenting_authors'] as $presenting_author) {
//                    print_r($presenting_author);exit;
//                    $logs = $LogsModel
//                        ->where('ref_1', $presenting_author)
//                        ->where('user_id', session('user_id'))
//                        ->where('context', 'copyright')
//                        ->where('action', 'email')
//                        ->where('message', 'sent')
//                        ->findAll();
////
//                    if (empty($logs)) {
//                        $sendToId[] = $presenting_author;
//                    }

                    $PaperAuthorsModel
                        ->set('is_presenting_author', 'Yes')
                        ->set('update_date_time', date('Y-m-d H:i:s'))
                        ->where('author_id', $presenting_author)
                        ->where('paper_id', $post['paper_id'])
                        ->update();
                }
            }


           if(!$this->update_paper_ajax()){
                throw new \Exception('Error updating paper');
           }


            // Check transaction status
            if ($PaperAuthorsModel->db->transStatus() === false) {
                // Rollback and throw exception to catch block
                $PaperAuthorsModel->db->transRollback();
                throw new \Exception('Transaction status is false');
            } else {
                // Commit transaction
//                $email_error = 0;
                $PaperAuthorsModel->db->transCommit();
//                $sendToId = array_unique($sendToId);
//                foreach ($sendToId as $id){
//                    $emailResult = $this->send_author_email($id, 12, 'copyright');
//                    if($emailResult == 'success'){
//                        $email_error++;
//                    }
//                }
                return json_encode(array('status' => '200', 'message' => 'Success', 'data' => ''));

            }
        } catch (\Exception $e) {
            // Rollback transaction and log the error
            $PaperAuthorsModel->db->transRollback();
            error_log('Transaction failed: ' . $e->getMessage());
            return json_encode(array('status' => '500', 'message' => 'Transaction failed: ' . $e->getMessage(), 'data' => $e->getMessage()));
        }
    }


//
//    public function add_new_institution(){
//        $result = $this->api->post("user/add_new_institution/{$this->event_uri}", $_POST);
//        if(!$result->status){
//            return (new ErrorHandler($result->data))->errorPage();
//        }
//        echo (json_encode(($result)));exit;
//    }
//
//
    public function remove_paper_author() {
        $post = $this->request->getPost();
        $RemovedPaperAuthorModel = new RemovedPaperAuthorModel();

        try {
            $removedPaperAuthors = $RemovedPaperAuthorModel->where('paper_author_id', $post['paper_author_id'])->findAll();
            if (empty($removedPaperAuthors)) {
                $RemovedPaperAuthorModel->insert(['paper_author_id' => $post['paper_author_id']]);
                return json_encode(['status' => 200, 'message' => 'Transaction success']);
            }
        } catch (\Exception $e) {
            return json_encode(['status' => 500, 'message' => 'Transaction failed', 'error' => $e->getMessage()]);
        }
    }

    function send_author_email($author_id, $template_id, $context) {
        $sendMail = new PhpMail();
        try {
            $post = $this->request->getPost();
            // Initialize configuration
            $UsersModel = (new UserModel());
            $PaperAuthorsModel = (new PaperAuthorsModel());
            $PapersModel = (new PapersModel());
            $paperAuthors = $PaperAuthorsModel
                ->join($UsersModel->table, 'paper_authors.author_id = users.id')
                ->join('papers', 'paper_authors.paper_id = papers.id', 'left')
                ->where('author_id', $author_id)
                ->first();

            $papers = $PapersModel
                ->select('users.name as submitter_name, users.surname as submitter_surname')
                ->join('users', 'papers.user_id = users.id')
                ->find($post['paper_id']);


            $MailTemplates = (new EmailTemplatesModel())->find($template_id);

            $email_body = $MailTemplates['email_body'];
            $email_body = str_replace('##ABSTRACT_ID##', $post['paper_id'], $email_body);
            $email_body = str_replace('##ABSTRACT_TITLE##', strip_tags($paperAuthors['title']), $email_body);
            $email_body = str_replace('##RECIPIENTS_FULL_NAME##', ucFirst($paperAuthors['name']).' '.ucFirst($paperAuthors['surname']), $email_body);
            $email_body = str_replace('##SUBMITTER_NAME##', ucFirst($papers->submitter_name), $email_body);
            $email_body = str_replace('##SUBMITTER_SURNAME##', ucFirst($papers->submitter_surname), $email_body);

            $from = ['name'=>'Asia Pacific 2026', 'email'=>'ap@owpm2.com'];
            $addTo = $paperAuthors['email'];
            $subject = $MailTemplates['email_subject'];
            $addContent = $email_body;

            $response = $sendMail->send($from, $addTo, $subject, $addContent);

            $email_logs_array = [
                'user_id' => session('user_id'),
                'add_to' => ($addTo),
                'subject' => $subject,
                'add_content' => $addContent,
                'send_from' => "Submitter",
                'send_to' => "Author",
                'level' => "Info",
                'template_id' => $MailTemplates['id'],
                'paper_id' => $post['paper_id'],
                'user_agent' => $this->request->getUserAgent()->getBrowser(),
                'ip_address' => $this->request->getIPAddress(),
            ];
//            print_r($response);exit;
            if ($response->statusCode == 200) {
                // Email sent successfully
//                save to logs
                $logs = new LogsModel();
                $emailLogs = [
                    'user_id' => session('user_id'),
                    'ref_1' => $author_id,
                    'action' => 'email',
                    'ip_address' => $this->request->getIPAddress(),
                    'user_agent' => $this->request->getUserAgent()->getBrowser(),
                    'level'=> 'INFO',
                    'message' => 'sent',
                    'context' => $context
                ];

               ($logs->save($emailLogs));

                $email_logs_array['status'] = 'Success';
                $emailLogsModel = (new EmailLogsModel())->saveToMailLogs($email_logs_array);

                return 'success';
            } else {
                // Email sending failed
                $email_logs_array['status'] = 'Failed';
                $emailLogsModel = (new EmailLogsModel())->saveToMailLogs($email_logs_array);
                return 'error';
            }
            // Send the email
        }catch (\Exception $e){
            return $e->getMessage();
        }
    }

    function send_confirmation_email($author_id, $template_id, $paper_id, $post, $context) {

        $html = $post['html'];

        $attachments = [
            'name' => [],
            'type' => [],
            'tmp_name' => [],
            'error' => [],
            'size' => []
        ];

        $sendMail = new PhpMail();
        try {
            $post = $this->request->getPost();
            // Initialize configuration
            $UsersModel = (new UserModel());
            $user = $UsersModel->find($author_id);
            $uploadedFiles = (new PaperUploadsModel())->where('paper_id', $post['paper_id'])->findAll();

            $MailTemplates = (new EmailTemplatesModel())->find($template_id);

            $email_body = $MailTemplates['email_body'];

            $from = ['name'=>env('MAIL_FROM'), 'email'=>env('MAIL_FROM_ADDRESS')];
            $addTo = $user['email'];

            $email_header = '<img id="main-banner" src="https://ap.owpm2.com/main_banner.png" class=" figure-img" alt="Main Banner" style="width: 100% !important;object-fit: cover; mix-blend-mode: multiply;">';

            $subject = $MailTemplates['email_subject'];
            $addContent = $email_header;
            $addContent .= $email_body;

            $embeded_images = [];
            if (!empty($_POST['preview_image'])) {
                $imageData = $_POST['preview_image'];

                // Decode the base64 string
                $imageData = str_replace('data:image/png;base64,', '', $imageData);
                $imageData = str_replace(' ', '+', $imageData);
                $decodedImage = base64_decode($imageData);

                // Create a temporary file
                $tempFile = tempnam(WRITEPATH, 'canvas_');
                file_put_contents($tempFile, $decodedImage);

                // Add to $attachments
                $attachments['name'][] = 'submission_details.png';
                $attachments['type'][] = 'image/png';
                $attachments['tmp_name'][] = $tempFile;
                $attachments['error'][] = 0;
                $attachments['size'][] = strlen($decodedImage);

                $embeded_images = [
                    [
                        'tmp_name' => $tempFile,  // Temporary file path
                        'name'     => 'submission_details.png',  // Image name
                    ],
                ];
            }

            $addContent .= '<p>Here are the submission details: <img src="{image0}" alt="Embedded Image" /></p>';

            if(!empty($uploadedFiles)) {
                foreach ($uploadedFiles as $uploadedFile) {
                    $filePath = FCPATH.$uploadedFile['file_path'].'/'.$uploadedFile['file_name'];
                    $relativePath = parse_url($filePath, PHP_URL_PATH);

                    if (file_exists($filePath)) {
                        $attachments['name'][] = $uploadedFile['file_preview_name'];
                        $attachments['type'][] = mime_content_type($filePath);
                        $attachments['tmp_name'][] = $filePath;
                        $attachments['error'][] = UPLOAD_ERR_OK;
                        $attachments['size'][] = filesize($filePath);
                    }
                }
            }

            $response = $sendMail->send($from, $addTo, $subject, $addContent, $attachments, $embeded_images);

            // Clean up
            if(!empty($tempFile))
                unlink($tempFile);

            $email_logs_array = [
                'user_id' => session('user_id'),
                'add_to' => ($addTo),
                'subject' => $subject,
                'add_content' => $addContent,
                'send_from' => "Submitter",
                'send_to' => "Author",
                'level' => "Info",
                'template_id' => $MailTemplates['id'],
                'paper_id' => $post['paper_id'],
                'user_agent' => $this->request->getUserAgent()->getBrowser(),
                'ip_address' => $this->request->getIPAddress(),
            ];
//            print_r($response);exit;
            if ($response->statusCode == 200) {
                // Email sent successfully
//                save to logs
                $logs = new LogsModel();
                $emailLogs = [
                    'user_id' => session('user_id'),
                    'ref_1' => $author_id,
                    'ref_2' => $post['paper_id'],
                    'action' => 'email',
                    'ip_address' => $this->request->getIPAddress(),
                    'user_agent' => $this->request->getUserAgent()->getBrowser(),
                    'level'=> 'INFO',
                    'message' => 'sent',
                    'context' => $context
                ];

                ($logs->save($emailLogs));

                $email_logs_array['status'] = 'Success';
                $emailLogsModel = (new EmailLogsModel())->saveToMailLogs($email_logs_array);

                return 'success';
            } else {
                // Email sending failed
                $email_logs_array['status'] = 'Failed';
                $emailLogsModel = (new EmailLogsModel())->saveToMailLogs($email_logs_array);
                return 'error';
            }
            // Send the email
        }catch (\Exception $e){
            return $e->getMessage();
        }
    }

    public function presentation_upload($paper_id){
        $this->validate_user_access($paper_id);
        $paper = (new PapersModel())->asArray()->find($paper_id);
        if(!$paper)
            exit;
        $header_data = [
            'title' => "Image Upload"
        ];
        $data = [
            'paper_id'=> $paper_id,
            'paper' => $paper,
            'previous_url' => previous_url(),
            'previous_page' => service('uri')->setURI(previous_url())->getSegment($this->setSegment(3))?? '',
        ];
        return
            view('event/common/header', $header_data).
            view('event/presentation_upload',$data).
            view('event/common/footer')
            ;
    }

    public function presentation_do_upload() {
        $PaperUploadsModel = new PaperUploadsModel();
        $PapersModel = new PapersModel();
        $post = $this->request->getPost();

        $sendMail = new PhpMail();

        // ✅ Get site settings
        $siteSettings = (new SiteSettingModel())->findAll();

        // ✅ Get allowed file types and upload limit from settings
        $allowedTypes = [];
        $uploadLimit = 0;
        foreach ($siteSettings as $setting) {
            if ($setting['name'] === 'presentation_upload') {
                $allowedTypes = explode(',', $setting['value']);
            }
            if ($setting['name'] === 'presentation_upload_count') {
                $uploadLimit = intval($setting['value']);
            }
        }

        $filename = $_FILES['file']['name'];
        $file_type = $_FILES['file']['type'];
        $file_size = $_FILES['file']['size'];
        $file_extension = pathinfo($filename, PATHINFO_EXTENSION);

        // ✅ Check if file type is allowed
        if (!in_array($file_extension, $allowedTypes)) {
            return json_encode(['status' => 401, 'message' => 'File type not allowed!']);
        }

        // ✅ Check current upload count for this paper
        $currentUploadCount = $PaperUploadsModel->where('paper_id', $post['paper_id'])->countAllResults();
        if ($currentUploadCount >= $uploadLimit) {
            return json_encode(['status' => 401, 'message' => 'Upload limit reached!']);
        }

        $papers = $PapersModel->find($post['paper_id']);
        if (!$papers) {
            return json_encode(['status' => 404, 'message' => 'Paper not found!']);
        }

        $customName = $papers->custom_id . '_' . $filename;
        $filePath = "/uploads/presentation/" . $post['paper_id'] . "/";
        $savePath = FCPATH . $filePath;

        $files = $this->request->getFiles('file');
        $uploadResult = $this->doUpload($files, $filePath, $savePath, $customName);

        // ✅ Ensure upload result is valid
        if (!empty($uploadResult) && isset($uploadResult['new_name'])) {
            try {
                $reviews = (new AbstractReviewModel())->where('abstract_id', $papers->id)->findAll();
                $MailTemplates = (new EmailTemplatesModel())->find($reviews ? 16 : 7);

                $email_body = $MailTemplates['email_body'];

                $assignedUsers = (new PaperAssignedReviewerModel())
                    ->join($this->shared_db_name.'.users', 'reviewer_id = users.id')
                    ->join($this->shared_db_name.'.users_profile', 'users.id = users_profile.author_id')
                    ->where([
                        'reviewer_type' => 'regular',
                        'paper_id' => $post['paper_id'],
                        'is_declined' => 0,
                        'is_deleted' => 0
                    ])
                    ->findAll();

                foreach ($assignedUsers as $user) {
                    $user_divisions = json_decode($user['division_id']);
                    if (!empty($user_divisions) && in_array($papers->division_id, $user_divisions)) {
                        $PaperTemplates = str_replace(
                            ['##ABSTRACT_ID##', '##RECIPIENTS_FULL_NAME##', '##REVIEW_USERNAME##', '##REVIEW_PASSWORD##'],
                            [
                                $post['paper_id'],
                                ucFirst($user['name']) . ' ' . ucFirst($user['surname']),
                                $user['email'],
                                'Please reset your password in case forgotten. Thank you!'
                            ],
                            $email_body
                        );

                        $from = ['name' => 'Asia Pacific 2026', 'email' => 'ap@owpm2.com'];
                        $addTo = $user['email'];
                        $subject = $MailTemplates['email_subject'];

                        $result = $sendMail->send($from, $addTo, $subject, $PaperTemplates);

                        // ✅ Save to email logs
                        $email_logs_array = [
                            'user_id' => session('user_id'),
                            'add_to' => $addTo,
                            'subject' => $subject,
                            'ref_1' => 'presentation_upload',
                            'add_content' => $PaperTemplates,
                            'send_from' => "Submitter",
                            'send_to' => "Reviewers",
                            'level' => "Info",
                            'template_id' => $MailTemplates['id'],
                            'paper_id' => $post['paper_id'],
                            'user_agent' => $this->request->getUserAgent()->getBrowser(),
                            'ip_address' => $this->request->getIPAddress(),
                            'status' => ($result->statusCode == 200) ? 'Success' : 'Failed'
                        ];

                        (new EmailLogsModel())->saveToMailLogs($email_logs_array);
                    }
                }

                // ✅ Insert upload record into the database
                $PaperUploadsModel->insert([
                    'paper_id' => $post['paper_id'],
                    'file_preview_name' => $customName,
                    'file_format' => $file_type,
                    'file_size' => $file_size,
                    'file_path' => $filePath,
                    'file_extension' => $file_extension,
                    'file_name' => $uploadResult['new_name'],
                    'created_at' => date('Y-m-d H:i:s')
                ]);

                return json_encode(['status' => 200, 'message' => 'Upload successful!', 'data' => '']);
            } catch (\Exception $e) {
                return json_encode(['status' => 500, 'message' => $e->getMessage(), 'data' => '']);
            }
        }

        return json_encode(['status' => 500, 'message' => 'Upload failed!', 'data' => '']);
    }

    function templateReplaceKewords(){

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

    public function getPaperUploads(){
        $post = $this->request->getPost();
        $PaperUploadsModel = (new PaperUploadsModel());
        $paperUploads = $PaperUploadsModel->where('paper_id', $post['paper_id'])->findAll();


        if(!empty($paperUploads)){
            return json_encode(['status' => 200, 'message' => 'success', 'data' => $paperUploads]);
        }else{
            return json_encode(['status' => 200, 'message' => 'success', 'data' => $paperUploads]);
        }
    }

    function delete_presentation_upload(){
        $post = $this->request->getPost();
        $PaperUploadsModel = (new PaperUploadsModel());
        $deleteResult = $PaperUploadsModel->delete($post['upload_id']);

        if($deleteResult){
            return json_encode(['status' => 200, 'message' => 'success', 'data' => '']);
        }
    }
//
    public function resend_disclosure_email(){
        $post = $this->request->getPost();
//        print_r($post);exit;
            $mailResult = $this->send_author_email($post['author_id'], 12, 'copyright');
        if($mailResult){
            return json_encode(['status' => 200, 'message' => 'success', 'data' => '']);
        }else{
            return json_encode(['status' => 401, 'message' => 'failed', 'data' => '']);
        }
    }
//
    public function finalize_paper($paper_id)
    {
        $this->validate_user_access($paper_id);

        $user_id = session('user_id');
        $post = $this->request->getPost();

        // Load Models
        $UsersProfileModel = new UsersProfileModel();
        $PapersModel = new PapersModel();
        $PaperAuthorsModel = new PaperAuthorsModel();
        $PaperUploadsModel = new PaperUploadsModel();
        $UsersModel = new UserModel();
        $InstitutionModel = new InstitutionModel();
        $CitiesModel = new CitiesModel();
        $CountriesModel = new CountriesModel();
        $DesignationsModel = new DesignationsModel();
        $AbstractCategoriesModel = new AbstractCategoriesModel();

        // Fetch Paper Details
        $paper = $PapersModel
            ->select('papers.*, paper_type.name as paper_type_name')
            ->join('paper_type', 'papers.type_id = paper_type.type', 'left')
            ->where(['user_id' => $user_id, 'papers.id' => $paper_id])
            ->first();

        if (!$paper) {
            return redirect()->back()->with('error', 'Paper not found.');
        }

        // Fetch Authors
        $authors = $PaperAuthorsModel
            ->select('u.*, up.*, paper_authors.*, i.name as institution_name, ci.name as institution_city, co.name as institution_country')
            ->join($this->shared_db_name.'.users u', 'paper_authors.author_id = u.id', 'left')
            ->join($this->shared_db_name.'.users_profile up', 'paper_authors.author_id = up.author_id', 'left')
            ->join($InstitutionModel->table . ' i', 'up.institution_id = i.id', 'left')
            ->join($CitiesModel->table . ' ci', 'i.city_id = ci.id', 'left')
            ->join($CountriesModel->table . ' co', 'ci.country_id = co.id', 'left')
            ->whereNotIn('paper_authors.id', function ($builder) {
                $builder->select('paper_author_id')->from('removed_paper_authors');
            })
            ->where('paper_id', $paper_id)
            ->orderBy('author_order', 'asc')
            ->findAll();

        // Fetch Designations for Each Author
        foreach ($authors as &$author) {
            $designations = !empty($author['designations']) ? json_decode($author['designations'], true) : [];
            $author['designations'] = array_map(function($id){
                return (new DesignationsModel())->find($id);
            }, $designations);
        }

        unset($author);

        // Fetch User Info
        $userInfo = $UsersModel->find($user_id);

        // Fetch Paper Uploads
        $paper_uploads = $PaperUploadsModel->where('paper_id', $paper_id)->findAll();

        // Fetch Categories
        $categories = $AbstractCategoriesModel->findAll();

        // Validation for Missing Fields
        $authorDetailsRequiredFields = [
            'institution_id' => 'Institution',
            'signature_signed_date' => 'Disclosure'
        ];

        $paperRequiredFields = [];

        $incomplete = [];

        // Check Required Author Fields
        foreach ($authors as $author) {
            foreach ($authorDetailsRequiredFields as $field => $label) {
                if (empty($author[$field])) {
                    $authorName = ucwords(trim($author['name'] . ' ' . $author['surname']));
                    $incomplete['author'][] = [
                        'required' => "$label for Author: $authorName",
                        'message'  => "$label for Author: $authorName"
                    ];
                }
            }
        }

        // Check Required Paper Fields
        foreach ($paperRequiredFields as $field => $label) {
            if (empty($paper[$field])) {
                $incomplete['paper'][] = [
                    'required' => "$label for Paper: $paper_id",
                    'message'  => "$label for Paper: $paper_id"
                ];
            }
        }

        //find users marked as study group
        $studyGroups = $UsersModel->where('is_study_group', 1)->findAll();

        // Prepare Data for View
        $header_data = ['title' => "Preview"];
        $data = [
            'papers' => $paper,
            'paper_id' => $paper_id,
            'userInfo' => $userInfo,
            'paper_uploads' => $paper_uploads,
            'incompleteStatus' => $incomplete,
            'authors' => $authors,
            'categories' => $categories,
            'study_groups' => $studyGroups
        ];

        return view('event/common/header', $header_data) .
            view('event/finalize_paper', $data) .
            view('event/common/footer');
    }


    public function save_finalize_paper(){
        $post = $this->request->getPost();

        // print_r($_POST);exit;
        $paper_id = $post['paper_id'];
        $PapersModel = (new PapersModel());
        $PaperAuthorsModel = (new PaperAuthorsModel());
        $RemovedPaperAuthorsModel = (new RemovedPaperAuthorModel());
        $paper_authors = (new PaperAuthorsModel())
            ->where('paper_id', $post['paper_id'])
            ->whereNotIn($PaperAuthorsModel->table . '.id', function ($builder) use ($RemovedPaperAuthorsModel) {
                $builder->select('paper_author_id')->from($RemovedPaperAuthorsModel->table);
            })
            ->findAll();

        try {
            foreach ($paper_authors as $author){
                if($author['is_correspondent'] == 'Yes') {
                    $this->send_confirmation_email($author['author_id'], 1, $paper_id, $post, 'finalized');
                }
            }

            if(session('user_id')){
                $this->send_confirmation_email(session('user_id'), 1, $paper_id, $post, 'finalized');
            }

            $finalize = $PapersModel->set('is_finalized', 1)->where('id', $paper_id)->update();

            if($finalize){
                return json_encode(['status' => 200, 'message' => 'success', 'data' => '']);
            }
        }catch (\Exception $e){
            return json_encode(['status' => 500, 'message' => 'error: '. $e->getMessage(), 'data' => '']);
        }
        return json_encode(['status' => 500, 'message' => 'error', 'data' => '']);
    }


    function get_author_logs($id, $paper_id){
        $LogsModel = (new LogsModel());
        $logs = $LogsModel
            ->where('ref_1', $id)
            ->where('ref_2', $paper_id)
            ->where('user_id', session('user_id'))
            ->where('context', 'finalized')
            ->where('action', 'email')
            ->where('message', 'sent')
            ->findAll();
        return $logs;
    }


    public function save_finalize_panel(){

        $post = $this->request->getPost();
        // print_r($_POST);exit;
        $paper_id = $post['paper_id'];
        $PapersModel = (new PapersModel());
        try {
            $finalize = $PapersModel->set('is_finalized', 1)->where('id', $paper_id)->update();

            if($finalize){

                return json_encode(['status' => 200, 'message' => 'success', 'data' => '']);
            }
        }catch (\Exception $e){
            return json_encode(['status' => 500, 'message' => 'error: '. $e->getMessage(), 'data' => '']);
        }
        return json_encode(['status' => 500, 'message' => 'error', 'data' => '']);
    }

    public function get_user_info(){
        $result = session('user_id');
        $result = (new UserModel())->find(session('user_id'));

        if($result) {
            return json_encode(['status' => 200, 'message' => 'error', 'data' => $result]);
        }

        return redirect()->to(base_url().'login');
    }
//

    public function update_user_info($event_uri){
        $_POST['user_id'] = session('user_id');

        return '';
    }

    public function send_support_mail(){
        if ($this->request->getMethod() != 'post')return $this->response->setStatusCode(405, "{$this->request->getMethod(true)} method is not allowed");
        $post = $this->request->getPost();
        $required_data = [
            'fname',
            'lname',
            'email',
            'message'
        ];

        foreach ($required_data as $required)
        {
            if (!array_key_exists($required, $post))
            {
                return json_encode(['status' => 400, "message" => "{$required} is required", 'data' => '']);
            }

            if ($post[$required] == '')
            {
                return json_encode(['status' => 400, "message" => "{$required} cannot be empty", 'data' => '']);
            }
        }

        $sendMail = new PhpMail();
        $from = ['email' => 'ap@owpm2.com', 'name' => 'Asia Pacific 2026'];
        $subject = 'Support Request From '.$post['fname']." ".$post['lname'];
        $message = "First Name: ".$post['fname']."<br>";
        $message .= "Last Name: ".$post['lname']."<br>";
        $message .= "Email Name: ".$post['email']."<br>";
        $message .= "Message: ".$post['message']."<br>";
        $to = ['rexterdayuta@gmail.com', 'shannon@owpm.com'];

        $response = $sendMail->send($from, $to, $subject, $message);

        // ###################  Save to Email logs #####################
        $email_logs_array = [
            'user_id' => session('user_id') ?? '',
            'add_to' => (json_encode($to)),
            'subject' => $subject,
            'ref_1' => 'support',
            'add_content' => $message,
            'send_from' => "Submitter",
            'send_to' => "Author",
            'level' => "Info",
            'template_id' => 0,
            'paper_id' => isset($post['abstract_id']) ?? '',
            'user_agent' => $this->request->getUserAgent()->getBrowser(),
            'ip_address' => $this->request->getIPAddress(),
        ];
        // End Save to Email Logs        #####################################

        if ($response->statusCode == 200)
        {
            $email_logs_array['status'] = 'Success';
            $emailLogsModel = (new EmailLogsModel())->saveToMailLogs($email_logs_array);
            return json_encode(['status' => 200, "message" => "Email Sent!", 'data' => '']);
        }
        else
        {
            $email_logs_array['status'] = 'Failed';
            $emailLogsModel = (new EmailLogsModel())->saveToMailLogs($email_logs_array);
            return json_encode(['status' => 400, "message" => "Email Failed!", 'data' => '']);
        }
    }

    public function quick_add_author(){

        $post = $this->request->getPost();


        $RemovedPaperAuthor = (new RemovedPaperAuthorModel());
        $removedAuthor = $RemovedPaperAuthor
            ->join('paper_authors', 'removed_paper_authors.paper_author_id = paper_authors.id')
            ->where('paper_id', $post['paper_id'])
            ->where('paper_author_id', $post['author_id'])
            ->where('author_type', isset($post['author_type']) ?: 'author')
            ->first();

        if($removedAuthor) {
            try {
                $removeResult = $RemovedPaperAuthor->where('paper_author_id', $removedAuthor['id'])->delete();

                if ($removeResult) {
                    return $this->response->setJson(['status' => 200, 'message' => 'success', 'data' => '']);
                }
            } catch (\Exception $e) {
                return $this->response->setJson(['status' => 500, 'message' => 'error: ' . $e->getMessage(), 'data' => '']);
            }
        }
        return $this->response->setJson(['status' => 500, 'message' => 'You cannot add a duplicate author to the author list.', 'data' => '']);
    }
//
//    public function cv_upload(){
//        $_POST['user_id'] = session('user_id');
//
//
//        if ($this->request->getMethod() == 'post') {
//    // Retrieve the uploaded file from the AJAX request data
//            $file = $this->request->getFile('cv_upload', [
//                'max_size' => '10000', // Max file size in kilobytes
//                'ext_in' => 'pdf', // Allowed file extensions (comma-separated list)
//            ]);
//            $file_name = $file->getName();
//
//            // Check if the file is valid and has not been moved yet
//            if ($file->isValid() && !$file->hasMoved()) {
//                // Generate a new name for the file
//                $newName = $file->getRandomName();
//
//                // Set the file upload directory to '/uploads/cv/'
//                $filePath = "/uploads/cv/";
//                $savePath = FCPATH.$filePath;
//
//                // Check if the upload directory exists. If it does, move the uploaded file to that directory
//                if (is_dir(FCPATH.$filePath)) {
//
//                    if ($file->move($savePath, $newName)) {
//                        $_POST['cv_upload'] =  array('new_name'=>$newName, 'file_name'=>$file_name, 'savePath'=>$savePath, 'filePath'=>$filePath);
//                    }
//                } else {
//                    // If the directory does not exist, create the directory and move the uploaded file to it
//                    if (mkdir(FCPATH.$filePath, 0777, true)) {
//                        if ($file->move($savePath, $newName)) {
//                            $_POST['cv_upload'] = array('new_name'=>$newName, 'file_name'=>$file_name, 'savePath'=>$savePath, 'filePath'=>$filePath);
//                        }
//                    } else {
//                        return 'error';
//                    }
//                }
//
//            } else {
//                // If the uploaded file is not valid, return an error message
//
//                return 'invalid';
//            }
//        }
//
//
//        $result = $this->api->post("user/cv_upload/{$this->event_uri}", $_POST);
//        if(!$result->status){
//            return (new ErrorHandler($result->data))->errorPage();
//        }
//        if($result) {
//            echo(json_encode(($result)));
//        }
//        return '';
//    }


// ############## Pannel Submission #########################

    public function panel_coordinators($paper_id = null){ //todo: panel coordinators

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
            view('event/common/header', $header_data).
            view('event/panel_coordinators',$data).
            view('event/common/footer')
            ;
        }

    public function panel_submission(){

        $event = (new AbstractEventsModel())->first();
        if(!$event){
            return (new ErrorHandler($event))->errorPage();
        }

        $header_data = [
            'title' => $event->short_name
        ];
        $data = [
            'event'=> $event
        ];
        return
            view('event/common/header', $header_data).
            view('event/panel_submission',$data).
            view('event/common/footer')
            ;
    }

    public function panel_menu($paper_id){

        $event = (new EventsModel())->first();
        $PapersModel = (new PapersModel());
        $AuthorsModel = (new PaperAuthorsModel());
        $UsersProfileModel = (new UsersProfileModel());
        $PaperAuthorsModel = (new PaperAuthorsModel());
        $RemovedPaperAuthorsModel = (new RemovedPaperAuthorModel());
        $authors = $AuthorsModel
            ->join($UsersProfileModel->table, 'paper_authors.author_id = users_profile.author_id', 'left')
            ->where('paper_authors.paper_id', $paper_id)->findAll();

        $paper = $PapersModel->find($paper_id);

        $authorDetailsRequiredFields = [
            'electronic_signature', 'is_copyright_agreement_accepted', 'institution', 'deg', 'country', 'city', 'province'
        ];

        //         print_r($authors);exit;
        $paperUploads = (new PaperUploadsModel())->where('paper_id', $paper_id)->findAll();
        $paperRequiredFields = [
            'division_id', 'type_id', 'title', 'summary', 'is_ijmc_interested'
        ];

        $incomplete = [];
        foreach ($authors as $author) {
            foreach ($authorDetailsRequiredFields as $required) {
                if($author[$required] == '' ||$author[$required] == null ){
                    $incomplete['author'][]= 'required ' . $required . ' for author: ' . $author['author_id'];
                }
            }
        }

//         print_r($incomplete);exit;

        if(!$paperUploads){
            $incomplete['paperUpload'][] = 'required ' . 'Uploads' . ' for paper: ' . $paper_id;
        }

        if($paper){
            foreach ($paperRequiredFields as $required) {
                if($paper->$required == '' ||$paper->$required == null ){
                    $incomplete['paper'][] = 'required ' . $required . ' for paper: ' . $paper->id;
                }
            }
        }

        if($paper->is_finalized !== '1'){
            $incomplete['finalized'][] = 1;
        }

        if(!$event){
            return "error";
        }
        $header_data = [
            'title' => "Submission Menu"
        ];

        $data = [
            'event'=> $event,
            'paper_id'=>$paper_id,
            'papers' => $paper ?? [],
            'incompleteStatus' => $incomplete
        ];
        return
            view('event/common/header', $header_data).
            view('event/submission_menu',$data).
            view('event/common/footer')
            ;
    }

    public function assign_panel_coordinators()
    {
        $post = $this->request->getPost();
        $PaperAuthorModel = new PaperAuthorsModel();
        $paperModel = new PapersModel();
        $message = array();
        $duplicate = 0;

        //Note: if panel submission, an empty paper will be insert with a submission type "panel";
        $paperModel->db->transBegin();

        $post['author_type'] = 'coordinator';
//        print_r($post);exit;
        try {

            if($post['panel_paper_id'] == '') {
                $insertArray = [
                    'submission_type' => 'panel',
                    'user_id' => session('user_id')
                ];

                $insertPanelRecordID = $paperModel->set($insertArray)->insert();
                if($insertPanelRecordID) {
                    $newCustomId = $this->generateCustomID($insertPanelRecordID);
                    (new PapersModel())->set('custom_id', $newCustomId)->where('id', $insertPanelRecordID)->update();
                }
                $post['paper_id'] = $insertPanelRecordID;
            }else{
                $post['paper_id'] = $post['panel_paper_id'];
            }

            if (($post['paper_id'])) {


                $assignAbstract = json_decode($this->do_assign_panel_coordinator($post));
                if ($assignAbstract->status == 200) {
                    $paperModel->db->transCommit();
                    return json_encode(['status' => 200, 'message' => 'success', 'data' => $post['paper_id']]);
                } else {
                    // Handle error if assign_abstract_authorassign_abstract_author fails
                    $paperModel->db->transRollback();
                    // Add error handling or set a message indicating the failure
                }
            }
        } catch (\Exception $e) {
            $paperModel->db->transRollback();
        }
    }

    public function assign_panelist()
    {
        $post = $this->request->getPost();
        $PaperAuthorModel = new PaperAuthorsModel();
        $paperModel = new PapersModel();
        $message = array();
        $duplicate = 0;

        //Note: if panel submission, an empty paper will be insert with a submission type "panel";
        $paperModel->db->transBegin();

        $post['author_type'] = 'panelist';
//        print_r($post);exit;
        try {

            if($post['panel_paper_id'] == '') {
                $insertArray = [
                    'submission_type' => 'panel',
                    'user_id' => session('user_id')
                ];

                $insertPanelRecordID = $paperModel->set($insertArray)->insert();

                $post['paper_id'] = $insertPanelRecordID;
            }else{
                $post['paper_id'] = $post['panel_paper_id'];
            }

            if (($post['paper_id'])) {


                $assignAbstract = json_decode($this->do_assign_panel_panelist($post));
                if ($assignAbstract->status == 200) {
                    $paperModel->db->transCommit();
                    return json_encode(['status' => 200, 'message' => 'success', 'data' => $post['paper_id']]);
                } else {
                    // Handle error if assign_abstract_author fails
                    $paperModel->db->transRollback();
                    // Add error handling or set a message indicating the failure
                }
            }
        } catch (\Exception $e) {
            $paperModel->db->transRollback();
        }
    }

    function do_assign_panel_panelist($post){

        $PaperAuthorModel = new PaperAuthorsModel();
        $message = array();
        $duplicate = 0;

        $author_type = (isset($post['author_type']) ? $post['author_type'] : null);
        try {
            if (!empty($post['author_ids']) && $post['paper_id'] !== '') {
                foreach ($post['author_ids'] as $index => $author_id) {
                    $checkAbstractAuthor = $this->checkAbstractAuthor($author_id, $post['paper_id'], $author_type); //Todo: fix cant add multiple author at once

                    if (empty($checkAbstractAuthor)) {

                        $assignedAuthorExceed = $this->checkAssignedAuthorExceed($post['paper_id']);
                        $assignedAuthorExceed = '';
                        if (!$assignedAuthorExceed) {
                            $resultID = $this->assignToPaper($author_id, $post['paper_id'], $author_type);
                            if (is_int($resultID)) {

                           /*     $mailgunEmail = new MailGunEmail();
                                $UsersModel = (new UserModel());
                                $PaperAuthorsModel = (new PaperAuthorsModel());
                                $paperAuthors = $PaperAuthorsModel
                                    ->join($UsersModel->table, 'paper_authors.author_id = users.id')
                                    ->join('papers', 'paper_authors.paper_id = papers.id', 'left')
                                    ->find($resultID);

                                $msgBody = '';
                                $msgBody .= '<p>You have been named as an Panelist</p>';
                                $msgBody .= ' <p>Thank you.</p>';
                                $msgBody .= ' <p>AFS Administrator.</p>';

                                $result = $mailgunEmail->send("AFS <afs@owpm2.com>", $paperAuthors['email'], 'Panelist', $msgBody);

                                if ($result->getstatusCode !== 200) {
                                    $message[] = json_encode(['status' => '500', 'message' => 'Error sending author email', 'data' => '']);
                                }*/
                            } else {
                                $message[] = json_encode(['status' => '500', 'message' => 'Error assigning to paper', 'data' => $resultID]);
                            }
                        } else {
                            $message[] = json_encode(['status' => '500', 'message' => 'Author count exceeds or database setting not configured', 'data' => '']);
                        }
                    }
                    else {
                        $duplicate = $duplicate + 1;
                    }
                }
            } else {
                $message[] = json_encode(['status' => '500', 'message' => 'Error: Empty author ID or paper ID', 'data' => '']);
            }
        } catch (\Exception $e) {
            return json_encode(['status' => '500', 'message' => $e->getMessage(), 'data' => '']);
        }

        if($message)
            return json_encode($message);
        else
            return json_encode(['status' => '200', 'message' => 'You have already added this author.', 'data' => '']);
    }

    function do_assign_panel_coordinator($post){

            $PaperAuthorModel = new PaperAuthorsModel();
            $message = array();
            $duplicate = 0;

            $author_type = (isset($post['author_type']) ? $post['author_type'] : null);
            try {
                if (!empty($post['author_ids']) && $post['paper_id'] !== '') {
                    foreach ($post['author_ids'] as $index => $author_id) {
                        $checkAbstractAuthor = $this->checkAbstractAuthor($author_id, $post['paper_id'], $author_type); //Todo: fix cant add multiple author at once

                        if (empty($checkAbstractAuthor)) {

//                        $assignedAuthorExceed = $this->checkAssignedAuthorExceed($post['paper_id']);
                            $assignedAuthorExceed = '';
                            if (!$assignedAuthorExceed) {
                                $resultID = $this->assignToPaper($author_id, $post['paper_id'], $author_type);
                                if (is_int($resultID)) {

                                } else {
                                    $message[] = json_encode(['status' => '500', 'message' => 'Error assigning to paper', 'data' => $resultID]);
                                }
                            } else {
                                $message[] = json_encode(['status' => '500', 'message' => 'Author count exceeds or database setting not configured', 'data' => '']);
                            }
                        }
                        else {
                            $duplicate = $duplicate + 1;
                        }
                    }
                } else {
                    $message[] = json_encode(['status' => '500', 'message' => 'Error: Empty author ID or paper ID', 'data' => '']);
                }
            } catch (\Exception $e) {
                return json_encode(['status' => '500', 'message' => $e->getMessage(), 'data' => '']);
            }

            if($message)
                return json_encode($message);
            else
                return json_encode(['status' => '200', 'message' => 'Success, You have already added this author.', 'data' => '']);
        }



    public function edit_panel_coordinators($paper_id)
    {
        $post = $this->request->getPost();

        $event = (new AbstractEventsModel())->first();
        $papersModel = (new PapersModel());
        $papers = $papersModel->find($paper_id);
        $UsersProfileModel = (new UsersProfileModel());
        $UsersModel = (new UserModel());
        $recentAuthors = (new PaperAuthorsModel())
            ->join($UsersModel->table, 'paper_authors.author_id = users.id')
            ->where('paper_id', $paper_id)
            ->where('author_type', 'panelist')
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
            view('event/common/header', $header_data).
            view('event/panel_coordinators',$data).
            view('event/common/footer')
            ;
    }


    public function panel_submission_menu($paper_id){

        $event = (new EventsModel())->first();
        $PapersModel = (new PapersModel());
        $UsersProfileModel = (new UsersProfileModel());
        $DivisionsModel = (new DivisionsModel());
        $PaperTypeModel = (new PaperTypeModel());

        // Use the instantiated models to fetch the table names and perform the query
        $paper = $PapersModel
            ->select($PapersModel->table . '.*, ' . $DivisionsModel->table . '.name as division_name, ' . $PaperTypeModel->table . '.name as paper_type_name')
            ->join($DivisionsModel->table, $PapersModel->table . '.division_id = ' . $DivisionsModel->table . '.division_id', 'left')
            ->join($PaperTypeModel->table, $PapersModel->table . '.type_id = ' . $PaperTypeModel->table . '.type', 'left')
            ->where([$PapersModel->table . '.user_id' => session('user_id'), $PapersModel->table . '.id' => $paper_id])
            ->first();


        //         print_r($authors);exit;
        $paperUploads = (new PaperUploadsModel())->where('paper_id', $paper_id)->findAll();
        $paperRequiredFields = [
            'division_id', 'type_id', 'title', 'summary', 'is_ijmc_interested'
        ];

        $incomplete = array();


        $panelists = ($this->verifyPanelist($paper_id, $incomplete));
        $coordinators = ($this->verifyCoordinators($paper_id, $incomplete));

        if(!empty($panelists)){
            $incomplete = $panelists;
        }
        else if(!empty($coordinators)){
            $incomplete = $coordinators;
        }

        else if($paper){
            foreach ($paperRequiredFields as $required) {
                if($paper->$required == '' ||$paper->$required == null ){
                    $incomplete['paper'][] = 'required ' . $required . ' for paper: ' . $paper->id;
                }
            }
        }

        if($paper->is_finalized !== '1'){
            $incomplete['finalized'][] = 1;
        }

        if($paper->title == ''){
            $incomplete['paper'][] = 'required title for paper: ' . $paper->id;
        }

        if(!$event){
            return "error";
        }
        $header_data = [
            'title' => $event->short_name
        ];

        $data = [
            'event'=> $event,
            'paper_id'=>$paper_id,
            'papers' => ($papers ?? array()),
            'incompleteStatus' => $incomplete
        ];


        return
            view('event/common/header', $header_data).
            view('event/panel_submission_menu',$data).
            view('event/common/footer')
            ;
    }

    public function panel_overview($paper_id){

        $paper = (new PapersModel())->where('id', $paper_id)->first();
//        print_r($paper);exit;
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
            view('event/common/header', $header_data).
            view('event/panel_overview',$data).
            view('event/common/footer')
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
            view('event/common/header', $header_data) .
            view('event/panelist', $data) .
            view('event/common/footer');
    }

    public function update_panelist_submitted()
    {
        $post = $this->request->getPost();
        $PaperAuthorsModel = new PaperAuthorsModel();
        $PanelistPaperSubModel = new PanelistPaperSubModel();
        $PapersModel = new PapersModel();

        helper('text'); // Load text helper for generating random strings

        $customParentId = $PapersModel->select('custom_id')->find($post['paper_id']);

        if (!$customParentId) {
            return json_encode(['status' => '404', 'message' => 'Paper not found', 'data' => '']);
        }

        try {
            $PaperAuthorsModel->db->transBegin();

            // Reset panelist-related fields
            $PaperAuthorsModel
                ->set('is_presenting_author', 'No')
                ->set('is_correspondent', 'No')
                ->set('is_coauthor', 'No')
                ->set('author_order', null)
                ->where('paper_id', $post['paper_id'])
                ->where('author_type', 'panelist')
                ->update();

            if (isset($post['author_orders']) && is_array($post['author_orders'])) {
                foreach ($post['author_orders'] as $index => $author_order) {
                    if ($author_order !== '') {
                        $panelistCode = random_string('alnum', 10); // Generate unique code for each panelist

                        // Fetch existing custom_ids for the paper
                        $existingCustomIds = $PanelistPaperSubModel
                            ->select('custom_id')
                            ->where('paper_id', $post['paper_id'])
                            ->findColumn('custom_id');

                        $usedChars = [];

                        if ($existingCustomIds) {
                            foreach ($existingCustomIds as $existingId) {
                                $lastChar = substr($existingId, -1); // Extract the last character
                                if (ctype_alpha($lastChar)) {
                                    $usedChars[] = $lastChar;
                                }
                            }
                        }

                        $nextChar = 'A';
                        while (in_array($nextChar, $usedChars)) {
                            $nextChar++;
                        }

                        // Update panelist information
                        $PaperAuthorsModel
                            ->set('author_order', $index + 1)
                            ->set('custom_code', $panelistCode)
                            ->where('author_id', $author_order)
                            ->where('paper_id', $post['paper_id'])
                            ->where('author_type', 'panelist')
                            ->update();

                        // Construct custom ID
//                        $char = chr($index + ord('A'));
                        $customId = $customParentId->custom_id . '-' . $nextChar;

                        // Check if the panelist-paper relationship exists
                        $existingPanelistPaper = $PanelistPaperSubModel
                            ->where(['panelist_id' => $author_order, 'paper_id' => $post['paper_id']])
                            ->findAll();

                        if (empty($existingPanelistPaper)) {
                            $panelistPaperArray = [
                                'panelist_id' => $author_order,
                                'paper_id' => $post['paper_id'],
                                'date_time' => date('Y-m-d H:i:s'),
                                'custom_id' => $customId,
                            ];
                            $PanelistPaperSubModel->insert($panelistPaperArray);
                        }
                    }
                }
            }

            if ($PaperAuthorsModel->db->transStatus() === false) {
                $PaperAuthorsModel->db->transRollback();
                return json_encode(['status' => '500', 'message' => 'Transaction failed', 'data' => '']);
            } else {
                $PaperAuthorsModel->db->transCommit();

                $emailResult = $this->send_panelist_email_copyright($post['paper_id'], $panelistCode);

                $responseStatus = $emailResult === 'success' ? '200' : '201';
                return json_encode(['status' => $responseStatus, 'message' => 'Success', 'data' => '']);
            }
        } catch (\Exception $e) {
            $PaperAuthorsModel->db->transRollback();
            return json_encode(['status' => '500', 'message' => 'Transaction failed', 'data' => $e->getMessage()]);
        }
    }

    function send_panelist_email_copyright($paper_id, $panelistCode){

        $sendMail = new PhpMail();
        $from = ['email' => 'ap@owpm2.com', 'name' => 'Asia Pacific 2026'];

        $PaperAuthorModel = (new PaperAuthorsModel());
        $PapersModel = (new PapersModel());
        $UsersModel = (new UserModel());

        $paperAuthors = $PaperAuthorModel
            ->select($PaperAuthorModel->table . '.*, panelist.email AS panelist_email, panelist.name AS panelist_name, panelist.surname AS panelist_surname, submitter.name AS submitter_name, submitter.surname AS submitter_surname')
            ->join($UsersModel->table . ' AS panelist', $PaperAuthorModel->table . '.author_id = panelist.id', 'left')
            ->join($PapersModel->table, $PaperAuthorModel->table . '.paper_id = ' . $PapersModel->table . '.id', 'left')
            ->join($UsersModel->table . ' AS submitter', $PapersModel->table . '.user_id = submitter.id', 'left')
            ->where($PaperAuthorModel->table . '.paper_id', $paper_id)
            ->where($PaperAuthorModel->table . '.author_type', 'panelist')
            ->findAll();

        $paper = $PapersModel->find($paper_id);
        $subject = 'AP Metalcasting Congress 2025 - Copyright Agreement';


        try {
            if (!empty($paperAuthors)) {
                foreach ($paperAuthors as $author) {
//todo: need to transfer dynamic email template
                    $message = "<div>
                                <p><strong>Panel Session Invitation</strong></p>
                                <p>Dear " . ucfirst($author['panelist_name']) . " " . ucfirst($author['panelist_surname']) . ",</p>
                                <p>You have been invited to be a part of a Panel Session entitled, " . strip_tags($paper->title) . ". Please follow these instructions to make this submission:</p>
                                <p><strong>Step 1:</strong> Click on this link: <a href='" . base_url() . "author' target='_blank'>AFS Copyright</a></p>
                                <p><strong>Step 2:</strong> If you are a first-time user of this abstract system, you will be required to create a new user account and a unique password. 
                                If you have already set up a user account for the AFS abstract system, then you will need to log in using your email address and password. Once logged in,
                                you will be directed to the 'My Submissions' page. At the top of this page, please click on 'I have a panel session invitation code'. Once logged in, 
                                you will see three options.</p>
                                <p><strong>Step 3:</strong> To submit to this panel session, choose the third option, 'Submit an Individual Panel Presentation (by invitation only)'.
                                Please enter the following code:</p>
                                <p><strong>System Access Code:</strong> " . $panelistCode . "</p>
                                <p>Copy and paste this code into the code box. You will only need to use this code once as your panel will automatically be added to your 'My Submissions' page.</p>
                                <p><strong>Step 4:</strong> Fill out each of the pages required for your panel.</p>
                                <p><strong>Step 5:</strong> Once all pages are complete, please proceed to the preview page and click on 'finalize submission'. You may return to the site while it is open to view and make edits to your submission.</p>
                                <p>If you have any issues, please contact <a href='mailto:asf@owpm2.com'>afs@owpm2.com</a> for support.</p>
                                <p>Thank you,</p>
                                <p>AFS Administrator</p>
                            </div>";

                    // Attempt to send email
                    $result = $sendMail->send($from, [$author['panelist_email']], $subject, $message);

                    // ###################  Save to Email logs #####################
                    $email_logs_array = [
                        'user_id' => session('user_id'),
                        'add_to' => (json_encode([$author['panelist_email']])),
                        'subject' => $subject,
                        'ref_1' => 'panelist_copyright',
                        'add_content' => $message,
                        'send_from' => "Submitter",
                        'send_to' => "Author",
                        'level' => "Info",
                        'template_id' => 0,
                        'paper_id' => $paper_id,
                        'user_agent' => $this->request->getUserAgent()->getBrowser(),
                        'ip_address' => $this->request->getIPAddress(),
                    ];
//                    print_r($result);exit;
                    // Log email status
                    $logs = new LogsModel();
                    $emailLogs = [
                        'user_id' => session('user_id'),
                        'ref_1' => 'copyright',
                        'action' => 'email',
                        'ip_address' => $this->request->getIPAddress(),
                        'user_agent' => $this->request->getUserAgent()->getBrowser(),
                    ];

                    if (is_object($result)) {
                        if ($result->statusCode !== '200') {
                            $emailLogs['level'] = 'INFO';
                            $emailLogs['message'] = 'failed';
                            $emailLogs['context'] = 'failed';
                            $email_logs_array['status'] = 'Failed';
                        } else {
                            // Response is successful, log the whole response for debugging
                            $emailLogs['level'] = 'INFO';
                            $emailLogs['message'] = 'sent';
                            $emailLogs['context'] = 'success';
                            $email_logs_array['status'] = 'Success';
                        }
                    } else {
                        // If $result is not an array, log it directly
                        $emailLogs['level'] = 'ERROR';
                        $emailLogs['message'] = 'failed';
                        $emailLogs['context'] = json_encode($result);
                        $email_logs_array['status'] = 'Failed';
                    }
                    $logs->save($emailLogs);
                    $emailLogsModel = (new EmailLogsModel())->saveToMailLogs($email_logs_array);
                }
            }
        }catch (\Exception $e){
            return $e->getMessage();
        }
        return 'success';
    }

    public function finalize_panel($paper_id){
        $user_id = session('user_id');
        $post = $this->request->getPost();
        $event = (new AbstractEventsModel())->first();

        $PapersModel = (new PapersModel());
        $PaperAuthorsModel = (new PaperAuthorsModel());
        $PaperUploadsModel = (new PaperUploadsModel());
        $UsersModel = (new UserModel());
        $UsersProfileModel = (new UsersProfileModel());
        $InstitutionModel = (new InstitutionModel());
        $RemovedPaperAuthorsModel = (new RemovedPaperAuthorModel());

        $papers = $PapersModel
            ->select('papers.*, divisions.name as division_name, paper_type.name as paper_type_name')
            ->join('divisions', 'papers.division_id = divisions.division_id', 'left')
            ->join('paper_type', 'papers.type_id = paper_type.type', 'left')
            ->where(['user_id'=> session('user_id'), 'papers.id'=>$paper_id])->first();

        $coordinators = $PaperAuthorsModel
            ->select($PaperAuthorsModel->table . '.*, ' . $UsersModel->table . '.*, ' . $UsersProfileModel->table . '.*')
            ->join($UsersModel->table . ' AS users', $PaperAuthorsModel->table . '.author_id = ' . $UsersModel->table . '.id')
            ->join($UsersProfileModel->table . ' AS users_profile', $PaperAuthorsModel->table . '.author_id = ' . $UsersProfileModel->table . '.author_id', 'full')
//            ->join($InstitutionModel->table . ' AS institution', $UsersProfileModel->table . '.institution_id = ' . $InstitutionModel->table . '.id', 'left')
            ->where($PaperAuthorsModel->table . '.author_type', 'coordinator')
            ->where($PaperAuthorsModel->table . '.paper_id', $paper_id)
            ->whereNotIn($PaperAuthorsModel->table . '.id', function ($builder) use ($RemovedPaperAuthorsModel) {
                $builder->select('paper_author_id')->from($RemovedPaperAuthorsModel->table);
            })
            ->findAll();

        $panelists = $PaperAuthorsModel
            ->select($PaperAuthorsModel->table . '.*, ' . $UsersModel->table . '.*, ' . $UsersProfileModel->table . '.*')
            ->join($UsersModel->table . ' AS users', $PaperAuthorsModel->table . '.author_id = ' . $UsersModel->table . '.id')
            ->join($UsersProfileModel->table . ' AS users_profile', $PaperAuthorsModel->table . '.author_id = ' . $UsersProfileModel->table . '.author_id', 'full')
//            ->join($InstitutionModel->table . ' AS institution', $UsersProfileModel->table . '.institution_id = ' . $InstitutionModel->table . '.id', 'left')
            ->where($PaperAuthorsModel->table . '.author_type', 'panelist')
            ->where($PaperAuthorsModel->table . '.paper_id', $paper_id)
            ->whereNotIn($PaperAuthorsModel->table . '.id', function ($builder) use ($RemovedPaperAuthorsModel) {
                $builder->select('paper_author_id')->from($RemovedPaperAuthorsModel->table);
            })
            ->findAll();

        $userInfo = $UsersModel->find(session('user_id'));

        $paper_uploads = $PaperUploadsModel->where('paper_id', $paper_id)->findAll();

        $paper = $PapersModel->find($paper_id);

        //         print_r($authors);exit;
        $paperUploads = (new PaperUploadsModel())->where('paper_id', $paper_id)->findAll();
        $paperRequiredFields = [
            'division_id', 'type_id', 'title', 'summary', 'is_ijmc_interested'
        ];

//        $incomplete = [];
//        $incompletePanelists = ($this->verifyPanelist($paper_id, $incomplete));
//        $incompleteCoordinators = ($this->verifyCoordinators($paper_id, $incomplete));

//        if(!empty($panelists)){
//            $incomplete[]= $incompletePanelists;
//        }
//
//        if(!empty($coordinators)){
//            $incomplete[]= $incompleteCoordinators;
//        }

//        var_dump($incomplete);exit;
        if($paper){
            foreach ($paperRequiredFields as $required) {
                if($paper->$required == '' ||$paper->$required == null ){
                    $incomplete['paper'][] = 'required ' . $required . ' for Paper: ' . $paper->id;
                }
            }
        }

        $header_data = [
            'title' => "Preview"
        ];

        $data = [
            'event'=> $event,
            'papers'=> $papers,
            'coordinators'=> $coordinators,
            'panelists'=> $panelists,
            'paper_id'=> $paper_id,
            'userInfo'=> $userInfo,
            'paper_uploads' => $paper_uploads,
//            'incompleteStatus' => !empty($incomplete)?$incomplete:''
        ];

        return
            view('event/common/header', $header_data).
            view('event/finalize_panel',$data).
            view('event/common/footer')
            ;
    }

    function verifyPanelist($paper_id, $incomplete){ //Check if the Panelists is completed required fields
        $PaperAuthorsModel = (new PaperAuthorsModel());
        $UsersModel = (new UserModel());
        $UsersProfileModel = (new UsersProfileModel());
        $InstitutionModel = (new InstitutionModel());
        $RemovedPaperAuthorsModel = (new RemovedPaperAuthorModel());

        $panelists = $PaperAuthorsModel
            ->select($PaperAuthorsModel->table . '.*, ' . $UsersModel->table . '.*, ' . $UsersProfileModel->table . '.*')
            ->join($UsersModel->table . ' AS users', $PaperAuthorsModel->table . '.author_id = ' . $UsersModel->table . '.id')
            ->join($UsersProfileModel->table . ' AS users_profile', $PaperAuthorsModel->table . '.author_id = ' . $UsersProfileModel->table . '.author_id', 'full')
            ->where($PaperAuthorsModel->table . '.author_type', 'panelist')
            ->where($PaperAuthorsModel->table . '.paper_id', $paper_id)
            ->whereNotIn($PaperAuthorsModel->table . '.id', function ($builder) use ($RemovedPaperAuthorsModel) {
                $builder->select('paper_author_id')->from($RemovedPaperAuthorsModel->table);
            })
            ->findAll();


        $panelistRequiredFields = [
            'electronic_signature' => 'Copyright',
            'is_copyright_agreement_accepted' => 'Copyright Agreement',
            'institution' => 'Institution',
            'country' => 'Country',
            'city' => 'City',
            'province' => 'Province',
            'zipcode' => 'Zip Code',
            'address' => 'Address',
            'deg' => 'Degree'
        ];

        if (!empty($panelists)) {
            foreach ($panelists as $panelist) {
                foreach ($panelistRequiredFields as $field => $formalWord) {
                    if (empty($panelist[$field])) {
                        $incomplete['panelist'][] = 'Required ' . $formalWord . ' for panelist: ' . $panelist['name'].' '. $panelist['surname'];
                    }
                }
            }
        } else {
            $incomplete['panelist'][] = 'No Panelist';
        }

        return $incomplete;
    }

    function verifyCoordinators($paper_id, $incomplete){ //Check if the Coordinators is completed required fields
        $PaperAuthorsModel = (new PaperAuthorsModel());
        $UsersModel = (new UserModel());
        $UsersProfileModel = (new UsersProfileModel());
        $InstitutionModel = (new InstitutionModel());
        $RemovedPaperAuthorsModel = (new RemovedPaperAuthorModel());

        $coordinators = $PaperAuthorsModel
            ->select($PaperAuthorsModel->table . '.*, ' . $UsersModel->table . '.*, ' . $UsersProfileModel->table . '.*')
            ->join($UsersModel->table . ' AS users', $PaperAuthorsModel->table . '.author_id = ' . $UsersModel->table . '.id')
            ->join($UsersProfileModel->table . ' AS users_profile', $PaperAuthorsModel->table . '.author_id = ' . $UsersProfileModel->table . '.author_id', 'full')
            ->where($PaperAuthorsModel->table . '.author_type', 'coordinator')
            ->where($PaperAuthorsModel->table . '.paper_id', $paper_id)
            ->whereNotIn($PaperAuthorsModel->table . '.id', function ($builder) use ($RemovedPaperAuthorsModel) {
                $builder->select('paper_author_id')->from($RemovedPaperAuthorsModel->table);
            })
            ->findAll();


        $coordinatorRequiredFields = [
            'institution' => 'Institution',
            'country' => 'Country',
            'city' => 'City',
            'province' => 'Province',
            'zipcode' => 'Zip Code',
            'address' => 'Address',
            'deg' => 'Degree'
        ];

        if (!empty($coordinators)) {
            foreach ($coordinators as $coordinator) {
                foreach ($coordinatorRequiredFields as $field => $formalWord) {
                    if (empty($coordinator[$field])) {
                        $incomplete['coordinators'][] = 'Required ' . $formalWord . ' for panelist: ' . $coordinator['name'].' '. $coordinator['surname'];
                    }
                }
            }
        } else {
            $incomplete['coordinators'][] = 'No Coordinator';
        }

        return $incomplete;
    }

    public function saveCommentToUpload(){
        $post = $this->request->getPost();

        $AbstractReviewModel = (new AbstractReviewModel());

        $this->validate_user_access($post['paper_id']);

        $papers = (new PapersModel())->find($post['paper_id']);
        $MailTemplates = (new EmailTemplatesModel())->find(10);
        $email_body = $MailTemplates['email_body'];
        $deputy_users = (new UserModel())->join('users_profile', 'users.id = users_profile.author_id')->where('is_deputy_reviewer', 1)->findAll();

        $sendMail = (new PhpMail());

        $result = $AbstractReviewModel->where('id', $post['review_id'])->set('submitter_comment_on_upload', $post['comment'])->update();
        if($result){
            foreach ($deputy_users as $user) {
                $user_divisions = json_decode($user['division_id']);
                if (!empty($user_divisions)) {
                    if (in_array($papers->division_id, $user_divisions)) {
                        $PaperTemplates = $email_body;
                        $PaperTemplates = str_replace('##ABSTRACT_ID##', $post['custom_id'], $PaperTemplates);
                        $PaperTemplates = str_replace('##RECIPIENTS_FULL_NAME##', ucFirst($user['name']) . ' ' . ucFirst($user['surname']), $PaperTemplates);
                        $PaperTemplates = str_replace('##REVIEW_USERNAME##', $user['email'], $PaperTemplates);
                        $PaperTemplates = str_replace('##REVIEW_PASSWORD##', 'Please reset your password in case forgotten. Thank you!', $PaperTemplates);
                        $from = ['name' => 'Asia Pacific 2026', 'email' => 'ap@owpm2.com'];
                        $addTo = $user['email'];
                        $subject = $MailTemplates['email_subject'];
                        $addContent = $PaperTemplates;
                        $emailResult = $sendMail->send($from, $addTo, $subject, $addContent);

                        // ###################  Save to Email logs #####################
                        $email_logs_array = [
                            'user_id' => session('user_id'),
                            'add_to' => ($addTo),
                            'subject' => $subject,
                            'ref_1' => 'upload_comment',
                            'add_content' => $PaperTemplates,
                            'send_from' => "Submitter",
                            'send_to' => "Reviewers",
                            'level' => "Info",
                            'template_id' => $MailTemplates['id'],
                            'paper_id' => $post['paper_id'],
                            'user_agent' => $this->request->getUserAgent()->getBrowser(),
                            'ip_address' => $this->request->getIPAddress(),
                        ];

                        if($emailResult->statusCode == 200){
                            $email_logs_array['status'] = 'Success';
                        }else{
                            $email_logs_array['status'] = 'Failed';
                        }
                        $emailLogsModel = (new EmailLogsModel())->saveToMailLogs($email_logs_array);
                    }
                }
            }
            return json_encode(array('status' => '200', 'message' => 'success', 'data' => ''));
        }else {
            return json_encode(array('status' => '500', 'message' => 'error', 'data' => ''));
        }

    }

    function validate_user_access($paper_id)
    {
        $user_paper = (new PapersModel())->where('user_id', session('user_id'))->findAll();

        if (!$paper_id || !in_array($paper_id, array_column($user_paper, 'id'))) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Invalid ID');
        }

        return true;
    }



    public function testMail(){
        $mail = new PhpMail();
//        $mail->testMail();

        print_r($mail->testMail());
//        print_r('test');
    }

    public function phpInfo(){
        phpInfo();
    }
}

