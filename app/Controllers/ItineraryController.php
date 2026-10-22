<?php

namespace App\Controllers;
use App\Models\AdminAcceptanceModel;
use App\Models\DesignationsModel;
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
    private ?array $designationNameMap = null;

    public function __construct()
    {

    }

    public function index()
    {

        $header_data = [
            'title' => "Imast Itinerary"
        ];

        $data = [
        ];

        $scheduledDates = (new SchedulerDatesModel())->findAll();
        $scheduledDates = $this->getItinerary($scheduledDates);

        $data['schedules'] = $scheduledDates;

        return
            view('itinerary/common/header', $header_data) .
            view('itinerary/index', $data) .
            view('itinerary/common/footer');
    }

    function getItinerary($scheduledDates)
    {
        if($scheduledDates){
            foreach ($scheduledDates as &$scheduledDate){
                $scheduledDate['events']= (new SchedulerModel())->select('scheduler_events.*, scheduler_rooms.name as room_name')->join('scheduler_rooms', 'scheduler_events.room_id = scheduler_rooms.room_id', 'left')->where('is_deleted', 0)->where("DATE_FORMAT(session_date, '%Y-%m-%d')", date('Y-m-d', strtotime($scheduledDate['date'])))->orderBy('scheduler_events.session_start_time', 'asc')->findAll();
                if($scheduledDate['events']){
                    foreach ($scheduledDate['events'] as &$event) {
                        $event['moderators'] = [];
                        $event['track'] = [];
                        $session_chairs = !empty($event['session_chair_ids']) ? json_decode($event['session_chair_ids']) : [];
                        if($session_chairs) {
                            $event['moderators'] = (new UserModel())
                                ->join('users_profile', 'users.id = users_profile.author_id', 'left')
                                ->whereIn('users.id',$session_chairs)
                                ->orderBy('users.name', 'asc')
                                ->findAll();

                            foreach ($event['moderators'] as &$moderator) {
                                $rawDesignations = $moderator['designations'] ?? '[]';
                                $moderatorDesignations = json_decode($rawDesignations, true);
                                $moderator['designation'] = $this->getDesignationNames(
                                    is_array($moderatorDesignations) ? $moderatorDesignations : [],
                                    $moderator['other_designation'] ?? null
                                );
                            }
                        }

                        $event['table_moderators'] = [];
                        $table_moderator_ids = json_decode($event['table_moderator_ids'] ?? '[]', true);
                        $table_moderator_ids = is_array($table_moderator_ids)
                            ? array_values(array_filter($table_moderator_ids))
                            : [];
                        if (!empty($table_moderator_ids)) {
                            $event['table_moderators'] = (new UserModel())
                                ->join('users_profile', 'users.id = users_profile.author_id', 'left')
                                ->whereIn('users.id', $table_moderator_ids)
                                ->orderBy('users.name', 'asc')
                                ->findAll();

                            foreach ($event['table_moderators'] as &$moderator) {
                                $rawDesignations = $moderator['designations'] ?? '[]';
                                $moderatorDesignations = json_decode($rawDesignations, true);
                                $moderator['designation'] = $this->getDesignationNames(
                                    is_array($moderatorDesignations) ? $moderatorDesignations : [],
                                    $moderator['other_designation'] ?? null
                                );
                            }
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
                                    $talk['presenters'] = (new PaperAuthorsModel())->getPresentingAuthors($talk['abstract_id'])->get()->getResultArray();
                                    $talk['authors'] = (new PaperAuthorsModel())->getAuthors($talk['abstract_id'])->get()->getResultArray();
                                    foreach ($talk['presenters'] as &$presenter) {
                                        $presenter['details'] = (new UsersProfileModel())->where('author_id', $presenter['author_id'])->first();
                                        $presenter['details']['designation'] = $this->getDesignationNames(
                                            json_decode($presenter['details']['designations'] ?? '[]', true),
                                            $presenter['details']['other_designation'] ?? null
                                        );
                                    }
                                    foreach ($talk['authors'] as &$author) {
                                        $author['details'] = (new UsersProfileModel())->where('author_id', $author['author_id'])->first();
                                    }
                                    $talk['admin_acceptance'] = (new AdminAcceptanceModel())->where('abstract_id', $talk['abstract_id'])->first();
                                }
                            }else{
                                $talk['presenters'] = [];
                                $talk['authors'] = [];
                            }
                        }
                        if($event['session_track'])
                            $event['track'] = (new TracksModel())->find($event['session_track']);
                    }
                }
            }
        }
        return $scheduledDates;
    }

    private function getDesignationNameMap(): array
    {
        if ($this->designationNameMap === null) {
            $this->designationNameMap = array_column((new DesignationsModel())->findAll(), 'name', 'id');
        }

        return $this->designationNameMap;
    }

    private function getDesignationNames(array $designationIds, ?string $otherDesignation = null): array
    {
        $designationNames = $this->getDesignationNameMap();
        $formattedDesignations = [];

        foreach ($designationIds as $designationId) {
            $designationId = (int) $designationId;
            if (!isset($designationNames[$designationId])) {
                continue;
            }

            if (strtolower($designationNames[$designationId]) === 'other') {
                if (!empty($otherDesignation)) {
                    $formattedDesignations[] = $otherDesignation;
                }
                continue;
            }

            if (strtolower($designationNames[$designationId]) === 'none') {
                $formattedDesignations[] = '';
                continue;
            }

            $formattedDesignations[] = $designationNames[$designationId];
        }

        return $formattedDesignations;
    }
}