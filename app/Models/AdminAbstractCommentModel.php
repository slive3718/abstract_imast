<?php
namespace App\Models;

use CodeIgniter\Model;

class AdminAbstractCommentModel extends Model
{
    protected $table = 'admin_abstract_comment';

    protected $allowedFields = ['*', 'paper_id', 'admin_id', 'is_flag', 'date_time', 'comment'];
    protected $primaryKey = 'id';
    // protected $allowedFields = ['title', 'description'];
    protected $returnType = 'array';
    private $error;

}