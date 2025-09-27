<?php
namespace App\Models;

use CodeIgniter\Model;

class UserModel extends BaseModel
{
    protected $DBGroup = 'shared';
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $allowedFields;

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = true;


    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = ['excludeDeleted'];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];



    public function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect('shared');
        $this->allowedFields = $this->db->getFieldNames($this->table);
    }

     protected function excludeDeleted(array $data)
     {
         $this->builder()->where($this->table . '.deleted_at', null);
         return $data;
     }


    public function setDBConnection($connection)
    {
        $this->db = $connection;
        return $this;
    }


    function validateUser($post){
        // return $data;
        $result= $this->select('*')
        ->where('email', $post['email'])
        ->first();

        //  print_r($post['email']);
        return($result);
    }


    public function cred_check(string $email, string $password)
    {
        $user = $this->db->table('users')->where(['email'=>$email])->get()->getResultObject()[0]??false;
        if (!$user)
        {
            return false;
        }else{
            if (password_verify($password, $user->password))
            {
                return $this->db
                    ->table('users')
                    ->select('id, prefix, name, surname, suffix, email, is_super_admin, is_regular_reviewer, is_session_moderator, is_study_group')
                    ->where(['email'=>$email])
                    ->get()->getResultObject()[0]??false;
            }
        }
        return false;
    }

    public function author_cred_check($email)
    {
        $author = $this->builder()->where($this->table . '.deleted_at', null)
            ->join($this->defaultDB->database.'.paper_authors p', 'users.id = p.author_id')
            ->where('email', $email)
            ->get()
            ->getResultObject()[0] ?? false;

        if (!$author) {
            return false;
        } else {
            return
                $this->builder()->where($this->table . '.deleted_at', null)
                ->where(['email' => $email])
                ->get()->getResultObject()[0] ?? false;
        }
    }



}