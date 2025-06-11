<?php namespace App\Models;

use CodeIgniter\Model;

class EmailLogsModel extends Model
{
    protected $table      = 'mail_logs';
    protected $primaryKey = 'id';

    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $dateFormat    = 'datetime';

    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;

    protected $allowedFields;
    // protected $allowedFields = ['title', 'description'];

    public function __construct()
    {
        parent::__construct();
        $this->allowedFields = $this->db->getFieldNames($this->table);
    }

    public function saveToMailLogs($email_logs_array)
    {
        try{
            $this->insert($email_logs_array);
            return  1;
            }catch (\Exception $e){
            return $e;
        }

    }

    public function saveToLogEmailFromTemplate($from_id, $addTo, $subject, $ref_1, $addContent, $status, $from_user_type, $to_user_type, $template_id, $paper_id = null, $error_message = null){
        $insertArray = [
            'from_id'=>$from_id,
            'subject'=>$subject,
            'ref_1' => $ref_1,
            'body'=>$addContent,
            'status'=>$status,
            'from_user_type'=>$from_user_type,
            'to_user_type'=>$to_user_type,
            'template_id'=>$template_id,
            'paper_id'=>$paper_id,
            'error_message'=>$error_message,
            'date_sent'=>date('Y-m-d H:i:s')
        ];

        if (is_array($addTo)) {
            foreach ($addTo as $to_id) {
                $insertArray['to_id'] = $to_id;
                $insertArray['from_id'] = $from_id; // Assuming each 'send_to' is the same for all, adjust as needed
                $this->table->insert($insertArray);
            }
        } else {
            $insertArray['to_id'] = $addTo;
            $insertArray['from_id'] = $from_id; // Assuming 'send_to' is directly provided
            $this->table->insert($insertArray);
        }

    }
}
