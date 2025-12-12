<?php
namespace App\Models;

use CodeIgniter\Model;
use Faker\Provider\Base;

class CMEAssignedPapersModel extends BaseModel
{
    protected $table = 'cme_assigned_papers';

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
        $this->allowedFields = $this->db->getFieldNames($this->table);
    }

    protected function excludeDeleted(array $data)
    {
        $this->builder()->where($this->table . '.deleted_at', null);
        return $data;
    }


    function is_valid_cme($user_id){
        return $this->where('reviewer_id', $user_id)->get()->getRow() ?? false;
    }

}