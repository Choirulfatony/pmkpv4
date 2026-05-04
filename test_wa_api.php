<?php
/**
 * Test WhatsApp Business API
 * Access: http://localhost/pmkpv4/test_wa_api.php
 */

// Set timezone to WIB (Asia/Jakarta)
date_default_timezone_set('Asia/Jakarta');

$token = 'EAAOPZAk50d4QBRWgRZBlswqPFxIjTIWToyWsrS5Hj0ZCw7fVjSydW3sRqiUM6dgZCITNOK3MK7bDdl7Qbmt9LBMcbnhwXrZC9xoiNcS8Y4tjbj1kB0VgwI8ZBBhITGyzAeuFy2EXXzIeM3z6VDsw9NZCXlZAvku93DZAS2jiVBZCTBSf3nZCoBxGZBP0x7DopUOsDgZDZD';
$phone = '082233346468'; // Original number
$message = 'Test WhatsApp Business API - ' . date('Y-m-d H:i:s') . "\nWaktu WIB: " . date('H:i:s') . ' WIB';

// Format phone: 0822... -> 62822...
$phone = preg_replace('/^0/', '62', $phone);

$url = "https://graph.facebook.com/v19.0/1128976353628313/messages";

$data = [
    'messaging_product' => 'whatsapp',
    'to' => $phone,
    'type' => 'text',
    'text' => ['body' => $message]
];

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

echo "<h2>WhatsApp Business API Test</h2>";
echo "<p><strong>Phone:</strong> " . htmlspecialchars($phone) . "</p>";
echo "<p><strong>Message:</strong> " . htmlspecialchars($message) . "</p>";
echo "<p><strong>HTTP Code:</strong> " . $httpCode . "</p>";

if ($response) {
    echo "<p><strong>Response:</strong></p>";
    echo "<pre>" . htmlspecialchars($response) . "</pre>";
    
    $json = json_decode($response, true);
    if (isset($json['error'])) {
        echo "<p style='color:red'><strong>Error:</strong> " . htmlspecialchars($json['error']['message']) . "</p>";
    } elseif (isset($json['messages'])) {
        echo "<p style='color:green'><strong>SUCCESS! Message sent!</strong></p>";
        echo "<p>Message ID: " . htmlspecialchars($json['messages'][0]['id']) . "</p>";
    }
}

if ($error) {
    echo "<p style='color:red'><strong>cURL Error:</strong> " . htmlspecialchars($error) . "</p>";
}
