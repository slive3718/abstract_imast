<?php

namespace App\Controllers\admin;

use App\Controllers\admin\Abstracts\AbstractController;
use App\Controllers\User;
use App\Libraries\PhpMail;
use App\Models\AbstractEventsModel;
use App\Models\AdminIndividualPanelAcceptanceModel;
use App\Models\DivisionsModel;
use App\Models\EmailLogsModel;
use App\Models\EmailRecipientsModel;
use App\Models\EmailTemplatesModel;
use App\Models\EventsModel;
use App\Models\PanelistPaperSubModel;
use App\Models\PaperAssignedReviewerModel;
use App\Models\PaperAuthorsModel;
use App\Models\PapersDeputyAcceptanceModel;
use App\Models\PaperTypeModel;
use App\Models\PaperUploadsModel;
use App\Models\PaperUploadsViewsModel;
use App\Models\ReviewerPaperUploadsModel;
use App\Models\SchedulerModel;
use App\Models\SchedulerSessionTalksModel;
use App\Models\SiteSettingModel;
use App\Models\UsersProfileModel;
use CodeIgniter\Controller;
use App\Models\Core\Api;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Database\BaseConnection;
use CodeIgniter\Exceptions\PageNotFoundException;

use App\Models\UserModel;
use App\Models\PapersModel;
use App\Models\ReviewerModel;
// use App\Models\PopulationModel;
use App\Models\AbstractReviewModel;
use App\Models\AbstractFileUploadsModel;
use App\Models\AbstractCategoriesModel;

use App\Controllers\ExcelController;
use PhpParser\Node\Stmt\Case_;
use PHPUnit\TextUI\XmlConfiguration\Php;

class EmailController extends Controller
{

    public function __construct()
    {
        helper('url');

        if (empty(session('email')) || session('email') == '') {
            exit;
        }
    }

    public function email_templates(){
        $event = (new EventsModel())->first();

        $header_data = [
            'title' => 'Email Templates'
        ];

        $email_templates = (new EmailTemplatesModel())->findAll();

        $data = [
            'event'=> $event,
            'email_templates'=>$email_templates
        ];
        return
            view('admin/common/header', $header_data).
            view('admin/email_templates',$data).
            view('admin/common/footer')
            ;
    }

    public function get_email_templates($id){
        return json_encode(['status' => '200', 'message' => 'success', 'data' => (new EmailTemplatesModel())->find($id)]);
    }

    public function get_all_email_templates(){
        return json_encode(['status' => '200', 'message' => 'success', 'data' => (new EmailTemplatesModel())->findAll()]);
    }

    public function save_email_template(){
        $post = $this->request->getPost();
//        print_r($post);exit;
        $EmailTemplatesModel = new EmailTemplatesModel();

        $insertFields = [
            'template_name'=>$post['template_name'],
            'email_subject'=>$post['email_subject'],
            'email_description'=>$post['email_description'],
            'email_category'=>$post['email_category'],
            'email_body'=>$post['message'],
            'date_time'=>date('Y-m-d H:i:s')
        ];

        if(isset($post['template_id']) && $post['template_id'] !== "undefined") {
            $result = $EmailTemplatesModel->set($insertFields)->where('id', $post['template_id'])->update();
            if($result){
                return json_encode(['status'=>200, 'message'=>'success', 'data'=>'']);
            }else{
                return json_encode(['status'=>500, 'message'=>'error updating', 'data'=>'']);
            }
        }else{
            $EmailTemplatesModel->set($insertFields)->insert();
        }
    }

    public function mass_mailer(){
        $event = (new EventsModel())->first();

        $header_data = [
            'title' => 'Email Templates'
        ];

        $email_templates = (new EmailTemplatesModel())->findAll();
        $email_recipients = (new EmailRecipientsModel())->findAll();

        $data = [
            'event'=> $event,
            'email_templates'=>$email_templates,
            'email_recipients'=>$email_recipients
        ];
        return
            view('admin/common/header', $header_data).
            view('admin/mass_emailer',$data).
            view('admin/common/footer')
            ;
    }

    public function get_all_users_filtered() {

        $post = $this->request->getPost();
        $filter = explode(',', $post['selectedOption']);
        $groupFilter = $post['recipientGroup'];
        $recipientType = $post['recipientType'];

        $UsersProfileModel = new UsersProfileModel();
        $UsersModel = new UserModel();
        $PaperAuthorModel = new PaperAuthorsModel();
        $PaperModel = new PapersModel();

        $filteredUsers = array();

        try {
            if($recipientType == 'paper') {
                $baseQuery = $PaperAuthorModel
                    ->select('author_id, paper_id, papers.*')
                    ->where('author_type', 'author')
                    ->join($PaperModel->getTable() . ' AS papers', $PaperAuthorModel->getTable() . '.paper_id = papers.id', 'left')
                    ->whereNotIn('paper_authors.id', function ($builder) {
                        $builder->select('paper_author_id')->from('removed_paper_authors');
                    });

                $this->filterRecipientGroup($groupFilter, $baseQuery);

                if (in_array('all_submitters', $filter)) {
                    // Use Query Builder for the complex query
                    $builder = $PaperModel->builder();
                    $query = $builder->select('users.id as author_id, users.name, users.surname, papers.id as paper_id')
                        ->join('users', 'papers.user_id = users.id', 'left');
                    $this->filterRecipientGroup($groupFilter, $query);

                    // Execute the query
                    $query = $builder->get();

                    // Fetch result as an associative array
                    $user_ids = $query->getResultArray();

//                $filteredUsers.
                    foreach ($user_ids as &$user_id) {
                        $user_id = array_merge($user_id, ['filter' => 'all_submitter']);
                    }
                    unset($user_id); // Break the reference with the last element

                    $filteredUsers = array_merge($filteredUsers, $user_ids);

                }

                $filterConditions = [
                    'presenter_author_only' => ['is_presenting_author' => 'Yes'],
                    'co_author_only' => ['is_coauthor' => 'Yes'],
                    'all_correspondents' => ['paper_authors.is_correspondent' => 'Yes'],
                    'presenting_author_and_correspondents' => ['paper_authors.is_correspondent' => 'Yes'],
                    'all_presenting_authors' => ['paper_authors.is_correspondent' => 'Yes'],
                ];

                foreach ($filter as $option) {
                    if (array_key_exists($option, $filterConditions)) {
                        $query = clone $baseQuery;

                        foreach ($filterConditions[$option] as $field => $value) {
                            $query->where($field, $value);
                        }

                        $user_ids = $query->findAll();
                        foreach ($user_ids as &$user_id) {
                            $user_id = array_merge($user_id, ['filter' => $option]);
                        }
                        unset($user_id); // Break the reference with the last element
                        $filteredUsers = array_merge($filteredUsers, $user_ids);
                    }
                }

                $filtered_result = array();
                foreach ($filteredUsers as $user) {
                    $result = $UsersModel->where('users.id', $user['author_id'])->first();
                    $user['details'] = $result;
                    $filtered_result[] = $user;
                }

                // Sort the merged array by paper_id
                usort($filtered_result, function ($a, $b) {
                    return $a['paper_id'] <=> $b['paper_id']; // Ascending order
                });


                return json_encode(['status' => '200', 'message' => 'success', 'data' => $filtered_result]);
            }else if($recipientType == 'deputy'){
                $filtered_result = $this->allDPC();
                return json_encode(['status' => '200', 'message' => 'success', 'data' => $filtered_result]);
            }else if($recipientType == 'regular'){
                if($groupFilter == 'all_regular') {
                    $filtered_result = $this->allRegular();
                    return json_encode(['status' => '200', 'message' => 'success', 'data' => $filtered_result]);
                }elseif($groupFilter == 'all_regular_incomplete'){
                    $filtered_result = $this->allRegularIncomplete();
                    return json_encode(['status' => '200', 'message' => 'success', 'data' => $filtered_result]);
                }
            }else if($recipientType == 'panel'){
                return $this->recipientPanels();
            }else if($recipientType == 'moderator'){
                return $this->recipientModerators();
            }
        } catch (\Exception $e) {
            return json_encode(['status' => '500', 'message' => $e->getMessage(), 'data' => '']);
        }
    }

    function recipientPanels() {
        $post = $this->request->getPost();
        $filter = explode(',', $post['selectedOption'] ?? '');
        $groupFilter = $post['recipientGroup'] ?? null;

        $UsersModel = new UserModel();
        $PaperAuthorModel = new PaperAuthorsModel();
        $PaperModel = new PapersModel();

        $panelists = array();
        $coordinators = [];

        //select all accepted panelist that is not on the removed paper author. and should be accepted by admin
        if(in_array('all_panelists', $filter)){
            $query = (new PaperAuthorsModel())
                ->select('paper_authors.*, u.name as user_name, u.surname as user_surname, IFNULL(rpa.id, 0) as is_removed, pps.id as panelist_paper_sub_id')
                ->join('users u', 'paper_authors.author_id = u.id', 'left')
                ->join('removed_paper_authors rpa', 'paper_authors.id = rpa.paper_author_id', 'left')
                ->join('panelist_paper_sub pps', 'paper_authors.author_id = pps.panelist_id AND paper_authors.paper_id = pps.paper_id', 'left')
                ->where('paper_authors.author_type', 'panelist')
                ->where('rpa.id', Null)  // Exclude removed panelists
                ->where('pps.id IS NOT NULL');  // Ensure the panelist has a corresponding entry in panelist_paper_sub
            $query = $this->filterRecipientGroupPanel($groupFilter,$query);
            $panelists = $query->get()->getResultArray();
        }
        if(in_array('all_panel_coordinators', $filter)){
            $query = $PaperAuthorModel
                ->select('paper_authors.id, author_id, paper_authors.paper_id, papers.*')
                ->join($PaperModel->getTable() . ' AS papers', 'paper_authors.paper_id = papers.id', 'left')
                ->where('submission_type', 'panel')
                ->where('author_type', 'coordinator')
                ->whereNotIn('paper_authors.id', function ($builder) {
                    $builder->select('paper_author_id')->from('removed_paper_authors');
                })
                ->distinct();
            $query = $this->filterRecipientGroupPanel($groupFilter,$query);
            $coordinators = $query->findAll();
        }

        $filteredResult = array_merge($panelists, $coordinators);

        foreach ($filteredResult as &$user) {
            $result = $UsersModel->where('users.id', $user['author_id'])->first();
            if ($result) {
                $user['details'] = $result;
            }
        }

        // Sort the result by paper_id in ascending order
        usort($filteredResult, fn($a, $b) => $a['paper_id'] <=> $b['paper_id']);

        // Return the response as JSON
        return json_encode(['status' => '200', 'message' => 'success', 'data' => $filteredResult]);
    }




    public function recipientModerators() { // get all moderators assigned once.
        $post = $this->request->getPost();

        $UsersModel = new UserModel();
        $events_result = (new SchedulerModel())->where('is_deleted', 0)->findAll();

        $filtered_result = [];
        $session_chairs = [];

        foreach ($events_result as &$event) {
            $session_chair_ids = json_decode($event['session_chair_ids'], true);

            if (is_array($session_chair_ids) && !empty($session_chair_ids)) {
                foreach ($session_chair_ids as $id) {
                    $session_chairs[] = [
                        'id' => $id,
                        'details' => $UsersModel->find($id) ,// Store moderator details
                        'paper_id' => $event['id']
                    ];
                }
            }
        }
        $filtered_result = array_values($session_chairs);

        // Return the filtered results as JSON
        return json_encode(['status' => '200', 'message' => 'success', 'data' => $filtered_result]);
    }
    function allDPC(){
        $deputyUsers = (new UserModel())->where('is_deputy_reviewer', 1)->findAll();
        $array = array();
        $deputy = array_map(function($e) {
                $array['details'] = $e;
                return $array;
        }, $deputyUsers);

        return $deputy;
    }

    function allRegular(){
        $deputyUsers = (new UserModel())->where('is_regular_reviewer', 1)->findAll();

        $regular = array_map(function($e) {
            $array['details'] = array_merge($e, ['filter' => 'all_regular']);
            $array['filter'] = 'all_regular';
            return $array;
        }, $deputyUsers);

        return $regular;
    }


    function allRegularIncomplete(){
        $AbstractReview = (new AbstractReviewModel());
        $AssignedReviewer = (new PaperAssignedReviewerModel());
        $assignedReviewer = $AssignedReviewer->where(['reviewer_type'=> 'regular', 'is_deleted'=>0, 'is_declined'=>0])
            ->findAll();
        $newArray = array();
        foreach ($assignedReviewer as $reviewer){
            $reviews = $AbstractReview->where(['abstract_id'=>$reviewer['paper_id'], 'reviewer_id'=>$reviewer['reviewer_id']])->first();
            if(!$reviews) {
                $newArray[] = array_merge($reviewer, ['filter' => 'all_regular_incomplete']);
            }
        }

        $incompleteReviews = array();
        foreach ($newArray as $users){
            if($users){
                $users['details'] = (new UserModel())->find($users['reviewer_id']);
                $incompleteReviews[] = $users;
            }
        }
        return $incompleteReviews;
    }

    protected function filterRecipientGroup($groupFilter, &$query) {
        $conditions = [
            'all_papers' => function ($query) {
                $query->where('papers.active_status', '1');
            },
            'incomplete_papers' => function ($query) {
                $query->where('papers.is_finalized', '0');
            },
            'incomplete_participation' => function ($query) {
                $query->where('papers.is_finalized', '0');
            },
            'all_accepted' => function ($query) {
                $query->whereIn('papers.id', function ($builder) {
                    $builder->select('abstract_id')->from('admin_abstract_acceptance')->where('acceptance_confirmation', '1');
                });
            },
            'accepted_presentation' => function ($query) {
                $query->whereIn('papers.id', function ($builder) {
                    $builder->select('abstract_id')->from('admin_abstract_acceptance')
                        ->where('acceptance_confirmation', '1')
                        ->where('presentation_preference', '1');
                });
            },
            'accepted_publication' => function ($query) {
                $query->whereIn('papers.id', function ($builder) {
                    $builder->select('abstract_id')->from('admin_abstract_acceptance')
                        ->where('acceptance_confirmation', '1')
                        ->where('presentation_preference', '2');
                });
            },
            'accepted_presentation_publication' => function ($query) {
                $query->whereIn('papers.id', function ($builder) {
                    $builder->select('abstract_id')->from('admin_abstract_acceptance')
                        ->where('acceptance_confirmation', '1')
                        ->where('presentation_preference', '3');
                });
            },
            'all_rejected' => function ($query) {
                $query->whereIn('papers.id', function ($builder) {
                    $builder->select('abstract_id')->from('admin_abstract_acceptance')
                        ->where('acceptance_confirmation', '2');
                });
            },
        ];

        if (array_key_exists($groupFilter, $conditions)) {
            $conditions[$groupFilter]($query);
        } else {
            throw new \Exception("Invalid group filter: $groupFilter");
        }
        return $query;
    }

    function filterRecipientGroupPanel($groupFilter, &$query) {
        $conditions = [
            'all_panels' => function ($query) {
                $query->groupStart();
                $query->where('paper_authors.author_type', 'panelist');
                $query->orWhere('paper_authors.author_type', 'coordinator');
                $query->groupEnd();
            },
            'all_accepted' => function ($query) {
                $query->whereIn('pps.id', function ($builder) {
                    $builder->select('individual_panel_id')->from('admin_individual_panel_acceptance')->where('acceptance_confirmation', 1);
                });
            },
            'all_incomplete' => function ($query) {
                $query->whereNotIn('pps.id', function ($builder) {
                    $builder->select('individual_panel_id')->from('admin_individual_panel_acceptance');
                });
            },
        ];

        // Apply the condition based on the group filter
        if (array_key_exists($groupFilter, $conditions)) {
            $conditions[$groupFilter]($query);
        } else {
            throw new \Exception("Invalid group filter: $groupFilter");
        }

        return $query;
    }

    function filterRecipientGroupModerators($groupFilter, &$query){
        $conditions = [
            'all_moderators' => function ($query) {
            }
        ];
        if (array_key_exists($groupFilter, $conditions)) {
            $conditions[$groupFilter]($query);
        } else {
            throw new \Exception("Invalid group filter: $groupFilter");
        }

        return $query;
    }

    public function get_preview_email(){ //for authors only, need to modify for all user
        $post = $this->request->getPost();
//        print_R($_FILES['attachments']);exit;
        $UsersProfileModel = new UsersProfileModel();
        $UsersModel = (new UserModel());
        $PaperAuthorModel = (new PaperAuthorsModel());
        $PaperModel = (new PapersModel());
        $ReviewersAssignedModel = (new PaperAssignedReviewerModel());

        $PaperTemplates = $post['message_body'];
        $originalTemplate = $PaperTemplates;


        if(empty($post['recipients'])){
            return json_encode(['status' => 500, 'message' => 'error']);
        }

        $arr = [];
        try {
            if (!empty($post['recipients'])) {
                foreach ($post['recipients'] as $recipient) {
                    if ($post['recipientType'] == 'paper' || $post['recipientType'] == 'panel') {
                        $recipient = json_decode($recipient, true);
                        if ($recipient && $recipient['filter'] !== 'all_submitter') {
                            $PaperAuthorModel->select('*, papers.title as title,  papers.id as paper_id')
                                ->join($PaperModel->getTable() . ' as papers', $PaperAuthorModel->getTable() . '.paper_id = papers.id', 'left')
                                ->join($UsersModel->getTable() . ' as authors', $PaperAuthorModel->getTable() . '.author_id = authors.id', 'left'); // Use 'authors' alias here

                            if (!empty($post['recipientType'])) {
                                $PaperAuthorModel->where('papers.submission_type', $post['recipientType']);
                            }

                            $PaperAuthorModel
                                ->where($PaperAuthorModel->getTable() . '.author_id', $recipient['author_id'])
                                ->where($PaperAuthorModel->getTable() . '.paper_id', $recipient['abstract_id']);

                            $res = $PaperAuthorModel->first();
                        } else {
                            $UsersModel->select('*, papers.title as title, papers.id as paper_id')
                                ->join($PaperModel->getTable() . ' as papers', $UsersModel->getTable() . '.id = papers.user_id', 'left')
                                ->join($UsersProfileModel->getTable() . ' as profile', $PaperModel->getTable() . '.user_id = profile.author_id', 'left')
                                ->join($UsersModel->getTable() . ' as submitters', $PaperModel->getTable() . '.user_id = submitters.id', 'left'); // Use 'submitters' alias here

                            if (!empty($post['recipientType'])) {
                                $UsersModel->where('papers.submission_type', $post['recipientType']);
                            }

                            $UsersModel
                                ->where('submitters.id', $recipient['author_id'])
                                ->where('papers.id', $recipient['abstract_id']);
                            $res = $UsersModel->first();
                        }

                        if (!empty($res)) {
                            $arr[] = $res;
                        }
                    }
                    else if($post['recipientType'] == 'deputy'){
                        $recipient = json_decode($recipient, true);
                        $res = $UsersModel->where('id', $recipient['author_id'])->first();
                        if (!empty($res)) {
                            $arr[] = $res;
                        }
                    }else if($post['recipientType'] == 'regular') {
                        $recipient = json_decode($recipient, true);
                        if($recipient['filter'] == 'all_regular') {
                            $res = $UsersModel->where('id', $recipient['author_id'])->first();
                            if (!empty($res)) {
                                $arr[] = $res;
                            }
                        }
                        else  if($recipient['filter'] == 'all_regular_incomplete') {
                            $res = $UsersModel->where('id', $recipient['author_id'])->first();
                            if (!empty($res)) {
                                $arr[] = $res;
                            }
                        }
                    }
                    else if($post['recipientType'] == 'moderator'){
                        $recipient = json_decode($recipient, true);
                        $res = $UsersModel->where('id', $recipient['author_id'])->first();
                        if (!empty($res)) {
                            $item = $res;
                            $item['details'] = $recipient;
                            $arr[] = $item;
                        }
                    }
                }

            }
        } catch (\Exception $e) {
            error_log($e->getMessage());
            print_r($e->getMessage());
        }


        $email_array = [];

        if($post['recipientType'] == 'paper' || $post['recipientType'] == 'panel' ) {
            foreach ($arr as $val) {
                $PaperTemplates = $originalTemplate;  // Assuming $originalTemplate holds the original template content

                // Get reviewers username and password of this paper
                $reviewers = $ReviewersAssignedModel
                    ->join($UsersModel->getTable() . ' as users', $ReviewersAssignedModel->getTable() . '.reviewer_id = users.id', 'left')
                    ->where('paper_id', $val['paper_id'])
                    ->where('reviewer_type', 'regular')
                    ->findAll();


                $scheduleEvents = (new SchedulerModel())
                    ->select('scheduler_events.*')
                    ->join('scheduler_session_talks', 'scheduler_events.id = scheduler_session_talks.scheduler_event_id', 'left')
                    ->where('scheduler_session_talks.abstract_id', $val['paper_id'])
                    ->first();

                foreach ($reviewers as $reviewer) {
                    $PaperTemplates = str_replace('##REVIEW_USERNAME##', $reviewer['email'], $PaperTemplates);
                    // Note: Password should not be included. Placeholder text used instead.
                    $PaperTemplates = str_replace('##REVIEW_PASSWORD##', 'pass', $PaperTemplates);
                }

                $PaperTemplates = str_replace('##ABSTRACT_ID##', $val['paper_id'], $PaperTemplates);
                $PaperTemplates = str_replace('##ABSTRACT_TITLE##', strip_tags($val['title']), $PaperTemplates);
                $PaperTemplates = str_replace('##RECIPIENTS_FULL_NAME##', $val['name'] . ' ' . $val['surname'], $PaperTemplates);
                $PaperTemplates = str_replace('##RECIPIENT_FIRST_NAME##', $val['name'], $PaperTemplates);
                $PaperTemplates = str_replace('##RECIPIENTS_LAST_NAME##', $val['surname'], $PaperTemplates);
                $PaperTemplates = str_replace('##RECIPIENT_EMAIL_ADDRESS##', $val['email'], $PaperTemplates);

                $PaperTemplates = str_replace('##SCHEDULER_SESSION_TITLE##', $scheduleEvents ?strip_tags($scheduleEvents['session_title']) : '', $PaperTemplates);
                $PaperTemplates = str_replace('##SCHEDULER_SESSION_DATE##', $scheduleEvents ? date('Y-m-d', strtotime($scheduleEvents['session_date'])) : '', $PaperTemplates);
                $PaperTemplates = str_replace('##SCHEDULER_SESSION_START_TIME##', $scheduleEvents ? date('h:i a', strtotime($scheduleEvents['session_start_time'])) : '', $PaperTemplates);
                $PaperTemplates = str_replace('##SCHEDULER_SESSION_END_TIME##', $scheduleEvents  ? date('h:i a', strtotime($scheduleEvents['session_end_time'])) : '', $PaperTemplates);

                // Add more replacements as necessary
                $PaperTemplates = str_replace('##PRESENTATION_DATE##', '', $PaperTemplates);
                $PaperTemplates = str_replace('##PRESENTATION_TIME##', '', $PaperTemplates);
                $PaperTemplates = str_replace('##ADMIN_COMMENTS##', '', $PaperTemplates);
                $PaperTemplates = str_replace('##TODAY_DATE##', '', $PaperTemplates);
                $PaperTemplates = str_replace('##ADMIN_COMMENTS_TO_SUBMITTER##', '', $PaperTemplates);

                // Get presenting authors
                $presentingAuthors = $PaperAuthorModel
                    ->join($UsersModel->getTable() . ' as users', $PaperAuthorModel->getTable() . '.author_id = users.id', 'left')
                    ->where('paper_id', $val['paper_id'])
                    ->where('is_presenting_author', 'Yes')
                    ->findAll();

                foreach ($presentingAuthors as $presenting) {
                    $PaperTemplates = str_replace('##PRESENTING_FULL_NAME##', $presenting['name'] . ' ' . $presenting['surname'], $PaperTemplates);
                    $PaperTemplates = str_replace('##PRESENTING_EMAIL##', $presenting['email'], $PaperTemplates);
                    $PaperTemplates = str_replace('##PRESENTING_PREFIX##', $presenting['prefix'], $PaperTemplates);
                }

                $email_entry = [
                    'name' => stripslashes($val['name']),
                    'surname' => stripslashes($val['surname']),
                    'email_template' => stripslashes($PaperTemplates),
                    'email' => $val['email'],
                    'subject' => $post['email_subject'],
                    'paper_id' => $val['paper_id'],
                    'author_id' => $val['author_id']
                ];

                $email_array[] = $email_entry;
            }
        }else if($post['recipientType'] == 'deputy' || $post['recipientType'] == 'regular'  ){
            foreach ($arr as $val) {

                $PaperTemplates = $originalTemplate;  // Assuming $originalTemplate holds the original template content/**/

                // Get reviewers username and password of this paper
                $users = $UsersModel->where('id', $val['id'])->findAll();

                foreach ($users as $user) {
                    $PaperTemplates = str_replace('##REVIEW_USERNAME##', $user['email'], $PaperTemplates);
                    // Note: Password should not be included. Placeholder text used instead.
                    $PaperTemplates = str_replace('##REVIEW_PASSWORD##', 'pass', $PaperTemplates);
                }

                $PaperTemplates = str_replace('##RECIPIENTS_FULL_NAME##', $val['name'] . ' ' . $val['surname'], $PaperTemplates);
                $PaperTemplates = str_replace('##TODAY_DATE##', date('Y-m-d'), $PaperTemplates);
                $PaperTemplates = str_replace('##RECIPIENT_FIRST_NAME##', $val['name'], $PaperTemplates);
                $PaperTemplates = str_replace('##RECIPIENTS_LAST_NAME##', $val['surname'], $PaperTemplates);
                $PaperTemplates = str_replace('##RECIPIENT_EMAIL_ADDRESS##', $val['email'], $PaperTemplates);

                // Add more replacements as necessary
                $PaperTemplates = str_replace('##PRESENTATION_DATE##', '', $PaperTemplates);
                $PaperTemplates = str_replace('##PRESENTATION_TIME##', '', $PaperTemplates);
                $PaperTemplates = str_replace('##TODAY_DATE##', date('Y-m-d'), $PaperTemplates);
                // Get presenting authors

                $email_entry = [
                    'name' => stripslashes($val['name']),
                    'surname' => stripslashes($val['surname']),
                    'email_template' => stripslashes($PaperTemplates),
                    'email' => $val['email'],
                    'subject' => $post['email_subject'],
                    'paper_id' => '',
                    'author_id' => $val['id']
                ];

                $email_array[] = $email_entry;
            }

        }else if ($post['recipientType'] == 'moderator') {
            $moderators = (new UserModel())->get_moderator_ids();
            $secondArray = $moderators;
            $firstArray = $arr;

            $validAuthorIds = array_column(array_column($firstArray, 'details'), 'author_id');
            $validAbstractIds = array_column(array_column($firstArray, 'details'), 'abstract_id');

            $filteredModerators = array_filter($secondArray, function ($moderator) use ($validAuthorIds, $validAbstractIds) {
                return in_array($moderator['user']['id'], $validAuthorIds) &&
                    in_array($moderator['event']['id'], $validAbstractIds);
            });

            // Process each moderator
            foreach ($filteredModerators as $moderator_event) {
                $PaperTemplates = $originalTemplate; // Assuming $originalTemplate holds the original email template

                $PaperTemplates = str_replace('##RECIPIENTS_FULL_NAME##', $moderator_event['user']['name'] . ' ' . $moderator_event['user']['surname'], $PaperTemplates);
                $PaperTemplates = str_replace('##TODAY_DATE##', date('Y-m-d'), $PaperTemplates);
                $PaperTemplates = str_replace('##RECIPIENT_FIRST_NAME##', $moderator_event['user']['name'], $PaperTemplates);
                $PaperTemplates = str_replace('##RECIPIENTS_LAST_NAME##', $moderator_event['user']['surname'], $PaperTemplates);
                $PaperTemplates = str_replace('##RECIPIENT_EMAIL_ADDRESS##', $moderator_event['user']['email'], $PaperTemplates);

                // Assuming the event contains session details for the moderator
                $PaperTemplates = str_replace('##SCHEDULER_SESSION_TITLE##', isset($moderator_event['event']['session_title']) ? strip_tags($moderator_event['event']['session_title']) : '', $PaperTemplates);
                $PaperTemplates = str_replace('##SCHEDULER_SESSION_DATE##', isset($moderator_event['event']['session_date']) ? date('Y-m-d', strtotime($moderator_event['event']['session_date'])) : '', $PaperTemplates);
                $PaperTemplates = str_replace('##SCHEDULER_SESSION_START_TIME##', isset($moderator_event['event']['session_start_time']) ? date('h:i a', strtotime($moderator_event['event']['session_start_time'])) : '', $PaperTemplates);
                $PaperTemplates = str_replace('##SCHEDULER_SESSION_END_TIME##', isset($moderator_event['event']['session_end_time']) ? date('h:i a', strtotime($moderator_event['event']['session_end_time'])) : '', $PaperTemplates);
                $PaperTemplates = str_replace('##SCHEDULER_SESSION_ROOM##', isset($moderator_event['event']['room_name']) ? $moderator_event['room_name'] : '', $PaperTemplates);

                // Prepare email entry
                $email_entry = [
                    'name' => stripslashes($moderator_event['user']['name']),
                    'surname' => stripslashes($moderator_event['user']['surname']),
                    'email_template' => stripslashes($PaperTemplates),
                    'email' => $moderator_event['user']['email'],
                    'subject' => $post['email_subject'],
                    'paper_id' => '',
                    'author_id' => $moderator_event['user']['id']
                ];

                $email_array[] = $email_entry;
            }
        }

        if($post['action'] == 'preview') {
            return json_encode(['status' => 200, 'message' => 'success', 'data' => $email_array]);
        }else{
            $mailResult = $this->sendMassMail($email_array, $PaperTemplates, $post, ($_FILES['attachments'] ?? null));
            return json_encode(['status' => 200, 'message' => 'success', 'data' => $email_array]);
        }

    }

    public function sendMassMail($email_array, $PaperTemplates, $post, $attachments = null){
        helper('text');
        $sendMail = new PhpMail();
        $from['email']= 'afs@owpm2.com';
        $from['name']= 'AFS';

        $unique_code = random_string('alnum', 30);

        $test_mode = $post['test_mode'];
        $test_email = $post['test_email_to'];
        $test_email_only = $post['test_email_only'];
        $emailCount = count($email_array);
//        print_R($email_array);exit;


        try {
            if ($test_mode == 'on') {
                if ($test_email_only == 'on') {
                    $result = $sendMail->send($from, $test_email, $email_array[0]['subject'], $email_array[0]['email_template'], $attachments);
                    $this->do_save_mail_log($result, $email_array[0], $post, $PaperTemplates, $unique_code, $emailCount);
                } else {
                    foreach ($email_array as $email) {
                        $result = $sendMail->send($from, $test_email, $email['subject'], $email['email_template'], $attachments);
                        $this->do_save_mail_log($result, $email, $post, $PaperTemplates, $unique_code, $emailCount);
                    }
                }
            } else {
                foreach ($email_array as $email) {
                    $result = $sendMail->send($from, $email['email'], $email['subject'], $email['email_template'], $attachments);
                    $this->do_save_mail_log($result, $email, $post, $PaperTemplates, $unique_code, $emailCount);
                }
            }

        }catch (\Exception $e){
            return $e->getMessage();
        }
    }

    function do_save_mail_log($result, $email_array, $post, $PaperTemplates, $unique_code, $emailCount){
        // Determine the email log status based on the mail result
        $status = ($result->statusCode == 200) ? 'Success' : 'Failed';

        $emailLogsModel = new EmailLogsModel();

            // Prepare the email log data
            $email_logs_array = [
                'user_id' => session('user_id'),
                'unique_code' => $unique_code,
                'add_to' => $email_array['email'],
                'subject' => $post['email_subject'],
                'ref_1' => 'mass_mailer',
                'add_content' => stripslashes($PaperTemplates),
                'send_from' => "Admin",
                'send_to' => $email_array['author_id'],
                'level' => "Info",
                'template_id' => $post['template_id'],
                'is_test' => ($post['test_mode'] == 'on' ? 1:0),
                'recipient_type' => $post['recipientType'],
                'recipient_group' => $post['recipientGroup'],
                'total_recipients' => $emailCount ?: 0,
                'paper_id' => $email_array['paper_id'],
                'user_agent' => $this->request->getUserAgent()->getBrowser(),
                'ip_address' => $this->request->getIPAddress(),
                'status' => $status,  // Set the status based on the mail result
            ];

            // Save the email log
            $emailLogsModel->saveToMailLogs($email_logs_array);

        return $result;
    }

    function replaceTemplatesKeyWords($old, $new, $PaperTemplates) {
      return  str_replace($old, $new, $PaperTemplates);
    }

    function getAllPapersDetailsBySubmitter($user_id){
        $PapersModel = new PapersModel();
        $papers = (object) $PapersModel->where('papers.user_id', $user_id)->findAll();
        $PaperAssignedReviewerModel = new PaperAssignedReviewerModel();
        $paper_array = [];

        try {
            foreach ($papers as $paper) {
                $user_array = [];
                $reviewer_array = [];
                $paper->authors = (new AbstractController())->getPaperAuthors($paper->id)->getResult();

                foreach ($paper->authors as $user) {
                    $user->details = (new UsersProfileModel())->where('author_id', $user->author_id)->first();
                    if (!empty($user->details))
                        $user_array[] = $user;
                }

                $paper->authors = $user_array;

                $paper->reviewers = $PaperAssignedReviewerModel->where('paper_id', $paper->id)->findAll();
                if (!empty($paper->reviewers)) {
                    foreach ($paper->reviewers as $reviewer) {
                        $reviewer['details'] = (new UsersProfileModel())->where('author_id', $reviewer['id'])->first();
                        $reviewer_array[] = $reviewer;
                    }
                }

                $paper->reviewers = $reviewer_array;
                $paperType = (new PaperTypeModel())->where('id', ($paper->type_id))->first();
                $paperDivision = (new DivisionsModel())->where('id', ($paper->division_id))->first();
                $paper->division = ($paperDivision) ? $paperDivision : [];
                $paper->type = ($paperType) ? $paperType : [];
                $paper_array[] = $paper;
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return $paper_array;
    }

    function sendCustomEmailReviewer($emailTemplateId,  $reviewer_id, $abstract_id, $abstract_title){

        $sendMail = new PhpMail();
        $EmailTemplates = (new EmailTemplatesModel());
        $EmailTemplates = $EmailTemplates->find($emailTemplateId);

//        print_r($EmailTemplates);exit;

        $user = (new UserModel())->find($reviewer_id);

        $email_body = $EmailTemplates['email_body'];
        $email_body = str_replace('##ABSTRACT_ID##', $abstract_id, $email_body);
        $email_body = str_replace('##RECIPIENTS_FULL_NAME##', ucFirst($user['name']).' '.ucFirst($user['surname']), $email_body);
        $email_body = str_replace('##REVIEW_USERNAME##', ($user['email']), $email_body);
        $email_body = str_replace('##REVIEW_PASSWORD##', 'Please reset your password in case forgotten. Thank you!', $email_body);

        $from = ['name'=>'AFS', 'email'=>'afs@owpm2.com'];
        $addTo = $user['email'];
//        $addTo = "rexterdayuta@gmail.com"; //todo: fetch all reviewers who dont have reviews yet

        $subject = $EmailTemplates['email_subject'];
        $addContent = $email_body;

        $mailResult = $sendMail->send($from, $addTo, $subject, $addContent);

        // ###################  Save to Email logs #####################
        $email_logs_array = [
            'user_id' => session('user_id'),
            'add_to' => ($addTo),
            'subject' => $subject,
            'ref_1' => 'mailer_custom_email',
            'add_content' => $addContent,
            'send_from' => "Admin",
            'send_to' => "",
            'level' => "Info",
            'template_id' => $EmailTemplates['id'],
            'paper_id' => $abstract_id,
            'user_agent' => $this->request->getUserAgent()->getBrowser(),
            'ip_address' => $this->request->getIPAddress(),
        ];

        if($mailResult->statusCode == 200) {
            $email_logs_array['status'] = 'Success';
            $emailLogsModel = (new EmailLogsModel())->saveToMailLogs($email_logs_array);
        }else{
            $email_logs_array['status'] = 'Failed';
            $emailLogsModel = (new EmailLogsModel())->saveToMailLogs($email_logs_array);
        }


    }


    public function getAllEmailLogs($unique_code) {
        $request = $this->request;
        $EmailLogsModel = new EmailLogsModel();

        $start = $request->getPost('start');
        $length = $request->getPost('length');
        $searchValue = $request->getPost('search')['value'];
        $orderColumn = $request->getPost('order')[0]['column'];
        $orderDir = $request->getPost('order')[0]['dir'];

        $query = $EmailLogsModel;

// Search filter
        if (!empty($searchValue)) {
            $query = $query->like('id', $searchValue)
                ->orLike('user_id', $searchValue)
                ->orLike('add_to', $searchValue)
                ->orLike('ref_1', $searchValue)
                ->orLike('subject', $searchValue)
                ->orLike('status', $searchValue)
                ->orLike('send_from', $searchValue)
                ->orLike('send_to', $searchValue)
                ->orLike('template_id', $searchValue)
                ->orLike('paper_id', $searchValue)
                ->orLike('created_at', $searchValue);
        }

        $query->where('unique_code', $unique_code);
// Get the total number of filtered records (before pagination)
        $totalFilteredRecords = $query->countAllResults(false); // Without resetting

// Apply ordering and pagination for the actual data retrieval
        $query = $query->orderBy($orderColumn, $orderDir)
            ->findAll($length, $start);

// Total number of records in the table (without filtering)
        $totalRecords = $EmailLogsModel->countAll();

// Prepare the data for DataTables
        $data = [];
        foreach ($query as $log) {
            $data[] = [
                $log['id'],
                $log['user_id'],
                $log['add_to'],
                $log['ref_1'],
                $log['subject'],
                $log['status'],
                $log['send_from'],
                $log['send_to'],
                $log['template_id'],
                $log['paper_id'],
                $log['created_at']
            ];
        }

// Return the response to DataTables
        $response = [
            'draw' => intval($request->getPost('draw')),
            'recordsTotal' => $totalRecords,         // Total number of records in the database
            'recordsFiltered' => $totalFilteredRecords,  // Total number of filtered records
            'data' => $data                           // Paginated data
        ];

        return $this->response->setJSON($response);

    }

    public function getGroupEmailLogs() {
        $request = $this->request;
        $EmailLogsModel = new EmailLogsModel();

        // Retrieve input values
        $start = $request->getPost('start');
        $length = $request->getPost('length');
        $searchValue = $request->getPost('search')['value'];
        $orderColumn = $request->getPost('order')[0]['column'];
        $orderDir = $request->getPost('order')[0]['dir'];

        // Base query
        $query = $EmailLogsModel;

        // Apply search filter
        if (!empty($searchValue)) {
            $query = $query->like('id', $searchValue)
                ->orLike('user_id', $searchValue)
                ->orLike('add_to', $searchValue)
                ->orLike('ref_1', $searchValue)
                ->orLike('subject', $searchValue)
                ->orLike('status', $searchValue)
                ->orLike('send_from', $searchValue)
                ->orLike('send_to', $searchValue)
                ->orLike('template_id', $searchValue)
                ->orLike('paper_id', $searchValue)
                ->orLike('created_at', $searchValue);
        }

        // Clone the query to get counts
        $countQuery = clone $query;
        $countQuery = $countQuery->select('unique_code, COUNT(*) as group_count')
            ->groupBy('unique_code')
            ->findAll();

        // Store group counts with 'created_at' as the key
        $groupCounts = [];
        foreach ($countQuery as $group) {
            $groupCounts[$group['unique_code']] = $group['group_count'];
        }

        // Clone the query to get success counts
        $successCountQuery = clone $query;
        $successCountQuery = $successCountQuery->select('unique_code, SUM(CASE WHEN LOWER(status) = "success" THEN 1 ELSE 0 END) as success_count')
            ->groupBy('unique_code')
            ->findAll();

        // Store success counts with 'created_at' as the key
        $successCounts = [];
        foreach ($successCountQuery as $group) {
            $successCounts[$group['unique_code']] = $group['success_count'];
        }

        // Reset the original query for the main data retrieval
        $query = $query->groupBy('unique_code')
            ->orderBy('created_at', 'desc');

        // Get the total number of filtered records (before pagination)
        $totalFilteredRecords = $query->countAllResults(false);

        // Apply ordering and pagination for the actual data retrieval
        $query = $query->orderBy($orderColumn, $orderDir)
            ->findAll($length, $start);

        // Total number of records in the table (without filtering)
        $totalRecords = $EmailLogsModel->countAll();

        // Prepare the data for DataTables
        $data = [];
        foreach ($query as $log) {
            $unique_code = $log['unique_code'];
            $groupCount = isset($groupCounts[$unique_code]) ? $groupCounts[$unique_code] : 0;
            $successCount = isset($successCounts[$unique_code]) ? $successCounts[$unique_code] : 0;
            $data[] = [
                $log['recipient_type'],
                $log['recipient_group'],
                ($log['is_test'] == 1 ? "<span class='text-danger'>TEST</span>" : ""),
                $log['ref_1'],
                $log['subject'],
                $log['status'],
                $log['template_id'],
                $log['created_at'],
                $groupCount,       // Total count for the group
                $successCount.'/'. $log['total_recipients'],     // Success count for the group
                $log['unique_code']
            ];
        }

        // Return the response to DataTables
        $response = [
            'draw' => intval($request->getPost('draw')),
            'recordsTotal' => $totalRecords,         // Total number of records in the database
            'recordsFiltered' => $totalFilteredRecords,  // Total number of filtered records
            'data' => $data                           // Paginated data
        ];

        return $this->response->setJSON($response);
    }

    public function group_email_logs(){

        $event = (new EventsModel())->first();
        if(!$event){
            return 'error';
        }

        $header_data = [
            'title' => $event->short_name
        ];


        $data = [
            'event'=> $event
        ];
        return
            view('admin/common/header', $header_data).
            view('admin/group_mail_logs',$data).
            view('admin/common/footer')
            ;
    }

    public function email_logs($unique_code){

        $event = (new EventsModel())->first();
        if(!$event){
            return 'error';
        }

        $header_data = [
            'title' => $event->short_name
        ];

        $data = [
            'event'=> $event,
            'unique_code'=>$unique_code
        ];
        return
            view('admin/common/header', $header_data).
            view('admin/email_logs',$data).
            view('admin/common/footer')
            ;
    }


}