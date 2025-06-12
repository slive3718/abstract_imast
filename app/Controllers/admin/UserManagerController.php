<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Controllers\User;

use App\Models\AbstractReviewModel;
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
    private UserModel $userModel;
    private UsersProfileModel $userProfileModel;
    private $db;
    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->userProfileModel = new UsersProfileModel();
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

                    // Check and set reviewer roles
                    $importData = $this->setReviewerRole($importData, trim($cellValue[7]));

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
                        $profileData = $this->mergeProfileData($existingProfile, $cellValue[5], $division_id);
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

    private function setReviewerRole($importData, $role)
    {
        if ($role == 'Program Chair') {
            $importData['is_deputy_reviewer'] = 1;
        } elseif ($role == 'Reviewer') {
            $importData['is_regular_reviewer'] = 1;
        }
        return $importData;
    }

    private function createProfileData($user_id, $company, $division_id)
    {
        return [
            'author_id' => $user_id,
            'company' => !empty($company) ? $company : "",
            'division_id' => json_encode(!empty($division_id) ? [$division_id] : [""])
        ];
    }

    private function mergeProfileData($existingProfile, $company, $division_id)
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

            $this->userModel->update($post['user_id'], $userFields);

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

        if(!$this->userProfileModel->where('author_id', $userId)->first())
            $this->createUserProfile($userId, $post);
        else
            $this->userProfileModel->where('author_id', $userId)->set($profileFields)->update();
    }

}
