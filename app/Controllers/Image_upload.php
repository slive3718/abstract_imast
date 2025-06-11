<?php

namespace App\Controllers;

use App\Models\Core\Api;
new \CodeIgniter\Publisher\Publisher();
class Image_upload extends User
{

    private Api $api;

    public function __construct()
    {

        parent::__construct();
        $this->event_uri = session('event_uri');

        $this->api = new Api();
        if (empty(session('email')) || session('email') == '') {
            return redirect()->to(base_url() . '/' . $this->event_uri . '/login');
            exit;
        }
    }


    public function index(){

    }

    public function view($event_uri = null, $abstract_id =null)  {
        $api2 = new Api();
        $_POST['abstract_id'] = $abstract_id;
        $_POST['user_id'] = session('user_id');
        $event = (new AbstractEventsModel())->first();
        $abstract_details = $api2->post("user/get_abstract_by_id/{$this->event_uri}", $_POST);

        if(!$event){
            return (new ErrorHandler($event))->errorPage();
        }

        $header_data = [
            'title' => "Image Upload"
        ];
        $data = [
            'event'=> $event,
            'abstract_id'=>$abstract_id,
            'abstract_details'=>$abstract_details->data[0]
        ];
        return
            view('event/common/header', $header_data).
            view('event/image_upload',$data).
            view('event/common/footer')
            ;
    }

    public function get($event_uri = null){

        $result = $this->api->post("user/get_abstract_uploads/{$this->event_uri}", $_POST);
        if(!$result->status){
            return (new ErrorHandler($result->data))->errorPage();
        }
        echo json_encode($result);
    }

    public function abstract_file_upload($event_uri){
//        print_r($_POST);exit;
        helper('text');

        $result = $this->api->post("user/check_abstract_uploads_count/{$this->event_uri}", $_POST);

        if($result->status==200)
        {
            $result = '';
            $randomStr = random_string('alnum', 30);
            $file_path = $this->uploadFile($event_uri, $_FILES, $randomStr, $_POST['abstract_id']);
            $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);

            $_POST['file_details'] = $_FILES;
            $_POST['file_path'] = $file_path;
            $_POST['file_extension'] = $ext;
            $_POST['random_name']=$randomStr;

            if($file_path){
                $result = $this->api->post("user/abstract_file_upload/{$this->event_uri}", $_POST);
                if(!$result->status){
                    return (new ErrorHandler($result->data))->errorPage();
                }
            }
        } 

        echo (json_encode(($result)));exit;
    }

    private function uploadFile($event_uri, $file, $randomStr, $abstract_id): string {
        $ext = pathinfo($file['file']['name'], PATHINFO_EXTENSION);
        $filePath = "/uploads"."/".$event_uri."/abstracts/".$abstract_id."/";
        $savePath = FCPATH.$filePath.$abstract_id."_".$randomStr.".".$ext;
        // upload image into the 'uploads' folder

        if(is_dir(FCPATH.$filePath)) {
            if(move_uploaded_file($file['file']['tmp_name'], $savePath)){
                return $filePath;
            }
            else{
                return '';
            }
        }else{
            if(mkdir(FCPATH.$filePath, 0777, true)){
                if(move_uploaded_file($file['file']['tmp_name'], $savePath)){
                    return $filePath;
                }
                else{
                    return '';
                }
            }else{
                return 'error';
            }

        }
    }
//
//    public function get_image(){
//      echo "<img src='".FCPATH."'">
//    }

    public function submit($event_uri){
//        print_r($_POST);exit;
        if($_POST['totalCharCount'] > 2500){
            echo json_encode(['status'=>'error', 'reason'=>'total character count exceed limit']);
        }
        $result = $this->api->post("user/submit_image_upload/{$this->event_uri}", $_POST);
        if(!$result->status){
            return (new ErrorHandler($result->data))->errorPage();
        }
        echo json_encode($result);
    }

    public function delete_uploaded(){
        $result = $this->api->post("user/delete_abstract_uploads/{$this->event_uri}", $_POST);
        if(!$result->status){
            return (new ErrorHandler($result->data))->errorPage();
        }
        echo json_encode($result);
    }

    public function no_upload(){
          $result = $this->api->post("user/submit_image_no_upload/{$this->event_uri}", $_POST);
        if(!$result->status){
            return (new ErrorHandler($result->data))->errorPage();
        }
        echo json_encode($result);
    }


}
