<?php
namespace App\Models;

use CodeIgniter\Model;

class PanelistPaperSubModel extends Model
{
    protected $table = 'panelist_paper_sub'; // Set the database table name
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


    public function getAssingedPanelist($paper_id){
        $query = $this->db->table('paper_authors')
            ->join('panelist_paper_sub pps', 'paper_authors.author_id = pps.panelist_id', 'left')
            ->join('users u', 'paper_authors.author_id = u.id', 'left')
            ->where('author_type', 'panelist');

        return $query;
    }
}