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
    <!-- JavaScript (WAJIB sebelum logic JS bottom navbar jalan) -->
    <?= @$_js ?>
</head>

<!-- <body class="layout-fixed fixed-header fixed-footer sidebar-expand-lg sidebar-mini sidebar-collapse bg-body-tertiary"> -->

<body class="layout-fixed fixed-header fixed-footer sidebar-expand-lg sidebar-open" style="background-color: inherit;">

    <div class="app-wrapper">

        <!-- Bagian Header -->
        <?= @$_header ?>

        <!-- Bagian Sidebar -->
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

        <!-- Bagian Footer -->
        <?= @$_footer ?>

    </div>
    <!-- AKHIR app-wrapper -->


    <!-- ⬇️⬇️⬇️ INI POSISI BENAR BOTTOM NAVBAR -->
    <?= view('_layout/_bottom_navbar') ?>
    <!-- ⬆️⬆️⬆️ -->





    <!-- ⏳ Modal Peringatan Idle -->
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
            MODAL IDLE - POJOK KANAN ATAS
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

<script>
// Paksa sidebar collapse setelah AdminLTE jalan
(function() {
    var t = setTimeout(function() {
        document.body.classList.remove('sidebar-open');
        document.body.classList.add('sidebar-collapse');
    }, 100);
})();
</script>
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
    // 🔒 Anti Back - Periksa session dan redirect jika tidak valid
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

    /* ===============================
       RESPONSIVE GLOBAL
    ================================ */
    @media (max-width: 767.98px) {
        .app-content {
            padding: 0.5rem !important;
        }
        .app-content-header {
            padding: 0.5rem 0.5rem 0 !important;
        }
        .content-header h5 {
            font-size: 1rem;
        }
        .card-body {
            padding: 0.5rem !important;
        }
        .card-header {
            padding: 0.5rem 0.5rem !important;
        }
        .info-box {
            min-height: 60px;
        }
        .info-box .info-box-icon {
            width: 50px;
            font-size: 1.2rem;
        }
        .info-box .info-box-content {
            padding: 0 0 0 0.5rem;
        }
        .info-box .info-box-text {
            font-size: 0.7rem;
            white-space: nowrap;
        }
        .info-box .info-box-number {
            font-size: 1rem;
        }
        table.dataTable {
            font-size: 0.75rem;
        }
        table.dataTable th,
        table.dataTable td {
            padding: 0.25rem 0.3rem !important;
        }
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter,
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_paginate {
            font-size: 0.75rem;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 0;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button .page-link {
            padding: 0.2rem 0.5rem;
            font-size: 0.7rem;
        }
        .dataTables_length select {
            font-size: 0.75rem;
            padding: 0.15rem 0.3rem !important;
        }
        .dataTables_filter input {
            font-size: 0.75rem;
            padding: 0.15rem 0.3rem !important;
            max-width: 120px;
        }
        .modal-body {
            padding: 0.5rem !important;
        }
        .modal-header {
            padding: 0.5rem !important;
        }
        .modal-footer {
            padding: 0.5rem !important;
        }
        .btn-sm {
            font-size: 0.7rem;
            padding: 0.2rem 0.4rem;
        }
        .container-fluid {
            padding-left: 0.5rem;
            padding-right: 0.5rem;
        }
        .app-footer {
            font-size: 10px !important;
            padding: 0.5rem !important;
            text-align: center;
        }
        .app-footer .float-end {
            float: none !important;
            display: block !important;
        }
        .row {
            margin-left: -0.25rem;
            margin-right: -0.25rem;
        }
        .row > [class*="col-"] {
            padding-left: 0.25rem;
            padding-right: 0.25rem;
        }
        .card-tools .input-group {
            max-width: 100% !important;
        }
        .table-responsive {
            font-size: 0.7rem;
        }
        .breadcrumb {
            font-size: 0.75rem;
            flex-wrap: nowrap;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
    }

    @media (max-width: 575.98px) {
        .app-content {
            padding: 0.25rem !important;
        }
        .info-box {
            margin-bottom: 0.5rem;
        }
        h6 {
            font-size: 0.85rem;
        }
        .card-header .card-title {
            font-size: 0.85rem;
        }
        .nav-pills .nav-link {
            font-size: 0.7rem;
            padding: 0.25rem 0.3rem;
        }
    }

    @media (min-width: 768px) and (max-width: 991.98px) {
        .app-content {
            padding: 0.75rem !important;
        }
        .card-body {
            padding: 0.75rem !important;
        }
    }
</style>