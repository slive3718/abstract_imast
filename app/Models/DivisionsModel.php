<?php
namespace App\Models;

use CodeIgniter\Model;

class DivisionsModel extends Model
{
    protected $table = 'divisions';

    protected $primaryKey = 'id';

    private $error;
    protected  $returnType = 'object';


}