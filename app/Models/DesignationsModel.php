<?php
namespace App\Models;

use CodeIgniter\Model;

class DesignationsModel extends Model
{
    protected $table = 'designations';

    protected $primaryKey = 'id';

    private $error;
    protected  $returnType = 'array';



    public function getDesignationsColumn(){
        $all = $this->findAll();
        return array_column($all,'name', 'id');
    }

    public function get_array_column(){
        $designations = $this->findAll();
        return array_column($designations,'name','id');
    }
}