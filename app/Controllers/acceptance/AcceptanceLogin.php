<?php

namespace App\Controllers\acceptance;

use App\Models\AdminAcceptanceModel;
use App\Models\AuthorAcceptanceModel;
use App\Models\EventsModel;
use App\Models\PanelistPaperSubModel;
use App\Models\PaperAuthorsModel;
use App\Models\PapersModel;
use App\Models\SchedulerModel;
use CodeIgniter\Controller;
use App\Models\Core\Api;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Database\BaseConnection;

use App\Models\UserModel;

class AcceptanceLogin extends Controller
{
    private Api $api;
    private BaseConnection $db;

    private $session;
    public function __construct()
    {
        $this->db = db_connect();
        $this->api = new Api();

        $this->session = \Config\Services::session();
    }
    
    public function index(){
        // return ('<a href="'.$this->event_uri .'/'.base_url().'/login/logOut/'.$this->event_uri .'">logout</a>');
        $event = (new EventsModel())->first();
        if(!$event){
            return (new ErrorHandler($event))->errorPage();
        }

        $header_data = [
            'title' => ''
        ];
        $data = [
            'event'=> $event
        ];
        return
            view('acceptance/common/header', $header_data).
            view('acceptance/login',$data).
            view('acceptance/common/footer')
            ;
    }

    public function authenticate(){
        try {
            $user = (new UserModel())->validateUser($_POST);

            if(!$user){
                $this->response->setStatusCode(404, 'Not Authorized.');
                return $this->response->setJSON(['status' => 'failed', 'msg' => 'Email not found!']);
            }

            $presenting_authors = (new PaperAuthorsModel())
                ->getPresentingAuthors()
                ->select('aaa.id as acceptance_id, aaa.acceptance_confirmation, aaa.presentation_preference')
                ->join('admin_abstract_acceptance aaa', 'paper_authors.paper_id = aaa.abstract_id', 'left')
                ->where('author_id', $user['id'])
                ->where('paper_authors.author_type', 'author')
                ->where('paper_authors.is_presenting_author', 'Yes')
                ->where('aaa.acceptance_confirmation ', '1')
                ->where('aaa.presentation_preference !=', '2')
                ->get()->getResultArray();


            $panelists = (new PaperAuthorsModel())->getPanelists()  // check all author that is a panelist and accepted by admin
                ->select('paper_authors.*, aipa.id as acceptance_id, aipa.acceptance_confirmation, aipa.presentation_preference,
                 pps.panelist_id as author_id, pps.paper_id as paper_id')
                ->join('panelist_paper_sub pps', 'paper_authors.paper_id = pps.paper_id', 'left')
                ->join('admin_individual_panel_acceptance aipa', 'pps.id = aipa.individual_panel_id', 'left')
                ->where('author_id', $user['id'])
                ->where('aipa.acceptance_confirmation !=', '2')
                ->where('paper_authors.author_type =', 'panelist')
                ->get()->getResultArray();

            $schedules = (new SchedulerModel())
                ->findAll();

            $session_chairs = [];
            if($schedules){
                foreach ($schedules as &$schedule){
                    if($schedule['session_chair_ids']){
                        $session_chairs[] = !empty($schedule['session_chair_ids']) ? json_decode($schedule['session_chair_ids']) : [];
                    }
                }
            }

            $is_moderator = false;
            if(in_array(session('user_id'), $session_chairs)){
                $is_moderator = true;
            }

            if(!$presenting_authors && !$panelists && !$is_moderator){
                $this->response->setStatusCode(404, 'Not Authorized.');
                return $this->response->setJSON(['status' => 'failed', 'msg' => 'Sorry, there are no accepted paper affiliated with that email address.']);
            }

            if($presenting_authors) {
                foreach ($presenting_authors as $presenter) {
                    if (!empty($presenter) && $presenter['acceptance_confirmation'] == 1) {
                        $session_array = array(
                            'email' => $user['email'],
                            'user_id' => $user['id'],
                            'user_type' => $_POST['login_type'],
                            'name' => $user['name'],
                            'surname' => $user['surname'],
                        );
                        $this->session->set($session_array);
                        $this->response->setStatusCode(200);
                        return $this->response->setJSON(['status' => 'success']);
                    } else {
                        $this->response->setStatusCode(404, 'Not Authorized.');
                        return $this->response->setJSON(['status' => 'failed', 'msg' => 'Sorry, there are no presenter roles affiliated with that email address.']);
                    }
                }
            }else if($panelists){
                foreach ($panelists as $panelist) {
                    if (!empty($panelist) && $panelist['acceptance_confirmation'] == 1) {
                        $session_array = array(
                            'email' => $user['email'],
                            'user_id' => $user['id'],
                            'user_type' => $_POST['login_type'],
                            'name' => $user['name'],
                            'surname' => $user['surname'],
                        );
                        $this->session->set($session_array);
                        $this->response->setStatusCode(200);
                        return $this->response->setJSON(['status' => 'success']);
                    } else {
                        $this->response->setStatusCode(404, 'Not Authorized.');
                        return $this->response->setJSON(['status' => 'failed', 'msg' => 'Sorry, there are no presenter roles affiliated with that email address.']);
                    }
                }
            }else if($is_moderator){
                $session_array = array(
                    'email' => $user['email'],
                    'user_id' => $user['id'],
                    'user_type' => $_POST['login_type'],
                    'name' => $user['name'],
                    'surname' => $user['surname'],
                );
                $this->session->set($session_array);
                $this->response->setStatusCode(200);
                return $this->response->setJSON(['status' => 'success']);
            }

        }catch (\Exception $e){
            return $this->response->setJSON(['status'=>'error', 'msg'=>$e->getMessage()]);
        }
    }

    public function logout(){
        $event_uri = session('event_uri');
        session()->destroy();
        return redirect()->to(base_url().$event_uri.'/acceptance/');
    }
    

}