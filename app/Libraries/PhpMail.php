<?php

namespace App\Libraries;

use App\Models\EmailLogsModel;
use App\Models\UserModel;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


class PhpMail
{
    public function send($from, $addTo, $subject, $addContent, $attachment = null, $embeded_images = null )
    {
        $isProd =  (env('CI_ENVIRONMENT') === 'production');

        if ($isProd) {
            return $this->send_mail_production($from, $addTo, $subject, $addContent, $attachment, $embeded_images);
        }else{
            return $this->send_mail_dev($from, $addTo, $subject, $addContent, $attachment, $embeded_images);
        }
    }

    function send_mail_dev($from, $addTo, $subject, $addContent, $attachment, $embeded_images)
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

        $mail = new PHPMailer(true); // Enable exceptions

        try {
            // SMTP Settings
            $mail->isSMTP();
            $mail->Host       = 'owpm2.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'no-reply@owpm2.com';
            $mail->Password   = 'owpm2_email#';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // Use PHPMailer constant
            $mail->Port       = 465;
            $mail->CharSet = 'UTF-8';             // Ensure proper encoding
            $mail->Encoding = 'base64';           // Handles non-ASCII characters properly
            $mail->isHTML(true);                  // Set email format to HTML

            // Default sender details
            $defaultFromEmail = env('MAIL_FROM_ADDRESS');
            $defaultFromName  = env('MAIL_FROM');

            // Set sender
            if (!empty($from)) {
                $mail->setFrom($from['email'], $from['name']);
            } else {
                $mail->setFrom($defaultFromEmail, $defaultFromName);
            }

            // Add recipients

            $mail->addAddress('rexterdayuta@gmail.com');  // for testing default

//            if (is_array($addTo)) {
//                foreach ($addTo as $recipient) {
//                    $mail->addAddress($recipient);
//                }
//            } else {
//                $mail->addAddress($addTo);
//            }

            // CC & BCC
//            $mail->addCC('shannononeworld@gmail.com');
//            $mail->addBCC('rexterdayuta@gmail.com');

            // Email Subject & Body
            $mail->isHTML(true);
            $mail->Subject = $subject;

            // Embed the images dynamically
            $cid_references = [];

            if (!empty($embeded_images)) {
                foreach ($embeded_images as $key => $embeded_image) {
                    $cid = 'cid:image' . $key; // Unique CID for each image
                    $mail->AddEmbeddedImage($embeded_image['tmp_name'], $cid, basename($embeded_image['tmp_name'])); // Embed image and associate with CID
                    $cid_references[$key] = $cid; // Save CID references to use in the email body
                }
            }

            // Update the body content to reference the embedded images
            foreach ($cid_references as $key => $cid) {
                // Replace image placeholders with the CID references
                $addContent = str_replace("{image$key}", "cid:$cid", $addContent);
            }


            $mail->Body    = $addContent;

            // Attachments
            if (!empty($attachment['name'][0])) {
                foreach ($attachment['name'] as $index => $filename) {
                    if ($attachment['error'][$index] === UPLOAD_ERR_OK) {
                        $mail->addAttachment($attachment['tmp_name'][$index], $filename);
                    }
                }
            }

            // Send email
            $mail->send();

            return (object)[
                'success'    => true,
                'statusCode' => 200,
                'message'    => 'Email sent successfully.'
            ];
        } catch (Exception $e) {
            return (object)[
                'success'    => false,
                'statusCode' => 450,
                'message'    => 'Error: ' . $e->getMessage()
            ];
        }
    }

    function send_mail_production($from, $addTo, $subject, $addContent, $attachment, $embeded_images)
    {

        $mail = new PHPMailer(true); // Enable exceptions

        try {
            // SMTP Settings
            $mail->isSMTP();
            $mail->Host       = 'owpm2.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'no-reply@owpm2.com';
            $mail->Password   = 'owpm2_email#';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // Use PHPMailer constant
            $mail->Port       = 465;
            $mail->CharSet = 'UTF-8';             // Ensure proper encoding
            $mail->Encoding = 'base64';           // Handles non-ASCII characters properly
            $mail->isHTML(true);                  // Set email format to HTML

            // Default sender details
            $defaultFromEmail = 'imast@owpm2.com';
            $defaultFromName  = 'IMAST 2026';

            // Set sender
            if (!empty($from)) {
                $mail->setFrom($from['email'], $from['name']);
            } else {
                $mail->setFrom($defaultFromEmail, $defaultFromName);
            }

            // Add recipients
            if (is_array($addTo)) {
                foreach ($addTo as $recipient) {
                    $mail->addAddress($recipient);
                }
            } else {
                $mail->addAddress($addTo);
            }

            // CC & BCC
            $mail->addCC('shannononeworld@gmail.com');
            $mail->addBCC('rexterdayuta@gmail.com');

            // Email Subject & Body
            $mail->isHTML(true);
            $mail->Subject = $subject;

            // Embed the images dynamically
            $cid_references = [];

            if (!empty($embeded_images)) {
                foreach ($embeded_images as $key => $embeded_image) {
                    $cid = 'cid:image' . $key; // Unique CID for each image
                    $mail->AddEmbeddedImage($embeded_image['tmp_name'], $cid, basename($embeded_image['tmp_name'])); // Embed image and associate with CID
                    $cid_references[$key] = $cid; // Save CID references to use in the email body
                }
            }

            // Update the body content to reference the embedded images
            foreach ($cid_references as $key => $cid) {
                // Replace image placeholders with the CID references
                $addContent = str_replace("{image$key}", "cid:$cid", $addContent);
            }
            
            $mail->Body    = $addContent;

            // Attachments
            if (!empty($attachment['name'][0])) {
                foreach ($attachment['name'] as $index => $filename) {
                    if ($attachment['error'][$index] === UPLOAD_ERR_OK) {
                        $mail->addAttachment($attachment['tmp_name'][$index], $filename);
                    }
                }
            }

            // Send email
            $mail->send();

            return (object)[
                'success'    => true,
                'statusCode' => 200,
                'message'    => 'Email sent successfully.'
            ];
        } catch (Exception $e) {
            return (object)[
                'success'    => false,
                'statusCode' => 450,
                'message'    => 'Error: ' . $e->getMessage()
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
            $mail->setFrom(env('MAIL_FROM'), env('MAIL_FROM_ADDRESS'));

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
