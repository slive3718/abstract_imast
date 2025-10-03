<?php

namespace App\Services;


use App\Models\DesignationsModel;
use App\Models\SchedulerModel;
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

    function get_moderator_ids(){

        $result = (new SchedulerModel())->asArray()->findAll();
        $moderator_ids = [];
        if ($result) {
            foreach ($result as $res) {
                if (!empty($res['session_chair_ids']) && $res['session_chair_ids'] !== '0' && $res['session_chair_ids'] !== "[]") {
                    $session_chair_ids = json_decode($res['session_chair_ids'], true);

                    if (is_array($session_chair_ids)) {
                        foreach ($session_chair_ids as $session_chair_id) {
                            $moderator_ids[] = [
                                'id' => $session_chair_id,
                                'user' => (new UserModel())->find($session_chair_id),
                                'event' => $res
                            ];
                        }
                    }
                }
            }
        }
        return $moderator_ids;
    }

    function get_assigned_moderators_events($moderators){
        $moderators_unique = array_values($moderators);

        $assigned_events = [];
        foreach ($moderators_unique as $moderator) {
            $events = $this->get_assigned_moderator_events($moderator['id']);
            if ($events) {
                $assigned_events[$moderator['id']] = [
                    'user' => $moderator,
                    'events' => $events
                ];
            }
        }

        return $assigned_events;
    }

    function get_assigned_moderator_events($moderator_id){
        $result = (new SchedulerModel())
            ->select('scheduler_events.*, r.name as room_name')
            ->join('scheduler_rooms r', 'scheduler_events.room_id = r.id', 'left')
            ->where('scheduler_events.session_chair_ids', 'LIKE', '%"' . $moderator_id . '"%')
            ->asArray()->findAll();
        return $result;
    }
}

//->select('scheduler_events.*, r.name as room_name')
//
