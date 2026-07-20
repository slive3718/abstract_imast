<?php
namespace App\Controllers;

use App\Libraries\PhpMail;
use App\Models\EmailLogsModel;

class EmailController extends BaseController
{
    public function __construct()
    {
        // Load necessary models or libraries if needed
    }

    public function send_support_mail()
    {

        // Initialize validation service
        $validation = \Config\Services::validation();

        // Set validation rules
        $rules = [
            'fname' => 'required|max_length[100]',
            'lname' => 'required|max_length[100]',
            'email' => 'required|valid_email|max_length[100]',
            'message' => 'required|min_length[10]',
            'support_recaptcha' => 'required'
        ];

        // Custom error messages
        $messages = [
            'fname' => [
                'required' => 'First name is required',
                'max_length' => 'First name cannot exceed 100 characters'
            ],
            'lname' => [
                'required' => 'Last name is required',
                'max_length' => 'Last name cannot exceed 100 characters'
            ],
            'email' => [
                'required' => 'Email is required',
                'valid_email' => 'Please provide a valid email address',
                'max_length' => 'Email cannot exceed 100 characters'
            ],
            'message' => [
                'required' => 'Message is required',
                'min_length' => 'Message should be at least 10 characters long'
            ],
            'support_recaptcha' => [
                'required' => 'reCAPTCHA verification is required'
            ]
        ];

        // Run validation
        if (!$this->validate($rules, $messages)) {
            return $this->response->setJSON([
                'status' => 400,
                'message' => 'Validation failed',
                'errors' => $validation->getErrors()
            ]);
        }


        // Verify reCAPTCHA
        if (!validate_recaptcha($this->request->getPost('support_recaptcha'), 'support_form')) {
            return $this->response->setJSON([
                'status' => 400,
                'message' => 'reCAPTCHA verification failed. Please reload this page and try again.'
            ]);
        }

        // Get post data
        $post = $this->request->getPost();

        try {
            // Send email
            $sendMail = new PhpMail();
            $from = ['email' => 'imast@owpm2.com', 'name' => 'AFS 2026'];
            $subject = 'Support Request From ' . $post['fname'] . " " . $post['lname'];
            $message = "First Name: " . $post['fname'] . "<br>";
            $message .= "Last Name: " . $post['lname'] . "<br>";
            $message .= "Email: " . $post['email'] . "<br>";
            $message .= "Message: " . $post['message'] . "<br>";
            $to = ['rexterdayuta@gmail.com', 'shannonmorton544@gmail.com'];

            $response = $sendMail->send($from, $to, $subject, $message);

            // Save to email logs
            $email_logs_array = [
                'user_id' => session('user_id') ?? '',
                'add_to' => json_encode($to),
                'subject' => $subject,
                'ref_1' => 'support',
                'add_content' => $message,
                'send_from' => "Submitter",
                'send_to' => "Author",
                'level' => "Info",
                'template_id' => 0,
                'paper_id' => $post['abstract_id'] ?? NULL,
                'user_agent' => $this->request->getUserAgent()->getBrowser(),
                'ip_address' => $this->request->getIPAddress(),
                'status' => ($response->statusCode == 200) ? 'Success' : 'Failed'
            ];

            (new EmailLogsModel())->saveToMailLogs($email_logs_array);

            return $this->response->setJSON([
                'status' => $response->statusCode,
                'message' => $response->message
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Support email error: ' . $e->getMessage());

            return $this->response->setJSON([
                'status' => 500,
                'message' => 'An error occurred while processing your request'
            ]);
        }
    }
}