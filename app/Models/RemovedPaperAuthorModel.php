<?php
namespace App\Models;

use CodeIgniter\Model;

class RemovedPaperAuthorModel extends Model
{
    protected $table = 'removed_paper_authors';
    protected $primaryKey = 'id';
    protected $allowedFields = ['*', 'paper_author_id'];
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

    function validateUser($post){
        // return $data;
        $result= $this->select('*')
        ->where('email', $post['email'])
        ->first();

        //  print_r($post['email']);
        return($result);
    }
    
  



}