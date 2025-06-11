<?php
namespace App\Models;

use CodeIgniter\Model;

class RoomsModel extends Model
{
    protected $table = 'scheduler_rooms';

    protected $allowedFields = ['room_id', 'name'];
    // protected $allowedFields = ['title', 'description'];
    protected $primaryKey = 'id';
    

}