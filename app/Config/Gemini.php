<?php

declare(strict_types=1);

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Gemini extends BaseConfig
{
    public string $apiKey = '';

    public string $baseUrl = 'https://generativelanguage.googleapis.com/v1beta';

    public function __construct()
    {
        parent::__construct();

        $this->apiKey = env('gemini.api_key', '');
    }
}