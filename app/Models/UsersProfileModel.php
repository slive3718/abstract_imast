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

}