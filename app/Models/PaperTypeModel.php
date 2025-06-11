<?php
namespace App\Models;

use CodeIgniter\Model;

class PaperTypeModel extends Model
{
    protected $table = 'paper_type';

    protected $primaryKey = 'id';

    private $error;
    protected  $returnType = 'object';


}