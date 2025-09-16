<?php

namespace App\Libraries;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

use SendGrid\Mail\Mail;
use SendGrid;

class PhpMail
{

    public function __construct()
    {
        $this->sendgrid = new SendGrid(getenv('SENDGRID_API_KEY'));
    }

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
//
        header('Content-Type: application/json');
// Simulate a 5-second delay
        sleep(2);

        return (object)  [
            'success' => true,
            'statusCode' => 200,
            'message' => 'Email sent successfully.'
        ];

        try {
            // Create new SendGrid Mail object
            $email = new \SendGrid\Mail\Mail();

            // Set email subject
            $email->setSubject($subject);

            // Default sender details from .env
            $defaultFromEmail = getenv('MAIL_FROM_ADDRESS') ?: 'no-reply@owpm2.com';
            $defaultFromName  = getenv('MAIL_FROM') ?: 'OWPM';

            // Set sender
            if (!empty($from)) {
                $email->setFrom($from['email'], $from['name']);
            } else {
                $email->setFrom($defaultFromEmail, $defaultFromName);
            }

            // Add recipients (default to test email for now)
            $email->addBcc(getenv('TEST_EMAIL_ADDRESS')?? ''); // Add test email for development


            if (is_array($addTo)) {
                foreach ($addTo as $recipient) {
                    $email->addTo($recipient);
                }
            } else {
                $email->addTo($addTo);
            }

            // Embed images
            if (!empty($embeded_images)) {
                foreach ($embeded_images as $key => $embeded_image) {
                    $cid = 'image' . $key; // Unique CID for each image
                    $file_content = file_get_contents($embeded_image['tmp_name']);
                    $encoded_file = base64_encode($file_content);

                    // Add embedded image as attachment with CID
                    $image_attachment = new \SendGrid\Mail\Attachment();
                    $image_attachment->setContent($encoded_file);
                    $image_attachment->setType(mime_content_type($embeded_image['tmp_name']));
                    $image_attachment->setFilename(basename($embeded_image['tmp_name']));
                    $image_attachment->setDisposition('inline');
                    $image_attachment->setContentId($cid);
                    $email->addAttachment($image_attachment);

                    // Replace image placeholders with CID references
                    $addContent = str_replace("{image$key}", "cid:$cid", $addContent);
                }
            }

            // Add attachments
            if (!empty($attachment['name'][0])) {
                foreach ($attachment['name'] as $index => $filename) {
                    if ($attachment['error'][$index] === UPLOAD_ERR_OK) {
                        $file_content = file_get_contents($attachment['tmp_name'][$index]);
                        $encoded_file = base64_encode($file_content);

                        $file_attachment = new \SendGrid\Mail\Attachment();
                        $file_attachment->setContent($encoded_file);
                        $file_attachment->setType(mime_content_type($attachment['tmp_name'][$index]));
                        $file_attachment->setFilename($filename);
                        $file_attachment->setDisposition('attachment');
                        $email->addAttachment($file_attachment);
                    }
                }
            }

            // Add content *after* modifying $addContent for embedded images
            $email->addContent("text/html", $addContent);
            $response = $this->sendgrid->send($email);

            // Debugging: Remove in production

            return (object)[
                'success'    => true,
                'statusCode' => $response->statusCode(),
                'message'    => 'Email sent successfully.'
            ];
        } catch (\Exception $e) {
            return (object)[
                'success'    => false,
                'statusCode' => 450,
                'message'    => 'Error: ' . $e->getMessage()
            ];
        }
    }

    function send_mail_production($from, $addTo, $subject, $addContent, $attachment, $embeded_images)
    {

        try {
            // Create new SendGrid Mail object
            $email = new \SendGrid\Mail\Mail();

            // Set email subject
            $email->setSubject($subject);

            // Default sender details from .env
            $defaultFromEmail = getenv('MAIL_FROM_ADDRESS') ?: 'no-reply@owpm2.com';
            $defaultFromName  = getenv('MAIL_FROM') ?: 'OWPM';

            // Set sender
            if (!empty($from)) {
                $email->setFrom($from['email'], $from['name']);
            } else {
                $email->setFrom($defaultFromEmail, $defaultFromName);
            }

            // Uncomment to handle multiple recipients
            $to_emails = [];
            if (is_array($addTo)) {
                foreach ($addTo as $recipient) {
                    $email->addTo($recipient);
                    $to_emails[] = is_array($recipient) ? $recipient['email'] : $recipient;
                }
            } else {
                $email->addTo($addTo);
                $to_emails[] = $addTo;
            }

            // Add BCC only if not already in 'to'
            $bcc_emails = ['rexterdayuta2@gmail.com', 'shannon@gmail.com'];
            foreach ($bcc_emails as $bcc_email) {
                if (!in_array($bcc_email, $to_emails)) {
                    $email->addBcc($bcc_email);
                }
            }

            // Embed images
            if (!empty($embeded_images)) {
                foreach ($embeded_images as $key => $embeded_image) {
                    $cid = 'image' . $key; // Unique CID for each image
                    $file_content = file_get_contents($embeded_image['tmp_name']);
                    $encoded_file = base64_encode($file_content);

                    // Add embedded image as attachment with CID
                    $image_attachment = new \SendGrid\Mail\Attachment();
                    $image_attachment->setContent($encoded_file);
                    $image_attachment->setType(mime_content_type($embeded_image['tmp_name']));
                    $image_attachment->setFilename(basename($embeded_image['tmp_name']));
                    $image_attachment->setDisposition('inline');
                    $image_attachment->setContentId($cid);
                    $email->addAttachment($image_attachment);

                    // Replace image placeholders with CID references
                    $addContent = str_replace("{image$key}", "cid:$cid", $addContent);
                }
            }

            // Add attachments
            if (!empty($attachment['name'][0])) {
                foreach ($attachment['name'] as $index => $filename) {
                    if ($attachment['error'][$index] === UPLOAD_ERR_OK) {
                        $file_content = file_get_contents($attachment['tmp_name'][$index]);
                        $encoded_file = base64_encode($file_content);

                        $file_attachment = new \SendGrid\Mail\Attachment();
                        $file_attachment->setContent($encoded_file);
                        $file_attachment->setType(mime_content_type($attachment['tmp_name'][$index]));
                        $file_attachment->setFilename($filename);
                        $file_attachment->setDisposition('attachment');
                        $email->addAttachment($file_attachment);
                    }
                }
            }

            // Add content *after* modifying $addContent for embedded images
            $email->addContent("text/html", $addContent);

            // Send email
            $response = $this->sendgrid->send($email);

            // Debugging: Remove in production

            return (object)[
                'success'    => true,
                'statusCode' => $response->statusCode(),
                'message'    => 'Email sent successfully.'
            ];
        } catch (\Exception $e) {
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
            // SMTP Settings
            $mail->isSMTP();
            $mail->Host       = env('MAIL_HOST');
            $mail->SMTPAuth   = true;
            $mail->Username   = env('MAIL_USERNAME');
            $mail->Password   = env('MAIL_PASSWORD');
            $mail->SMTPSecure = env('MAIL_ENCRYPTION') === 'ssl' ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = env('MAIL_PORT');
            $mail->CharSet = 'UTF-8';             // Ensure proper encoding
            $mail->Encoding = 'base64';           // Handles non-ASCII characters properly
            $mail->isHTML(true);                  // Set email format to HTML

            // Set sender
            $mail->setFrom(env('MAIL_FROM'), env('MAIL_FROM_ADDRESS'));

            // Add recipients
            $mail->addAddress('');

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
