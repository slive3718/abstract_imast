<?php
namespace App\Models;

use CodeIgniter\Model;

class AttestationModel extends Model
{
    protected $table = 'attestation';
    protected $primaryKey = 'id';
    protected $allowedFields = []; // Initialize as empty array

    public function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect();

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

    public function Get()
    {
    
       try {
            return $this->findAll();
        } catch (\CodeIgniter\Database\Exceptions\DatabaseException $e) {
            // Log the error or display an error message
            $error = json_encode('Database error: ' . $e->getMessage());
            return $error;
        }
    }

    public function Add($data){
        try {
            $this->insert($data);
            if ($this->affectedRows() > 0) {
                return true;
            } else {
                return false;
            }
        } catch (\Exception $e) {
            // Handle the exception here
            return json_encode(array('error'=>$e->getMessage()));
            
        }
    }


}