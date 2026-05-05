<?php
/**
 * Test WhatsApp Business API dengan Template
 * Access: http://localhost/pmkpv4/test_wa_template.php
 */

declare(strict_types=1);

// Set timezone to WIB (Asia/Jakarta)
date_default_timezone_set('Asia/Jakarta');

// Konfigurasi
// $token = 'EAAOPZAk50d4QBRWgRZBlswqPFxIjTIWToyWsrS5Hj0ZCw7fVjSydW3sRqiUM6dgZCITNOK3MK7bDdl7Qbmt9LBMcbnhwXrZC9xoiNcS8Y4tjbj1kB0VgwI8ZBBhITGyzAeuFy2EXXzIeM3z6VDsw9NZCXlZAvku93DZAS2jiVBZCTBSf3nZCoBxGZBP0x7DopUOsDgZDZD';
$token = 'EAAOPZAk50d4QBRaY7tZCHk10fMYjhGmP0bbtXgCop8KZCu9bibCVndeeT3HxvhAQlpT7co0tHTJqaYZAmZADCoWc2qqcb60M2Qhlz8o2CyPcucRxNEysuZB2SN5jL1A5aU0vrZBJ4DV0gtSC051419P4LQ48S7rVY1mXjOqdZCNQZAVmaa3bbElZBiG83PlZBDHsxnlKDS5ZBTempySZB6DD7zCpJj121qt0rzFBYuVU5fIiiivV7ZCtcjpohUZAskZB85pZBfaTZAS2ZCiWZCCWhknPORoOshXNjwxusfmg0YJRMgZDZD';
$phone = '082233346468'; // Ganti dengan nomor tujuan
$phone = preg_replace('/^0/', '62', $phone);

$url = "https://graph.facebook.com/v19.0/1066326136567077/messages";

// Pilihan 1: Text message (recommended untuk testing)
$data = [
    'messaging_product' => 'whatsapp',
    'to' => $phone,
    'type' => 'text',
    'text' => [
        'body' => 'Test WhatsApp API - ' . date('Y-m-d H:i:s') . "\nWaktu: " . date('H:i:s') . ' WIB'
    ]
];

// Pilihan 2: Template (ganti dengan template yang sudah disetujui di akun Anda)
/*
$data = [
    'messaging_product' => 'whatsapp',
    'to' => $phone,
    'type' => 'template',
    'template' => [
        'name' => 'nama_template_anda', // Ganti dengan template yang sudah disetujui
        'language' => [
            'code' => 'id'
        ]
    ]
];
*/

$headers = [
    'Authorization: Bearer ' . $token,
    'Content-Type: application/json'
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // For testing only

$response = curl_exec($ch);
$error = curl_error($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test WhatsApp Template API</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>WhatsApp Business API - Template Test</h2>
        
        <div class="card mt-4">
            <div class="card-header">
                <strong>Request Info</strong>
            </div>
            <div class="card-body">
                <p><strong>Phone:</strong> <?= htmlspecialchars($phone) ?></p>
                <p><strong>Type:</strong> <?= htmlspecialchars($data['type']) ?></p>
                <?php if ($data['type'] === 'template'): ?>
                <p><strong>Template:</strong> <?= htmlspecialchars($data['template']['name']) ?></p>
                <p><strong>Language:</strong> <?= htmlspecialchars($data['template']['language']['code']) ?></p>
                <?php endif; ?>
                <p><strong>HTTP Code:</strong> 
                    <span class="badge bg-<?= $httpCode == 200 ? 'success' : 'danger' ?>"><?= $httpCode ?></span>
                </p>
            </div>
        </div>

        <?php if ($response): ?>
        <div class="card mt-3">
            <div class="card-header">
                <strong>Response</strong>
            </div>
            <div class="card-body">
                <pre class="bg-light p-3"><?= htmlspecialchars(json_encode(json_decode($response), JSON_PRETTY_PRINT)) ?></pre>
                
                <?php
                $json = json_decode($response, true);
                if (isset($json['error'])) {
                    echo '<div class="alert alert-danger">';
                    echo '<strong>Error:</strong> ' . htmlspecialchars($json['error']['message']) . '<br>';
                    echo '<small>Code: ' . htmlspecialchars((string)$json['error']['code']) . '</small>';
                    echo '</div>';
                } elseif (isset($json['messages'])) {
                    echo '<div class="alert alert-success">';
                    echo '<strong>SUCCESS! Template message sent!</strong><br>';
                    echo 'Message ID: ' . htmlspecialchars($json['messages'][0]['id']);
                    echo '</div>';
                }
                ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($error): ?>
        <div class="alert alert-danger mt-3">
            <strong>cURL Error:</strong> <?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>

        <div class="mt-4">
            <h5>Cara Menggunakan Template:</h5>
            <ol>
                <li>Pastikan template sudah disetujui di <a href="https://business.facebook.com/" target="_blank">Meta Business Manager</a></li>
                <li>Ganti <code>name</code> dengan nama template yang disetujui</li>
                <li>Sesuaikan <code>parameters</code> dengan variabel template</li>
                <li>Pastikan nomor HP sudah terdaftar di WhatsApp</li>
            </ol>
        </div>

        <div class="mt-3">
            <a href="/pmkpv4/" class="btn btn-secondary">Kembali</a>
        </div>
    </div>
</body>
</html>
