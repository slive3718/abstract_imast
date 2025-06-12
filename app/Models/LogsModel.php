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

    function getCompiledUnseenLogs($ref_1, $ref_2, $user_id, $whereContextArray = [], $whereActionArray = [])
    {
        $LogsModel = new LogsModel();
        $currentUserId = session('user_id');

        $logsQuery = $LogsModel
            ->select('logs.*, logs_seen.user_id as seen_by')
            ->join('logs_seen', 'logs.id = logs_seen.log_id AND logs_seen.user_id = '.$currentUserId, 'left');

        // Base conditions
        $logsQuery->where([
            'logs.ref_1' => $ref_1,
            'logs.ref_2' => $ref_2,
            'logs.user_id' => $user_id,
        ]);

        // Context conditions (if provided)
        if (!empty($whereContextArray)) {
            $logsQuery->groupStart();
            foreach ($whereContextArray as $whereContext) {
                $logsQuery->orWhere('logs.context', $whereContext);
            }
            $logsQuery->groupEnd();
        }

        // Action conditions (if provided)
        if (!empty($whereActionArray)) {
            $logsQuery->groupStart();
            foreach ($whereActionArray as $whereAction) {
                $logsQuery->orWhere('logs.action', $whereAction);
            }
            $logsQuery->groupEnd();
        }

        // Only get unseen logs by current user
        $logsQuery->where('logs_seen.id IS NULL');

        // Grouping and ordering
        $logsQuery
            ->groupBy('logs.user_id, logs.ref_1, logs.ref_2, logs.message, logs.context, logs.action')
            ->selectMax('logs.created_at', 'latest_created_at')
            ->orderBy('latest_created_at', 'DESC');

        return $logsQuery->findAll();
    }

    function getCompiledLogs($ref_1, $ref_2, $user_id, $whereContextArray = [], $whereActionArray = [], $limit = '', $groupBy = '', $orderBy = '')
    {
        $LogsModel = new LogsModel();

        $logsQuery = $LogsModel
            ->select('logs.*');

        // Base conditions
        $logsQuery->where([
            'logs.ref_1' => $ref_1,
            'logs.ref_2' => $ref_2,
            'logs.user_id' => $user_id,
        ]);

        // Context conditions (if provided)
        if (!empty($whereContextArray)) {
            $logsQuery->groupStart();
            foreach ($whereContextArray as $whereContext) {
                $logsQuery->orWhere('logs.context', $whereContext);
            }
            $logsQuery->groupEnd();
        }

        // Action conditions (if provided)
        if (!empty($whereActionArray)) {
            $logsQuery->groupStart();
            foreach ($whereActionArray as $whereAction) {
                $logsQuery->orWhere('logs.action', $whereAction);
            }
            $logsQuery->groupEnd();
        }

        if(!empty($groupBy)){
            foreach ($groupBy as $group)
            $logsQuery->groupBy($group);
        }

        if(!empty($orderBy)){
            foreach ($orderBy as $order){
                $logsQuery->orderBy($order['column'], $order['value']);
            }
        }

        if(!empty($limit)){
            $logsQuery->limit($limit);
        }

        return $logsQuery->findAll();
    }
}