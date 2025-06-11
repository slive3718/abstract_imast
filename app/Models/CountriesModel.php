<?php
namespace App\Models;

use CodeIgniter\Model;

class CountriesModel extends Model
{
    protected $table = 'countries';
    protected $primaryKey = 'id';

    
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

    public function Add($data){
        try {
            $this->insert($data);
            if ($this->affectedRows() > 0) {
                return true;
            } else {
                return false;
            }
        } catch (\Exception $e) {
            // Handle the exception here
            return json_encode(array('error'=>$e->getMessage()));
            
        }
    }


}