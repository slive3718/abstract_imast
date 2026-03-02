<?php

namespace App\Services;


use App\Models\DesignationsModel;
use App\Models\SchedulerModel;
use App\Models\SiteSettingModel;
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

    public function get_designations($designation_ids, $otherDesignation = null) :array{
        if($designation_ids){
            $designations = (new DesignationsModel())->whereIn('id', json_decode($designation_ids))->findAll();

            $designation_names = $designations ? array_map(function($designation) use($otherDesignation) {
                if(strtolower($designation['name']) == 'other'){
                    return $otherDesignation ?? 'None';
                }
                return $designation['name'];
            }, $designations) : [];


            return ['status'=>'success', 'data'=>$designation_names];
        }
        return ['status'=>'error', 'message'=>'No designations found!', 'data'=>[]];
    }

    function get_assigned_moderators_events($moderators){
        $moderators_unique = array_values($moderators);

        $assigned_events = [];
        foreach ($moderators_unique as $moderator) {
            $events = $this->get_assigned_moderator_events($moderator['id'], $moderator['scheduler_id'])->asArray()->findAll();
            if ($events) {
                $assigned_events[$moderator['id']] = [
                    'user' => $moderator,
                    'events' => $events
                ];
            }
        }

        return $assigned_events;
    }
    public function is_incomplete_disclosure($user_id)
    {
        // Validate input
        if (!$user_id || !is_numeric($user_id)) {
            return 'missing UserID';
        }

        // Get user's disclosure record
        $disclosure = (new UsersProfileModel())->where('author_id', $user_id)->first();

        // If no disclosure record exists
        if (!$disclosure) {
            return 'Incomplete';
        }

        // Check if signature or signature date is missing
        if (empty($disclosure['disclosure_signature']) || empty($disclosure['signature_signed_date'])) {
            return 'Incomplete';
        }

        // Get current disclosure date from site settings
        $siteSetting = (new SiteSettingModel())->where('name', 'disclosure_current_date')->first();

        // If no current date setting exists
        if (!$siteSetting || empty($siteSetting['value'])) {
            // You might want to handle this differently - maybe throw an exception or return a specific error
            return 'Error: No disclosure date set in site settings';
        }

        $disclosure_current_date = $siteSetting['value'];
        $user_disclosure_date = $disclosure['signature_signed_date'];

        // Validate dates
        $current_date_timestamp = strtotime($disclosure_current_date);
        $user_date_timestamp = strtotime($user_disclosure_date);

        // Check if dates are valid
        if ($current_date_timestamp === false || $user_date_timestamp === false) {
            return 'Error: Invalid date format';
        }

        // Check if the signature date is older than current required date
        if ($user_date_timestamp < $current_date_timestamp) {
            return 'Expired';
        }

        // If we get here, the disclosure is valid
        return 'Complete';
    }

}