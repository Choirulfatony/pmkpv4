<?php
$layout = '
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registrasi - PMKP v2.0 RSSM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        html, body { height: 100%; }
        .login-wallpaper {
            min-height: 100vh;
            background: url("' . base_url('assets/img/rsud.png') . '") no-repeat;
            background-size: 100% 100%;
        }
        .login-overlay {
            min-height: 100vh;
            background: rgba(43, 185, 15, 0.15);
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .login-container {
            width: 100%;
            padding: 30px;
            display: flex;
            justify-content: center;
        }
        .login-card {
            width: 450px;
            background: #fff;
            border-radius: 10px;
            padding: 35px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.24);
        }
    </style>
</head>
<body>
    <section class="login-wallpaper">
        <div class="login-overlay">
            <div class="login-container">
                <div class="login-card">
                    {CONTENT}
                </div>
            </div>
        </div>
    </section>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
';
?>

<?= str_replace('{CONTENT}', $content, $layout) ?>
