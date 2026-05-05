<?php
namespace App\Models;

use App\Services\InstitutionServices;
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
    protected $useTimestamps = true;
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
    protected $beforeInsert   = ['prepareData'];
    protected $afterInsert    = [];
    protected $beforeUpdate   = ['prepareData'];
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

    protected function prepareData(array $data): array
    {
        $fieldsToClean = ['name', 'surname', 'middle_name'];

        foreach ($fieldsToClean as $field) {
            if (isset($data['data'][$field])) {
                $data['data'][$field] = $this->safeCleanName($data['data'][$field]);
            }
        }

        return $data;
    }

    protected function safeCleanName(string $value): string
    {
        // Remove null bytes, vertical tab, form feed, etc.
        $cleaned = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $value);

        // Remove zero-width characters (invisible characters)
        $cleaned = preg_replace('/[\x{200B}-\x{200D}\x{FEFF}]/u', '', $cleaned);

        // Remove bidirectional control characters that could cause display issues
        $cleaned = preg_replace('/[\x{202A}-\x{202E}\x{2066}-\x{2069}]/u', '', $cleaned);

        $cleaned = str_replace(["\r\n", "\r"], "", $cleaned);

        // Trim to remove leading/trailing whitespace
        $cleaned = trim($cleaned);

        return $cleaned;
    }

    private function cleanName(string $name): string
    {
        return preg_replace('/[^a-zA-Z\s]/', '', trim($name));
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
        $result= $this->select('*')
        ->where('email', $post['email'])
        ->first();
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