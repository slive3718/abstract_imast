<?php

namespace App\Libraries;

use App\Models\EmailLogsModel;
use App\Models\UserModel;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


class PhpMail
{
    public function send($from, $addTo, $subject, $addContent, $attachment = null)
    {
//        header('Content-Type: application/json');
//
//// Simulate a 5-second delay
//        sleep(2);
//
//        return (object)  [
//            'success' => true,
//            'statusCode' => 200,
//            'message' => 'Email sent successfully.'
//        ];

        // Create a From object with the provided sender information
        $mail = new PHPMailer(true); // Passing true enables exceptions

        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host       = 'owpm2.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'no-reply@owpm2.com';
            $mail->Password   = 'owpm2_email#';
            $mail->SMTPSecure = 'ssl';
            $mail->Port       = 465;

            $from_email = ['email'=>'afs@owpm2.com', 'name'=>'AFS'];
            $mail->setFrom( $from_email['email'], $from_email['name']);

            // Add recipients
//            $mail->addAddress('rexterdayuta@gmail.com');
            if(is_array($addTo)) {
                foreach ($addTo as $recipient) {
                    $mail->addAddress($recipient);
                }
            }else{
                $mail->addAddress($addTo);
            }

            $mail->addCC('shannononeworld@gmail.com');
            $mail->addBCC('rexterdayuta@gmail.com');
            // Email subject
            $mail->Subject = $subject;

            // Email content
            $mail->isHTML(true);
            $mail->Body = $addContent;

            // Attachments
            if (!empty($attachment['name'][0])) {
                for ($i = 0; $i < count($attachment['name']); $i++) {
                    // Ensure there are no upload errors
                    if ($attachment['error'][$i] === UPLOAD_ERR_OK) {
                        $mail->addAttachment($attachment['tmp_name'][$i], $attachment['name'][$i]);
                    }
                }
            }

            // Send email
            $mail->send();

           return (object)  [
                'success' => true,
                'statusCode' => 200,
                'message' => 'Email sent successfully.'
            ];
        } catch (Exception $e) {
           return (object)  [
                'success' => true,
                'statusCode' => 450,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }


    public function testMail(){
        $mail = new PHPMailer(true); // Passing true enables exceptions

        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host       = 'owpm2.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'no-reply@owpm2.com';
            $mail->Password   = 'owpm2_email#';
            $mail->SMTPSecure = 'ssl';
            $mail->Port       = 465;

            // Set sender
            $mail->setFrom('afs@owpm2.com', 'AFS');

            // Add recipients

            $mail->addAddress('rexterdayuta@gmail.com');

            // Email subject
            $mail->Subject = 'TEST SUBJECT';

            // Email content
            $mail->isHTML(true);
            $mail->Body = "TEST BODY";

            // Send email
            $mail->send();
//            print_r($mail->send());
            return (object)  [
                'success' => true,
                'statusCode' => 200,
                'message' => 'Email sent successfully.'
            ];
        } catch (Exception $e) {
            return (object)  [
                'success' => true,
                'statusCode' => 450,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }
}
