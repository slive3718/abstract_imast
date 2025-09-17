<?php

namespace App\Services;

use App\Libraries\PhpMail;
use App\Models\AuthorAcceptanceModel;
use App\Models\EmailLogsModel;
use App\Models\LogsModel;
use CodeIgniter\Config\BaseService;
use CodeIgniter\Database\Config;
use Config\Services;
use http\Env\Request;

class AcceptanceService extends BaseService
{

    private $request;
    public function __construct() {

        $this->request = \Config\Services::request();

    }

    public function acceptance_message($acceptance_confirmation) :array{
//        $acceptanceModel =  (new AuthorAcceptanceModel())->where(['abstract_id'=>$abstract_id, 'author_id'=>session('user_id')])->first();
//        print_R($acceptanceModel);exit;
        if($acceptance_confirmation == 1){
            $acceptance_status = "Acceptance Form Successfully Submitted";
            $acceptance_message = "Thank you for confirming your participation in the 33rd International Meeting on Advanced Spine Techniques (IMAST), scheduled for April 15-18, 2026 in Toronto, ON, Canada.";
            $data['acceptance_status'] = $acceptance_status;
            $data['acceptance_message'] = $acceptance_message;
        }else{
            $acceptance_status = "Acceptance Form Successfully Submitted";
            $acceptance_message = "We regret that you are unable to participate in the 33rd International Meeting on Advanced Spine Techniques. ";
            $data['acceptance_status'] = $acceptance_status;
            $data['acceptance_message'] = $acceptance_message;
        }
        return $data ?? [];
    }


    function email_declined_acceptance($abstract) :bool {
        $mailData = [
            'from' => '',
            'addTo' => [env('EMAIL_BCC_ADDRESS'), env('APP_EMAIL_ADDRESS'), env('TEST_EMAIL_ADDRESS')],
            'subject' => 'IMAST Declined Participation',
            'addContent' => 'We regret that you are unable to participate in the 33rd International Meeting on Advanced Spine Techniques.',
            'abstract_id' => $abstract['id'],
            'abstract_assigned_id' => $abstract['assigned_id'],
            'user_agent' => $this->request->getUserAgent()->getBrowser(),
            'ip_address' => $this->request->getIPAddress(),
        ];
        $mail_result = (new PhpMail())->send(
            $mailData['from'],
            $mailData['addTo'],
            $mailData['subject'],
            $mailData['addContent'],
        );

        $this->log_declined_acceptance_email($mailData);

        if($mail_result->statusCode >= 200 && $mail_result->statusCode < 300 ){
            return true;
        }
        return false;
    }

    function log_declined_acceptance_email($mailData) :void{

        foreach ($mailData['addTo'] as $addTo){
            $email_logs_array = [
                'user_id' => session('user_id'),
                'add_to' =>   $addTo,
                'subject' =>  $mailData['subject'],
                'ref_1' => 'declined_acceptance',
                'add_content' => $mailData['addContent'],
                'send_from' => "Acceptance",
                'send_to' => "custom_user",
                'level' => "Info",
                'template_id' => 0,
                'paper_id' => $mailData['abstract_id'],
                'user_agent' => $mailData['user_agent'],
                'ip_address' => $mailData['ip_address']
            ];
            if(!(new EmailLogsModel())->saveToMailLogs($email_logs_array)){
                throw new \Error('Fail saving to logs.');
            }
        }
    }

}