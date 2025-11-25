<?php

namespace App\Services;

use App\Models\CitiesModel;
use App\Models\CountriesModel;
use App\Models\InstitutionModel;
use App\Models\StatesModel;
use CodeIgniter\Config\BaseService;


class InstitutionServices extends BaseService
{

    public function __construct() {

    }

    function getInstitutionWithAddress($id){

        $InstitutionModel = (new InstitutionModel());
        $CitiesModel = (new CitiesModel());
        $CountriesModel = (new CountriesModel());
        $StateModel = (new StatesModel());

        $name = $InstitutionModel->getColumnValue('name', $id)['name'];
        $city_id = $InstitutionModel->getColumnValue('city_id', $id)['city_id'];
        $country_id = $InstitutionModel->getColumnValue('country_id', $id)['country_id'];
        $state_id = $InstitutionModel->getColumnValue('state_id', $id)['state_id'];

        $city = $CitiesModel->find($city_id);
        $country = $CountriesModel->find($country_id);
        $state = $StateModel->find($state_id);

        return [
            'name' => $name,
            'city' => $city['name'],
            'country' => $country['name'],
            'state' => $state['name'],
        ];

    }
}