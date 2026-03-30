<style>
    /* =========================
    NAVBAR GLOBAL
    ========================= */
    .navbar .nav-link {
        white-space: nowrap;
    }

    .navbar .user-image {
        width: 28px;
        height: 28px;
        object-fit: cover;
    }

    .navbar-badge {
        font-size: .65rem;
        font-weight: bold;
    }

    .navbar .form-check-input {
        cursor: pointer;
    }

    /* =========================
    MOBILE
    ========================= */
    @media (max-width: 767.98px) {
        .nav-text {
            display: none !important;
        }

        #mainNavbar .nav-link {
            padding: 0 .5rem;
        }

        .navbar-badge {
            font-size: .55rem;
        }

        .user-image {
            width: 26px;
            height: 26px;
        }

        .app-main {
            padding-bottom: 70px;
        }
    }


    /* =========================
     NOTIFICATION STYLE
    ========================= */

    /* unread background */
    .notif-unread {
        background: #f5f7fa;
    }

    [data-bs-theme="dark"] .notif-unread {
        background: #2b2f33;
    }

    /* unread text (smooth, tidak terlalu bold) */
    .notif-unread-text {
        font-weight: 500;
    }

    .notif-unread-text .fw-semibold {
        font-weight: 600;
    }

    /* hover */
    .notif-open:hover {
        background: #eef2f6;
    }

    [data-bs-theme="dark"] .notif-open:hover {
        background: #3a3f44;
    }

    /* dot indicator */
    .notif-dot {
        width: 6px;
        height: 6px;
        background: #0d6efd;
        border-radius: 50%;
        margin-right: 8px;
        margin-top: 6px;
        flex-shrink: 0;
        animation: pulse 1.5s infinite;
    }

    [data-bs-theme="dark"] .notif-dot {
        background: #4dabf7;
    }

    /* animation */
    @keyframes pulse {
        0% {
            opacity: 1;
        }

        50% {
            opacity: 0.3;
        }

        100% {
            opacity: 1;
        }
    }

    /* dropdown dark mode */
    [data-bs-theme="dark"] .dropdown-menu {
        background-color: #2b2f33;
        color: #fff;
    }

    .dropdown-menu {
        border-radius: 14px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.25);
    }

    [data-bs-theme="dark"] .dropdown-item {
        color: #ddd;
    }

    /* =========================
    SCROLL NOTIF
    ========================= */
    #notif-list {
        max-height: 320px;
        overflow-y: auto;
    }

    /* sembunyikan scrollbar (modern UI) */
    #notif-list::-webkit-scrollbar {
        width: 0;
        height: 0;
    }

    #notif-list .dropdown-item {
        white-space: normal;
    }

    #notif-list small {
        display: block;
        line-height: 1.3;
        word-break: break-word;
    }

    .notif-status {
        margin-top: 8px;
        font-size: 0.82rem;
        opacity: 0.9;
    }



    /* =========================
    ITEM
    ========================= */
    .notif-item {
        padding: 12px 14px;
        border-radius: 12px;
    }

    /* hover */
    .notif-item:hover {
        background: rgba(255, 255, 255, 0.05);
        transform: translateX(3px);
    }

    /* disabled */
    .notif-disabled {
        pointer-events: none;
        opacity: 0.5;
    }

    /* =========================
    TEXT STYLE
    ========================= */
    .notif-title {
        font-weight: 600;
        font-size: 0.95rem;
    }

    .notif-desc {
        font-size: 0.85rem;
        margin-top: 3px;
        opacity: 0.85;
    }

    .notif-status {
        margin-top: 8px;
        font-size: 0.75rem;
    }

    /* =========================
      STATUS
    ========================= */
    .notif-status {
        margin-top: 6px;
        font-size: 0.75rem;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    /* =========================
     ICON
    ========================= */
    .notif-icon {
        font-size: 1.1rem;
        margin-top: 2px;
    }

    /* =========================
    UNREAD
    ========================= */
    .notif-unread {
        background: rgba(13, 110, 253, 0.05);
    }

    [data-bs-theme="dark"] .notif-unread {
        background: rgba(77, 171, 247, 0.08);
    }

    .notif-unread-text {
        font-weight: 500;
    }

    /* =========================
    DOT
    ========================= */
    .notif-dot {
        width: 8px;
        height: 8px;
        background: #4dabf7;
        border-radius: 50%;
        margin-top: 6px;
        flex-shrink: 0;
    }

    [data-bs-theme="dark"] .notif-dot {
        background: #4dabf7;
    }

    .dropdown-divider {
        margin: 4px 0;
        opacity: 0.2;
    }

    .notif-item {
        animation: fadeIn 0.3s ease;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(5px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .dropdown-footer {
        text-align: center;
        font-weight: 500;
        padding: 10px;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
    }

    /* hover biar halus */
    .dropdown-footer:hover {
        background: rgba(255, 255, 255, 0.05);
    }
</style>



<!--begin::Header / Navbar-->
<nav id="mainNavbar"
    class="app-header navbar navbar-expand border-bottom">
    <div class="container-fluid">

        <!-- LEFT -->
        <ul class="navbar-nav align-items-center">

            <!-- Sidebar toggle -->
            <li class="nav-item">
                <a class="nav-link" data-lte-toggle="sidebar" href="#">
                    <i class="bi bi-list"></i>
                </a>
            </li>

            <!-- Dashboard -->
            <li class="nav-item">
                <a href="<?= site_url('dashboard') ?>" class="nav-link">
                    <i class="bi bi-speedometer"></i>
                    <span class="nav-text ms-1">Dashboard</span>
                </a>
            </li>

        </ul>

        <!-- RIGHT -->
        <ul class="navbar-nav ms-auto align-items-center">

            <!-- CLOCK -->

            <li class="nav-item">
                <a class="nav-link" href="javascript:void(0)">
                    <i class="bi bi-clock" id="clockIcon" title="--:--:--"></i>
                    <span class="nav-text ms-1" id="jam"></span>
                </a>
            </li>


            <!-- NOTIFICATION -->
            <li class="nav-item dropdown">
                <a href="#" class="nav-link position-relative"
                    data-bs-toggle="dropdown">
                    <i class="bi bi-bell"></i>
                    <span id="badge-notif_header" class="badge bg-danger navbar-badge"></span>
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-lg">
                    <span class="dropdown-item dropdown-header">
                        Informasi
                    </span>
                    <div class="dropdown-divider"></div>
                    <!-- <a href="#" class="dropdown-item">
                        <i class="bi bi-envelope me-2"></i> Pesan baru
                    </a> -->
                    <div id="notif-list"></div>

                    <div class="dropdown-divider"></div>
                    <a href="<?= base_url('ikprs/form_inbox_karu') ?>"
                        class="dropdown-item dropdown-footer">
                        Lihat semua notifikasi
                    </a>
                </div>
            </li>

            <!-- DARK MODE -->
            <li class="nav-item">
                <a href="#" class="nav-link d-flex align-items-center gap-2">
                    <!-- <i id="themeIcon" class="bi bi-moon"></i> -->
                    <!-- <span class="nav-text">Theme</span> -->
                    <div class="form-check form-switch m-0">
                        <input class="form-check-input mt-0"
                            type="checkbox"
                            id="darkModeToggle">
                    </div>
                </a>
            </li>

            <!-- USER MENU -->
            <li class="nav-item dropdown user-menu">
                <a href="#"
                    class="nav-link dropdown-toggle d-flex align-items-center gap-2"
                    data-bs-toggle="dropdown">
                    <img src="<?= base_url('assets/adminlte/img/logorssmnew.png') ?>"
                        class="user-image rounded-circle shadow">
                </a>

                <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
                    <li class="user-header text-bg-success">
                        <img src="<?= base_url('assets/adminlte/img/logorssmnew.png') ?>"
                            class="rounded-circle shadow">
                        <p>
                        <h5 class="fw-bold mb-0">
                            <?= esc(session('nama_lengkap')) ?>
                        </h5>
                        <h5 class="fw-bold mb-0">
                            <?= esc(session('hris_full_name')) ?>
                        </h5>
                        <small><?= esc(session()->get('department_name')) ?></small>
                        <small><?= esc(session()->get('hris_nip')) ?></small>

                        </p>
                    </li>
                    <li class="user-footer">
                        <a href="#" class="btn btn-default btn-flat">Profile</a>
                        <a href="<?= site_url('auth/logout') ?>"
                            class="btn btn-default btn-flat float-end">
                            Sign out
                        </a>
                    </li>
                </ul>
            </li>

        </ul>
    </div>
</nav>
<!--end::Header / Navbar-->

<script>
    window.user_id = "<?= session('hris_user_id') ?? '' ?>";
    window.user_role = "<?= session('user_role') ?? '' ?>";
    window.lastInboxCount = 0;
    window.lastDraftCount = 0;
    window.lastSendCount = 0;
    window.lastNotifCount = 0;
</script>

<script>
    $(document).ready(function() {

        refreshNotif();

        setInterval(function() {
            refreshNotif();
        }, 8000);
    });


    $(document).on('click', '.notif-open', function(e) {

        e.preventDefault();
        e.stopPropagation();

        const el = $(this);
        const insiden_id = el.data('insiden');

        console.log('CLICK notif:', insiden_id);

        // ❌ pelapor tidak boleh klik langsung ke detail dari notifikasi
        if (user_role === 'PELAPOR') {
            return false;
        }

        // Update UI dulu
        el.removeClass('notif-unread notif-unread-text');
        el.find('.notif-dot').remove();

        // Langsung load detail tanpa AJAX tandaiDibaca dulu
        // Nanti saat load detail, akan otomatis tandai baca
        if (typeof loadDetailInsiden === "function") {
            loadDetailInsiden(insiden_id, 'inbox');
        } else {
            window.location.href = "<?= site_url('ikprs/menu') ?>?id=" + insiden_id;
        }

        return false;

    });


    function refreshNotif() {
        $.ajax({
            url: "<?= base_url('ikprs/counter-ajax') ?>",
            type: "GET",
            dataType: "json",
            success: function(res) {

                let notif = res.total_notif ?? 0;
                let inbox = res.total_inbox ?? 0;
                let draft = res.total_draft ?? 0;
                let send = res.total_send ?? 0;
                let totalNotif = res.total_notif ?? 0;

                /* =============================
                      🔔 SOUND NOTIFIKASI (BERULANG KALAU BELUM DIBUKA)
                      Hanya untuk KARU dan KOMITE, tidak untuk PELAPOR
                 ============================= */
                if (totalNotif > 0 && totalNotif >= lastNotifCount && (user_role === 'KARU' || user_role === 'KOMITE')) {
                    // Mainkan suara beep pake AudioContext (tanpa perlu file)
                    // Berulang sampai user buka
                    try {
                        const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                        const oscillator = audioCtx.createOscillator();
                        const gainNode = audioCtx.createGain();
                        
                        oscillator.connect(gainNode);
                        gainNode.connect(audioCtx.destination);
                        
                        oscillator.frequency.value = 800;
                        oscillator.type = 'sine';
                        gainNode.gain.setValueAtTime(0.3, audioCtx.currentTime);
                        gainNode.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.5);
                        
                        oscillator.start(audioCtx.currentTime);
                        oscillator.stop(audioCtx.currentTime + 0.5);
                    } catch(e) {
                        console.log('Audio error:', e);
                    }
                } else if (totalNotif == 0) {
                    // Reset when no more notifications
                    lastNotifCount = 0;
                }
                lastNotifCount = totalNotif;

                /* =============================
                            AUTO REFRESH INBOX
                         ============================= */

                if (inbox > lastInboxCount) {
                    console.log("Inbox baru masuk");
                    if (typeof loadInbox === "function") {
                        loadInbox();
                    }
                }

                /* =============================
                            AUTO REFRESH DRAFTS
                         ============================= */

                if (draft > lastDraftCount) {
                    console.log("Draft baru masuk");
                    if (typeof loadDrafts === "function") {
                        loadDrafts();
                    }
                }

                /* =============================
                            AUTO REFRESH SENT
                         ============================= */

                if (send > lastSendCount) {
                    console.log("Sent baru masuk");
                    if (typeof loadSend === "function") {
                        loadSend();
                    }
                }

                lastInboxCount = inbox;
                lastDraftCount = draft;
                lastSendCount = send;


                /* =============================
                   UPDATE BADGE COUNTER
                ============================= */

                $('#badge-notif_header').text(notif);

                $('#badge-notif')
                    .text(notif)
                    .removeClass('bg-info')
                    .addClass('bg-info');

                $('#badge-inbox')
                    .text(inbox)
                    .removeClass('bg-primary')
                    .addClass('bg-primary');

                $('#badge-draft')
                    .text(draft)
                    .removeClass('bg-warning')
                    .addClass('bg-warning');

                $('#badge-send')
                    .text(send)
                    .removeClass('bg-success')
                    .addClass('bg-success');


                /* =============================
                   UPDATE DROPDOWN NOTIF
                ============================= */

                $('#notif-header').text(notif + " Notifications");

                let html = '';

                if (!res.data || res.data.length === 0) {

                    html = `
                <a class="dropdown-item text-center text-muted">
                    Tidak ada notifikasi
                </a>`;

                } else {


                    res.data.forEach(function(item) {

                        // =============================
                        // ✅ ICON JENIS (ATAS)
                        // =============================
                        let iconJenis = "bi bi-info-circle";

                        switch (item.jenis) {
                            case "KTD":
                                iconJenis = "bi bi-exclamation-triangle-fill text-danger";
                                break;

                            case "KNC":
                                iconJenis = "bi bi-exclamation-circle-fill text-warning";
                                break;

                            case "KTC":
                                iconJenis = "bi bi-info-circle-fill text-primary";
                                break;

                            case "KPC":
                                iconJenis = "bi bi-shield-exclamation text-purple";
                                break;

                            case "SENTINEL":
                                iconJenis = "bi bi-exclamation-octagon-fill text-danger";
                                break;
                        }

                        // =============================
                        // 🔥 STATUS (SESUAI ROLE)
                        // =============================
                        let warna_status = "text-danger";
                        let status_read = "Baru";
                        let iconStatus = "bi bi-circle";

                        // Cek status laporan
                        const status = item.status_laporan;

                        if (user_role === 'PELAPOR') {
                            // Tampilkan status laporan + sudah dibaca atau belum
                            // Cek juga apakah KARU sudah membaca dari karu_read_at
                            const hasKaruRead = item.karu_read_at !== null && item.karu_read_at !== '';

                            if (item.is_read == 1) {
                                // Sudah dibaca (sudah klik notifikasi)
                                if (status === 'DRAFT') {
                                    status_read = "Menunggu Verifikasi KARU";
                                    warna_status = "text-secondary";

                                } else if (status === 'KARU') {
                                    status_read = hasKaruRead ? "Telah Diverifikasi KARU" : "Dalam Verifikasi KARU";
                                    warna_status = "text-primary";

                                } else if (status === 'INSTALASI') {
                                    status_read = "Dalam Analisis Komite PMKP";
                                    warna_status = "text-warning";

                                } else if (status === 'SELESAI') {
                                    status_read = "Laporan Selesai";
                                    warna_status = "text-success";

                                } else {
                                    status_read = status || "Sudah dibaca";
                                }
                            } else {
                                // Belum dibaca (belum klik notifikasi)
                                if (status === 'DRAFT') {
                                    status_read = "Menunggu Verifikasi KARU";
                                    warna_status = "text-danger";

                                } else if (status === 'KARU') {
                                    status_read = hasKaruRead ? "Telah Diverifikasi KARU" : "Dalam Verifikasi KARU";
                                    warna_status = hasKaruRead ? "text-primary" : "text-danger";

                                } else if (status === 'INSTALASI') {
                                    status_read = "Dalam Analisis Komite PMKP";
                                    warna_status = "text-danger";

                                } else if (status === 'SELESAI') {
                                    status_read = "Laporan Selesai";
                                    warna_status = "text-success";

                                } else {
                                    status_read = "Belum dibaca";
                                    warna_status = "text-danger";
                                }
                            }

                            iconStatus = item.is_read == 0 ? "bi bi-circle-fill" : "bi bi-circle";
                        } else if (user_role === 'KARU') {
                            // KARU: cek dari is_read dan siapa yang sudah baca
                            if (item.is_read == 1) {

                                if (item.komite_read_at) {
                                    status_read = "Telah Dibaca Komite";
                                    warna_status = "text-success";
                                    iconStatus = "bi bi-check-circle-fill";

                                } else if (item.karu_read_at) {
                                    status_read = "Sudah Dibaca";
                                    warna_status = "text-primary";
                                    iconStatus = "bi bi-eye-fill";

                                } else {
                                    status_read = "Sudah Dibaca";
                                    warna_status = "text-primary";
                                }

                            } else {
                                status_read = "Belum Dibaca";
                                warna_status = "text-danger";
                            }
                        } else if (user_role === 'KOMITE') {
                            // KOMITE: cek komite sudah baca atau belum
                            if (item.komite_read_at) {
                                status_read = "Sudah Dibaca";
                                warna_status = "text-success";
                                iconStatus = "bi bi-check-circle-fill";

                            } else {
                                status_read = "Belum Dibaca";
                                warna_status = "text-danger";
                            }

                        }

                        // let bg = item.is_read == 0 ? "bg-light" : "";
                        let bg = item.is_read == 0 ? "notif-unread" : "";
                        let bold = item.is_read == 0 ? "notif-unread-text" : "";

                        let disabledClass = '';

                        // PELAPOR tidak bisa klik dari notifikasi
                        if (user_role === 'PELAPOR') {
                            disabledClass = 'notif-disabled';
                            // Disable klik dengan inline style juga
                        } else {
                            // Untuk KARU dan KOMITE, wajib ada class notif-open
                            disabledClass = 'notif-open';
                        }

                        // =============================
                        // HTML
                        // =============================
                        html += `
                        <a href="#" 
                        class="dropdown-item notif-item ${disabledClass} ${bg} ${bold}"
                        data-insiden="${item.insiden_id}">

                            <div class="d-flex align-items-start gap-2">

                                ${item.is_read == 0 ? '<div class="notif-dot"></div>' : ''}

                                <div class="notif-icon">
                                    <i class="${iconJenis}"></i>
                                </div>

                                <div class="flex-grow-1">

                                    <div class="d-flex justify-content-between">

                                        <div class="notif-title">
                                            ${item.jenis ?? '-'} - ${item.unit ?? '-'}
                                        </div>

                                        <div class="notif-time">
                                            ${item.waktu_lalu ?? ''}
                                        </div>

                                    </div>

                                    <div class="notif-desc">
                                        ${item.status_text ?? ''}
                                    </div>

                                    <div class="notif-status ${warna_status}">
                                        <i class="${iconStatus}"></i> ${status_read}
                                    </div>

                                </div>

                            </div>
                        </a>
                        `;
                    });

                }

                $('#notif-list').html(html);

            }
        });
    }

    function updateClock() {
        const now = new Date();

        const h = String(now.getHours()).padStart(2, '0');
        const m = String(now.getMinutes()).padStart(2, '0');
        const s = String(now.getSeconds()).padStart(2, '0');

        const time = `${h}:${m}:${s}`;

        // tampilkan di navbar
        const jam = document.getElementById('jam');
        if (jam) jam.textContent = time;

        // tampilkan di title (tooltip)
        const icon = document.getElementById('clockIcon');
        if (icon) icon.setAttribute('title', `Jam sekarang: ${time}`);
    }

    document.addEventListener('DOMContentLoaded', () => {
        updateClock();
        setInterval(updateClock, 1000);
    });

    function applyTheme(theme) {
        const html = document.documentElement;
        const navbar = document.getElementById('mainNavbar');
        const bottomNavbar = document.getElementById('bottomNavbar');
        const icon = document.getElementById('themeIcon');

        html.setAttribute('data-bs-theme', theme);

        if (navbar) {
            navbar.classList.toggle('navbar-dark', theme === 'dark');
            navbar.classList.toggle('bg-dark', theme === 'dark');
            navbar.classList.toggle('navbar-light', theme !== 'dark');
            navbar.classList.toggle('bg-light', theme !== 'dark');
        }

        if (bottomNavbar) {
            bottomNavbar.classList.toggle('navbar-dark', theme === 'dark');
            bottomNavbar.classList.toggle('bg-dark', theme === 'dark');
            bottomNavbar.classList.toggle('navbar-light', theme !== 'dark');
            bottomNavbar.classList.toggle('bg-light', theme !== 'dark');
        }

        if (icon) {
            icon.className = theme === 'dark' ?
                'bi bi-sun' :
                'bi bi-moon';
        }

        localStorage.setItem('theme', theme);
    }

    document.addEventListener('DOMContentLoaded', () => {
        updateClock();
        setInterval(updateClock, 1000);

        const toggle = document.getElementById('darkModeToggle');
        const savedTheme = localStorage.getItem('theme') || 'light';

        applyTheme(savedTheme);

        if (toggle) {
            toggle.checked = savedTheme === 'dark';
            toggle.addEventListener('change', () => {
                applyTheme(toggle.checked ? 'dark' : 'light');
            });
        }
    });
</script>