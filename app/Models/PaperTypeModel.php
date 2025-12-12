<?php
namespace App\Models;

use CodeIgniter\Database\ConnectionInterface;
use CodeIgniter\Model;
use CodeIgniter\Validation\ValidationInterface;

class PaperTypeModel extends Model
{
    protected $table = 'paper_type';

    protected $primaryKey = 'id';

    private $error;
    protected  $returnType = 'object';


    public function getPaperTypeName($type_id){
        return $this->select('name')
            ->where('type', $type_id)
            ->asArray()->first();
    }
}