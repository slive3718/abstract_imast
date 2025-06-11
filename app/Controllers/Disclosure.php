<?php

namespace App\Controllers;

use App\Models\Core\Api;

class Disclosure extends BaseController
{

    private Api $api;

    public function __construct()
    {
        $this->api = new Api();
//        if (empty(session('email')) || session('email') == '') {
//            print_r('User must login to continue');
//            exit;
//        }
    }

    public function index($event_uri = null): string
    {
        $event = $this->api->getRequest("event/details/{$event_uri}");
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
            view('event/common/header', $header_data).
            view('disclosure/index',$data).
            view('event/common/footer')
            ;
    }
}