<?php

namespace App\Controllers;


use App\Models\Core\Api;
use App\Models\AbstractEventsModel;
use CodeIgniter\HTTP\ResponseInterface;

class Event extends BaseController
{

    public function index()
    {
        $event_uri = 1;
        $event =  new \stdClass();
        $event = (new AbstractEventsModel())->first();

        if(session('user_id')){
            return redirect()->to(base_url().'/home');
        }

        if(!$event){
            return 'error';
        }

        $header_data = [
            'title' => $event->short_name
        ];
        $data = [
          'event'=> $event
        ];
        return
            view('event/common/header', $header_data).
            view('event/landing', $data).
            view('event/common/footer')
            ;
    }

    public function submissionGuidelines(){
        $event_uri = 1;
        $event =  new \stdClass();
        $event = (new AbstractEventsModel())->first();

        if(!$event){
            return 'error';
        }

        $header_data = [
            'title' => $event->short_name
        ];
        $data = [
            'event'=> $event
        ];
        return
            view('event/common/header', $header_data).
            view('event/submission_guidelines', $data).
            view('event/common/footer')
            ;
    }
}
