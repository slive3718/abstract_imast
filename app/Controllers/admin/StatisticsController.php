<?php

namespace App\Controllers\admin;

use App\Controllers\api\v1\UserApiController;
use App\Controllers\BaseController;
use App\Models\PapersModel;
use App\Models\UserModel;
use App\Models\UsersProfileModel;

class StatisticsController extends BaseController
{

    protected $helpers = ['form', 'general_helpers'];
    private $db;
    private $userModel;
    private $userProfileModel;
    public function __construct()
    {

        $this->db = \Config\Database::connect();
        $this->userModel = new UserModel();
        $this->userProfileModel = new UsersProfileModel();
    }

    public function index(){
        $header_data['title'] = 'Statistics';
        $data = [];

        $paperTotalPerCategory = (new PapersModel())->getActivePaperTotalPerCategory();
        $data['paperTotalPerCategory'] = $paperTotalPerCategory;
        return
            view('admin/common/header', $header_data).
            view('admin/statistics',$data).
            view('admin/common/footer')
            ;
    }

}
