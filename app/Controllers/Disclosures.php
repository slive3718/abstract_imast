<?php

namespace App\Controllers;

use App\Models\Core\Api;

class Disclosures extends BaseController
{

    private Api $api;

    public function __construct()
    {
        $this->event_uri = session('event_uri');
        $this->api = new Api();
        if (empty(session('email')) || session('email') == '') {
            return redirect()->to(base_url() . '/' . $this->event_uri . '/login');
            exit;
        }
    }

    public function index($event_uri = null, $unique_key= null)
    {
        //        print_r( $this->uri);exit;
        $event = (new AbstractEventsModel())->first();
        if(!$event){
            return (new ErrorHandler($event))->errorPage();
        }

        $header_data = [
            'title' => $event->short_name
        ];
        $data = [
            'event'=> $event
        ];
        return
            view('event/disclosures/common/header', $header_data).
            view('event/disclosures/login',$data).
            view('event/disclosures/common/footer')
            ;
    }


}
