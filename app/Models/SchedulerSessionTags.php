<?php
namespace App\Models;

use CodeIgniter\Model;

class SchedulerSessionTags extends Model
{
    protected $table = 'scheduler_session_tags';

    protected $primaryKey = 'id';

    private $error;
    protected  $returnType = 'object';


    public function getPaperTypeName($id){
        return $this->select('name')
            ->where('id', $id)
            ->asArray()->first();
    }
}