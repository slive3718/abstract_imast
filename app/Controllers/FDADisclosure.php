<?php

namespace App\Controllers;

use App\Models\PapersModel;

class FDADisclosure extends User
{

    public function __construct()
    {
        parent::__construct();
        if (empty(session('email')) || session('email') == '') {
            return redirect()->to(base_url() . '/login');
            exit;
        }
    }


    public function view_fda($abstract_id=null)
    {
        $abstract_details = (new PapersModel())->asArray()->find($abstract_id);

        $data['controller_name'] = $this->request->uri->getSegment(1);

        $header_data = [
            'title' => "FDA Disclosure",
        ];

        $data = [
            'id' => $this->request->uri->getSegment(3),
            'paper_id'=> $abstract_id,
            'abstract_details'=> $abstract_details,
            'previous_url' => previous_url(),
            'previous_page' => service('uri')->setURI(previous_url())->getSegment($this->setSegment(3))?? '',
        ];

        return
            view('event/common/header', $header_data).
            view('event/fda_disclosure',$data).
            view('event/common/footer')
            ;

    }

    public function save_fda_disclosure(){
        $post = $this->request->getPost();


        $fda_fields = [
            'is_fda_accepted' => isset($post['is_fda_accepted']) ? 1 : 0,
            'fda_unapproved_uses' => $post['unapproved_publication'] ?? null,
            'fda_discuss_product_name' => $post['discuss_product_name'] ?? null,
            'fda_unapproved_explanation' => $post['fda_unapproved_explanation'] ?? '',
            'fda_product_name_explanation' => $post['fda_product_name_explanation'] ?? '',
        ];
        $result = (new PapersModel())->set($fda_fields)->where('id', $post['abstract_id'])->update();

        if($result){
            $response = [
                'status' => 200,
                'message' => 'FDA Disclosure saved successfully.'
            ];
        } else {
            $response = [
                'status' => 500,
                'message' => 'Failed to save FDA Disclosure.'
            ];
        }

        return $this->response->setJson($response);
    }

}