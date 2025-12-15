<?php

namespace App\Services;

class CMEServices extends CoreServices
{
    public function __construct()
    {

        $this->request = \Config\Services::request();
        $this->initializeModels();
    }

    function reviewFieldEntities($post): array
    {
        return [
            'answer1' => $post['relevantRelation'],
            'answer2' => $post['commercialBias1'],
            'answer3' => $post['commercialBias2'],
            'answer4' => $post['commercialBias3'],
            'answer5' => $post['commercialBias4'],
            'answer6' => $post['contentValidity1'],
            'answer7' => $post['contentValidity2'],
            'answer8' => $post['contentValidity3'],
            'answer9' => $post['contentValidity4'],
            'answer10' => $post['contentValidity5'],
            'answer11' => $post['contentValidity6'],
            'answer12' => $post['mitigation'],
            'e_signature' => $post['e_signature'],
            'request_text' => $post['request_text'] ?: ''
        ];
    }

}