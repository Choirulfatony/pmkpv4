<?php

namespace App\Libraries;

use App\Models\IkpNotifikasiModel;

class ClaudeWhatsApp
{
    protected $apiKey;
    protected $model;
    protected $maxTokens;
    protected $notifikasiModel;

    public function __construct()
    {
        $config = config('ClaudeWhatsApp');
        
        $this->apiKey = $config->apiKey ?? getenv('claude.api_key');
        $this->model = $config->model ?? 'claude-3-haiku-20240307';
        $this->maxTokens = $config->maxTokens ?? 1024;
        $this->notifikasiModel = new IkpNotifikasiModel();
    }

    public function setApiKey(string $apiKey): self
    {
        $this->apiKey = $apiKey;
        return $this;
    }

    public function setModel(string $model): self
    {
        $this->model = $model;
        return $this;
    }

    public function generateResponse(string $userMessage, array $context = []): array
    {
        if (empty($this->apiKey)) {
            return [
                'success' => false,
                'error' => 'Claude API key not configured'
            ];
        }

        $systemPrompt = $this->buildSystemPrompt($context);

        $data = [
            'model' => $this->model,
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $userMessage
                ]
            ],
            'max_tokens' => $this->maxTokens
        ];

        if (!empty($systemPrompt)) {
            $data['system'] = $systemPrompt;
        }

        return $this->sendToClaude($data);
    }

    public function chat(array $messages, ?string $systemPrompt = null): array
    {
        if (empty($this->apiKey)) {
            return [
                'success' => false,
                'error' => 'Claude API key not configured'
            ];
        }

        $data = [
            'model' => $this->model,
            'messages' => $messages,
            'max_tokens' => $this->maxTokens
        ];

        if ($systemPrompt) {
            $data['system'] = $systemPrompt;
        }

        return $this->sendToClaude($data);
    }

    public function generateNotificationMessage(string $title, string $body, ?string $action = null): array
    {
        $prompt = "Buatkan pesan notifikasi WhatsApp yang singkat dan jelas untuk:

Judul: {$title}
Detail: {$body}
" . ($action ? "Tindakan yang perlu dilakukan: {$action}" : "") .

"Tolakannyal:
- Jangan pakai emoji berlebihan
- Maksimal 500 karakter
- Langsung to the point
- Sertakan deadline jika ada
- Format: teks biasa";

        return $this->generateResponse($prompt);
    }

    public function summarizeText(string $text, int $maxLength = 200): array
    {
        $prompt = "Ringkas teks berikut menjadi maksimal {$maxLength} karakter. Jadikan satu paragraf saja.

Teks:
{$text}

Jika tidak ada teks yang bisa diringkas, balas dengan 'Teks terlalu pendek untuk diringkas.'";

        return $this->generateResponse($prompt);
    }

    public function analyzeData(string $dataDescription, string $question): array
    {
        $prompt = "Analisa data berikut dan jawab pertanyaan:

Data: {$dataDescription}

Pertanyaan: {$question}

Berikan jawaban yang singkat, akurat, dan mudah dipahami.";

        return $this->generateResponse($prompt);
    }

    public function generateReportSummary(array $indicators): array
    {
        $summary = json_encode($indicators, JSON_PRETTY_PRINT);
        
        $prompt = "Buatkan ringkasan laporan PMKP dari data berikut dalam bahasa Indonesia yang mudah dipahami:

{$summary}

Ringkasan harus mencakup:
1. Gambaran umum capaian
2. Indikator yang tercapai
3. Indikator yang belum tercapai
4. Rekomendasi sederhana

Maksimal 500 karakter.";

        return $this->generateResponse($prompt);
    }

    protected function buildSystemPrompt(array $context): string
    {
        $systemPrompt = "Kamu adalah asisten notifikasi RS yang membantu menyampaikan informasi penting.";

        if (!empty($context['user_name'])) {
            $systemPrompt .= "\n\nNama pasien/user: {$context['user_name']}";
        }

        if (!empty($context['department'])) {
            $systemPrompt .= "\n\nUnit/Departemen: {$context['department']}";
        }

        if (!empty($context['role'])) {
            $systemPrompt .= "\n\nRole pengguna: {$context['role']}";
        }

        $systemPrompt .= "\n\nGaya penulisan:
- Formal tapi ramah
- Bahasa Indonesia
- Singkat dan jelas
- Tidak menggunakan terlalu banyak emoji";

        return $systemPrompt;
    }

    protected function sendToClaude(array $data): array
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://api.anthropic.com/v1/messages');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'x-api-key: ' . $this->apiKey,
            'anthropic-version: 2023-06-01'
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            return [
                'success' => false,
                'error' => 'cURL Error: ' . $error
            ];
        }

        $result = json_decode($response, true);

        if ($httpCode !== 200) {
            return [
                'success' => false,
                'error' => $result['error']['message'] ?? 'Unknown error',
                'http_code' => $httpCode
            ];
        }

        return [
            'success' => true,
            'response' => $result['content'][0]['text'] ?? '',
            'usage' => $result['usage'] ?? null
        ];
    }
}