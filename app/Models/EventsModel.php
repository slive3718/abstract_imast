<?php
namespace App\Models;

use CodeIgniter\Model;

class EventsModel extends Model
{
    protected $table = 'events';
    protected $primaryKey = 'id';
    protected $allowedFields = [];
    protected $returnType = 'object';
    // protected $allowedFields = ['title', 'description'];

}
