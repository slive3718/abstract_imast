<?php

use Config\Recaptcha;

if (!function_exists('recaptcha')) {
    /**
     * reCAPTCHA script tag
     */
    function recaptcha(string $action,  string $recaptchaName = ''): string
    {
        $config = new Recaptcha();
        return '<script src="https://www.google.com/recaptcha/api.js?render=' . $config->siteKey . '"></script>
                <script>
                    grecaptcha.ready(function() {
                        grecaptcha.execute("' . $config->siteKey . '", {action: "' . $action . '"})
                        .then(function(token) {
                            document.getElementById("'.$recaptchaName.'").value = token;
                        });
                    });
                </script>
                <input type="hidden" id="'.$recaptchaName.'" name="'.$recaptchaName.'">';
    }
}

if (!function_exists('validate_recaptcha')) {
    /**
     * Validate reCAPTCHA response
     */
    function validate_recaptcha(string $response, string $action): bool
    {
        $config = new Recaptcha();

        // Check if response is empty
        if (empty($response)) {
            log_message('error', 'Empty reCAPTCHA response');
            return false;
        }

        $client = \Config\Services::curlrequest();

        try {
            $response = $client->request('POST', 'https://www.google.com/recaptcha/api/siteverify', [
                'form_params' => [
                    'secret'   => $config->secretKey,
                    'response' => $response,
                    'remoteip' => service('request')->getIPAddress()
                ],
                'timeout' => 5 // Set timeout to 5 seconds
            ]);

            $result = json_decode($response->getBody());

            // Debug logging (remove in production)
            log_message('debug', 'reCAPTCHA verification result: ' . print_r($result, true));

            // Validate the response
            if (!isset($result->success)) {
                log_message('error', 'Invalid reCAPTCHA response format');
                return false;
            }

            // Return validation result
            return $result->success &&
                isset($result->action) &&
                $result->action === $action &&
                isset($result->score) &&
                $result->score >= $config->threshold;

        } catch (\Exception $e) {
            log_message('error', 'reCAPTCHA validation error: ' . $e->getMessage());
            return false;
        }
    }
}