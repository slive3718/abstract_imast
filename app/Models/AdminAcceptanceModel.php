<?php
namespace App\Models;

use CodeIgniter\Database\ConnectionInterface;
use CodeIgniter\Model;
use CodeIgniter\Validation\ValidationInterface;

class AdminAcceptanceModel extends Model
{
    protected $table = 'admin_abstract_acceptance';

    protected $allowedFields = ['*', 'abstract_id','user_id','acceptance_confirmation','comment', 'presentation_preference', 'email_template_used', 'acceptance_confirmation_date', 'acceptance_type'];
    protected $primaryKey = 'id';
    // protected $allowedFields = ['title', 'description'];
    protected $returnType = 'array';
    private $error;

}