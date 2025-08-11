<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Recaptcha extends BaseConfig
{
    public $siteKey;
    public $secretKey;
    public $threshold = 0.5;
    public $version = 'v3'; // Default to v3, can be changed to 'v2' if needed
    public function __construct()
    {
        parent::__construct();

        $this->siteKey = getenv('RECAPTCHA_SITE_KEY');
        $this->secretKey = getenv('RECAPTCHA_SECRET_KEY');
        $this->threshold = (float)(getenv('RECAPTCHA_THRESHOLD') ?? 0.5);
    }
}