<?php
/**
 * SIIMUT _js.php
 *
 * Toggle SIIMUT_OFFLINE_MODE in app/Config/Constants.php (or via .env):
 *   - true  = load from public/assets/adminlte/* (offline)
 *   - false = load from CDN (online)
 */
$offline = defined('SIIMUT_OFFLINE_MODE') ? SIIMUT_OFFLINE_MODE : true;

$ver = defined('SIIMUT_ASSET_VERSION') ? '?v=' . SIIMUT_ASSET_VERSION : '';
$asset = function (string $cdnUrl, string $localPath) use ($offline, $ver): string {
    return $offline ? base_url($localPath . $ver) : $cdnUrl;
};

$JQUERY_JS          = $asset('https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js',                                              'assets/adminlte/js/jquery-3.7.1.min.js');
$CHART_JS           = $asset('https://cdn.jsdelivr.net/npm/chart.js',                                                                          'assets/adminlte/plugins/chart.js');
$BOOTSTRAP_JS       = $asset('https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js',                                  'assets/adminlte/plugins/bootstrap.bundle.min.js');
$ADMINLTE_JS        = $asset('https://cdn.jsdelivr.net/npm/admin-lte@4.0.0-beta3/dist/js/adminlte.min.js',                                   'assets/adminlte/js/adminlte.min.js');
$MOMENT_JS          = $asset('https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js',                                         'assets/adminlte/js/moment.min.js');
$TEMPUS_V6_JS       = $asset('https://cdn.jsdelivr.net/npm/@eonasdan/tempus-dominus@6.9.4/dist/js/tempus-dominus.min.js',                     'assets/adminlte/plugins/tempus-dominus/tempus-dominus.min.js');
$SELECT2_JS         = $asset('https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',                                        'assets/adminlte/js/select2.full.min.js');
$OVERLAYSCROLL_JS   = $asset('https://cdn.jsdelivr.net/npm/overlayscrollbars@2.4.7/browser/overlayscrollbars.browser.es6.min.js',             'assets/adminlte/js/overlayscrollbars.browser.es6.min.js');
$BS_STEPPER_JS      = $asset('https://cdn.jsdelivr.net/npm/bs-stepper/dist/js/bs-stepper.min.js',                                             'assets/adminlte/plugins/bs-stepper/bs-stepper.min.js');
$SWEETALERT2_JS     = $asset('https://cdn.jsdelivr.net/npm/sweetalert2@11',                                                                   'assets/adminlte/js/sweetalert2@11.all.min.js');
$TOASTR_JS          = $asset('https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js',                                          'assets/adminlte/js/toastr.min.js');
$FLATPICKR_JS       = $asset('https://cdn.jsdelivr.net/npm/flatpickr',                                                                         'assets/adminlte/plugins/flatpickr/flatpickr.min.js');
$DATATABLES_JS      = $asset('https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js',                                                 'assets/adminlte/js/jquery.dataTables.min.js');
$DATATABLES_B5_JS   = $asset('https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js',                                              'assets/adminlte/js/dataTables.bootstrap5.min.js');
?>
<!-- ================= CORE JS ================= -->

<!-- jQuery (WAJIB untuk DataTables, Select2) -->
<script src="<?= $JQUERY_JS ?>"></script>

<!-- chart -->
<script src="<?= $CHART_JS ?>"></script>

<!-- Bootstrap 5 Bundle (SUDAH TERMASUK POPPER) -->
<script src="<?= $BOOTSTRAP_JS ?>"></script>

<!-- ================= ADMINLTE ================= -->
<script src="<?= $ADMINLTE_JS ?>"></script>

<!-- ================= PLUGINS ================= -->

<!-- Tempus Dominus -->
<script src="<?= $TEMPUS_V6_JS ?>"></script>

<!-- Select2 -->
<script src="<?= $SELECT2_JS ?>"></script>

<!-- OverlayScrollbars (AdminLTE compatible) -->
<script src="<?= $OVERLAYSCROLL_JS ?>"></script>

<!-- bs-stepper -->
<script src="<?= $BS_STEPPER_JS ?>"></script>

<!-- SweetAlert2 -->
<script src="<?= $SWEETALERT2_JS ?>"></script>

<!-- Toastr -->
<script src="<?= $TOASTR_JS ?>"></script>

<!-- Flatpickr -->
<script src="<?= $FLATPICKR_JS ?>"></script>

<!-- DataTables -->
<script src="<?= $DATATABLES_JS ?>"></script>
<script src="<?= $DATATABLES_B5_JS ?>"></script>

<!-- ================= CUSTOM SCRIPT ================= -->

<script>
    document.addEventListener("DOMContentLoaded", function() {

        /* ================= IDLE LOGOUT ================= */
        let idleTimer, warningTimer, countdownInterval;
        let isWarningVisible = false;

        const CLIENT_TIMEOUT = 3600000;
        const WARNING_BEFORE = 60000;

        const warningModal = document.getElementById('idleWarningModal');
        const stayBtn = document.getElementById('stayLoggedIn');
        const countdownText = document.getElementById('countdownText');

        if (warningModal && stayBtn && countdownText) {
            const modal = bootstrap.Modal.getOrCreateInstance(warningModal);

            function resetActivity() {
                if (isWarningVisible) return;

                clearTimeout(idleTimer);
                clearTimeout(warningTimer);
                clearInterval(countdownInterval);

                warningTimer = setTimeout(showWarning, CLIENT_TIMEOUT - WARNING_BEFORE);
                idleTimer = setTimeout(() => {
                    window.location.href = "<?= site_url('auth/logout') ?>";
                }, CLIENT_TIMEOUT);
            }

            function showWarning() {
                let seconds = WARNING_BEFORE / 1000;
                isWarningVisible = true;

                modal.show();
                countdownText.textContent = seconds;

                countdownInterval = setInterval(() => {
                    countdownText.textContent = --seconds;
                    if (seconds <= 0) clearInterval(countdownInterval);
                }, 1000);
            }

            ['mousemove', 'keydown', 'scroll', 'input', 'focus']
            .forEach(evt => document.addEventListener(evt, resetActivity));

            stayBtn.addEventListener('click', e => {
                e.preventDefault();
                isWarningVisible = false;
                modal.hide();
                resetActivity();
            });

            resetActivity();
        }

        /* ================= OVERLAY SCROLLBAR ================= */
        const sidebar = document.querySelector('.sidebar-wrapper');
        if (sidebar && OverlayScrollbarsGlobal?.OverlayScrollbars) {
            OverlayScrollbarsGlobal.OverlayScrollbars(sidebar, {
                scrollbars: {
                    theme: 'os-theme-light',
                    autoHide: 'leave',
                    clickScroll: true
                }
            });
        }

    });

    (function() {

        const html = document.documentElement;
        const navbar = document.getElementById('mainNavbar');
        const sidebar = document.getElementById('mainSidebar');
        const toggle = document.getElementById('darkModeToggle');

        function applyTheme(theme) {

            html.setAttribute('data-bs-theme', theme);

            if (navbar) {
                navbar.classList.toggle('navbar-dark', theme === 'dark');
                navbar.classList.toggle('bg-dark', theme === 'dark');
                navbar.classList.toggle('navbar-light', theme !== 'dark');
                navbar.classList.toggle('bg-light', theme !== 'dark');
            }

            if (sidebar) {
                sidebar.classList.toggle('bg-dark', theme === 'dark');
                sidebar.classList.toggle('bg-body-secondary', theme !== 'dark');
            }

            localStorage.setItem('theme', theme);
        }

        document.addEventListener('DOMContentLoaded', () => {
            const savedTheme = localStorage.getItem('theme') || 'light';

            applyTheme(savedTheme);

            if (toggle) {
                toggle.checked = savedTheme === 'dark';
                toggle.addEventListener('change', () => {
                    applyTheme(toggle.checked ? 'dark' : 'light');
                });
            }

            if (!localStorage.getItem('theme')) {
                window.matchMedia('(prefers-color-scheme: dark)')
                    .addEventListener('change', e => {
                        applyTheme(e.matches ? 'dark' : 'light');
                    });
            }
        });

    })();

    $(function() {

        $('.checkbox-toggle').on('click', function() {

            const $checkboxes = $('.mailbox-checkbox');
            const $icon = $(this).find('i');
            const total = $checkboxes.length;
            const checked = $checkboxes.filter(':checked').length;

            if (checked === total) {
                $checkboxes.prop('checked', false);
                $icon.removeClass('bi-check-square-fill')
                    .addClass('bi-square');
            } else {
                $checkboxes.prop('checked', true);
                $icon.removeClass('bi-square')
                    .addClass('bi-check-square-fill');
            }
        });

        $('.mailbox-checkbox').on('change', function() {
            const total = $('.mailbox-checkbox').length;
            const checked = $('.mailbox-checkbox:checked').length;
            const $icon = $('.checkbox-toggle i');

            if (checked === total) {
                $icon.removeClass('bi-square')
                    .addClass('bi-check-square-fill');
            } else {
                $icon.removeClass('bi-check-square-fill')
                    .addClass('bi-square');
            }
        });
    });

    function toastWarning(msg) {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'warning',
            iconColor: '#f0ad4e',
            title: msg,
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
    }

    function toastError(msg) {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'error',
            iconColor: '#d9534f',
            title: msg,
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
    }

    function toastSuccess(msg) {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            iconColor: '#5cb85c',
            title: msg,
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
    }
</script>
