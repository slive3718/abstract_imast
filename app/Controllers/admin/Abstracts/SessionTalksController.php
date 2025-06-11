<?php

namespace App\Controllers\admin\Abstracts;
use App\Models\AdminIndividualPanelAcceptanceModel;
use App\Models\EventsModel;
use App\Models\PanelistPaperSubModel;
use App\Models\PaperAuthorsModel;
use App\Models\PapersModel;
use App\Models\SchedulerModel;
use App\Models\SchedulerSessionTalksModel;
use App\Models\UserModel;
use App\Models\UsersProfileModel;

class SessionTalksController extends SchedulerController
{

    function index($event_uri)
    {
        $event = (new EventsModel())->first();
        if (!$event) {
            exit;
        }

        $header_data = [
            'title' => 'AFS Scheduler'
        ];
        $data = [
            'event' => $event
        ];
        return
            view('admin/common/header', $header_data) .
            view('admin/scheduler/scheduler_full_calendar', $data) .
            view('admin/common/footer');
    }

    function create()
    {
        $post = $this->request->getPost();
        $talks = $post['talk_details'] ?? [];
        $removedTalks = $post['removed_talks'] ?? [];
        $scheduler_event_id = $post['scheduler_event_id'] ?? [];

        if (!$scheduler_event_id) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Schedule Id not found!']);
        }

        $sessionTalks = new SchedulerSessionTalksModel();
        $existingTalks = $sessionTalks
            ->select('id, abstract_id, time_start, time_end, duration, sort, break_duration, paper_sub_id')
            ->where('scheduler_event_id', $scheduler_event_id)
            ->findAll();

        // Group existing talks by `abstract_id` and `paper_sub_id`
        $existingTalksLookup = [];
        foreach ($existingTalks as $existingTalk) {
            $key = $existingTalk['abstract_id'] . '_' . $existingTalk['paper_sub_id'];
            $existingTalksLookup[$key] = $existingTalk;
        }

        $talkDataArray = []; // New talks
        $filteredKeys = []; // Track keys of valid talks
        if ($talks) {
            foreach ($talks as $index => $talk) {
                $abstractId = $talk['abstract_id'];
                $paperSubId = $talk['paper_sub_id'] ?? null;

                $talkData = [
                    'abstract_id' => $abstractId,
                    'scheduler_event_id' => $scheduler_event_id,
                    'time_start' => date('Y-m-d H:i:s', strtotime($talk['start_time'])),
                    'time_end' => date('Y-m-d H:i:s', strtotime($talk['end_time'])),
                    'duration' => $talk['duration'],
                    'sort' => $index + 1,
                    'break_duration' => $talk['break_duration'],
                    'custom_abstract_desc' => $talk['custom_desc'],
                    'paper_sub_id' => $paperSubId,
                ];

                $key = $abstractId . '_' . $paperSubId;

                if (isset($existingTalksLookup[$key])) {
                    // Check if updates are needed
                    $existingTalk = $existingTalksLookup[$key];
                    if (
                        $talkData['time_start'] !== $existingTalk['time_start'] ||
                        $talkData['time_end'] !== $existingTalk['time_end'] ||
                        $talkData['duration'] !== $existingTalk['duration'] ||
                        $talkData['sort'] !== $existingTalk['sort'] ||
                        $talkData['break_duration'] !== $existingTalk['break_duration'] ||
                        $talkData['paper_sub_id'] !== $existingTalk['paper_sub_id']
                    ) {
                        // Update the existing talk
                        $sessionTalks->update($existingTalk['id'], $talkData);
                    }
                } else {
                    // Add new talk
                    $talkDataArray[] = $talkData;
                }

                // Mark this key as processed
                $filteredKeys[] = $key;
            }

            // Remove talks if any are marked for deletion
            if (!empty($removedTalks)) {
                $sessionTalks->whereIn('abstract_id', $removedTalks)->delete();
            }

            if (!empty($talkDataArray)) {
                $sessionTalks->insertBatch($talkDataArray);
            }
        } else {
            // If no talks are provided, delete all associated with the scheduler_event_id
            $sessionTalks->where('scheduler_event_id', $scheduler_event_id)->delete();
        }

        return $this->response->setJSON(['status' => 'success', 'message' => 'Talks processed successfully!']);
    }






    public function get(){ //this will get both paper and panelist
            $talks = (new SchedulerSessionTalksModel())
                ->select('scheduler_session_talks.*, p.custom_id as abstract_custom_id, p.title as abstract_title, p.submission_type')
                ->join('papers p', 'scheduler_session_talks.abstract_id = p.id', 'left')
                ->join('scheduler_events se', 'scheduler_session_talks.scheduler_event_id = se.id', 'left')
                ->orderBy('sort', 'asc')
                ->findAll();

        if($talks){
            foreach ($talks as &$talk){
                if($talk['submission_type'] == 'panel'){
                    $paper_sub = (new PanelistPaperSubModel())->find($talk['paper_sub_id']);
                    if($paper_sub){
                        $talk['paper_sub'] = $paper_sub;
                        $talk['panelist'] = (new UserModel())
                            ->select('* ,name as user_name , surname as user_surname')
                            ->join('users_profile up', 'users.id = up.author_id')->find($paper_sub['panelist_id']);
                    }
                }else{
                    if($talk['abstract_id']) {
                        $talk['presenters'] = (new PaperAuthorsModel())->getPresentingAuthors($talk['abstract_id'])->get()->getResult();
                        foreach ($talk['presenters'] as &$presenter) {
                            $presenter->details = (new UsersProfileModel())->find($presenter->author_id);
                        }
                    }
                }

            }
        }
        return $this->response->setJSON(['status'=>'success', 'data'=> $talks]);
    }

    public function talk_scheduled($scheduler_id){ //this will get both paper and panelist
        $talks = (new SchedulerSessionTalksModel())
            ->select('scheduler_session_talks.*, p.custom_id as abstract_custom_id, p.title as abstract_title, p.submission_type')
            ->join('papers p', 'scheduler_session_talks.abstract_id = p.id', 'left')
            ->join('scheduler_events se', 'scheduler_session_talks.scheduler_event_id = se.id', 'left')
            ->orderBy('sort', 'asc')
            ->where('scheduler_event_id', $scheduler_id)
            ->findAll();

        if($talks){
            foreach ($talks as &$talk){
                if($talk['submission_type'] == 'panel'){
                    $paper_sub = (new PanelistPaperSubModel())->find($talk['paper_sub_id']);
                    if($paper_sub){
                        $talk['paper_sub'] = $paper_sub;
                        $talk['panelist'] = (new UserModel())
                            ->select('* ,name as user_name , surname as user_surname')
                            ->join('users_profile up', 'users.id = up.author_id')->find($paper_sub['panelist_id']);
                    }
                }else{
                    if($talk['abstract_id']) {
                        $talk['presenters'] = (new PaperAuthorsModel())->getPresentingAuthors($talk['abstract_id'])->get()->getResult();
                        foreach ($talk['presenters'] as &$presenter) {
                            $presenter->details = (new UsersProfileModel())->find($presenter->author_id);
                        }
                    }else{
                        $talk['presenters'] = [];
                    }
                }

            }
        }
        return $this->response->setJSON(['status'=>'success', 'data'=> $talks]);
    }



}
