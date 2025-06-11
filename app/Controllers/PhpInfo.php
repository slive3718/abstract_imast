<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class Phpinfo extends Controller
{
    public function index()
    {
        // Display the PHP configuration information
        phpinfo();
    }
}
