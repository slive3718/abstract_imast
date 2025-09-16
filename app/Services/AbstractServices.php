<?php

namespace App\Services;

use App\Libraries\PhpMail;
use App\Models\AbstractTopicsModel;
use App\Models\EmailLogsModel;
use App\Models\LogsModel;
use CodeIgniter\Config\BaseService;
use CodeIgniter\Database\Config;
use Config\Services;
use http\Env\Request;

class AbstractServices extends BaseService
{

    private $request;
    public function __construct() {

        $this->request = \Config\Services::request();
    }

    function topics_column($topics_array){
        $abstract_topics_array = [];
        foreach ($topics_array as $primary_topic) {
            $topic_model = new AbstractTopicsModel();
            $abstract_topics_array[] = $topic_model->getTopicsColumn($primary_topic);  // Use $topic_id, not fixed primary_topic
        }
        return $abstract_topics_array;
    }
}