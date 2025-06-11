<?php

namespace App\Controllers;

use App\Models\InstitutionModel;

class Institution extends User
{
    public function __construct()
    {
        parent::__construct();

    }

    public function add_new()
    {
        $required_data = [
            'institution_name',
            'institution_country_id',
            'institution_state_id',
            'institution_city_id',
        ];

        $post = $this->request->getPost();

        // Validate required fields
        foreach ($required_data as $required) {
            if (!isset($post[$required]) || $post[$required] === '') {
                return $this->response->setStatusCode(400)->setJSON([
                    'error' => "{$required} is required"
                ]);
            }
        }

        // Check if institution already exists
        $existingInstitution = $this->checkExistInstitution($post);

        if (empty($existingInstitution)) {
            $result = (new InstitutionModel())->insert([
                'name'       => $post['institution_name'],
                'country_id' => $post['institution_country_id'],
                'state_id'   => $post['institution_state_id'],
                'city_id'    => $post['institution_city_id'],
                'date_time'  => date('Y-m-d H:i:s'),
            ]);

            if ($result) { // $result holds the inserted ID
                return $this->response->setStatusCode(200)->setJSON([
                    'institution_name' => $post['institution_name'],
                    'institution_id'   => $result
                ]);
            } else {
                return $this->response->setStatusCode(500)->setJSON([
                    'error' => 'Problem inserting to database'
                ]);
            }
        }

        return $this->response->setStatusCode(409)->setJSON([
            'error' => 'Institution already exists'
        ]);
    }


    private function checkExistInstitution($post) {
        $result = (new InstitutionModel())
            ->select('*')
            ->where('name', $post['institution_name'])
            ->where('country_id', $post['institution_country_id'])
            ->where('state_id', $post['institution_state_id'])
            ->where('city_id', $post['institution_city_id'])
            ->findAll();

        // Use count() to check if result is not empty
        if (count($result) > 0) {
            return $result;
        } else {
            return [];
        }
    }



}
