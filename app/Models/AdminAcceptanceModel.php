<?php
namespace App\Models;

use CodeIgniter\Database\ConnectionInterface;
use CodeIgniter\Model;
use CodeIgniter\Validation\ValidationInterface;

class AdminAcceptanceModel extends Model
{
    protected $table = 'admin_abstract_acceptance';

    protected $allowedFields = ['*', 'abstract_id','user_id','acceptance_confirmation','comment', 'presentation_preference', 'email_template_used', 'acceptance_confirmation_date', 'acceptance_type'];
    protected $primaryKey = 'id';
    // protected $allowedFields = ['title', 'description'];
    protected $returnType = 'array';
    private $error;

    public function getAllWithDetails(array $filters = []): array
    {
        $builder = $this->db->table($this->table . ' a');
        // Apply filters
        if (!empty($filters['status'])) {
            $builder->where('a.acceptance_confirmation', $filters['status']);
        }

        if (!empty($filters['user_id'])) {
            $builder->where('a.user_id', $filters['user_id']);
        }

        if (!empty($filters['acceptance_type'])) {
            $builder->where('a.acceptance_type', $filters['acceptance_type']);
        }

        // Add date range filters if needed
        if (!empty($filters['start_date'])) {
            $builder->where('a.acceptance_confirmation_date >=', $filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $builder->where('a.acceptance_confirmation_date <=', $filters['end_date']);
        }

        return $builder->get()->getResultArray();
    }

}