<?php
namespace App\Models;

use CodeIgniter\Database\ConnectionInterface;
use CodeIgniter\Model;
use CodeIgniter\Validation\ValidationInterface;

class AffiliationsModel extends Model
{
    protected $table = 'affiliations';
    protected $primaryKey = 'id';
    protected $allowedFields ;

    function __construct(ConnectionInterface $db = null, ValidationInterface $validation = null)
    {
        parent::__construct();
        $this->db = $db ?? db_connect();
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
}