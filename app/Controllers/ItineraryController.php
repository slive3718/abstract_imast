<?php

namespace App\Controllers;
use App\Models\EventsModel;
use App\Models\PanelistPaperSubModel;
use App\Models\PaperAuthorsModel;
use App\Models\PapersModel;
use App\Models\RoomsModel;
use App\Models\SchedulerDatesModel;
use App\Models\SchedulerModel;
use App\Models\SchedulerSessionTalksModel;
use App\Models\TracksModel;
use App\Models\UserModel;
use App\Models\UsersProfileModel;

class ItineraryController extends BaseController
{

    public function __construct()
    {

    }

    public function index()
    {
        $event = (new EventsModel())->first();
        if (!$event) {
            return 'error';
        }

        $header_data = [
            'title' => $event->short_name
        ];

        $data = [
            'event' => $event
        ];

        $scheduledDates = (new SchedulerDatesModel())->findAll();

        if($scheduledDates){
            foreach ($scheduledDates as &$scheduledDate){
                $scheduledDate['events']= (new SchedulerModel())->select('scheduler_events.*, scheduler_rooms.name as room_name')->join('scheduler_rooms', 'scheduler_events.room_id = scheduler_rooms.room_id', 'left')->where('is_deleted', 0)->where("DATE_FORMAT(session_date, '%Y-%m-%d')", date('Y-m-d', strtotime($scheduledDate['date'])))->orderBy('scheduler_events.session_start_time', 'asc')->findALl();
                if($scheduledDate['events']){
                    foreach ($scheduledDate['events'] as &$event) {
                        $event['moderators'] = [];
                        $event['track'] = [];
                        $session_chairs = !empty($event['session_chair_ids']) ? json_decode($event['session_chair_ids']) : [];
                        if($session_chairs) {
                            $event['moderators'] = (new UserModel())->join('users_profile', 'users.id = users_profile.author_id', 'left')->whereIn('users.id',$session_chairs)->findAll();
                        }
                        $event['talks'] = (new SchedulerSessionTalksModel())->join('papers', 'scheduler_session_talks.abstract_id = papers.id', 'left')->where('scheduler_event_id', $event['id'])->orderBy('scheduler_session_talks.sort', 'asc')->findAll();
                        foreach (  $event['talks']  as &$talk) {
                            if($talk['abstract_id']) {
                                if ($talk['submission_type'] == 'panel') {
                                    $paper_sub = (new PanelistPaperSubModel())->find($talk['paper_sub_id']);
                                    if ($paper_sub) {
                                        $talk['paper_sub'] = $paper_sub;
                                        $talk['panelist'] = (new UserModel())->select('* ,name as user_name , surname as user_surname')->join('users_profile up', 'users.id = up.author_id')->find($paper_sub['panelist_id']);
                                    }
                                } else {
                                    $talk['presenters'] = (new PaperAuthorsModel())->getPresentingAuthors($talk['abstract_id'])->get()->getResult();
                                    foreach ($talk['presenters'] as &$presenter) {
                                        $presenter->details = (new UsersProfileModel())->where('author_id', $presenter->author_id)->first();
                                    }
                                }
                            }else{
                                $talk['presenters'] = [];
                            }
                        }
                        if($event['session_track'])
                            $event['track'] = (new TracksModel())->find($event['session_track']);
                    }
                }
            }
        }

//        print_R($scheduledDates);exit;

        $data['schedules'] = $scheduledDates;
//        $data['scheduleDates'] = (new SchedulerDatesModel())->findALl();

        return
            view('itinerary/common/header', $header_data) .
            view('itinerary/index', $data) .
            view('itinerary/common/footer');
    }
}

