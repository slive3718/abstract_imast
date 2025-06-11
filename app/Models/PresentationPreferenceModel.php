<?php
namespace App\Models;

use CodeIgniter\Model;

class PresentationPreferenceModel extends Model
{
    protected $table = 'presentation_preferences';

    protected $allowedFields = ['preference_id', 'value'];
    // protected $allowedFields = ['title', 'description'];
    protected $primaryKey = 'id';
    

}