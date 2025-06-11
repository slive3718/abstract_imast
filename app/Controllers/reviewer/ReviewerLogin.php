<?php

namespace App\Controllers\reviewer;

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
    private BaseConnection $db;

    private $session;
    public function __construct()
    {
        $this->db = db_connect();

        $this->session = \Config\Services::session();
    }
    
    public function index(){
        // return ('<a href="'.$this->event_uri .'/'.base_url().'/login/logOut/'.$this->event_uri .'">logout</a>');
        $event = (new EventsModel())->first();

        $header_data = [
            'title' => ''
        ];
        $data = [
            'event'=> $event
        ];
        return
            view('reviewer/common/header', $header_data).
            view('reviewer/login',$data).
            view('reviewer/common/footer')
            ;
    }

    public function authenticate(){
        $post = $this->request->getPost();
        $user = (new UserModel())->validateUser($post);
         if ($user && password_verify($post['password'], $user['password'])) {
            if((new ReviewerModel)->validateReviewer($post, $user['id'])){
            
                  $session_array = array(
                    'email'=>$user['email'],
                    // 'token'=>$user->data,
                    'user_id'=>$user['id'],
                    'event_uri'=>$_POST['event_uri'],
                    'user_type'=>$_POST['login_type'],
                    'name'=>$user['name'],
                    'surname'=>$user['surname'],
                    'middle_name'=>$user['middle_name'],
                );
                
                $this->session->set($session_array);
                echo json_encode(array('status'=>'success'));
            }else{
                 echo json_encode(array('status'=>'error', 'msg'=> 'User is not reviewer'));
            }
            // Success auth
        } else {
             echo json_encode(array('status'=>'error', 'msg'=> 'Invalid email or password'));
            //error auth
        }
    
    }

    public function logout(){
        session()->destroy();
        return redirect()->to(base_url().'/reviewer');
    }
    

}