<?php
namespace App\Models;

use CodeIgniter\Database\ConnectionInterface;
use CodeIgniter\Model;
use CodeIgniter\Validation\ValidationInterface;

class PaperUploadsModel extends Model
{
    protected $table = 'paper_uploads';
    protected $primaryKey = 'id';
    protected $allowedFields;
    protected $returnType = 'array';

    public function __construct()
    {
        parent::__construct();

        // Fetch the list of fields from the database table
        $fields = $this->db->getFieldNames($this->table);

        // Set the allowed fields to the fetched list
        $this->allowedFields = $fields;
    }
    // protected $allowedFields = ['title', 'description'];

}