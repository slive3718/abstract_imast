<?php

namespace App\Services;


use App\Models\UserModel;
use CodeIgniter\Config\BaseService;

class AccountServices extends BaseService
{

    private $request;
    public function __construct() {

        $this->request = \Config\Services::request();
    }

    function is_unique_email( string $email, int $userId) : array
    {
        $existingUser = (new UserModel())
            ->where(['email'=> $email]);

        if ($userId) {
            $existingUser->where('id !=', $userId);
        }

        if ($existingUser->first()) {
            return (['status' => 'error', 'message' => 'This email is already taken!']);
        }
        return (['status' => 'success', 'message' => 'Email is valid!']);
    }
}