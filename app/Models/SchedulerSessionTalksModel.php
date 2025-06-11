<?php
namespace App\Models;

use CodeIgniter\Database\ConnectionInterface;
use CodeIgniter\Model;
use CodeIgniter\Validation\ValidationInterface;

class SchedulerSessionTalksModel extends Model
{
    protected $table = 'scheduler_session_talks';

    protected $allowedFields;
    // protected $allowedFields = ['title', 'description'];
    protected $primaryKey = 'id';

    protected $useTimestamps = true; // Enable automatic timestamps
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    function __construct(ConnectionInterface $db = null, ValidationInterface $validation = null)
    {
        parent::__construct();
        $this->db = $db ?? db_connect();
        $this->validation = $validation;

        $this->initializeAllowedFields();
    }

    /**
     * Initialize the allowed fields property.
     */
    protected function initializeAllowedFields(): void
    {
        $this->allowedFields = $this->db->getFieldNames($this->table);

        // Optionally, you can filter or manipulate the allowed fields array
        $excludedFields = ['created_at', 'updated_at'];
        $this->allowedFields = array_diff($this->allowedFields, $excludedFields);
    }

    function get_talk_schedule_query($abstract_id){
        $query = $this->where('abstract_id', $abstract_id)
            ->join('scheduler_events se', $this->table.'.scheduler_event_id = se.id', 'left');
        return $query;
    }
}