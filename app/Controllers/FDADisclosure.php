<?php

namespace App\Controllers;

use App\Models\Core\Api;

class FDADisclosure extends User
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


    public function view_fda($event_uri = null, $abstract_id=null)
    {
        $_POST['abstract_id'] = $abstract_id;
        $_POST['user_id'] = session('user_id');
        $Api2 = new Api();
        $event = (new AbstractEventsModel())->first();
        $abstract_details = $Api2->post("user/get_abstract_by_id/{$this->event_uri}", $_POST);
//        print_r($abstract_details);exit;
        if (!$event) {
            return (new ErrorHandler($event))->errorPage();
        }
        if (!$abstract_details || $abstract_details->data =='' ) {
           exit;
        }

        $data['controller_name'] = $this->request->uri->getSegment(1);

        $header_data = [
            'title' => $event->short_name
        ];
        $data = [
            'id' => $this->request->uri->getSegment(3),
            'event'=> $event,
            'abstract_id'=> $abstract_id,
            'abstract_details'=> $abstract_details->data[0]
        ];
        return
            view('event/common/header', $header_data).
            view('event/fda_disclosure',$data).
            view('event/common/footer')
            ;

    }

    public function save_fda_disclosure($event_uri){

        $result = $this->api->post("user/save_fda_disclosure/{$this->event_uri}", $_POST);
        if(!$result->status){
            return (new ErrorHandler($result->data))->errorPage();
        }
        echo (json_encode(($result)));exit;
    }

}