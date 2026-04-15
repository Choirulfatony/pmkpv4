<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Database Columns</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
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
            padding: 10px 15px;
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
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #007bff;
            color: white;
        }
        tr:hover {
            background: #f5f5f5;
        }
        code {
            background: #f4f4f4;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Database Column Verification</h1>
        <p>Checking <code>user_profile</code> table for email verification columns...</p>

        <?php
        try {
            // Load CodeIgniter
            require __DIR__ . '/vendor/autoload.php';
            require __DIR__ . '/app/Config/Paths.php';
            
            $paths = new Config\Paths();
            require SYSTEMPATH . 'bootstrap.php';
            
            $db = \Config\Database::connect();
            
            // Get table structure
            $fields = $db->getFieldData('user_profile');
            
            // Check for specific columns
            $requiredColumns = [
                'profile_is_verified' => 'Email verification status',
                'profile_verification_token' => 'Verification token',
                'profile_verification_sent_at' => 'Verification email sent timestamp'
            ];
            
            $existingColumns = [];
            foreach ($fields as $field) {
                $existingColumns[$field->name] = $field;
            }
            
            echo '<h2>✅ Results</h2>';
            echo '<table>';
            echo '<thead><tr><th>Column Name</th><th>Description</th><th>Status</th><th>Type</th></tr></thead>';
            echo '<tbody>';
            
            $allExist = true;
            foreach ($requiredColumns as $column => $description) {
                if (isset($existingColumns[$column])) {
                    $field = $existingColumns[$column];
                    echo "<tr>";
                    echo "<td><code>{$column}</code></td>";
                    echo "<td>{$description}</td>";
                    echo "<td><span class='status success'>✅ EXISTS</span></td>";
                    echo "<td>{$field->type}";
                    if ($field->default !== null) {
                        echo " (Default: {$field->default})";
                    }
                    if ($field->nullable) {
                        echo " (Nullable)";
                    }
                    echo "</td>";
                    echo "</tr>";
                } else {
                    $allExist = false;
                    echo "<tr>";
                    echo "<td><code>{$column}</code></td>";
                    echo "<td>{$description}</td>";
                    echo "<td><span class='status error'>❌ MISSING</span></td>";
                    echo "<td>-</td>";
                    echo "</tr>";
                }
            }
            echo '</tbody></table>';
            
            if ($allExist) {
                echo '<div class="status success">';
                echo '<strong>✅ Success!</strong> All required columns exist in user_profile table.';
                echo '</div>';
                
                echo '<div class="status info">';
                echo '<strong>ℹ️ Next Steps:</strong><br>';
                echo '1. Try logging in with Google OAuth<br>';
                echo '2. Check logs at: <code>writable/logs/log-' . date('Y-m-d') . '.log</code><br>';
                echo '3. Verify auto-verify actions in logs';
                echo '</div>';
                
                // Show some stats
                $totalUsers = $db->table('user_profile')->countAll();
                $googleUsers = $db->table('user_profile')
                    ->where('profile_insert_by', 'GOOGLE')
                    ->countAllResults();
                $verifiedUsers = $db->table('user_profile')
                    ->where('profile_is_verified', 1)
                    ->countAllResults();
                $unverifiedGoogleUsers = $db->table('user_profile')
                    ->where('profile_insert_by', 'GOOGLE')
                    ->where('profile_is_verified', 0)
                    ->countAllResults();
                
                echo '<h2>📊 User Statistics</h2>';
                echo '<table>';
                echo '<tr><td>Total Users</td><td><strong>' . $totalUsers . '</strong></td></tr>';
                echo '<tr><td>Google OAuth Users</td><td><strong>' . $googleUsers . '</strong></td></tr>';
                echo '<tr><td>Verified Users</td><td><strong>' . $verifiedUsers . '</strong></td></tr>';
                echo '<tr><td>Unverified Google Users (will auto-verify on login)</td><td><strong>' . $unverifiedGoogleUsers . '</strong></td></tr>';
                echo '</table>';
                
            } else {
                echo '<div class="status error">';
                echo '<strong>❌ Error!</strong> Some columns are missing. Run the migration:<br>';
                echo '<code>php spark migrate</code>';
                echo '</div>';
            }
            
        } catch (Exception $e) {
            echo '<div class="status error">';
            echo '<strong>❌ Database Error:</strong> ' . htmlspecialchars($e->getMessage());
            echo '</div>';
        }
        ?>
        
        <hr style="margin: 30px 0;">
        <p style="color: #666; font-size: 12px;">
            <strong>Generated:</strong> <?php echo date('Y-m-d H:i:s'); ?><br>
            <strong>File:</strong> verify_columns.php (delete after use)
        </p>
    </div>
</body>
</html>
