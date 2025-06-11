<?php namespace App\Models;

use CodeIgniter\Model;

class LogsModel extends Model
{
    protected $table      = 'logs';
    protected $primaryKey = 'id';

    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    // this happens first, model removes all other fields from input data
    protected $allowedFields = [
        'date', 'user_id', 'ref_1', 'ref_2', 'ip_address', 'location', 'user_agent', 'message', 'context', 'action', 'level'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $dateFormat  	 = 'datetime';

    protected $validationRules = [];

    // we need different rules for logs
    protected $dynamicRules = [
        'logs' => [
            'date'	=> 'required',
            'user_id' => 'user_id',
            'reference'	=> 'required',
            'ip'	=> 'required',
            'location'	=> 'required',
            'user_agent'	=> 'required',
            'action'	=> 'action',
        ]
    ];

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