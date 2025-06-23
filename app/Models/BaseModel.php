<?php
namespace App\Models;

use CodeIgniter\Model;

class BaseModel extends Model
{

    protected $defaultDB = null;
    protected $sharedDB = null;

    public function __construct()
    {
        parent::__construct();
        $this->defaultDB = \Config\Database::connect();
        $this->sharedDB = \Config\Database::connect('shared');
    }


}