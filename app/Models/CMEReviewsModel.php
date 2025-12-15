<?php
namespace App\Models;

use CodeIgniter\Model;

class CMEReviewsModel extends Model
{
    protected $table = 'cme_reviews';

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
    protected $beforeInsert   = ['validateReferences'];
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

    /**
     * Prevent zero/negative department IDs
     */
    protected function validateReferences(array $data)
    {
        if (empty($data['data'])) {
            throw new \InvalidArgumentException("Empty data provided for validation.");
        }

        $requiredFields = ['paper_id', 'cme_reviewer_id'];

        foreach ($requiredFields as $field) {
            // Check if field exists and is valid
            if (!isset($data['data'][$field])) {
                throw new \InvalidArgumentException("Field '$field' is required.");
            }

            $value = $data['data'][$field];

            // Skip validation if value is null (for updates)
            if ($value === null) {
                continue;
            }

            // Check if it's numeric
            if (!is_numeric($value)) {
                throw new \InvalidArgumentException("Field '$field' must be a numeric value.");
            }

            // Convert to integer
            $intValue = (int)$value;
            if ($intValue <= 0) {
                throw new \InvalidArgumentException("Field '$field' must be a positive integer.");
            }
        }

        return $data;
    }


    function is_valid_cme($user_id){
        return $this->where('reviewer_id', $user_id)->get()->getRow() ?? false;
    }

}