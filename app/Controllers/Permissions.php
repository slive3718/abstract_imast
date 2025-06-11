<?php

namespace App\Controllers;

use App\Models\Core\Api;

class Permissions extends User
{

    private Api $api;

    public function __construct()
    {
        parent::__construct();
        $this->event_uri = session('event_uri');

        $this->api = new Api();
        if (empty(session('email')) || session('email') == '') {
            return redirect()->to(base_url() . '/' . $this->event_uri . '/login');
            exit;
        }
    }


        public function Get($event_uri = null, $abstract_id=null)
    {
        $_POST['abstract_id'] = $abstract_id;
        $_POST['user_id'] = session('user_id');
        $event = (new AbstractEventsModel())->first();
        $abstract_details = (new Api())->post("user/get_abstract_by_id/{$this->event_uri}", $_POST);
        // $permissions = (new Api())->post("permissions/Get/{$this->event_uri}", $_POST);
//        print_r($abstract_details);exit;
        if (!$event) {
            return (new ErrorHandler($event))->errorPage();
        }
        if (!$abstract_details || $abstract_details->data =='' ) {
           exit;
        }

        $header_data = [
            'title' => $event->short_name
        ];
        $data = [
            'event'=> $event,
            'abstract_id'=> $abstract_id,
            'abstract_details'=> $abstract_details->data[0],
            // 'permissions' =>($permissions->data)?$permissions->data[0]:array(),
        ];
        return
            view('event/common/header', $header_data).
            view('event/permissions',$data).
            view('event/common/footer')
            ;

    }

    public function Add($event_uri){

        // print_r($_POST);exit;
        $result = $this->api->post("permissions/Create/{$this->event_uri}", $_POST);
        if(!$result->status){
            return (new ErrorHandler($result->data))->errorPage();
        }
        echo (json_encode(($result)));exit;
    }

     public function Update($event_uri){

        $result = $this->api->post("user/update_learning_objective/{$this->event_uri}", $_POST);
        if(!$result->status){
            return (new ErrorHandler($result->data))->errorPage();
        }
        echo (json_encode(($result)));exit;
    }

}