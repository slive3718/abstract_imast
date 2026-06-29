<?php

namespace App\Models;

use CodeIgniter\Model;

class AppDisclosureModel extends Model
{
    protected $table = 'app_disclosures';

    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = [
        'author_id',
        'financial_relationship',
        'disclosure_support',
        'disclosure_discussed',
        'disclosure_relationship',
        'disclosure_signature',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
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

    // Schema-level validation (basic data integrity)
    protected $initValidationRules = [
        'author_id' => 'required|integer|is_unique[app_disclosures.author_id]',
        'financial_relationship' => 'required|in_list[yes,no,1,0]',
        'disclosure_support' => 'required|in_list[1,0,yes,no]',
        'disclosure_discussed' => 'required|in_list[1,0,yes,no]',
        'disclosure_relationship' => 'required|in_list[1,0,yes,no]',
        'disclosure_signature' => 'required|string|min_length[3]|max_length[100]'
    ];
    protected $initValidationMessages = [
        'author_id' => [
            'required' => 'Author ID is required',
            'integer' => 'Author ID must be a valid number',
            'is_unique' => 'This author already has a disclosure submission'
        ],
        'financial_relationship' => [
            'required' => 'Please answer the financial relationship question',
            'numeric' => 'Please select either Yes or No'
        ],
        'disclosure_support' => [
            'required' => 'Please answer the disclosure support question',
            'numeric' => 'Please select either Yes or No'
        ],
        'disclosure_discussed' => [
            'required' => 'Please answer if disclosure was discussed',
            'numeric' => 'Please select either Yes or No'
        ],
        'disclosure_relationship' => [
            'required' => 'Please answer the relationship question',
            'numeric' => 'Please select either Yes or No'
        ],
        'disclosure_signature' => [
            'required' => 'Please provide your signature confirmation',
            'string' => 'Please type signature'
        ]
    ];

    /**
     * Find disclosure by author ID
     */
    public function findByAuthorId(int $authorId): ?array
    {
        return $this->where('author_id', $authorId)
            ->where('deleted_at', null)
            ->first();
    }


    /**
     * Check if author has submitted disclosure
     */
    public function hasSubmitted(int $authorId): bool
    {
        return $this->where('author_id', $authorId)
                ->where('deleted_at', null)
                ->countAllResults() > 0;
    }

    /**
     * Get all disclosures (for admin use)
     */
    public function getAllActive(): array
    {
        return $this->where('deleted_at', null)
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    /**
     * Soft delete disclosure by author ID
     */
    public function deleteByAuthorId(int $authorId): bool
    {
        return $this->where('author_id', $authorId)
            ->delete();
    }
}