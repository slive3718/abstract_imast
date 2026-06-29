<?php

namespace App\Controllers;

use App\Models\PapersModel;

class UseOfAITools extends User
{

    public function index($abstract_id=null)
    {
        $abstract_details = (new PapersModel())->asArray()->find($abstract_id);

        $data['controller_name'] = $this->request->uri->getSegment(1);

        $header_data = [
            'title' => "Use of Artificial Intelligence (AI) Tools ",
        ];
//        print_r($abstract_details);exit;
        $data = [
            'id' => $this->request->uri->getSegment(3),
            'paper_id'=> $abstract_id,
            'abstract_details'=> $abstract_details,
            'previous_url' => previous_url(),
            'previous_page' => service('uri')->setURI(previous_url())->getSegment($this->setSegment(3))?? '',
        ];

        return
            view('event/common/header', $header_data).
            view('event/use_of_ai_tools',$data).
            view('event/common/footer')
            ;

    }

    public function save(){
        $post = $this->request->getPost();

        // Validate abstract_id exists
        if (empty($post['abstract_id'])) {
            return $this->response->setJson([
                'status' => 400,
                'message' => 'Abstract ID is required.'
            ]);
        }

        $aiUsed = $post['ai_used'] ?? null;
        $isAiUsed = ($aiUsed === '1');

        // Build base fields
        $fields = [
            'ai_terms' => isset($post['ai_terms']) ? 1 : 0,
            'ai_used' => $aiUsed,
        ];

        // Add AI fields conditionally
        if ($isAiUsed) {
            $purposes = isset($post['ai_purposes']) ? explode(',', $post['ai_purposes']) : [];
            $hasOtherPurpose = in_array('other', $purposes);

            $aiFields = [
                'ai_tools' => trim($post['ai_tools'] ?? ''),
                'ai_purposes' => $post['ai_purposes'] ?? '',
                'ai_other_purpose' => $hasOtherPurpose ? trim($post['ai_other_purpose'] ?? '') : null,
                'ai_attestation' => isset($post['ai_attestation']) ? 1 : 0,
                'ai_attestation_name' => trim($post['ai_attestation_name'] ?? ''),
                'ai_attestation_date' => $post['ai_attestation_date'] ?? null,
            ];

            $fields = array_merge($fields, $aiFields);
        } else {
            // Clear AI fields when not used
            $fields = array_merge($fields, [
                'ai_tools' => null,
                'ai_purposes' => null,
                'ai_other_purpose' => null,
                'ai_attestation' => 0,
                'ai_attestation_name' => null,
                'ai_attestation_date' => null,
            ]);
        }

        try {
            $result = (new PapersModel())->set($fields)->where('id', $post['abstract_id'])->update();

            if ($result) {
                $response = [
                    'status' => 200,
                    'message' => 'Disclosure saved successfully.'
                ];
            } else {
                $response = [
                    'status' => 500,
                    'message' => 'Failed to save disclosure.'
                ];
            }
        } catch (\Exception $e) {
            log_message('error', 'Save disclosure error: ' . $e->getMessage());
            $response = [
                'status' => 500,
                'message' => 'An error occurred while saving.'
            ];
        }

        return $this->response->setJson($response);
    }

}