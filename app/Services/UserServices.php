<?php

namespace App\Services;


use App\Models\AppDisclosureModel;
use App\Models\CitiesModel;
use App\Models\CMEReviewersModel;
use App\Models\CountriesModel;
use App\Models\DesignationsModel;
use App\Models\DivisionsModel;
use App\Models\InstitutionModel;
use App\Models\PaperAuthorsModel;
use App\Models\SchedulerModel;
use App\Models\SiteSettingModel;
use App\Models\StatesModel;
use App\Models\UserModel;
use App\Models\UsersProfileModel;
use CodeIgniter\Config\BaseService;

class UserServices extends BaseService
{

    private $request;
    public function __construct() {

        $this->request = \Config\Services::request();

        $this->model = model(UserModel::class);
        $this->profileModel = model(UsersProfileModel::class);
        $this->cmeModel = model(CMEReviewersModel::class);
        $this->divisionsModel = model(DivisionsModel::class);
        $this->institutionsModel = model(InstitutionModel::class);
        $this->citiesModel = model(CitiesModel::class);
        $this->countriesModel = model(CountriesModel::class);
        $this->statesModel = model(StatesModel::class);
        $this->designationsModel = model(DesignationsModel::class);
        $this->appDisclosureModel = model(AppDisclosureModel::class);
        $this->paperAuthorsModel = model(PaperAuthorsModel::class);
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

    private function getUsersQuery($searchFields = null, $filters = null): object{
        $query = $this->model->db->table('users_profile up')
            ->select('
                up.*, 
                u.id as user_id,
                u.email, 
                u.surname, 
                u.name, 
                u.middle_name, 
                u.is_study_group,
                u.is_regular_reviewer,
                u.is_deputy_reviewer,
                u.is_session_moderator,
                u.created_at,
                u.updated_at,
                u.deleted_at,
                up.other_designation
            ')
            ->join($this->model->database.'.users u','up.author_id = u.id', 'right');

        if(!empty($searchFields) && in_array('institutions', $searchFields)) {
            $this->joinInstitutions($query);
        }

        if(!empty($searchFields) && in_array('divisions', $searchFields)) {
            $this->joinDivisions($query);
        }

        if (!empty($searchFields)  && in_array('designations', $searchFields)) {
            $this->joinDesignations($query);
        }

        if (!empty($searchFields)  && in_array('cme', $searchFields)) {
            $this->joinCme($query);
        }

        if (!empty($searchFields)  && in_array('app_disclosures', $searchFields)) {
            $this->joinAppDisclosure($query);
        }

        if (!empty($searchFields)  && in_array('paper_authors', $searchFields)) {
            $this->joinPaperAuthors($query);
        }

        if(!empty($filters['order_by'])) {
            $query->orderBy($filters['order_by']['field'], $filters['order_by']['direction']);
        }


        $query->where('u.deleted_at', null);

        return $query;
    }

    function joinCme($query): object{
        return $query->join($this->cmeModel->db->database .'.cme_reviewers cme', 'u.id = cme.cme_reviewer_id', 'left')
            ->select('cme.id as cme_reviewer_id, cme.deleted_at as cme_reviewer_deleted_at');
    }

    function joinDesignations($query): object{
        $query->join(
            $this->designationsModel->db->database . '.divisions as des',
            'CONCAT(",", REPLACE(REPLACE(REPLACE(up.division_id, "[", ""), "]", ""), "\"", ""), ",") LIKE CONCAT("%,", des.id, ",%")',
            'left'
        )
            ->select('des.id as division_id, des.name as division_name');
        return $query;
    }

    function joinDivisions($query): object{
        $query->join($this->divisionsModel->db->database . '.divisions d', 'up.division_id = d.id', 'left')
            ->select('d.id as division_id, d.name as division_name');

        return $query;
    }

    function joinInstitutions($query): object{
        $query->join($this->institutionsModel->db->database . '.institution i', 'up.institution_id = i.id', 'left')
            ->join($this->citiesModel->db->database . '.cities ci', 'i.city_id = ci.id', 'left')
            ->join($this->statesModel->db->database . '.states s', 'ci.state_id = s.id', 'left')
            ->join($this->countriesModel->db->database . '.countries co', 'ci.country_id = co.id', 'left')
            ->select('i.name as institution_name, 
            ci.name as institution_city, 
            co.name as institution_country,
            s.name as institution_state');

        return $query;
    }

    function joinAppDisclosure($query){
        $query->join($this->appDisclosureModel->db->database . '.app_disclosures ad', 'u.id = ad.author_id', 'left')
            ->select('ad.financial_relationship, ad.disclosure_signature, ad.created_at as app_disclosure_created_at,
             ad.updated_at as app_disclosure_update_at, ad.id as app_disclosure_id');

        return $query;
    }

    function joinPaperAuthors($query){
        $query->join($this->paperAuthorsModel->db->database . '.paper_authors pa', 'pa.author_id = u.id', 'left')
            ->select('pa.paper_id as paper_author_id');

        return $query;
    }

    function searchUserByName($post, $searchFields = null, $filters = null): array{

        $query = $this->getUsersQuery($post, $searchFields, $filters);
        $query->groupStart();
        $query->like('LOWER(u.surname)', strtolower($post['searchValue']['authorName']));
        $query->orlike('LOWER(u.name)', strtolower($post['searchValue']['authorName']));
        $query->groupEnd();
        return $query->get()->getResultArray();
    }

}