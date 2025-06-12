<?php

namespace App\Controllers\admin\Abstracts;

use App\Controllers\PapersController;
use App\Controllers\User;
use App\Models\AdminAcceptanceModel;
use App\Models\AdminIndividualPanelAcceptanceModel;
use App\Models\EventsModel;
use App\Models\ModeratorAcceptanceModel;
use App\Models\PanelistPaperSubModel;
use App\Models\PaperAuthorsModel;
use App\Models\PaperTypeModel;
use App\Models\PresentationPreferenceModel;
use App\Models\RoomsModel;
use App\Models\SchedulerDatesModel;
use App\Models\SchedulerModel;
use App\Models\SchedulerSessionTalksModel;
use App\Models\TracksModel;
use CodeIgniter\Controller;
use App\Models\Core\Api;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Database\BaseConnection;
use CodeIgniter\Exceptions\PageNotFoundException;

use App\Models\UserModel;
use App\Models\PapersModel;
use App\Models\PaperAuthorModel;
use App\Models\ReviewerModel;
use App\Models\AbstractTopicsModel;
// use App\Models\PopulationModel;
use App\Models\AbstractReviewModel;
use App\Models\InstitutionModel;
use App\Models\AuthorDetailsModel;
use App\Models\LearningObjectivesModel;
use App\Models\AbstractFileUploadsModel;
use App\Models\RemovedDisclosureAuthor;

use App\Models\AcceptanceRoomsModel;
use App\Models\AbstractCategoriesModel;

use App\Controllers\ExcelController;
use PhpOffice\PhpWord\Style\Paper;

class SchedulerController extends Controller
{
    public function __construct()
    {
        helper('url');
        $this->db = db_connect();

        if(session('user_id')){
            $this->user_id = session('user_id');
        }

        if(empty(session('email')) || session('email') == ''){
            exit;
        }
    }

    function index($event_uri){
        $header_data = [
            'title' => 'AFS Scheduler'
        ];

        $data = [
        ];

        return
            view('admin/common/header', $header_data).
            view('admin/scheduler/scheduler_full_calendar',$data).
            view('admin/common/footer')
            ;
    }


    public function get(){
        $scheduler_events =  (new SchedulerModel())->where(['is_deleted'=>0])->findAll();
       if($scheduler_events){
           foreach ($scheduler_events as &$scheduler_event) {
               $scheduler_event['rooms'] = (new RoomsModel())->find($scheduler_event['room_id']);
               $scheduler_event['talks'] = (new SchedulerSessionTalksModel())->where(['scheduler_event_id'=> $scheduler_event['id'], 'is_deleted'=>0])->orderBy('sort', 'asc')->findAll();
               if ($scheduler_event['session_chair_ids']) {
                   $scheduler_event['session_chair'] = []; // Initialize the array

                   foreach (json_decode($scheduler_event['session_chair_ids']) as $session_chair_id) {
                       $session_chair = (new UserModel())->find($session_chair_id);

                       // Add the acceptance field
                       $session_chair['acceptance'] = (new ModeratorAcceptanceModel())
                           ->where('scheduler_id', $scheduler_event['id'])
                           ->where('author_id', $session_chair_id)
                           ->first() ?? [];

                       // Append to the array
                       $scheduler_event['session_chair'][] = $session_chair;
                   }
               }

               if($scheduler_event['talks']){
                   foreach ($scheduler_event['talks'] as &$talk){
                       $talk['presenters'] = (new PaperAuthorsModel())->getPresentingAuthors($talk['abstract_id'])->get()->getResult();
                       if($talk['paper_sub_id']){
                           $talk['panelist'] =  $this->getTalkPanelist($talk['paper_sub_id']);
                       }
                       $talk['abstract'] = (new PapersModel())->find($talk['abstract_id']);
                   }
               }
           }
       }


       if($scheduler_events)
           return $this->response->setJSON($scheduler_events);
       return false;
    }

    function getTalkPanelist($id){
        return  (new PanelistPaperSubModel())
            ->join('users', 'panelist_paper_sub.panelist_id  = users.id', 'left')
            ->where('panelist_paper_sub.id', $id)->first();
    }

    public function get_one($scheduler_id){  //fetch all the accepted abstracts by admin.
        if($scheduler_id) {
            $scheduler_query = (new SchedulerModel());
            $scheduler_query->where('id', $scheduler_id);
            $scheduler_query->where('is_deleted', 0);
            $scheduler_query->select('*, 
            TIMESTAMPDIFF(MINUTE, session_start_time, session_end_time) AS duration_in_minutes,
            TIME(session_start_time) AS start_time, 
            TIME(session_end_time) AS end_time, 
            DATE_FORMAT(session_date, "%Y-%m-%d") AS session_day
            ')
            ;
            $scheduled_event = $scheduler_query->first();
            $accepted_abstracts = (new AdminAcceptanceModel())->where('acceptance_confirmation', 1)->orderBy('abstract_id', 'asc')->findAll();
//            $accepted_panels_group = (new AdminIndividualPanelAcceptanceModel())
//                ->select('panelist_paper_sub.*, papers.*, users.name as user_name, users.surname as user_surname')
//                ->join('panelist_paper_sub', 'admin_individual_panel_acceptance.individual_panel_id = panelist_paper_sub.id')
//                ->join('papers', 'panelist_paper_sub.paper_id = papers.id')
//                ->join('users', 'papers.user_id = users.id')
//                ->where('acceptance_confirmation', 1)
//                ->groupBy('panelist_paper_sub.paper_id')
//                ->orderBy('individual_panel_id', 'asc')->findAll();


            //todo: fetching should come from admin_accepted_abstracts because we need to find all accepted abstracts that have the sub-panel for selecting.

            $admin_accepted_panels = (new PapersModel())->select("papers.*, u.name as user_name, u.surname as user_surname")
                ->join('panelist_paper_sub pps', 'pps.paper_id = papers.id', 'left')
                ->join('admin_individual_panel_acceptance aipa', 'pps.id = aipa.individual_panel_id', 'inner')
                ->join('users u', 'papers.user_id = u.id', 'left')
                ->where('aipa.acceptance_confirmation', '1')
                ->where('aipa.presentation_preference !=', '2')
                ->where('papers.id IS NOT NULL') // Only fetch children
                ->groupBy('papers.id')->asArray()->findAll();

            foreach ($admin_accepted_panels as &$admin_accepted_panel){
                //todo: fetch all the panelist abstracts that are already accepted by admin
                $admin_accepted_panel['panelist_abstract'] = (new PanelistPaperSubModel())
                    ->join('users u', 'panelist_paper_sub.panelist_id = u.id', 'left')
                    ->join('admin_individual_panel_acceptance aipa', 'panelist_paper_sub.id = aipa.individual_panel_id', 'inner')
                    ->where('aipa.acceptance_confirmation', '1')
                    ->where('aipa.presentation_preference !=', '2')
                    ->where('panelist_paper_sub.paper_id', $admin_accepted_panel['id'])
                    ->findAll();
            }



//            $accepted_abstracts_panel  = (new SchedulerSessionTalksModel())->where('scheduler_event_id', $scheduler_id)->findAll();
//            foreach ($accepted_abstracts_panel as &$talk) {
//                $talk['abstract'] = (new PapersModel())->asArray()
//                    ->select('papers.*, u.name as user_name, u.surname as user_surname')
//                    ->join('users u', 'papers.user_id = u.id', 'left')
//                    ->find($talk['abstract_id']);
//                $talk['accepted_abstract_panel'] = (new AdminIndividualPanelAcceptanceModel())
//                    ->select('pps.*, p.id, admin_individual_panel_acceptance.*') // Select relevant fields
//                    ->join('panelist_paper_sub pps', 'admin_individual_panel_acceptance.individual_panel_id = pps.id', 'left')
//                    ->join('papers p', 'pps.paper_id = p.id', 'left')
//                    ->join('users u', 'pps.panelist_id = u.id', 'left')
//                    ->where('admin_individual_panel_acceptance.acceptance_confirmation', '1')
//                    ->where('admin_individual_panel_acceptance.presentation_preference !=', '2')
//                    ->where('p.id', $talk['abstract_id'])
//                    ->findAll();
//            }

            if(!empty($accepted_abstracts)){
                foreach ($accepted_abstracts as &$accepted_abstract){
                    $accepted_abstract['details'] = (new PapersModel())->find($accepted_abstract['abstract_id']);
                    $accepted_abstract['authors'] = (new PaperAuthorsModel())->join('users', 'paper_authors.author_id = users.id')->where(['paper_id'=> $accepted_abstract['abstract_id'], 'is_presenting_author'=>'Yes'])->orderBy('author_order', 'asc')->findAll();
                    $accepted_abstract['submitter'] = (new UserModel())->find($accepted_abstract['user_id']);
                }
            }


            $talks = (new SchedulerSessionTalksModel())->findAll();
            $view_data['scheduled_event'] = $scheduled_event;
            $view_data['abstracts'] = $accepted_abstracts;
            $view_data['admin_accepted_panels'] = $admin_accepted_panels;
            $view_data['presentation_preferences'] = json_decode(json_encode( (new PaperTypeModel())->findAll()), true);
            $view_data['tracks'] = (new TracksModel())->findAll();
            $view_data['talks'] = $talks;
            if ($scheduled_event) {
                $scheduled_event['rooms'] = (new RoomsModel())->find($scheduled_event['room_id']);
            }

//            print_R($view_data);exit;
            return $view_data;
        }
        return false;
    }

    public function get_one_new($id){  //fetch all the accepted abstracts by admin.
        if($id) {
            $scheduler_event = (new SchedulerModel());
            $scheduler_event->where('id', $id);
            $scheduler_event->where('is_deleted', 0);
            $scheduler_event->select('*, 
            TIMESTAMPDIFF(MINUTE, session_start_time, session_end_time) AS duration_in_minutes,
            TIME(session_start_time) AS start_time, 
            TIME(session_end_time) AS end_time, 
            DATE_FORMAT(session_date, "%Y-%m-%d") AS session_day
            ')
            ;
            $scheduled_event = $scheduler_event->first();
            $accepted_abstracts = (new AdminAcceptanceModel())->where('acceptance_confirmation', 1)->orderBy('abstract_id', 'asc')->findAll();
            $accepted_panels_group = (new AdminIndividualPanelAcceptanceModel())
                ->join('panelist_paper_sub', 'admin_individual_panel_acceptance.individual_panel_id = panelist_paper_sub.id')
                ->join('papers', 'panelist_paper_sub.paper_id = papers.id')
                ->where('acceptance_confirmation', 1)->groupBy('panelist_paper_sub.paper_id')->orderBy('individual_panel_id', 'asc')->findAll();

            if(!empty($accepted_abstracts)){
                foreach ($accepted_abstracts as &$accepted_abstract){
                    $accepted_abstract['details'] = (new PapersModel())->where($accepted_abstract['abstract_id']);
                    $accepted_abstract['authors'] = (new PaperAuthorsModel())->join('users', 'paper_authors.author_id = users.id')->where(['paper_id'=> $accepted_abstract['abstract_id'], 'is_presenting_author'=>'Yes'])->orderBy('author_order', 'asc')->findAll();
                    $accepted_abstract['submitter'] = (new UserModel())->find($accepted_abstract['user_id']);
                }
            }

            foreach ($accepted_panels_group as &$panel_group){
                $panel_group['panel_subs'] = (new PanelistPaperSubModel())->where('paper_id', $panel_group['paper_id'])->findALl();
                if($panel_group['panel_subs'] ){
                    foreach($panel_group['panel_subs'] as &$panel_sub){
                        $panel_sub['details'] = (new PapersModel())->find($panel_sub['paper_id']);
                        $panel_sub['authors'] = (new PaperAuthorsModel())->join('users', 'paper_authors.author_id = users.id')
                            ->where(['paper_id'=> $panel_sub['paper_id']])
                            ->where(['author_type'=> 'panelist'])
                            ->whereNotIn('paper_authors.id', function ($builder) {
                                $builder->select('paper_author_id')->from('removed_paper_authors');
                            })
                            ->findAll();
                        $panel_sub['submitter'] =  (new UserModel())->find($panel_sub['panelist_id']);
                    }
                }
            }

            $talks = (new SchedulerSessionTalksModel())->findAll();
            $view_data['scheduled_event'] = $scheduled_event;
            $view_data['abstracts'] = $accepted_abstracts;
            $view_data['panels'] = $accepted_panels_group;
            $view_data['presentation_preferences'] = json_decode(json_encode( (new PaperTypeModel())->findAll()), true);
            $view_data['tracks'] = (new TracksModel())->findAll();
            $view_data['talks'] = $talks;
            if ($scheduled_event) {
                $scheduled_event['rooms'] = (new RoomsModel())->find($scheduled_event['room_id']);
            }
            return $view_data;
        }
        return false;
    }

    public function get_one_json($id){
        return $this->response->setJSON($this->get_one($id));
    }

    function get_scheduled_events($ids){
        $ids = json_decode($ids);
        $scheduled_abstract = [];
        $scheduled_abstracts = [];
        if(is_array($ids)){
            foreach ($ids as $id){
                $scheduled_abstract['paper'] = (new PapersModel())->find($id);
                $scheduled_abstract['submitter'] = (new UserModel())->find($scheduled_abstract['paper']->id);
                $scheduled_abstract['authors'] = (new PaperAuthorsModel())->where(['paper_id'=> $scheduled_abstract['paper']->id, 'is_presenting_author'=>'Yes', 'author_type'=>'author'])->orderBy('author_order', 'asc')->findAll();
                $scheduled_abstract['panelist_presenters'] = (new PaperAuthorsModel())->where(['paper_id'=> $scheduled_abstract['paper']->id, 'is_presenting_author'=>'Yes', 'author_type'=>'panelist'])->orderBy('author_order', 'asc')->findAll();
                if(!empty($scheduled_abstract['authors'])){
                    foreach ( $scheduled_abstract['authors'] as &$author) {
                        $author['details'] = (new UserModel())->find($author['author_id']);
                    }
                }
                $scheduled_abstracts[] = $scheduled_abstract;
            }
            $this->response->setStatusCode('200');
            return $this->response->setJSON($scheduled_abstracts);
        }
    }

    function render_talks($id){
        $view_data = $this->get_one($id);
        return view('admin/scheduler/scheduler_talks_form', $view_data);
    }

    public function create()
    {
        helper('date');
        $post = $this->request->getPost();

        // Validation rules
        $validationRules = [
            'day' => 'required',
            'time_from' => 'required',
            'time_to' => 'required',
            'session_title' => 'required|string|max_length[255]',
            'session_description' => 'permit_empty|string',
//            'session_type' => 'required|string|max_length[100]',
//            'duration_talk' => 'required|integer|greater_than[0]',
//            'duration_break' => 'permit_empty|integer',
//            'session_number' => 'required|integer|greater_than[0]',
//            'rooms' => 'required|integer|greater_than[0]',
        ];

        // Set validation messages (optional)
        $validationMessages = [
            'day' => [
                'required' => 'The session date is required.',
            ],
            'time_from' => [
                'required' => 'The start time is required.',
            ],
            'time_to' => [
                'required' => 'The end time is required.',
            ],
            'session_title' => [
                'required' => 'The session title is required.',
                'max_length' => 'The session title cannot exceed 255 characters.',
            ],
        ];

        if (!$this->validate($validationRules, $validationMessages)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $this->validator->getErrors(),
            ]);
        }

        $day = $post['day'];
        $start = date("H:i", strtotime($post['time_from']));
        $end = date("H:i", strtotime($post['time_to']));


        if ($start >= $end) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => ['time_from' => 'The start time must be earlier than the end time.'],
            ]);
        }
        $chair = [];

        if($post['session_chair']){
            foreach ($post['session_chair'] as $session_chair){
               if($session_chair){
                   $chair[] = $session_chair;
               }
            }
        }

        $insertFields = [
            'session_date' => date('Y-m-d H:i:s', strtotime($post['day'])),
            'session_start_time' => date('Y-m-d H:i:s', strtotime("$day $start")),
            'session_end_time' => date('Y-m-d H:i:s', strtotime("$day $end")),
            'session_title' => $post['session_title'],
            'description' => $post['session_description'],
            'paper_type' => $post['session_type'],
            'talk_duration' => $post['duration_talk'],
            'break_duration' => $post['duration_break'],
            'session_number' => $post['session_number'],
            'session_chair_ids' => json_encode($chair),
            'session_track' => $post['session_track'] ?? null,
            'room_id' => $post['rooms'],
        ];

        if (!$post['updateID']) {
            $result = (new SchedulerModel())->insert($insertFields);
        } else {
            $result = (new SchedulerModel())
                ->where('id', $post['updateID'])
                ->set($insertFields)
                ->update();
        }

        if ($result) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Session Saved!',
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to save session.',
            ]);
        }
    }

    function move ()
    {
        $post = $this->request->getPost();
        $updateArray = [
            'session_date'=> date('Y-m-d H:i:s', strtotime( $post['start'])),
            'session_start_time'=> date('Y-m-d H:i:s', strtotime( $post['start'])),
            'session_end_time'=> date('Y-m-d H:i:s', strtotime( $post['end'])),
        ];

        if($post['room_id']){
            $updateArray['room_id'] = $post['room_id'];
        }

        $talkModel = new SchedulerSessionTalksModel();
        $talks = $talkModel->where('scheduler_event_id', $post['id'])->findAll();
        if ($talks) {
            $startShift = strtotime($post['start']) - strtotime($talks[0]['time_start']);
            foreach ($talks as $talk) {
                $fieldUpdate = [
                    'time_start' => date('Y-m-d H:i', strtotime($talk['time_start']) + $startShift),
                    'time_end' => date('Y-m-d H:i', strtotime($talk['time_end']) + $startShift),
                ];
                $talkModel->where('id', $talk['id'])->set($fieldUpdate)->update();
            }
        }

        (new SchedulerModel())->set($updateArray)->where('id', $post['id'])->update();
        return $this->response->setJSON(['status' => 'success', 'message' => 'Talks processed successfully!']);
    }

    public function getSchedulerAllowedDate(){
        $this->response->setStatusCode(200);
        return $this->response->setJson((new SchedulerDatesModel())->where('is_deleted', 0)->findAll() ?? []);
    }

    public function delete($id){
        $result = (new SchedulerModel())->where('id', $id)->set(['is_deleted' => 1])->update();
        if($result)
            return $this->response->setJson(['status'=>'success']);
    }

}