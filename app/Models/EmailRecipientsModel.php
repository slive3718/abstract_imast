<?php
namespace App\Models;

use CodeIgniter\Model;

class EmailRecipientsModel extends Model
{
    protected $table = 'email_recipients';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'template_name',
        'email_subject',
        'email_description',
        'email_category',
        'email_body',
        'date_time',
    ];
    // protected $allowedFields = ['title', 'description'];

}