<?php namespace App\Models;

use CodeIgniter\Model;

class LogsSeenModel extends Model
{
    protected $table      = 'logs_seen';
    protected $primaryKey = 'id';

    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    // this happens first, model removes all other fields from input data
    protected $allowedFields = [
       'user_id', 'user_type', 'log_id'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $dateFormat  	 = 'datetime';

    protected $validationRules = [];

    // we need different rules for logs
    protected $validationMessages = [];

    protected $skipValidation = false;


    //--------------------------------------------------------------------

    /**
     * Retrieves validation rule
     */
    public function getRule(string $rule)
    {
        return $this->dynamicRules[$rule];
    }


}