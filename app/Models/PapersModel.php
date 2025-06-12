<?php
namespace App\Models;

use CodeIgniter\Model;

class PapersModel extends Model
{
    protected $table = 'papers';

    protected $allowedFields = [
        'id',
        'user_id',
        'submission_type',
        'division_id',
        'type_id',
        'title',
        'summary',
        'is_ijmc_interested',
        'is_finalized',
        'active_status',
        'tracks',
        'custom_id'
    ];
    protected $primaryKey = 'id';

    protected $returnType = 'object';
    private $error;

    

    public function GetJoinedUser($submission_type)
    {
       try {
           return $this->table('papers')
               ->select('papers.*, u.name as user_name, u.surname as user_surname, u.email as user_email, u.middle_name as user_middle')
                ->join('users u', 'u.id = papers.user_id', 'left')
                ->where('active_status', 1)
                ->where('submission_type =', $submission_type)
                ->get();
            // return $this->findAll();
        } catch (\CodeIgniter\Database\Exceptions\DatabaseException $e) {
            // Log the error or display an error message
           return json_encode('Database error: ' . $e->getMessage());
        }
    }

    public function GetJoinedUserQuery($submission_type)
    {
        try {
            return $this->table('papers')
                ->select('papers.*, u.name as user_name, u.surname as user_surname, u.email as user_email, u.middle_name as user_middle')
                ->join('users u', 'u.id = papers.user_id', 'left')
                ->where('active_status', 1)
                ->where('submission_type =', $submission_type);
            // return $this->findAll();
        } catch (\CodeIgniter\Database\Exceptions\DatabaseException $e) {
            // Log the error or display an error message
            return json_encode('Database error: ' . $e->getMessage());
        }
    }

}