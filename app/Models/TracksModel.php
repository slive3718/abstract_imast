<?php
namespace App\Models;

use CodeIgniter\Model;

class TracksModel extends Model
{
    protected $table = 'tracks';
    protected $primaryKey = 'id';
    // protected $allowedFields = ['title', 'description'];

    public function Get()
    {
       try {
            return $this->findAll();
        } catch (\CodeIgniter\Database\Exceptions\DatabaseException $e) {
            // Log the error or display an error message
            $error = json_encode('Database error: ' . $e->getMessage());
            return $error;
        }
    }
}