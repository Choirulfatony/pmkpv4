<?php

namespace App\Libraries;

use App\Models\IkpNotifikasiModel;
use App\Models\HrisUserModel;

class WhatsAppWeb
{
    protected $notifikasiModel;
    protected $userModel;

    public function __construct()
    {
        $this->notifikasiModel = new IkpNotifikasiModel();
        $this->userModel = new HrisUserModel();
    }

    public function getSessionPath(): string
    {
        return WRITEPATH . 'whatsapp_sessions';
    }

    public function getQRCode(): ?array
    {
        $cache = \Config\Services::cache();
        $qrData = $cache->get('whatsapp_qr');

        if ($qrData) {
            return [
                'qr' => $qrData,
                'status' => 'waiting_scan'
            ];
        }

        $connected = $cache->get('whatsapp_connected');
        if ($connected) {
            return [
                'status' => 'connected',
                'phone' => $connected
            ];
        }

        return null;
    }

    public function sendMessage(string $phone, string $message): array
    {
        $cache = \Config\Services::cache();
        
        if (!$cache->get('whatsapp_connected')) {
            return [
                'success' => false,
                'message' => 'WhatsApp not connected'
            ];
        }

        try {
            $data = [
                'action' => 'send_message',
                'phone' => $this->formatPhone($phone),
                'message' => $message,
                'timestamp' => time()
            ];

            $cache->save('whatsapp_message_queue', $data, 300);

            return [
                'success' => true,
                'message' => 'Message queued'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function sendNotification(int $userId, string $title, string $body, ?string $link = null): array
    {
        $user = $this->userModel->find($userId);
        
        if (!$user || empty($user->no_hp)) {
            return [
                'success' => false,
                'message' => 'User phone not found'
            ];
        }

        $message = "📋 *{$title}*\n\n{$body}";
        
        if ($link) {
            $message .= "\n\n🔗 {$link}";
        }

        $message .= "\n\n-- Sistem PMKP";

        return $this->sendMessage($user->no_hp, $message);
    }

    public function sendBulkNotification(array $userIds, string $title, string $body, ?string $link = null): array
    {
        $results = [
            'success' => 0,
            'failed' => 0,
            'errors' => []
        ];

        foreach ($userIds as $userId) {
            $result = $this->sendNotification($userId, $title, $body, $link);
            
            if ($result['success']) {
                $results['success']++;
            } else {
                $results['failed']++;
                $results['errors'][] = "User {$userId}: " . $result['message'];
            }
        }

        return $results;
    }

    public function disconnect(): bool
    {
        $cache = \Config\Services::cache();
        $cache->delete('whatsapp_connected');
        $cache->delete('whatsapp_qr');
        $cache->delete('whatsapp_message_queue');

        return true;
    }

    public function getStatus(): array
    {
        $cache = \Config\Services::cache();
        
        $connected = $cache->get('whatsapp_connected');
        $qrData = $cache->get('whatsapp_qr');

        if ($connected) {
            return [
                'status' => 'connected',
                'phone' => $connected,
                'last_seen' => $cache->get('whatsapp_last_activity') ?? null
            ];
        }

        if ($qrData) {
            return [
                'status' => 'waiting_scan',
                'qr' => $qrData
            ];
        }

        return [
            'status' => 'disconnected'
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

        if (substr($phone, 0, 3) !== '62') {
            $phone = '62' . $phone;
        }

        return $phone . '@c.us';
    }
}