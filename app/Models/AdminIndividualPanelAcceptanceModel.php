<?php
namespace App\Models;

use CodeIgniter\Model;

class AdminIndividualPanelAcceptanceModel extends Model
{
    protected $table = 'admin_individual_panel_acceptance';

    protected $allowedFields = ['*', 'individual_panel_id','user_id','acceptance_confirmation','comment_to_submitter', 'presentation_preference', 'email_template_used', 'acceptance_confirmation_date', 'acceptance_type',
        'administration_comment',
        'is_flag',
        'administration_comment_date_time'
        ];
    protected $primaryKey = 'id';
    // protected $allowedFields = ['title', 'description'];
    protected $returnType = 'array';
    private $error;

}