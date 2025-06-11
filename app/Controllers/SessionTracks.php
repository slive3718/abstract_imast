<?php

namespace App\Controllers;

use App\Models\TracksModel;

class SessionTracks extends BaseController
{

    public function __construct()
    {

    }

    function get(){
        return (new TracksModel())->findAll();
    }

    function getJson(){
        return $this->response->setJSON($this->get());
    }
}
