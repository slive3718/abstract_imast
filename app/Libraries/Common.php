<?php

namespace App\Libraries;

use Config\Database;



class Common
{
    protected $default_db;
    protected $shared_db;

    function initializeDatabase(){
        $this->default_db = \Config\Database::connect();
        $this->shared_db = \Config\Database::connect('shared');
    }
}