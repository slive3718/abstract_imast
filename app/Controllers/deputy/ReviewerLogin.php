<?php

namespace App\Controllers\deputy;

use App\Models\EventsModel;
use CodeIgniter\Controller;
use App\Models\Core\Api;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Database\BaseConnection;

use App\Models\UserModel;
use App\Models\PapersModel;
use App\Models\PaperAuthorModel;
use App\Models\ReviewerModel;

class ReviewerLogin extends Controller
{

    private $session;

    public function __construct()
    {

        $this->session = \Config\Services::session();
    }
    
    public function index(){
        $event = (new EventsModel())->first();

        $header_data = [
            'title' => 'Deputy Review'
        ];
        $data = [
            'event'=> $event
        ];
        return
            view('deputy/common/header', $header_data).
            view('deputy/login',$data).
            view('deputy/common/footer')
            ;
    }

    public function authenticate(){

        $post = $this->request->getPost();
        $UserModel = new UserModel();
        $user = $UserModel->validateUser($post);

        if ($user && password_verify($post['password'], $user['password'])) {
            if($user['is_deputy_reviewer'] == 1) {
                $session_array = array(
                    'email' => $user['email'],
                    // 'token'=>$user->data,
                    'user_id' => $user['id'],
                    'event_uri' => $_POST['event_uri'],
                    'user_type' => $_POST['login_type'],
                    'name' => $user['name'],
                    'surname' => $user['surname'],
                    'middle_name'=>$user['middle_name'],
                );

                $this->session->set($session_array);
                echo json_encode(array('status'=>'200', 'msg'=> "Login success", 'data'=>""));
            }else{
                echo json_encode(array('status'=>'403', 'msg'=> "You do not have access to this site", 'data'=>""));
            }
        } else {
            echo json_encode(array('status'=>'500', 'msg'=> "Invalid email or password", 'data'=>""));
        }

    }

    public function logout(){
        session()->destroy();
        return redirect()->to(base_url().'/deputy');
    }
    

}