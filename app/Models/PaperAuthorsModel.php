<?php
namespace App\Models;

use CodeIgniter\Model;

class PaperAuthorsModel extends BaseModel
{
    protected $table = 'paper_authors'; // Set the database table name
    protected $primaryKey = 'id'; // Set the primary key field

    // Define the fields that can be manipulated (for insert and update)
    protected $allowedFields;

    // Optionally, set return type as 'object' for all queries by default
    protected $returnType = 'array';
    protected $useAutoFields = true;
    public function __construct()
    {
        parent::__construct();
        $this->allowedFields = $this->db->getFieldNames($this->table);
    }

    protected function withUserAndRemovalStatus()
    {
        return $this->select('
            paper_authors.*,
            u.name AS user_name,
            u.surname AS user_surname,
            u.middle_name AS user_middle,
            u.email AS user_email,
            IFNULL(rpa.id, 0) AS is_removed,
        ')
            ->join($this->sharedDB->database.'.users u', 'paper_authors.author_id = u.id', 'left')
            ->join('removed_paper_authors rpa', 'paper_authors.id = rpa.paper_author_id', 'left');
    }

    public function GetJoinedUser($paper_id)
    {
        try {
            return $this->db->table('paper_authors')
                ->select('paper_authors.*, u.name as user_name, u.surname as user_surname, u.middle_name as user_middle, u.email as user_email, IFNULL(rpa.id, 0) as is_removed')
                ->join($this->sharedDB->database.'.users u', 'paper_authors.author_id = u.id', 'left')
                ->join('removed_paper_authors rpa', 'paper_authors.id = rpa.paper_author_id', 'left')
                ->where('paper_authors.paper_id', $paper_id)
                ->where('author_type', 'author')
                ->orderBy('paper_authors.author_order', 'asc')
//                ->where('is_active !=', '0')
                ->get();

            // return $this->findAll();
        } catch (\CodeIgniter\Database\Exceptions\DatabaseException $e) {
            // Log the error or display an error message
            return json_encode('Database error: ' . $e->getMessage());
        }
    }

    public function getPanelists()
    {
        try {
            // Returning the query builder instance, don't execute the query here
            return $this->table('paper_authors')
                ->select('paper_authors.*, u.name as user_name, u.surname as user_surname, IFNULL(rpa.id, 0) as is_removed')
                ->join($this->sharedDB->database.'.users u', 'paper_authors.author_id = u.id', 'left')
                ->join('removed_paper_authors rpa', 'paper_authors.id = rpa.paper_author_id', 'left')
                ->where('author_type', 'panelist');
        } catch (\CodeIgniter\Database\Exceptions\DatabaseException $e) {
            // Log the error or display an error message
            return json_encode('Database error: ' . $e->getMessage());
        }
    }

    public function getCoordinators($paper_id)
    {
        try {
            return $this->table('paper_authors')
                ->select('paper_authors.*, u.name as user_name, u.surname as user_surname, IFNULL(rpa.id, 0) as is_removed')
                ->join($this->sharedDB->database.'.users u', 'paper_authors.author_id = u.id', 'left')
                ->join('removed_paper_authors rpa', 'paper_authors.id = rpa.paper_author_id', 'left')
                ->where('paper_authors.paper_id', $paper_id)
                ->where('author_type', 'coordinator')
//                ->where('is_active !=', '0')
                ->get();

            // return $this->findAll();
        } catch (\CodeIgniter\Database\Exceptions\DatabaseException $e) {
            // Log the error or display an error message
            return json_encode('Database error: ' . $e->getMessage());
        }
    }

    public function getPresentingAuthors($paper_id = null)
    {
        try {
            $query =  $this->table('paper_authors')
                ->select('paper_authors.*, u.name as user_name, u.surname as user_surname,  IFNULL(rpa.id, 0) as is_removed')
                ->join($this->sharedDB->database.'.users u', 'paper_authors.author_id = u.id', 'left')
                ->join('removed_paper_authors rpa', 'paper_authors.id = rpa.paper_author_id', 'left');
                if($paper_id){
                    $query  ->where('paper_authors.paper_id', $paper_id);
                }
            $query->where('paper_authors.is_presenting_author', 'Yes')
                ->orderBy('author_order', 'asc');

                return $query;
        } catch (\CodeIgniter\Database\Exceptions\DatabaseException $e) {
            // Log the error or display an error message
            return json_encode('Database error: ' . $e->getMessage());
        }
    }

    public function getAuthors($paper_id = null)
    {
        try {
            $query =  $this->table('paper_authors')
                ->select('paper_authors.*, u.name as user_name, u.middle_name as user_middle, u.surname as user_surname,  IFNULL(rpa.id, 0) as is_removed')
                ->join($this->sharedDB->database.'.users u', 'paper_authors.author_id = u.id', 'left')
                ->join('removed_paper_authors rpa', 'paper_authors.id = rpa.paper_author_id', 'left');
            if($paper_id){
                $query  ->where('paper_authors.paper_id', $paper_id);
            }
            $query->orderBy('author_order', 'asc');

            return $query;
        } catch (\CodeIgniter\Database\Exceptions\DatabaseException $e) {
            // Log the error or display an error message
            return json_encode('Database error: ' . $e->getMessage());
        }
    }

    public function getAuthorsWithProfiles(array $paperIds): array
    {
        $builder = $this->withUserAndRemovalStatus();
        $builder->whereIn('paper_authors.paper_id', $paperIds);
        $builder->join($this->sharedDB->database. '.users_profile up', 'u.id = up.author_id', 'left');
        $builder->select('up.*');
        $builder->orderBy('author_order', 'asc');

        if(!empty($orderBy))
            $builder->orderBy($orderBy['column'], $orderBy['direction']);

        return $builder->findAll();
    }

    public function getAuthorsAcceptedByAdmin(int $paperId = null, array $orderBy = null): array
    {
        $builder = $this->withUserAndRemovalStatus();
        $builder->join('admin_abstract_acceptance aaa', 'paper_authors.paper_id = aaa.abstract_id', 'left');
        $builder->where('aaa.acceptance_confirmation', 1);

        if(!empty($orderBy))
            $builder->orderBy($orderBy['column'], $orderBy['direction']);

        $builder->orderBy('paper_authors.author_order', 'ASC');

        $builder->where('rpa.id', NULL); // Only include authors that are NOT removed

        if ($paperId !== null) {
            $builder->where('paper_authors.paper_id', $paperId);
        }

        return $builder->findAll();
    }

    public function getByAuthors($authorsIds): array {
        return $this->withUserAndRemovalStatus()
            ->join('papers p', 'paper_authors.paper_id = p.id', 'left')
            ->select('paper_authors.*, p.*')
            ->whereIn('author_id', $authorsIds)
            ->findAll();
    }
}