<!doctype html>
<html lang="id">
<head>
    <script>
        (function() {
            var theme = localStorage.getItem('theme');
            if (!theme) {
                theme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            }
            document.documentElement.setAttribute('data-bs-theme', theme);
            var bg = theme === 'dark' ? '#000000' : '#ffffff';
            var color = theme === 'dark' ? '#ffffff' : '#000000';
            var style = document.getElementById('theme-styles');
            if (style) {
                style.textContent = 'html, body { background-color: ' + bg + ' !important; color: ' + color + ' !important; }';
            }
        })();
    </script>
    <style id="theme-styles"></style>

    <title><?= isset($judul) ? $judul : '' ?></title>
    <?= @$_meta ?>
    <?= @$_css ?>
    <!-- JavaScript (WAJIB sebelum bottom navbar JS logic jalan) -->
    <?= @$_js ?>
</head>

<!-- <body class="layout-fixed fixed-header fixed-footer sidebar-expand-lg sidebar-mini sidebar-collapse bg-body-tertiary"> -->

<body class="layout-fixed fixed-header fixed-footer sidebar-expand-lg sidebar-open" style="background-color: inherit;">

    <div class="app-wrapper">

        <!-- Header -->
        <?= @$_header ?>

        <!-- Sidebar -->
        <?= @$_sidebar ?>

        <!--begin::App Main-->
        <main class="app-main">

            <div class="app-content-header">
                <?= @$_headerContent ?>
            </div>

            <div class="app-content">
                <?= @$_content ?>
            </div>

        </main>
        <!--end::App Main-->

        <!-- Footer -->
        <?= @$_footer ?>

    </div>
    <!-- END app-wrapper -->


    <!-- ⬇️⬇️⬇️ INI POSISI BENAR BOTTOM NAVBAR -->
    <?= view('_layout/_bottom_navbar') ?>
    <!-- ⬆️⬆️⬆️ -->





    <!-- ⏳ Idle Warning Modal -->
    <!-- <div class="modal fade" id="idleWarningModal" tabindex="-1"
        data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title">Sesi akan berakhir</h5>
                </div>
                <div class="modal-body text-center">
                    <p>Tidak ada aktivitas terdeteksi.</p>
                    <h1 id="countdownText" class="fw-bold text-danger">10</h1>
                    <p>detik lagi Anda akan logout otomatis.</p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button class="btn btn-success" id="stayLoggedIn">
                        Saya masih bekerja
                    </button>
                    <a href="<?= site_url('auth/logout') ?>" class="btn btn-outline-danger">
                        Logout sekarang
                    </a>
                </div>
            </div>
        </div>
    </div> -->
    <style>
        /* ===============================
            IDLE MODAL - TOP RIGHT
            ================================ */
        .modal-top-right {
            position: fixed;
            top: 1rem;
            right: 1rem;
            margin: 0;
            pointer-events: auto;
        }

        .modal.fade .modal-dialog.modal-top-right {
            transform: translate(0, -20px);
        }

        .modal.show .modal-dialog.modal-top-right {
            transform: translate(0, 0);
        }

        @media (max-width: 576px) {
            .modal-top-right {
                right: .5rem;
                left: .5rem;
                max-width: calc(100% - 1rem);
            }
        }
    </style>
    <div class="modal fade" id="idleWarningModal"
        tabindex="-1"
        data-bs-backdrop="static"
        data-bs-keyboard="false">

        <div class="modal-dialog modal-sm modal-top-right">
            <div class="modal-content border-0 shadow">

                <div class="modal-header bg-warning text-dark py-2">
                    <h6 class="modal-title mb-0">
                        ⏳ Sesi akan berakhir
                    </h6>
                </div>

                <div class="modal-body text-center py-3">
                    <p class="mb-2">Tidak ada aktivitas</p>
                    <h2 id="countdownText" class="fw-bold text-danger mb-2">10</h2>
                    <small>detik lagi logout otomatis</small>
                </div>

                <div class="modal-footer justify-content-center py-2">
                    <button class="btn btn-success btn-sm" id="stayLoggedIn">
                        Tetap login
                    </button>
                    <a href="<?= site_url('auth/logout') ?>"
                        class="btn btn-outline-danger btn-sm">
                        Logout
                    </a>
                </div>

            </div>
        </div>
    </div>

</body>

<script>
    // 🔒 Anti Back - Cek session dan redirect jika tidak valid
    (function() {
        function checkSessionAndRedirect() {
            const currentPath = window.location.pathname;
            
            if (currentPath.indexOf('ikprs') !== -1) {
                fetch('<?= site_url('auth/cek_session') ?>', {
                    method: 'GET',
                    cache: 'no-store',
                    credentials: 'same-origin'
                })
                .then(r => r.json())
                .then(data => {
                    if (data.login_source === 'APP') {
                        window.location.replace('<?= site_url('siimut/dashboard') ?>');
                    }
                })
                .catch(() => {
                    window.location.replace('<?= site_url('auth') ?>');
                });
            }
        }

        window.addEventListener('pageshow', function(event) {
            checkSessionAndRedirect();
        });

        if (window.history && window.history.pushState) {
            window.history.pushState(null, '', window.location.href);
        }
    })();
</script>


</html>

<style>
    .nav-link p {
        display: flex;
        align-items: center;
        width: 100%;
        margin-bottom: 0;
    }

    .nav-link .nav-arrow {
        margin-left: auto;
    }
</style>