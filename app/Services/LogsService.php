<?php

namespace App\Services;

use App\Libraries\PhpMail;
use CodeIgniter\Config\BaseService;

class LogsService extends BaseService
{


    public function __construct() {

    }


    function email_declined_acceptance($abstract){
        $mail_result = (new PhpMail())->send(
            '',
            'rexterdayuta@gmail.com',
            'IMAST Declined Participation',
            'Assigned ID# '.$abstract['assigned_id'].' , Presenting Author:  '.session('name').' '.session('surname').' declined participation in the IMAST 2027 Meeting.',
        );

        if($mail_result->statusCode >= 200 && $mail_result->statusCode < 300 ){
            return true;
        }
        return false;
    }

}