<?php

namespace App\Controllers\cme;

use App\Controllers\BaseController;
use App\Models\CMEReviewersModel;
use CodeIgniter\Database\BaseConnection;

use App\Models\UserModel;
use App\Models\ReviewerModel;

class CMELogin extends BaseController
{

    public function __construct()
    {

    }
    
    public function index(){
        $header_data = [
            'title' => ''
        ];
        $data = [
        ];
        return
            view('cme/common/header', $header_data).
            view('cme/login',$data).
            view('cme/common/footer')
            ;
    }

    public function authenticate(){
        $post = $this->request->getPost();
        $user = (new UserModel())->validateUser($post);
         if ($user && password_verify($post['password'], $user['password'])) {
            if((new CMEReviewersModel())->is_valid_cme($user['id'])){
                $this->set_session($user);
                echo json_encode(array('status'=>'success'));
            }else{
                 echo json_encode(array('status'=>'error', 'msg'=> 'User is not reviewer'));
            }
        } else {
             echo json_encode(array('status'=>'error', 'msg'=> 'Invalid email or password'));
        }
    }

    function set_session($user){
        $session_array = array(
            'email'=>$user['email'],
            'user_id'=>$user['id'],
            'user_type'=>'cme',
            'name'=>$user['name'],
            'surname'=>$user['surname'],
            'middle_name'=>$user['middle_name'],
        );
        session()->set($session_array);
    }

    public function logout(){
        session()->destroy();
        return redirect()->to(base_url().'/cme');
    }
    

}