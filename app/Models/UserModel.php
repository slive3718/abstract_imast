<?php
namespace App\Models;

use CodeIgniter\Model;

class UserModel extends BaseModel
{
    protected $DBGroup = 'shared';
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'email',
        'name',
        'middle_name',
        'surname',
        'prefix',
        'suffix',
        'password',
        'username',
        'is_deputy_reviewer',
        'is_regular_reviewer',
        'is_session_moderator',
        'is_study_group'
    ];
    // protected $allowedFields = ['title', 'description'];


    public function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect('shared');
    }

    public function setDBConnection($connection)
    {
        $this->db = $connection;
        return $this;
    }


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
        $author = $this->db->table('users')
            ->join($this->defaultDB->database.'.paper_authors p', 'users.id = p.author_id')
            ->where('email', $email)
            ->get()
            ->getResultObject()[0] ?? false;

        if (!$author) {
            return false;
        } else {
            return $this->db
                ->table('users')
                ->where(['email' => $email])
                ->get()->getResultObject()[0] ?? false;
        }
    }



}