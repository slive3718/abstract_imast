<?php
namespace App\Models;

use App\Services\InstitutionServices;
use CodeIgniter\Model;

class UsersProfileModel extends BaseModel
{
    protected $DBGroup = 'shared';
    protected $table = 'users_profile';
    protected $primaryKey = 'id';
    protected $allowedFields = []; // Initialize as empty array

    public function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect('shared');

        $this->initializeAllowedFields();
    }

    public function setDBConnection($connection)
    {
        $this->db = $connection;
        return $this;
    }
    /**
     * Initialize allowed fields dynamically while excluding sensitive/auto-increment fields
     */
    protected function initializeAllowedFields()
    {
        $fields = $this->db->getFieldNames($this->table);
        $excludedFields = ['id', 'created_at', 'updated_at', 'deleted_at']; // Add any sensitive fields here

        $this->allowedFields = array_diff($fields, $excludedFields);
    }

    public function institution($user_id):array{
        $user = $this->where('author_id', $user_id)->first();
        if(empty($user['institution_id'])){
            return [];
        }
        return (new InstitutionServices())->getInstitutionWithAddress($user['institution_id']);
    }

    public function institutions($user_ids):array{
        $users = $this->whereIn('author_id', $user_ids)->findAll();
        $userInstitution = array_map(function($user) {
            return (new InstitutionServices())->getInstitutionWithAddress($user['institution_id']);
            }, $users);

        return $userInstitution;

    }

//    public function designations($user_id): array{
//        if(is_array($user_id))
//            $user = $this->whereIn('author_id', $user_id)->first();
//       else
//           $user = $this->where('author_id', $user_id)->first();
//        return $user['designations'] ? json_decode($user['designations'], true) : [];
//    }
//
//    public function designationsArray($user_id): array
//    {
//        // Check if an array of IDs is provided for bulk retrieval
//        if (is_array($user_id)) {
//            // 1. Fetch ALL profiles matching the IDs using whereIn()
//            // This is the correct way to handle array parameters in the query builder.
//            $profiles = $this->whereIn('author_id', $user_id)->findAll();
//
//            $designationsMap = [];
//            // 2. Map the results: author_id => designations_array
//            foreach ($profiles as $profile) {
//                $authorId = $profile['author_id'];
//                $designationsMap[$authorId] = $profile['designations']
//                    ? json_decode($profile['designations'], true)
//                    : [];
//            }
//
//            return $designationsMap;
//
//        } else {
//            // Handle a single user ID
//            // Note: The error is unlikely to originate here, but this ensures correctness.
//            $user = $this->where('author_id', $user_id)->first();
//
//            return $user['designations'] ? json_decode($user['designations'], true) : [];
//        }
//    }

    // In UsersProfileModel
    public function designationsForMultipleUsers(array $authorIds) : array
    {
        if (empty($authorIds)) return [];

        // Adjust this query based on your actual table structure
        $result = $this->db->table('designations')
            ->whereIn('author_id', $authorIds)
            ->get()
            ->getResultArray();

        $designationsMap = [];
        foreach ($result as $row) {
            $designationsMap[$row['author_id']][] = $row['designation_id'];
        }

        return $designationsMap;
    }

    public function institutionsForMultipleUsers(array $authorIds) : array
    {
        if (empty($authorIds)) return [];

        // Adjust this query based on your actual table structure
        $result = $this->db->table('user_institutions')
            ->whereIn('author_id', $authorIds)
            ->get()
            ->getResultArray();

        $institutionsMap = [];
        foreach ($result as $row) {
            $institutionsMap[$row['author_id']] = $row['institution_name']; // Adjust field name
        }

        return $institutionsMap;
    }


}