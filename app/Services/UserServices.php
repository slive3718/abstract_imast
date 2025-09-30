<?php

namespace App\Services;


use App\Models\DesignationsModel;
use App\Models\UserModel;
use App\Models\UsersProfileModel;
use CodeIgniter\Config\BaseService;

class UserServices extends BaseService
{

    private $request;
    public function __construct() {

        $this->request = \Config\Services::request();
    }

    public function get_user(int $user_id) : array
    {
        $userData = (new UserModel())->find($user_id);
        if(!$userData)
            return ['status' => 'error', 'message' => 'User not found!', 'data'=>[]];

        $userProfile = $this->get_user_profile($user_id);

        $userData['profile'] = $userProfile;
        return ['status'=>'success', 'data' => $userData];
    }

    public function get_user_profile($user_id) :array{
        $userProfile = (new UsersProfileModel())->where(['author_id'=>$user_id])->first();
        if(!$userProfile)
            return ['status' => 'error', 'message' => 'User profile not found!'];

        return $userProfile;
    }

    public function get_designations($designation_ids) :array{
        if($designation_ids){
            $designations = (new DesignationsModel())->whereIn('id', json_decode($designation_ids))->findAll();

            $designation_names = $designations ? array_map(function($designation) {
                if(strtolower($designation['name']) == 13){
                    return $designation['other_designation'] ?? 'Other';
                }
                return $designation['name'];
            }, $designations) : [];


            return ['status'=>'success', 'data'=>$designation_names];
        }
        return ['status'=>'error', 'message'=>'No designations found!', 'data'=>[]];
    }

}