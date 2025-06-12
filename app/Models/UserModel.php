<?php
namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
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
        'is_session_moderator'
    ];
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
                    ->select('id, prefix, name, surname, suffix, email, is_super_admin')
                    ->where(['email'=>$email])
                    ->get()->getResultObject()[0]??false;
            }
        }
        return false;
    }

    public function author_cred_check($email)
    {
        $author = $this->db->table('users')
            ->join('paper_authors p', 'users.id = p.author_id')
            ->where(['email'=>$email])
            ->get()->getResultObject()[0]??false;
        if (!$author)
        {
            return false;
        }else{
            return $this->db
                ->table('users')
                ->select('id, prefix, name, surname, suffix, email, is_super_admin')
                ->where(['email'=>$email])
                ->get()->getResultObject()[0]??false;
        }
    }

    function get_moderator_ids(){
        $result = $this->db->table('scheduler_events')->get()->getResultArray();
        $moderator_ids = [];
        if ($result) {
            foreach ($result as $res) {
                if (!empty($res['session_chair_ids']) && $res['session_chair_ids'] !== '0' && $res['session_chair_ids'] !== "[]") {
                    $session_chair_ids = json_decode($res['session_chair_ids'], true);

                    if (is_array($session_chair_ids)) {
                        foreach ($session_chair_ids as $session_chair_id) {
                            $moderator_ids[] = [
                                'id' => $session_chair_id,
                                'user' => (new UserModel())->find($session_chair_id),
                                'event' => $res
                            ];
                        }
                    }
                }
            }
        }
        return $moderator_ids;
    }


}