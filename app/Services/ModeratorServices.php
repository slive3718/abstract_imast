<?php

namespace App\Services;

use App\Models\SchedulerModel;
use App\Models\UserModel;
use CodeIgniter\Config\BaseService;

class ModeratorServices extends BaseService
{

    private $request;
    public function __construct() {

        $this->request = \Config\Services::request();
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

    function get_assigned_moderator_events($moderator_id, $scheduler_id) {
        $result = (new SchedulerModel())
            ->select('scheduler_events.*, r.name as room_name')
            ->join('scheduler_rooms r', 'scheduler_events.room_id = r.id', 'left')
            ->where("scheduler_events.session_chair_ids LIKE '%\"{$moderator_id}\"%'");
        if($scheduler_id){
            $result->where("scheduler_events.id", $scheduler_id);
        }

        return $result;
    }
}
