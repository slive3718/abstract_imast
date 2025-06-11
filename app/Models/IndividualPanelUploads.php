<?php
namespace App\Models;

use CodeIgniter\Model;

class IndividualPanelUploads extends Model
{
    protected $table = 'individual_panel_uploads'; // Set the database table name
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

}