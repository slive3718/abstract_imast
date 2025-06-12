<?php

namespace App\Controllers;

use App\Models\Core\Api;
use App\Models\CitiesModel;
use App\Models\CountriesModel;
use App\Models\StatesModel;


class Locations extends User
{

    private Api $api;
    public function __construct()
    {
        parent::__construct();
        $this->event_uri = session('event_uri');

        $this->api = new Api();
        if(empty(session('email')) || session('email') == ''){
            return redirect()->to(base_url().'/'.$this->event_uri.'/login');
            exit;
        }
    }

    public function get_countries(){
        $post = $this->request->getPost();
        $CountriesModel = (new CountriesModel());

        $result = $CountriesModel->like('name', $post['searchValue'])->findAll();
        if($result){
            echo (json_encode(($result)));
        }
        exit;
    }

    public function get_country_states(){
        $post = $this->request->getPost();

        $StatesModel = (new StatesModel());

        $result = $StatesModel->like('name', $post['searchValue'])->findAll();

        if($result){
            echo (json_encode(($result)));
        }
        exit;
    }

    public function get_state_cities(){
        $post = $this->request->getPost();
        $CitiesModel = (new CitiesModel());
        $result = $CitiesModel->where('country_id', $post['country_id'])->where('state_id', $post['state_id'])->like('name', $post['searchValue'])->findAll();
        if($result){
            echo (json_encode(($result)));
        }
        exit;
    }

    public function get_all_cities(){
        $post = $this->request->getPost();
        $CitiesModel = (new CitiesModel());
        $result = $CitiesModel->query('SELECT *, CONCAT(cities.name, ", ", s.name, ", ", co.name) AS completeAddress, s.name AS state_name, co.name AS country_name FROM cities 
                                        JOIN countries co ON cities.country_id = co.id 
                                        JOIN states s ON cities.state_id = s.id 
                                        WHERE CONCAT(cities.name, ", ", s.name, ", ", co.name) LIKE "%'.$post['searchValue'].'%" LIMIT 100')->getResult();

        if($result){
            echo (json_encode(($result)));
        }
        exit;
    }
}