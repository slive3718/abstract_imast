<?php

namespace App\Controllers\admin\Abstracts;

use App\Models\EventsModel;
use CodeIgniter\Controller;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Database\BaseConnection;

use App\Models\UserModel;
use App\Models\PapersModel;
use App\Models\PaperAuthorModel;
use App\Models\ReviewerModel;

class AdminLogin extends Controller
{
    private BaseConnection $db;

    public function __construct()
    {
        $this->db = db_connect();
    }
    
    public function index(){
        $header_data = [
            'title' => ''
        ];
        $data = [];
        return
            view('admin/common/header', $header_data).
            view('admin/login',$data).
            view('admin/common/footer')
            ;
    }


    public function logout(){
        session()->destroy();
        return redirect()->to(base_url().'admin/');
    }
    

}