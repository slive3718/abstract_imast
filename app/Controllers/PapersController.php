<?php

namespace App\Controllers;



use App\Models\PapersModel;
use PhpOffice\PhpWord\Style\Paper;

class PapersController extends BaseController
{

    public function __construct()
    {

        if (session('user_id')) {
            $this->user_id = session('user_id');
        } else {
            return redirect()->to(base_url() . 'afs/login');
        }

        if (empty(session('email')) || session('email') == '') {
            return redirect()->to(base_url() . 'afs/logout');
        }

        helper('text');

    }

    function index(){
        return ("Papers Controller");
    }

    function get(){
        return (new PapersModel())->findAll();
    }

    function get_one($id){
        return (new PapersModel())->find($id);
    }



}