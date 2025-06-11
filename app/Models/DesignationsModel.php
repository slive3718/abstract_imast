<?php
namespace App\Models;

use CodeIgniter\Model;

class DesignationsModel extends Model
{
    protected $table = 'designations';

    protected $primaryKey = 'id';

    private $error;
    protected  $returnType = 'array';


}