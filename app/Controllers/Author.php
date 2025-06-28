<?php

namespace App\Controllers;

use App\Libraries\MailGunEmail;
use App\Libraries\PhpMail;
use App\Models\AffiliationsModel;
use App\Models\AttestationModel;
use App\Models\Core\Api;
use App\Models\EmailLogsModel;
use App\Models\EmailTemplatesModel;
use App\Models\EventsModel;
use App\Models\OrganizationsModel;
use App\Models\PaperAuthorsModel;
use App\Models\PapersModel;
use App\Models\SiteSettingModel;
use App\Models\UserModel;
use App\Models\UserOrganizationsModel;
use App\Models\UsersProfileModel;


use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;


class Author extends BaseController
{

    private $db;

    public function __construct()
    {
        $this->db = db_connect();
    }

    public function index(): string
    {
        $header_data = [
            'title' => "Login"
        ];

        $data = [];

        return
            view('author/common/header', $header_data).
            view('author/login', $data).
            view('author/common/footer')
            ;
    }

    public function view_copyright(){

        $PaperAuthorsModel = (new PaperAuthorsModel());
        $author = $PaperAuthorsModel
            ->join($this->shared_db_name.'.users', 'paper_authors.author_id = users.id', 'left')
            ->join($this->shared_db_name.'.users_profile', 'paper_authors.author_id = users_profile.author_id', 'left')
            ->where('users.id',  session('user_id'))
            ->first();

        $disclosure_current_date = (new SiteSettingModel())->where('name', 'disclosure_current_date')->first()['value'];
       $disclosure_expire_date = date('Y-m-d', strtotime($disclosure_current_date . ' +1 year'));
        $header_data = [
            'title' => "Author Copyright"
        ];

        $data = [
            'author'=>$author,
            'disclosure_current_date' => $disclosure_current_date,
            'disclosure_expire_date' => $disclosure_expire_date
        ];

//        print_r($data);exit;
        return
            view('author/common/header', $header_data).
            view('author/copyright_main', $data).
            view('author/common/footer')
            ;
    }

    public function profile(){
        session('user_type');
        $_POST['author_id']= session('user_id');
        $api2 = new Api();
        $event = $this->api->getRequest("event/details/{}");
        $user_details = $api2->post("author/details/{}", $_POST);
//        print_R($user_details);exit;
        if(!$event){
            return (new ErrorHandler($event))->errorPage();
        }

        $header_data = [
            'title' => "{$event->short_name} Login"
        ];
        $data = [
            'event'=> $event,
            'user_details'=>$user_details->data
        ];


        return
            view('author/common/header', $header_data).
            view('author/profile', $data).
            view('author/common/footer')
            ;
    }

    public function copyright_of_publication_agreement($paper_id){

        if(!$paper_id){
            return 'error';
        }

        $UserModel = (new UserModel());
        $PapersModel = (new PapersModel());
        $papers = $PapersModel
            ->select('papers.*, users.name as user_name, users.surname as user_surname')
            ->join($this->shared_db_name.'.users', 'papers.user_id = users.id')
            ->where('papers.id', $paper_id)
            ->first();

        $author = $UserModel
            ->join('paper_authors', 'users.id = paper_authors.author_id', 'right')
            ->where('author_id', session('user_id'))
            ->where('paper_id', $paper_id)
            ->first();


        $header_data = [
            'title' => "Author Login"
        ];
        $data = [
            'papers'=>$papers,
            'author'=>$author
        ];

        return
            view('author/common/header', $header_data).
            view('author/copyright_of_publication_agreement', $data).
            view('author/common/footer')
            ;
    }

    public function financial_relationship_disclosure() {
        $user_id = session('user_id');
        if (!$user_id) {
            exit;
        }

        $UserModel = new UserModel();
        $OrganizationsModel = new OrganizationsModel();
        $AffiliationsModel = new AffiliationsModel();
        $UserOrganizationsModel = new UserOrganizationsModel(); // New model to handle user affiliations

        // Get author data
        $author = $UserModel
            ->join($this->shared_db_name.'.users_profile up', 'users.id = up.author_id', 'left')
            ->where('users.id', $user_id)
            ->asArray()
            ->first();

        $organizations = $OrganizationsModel->findAll();
        $affiliations = $AffiliationsModel->findAll();

        // Get saved affiliations for the user
        $savedOrganizations = $UserOrganizationsModel
            ->where('user_id', $user_id)
            ->orderBy('id', 'asc') // <-- Order by insertion order
            ->findAll();

        // Map saved affiliations to an easy-to-use array
        $selectedOrganizations = [];
        if (!empty($savedOrganizations)) {
            foreach ($savedOrganizations as $org) {
                $selectedOrganizations[$org['id']] = [
                    'organization_id' => $org['organization_id'], // Fixed ID to match organization_id
                    'affiliations' => json_decode($org['affiliation'], true) ?? [],
                    'custom_organization' => $org['custom_organization'] ?? null
                ];
            }
        }


        $header_data = [
            'title' => "Financial Relationship Disclosure"
        ];

        $data = [
            'author' => $author,
            'organizations' => $organizations,
            'affiliations' => $affiliations,
            'selectedOrganizations' => $selectedOrganizations
        ];

//        print_r($data);exit;

        return view('author/common/header', $header_data)
            . view('author/financial_relationship_disclosure', $data)
            . view('author/common/footer');
    }

    public function attestation() {
        $user_id = session('user_id');
        if (!$user_id) {
            exit;
        }
        $UserModel = new UserModel();
        $author = $UserModel
            ->select('users.*, up.*, at.signature as attestation_signature, at.date as attestation_date')
            ->join($this->shared_db_name.'.users_profile up', 'users.id = up.author_id', 'left')
            ->join($this->default_db->database.'.attestation at', 'up.author_id = at.author_id', 'left')
            ->where('users.id', $user_id)
            ->asArray()
            ->first();

        $header_data = [
            'title' => "Attestation for Asia Pacific 2026"
        ];

        $data = [
            'author' => $author,
        ];

        return view('author/common/header', $header_data)
            . view('author/attestation', $data)
            . view('author/common/footer');
    }



//    public function save_financial_relationship() {
//        $request = $this->request->getPost();
//
//        // Log the data for debugging
//        log_message('debug', print_r($request, true));
//
//        // Assuming user ID is stored in session
//        $userId = session()->get('user_id');
//
//        if (!$userId) {
//            return $this->response->setJSON(['success' => false, 'message' => 'User not logged in']);
//        }
//
//        // Prepare data for updating user profile
//        $data = [
//            'financial_relationship' => $request['financial_relationship'] ?? null,
//            'disclosure_support'     => isset($request['disclosure_support']) ? 1 : 0,
//            'disclosure_discussed'   => isset($request['disclosure_discussed']) ? 1 : 0,
//            'disclosure_signature'   => $request['disclosure_signature'] ?? null,
//            'updated_at'             => date('Y-m-d H:i:s'), // Use `updated_at` for updates
//        ];
//
//        $model = new UsersProfileModel();
//
//        // Update the existing user record
//        $isUpdated = $model->set($data)->where('author_id', $userId)->update();
//
//        if ($isUpdated) {
//            // Save organization data if financial relationship is 'yes'
//            if ($request['financial_relationship'] === 'yes' && !empty($request['organization'])) {
//                $db = db_connect();
//                $builder = $db->table('user_organizations');
//
//                $existingIds = [];
//                foreach ($request['organization'] as $organization) {
//                    $orgId = $organization['name'] ?? null;
//                    $affiliations = isset($organization['affiliation']) ? json_encode($organization['affiliation']) : null;
//                    $otherName = $organization['other_name'] ?? null;
//
//                    if ($orgId) {
//                        $data = [
//                            'user_id'         => $userId,
//                            'organization_id' => $orgId,
//                            'affiliation'     => $affiliations,
//                            'custom_organization'      => ($orgId == 29) ? $otherName : null,
//                        ];
//
//                        // Try updating existing record
//                        $exists = $builder
//                            ->where('user_id', $userId)
//                            ->where('organization_id', $orgId)
//                            ->countAllResults();
//
//                        if ($exists) {
//                            // Update existing record
//                            $builder->where('user_id', $userId)
//                                ->where('organization_id', $orgId)
//                                ->update($data);
//                        } else {
//                            // Insert new record
//                            $builder->insert($data);
//                        }
//
//                        // Keep track of valid records
//                        $existingIds[] = $orgId;
//                    }
//                }
//
//                // Remove records that are no longer in the request (cleanup step)
//                if (!empty($existingIds)) {
//                    $builder->where('user_id', $userId)
//                        ->whereNotIn('organization_id', $existingIds)
//                        ->delete();
//                }
//            }
//
//            return $this->response->setJSON(['success' => true]);
//        } else {
//            return $this->response->setJSON(['success' => false, 'message' => 'Failed to update user profile']);
//        }
//    }

    public function save_financial_relationship() {
        $request = $this->request->getPost();

        // Log the data for debugging
        log_message('debug', print_r($request, true));

        // Assuming user ID is stored in session
        $userId = session()->get('user_id');

        if (!$userId) {
            return $this->response->setJSON(['success' => false, 'message' => 'User not logged in']);
        }

        // Prepare data for updating user profile
        $data = [
            'financial_relationship' => $request['financial_relationship'] ?? null,
            'disclosure_support'     => isset($request['disclosure_support']) ? 1 : 0,
            'disclosure_discussed'   => isset($request['disclosure_discussed']) ? 1 : 0,
            'disclosure_signature'   => trim($request['disclosure_signature']) ?? null,
            'disclosure_relationship'   => isset($request['disclosure_relationship']) ? 1 : 0,
            'signature_signed_date'   => date('Y-m-d H:i:s'),
            'updated_at'             => date('Y-m-d H:i:s'), // Use `updated_at` for updates
        ];

        $model = new UsersProfileModel();

        // Update the existing user record
        $isUpdated = $model->set($data)->where('author_id', $userId)->update();

        if ($isUpdated) {
            // Save organization data if financial relationship is 'yes'
            if ($request['financial_relationship'] === 'yes' && !empty($request['organization'])) {
                $db = db_connect();
                $builder = $db->table('user_organizations');
                $builder->delete(['user_id' => $userId]);
                foreach ($request['organization'] as $organization) {
                    $orgId = $organization['name'] ?? null;
                    $affiliations = isset($organization['affiliation']) ? json_encode($organization['affiliation']) : null;
                    $otherName = $organization['other_name'] ?? null;

                    if ($orgId) {
                        $data = [
                            'user_id'              => $userId,
                            'organization_id'      => $orgId,
                            'affiliation'          => $affiliations,
                            'custom_organization'  => ($orgId == 29) ? $otherName : '',
                        ];

                        // ✅ Directly insert without checking for existing records
                        $builder->insert($data);
                    }
                }
            }

            return $this->response->setJSON(['success' => true]);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to update user profile']);
        }
    }


    public function submit_attestation() {
        $request = $this->request->getPost();

        // Log the data for debugging
        log_message('debug', print_r($request, true));

        // Assuming user ID is stored in session
        $userId = session('user_id');



        if (!$userId) {
            return $this->response->setJSON(['success' => false, 'message' => 'User not logged in']);
        }

        // Prepare data for updating user profile
        $data = [
            'signature' => $request['attestation_signature'] ?? null,
            'date'   => date('Y-m-d', strtotime($request['attestation_date'])) ?? null
        ];

        $model = new AttestationModel();

        $attestation_data = $model->where('author_id', $userId)->find();
        if( !empty($attestation_data) && is_array($attestation_data)){
            // If attestation record exists, update it
            $isUpdated = $model->set($data)->where('author_id', $userId)->update();
        } else {
            // If no record exists, create a new one
            $data['author_id'] = $userId;
            $isUpdated = $model->insert($data);
        }

        // Update the existing user record
        $isUpdated = $model->set($data)->where('author_id', $userId)->update();

        if ($isUpdated) {
            return $this->response->setJSON(['success' => true]);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to update user profile']);
        }
    }

    public function preview_finalize()
    {
        $user_id = session('user_id');
        if (!$user_id) {
            exit;
        }

        $UserModel = new UserModel();
        $OrganizationsModel = new OrganizationsModel();
        $AffiliationsModel = new AffiliationsModel();
        $UserOrganizationsModel = new UserOrganizationsModel(); // New model to handle user affiliations

        // Get author data
        $author = $UserModel
            ->join($this->shared_db_name.'.users_profile up', 'users.id = up.author_id', 'left')
            ->where('users.id', $user_id)
            ->asArray()
            ->first();

        $organizations = $OrganizationsModel->findAll();
        $affiliations = $AffiliationsModel->findAll();

        // Get saved affiliations for the user
        $savedOrganizations = $UserOrganizationsModel
            ->where('user_id', $user_id)
            ->orderBy('id', 'asc') // <-- Order by insertion order
            ->findAll();

        // Map saved affiliations to an easy-to-use array
        $selectedOrganizations = [];
        if (!empty($savedOrganizations)) {
            foreach ($savedOrganizations as $org) {
                $selectedOrganizations[$org['id']] = [
                    'organization_id' => $org['organization_id'], // Fixed ID to match organization_id
                    'affiliations' => json_decode($org['affiliation'], true) ?? [],
                    'custom_organization' => $org['custom_organization'] ?? null
                ];
            }
        }


        $header_data = [
            'title' => "Print/Preview"
        ];

        $data = [
            'author' => $author,
            'organizations' => $organizations,
            'affiliations' => $affiliations,
            'selectedOrganizations' => $selectedOrganizations
        ];

        return view('author/common/header', $header_data)
            . view('author/preview_finalize', $data)
            . view('author/common/footer');
    }



    public function confirm_copyright_ajax(){

//        print_r("hses");exit;
        $post = $this->request->getPost();
        $PaperAuthors = (new PaperAuthorsModel());


        $UsersModel = (new UserModel());
        $author = $UsersModel->find(session('user_id'));
        $PapersModel = (new PapersModel());
        $papers = $PapersModel
            ->select('users.name as submitter_name, users.surname as submitter_surname, papers.title as paper_title')
            ->join($this->shared_db_name.'.users', 'papers.user_id = users.id')
            ->find($post['paper_id']);


        $sendMail = new PhpMail();
        $MailTemplates = (new EmailTemplatesModel())->find(11);

        $email_body = $MailTemplates['email_body'];
        $email_body = str_replace('##ABSTRACT_ID##', $post['paper_id'], $email_body);
        $email_body = str_replace('##ABSTRACT_TITLE##', strip_tags($papers->paper_title), $email_body);
        $email_body = str_replace('##RECIPIENTS_FULL_NAME##', ucFirst($author['name']).' '.ucFirst($author['surname']), $email_body);
        $email_body = str_replace('##SUBMITTER_NAME##', ucFirst($papers->submitter_name), $email_body);
        $email_body = str_replace('##SUBMITTER_SURNAME##', ucFirst($papers->submitter_surname), $email_body);

        $from = ['name'=>'AFS', 'email'=>'afs@owpm2.com'];
        $addTo = [$author['email']];
        $subject = $MailTemplates['email_subject'];
        $addContent = $email_body;

        try{

            $paperAuthors = $PaperAuthors->where(['paper_id'=>$post['paper_id'], 'author_id'=>session('user_id')])->set($insertArray)->update();
            if($paperAuthors) {
                $mailResult = $sendMail->send($from, $addTo, $subject, $addContent);

                // ###################  Save to Email logs #####################
                $email_logs_array = [
                    'user_id' => session('user_id'),
                    'add_to' => (json_encode($addTo)),
                    'subject' => $subject,
                    'ref_1' => 'copyright_confirmation',
                    'add_content' => $addContent,
                    'send_from' => "App",
                    'send_to' => "Author",
                    'level' => "Info",
                    'template_id' => $MailTemplates['id'],
                    'paper_id' => $post['paper_id'],
                    'user_agent' => $this->request->getUserAgent()->getBrowser(),
                    'ip_address' => $this->request->getIPAddress(),
                ];

                if(!is_string($mailResult)) {
                    if ($mailResult->statusCode == 200) {
                        foreach ($addTo as $to){
                            $email_logs_array['status'] = 'Success';
                            $email_logs_array['add_to'] = $to;
                            (new EmailLogsModel())->saveToMailLogs($email_logs_array);
                        }
                       return json_encode(array('status' => '200', 'message' => 'Success :', 'data' => $PaperAuthors->affectedRows()));
                    }
                    else {
                        foreach ($addTo as $to){
                            $email_logs_array['status'] = 'Failed';
                            $email_logs_array['add_to'] = $to;
                            (new EmailLogsModel())->saveToMailLogs($email_logs_array);
                        }
                      return json_encode(array('status' => '201', 'message' => 'Success :', 'data' => $PaperAuthors->affectedRows()));
                    }
                }else{
                    return json_encode(array('status' => '201', 'message' => 'Success :', 'data' => $PaperAuthors->affectedRows()));
                }
            }
        }catch (\Exception $e){
            return json_encode(array('status' => '500', 'message' => 'Error: '.$e->getMessage(), 'data' =>''));
        }
    }

    public function finalize_disclosure(){
        $_POST['author_id'] = session()->get('user_id');

        $this->confirm_copyright_ajax();
    }

    public function finalize_success(){

        $event = (new AbstractEventsModel())->first();
        if(!$event){
            return (new ErrorHandler($event))->errorPage();
        }

        $header_data = [
            'title' => "{$event->short_name} Login"
        ];

        $data = [
            'event'=> $event
        ];

        return
            view('author/common/header', $header_data).
            view('author/finalize_success', $data).
            view('author/common/footer')
            ;
    }


    public function logout(){
        session()->destroy();
        return redirect()->to('/author');
    }

}
