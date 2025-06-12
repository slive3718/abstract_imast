<?php

namespace App\Controllers;

use App\Libraries\MailGunEmail;
use App\Libraries\PhpMail;
use App\Models\AuthorDetailsModel;
use App\Models\Core\Api;
use App\Models\EmailLogsModel;
use App\Models\EmailTemplatesModel;
use App\Models\EventsModel;
use App\Models\PaperAuthorsModel;
use App\Models\PapersModel;
use App\Models\UserModel;
use CodeIgniter\Controller;
use Config\Mailgun;
use GuzzleHttp\Client;


class Author extends BaseController
{

    public function __construct()
    {

    }

    public function index(): string
    {
        $event = (new EventsModel())->first();
        $header_data = [
            'title' => "Login"
        ];

        $data = [
            'event'=> $event
        ];

        return
            view('author/common/header', $header_data).
            view('author/login', $data).
            view('author/common/footer')
            ;
    }

    public function view_copyright(){

        $PaperAuthorsModel = (new PaperAuthorsModel());
        $author_details = $PaperAuthorsModel
            ->join('users', 'paper_authors.author_id = users.id', 'left')
            ->join('users_profile', 'paper_authors.author_id = users_profile.author_id', 'left')
            ->join('papers', 'paper_authors.paper_id = papers.id', 'left')
            ->where('paper_authors.author_id', session('user_id'))->findAll();

        $event = (new EventsModel())->first();

//        print_r($author_details);exit;
        $header_data = [
            'title' => "{$event->short_name} Login"
        ];
        $data = [
            'event'=> $event,
            'author_details'=>$author_details
        ];

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
            ->join('users', 'papers.user_id = users.id')
            ->where('papers.id', $paper_id)
            ->first();

        $author = $UserModel
            ->join('paper_authors', 'users.id = paper_authors.author_id', 'right')
            ->where('author_id', session('user_id'))
            ->where('paper_id', $paper_id)
            ->first();

        $event = (new EventsModel())->first();

        if(!$event){
            return (new ErrorHandler($event))->errorPage();
        }

        $header_data = [
            'title' => "{$event->short_name} Login"
        ];
        $data = [
            'event'=> $event,
            'papers'=>$papers,
            'author'=>$author
        ];

        return
            view('author/common/header', $header_data).
            view('author/copyright_of_publication_agreement', $data).
            view('author/common/footer')
            ;
    }

    public function conflict_of_interest_disclosure_review(){
        session('user_type');
        $_POST['author_id']= session('user_id');
        $api2 = new Api();
        $api3 = new Api();
        $api4 = new Api();
        $api5 = new Api();
        $api6 = new Api();
        $organizations = $api2->getRequest("author/get_organizations/{}");
        $affiliations = $api3->getRequest("author/get_affiliations/{}");
        $declarations = $api4->getRequest("author/get_declarations/{}");
        $event = $this->api->getRequest("event/details/{}");
        $author_details = $api5->post("author/get_author_cod_details/{}", $_POST);
        $author_organizations = $api6->post("author/get_author_organizations/{}", $_POST);

//        print_r($author_details);exit;
        if(!$event){
            return (new ErrorHandler($event))->errorPage();
        }
//        print_r($declarations->data);exit;

        $header_data = [
            'title' => "{$event->short_name} Login"
        ];
        $data = [
            'event'=> $event,
            'organizations'=> $organizations->data,
            'affiliations'=> $affiliations->data,
            'declarations'=> $declarations->data,
            'author_details'=> $author_details->data,
            'author_organizations'=> $author_organizations->data,
        ];


        return
            view('author/common/header', $header_data).
            view('author/conflict_of_interest_disclosure_review', $data).
            view('author/common/footer')
            ;
    }


    public function confirm_copyright_ajax(){

        $post = $this->request->getPost();
        $PaperAuthors = (new PaperAuthorsModel());

        if(!$post['agreementCheckBox'] || !$post['signature']){
            return json_encode(array('status' => '500', 'message' => 'Error: Missing Inputs', 'data' =>''));
        }
        $UsersModel = (new UserModel());
        $author = $UsersModel->find(session('user_id'));
        $PapersModel = (new PapersModel());
        $papers = $PapersModel
            ->select('users.name as submitter_name, users.surname as submitter_surname, papers.title as paper_title')
            ->join('users', 'papers.user_id = users.id')
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
            $insertArray = [
                'is_copyright_agreement_accepted'=> '1',
                'electronic_signature'=> $post['signature'],
                'copyright_agreement_date'=> date("Y-m-d H:i:s")
            ];

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

//        print_r($_POST);exit;
        $_POST['author_id'] = session()->get('user_id');
        $_POST['event_uri'] = $this->event_uri;
        $result = $this->api->post("author/finalize_disclosure/{$this->event_uri}", $_POST);
        if(!$result->status){
            return (new ErrorHandler($result->data))->errorPage();
        }
        if($result->data){
            return redirect()->to($this->event_uri.'/author/finalize_success');
        }
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
        return redirect()->to('/author/login');
    }

}
