<?php
namespace App\Models;

use CodeIgniter\Model;

class CMEReviewersModel extends Model
{
    protected $table = 'cme_reviewers';

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
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
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
        return $this->where('cme_reviewer_id', $user_id)->get()->getRow() ?? false;
    }

    public function getCMEPapersToReview($user_id, $is_declined = false){
        $this->where('cme_reviewer_id', $user_id);
        if($is_declined) {
            $this->where('is_declined', $is_declined);
        }
        $this->orderBy('id', 'desc');
        return $this->get();
    }
}