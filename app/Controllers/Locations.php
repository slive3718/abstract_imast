<?php

namespace App\Controllers;

use App\Models\Core\Api;
use App\Models\CitiesModel;
use App\Models\CountriesModel;
use App\Models\StatesModel;
use Config\Database;


class Locations extends User
{


    public function __construct()
    {
        parent::__construct();

    }

    public function get_countries(){
        $post = $this->request->getPost();
        $CountriesModel = (new CountriesModel());

        $result = $CountriesModel->like('name', $post['searchValue'])->findAll();
        if($result){
            return $this->response->setJSON(['status'=> 200, 'data'=>$result, 'reason'=>'success']);
        }
        exit;
    }

    public function get_country_states(){
        $post = $this->request->getPost();

        $StatesModel = (new StatesModel());

        $result = $StatesModel->like('name', $post['searchValue'])->findAll();

        if($result){
            return $this->response->setJSON(['status'=> 200, 'data'=>$result, 'reason'=>'success']);
        }
        exit;
    }

    public function get_state_cities(){
        $post = $this->request->getPost();
        $CitiesModel = (new CitiesModel());
        $result = $CitiesModel->where('country_id', $post['country_id'])->where('state_id', $post['state_id'])->like('name', $post['searchValue'])->findAll();
        if($result){
            return $this->response->setJSON(['status'=> 200, 'data'=>$result, 'reason'=>'success']);
        }
        exit;
    }

    public function get_all_cities() {
        $post = $this->request->getPost();
        $searchValue = $post['searchValue'] ?? '';

        $CitiesModel = new CitiesModel();
        $this->db = $CitiesModel->db;
        // Use parameter binding to prevent SQL injection
        $searchValue = $this->request->getPost('searchValue');
        $search = '%' . $searchValue . '%';

        $sql = 'SELECT cities.*, 
               CONCAT(cities.name, ", ", IFNULL(s.name, ""), ", ", co.name) AS completeAddress, 
               IFNULL(s.name, "") AS state_name, 
               co.name AS country_name 
        FROM cities 
        JOIN countries co ON cities.country_id = co.id 
        LEFT JOIN states s ON cities.state_id = s.id 
        WHERE cities.name LIKE ? 
        ORDER BY 
            CASE 
                WHEN cities.name LIKE "%' . $searchValue . '%" THEN 1
                ELSE 2
            END,
            cities.name ASC';

        $params = [$search];
        $query = $this->db->query($sql, $params);
        $result = $query->getResult();

        if ($result) {
            return $this->response->setJSON(['status' => 200, 'data' => $result, 'reason' => 'success']);
        } else {
            return $this->response->setJSON(['status' => 404, 'data' => [], 'reason' => 'No data found']);
        }
    }

}