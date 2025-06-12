<?php
namespace App\Models;

use CodeIgniter\Model;

class PaperAuthorsModel extends Model
{
    protected $table = 'paper_authors'; // Set the database table name
    protected $primaryKey = 'id'; // Set the primary key field

    // Define the fields that can be manipulated (for insert and update)
    protected $allowedFields;

    // Optionally, set return type as 'object' for all queries by default
    protected $returnType = 'array';
    protected $useAutoFields = true;
    private $shared_db_name;

    public function __construct()
    {
        parent::__construct();
        $this->allowedFields = $this->db->getFieldNames($this->table);
        $this->shared_db_name = ENV('abstract_suit_shared_db', 'abstract_suit_shared_db');
    }

    public function GetJoinedUser($paper_id)
    {
        try {
            return $this->db->table('paper_authors')
                ->select('paper_authors.*, u.name as user_name, u.surname as user_surname, u.middle_name as user_middle, u.email as user_email, IFNULL(rpa.id, 0) as is_removed')
                ->join($this->shared_db_name.'.users u', 'paper_authors.author_id = u.id', 'left')
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
                ->join('abstract_suit_shared_db.users u', 'paper_authors.author_id = u.id', 'left')
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
                ->join('abstract_suit_shared_db.users u', 'paper_authors.author_id = u.id', 'left')
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
                ->join('abstract_suit_shared_db.users u', 'paper_authors.author_id = u.id', 'left')
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


}