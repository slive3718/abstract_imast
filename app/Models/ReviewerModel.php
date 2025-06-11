<?php
namespace App\Models;

use CodeIgniter\Model;

class ReviewerModel extends Model
{
    protected $table = 'paper_assigned_reviewer';

    protected $allowedFields = ['paper_id', 'reviewer'];
    // protected $allowedFields = ['title', 'description'];
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

      public function GetJoinedUser($abstract_id = null)
    {

       try {
        $this->select('*');
        if($abstract_id !== null){
            $this->where('paper_id', $abstract_id);
        }
        $this->join('users u', "u.id = {$this->table}.reviewer_id", 'left');
       return $this->get();

        } catch (\CodeIgniter\Database\Exceptions\DatabaseException $e) {
            // Log the error or display an error message
            $error = json_encode('Database error: ' . $e->getMessage());
            return $error;
        }
    }

    public function Add($data){
        try {
            // $this->db->table('abstract_assigned_reviewer');
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

    public function AddReviewerTransaction($data){
    
        // print_r($data);exit;
        $this->db->transStart();
       
        try {
            // $this->db->table('abstract_assigned_reviewer');
            if(isset($data['reviewers']) && !empty($data['reviewers'])){
                foreach($data['reviewers'] as $reviewer) {
                    $this->delete(array('abstract_id'=>$data['abstract_id'], 'reviewer'=>$reviewer));
                $this->insert(array('abstract_id'=>$data['abstract_id'], 'reviewer'=>$reviewer));
                }
              }
             // Commit the transaction if all iterations succeed
            $this->db->transCommit();
            return json_encode(array('success'=>'All records are inserted successfully'));
         
        } catch (\Exception $e) {
            // Handle the exception here
            $this->db->transRollback();
            return json_encode(array('error'=>$e->getMessage()));
            
        }

    }

     public function DeleteWhereAbstract($abstract_id)
    {
        // Delete record based on ID
        $this->where('abstract_id', $abstract_id)->delete();
    }

    public function validateReviewer($post, $user_id){

        $this->where('reviewer_id', $user_id)->where('reviewer_type', 'regular');
        return $this->get();
    }

    public function validateDeputy($post, $user_id){
        $this->where('reviewer_id', $user_id)->where('reviewer_type', 'deputy');
        return $this->get();
    }

    public function getReviewerAbstracts($user_id, $reviewer_type, $is_declined = null){
        $this->where('reviewer_id', $user_id);
        $this->where('is_deleted', 0);
        $this->where('reviewer_type', $reviewer_type);
        if($is_declined!== null) {
            $this->where('is_declined', $is_declined);
        }
        $this->orderBy('id', 'desc');
        return $this->get();
    }
    

    public function getDistinctArray($columnName=null)
    {
        $this->distinct()->select('*');
        $this->groupBy('reviewer');
        return $this->get();

    }

    

}