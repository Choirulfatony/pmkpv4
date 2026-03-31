<!-- ================= CORE JS ================= -->

<!-- jQuery (WAJIB untuk DataTables, Select2) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

<!-- <script src="<?= base_url('public/assets/js/ikprs.js') ?>"></script> -->

<!-- chart -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Bootstrap 5 Bundle (SUDAH TERMASUK POPPER) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- ================= ADMINLTE ================= -->
<script src="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0-beta3/dist/js/adminlte.min.js"></script>

<!-- ================= PLUGINS ================= -->

<!-- Moment -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>

<!-- Tempus Dominus -->
<script src="https://cdn.jsdelivr.net/npm/@eonasdan/tempus-dominus@6.9.4/dist/js/tempus-dominus.min.js"></script>

<!-- Select2 -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- OverlayScrollbars (AdminLTE compatible) -->
<script src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.4.7/browser/overlayscrollbars.browser.es6.min.js"></script>

<!-- bs-stepper -->
<script src="https://cdn.jsdelivr.net/npm/bs-stepper/dist/js/bs-stepper.min.js"></script>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Toastr -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<!--Flatpickr-->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<!-- DataTables -->
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

<!-- ================= CUSTOM SCRIPT ================= -->

<script>
    document.addEventListener("DOMContentLoaded", function() {

        /* ================= IDLE LOGOUT ================= */
        let idleTimer, warningTimer, countdownInterval;
        let isWarningVisible = false;

        const CLIENT_TIMEOUT = 3600000;
        const WARNING_BEFORE = 60000;

        // ⏱️ 1 MENIT
        // const CLIENT_TIMEOUT = 60000; // 60 detik
        // const WARNING_BEFORE = 10000; // 10 detik sebelum logout

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

            // HTML ROOT
            html.setAttribute('data-bs-theme', theme);

            // NAVBAR
            if (navbar) {
                navbar.classList.toggle('navbar-dark', theme === 'dark');
                navbar.classList.toggle('bg-dark', theme === 'dark');
                navbar.classList.toggle('navbar-light', theme !== 'dark');
                navbar.classList.toggle('bg-light', theme !== 'dark');
            }

            // SIDEBAR
            if (sidebar) {
                sidebar.classList.toggle('bg-dark', theme === 'dark');
                sidebar.classList.toggle('bg-body-secondary', theme !== 'dark');
            }

            // SIMPAN
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

            // Auto ikut OS jika user belum pilih manual
            if (!localStorage.getItem('theme')) {
                window.matchMedia('(prefers-color-scheme: dark)')
                    .addEventListener('change', e => {
                        applyTheme(e.matches ? 'dark' : 'light');
                    });
            }
        });

    })();

    $(function() {

        // TOGGLE CHECK ALL
        $('.checkbox-toggle').on('click', function() {

            const $checkboxes = $('.mailbox-checkbox');
            const $icon = $(this).find('i');
            const total = $checkboxes.length;
            const checked = $checkboxes.filter(':checked').length;

            if (checked === total) {
                // Uncheck semua
                $checkboxes.prop('checked', false);
                $icon.removeClass('bi-check-square-fill')
                    .addClass('bi-square');
            } else {
                // Check semua
                $checkboxes.prop('checked', true);
                $icon.removeClass('bi-square')
                    .addClass('bi-check-square-fill');
            }
        });

        // SINKRON ICON SAAT CHECKBOX DIKLIK MANUAL
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

    // window.ikpStepperInstance = null;

    // window.initIkpStepper = function() {
    //     const el = document.querySelector('#ikpStepper');
    //     if (!el || window.ikpStepperInstance) return;

    //     window.ikpStepperInstance = new Stepper(el, {
    //         linear: false,
    //         animation: true
    //     });

    //     el.addEventListener('shown.bs-stepper', function() {
    //         // updateProgress();
    //         updateNavButtons();
    //     });

    //     // ✅ STEP PERTAMA HARUS 1
    //     window.ikpStepperInstance.to(1);

    //     document.getElementById('btnNext').disabled = false;
    //     document.getElementById('btnPrev').disabled = true;

    //     // updateProgress();
    //     updateNavButtons();

    //     console.log('✅ Stepper SIAP (EVENT MODE)');
    // };

    // window.nextStep = function() {
    //     const stepper = window.ikpStepperInstance;
    //     if (!stepper) return;

    //     const totalSteps = document.querySelectorAll('.bs-stepper-header .step').length;
    //     const currentIndex = stepper._currentIndex;

    //     // STEP TERAKHIR → SUBMIT
    //     if (currentIndex >= totalSteps - 1) {
    //         submitIkp();
    //         return;
    //     }
    //     console.log('➡️ Next Step', currentIndex + 1);

    //     stepper.next(); // ⬅️ UI akan update via EVENT
    // };

    // window.prevStep = function() {
    //     const stepper = window.ikpStepperInstance;
    //     if (!stepper) return;

    //     if (stepper._currentIndex <= 1) return;
    //     console.log('⬅️ Previous Step', stepper._currentIndex - 1);

    //     stepper.previous(); // ⬅️ EVENT yang handle update
    // };

    // // function updateProgress() {
    // //     const bar = document.getElementById('ikpProgress');
    // //     if (!bar || !window.ikpStepperInstance) return;

    // //     const total = document.querySelectorAll('.bs-stepper-header .step').length;
    // //     const current = window.ikpStepperInstance._currentIndex + 1;

    // //     bar.style.width = Math.round((current / total) * 100) + '%';
    // // }

    // function updateNavButtons() {
    //     const stepper = window.ikpStepperInstance;
    //     if (!stepper) return;

    //     const totalSteps = document.querySelectorAll('.bs-stepper-header .step').length;
    //     const currentStep = stepper._currentIndex + 1;

    //     const btnPrev = document.getElementById('btnPrev');
    //     const btnNext = document.getElementById('btnNext');

    //     // BACK
    //     btnPrev.disabled = currentStep === 1;

    //     // STEP TERAKHIR
    //     if (currentStep === totalSteps) {
    //         btnNext.textContent = 'Simpan';
    //         btnNext.onclick = submitIkp;
    //     } else {
    //         btnNext.textContent = 'Selanjutnya';
    //         btnNext.onclick = nextStep;
    //     }
    // }

    // function submitIkp() {
    //     console.log('🚀 Submit IKP');
    //     // AJAX submit di sini
    // }
</script>