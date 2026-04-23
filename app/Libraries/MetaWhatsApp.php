<?php

namespace App\Libraries;

class MetaWhatsApp
{
    protected $accessToken;
    protected $phoneNumberId;
    protected $apiVersion;
    protected $businessAccountId;
    protected $endpoint;

    public function __construct()
    {
        $config = config('MetaWhatsApp');
        
        $this->accessToken = $config->accessToken ?? getenv('meta.whatsapp.access_token');
        $this->phoneNumberId = $config->phoneNumberId ?? getenv('meta.whatsapp.phone_number_id');
        $this->businessAccountId = $config->businessAccountId ?? getenv('meta.whatsapp.business_account_id');
        $this->apiVersion = $config->apiVersion ?? 'v18.0';
        $this->endpoint = "https://graph.facebook.com/{$this->apiVersion}";
    }

    public function sendText(string $recipient, string $message): array
    {
        $data = [
            'messaging_product' => 'whatsapp',
            'to' => $this->formatPhone($recipient),
            'type' => 'text',
            'text' => [
                'body' => $message,
                'preview_url' => false
            ]
        ];

        return $this->sendMessage($data);
    }

    public function sendImage(string $recipient, string $imageUrl, ?string $caption = null): array
    {
        $data = [
            'messaging_product' => 'whatsapp',
            'to' => $this->formatPhone($recipient),
            'type' => 'image',
            'image' => [
                'link' => $imageUrl,
                'caption' => $caption ?? ''
            ]
        ];

        return $this->sendMessage($data);
    }

    public function sendDocument(string $recipient, string $documentUrl, string $filename, ?string $caption = null): array
    {
        $data = [
            'messaging_product' => 'whatsapp',
            'to' => $this->formatPhone($recipient),
            'type' => 'document',
            'document' => [
                'link' => $documentUrl,
                'filename' => $filename,
                'caption' => $caption ?? ''
            ]
        ];

        return $this->sendMessage($data);
    }

    public function sendTemplate(string $recipient, string $templateName, array $components = []): array
    {
        $data = [
            'messaging_product' => 'whatsapp',
            'to' => $this->formatPhone($recipient),
            'type' => 'template',
            'template' => [
                'name' => $templateName,
                'language' => [
                    'code' => 'id_ID'
                ],
                'components' => $components
            ]
        ];

        return $this->sendMessage($data);
    }

    public function sendButton(string $recipient, string $message, array $buttons): array
    {
        $buttonsComponent = [];
        foreach ($buttons as $index => $button) {
            $buttonsComponent[] = [
                'type' => 'reply',
                'reply' => [
                    'id' => 'btn_' . ($index + 1),
                    'title' => substr($button, 0, 25)
                ]
            ];
        }

        $data = [
            'messaging_product' => 'whatsapp',
            'to' => $this->formatPhone($recipient),
            'type' => 'interactive',
            'interactive' => [
                'type' => 'button',
                'body' => [
                    'text' => $message
                ],
                'action' => [
                    'buttons' => $buttonsComponent
                ]
            ]
        ];

        return $this->sendMessage($data);
    }

    public function sendListMessage(string $recipient, string $message, array $rows): array
    {
        $listRows = [];
        foreach ($rows as $row) {
            $listRows[] = [
                'id' => $row['id'] ?? uniqid(),
                'title' => substr($row['title'], 0, 25),
                'description' => substr($row['description'] ?? '', 0, 72)
            ];
        }

        $data = [
            'messaging_product' => 'whatsapp',
            'to' => $this->formatPhone($recipient),
            'type' => 'interactive',
            'interactive' => [
                'type' => 'list',
                'header' => [
                    'type' => 'text',
                    'text' => 'Menu Pilihan'
                ],
                'body' => [
                    'text' => $message
                ],
                'action' => [
                    'button' => 'Pilih',
                    'sections' => [
                        [
                            'title' => 'Pilihan',
                            'rows' => $listRows
                        ]
                    ]
                ]
            ]
        ];

        return $this->sendMessage($data);
    }

    public function getMessageStatus(string $messageId): array
    {
        $ch = curl_init();
        
        $url = "{$this->endpoint}/{$messageId}";
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->accessToken
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return [
            'status' => $httpCode === 200,
            'http_code' => $httpCode,
            'data' => json_decode($response, true)
        ];
    }

    public function getPhoneNumberSettings(): array
    {
        $ch = curl_init();
        
        $url = "{$this->endpoint}/{$this->phoneNumberId}";
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->accessToken
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return [
            'status' => $httpCode === 200,
            'data' => json_decode($response, true)
        ];
    }

    public function registerWebhook(string $verifyToken): array
    {
        $ch = curl_init();
        
        $url = "{$this->endpoint}/{$this->phoneNumberId}/webhooks";
        $params = http_build_query([
            'access_token' => $this->accessToken
        ]);
        
        curl_setopt($ch, CURLOPT_URL, "{$url}?{$params}");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            'verify_token' => $verifyToken,
            'url' => base_url('webhook/whatsapp')
        ]));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }

    public function markAsRead(string $messageId): array
    {
        $ch = curl_init();
        
        $url = "{$this->endpoint}/{$this->phoneNumberId}/messages";
        
        $data = [
            'messaging_product' => 'whatsapp',
            'status' => 'read',
            'message_id' => $messageId
        ];
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->accessToken,
            'Content-Type: ' . 'application/json'
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return [
            'status' => $httpCode === 200,
            'http_code' => $httpCode
        ];
    }

    public function sendMediaFile(string $recipient, string $filePath, string $mimeType, ?string $caption = null): array
    {
        $fileType = $this->getFileType($mimeType);
        
        $ch = curl_init();
        $url = "{$this->endpoint}/{$this->phoneNumberId}/messages";
        
        $postFields = [
            'messaging_product' => 'whatsapp',
            'to' => $this->formatPhone($recipient),
            'type' => $fileType,
            $fileType => curl_file_create($filePath, $mimeType)
        ];
        
        if ($caption) {
            $postFields[$fileType]['caption'] = $caption;
        }
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->accessToken
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }

    protected function sendMessage(array $data): array
    {
        $ch = curl_init();
        $url = "{$this->endpoint}/{$this->phoneNumberId}/messages";
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->accessToken,
            'Content-Type: ' . 'application/json'
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            return [
                'success' => false,
                'error' => $error
            ];
        }

        $result = json_decode($response, true);

        return [
            'success' => $httpCode === 200,
            'http_code' => $httpCode,
            'message_id' => $result['messages'][0]['id'] ?? null,
            'response' => $result
        ];
    }

    protected function formatPhone(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (substr($phone, 0, 2) === '08') {
            $phone = '628' . substr($phone, 2);
        }

        if (substr($phone, 0, 2) === '8') {
            $phone = '62' . $phone;
        }

        return $phone;
    }

    protected function getFileType(string $mimeType): string
    {
        $types = [
            'image/jpeg' => 'image',
            'image/png' => 'image',
            'image/gif' => 'image',
            'application/pdf' => 'document',
            'application/msword' => 'document',
            'audio/mpeg' => 'audio',
            'video/mp4' => 'video'
        ];

        return $types[$mimeType] ?? 'document';
    }
}