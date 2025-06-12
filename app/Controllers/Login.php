<?php

namespace App\Controllers;

use App\Models\AbstractEventsModel;
use App\Models\Core\Api;
use App\Models\LogsModel;
use App\Models\UserModel;
use CodeIgniter\Controller;
use CodeIgniter\HTTP\ResponseInterface;
use Firebase\JWT\JWT;
use GuzzleHttp\Client;
use DateTimeImmutable;

class Login extends BaseController
{
    public function __construct()
    {
        $this->event_uri = session('event_uri');

    }

    public function index(): string
    {

        $header_data = [
            'title' => "Login"
        ];

        $data = [
            'event'=> 'Asia Pacific'
        ];
        return
            view('event/common/header', $header_data).
            view('event/login', $data).
            view('event/common/footer')
            ;
    }

    public function validateLogin(){

        $result = $this->getToken($_POST);

//        print_r($result);exit;
        if($result){
            $session_array = array(
                'email'=>$result['credentials']->email,
                'token'=>$result,
                'user_id'=>$result['credentials']->id,
                'user_type'=>$_POST['login_type'],
                'name'=>$result['credentials']->name,
                'surname'=>$result['credentials']->surname,
                'is_super_admin'=>$result['credentials']->is_super_admin ?? 0,
                'is_regular_reviewer'=>$result['credentials']->is_regular_reviewer ?? 0,
                'is_session_moderator'=>$result['credentials']->is_session_moderator ?? 0,
                'is_study_group'=>$result['credentials']->is_study_group ?? 0,
            );
            $this->session->set($session_array);

            $agent = $this->request->getUserAgent();
            // load logs model
            $logs = new LogsModel();
            // logs data
            $userlog = [
                'level' => 'INFO',
                'user_id' => $result['credentials']->id,
                'ref_1' => $_POST['login_type'],
                'action' => "Login",
                'ip_address' => $this->request->getIPAddress(),
                'user_agent' => $agent->getBrowser(),
                'message' => 'Login Success'
            ];
            // save to logs
            $logs->save($userlog);

            return  json_encode(array('status'=>"200", 'data'=>$result));

        }else{
            return  json_encode(array('status'=>"201", 'data'=>$result, 'message'=>'Invalid email or password'));
        }
    }

    public function backdoor($event_uri): string
    {
        $event = (new AbstractEventsModel())->first();

        if(!$event){
            return ('error');
        }

        $header_data = [
            'title' => "{$event->short_name} Login"
        ];
        $data = [
            'event'=> $event
        ];
        return
            view('event/common/header', $header_data).
            view('event/login_backdoor', $data).
            view('event/common/footer')
            ;
    }

    public function getToken($post)
    {

        if (!$post) {
            return ("Missing Data");
        }

//        print_R($post['login_type'] );exit;
        if($post['login_type'] == 'author'){
            $required_data = [
                'email'
            ];
        }else{
            $required_data = [
                'email',
                'password',
            ];
        }



        foreach ($required_data as $required)
        {
            if (!array_key_exists($required, $post))
            {
                return "{$required} is required";
            }

            if ($post[$required] == '')
            {
                return "{$required} cannot be empty";
            }
        }

        if($post['login_type'] == 'author'){
            $cred_check = (new UserModel())->author_cred_check($post['email']);
        }else{
            $cred_check =  (new UserModel())->cred_check($post['email'], $post['password']);
        }


        if (!$cred_check)
        {
            return (['message'=>'Invalid username or password', 'data'=>'']);
        }

        $expiry = (isset($post['expiry']))?$post['expiry']:24; // hours

        $token = JWT::encode(
            [
                'iat'  => (new DateTimeImmutable())->getTimestamp(), // Issued at: time when the token was generated
                'iss'  => $_ENV['app.domain'], // Issuer
                'nbf'  => (new DateTimeImmutable())->getTimestamp(), // Not before
                'exp'  => ((new DateTimeImmutable())->modify("+{$expiry} hours"))->getTimestamp(), // Expire
                'user' => $cred_check, // User object
            ],
            $_ENV['jwt.secret'],
            $_ENV['jwt.alg']
        );

        $data = array('token'=>$token, 'credentials'=>$cred_check);


        return ($data);
    }



    public function logout(){
        $event_uri = session('event_uri');
        session()->destroy();
        return redirect()->to(base_url());

    }
}
