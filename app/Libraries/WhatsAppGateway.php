<?php

namespace App\Libraries;

use CodeIgniter\Config\Services;

class WhatsAppGateway
{
    protected $apiUrl;
    protected $apiKey;
    protected $sender;

    public function __construct()
    {
        $config = config('WhatsApp');
        
        $this->apiUrl = $config->apiUrl ?? 'https://api.fonnte.com/send';
        $this->apiKey = $config->apiKey ?? getenv('whatsapp.api_key');
        $this->sender = $config->sender ?? '';
    }

    public function send(string $target, string $message, ?string $file = null): array
    {
        $target = $this->formatPhone($target);

        $data = [
            'target' => $target,
            'message' => $message,
        ];

        if ($file) {
            $data['file'] = $file;
        }

        if (!empty($this->sender)) {
            $data['sender'] = $this->sender;
        }

        return $this->sendRequest($data);
    }

    public function sendBulk(array $targets, string $message): array
    {
        $results = [
            'success' => 0,
            'failed' => 0,
            'details' => []
        ];

        foreach ($targets as $target) {
            $result = $this->send($target, $message);
            
            if ($result['status']) {
                $results['success']++;
            } else {
                $results['failed']++;
            }
            
            $results['details'][] = [
                'target' => $target,
                'status' => $result['status'] ?? false,
                'response' => $result['response'] ?? null
            ];
        }

        return $results;
    }

    public function sendText(string $target, string $text): array
    {
        return $this->send($target, $text);
    }

    public function sendImage(string $target, string $caption, string $imageUrl): array
    {
        $target = $this->formatPhone($target);

        $data = [
            'target' => $target,
            'message' => $caption,
            'url' => $imageUrl,
            'filename' => 'image.jpg'
        ];

        return $this->sendRequest($data, 'image');
    }

    public function sendDocument(string $target, string $caption, string $documentUrl, string $filename = 'document.pdf'): array
    {
        $target = $this->formatPhone($target);

        $data = [
            'target' => $target,
            'message' => $caption,
            'url' => $documentUrl,
            'filename' => $filename
        ];

        return $this->sendRequest($data, 'document');
    }

    public function getDeviceStatus(): array
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.fonnte.com/device');
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: ' . $this->apiKey
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return [
            'status' => $httpCode === 200,
            'http_code' => $httpCode,
            'response' => json_decode($response, true)
        ];
    }

    protected function sendRequest(array $data, string $type = 'text'): array
    {
        $ch = curl_init();
        
        $url = $this->apiUrl;
        if ($type === 'image') {
            $url .= '/image';
        } elseif ($type === 'document') {
            $url .= '/document';
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: ' . $this->apiKey
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            return [
                'status' => false,
                'http_code' => 0,
                'response' => null,
                'error' => $error
            ];
        }

        $result = json_decode($response, true);

        return [
            'status' => $httpCode === 200 && ($result['status'] ?? false),
            'http_code' => $httpCode,
            'response' => $result
        ];
    }

    protected function formatPhone(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (substr($phone, 0, 2) === '08') {
            $phone = '62' . substr($phone, 2);
        }

        if (substr($phone, 0, 2) === '8') {
            $phone = '62' . $phone;
        }

        return $phone;
    }
}