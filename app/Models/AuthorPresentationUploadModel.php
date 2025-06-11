<?php
namespace App\Models;

use CodeIgniter\Model;

class AuthorPresentationUploadModel extends Model
{
    protected $table = 'author_presentation_upload';

    protected $allowedFields = ['original_name','saved_name','upload_path','file_path','date'];
    protected $primaryKey = 'id';
    protected $returnType = 'object'; 
    private $error;

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