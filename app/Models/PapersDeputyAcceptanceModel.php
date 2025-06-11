<?php
namespace App\Models;

use CodeIgniter\Model;

class PapersDeputyAcceptanceModel extends Model
{
    protected $table = 'paper_deputy_acceptance';

    protected $allowedFields;
    protected $primaryKey = 'id';

    protected $returnType = 'array';
    private $error;

    public function __construct()
    {
        parent::__construct();

        // Fetch the list of fields from the database table
        $fields = $this->db->getFieldNames($this->table);

        // Set the allowed fields to the fetched list
        $this->allowedFields = $fields;
    }
}