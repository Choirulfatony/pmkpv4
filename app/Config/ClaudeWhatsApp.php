<?php

namespace Config;

class ClaudeWhatsApp extends BaseConfig
{
    public $apiKey = '';
    public $model = 'claude-3-haiku-20240307';
    public $maxTokens = 1024;
    public $enabled = false;
}