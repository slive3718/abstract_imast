<?php
namespace App\Models;

use CodeIgniter\Model;

class EmailTemplatesModel extends Model
{
    protected $table = 'email_templates';
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