<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activate Google OAuth Users</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            font-size: 28px;
            margin-bottom: 10px;
        }
        .header p {
            font-size: 14px;
            opacity: 0.9;
        }
        .content {
            padding: 30px;
        }
        .warning-box {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .warning-box strong {
            color: #856404;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            border: 2px solid #e9ecef;
        }
        .stat-card .number {
            font-size: 36px;
            font-weight: bold;
            color: #495057;
        }
        .stat-card .label {
            font-size: 12px;
            color: #6c757d;
            margin-top: 5px;
            text-transform: uppercase;
        }
        .stat-card.success {
            border-color: #28a745;
            background: #d4edda;
        }
        .stat-card.success .number {
            color: #155724;
        }
        .stat-card.warning {
            border-color: #ffc107;
            background: #fff3cd;
        }
        .stat-card.warning .number {
            color: #856404;
        }
        h2 {
            color: #333;
            margin-bottom: 15px;
            font-size: 20px;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s ease;
            margin: 5px;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }
        .btn-success {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
        }
        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(17, 153, 142, 0.4);
        }
        .btn-danger {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
        }
        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(245, 87, 108, 0.4);
        }
        .btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        .btn:disabled:hover {
            transform: none;
            box-shadow: none;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background: white;
        }
        thead {
            background: #667eea;
            color: white;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }
        tbody tr:hover {
            background: #f8f9fa;
        }
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
        }
        .badge-success {
            background: #d4edda;
            color: #155724;
        }
        .badge-warning {
            background: #fff3cd;
            color: #856404;
        }
        .action-buttons {
            text-align: center;
            margin: 30px 0;
        }
        .log-box {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 15px;
            margin-top: 20px;
            max-height: 400px;
            overflow-y: auto;
            font-family: 'Courier New', monospace;
            font-size: 12px;
        }
        .log-entry {
            margin: 5px 0;
            padding: 5px;
            border-left: 3px solid #667eea;
            padding-left: 10px;
        }
        .log-entry.success {
            border-left-color: #28a745;
            color: #155724;
        }
        .log-entry.warning {
            border-left-color: #ffc107;
            color: #856404;
        }
        .hidden {
            display: none;
        }
        .modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }
        .modal-content {
            background: white;
            padding: 30px;
            border-radius: 12px;
            max-width: 500px;
            text-align: center;
        }
        .modal-content h3 {
            margin-bottom: 15px;
            color: #dc3545;
        }
        .modal-buttons {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔧 Activate Google OAuth Users</h1>
            <p>Aktivasi semua user Google OAuth yang sudah terdaftar di database</p>
        </div>

        <div class="content">
            <?php
            require __DIR__ . '/../vendor/autoload.php';
            require __DIR__ . '/../app/Config/Paths.php';
            
            $paths = new Config\Paths();
            require SYSTEMPATH . 'bootstrap.php';
            
            $db = \Config\Database::connect();
            
            // Get statistics (include variations of GOOGLE)
            $totalUsers = $db->table('user_profile')->countAll();
            $googleUsers = $db->table('user_profile')
                ->group_start()
                    ->where('profile_insert_by', 'GOOGLE')
                    ->or_like('profile_insert_by', 'GOOGL')
                    ->or_like('profile_insert_by', 'GOOG')
                    ->or_like('profile_insert_by', 'Google')
                ->group_end()
                ->countAllResults();
            $verifiedGoogleUsers = $db->table('user_profile')
                ->group_start()
                    ->where('profile_insert_by', 'GOOGLE')
                    ->or_like('profile_insert_by', 'GOOGL')
                    ->or_like('profile_insert_by', 'GOOG')
                    ->or_like('profile_insert_by', 'Google')
                ->group_end()
                ->where('profile_is_verified', 1)
                ->where('profile_verification_token IS NULL')
                ->countAllResults();
            $unverifiedGoogleUsers = $db->table('user_profile')
                ->group_start()
                    ->where('profile_insert_by', 'GOOGLE')
                    ->or_like('profile_insert_by', 'GOOGL')
                    ->or_like('profile_insert_by', 'GOOG')
                    ->or_like('profile_insert_by', 'Google')
                ->group_end()
                ->group_start()
                    ->where('profile_is_verified', 0)
                    ->or_where('profile_is_verified IS NULL')
                    ->or_where('profile_verification_token IS NOT NULL')
                ->group_end()
                ->countAllResults();
            
            // Get unverified Google users (include those with wrong profile_insert_by)
            $unverifiedUsers = $db->table('user_profile')
                ->select('profile_id, profile_email, profile_fullname, profile_insert_date, profile_insert_by, profile_is_verified, profile_verification_token')
                ->group_start()
                    ->where('profile_insert_by', 'GOOGLE')
                    ->or_like('profile_insert_by', 'GOOGL')
                    ->or_like('profile_insert_by', 'GOOG')
                    ->or_like('profile_insert_by', 'Google')
                ->group_end()
                ->group_start()
                    ->where('profile_is_verified', 0)
                    ->or_where('profile_is_verified IS NULL')
                    ->or_where('profile_verification_token IS NOT NULL')
                ->group_end()
                ->orderBy('profile_insert_date', 'DESC')
                ->get()
                ->getResult();
            
            // Handle activation
            $message = '';
            $logEntries = [];
            
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
                if ($_POST['action'] === 'activate_all') {
                    $activated = 0;
                    foreach ($unverifiedUsers as $user) {
                        $fixData = [
                            'profile_is_verified' => 1,
                            'profile_verification_token' => null,
                            'profile_verification_sent_at' => null,
                        ];
                        
                        // Fix profile_insert_by if not standard
                        $insertBy = strtoupper($user->profile_insert_by ?? '');
                        if ($insertBy !== 'GOOGLE') {
                            $fixData['profile_insert_by'] = 'GOOGLE';
                        }
                        
                        $db->table('user_profile')
                            ->where('profile_id', $user->profile_id)
                            ->update($fixData);
                        $activated++;
                        $logEntries[] = [
                            'type' => 'success',
                            'message' => "✅ Verified: {$user->profile_email} ({$user->profile_fullname}) - Fixed profile_insert_by"
                        ];
                    }
                    $message = "<div class='warning-box'><strong>✅ Success!</strong> {$activated} user(s) have been activated and fixed.</div>";
                    $logEntries[] = [
                        'type' => 'success',
                        'message' => "🎉 Total activated: {$activated} users"
                    ];
                } elseif ($_POST['action'] === 'activate_single' && isset($_POST['email'])) {
                    $email = $_POST['email'];
                    $db->table('user_profile')
                        ->where('profile_email', $email)
                        ->update([
                            'profile_is_verified' => 1,
                            'profile_verification_token' => null,
                            'profile_verification_sent_at' => null,
                            'profile_insert_by' => 'GOOGLE'
                        ]);
                    $message = "<div class='warning-box'><strong>✅ Success!</strong> User {$email} has been activated and fixed.</div>";
                    $logEntries[] = [
                        'type' => 'success',
                        'message' => "✅ Verified: {$email} - Fixed profile_insert_by"
                    ];
                }
                
                // Refresh stats
                $verifiedGoogleUsers = $db->table('user_profile')
                    ->where('profile_insert_by', 'GOOGLE')
                    ->where('profile_is_verified', 1)
                    ->countAllResults();
                $unverifiedGoogleUsers = $db->table('user_profile')
                    ->where('profile_insert_by', 'GOOGLE')
                    ->group_start()
                        ->where('profile_is_verified', 0)
                        ->or_where('profile_is_verified IS NULL')
                    ->group_end()
                    ->countAllResults();
                $unverifiedUsers = $db->table('user_profile')
                    ->select('profile_id, profile_email, profile_fullname, profile_insert_date')
                    ->where('profile_insert_by', 'GOOGLE')
                    ->group_start()
                        ->where('profile_is_verified', 0)
                        ->or_where('profile_is_verified IS NULL')
                    ->group_end()
                    ->orderBy('profile_insert_date', 'DESC')
                    ->get()
                    ->getResult();
            }
            
            echo $message;
            ?>

            <h2>📊 Statistics</h2>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="number"><?= $totalUsers ?></div>
                    <div class="label">Total Users</div>
                </div>
                <div class="stat-card">
                    <div class="number"><?= $googleUsers ?></div>
                    <div class="label">Google OAuth Users</div>
                </div>
                <div class="stat-card success">
                    <div class="number"><?= $verifiedGoogleUsers ?></div>
                    <div class="label">Verified</div>
                </div>
                <div class="stat-card warning">
                    <div class="number"><?= $unverifiedGoogleUsers ?></div>
                    <div class="label">Need Activation</div>
                </div>
            </div>

            <?php if ($unverifiedGoogleUsers > 0): ?>
                <h2>🔴 Unverified/Fixable Google Users</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Email</th>
                            <th>Full Name</th>
                            <th>Insert By</th>
                            <th>Verified</th>
                            <th>Has Token</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($unverifiedUsers as $user): 
                            $insertByFixed = strtoupper($user->profile_insert_by ?? '') !== 'GOOGLE';
                            $hasToken = !empty($user->profile_verification_token);
                        ?>
                            <tr>
                                <td><?= htmlspecialchars($user->profile_email) ?></td>
                                <td><?= htmlspecialchars($user->profile_fullname) ?></td>
                                <td>
                                    <?= htmlspecialchars($user->profile_insert_by ?? 'NULL') ?>
                                    <?php if ($insertByFixed): ?>
                                        <span class="badge badge-warning">NEED FIX</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($user->profile_is_verified == 1): ?>
                                        <span class="badge badge-success">YES</span>
                                    <?php else: ?>
                                        <span class="badge badge-warning">NO</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($hasToken): ?>
                                        <span class="badge badge-warning">YES (Clear)</span>
                                    <?php else: ?>
                                        <span class="badge badge-success">NULL</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="action" value="activate_single">
                                        <input type="hidden" name="email" value="<?= htmlspecialchars($user->profile_email) ?>">
                                        <button type="submit" class="btn btn-primary" style="padding: 6px 12px; font-size: 12px;">
                                            <?= $insertByFixed || $hasToken ? 'Fix & Activate' : 'Activate' ?>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div class="action-buttons">
                    <form method="POST" id="activateAllForm">
                        <input type="hidden" name="action" value="activate_all">
                        <button type="button" class="btn btn-success btn-lg" onclick="showConfirmModal()">
                            🚀 Activate All <?= $unverifiedGoogleUsers ?> Users
                        </button>
                    </form>
                </div>
            <?php else: ?>
                <div class="warning-box" style="background: #d4edda; border-color: #28a745;">
                    <strong>✅ Great!</strong> All Google OAuth users are verified. No activation needed.
                </div>
            <?php endif; ?>

            <?php if (!empty($logEntries)): ?>
                <h2>📝 Activity Log</h2>
                <div class="log-box">
                    <?php foreach ($logEntries as $log): ?>
                        <div class="log-entry <?= $log['type'] ?>">
                            <?= htmlspecialchars($log['message']) ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div id="confirmModal" class="modal hidden">
        <div class="modal-content">
            <h3>⚠️ Confirm Activation</h3>
            <p>Are you sure you want to activate <strong><?= $unverifiedGoogleUsers ?></strong> user(s)?</p>
            <p style="font-size: 12px; color: #6c757d; margin-top: 10px;">
                This will set <code>profile_is_verified = 1</code> for all unverified Google OAuth users.
            </p>
            <div class="modal-buttons">
                <button class="btn btn-danger" onclick="cancelActivation()">Cancel</button>
                <button class="btn btn-success" onclick="confirmActivation()">Yes, Activate All</button>
            </div>
        </div>
    </div>

    <script>
        function showConfirmModal() {
            document.getElementById('confirmModal').classList.remove('hidden');
        }

        function cancelActivation() {
            document.getElementById('confirmModal').classList.add('hidden');
        }

        function confirmActivation() {
            document.getElementById('activateAllForm').submit();
        }

        // Auto-close modal on outside click
        document.getElementById('confirmModal').addEventListener('click', function(e) {
            if (e.target === this) {
                cancelActivation();
            }
        });
    </script>
</body>
</html>
