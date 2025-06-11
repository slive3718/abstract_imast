<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Controllers\User;

use App\Models\AbstractReviewModel;
use App\Models\CitiesModel;
use App\Models\DesignationsModel;
use App\Models\DivisionsModel;
use App\Models\EmailLogsModel;
use App\Models\InstitutionModel;
use App\Models\PaperAssignedReviewerModel;
use App\Models\PapersModel;
use App\Models\UserModel;
use App\Models\UsersProfileModel;

use App\Controllers\ExcelController;
use CodeIgniter\Controller;

class UserManagerController extends Controller
{

    protected $helpers = ['form'];
    private $db;
    public function __construct()
    {

        $this->db = \Config\Database::connect();
    }

    public function index(){


    }

    public function importReviewers()
    {
        $file = $this->request->getFile('reviewerImportFile');

        $UserModel = new UserModel();
        $UserProfileModel = new UsersProfileModel();
        $DivisionModel = new DivisionsModel();
        $duplicate = [];
        $insertedCount = 0;
        $updatedCount = 0;
        $count = 0;

        session()->set('import_progress', 0);
        // Check if file is uploaded successfully
        try {
            if ($file->isValid() && ($file->getExtension() === 'xlsx' || $file->getExtension() === 'xls')) {
                // Load necessary libraries/helpers
                helper('excel');

                // Load the Excel file
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);

                // Get the first worksheet
                $worksheet = $spreadsheet->getActiveSheet();
                $totalRows = $worksheet->getHighestDataRow();

                // Iterate through rows
                foreach ($worksheet->getRowIterator() as $index => $row) {
                    if ($index === 1) {
                        continue; // Skip header row
                    }

                    $count++;
                    $cellValue = []; // Reset cell values for each row

                    // Iterate through cells in the row
                    foreach ($row->getCellIterator() as $cell) {
                        $cellValue[] = $cell->getValue();
                    }

                    // Assuming the order of fields in the Excel file is: First Name, Last Name, User, Password, E-Mail, Company, Division
                    $importData = [
                        'name' => $cellValue[0],
                        'surname' => $cellValue[1],
                        'username' => $cellValue[2],
                        'password' => password_hash($cellValue[3], PASSWORD_DEFAULT),
                        'email' => $cellValue[4]
                    ];


                    $division = $DivisionModel->like('LOWER(name)', strtolower(trim($cellValue[6])))->first();
                    $division_id = (!empty($division->division_id) ? $division->division_id : '');

                    $user = $UserModel->where('email', trim($importData['email']))->first();

                    if (empty($user)) {
                        // Insert new user
                        $insertedCount++;
                        $user_id = $UserModel->insert($importData);

                        if ($user_id) {
                            $profileData = $this->createProfileData($user_id, $cellValue[5], $division_id);
                            $UserProfileModel->insert($profileData);
                        }
                    } else {
                        // Update existing user
                        $updatedCount++;
                        $user_id = $user['id'];

                        // Update main user data
                        $UserModel->update($user_id, $importData);

                        // Update or create profile data
                        $existingProfile = $UserProfileModel->where('author_id', $user_id)->first();
                        $profileData = $this->updateProfileData($existingProfile, $cellValue[5], $division_id);
                        if ($existingProfile) {
                            $UserProfileModel->update($existingProfile['id'], $profileData);
                        } else {
                            $UserProfileModel->insert($profileData);
                        }

                        // Update reviewer roles
                        $reviewerData = $this->setReviewerRole([], trim($cellValue[7]));
                        if (!empty($reviewerData)) {
                            $UserModel->update($user_id, $reviewerData);
                        }
                    }

                    $progress = ($count / $totalRows) * 100;
                    session()->set('import_progress', $progress);
                }

                return json_encode(['status' => 200, 'message' => "Reviewers imported successfully! Inserted Count: " . $insertedCount . " Updated Count: " . $updatedCount, 'data' => '']);
            } else {
                return json_encode(['status' => 500, 'message' => "Invalid file format. Please upload a valid Excel file.", 'data' => '']);
            }
        } catch (\Exception $e) {
            return json_encode(['status' => 500, 'message' => $e->getMessage(), 'data' => '']);
        }
    }

    public function importUsers()
    {
        $file = $this->request->getFile('user_import_file');
        $UserModel = new UserModel();
        $UserProfileModel = new UsersProfileModel();

        $stats = [
            'total' => 0,
            'users_created' => 0,
            'users_updated' => 0,
            'profiles_created' => 0,
            'profiles_updated' => 0,
            'skipped' => 0
        ];

        session()->set('import_progress', 0);
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            if ($file->isValid() && in_array($file->getExtension(), ['xlsx', 'xls'])) {
                helper('excel');
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
                $worksheet = $spreadsheet->getActiveSheet();

                foreach ($worksheet->getRowIterator() as $index => $row) {
                    if ($index === 1) continue; // Skip header

                    $stats['total']++;
                    $cellValues = $this->extractCellValues($row);

                    // Skip if no email (but track it)
                    if (empty($cellValues[3])) {
                        $stats['skipped']++;
                        continue;
                    }

                    // Process each user with enforced profile
                    $this->processUserWithEnforcedProfile(
                        $UserModel,
                        $UserProfileModel,
                        $cellValues,
                        $stats
                    );

                    session()->set('import_progress', ($index / $worksheet->getHighestRow()) * 100);
                }

                // Final verification
                $this->verifyCountsMatch($stats);
                $db->transComplete();

                return $this->buildSuccessResponse($stats);
            }
            throw new \RuntimeException("Invalid file format");
        } catch (\Exception $e) {
            $db->transRollback();
            return $this->buildErrorResponse($e, $stats);
        }
    }

    private function processUserWithEnforcedProfile($UserModel, $UserProfileModel, $cellValues, &$stats)
    {
        $email = trim($cellValues[3]);
        $userData = $this->prepareUserData($cellValues);
        $user = $UserModel->where('email', $email)->first();

        try {
            if (empty($user)) {
                // CREATE NEW USER + PROFILE
                $user_id = $UserModel->insert($userData);
                if (!$user_id) throw new \RuntimeException("User creation failed");

                $stats['users_created']++;
                $this->createEnforcedProfile($UserProfileModel, $user_id, $cellValues);
                $stats['profiles_created']++;
            } else {
                // UPDATE EXISTING USER + PROFILE
                $user_id = $user['id'];
                $UserModel->update($user_id, $userData);
                $stats['users_updated']++;

                $profile = $UserProfileModel->where('author_id', $user_id)->first();
                if ($profile) {
                    $this->updateProfile($UserProfileModel, $profile['id'], $cellValues);
                    $stats['profiles_updated']++;
                } else {
                    $this->createEnforcedProfile($UserProfileModel, $user_id, $cellValues);
                    $stats['profiles_created']++;
                }
            }
        } catch (\Exception $e) {
            log_message('error', "Failed processing {$email}: " . $e->getMessage());
            throw $e; // Re-throw to trigger rollback
        }
    }

    private function createEnforcedProfile($UserProfileModel, $user_id, $cellValues)
    {
        $profileData = [
            'author_id' => $user_id,
            'institution_id' => $this->safeResolveInstitution($cellValues),
            'designations' => $this->safeSetDesignations($cellValues[6] ?? ''),
            'other_designation' => $cellValues[7] ?? NULL,
            'phone' => $cellValues[8] ?? NULL,
            'cellphone' => $cellValues[9] ?? NULL
        ];

        if (!$UserProfileModel->insert($profileData)) {
            throw new \RuntimeException("Profile creation failed for user {$user_id}");
        }
    }

    private function updateProfile($UserProfileModel, $profile_id, $cellValues)
    {
        $updateData = [
            'institution_id' => $this->safeResolveInstitution($cellValues),
            'designations' => $this->safeSetDesignations($cellValues[6] ?? ''),
            'other_designation' => $cellValues[7] ?? NULL,
            'phone' => $cellValues[8] ?? NULL,
            'cellphone' => $cellValues[9] ?? NULL
        ];

        if (!$UserProfileModel->update($profile_id, $updateData)) {
            throw new \RuntimeException("Profile update failed");
        }
    }

// Safe versions that return NULL instead of failing
    private function safeResolveInstitution($cellValues)
    {
        try {
            return $this->resolveInstitution($cellValues);
        } catch (\Exception $e) {
            log_message('error', "Institution resolution failed: " . $e->getMessage());
            return NULL;
        }
    }

    private function safeSetDesignations($designation_cell)
    {
        try {
            return $this->set_designations($designation_cell);
        } catch (\Exception $e) {
            log_message('error', "Designations failed: " . $e->getMessage());
            return NULL;
        }
    }

    private function verifyCountsMatch($stats)
    {
        $total_users = $stats['users_created'] + $stats['users_updated'];
        $total_profiles = $stats['profiles_created'] + $stats['profiles_updated'];

        if ($total_users !== $total_profiles) {
            throw new \RuntimeException(
                "Count mismatch: Users ({$total_users}) ≠ Profiles ({$total_profiles})"
            );
        }
    }

    private function resolveInstitution($cellValues)
    {
        $InstitutionModel = new InstitutionModel();
        $cityModel = new CitiesModel();

        $institutionName = trim($cellValues[10] ?? '');
        $cityName = trim($cellValues[11] ?? '');
        $countryName = trim($cellValues[12] ?? '');

        // Return NULL if no institution name provided
        if (empty($institutionName)) {
            return NULL;
        }

        // Try to find existing institution
        $institution = $InstitutionModel
            ->where("REPLACE(LOWER(name), ' ', '')", strtolower(str_replace(' ', '', $institutionName)))
            ->first();

        if ($institution) {
            return $institution['id'];
        }

        // Find matching city and country
        $city = $cityModel
            ->select('cities.*')
            ->join('countries', 'countries.id = cities.country_id')
            ->where("REPLACE(LOWER(cities.name), ' ', '')", strtolower(str_replace(' ', '', $cityName)))
            ->where("REPLACE(LOWER(countries.name), ' ', '')", strtolower(str_replace(' ', '', $countryName)))
            ->first();

        // Only create institution if city/country was found
        if ($city) {
            $institutionData = [
                'name' => $institutionName,
                'country_id' => $city['country_id'],
                'state_id' => $city['state_id'],
                'city_id' => $city['id']
            ];

            $institutionId = $InstitutionModel->insert($institutionData);
            return $institutionId ?: NULL;
        }

        return NULL; // Return NULL if city/country not found
    }

    private function set_designations($designation_cell)
    {
        if (empty($designation_cell)) {
            return NULL;
        }

        $designationModel = new DesignationsModel();
        $designations = array_filter(array_map('trim', explode(',', $designation_cell)));
        $designationIds = [];

        foreach ($designations as $designation) {
            $result = $designationModel->where('LOWER(name)', strtolower($designation))->first();
            if ($result) {
                $designationIds[] = $result['id'];
            }
        }

        return !empty($designationIds) ? json_encode($designationIds) : NULL;
    }

    private function setReviewerRole($importData, $role)
    {
        $role = strtolower(trim($role ?? ''));

        if ($role === 'program chair') {
            $importData['is_deputy_reviewer'] = 1;
        } elseif ($role === 'reviewer') {
            $importData['is_regular_reviewer'] = 1;
        }

        return $importData;
    }

    private function extractCellValues($row)
    {
        $cellValues = [];
        foreach ($row->getCellIterator() as $cell) {
            $cellValues[] = $cell->getFormattedValue(); // Gets the displayed value
        }
        return $cellValues;
    }

    private function prepareUserData($cellValues)
    {
        return [
            'name'        => trim($cellValues[0] ?? ''),
            'middle_name' => trim($cellValues[1] ?? ''),
            'surname'     => trim($cellValues[2] ?? ''),
            'email'       => trim($cellValues[3] ?? ''),
            'username'    => trim($cellValues[4] ?? ''),
            'password'    => !empty($cellValues[5]) ? password_hash(trim($cellValues[5]), PASSWORD_DEFAULT) : '',
        ];
    }

    private function buildSuccessResponse($stats)
    {
        return $this->response->setJSON([
            'status' => 200,
            'message' => sprintf(
                "Import complete. Users: %d (%d new, %d updated). Profiles: %d (%d new, %d updated). Skipped: %d",
                $stats['users_created'] + $stats['users_updated'],
                $stats['users_created'],
                $stats['users_updated'],
                $stats['profiles_created'] + $stats['profiles_updated'],
                $stats['profiles_created'],
                $stats['profiles_updated'],
                $stats['skipped']
            ),
            'data' => $stats
        ]);
    }

    private function buildErrorResponse($e, $stats)
    {
        log_message('error', 'Import failed: ' . $e->getMessage());
        return $this->response->setJSON([
            'status' => 500,
            'message' => 'Import failed: ' . $e->getMessage(),
            'data' => [
                'stats' => $stats,
                'error' => $e->getMessage(),
                'trace' => ENVIRONMENT === 'development' ? $e->getTrace() : NULL
            ]
        ]);
    }
    
//    function set_designations($designation_cell){
//        if(!$designation_cell)
//            return NULL;
//        $designations_import = array_filter(array_map('trim', explode(',', $designation_cell))); // Trim and remove empty values
//        $designation_ids = [];
//
//        if (!empty($designations_import)) {
//            $designationModel = new DesignationsModel();
//
//            foreach ($designations_import as $designation) {
//                $designation_result = $designationModel->where('LOWER(name)', strtolower($designation))->first();
//
//                if ($designation_result) { // Only add if found
//                    $designation_ids[] = $designation_result['id']; // Assuming 'id' is the primary key
//                }
//            }
//        }
//        return $designation_ids;
//    }
//
//    private function setReviewerRole($importData, $role)
//    {
//        if ($role == 'Program Chair') {
//            $importData['is_deputy_reviewer'] = 1;
//        } elseif ($role == 'Reviewer') {
//            $importData['is_regular_reviewer'] = 1;
//        }
//        return $importData;
//    }

    private function createProfileData($user_id)
    {
        return [
            'author_id' => $user_id,
            'company' => !empty($company) ? $company : "",
            'division_id' => json_encode(!empty($division_id) ? [$division_id] : NULL)
        ];
    }

    private function updateProfileData($existingProfile, $company, $division_id)
    {
        $mergedDivisions = [];
        if ($existingProfile) {
            $existingDivisions = json_decode($existingProfile['division_id'], true);
            if (is_array($existingDivisions)) {
                $mergedDivisions = array_unique(array_merge($existingDivisions, [$division_id]));
            }
            return [
                'company' => !empty($company) ? $company : "",
                'division_id' => json_encode($mergedDivisions)
            ];
        } else {
            return $this->createProfileData($existingProfile['author_id'], $company, $division_id);
        }
    }

    public function createUser()
    {
        $post = $this->request->getPost();

        $rules = [
            'password' => 'required|min_length[6]|max_length[255]',
            'email' => 'required|valid_email|max_length[255]|is_unique[users.email]',
            'name' => 'required|max_length[255]',
            'surname' => 'required|max_length[255]',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => $this->validator->getErrors(),
            ]);
        }

        $userFields = $this->prepareUserFields($post, true);

        try {
            $this->db->transStart();

            $userId = $this->userModel->insert($userFields);

            $this->createUserProfile($userId, $post);

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'errors' => ['Failed to create user!'],
                ]);
            }

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'User Created!',
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error creating user: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => ['An unexpected error occurred. Please try again.'],
            ]);
        }
    }

    public function updateUser()
    {
        $post = $this->request->getPost();

        if (empty($post['user_id'])) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => ['User ID is required for update!'],
            ]);
        }

        $rules = [
            'email' => 'required|valid_email|max_length[255]|is_unique[users.email,id,' . $post['user_id'] . ']',
            'name' => 'required|max_length[255]',
            'surname' => 'required|max_length[255]',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => $this->validator->getErrors(),
            ]);
        }

        $userFields = $this->prepareUserFields($post, false);

        try {
            $this->db->transStart();

            (new UserModel())->update($post['user_id'], $userFields);

            $this->updateUserProfile($post['user_id'], $post);


            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'errors' => ['Failed to update user!'],
                ]);
            }

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'User Updated!',
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error updating user: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => ['An unexpected error occurred. Please try again.'],
            ]);
        }
    }

    private function prepareUserFields($post, $isCreate = true)
    {
        $fields = [
            'name' => $post['name'],
            'surname' => $post['surname'],
            'middle_name' => $post['middle_name'] ?? '',
            'prefix' => $post['prefix'] ?? '',
            'suffix' => $post['suffix'] ?? '',
            'email' => $post['email'],
            'is_regular_reviewer' => isset($post['is_regular_reviewer']) ? 1 : 0,
            'is_deputy_reviewer' => isset($post['is_deputy_reviewer']) ? 1 : 0,
            'is_session_moderator' => isset($post['is_session_moderator']) ? 1 : 0,
        ];

        if (!empty($post['password']) ) {
            if($post['password'] !== '******') {
                $fields['password'] = password_hash($post['password'], PASSWORD_DEFAULT);
            }
        }

        return $fields;
    }

    private function createUserProfile($userId, $post)
    {
        $profileFields = [
            'author_id' => $userId
        ];

        if ($userId && !empty($post['divisions'])) {
            $profileFields['division_id'] = $post['divisions'] ? json_encode($post['divisions']) : [];
        }

        if(isset($post['institution']) ){
            $profileFields['institution'] = ($post['institution']);
        }

        if(!$this->userProfileModel->where('author_id', $userId)->first()){
            $this->userProfileModel->insert($profileFields);
        }
    }

    private function updateUserProfile($userId, $post)
    {
        $profileFields = [
            'institution' => isset($post['institution']) ? trim($post['institution']):'',
        ];

        if(!empty($post['divisions'])){
            $profileFields['division_id'] = !empty($post['divisions']) ? json_encode( $post['divisions']):[];
        }

        $profileFields['study_group_affiliation_status'] =  isset($post['study_group_affiliation_status']) ? 1 : 0 ;
        $profileFields['study_group_affiliation'] =  isset($post['study_group_affiliation']) ? $post['study_group_affiliation'] : NULL;

        if(!(new UsersProfileModel())->where('author_id', $userId)->first())
            $this->createUserProfile($userId, $post);
        else
            (new UsersProfileModel())->where('author_id', $userId)->set($profileFields)->update();
    }

}
