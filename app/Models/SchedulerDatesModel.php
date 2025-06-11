<?php
namespace App\Models;

use CodeIgniter\Model;

class SchedulerDatesModel extends Model
{
    protected $table = 'scheduler_dates_allowed';

    protected $allowedFields = ['description', 'date', 'is_deleted'];
    // protected $allowedFields = ['title', 'description'];
    protected $primaryKey = 'id';
    

}