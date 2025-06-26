<?php

namespace App\Controllers;

use App\Libraries\MailGunEmail;
use App\Libraries\PhpMail;
use App\Models\Core\Api;
use App\Models\AbstractEventsModel;
use App\Models\UserModel;
use App\Models\UsersProfileModel;
use DateTimeImmutable;
use Firebase\JWT\JWT;


class Account extends BaseController
{
    public function __construct()
    {

    }

    public function index($event_uri): string
    {
        $header_data = [
            'title' => "Asia Pacific Registration"
        ];
        $data = [];
        return
            view('event/common/header', $header_data).
            view('event/register', $data).
            view('event/common/footer')
            ;
    }

    public function register(){
//        print_R($_POST);exit;
        $post = $this->request->getpost();
        $usersModel = (new UserModel());

        $required_data = [
            'name',
            'surname',
            'email',
            'password'
        ];

        if (json_last_error() != 0) {
            return $this->response->setStatusCode(400, "body is not valid JSON");
        }

        foreach ($required_data as $required)
        {
            if (!array_key_exists($required, $post))
            {
                return $this->response->setStatusCode(400, "{$required} is required");
            }

            if ($post[$required] == '')
            {
                return $this->response->setStatusCode(400, "{$required} cannot be empty");
            }
        }

        $email_exists= $usersModel->where('email', $post['email'])->first()??false;

        if ($email_exists)
        {
            session()->setFlashdata('status', 'warning');
            session()->setFlashdata('notification', "Email {$post['email']} already exists in our database");
            return (json_encode(['status'=>500, 'message'=>"Email {$post['email']} already exists in our database"]));
        }

//        print_r($post);exit;
        $usersModel
            ->set([
                'name'=>$post['name'],
                'surname'=>$post['surname'],
                'email'=>$post['email'],
                'password'=>password_hash($post['password'], PASSWORD_DEFAULT)
            ])->insert();

        $insertID = $usersModel->insertID();
        if($insertID){
            (new UsersProfileModel())->set(['author_id' => $insertID])->insert();
        }

        $expiry = (isset($post['expiry']))?$post['expiry']:24; // hours

        $cred_check = $usersModel
            ->select('id, prefix, name, surname, suffix, email, is_super_admin')
            ->where(['id'=>$insertID])
            ->first()??false;



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
        

        if($cred_check){
            $session_array = array(
                'email'=>$cred_check['email'],
                'token'=>$token,
                'user_id'=>$cred_check['id'],
                'user_type'=>"user",
                'name'=>$cred_check['name'],
                'surname'=>$cred_check['surname'],
            );
            session()->set($session_array);
            session()->setFlashdata('status', 'success');
            session()->setFlashdata('notification', "Account creates successfully!");
            echo (json_encode(['status'=>'200', 'message'=>"Successfully created"]));
        }
    }

    public function update_password($event_uri){
        $post = $this->request->getPost();
        $post['user_id'] = session('user_id');
        $UsersModel = (new UserModel());
        $user = $UsersModel->where('id', $post['user_id'])->first()??false;

        if (!$user)
        {
            return (json_encode(['status'=> 400, 'message'=>"Email not found", 'data'=>'']));
        }

        if (password_verify($post['current_password'], $user['password']))
        {
            $UsersModel
                ->set([
                    'password'=>password_hash($post['new_password'], PASSWORD_DEFAULT)
                ])
                ->where('id', $post['user_id'])
                ->update();

            return (json_encode(['status'=> 200, 'message'=>"Password updated successfully", 'data'=>'']));
        }

        return (json_encode(['status'=> 500, 'message'=>"Password not matched", 'data'=>'']));
    }

    public function reset_password(){

        helper('text');
        $post = $this->request->getPost();
        $UsersModel = (new UserModel());

        $user = $UsersModel->where('email', $post['email'])->first()??false;


        if(isset($post['from']) && $user){
            $userType = $post['from'];
            if($userType == 'regular_reviewer' && $user['is_regular_reviewer'] !== '1'){
                $this->response->setStatusCode(401, "Email not a regular reviewer");
                return (json_encode(['status'=> 401, 'message'=>"Email not a regular reviewer", 'data'=>'']));
            } else if($userType == 'deputy_reviewer' && $user['is_deputy_reviewer'] !== '1'){
                $this->response->setStatusCode(401, "Email not a deputy reviewer");
                return (json_encode(['status'=> 401, 'message'=>"Email not a deputy reviewer", 'data'=>'']));
            }
        }
        if (!$user)
        {
            $this->response->setStatusCode(400, "Invalid credentials");
            return (json_encode(['status'=> 400, 'message'=>"Email not found", 'data'=>'']));

        }else{

            $random_password = random_string('alnum', 6);
            $UsersModel
                ->set('password', password_hash($random_password, PASSWORD_DEFAULT))
                ->where('email', $post['email'])
                ->update();

            $mail = new PhpMail();

            $message = 'Hi '.ucfirst($user['name']) .' '.ucfirst($user['surname']).', <br> We received a request to reset the password of your account. <br><br>
                    Your new password is: '.$random_password.'<br> <br> 
                    You can update your password by logging into submission <a href="https://imast.owpm2.com"></a>imast.owpm2.com, and on the top corner menu, click on settings, then password settings. <br>
                    <p> If you  need further assistance, please contact <a href="support@owpm2.com">support@owpm2.com</a></p>';

            $from['name']="IMAST 2026";
            $from['email'] = "IMAST@owpm2.com";
            if($mail->send($from, [$user['email']],  'IMAST 2026 Password Reset', $message)){
                $this->response->setStatusCode(200, 'success');
                return (json_encode(['status'=> 200, 'message'=>"Password sent to email", 'data'=>'']));
            }
        }
    }
}
