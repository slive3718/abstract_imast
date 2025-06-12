<?php
namespace App\Models;

use CodeIgniter\Database\ConnectionInterface;
use CodeIgniter\Model;
use CodeIgniter\Validation\ValidationInterface;

class PapersModel extends Model
{
    protected $table = 'papers';

    protected $allowedFields;
    protected $primaryKey = 'id';

    protected $returnType = 'object';

    protected $beforeFind = ['excludeDeletedRecords'];
    private $error;

    private $shared_db;


    function __construct(ConnectionInterface $db = null, ValidationInterface $validation = null)
    {
        parent::__construct();
        $this->db = $db ?? db_connect();
        $this->shared_db_name = ENV('database.shared.database');
        $this->validation = $validation;

        $this->initializeAllowedFields();
    }

    protected function initializeAllowedFields(): void
    {
        $this->allowedFields = $this->db->getFieldNames($this->table);

        // Optionally, you can filter or manipulate the allowed fields array
        $excludedFields = ['id', 'created_at', 'updated_at'];
        $this->allowedFields = array_diff($this->allowedFields, $excludedFields);
    }

    protected function excludeDeletedRecords(array $data){
        if (isset($data['builder']) && empty($data['method'])) {
            $data['builder']->where('deleted', 0);
        }
        return $data;
    }
    public function GetJoinedUser($submission_type)
    {
       try {
           return $this->table('papers')
               ->select('papers.*, u.name as user_name, u.surname as user_surname, u.email as user_email, u.middle_name as user_middle')
                ->join(   $this->shared_db_name.'.users u', 'u.id = papers.user_id', 'left')
                ->where('active_status', 1)
                ->where('submission_type =', $submission_type)
                ->get();
            // return $this->findAll();
        } catch (\CodeIgniter\Database\Exceptions\DatabaseException $e) {
            // Log the error or display an error message
           return json_encode('Database error: ' . $e->getMessage());
        }
    }

    public function GetJoinedUserQuery($submission_type)
    {
        try {
            return $this->table('papers')
                ->select('papers.*, u.name as user_name, u.surname as user_surname, u.email as user_email, u.middle_name as user_middle')
                ->join('users u', 'u.id = papers.user_id', 'left')
                ->where('active_status', 1)
                ->where('submission_type =', $submission_type);
            // return $this->findAll();
        } catch (\CodeIgniter\Database\Exceptions\DatabaseException $e) {
            // Log the error or display an error message
            return json_encode('Database error: ' . $e->getMessage());
        }
    }

}