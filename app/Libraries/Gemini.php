<?php

declare(strict_types=1);

namespace App\Libraries;

use Config\Gemini as GeminiConfig;

class Gemini
{
    protected GeminiConfig $config;

    protected string $model = 'gemini-2.0-flash';

    protected ?string $lastError = null;

    public function __construct(?GeminiConfig $config = null)
    {
        $this->config = $config ?? config('Gemini');
    }

    public function getLastError(): ?string
    {
        return $this->lastError;
    }

    public function setModel(string $model): static
    {
        $this->model = $model;

        return $this;
    }

    public function generateContent(string|array $prompt): ?array
    {
        $contents = is_string($prompt)
            ? [['parts' => [['text' => $prompt]]]]
            : $prompt;

        $url = rtrim($this->config->baseUrl, '/')
            . '/models/'
            . $this->model
            . ':generateContent'
            . '?key=' . $this->config->apiKey;

        $client = service('curlrequest', [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'timeout'  => 30,
            'http_errors' => false,
        ]);

        try {
            $response = $client->post($url, [
                'json' => ['contents' => $contents],
            ]);
        } catch (\Throwable $e) {
            $this->lastError = 'Koneksi gagal: ' . $e->getMessage();
            log_message('error', '[Gemini API] Exception: ' . $e->getMessage());

            return null;
        }

        $statusCode = $response->getStatusCode();

        if ($statusCode !== 200) {
            $body = $response->getBody();
            $error = json_decode($body, true);
            $message = $error['error']['message'] ?? $body;

            $this->lastError = 'HTTP ' . $statusCode . ': ' . $message;
            log_message('error', '[Gemini API] HTTP ' . $statusCode . ': ' . $message);

            return null;
        }

        $result = json_decode($response->getBody(), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->lastError = 'JSON decode error: ' . json_last_error_msg();
            log_message('error', '[Gemini API] JSON decode error: ' . json_last_error_msg());

            return null;
        }

        return $result;
    }

    public function getText(string|array $prompt): ?string
    {
        $result = $this->generateContent($prompt);

        return $result['candidates'][0]['content']['parts'][0]['text'] ?? null;
    }

    public function chat(array $history, string $message): ?array
    {
        $contents = $history;
        $contents[] = ['parts' => [['text' => $message]], 'role' => 'user'];

        return $this->generateContent($contents);
    }

    public function getChatText(array $history, string $message): ?string
    {
        $result = $this->chat($history, $message);

        return $result['candidates'][0]['content']['parts'][0]['text'] ?? null;
    }
}
