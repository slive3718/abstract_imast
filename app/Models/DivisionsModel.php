<?php
namespace App\Models;

use CodeIgniter\Model;

class DivisionsModel extends Model
{
    protected $table = 'divisions';

    protected $primaryKey = 'id';

    private $error;
    protected  $returnType = 'object';


    function __construct()
    {
        parent::__construct();
    }

    public function getDivisionName($division_id){
        return $this->select('name')
            ->where('division_id', $division_id)
            ->asArray()->first();
    }
}