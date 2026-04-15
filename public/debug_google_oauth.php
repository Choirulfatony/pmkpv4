<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debug Google OAuth</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1000px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
        }
        .status {
            padding: 15px;
            border-radius: 4px;
            margin: 10px 0;
        }
        .success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .warning {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
        }
        .info {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border: 1px solid #dee2e6;
        }
        th {
            background: #007bff;
            color: white;
        }
        pre {
            background: #f4f4f4;
            padding: 15px;
            border-radius: 4px;
            overflow-x: auto;
        }
        code {
            background: #f4f4f4;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
        }
        .step {
            padding: 15px;
            margin: 10px 0;
            border-left: 4px solid #007bff;
            background: #f8f9fa;
        }
        .step.success {
            border-left-color: #28a745;
        }
        .step.error {
            border-left-color: #dc3545;
        }
        .step.warning {
            border-left-color: #ffc107;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Debug Google OAuth Login</h1>
        <p>Script ini akan mendiagnosa masalah login Google OAuth Anda</p>

        <?php
        require __DIR__ . '/../vendor/autoload.php';
        require __DIR__ . '/../app/Config/Paths.php';
        
        $paths = new Config\Paths();
        require SYSTEMPATH . 'bootstrap.php';
        
        $errors = [];
        $warnings = [];
        $successes = [];
        $steps = [];

        // Step 1: Cek Google OAuth Configuration
        echo "<h2>Step 1: Cek Konfigurasi Google OAuth</h2>";
        
        $clientId = env('google.client_id');
        $clientSecret = env('google.client_secret');
        $redirectUri = env('google.redirect_uri');
        
        $steps[] = [
            'type' => !empty($clientId) && !empty($clientSecret) ? 'success' : 'error',
            'title' => 'Google OAuth Credentials',
            'content' => "
                <table>
                    <tr><td><strong>Client ID:</strong></td><td><code>" . ($clientId ? substr($clientId, 0, 30) . '...' : '❌ NOT SET') . "</code></td></tr>
                    <tr><td><strong>Client Secret:</strong></td><td><code>" . ($clientSecret ? 'SET (hidden)' : '❌ NOT SET') . "</code></td></tr>
                    <tr><td><strong>Redirect URI:</strong></td><td><code>" . ($redirectUri ?: '❌ NOT SET') . "</code></td></tr>
                </table>
            "
        ];

        // Step 2: Cek Database Connection
        echo "<h2>Step 2: Cek Database Connection</h2>";
        
        try {
            $db = \Config\Database::connect();
            $db->query("SELECT 1");
            
            $steps[] = [
                'type' => 'success',
                'title' => 'Database Connection',
                'content' => '✅ Database connection successful'
            ];
        } catch (Exception $e) {
            $steps[] = [
                'type' => 'error',
                'title' => 'Database Connection',
                'content' => '❌ ' . htmlspecialchars($e->getMessage())
            ];
        }

        // Step 3: Cek User di Database
        echo "<h2>Step 3: Cek User di Database</h2>";
        
        try {
            $db = \Config\Database::connect();
            
            // Cek user dengan email yang mirip
            $users = $db->table('user_profile')
                ->select('profile_id, profile_email, profile_fullname, profile_insert_by, profile_is_verified, profile_verification_token, profile_record_status')
                ->like('profile_email', 'choirulfatoni@gmail.com')
                ->get()
                ->getResult();
            
            if (count($users) > 0) {
                foreach ($users as $user) {
                    $issues = [];
                    
                    // Check for issues
                    if (strtoupper($user->profile_insert_by) !== 'GOOGLE') {
                        $issues[] = "❌ profile_insert_by = '{$user->profile_insert_by}' (should be 'GOOGLE')";
                    }
                    if ($user->profile_is_verified != 1) {
                        $issues[] = "❌ profile_is_verified = {$user->profile_is_verified} (should be 1)";
                    }
                    if (!empty($user->profile_verification_token)) {
                        $issues[] = "❌ profile_verification_token masih ada (should be NULL)";
                    }
                    if ($user->profile_record_status !== 'A') {
                        $issues[] = "❌ profile_record_status = '{$user->profile_record_status}' (should be 'A')";
                    }
                    
                    $steps[] = [
                        'type' => empty($issues) ? 'success' : 'warning',
                        'title' => 'User Found: ' . $user->profile_email,
                        'content' => "
                            <table>
                                <tr><td><strong>Profile ID:</strong></td><td>{$user->profile_id}</td></tr>
                                <tr><td><strong>Email:</strong></td><td>{$user->profile_email}</td></tr>
                                <tr><td><strong>Full Name:</strong></td><td>{$user->profile_fullname}</td></tr>
                                <tr><td><strong>Insert By:</strong></td><td>{$user->profile_insert_by}</td></tr>
                                <tr><td><strong>Is Verified:</strong></td><td>{$user->profile_is_verified}</td></tr>
                                <tr><td><strong>Has Token:</strong></td><td>" . (!empty($user->profile_verification_token) ? 'YES (should be NULL)' : 'NULL ✓') . "</td></tr>
                                <tr><td><strong>Record Status:</strong></td><td>{$user->profile_record_status}</td></tr>
                            </table>
                            " . (empty($issues) ? '<div class="status success">✅ No issues found</div>' : '<div class="status warning"><strong>Issues:</strong><ul><li>' . implode('</li><li>', $issues) . '</li></ul></div>') . "
                        "
                    ];
                }
            } else {
                $steps[] = [
                    'type' => 'error',
                    'title' => 'User Not Found',
                    'content' => '❌ User dengan email choirulfatoni@gmail.com tidak ditemukan di database'
                ];
            }
        } catch (Exception $e) {
            $steps[] = [
                'type' => 'error',
                'title' => 'Database Query Error',
                'content' => '❌ ' . htmlspecialchars($e->getMessage())
            ];
        }

        // Step 4: Cek Google API Client Library
        echo "<h2>Step 4: Cek Google API Client Library</h2>";
        
        try {
            if (class_exists('Google_Client')) {
                $client = new Google_Client();
                $steps[] = [
                    'type' => 'success',
                    'title' => 'Google API Client',
                    'content' => '✅ Google_Client class exists (version: ' . ($client->getLibraryVersion() ?? 'unknown') . ')'
                ];
            } else {
                $steps[] = [
                    'type' => 'error',
                    'title' => 'Google API Client',
                    'content' => '❌ Google_Client class not found. Run: composer install'
                ];
            }
        } catch (Exception $e) {
            $steps[] = [
                'type' => 'error',
                'title' => 'Google API Client',
                'content' => '❌ ' . htmlspecialchars($e->getMessage())
            ];
        }

        // Step 5: Cek Log Error Terbaru
        echo "<h2>Step 5: Cek Log Error Terbaru</h2>";
        
        try {
            $logFile = WRITEPATH . 'logs/log-' . date('Y-m-d') . '.log';
            if (file_exists($logFile)) {
                $logContent = file_get_contents($logFile);
                if (preg_match_all('/ERROR - .*?(GOOGLE.*?|Google Login Error.*?)(\n|$)/m', $logContent, $matches)) {
                    $logEntries = array_slice($matches[0], -5); // Last 5 entries
                    $steps[] = [
                        'type' => 'warning',
                        'title' => 'Latest Google Errors in Log',
                        'content' => '<pre>' . htmlspecialchars(implode("\n", $logEntries)) . '</pre>'
                    ];
                } else {
                    $steps[] = [
                        'type' => 'success',
                        'title' => 'Google Errors in Log',
                        'content' => '✅ No recent Google OAuth errors found in today\'s log'
                    ];
                }
            } else {
                $steps[] = [
                    'type' => 'info',
                    'title' => 'Log File',
                    'content' => 'ℹ️ No log file for today found at: ' . $logFile
                ];
            }
        } catch (Exception $e) {
            $steps[] = [
                'type' => 'error',
                'title' => 'Log File Error',
                'content' => '❌ ' . htmlspecialchars($e->getMessage())
            ];
        }

        // Display all steps
        foreach ($steps as $i => $step) {
            $type = $step['type'];
            echo "<div class='step {$type}'>";
            echo "<h3>Step " . ($i + 1) . ": {$step['title']}</h3>";
            echo $step['content'];
            echo "</div>";
        }

        // Summary
        echo "<hr><h2>📊 Summary</h2>";
        $errorCount = count(array_filter($steps, fn($s) => $s['type'] === 'error'));
        $warningCount = count(array_filter($steps, fn($s) => $s['type'] === 'warning'));
        $successCount = count(array_filter($steps, fn($s) => $s['type'] === 'success'));
        
        echo "<div class='status " . ($errorCount > 0 ? 'error' : ($warningCount > 0 ? 'warning' : 'success')) . "'>";
        echo "<strong>Results:</strong><br>";
        echo "❌ Errors: {$errorCount}<br>";
        echo "⚠️ Warnings: {$warningCount}<br>";
        echo "✅ Success: {$successCount}<br><br>";
        
        if ($errorCount > 0) {
            echo "<strong>⚠️ Ada error yang perlu diperbaiki. Lihat steps di atas untuk detailnya.</strong>";
        } else {
            echo "<strong>✅ Semua OK! Silakan test login Google OAuth.</strong>";
        }
        echo "</div>";
        ?>

        <hr style="margin: 30px 0;">
        <p style="color: #666; font-size: 12px;">
            <strong>Generated:</strong> <?php echo date('Y-m-d H:i:s'); ?><br>
            <strong>File:</strong> debug_google_oauth.php
        </p>
    </div>
</body>
</html>
