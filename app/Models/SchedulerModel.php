<?php
namespace App\Models;

use CodeIgniter\Database\ConnectionInterface;
use CodeIgniter\Model;
use CodeIgniter\Validation\ValidationInterface;

class SchedulerModel extends Model
{
    protected $table = 'scheduler_events';

    protected $allowedFields;
    // protected $allowedFields = ['title', 'description'];
    protected $primaryKey = 'id';

    protected $useTimestamps = true; // Enable automatic timestamps
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $beforeFind    = ['excludeDeleted'];

    function __construct(ConnectionInterface $db = null, ValidationInterface $validation = null)
    {
        parent::__construct();
        $this->db = $db ?? db_connect();
        $this->validation = $validation;

        $this->initializeAllowedFields();
    }

    protected function excludeDeleted(array $data)
    {
        $this->builder()->where($this->table . '.is_deleted', 0);
        return $data;
    }

    /**
     * Initialize the allowed fields property.
     */
    protected function initializeAllowedFields(): void
    {
        $this->allowedFields = $this->db->getFieldNames($this->table);

        // Optionally, you can filter or manipulate the allowed fields array
        $excludedFields = ['id', 'created_at', 'updated_at'];
        $this->allowedFields = array_diff($this->allowedFields, $excludedFields);
    }
}