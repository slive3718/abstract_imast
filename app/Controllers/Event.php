<?php

namespace App\Controllers;


use App\Models\Core\Api;
use App\Models\AbstractEventsModel;
use CodeIgniter\HTTP\ResponseInterface;

class Event extends BaseController
{

    public function index()
    {

        if(session('user_id')){
            return redirect()->to(base_url().'/home');
        }

        $header_data = [
            'title' => 'Abstract Asia Pasific'
        ];
        $data = [ ];
        return
            view('event/common/header', $header_data).
            view('event/landing', $data).
            view('event/common/footer')
            ;
    }

    public function submissionGuidelines(){

        $header_data = [
            'title' => 'Submission Guidelines'
        ];
        $data = [
        ];
        return
            view('event/common/header', $header_data).
            view('event/landing', $data).
            view('event/common/footer')
            ;
    }
}
