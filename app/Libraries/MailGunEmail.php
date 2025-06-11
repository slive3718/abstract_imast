<?php

namespace App\Libraries;

use App\Models\PaperAuthorsModel;
use CodeIgniter\Email\Email;
use Config\Mailgun as MailgunConfig;
use Mailgun\HttpClient\HttpClientConfigurator;
use Mailgun\Mailgun;


class MailGunEmail{

    protected $email;
    public function __construct()
    {
        $this->email = new Email();
        $this->email->initialize(config('Email'));
    }

    public function send($from, $to, $subject, $message, $attachments = null)
    {
        try {
            $config = new MailgunConfig();

            // Initialize the Mailgun library with the HttpClientConfigurator
            $httpClientConfigurator = new HttpClientConfigurator();
            $httpClientConfigurator->setApiKey($config->apiKey);
            $mailgun = new Mailgun($httpClientConfigurator);

            // Set the domain
            $domain = $config->domain;

            // Send the email
            $response = ($mailgun->messages()->send($domain, [
                'from' => $from,
//                'to' => $to,
                'to'=>'rexterdayuta2@gmail.com', 'shannononeworld@gmail.com',
                'subject' => $subject,
                'html' => $message,
                'attachment' => $attachments
            ]));


            return $response;

//            if ($response->getStatusCode() == 200) {
//                // Email sent successfully
//                return 'success';
//            } else {
//                // Email sending failed
//                return 'error';
//            }

        }catch (\Exception $e){
            return $e->getMessage();
        }

    }

}