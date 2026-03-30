<!-- <div style="background: yellow; padding: 15px; margin: 10px; border: 3px solid red; font-size: 18px; font-weight: bold;">
    <strong>DEBUG INBOX:</strong><br>
    hris_user_id (session) = <?= session('hris_user_id') ?><br>
    user_role = <?= session('user_role') ?><br>
    <span style="font-size: 24px; color: blue;">TOTAL DATA = <?= $total ?></span><br>
    karu_id_di_database = 17136<br>
    <br>
    <strong>PARAMETER:</strong><br>
    keyword = "<?= $keyword ?? '' ?>"<br>
    page = <?= $page ?? 1 ?>
    <br>
    <strong>PANGGIL MODEL LANGSUNG:</strong><br>
    <?php 
    $model = new \App\Models\IkpInsidenModel();
    $user_id = session('hris_user_id');
    
    // Panggil langsung seperti controller
    $totalTest = $model->countInboxFiltered($user_id, '', 'inbox');
    echo "countInboxFiltered(user_id, '', 'inbox'): " . $totalTest . "<br>";
    
    // Dengan keyword kosong
    $totalTest2 = $model->countInboxFiltered($user_id, null, 'inbox');
    echo "countInboxFiltered(user_id, null, 'inbox'): " . $totalTest2 . "<br>";
    ?>
</div> -->
<div class="card-header mailbox-header d-flex align-items-start gap-2 flex-wrap">

    <!-- LEFT TOOLBAR -->
    <div class="d-flex gap-2">
        <button class="btn btn-mailbox btn-sm btn-inbox-reload " title="Reload">
            <i class="bi bi-arrow-repeat"></i>
        </button>
    </div>

    <!-- FILTERS -->
    <div class="d-flex gap-2 align-items-center">
        <select class="form-select form-select-sm" id="filterTriwulan" style="width: 120px;">
            <option value="">Triwulan</option>
            <option value="1" <?= ($filters['triwulan'] ?? '') == '1' ? 'selected' : '' ?>>Triwulan 1</option>
            <option value="2" <?= ($filters['triwulan'] ?? '') == '2' ? 'selected' : '' ?>>Triwulan 2</option>
            <option value="3" <?= ($filters['triwulan'] ?? '') == '3' ? 'selected' : '' ?>>Triwulan 3</option>
            <option value="4" <?= ($filters['triwulan'] ?? '') == '4' ? 'selected' : '' ?>>Triwulan 4</option>
        </select>

        <select class="form-select form-select-sm" id="filterSemester" style="width: 120px;">
            <option value="">Semester</option>
            <option value="1" <?= ($filters['semester'] ?? '') == '1' ? 'selected' : '' ?>>Semester 1</option>
            <option value="2" <?= ($filters['semester'] ?? '') == '2' ? 'selected' : '' ?>>Semester 2</option>
        </select>

        <select class="form-select form-select-sm" id="filterTahun" style="width: 100px;">
            <option value="">Tahun</option>
            <?php $tahunSekarang = date('Y'); ?>
            <?php for ($t = $tahunSekarang; $t >= $tahunSekarang - 5; $t--): ?>
                <option value="<?= $t ?>" <?= ($filters['tahun'] ?? '') == $t ? 'selected' : '' ?>><?= $t ?></option>
            <?php endfor; ?>
        </select>

        <button class="btn btn-sm btn-outline-secondary" id="btnApplyFilter" title="Terapkan Filter">
            <i class="bi bi-funnel"></i>
        </button>

        <button class="btn btn-sm btn-outline-danger" id="btnClearFilter" title="Hapus Filter" <?= empty($filters['tahun']) && empty($filters['semester']) && empty($filters['triwulan']) ? 'style="display:none"' : '' ?>>
            <i class="bi bi-x-lg"></i>
        </button>
    </div>

    <!-- PUSH RIGHT -->
    <div class="ms-auto"></div>

    <!-- RIGHT AREA: SEARCH + INFO -->
    <div class="d-flex flex-column align-items-end gap-1">

        <!-- SEARCH -->
        <div class="input-group input-group-sm mailbox-search" style="width: 220px;">
            <input type="text"
                class="form-control"
                id="searchInbox"
                placeholder="Cari Inbox..."
                value="<?= esc($keyword ?? '') ?>">

            <button class="btn btn-primary btn-search-inbox" type="button">
                <i class="bi bi-search"></i>
            </button>
        </div>

    </div>
</div>

<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table table-hover mailbox-table mb-0">
            <tbody>

                <?php if (empty($inbox)): ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted p-4">
                            Tidak ada Kotak Masuk
                        </td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($inbox as $row): ?>

                    <tr class="inbox-row <?= ($row['is_read'] ?? 1) == 0 ? 'fw-bold' : '' ?>"
                        data-id="<?= esc($row['id']) ?>"
                        style="cursor:pointer">

                        <?php
                        $warna = '';
                        $icon  = '';

                        if (!empty($row['grading_risiko'])) {

                            switch (strtoupper($row['grading_risiko'])) {

                                case 'BIRU':
                                    $warna = 'primary';
                                    $icon  = 'bi-info-circle-fill';
                                    break;

                                case 'HIJAU':
                                    $warna = 'success';
                                    $icon  = 'bi-check-circle-fill';
                                    break;

                                case 'KUNING':
                                    $warna = 'warning';
                                    $icon  = 'bi-exclamation-triangle-fill';
                                    break;

                                case 'MERAH':
                                    $warna = 'danger';
                                    $icon  = 'bi-exclamation-octagon-fill';
                                    break;
                            }
                        }
                        ?>
                        <td class="mailbox-star text-muted">
                            <i class="bi <?= $icon ?> text-<?= $warna ?>"
                                title="<?= esc($row['grading_risiko']) ?>"
                                style="font-size:15px;"></i>
                        </td>

                        <!-- ICON -->
                        <td class="mailbox-star text-muted">
                            <?php if (($row['is_read'] ?? 1) == 0): ?>
                                <i class="bi bi-circle-fill text-primary me-2" style="font-size:8px;"></i>
                            <?php endif; ?>
                            <i class="bi bi-inbox text-primary"></i>
                        </td>

                        <!-- PASIEN -->
                        <td class="mailbox-name">
                            <div>
                                <?= esc($row['nama_pasien']) ?>
                            </div>
                            <small class="text-muted">
                                <?= esc($row['kd_pasien']) ?>
                            </small>
                        </td>

                        <!-- ISI -->
                        <td class="mailbox-subject">
                            <span><?= esc($row['jenis_insiden']) ?></span>
                            <span class="text-muted d-block text-truncate">
                                <?= esc(substr(strip_tags($row['insiden']), 0, 50)) ?>...
                            </span>
                        </td>

                        <!-- STATUS -->
                        <td class="text-center">
                            <span class="badge bg-primary">Inbox</span>
                        </td>

                        <!-- TANGGAL -->
                        <td class="mailbox-name">
                            <div class="text-truncate"
                                style="max-width:200px;"
                                title="<?= esc($row['nama_petugas']) ?>">
                                <?= esc($row['nama_petugas']) ?>
                            </div>

                            <small class="text-muted mailbox-date">
                                <?= date('d M Y H:i', strtotime($row['created_at'])) ?>
                            </small>
                        </td>


                    </tr>

                <?php endforeach; ?>

            </tbody>
        </table>
    </div>
</div>

<?php
$start = $total > 0 ? (($page - 1) * 10) + 1 : 0;
$end   = $total > 0 ? min($page * 10, $total) : 0;
?>

<div class="card-header mailbox-header d-flex align-items-center gap-2">

    <!-- INFO -->
    <span class="text-muted small">
        <?= $start ?> – <?= $end ?> / <?= $total ?>
    </span>

    <div class="ms-auto"></div>

    <span class="text-muted small">
        <?= $total > 0 ? $page : 0 ?> / <?= $total_pages ?>
    </span>

    <div class="btn-group btn-group-sm">
        <button class="btn btn-mailbox btn-inbox-prev"
            data-page="<?= $page - 1 ?>"
            <?= ($page <= 1 || $total == 0 ? 'disabled' : '') ?>>
            <i class="bi bi-chevron-left"></i>
        </button>

        <button class="btn btn-mailbox btn-inbox-next"
            data-page="<?= $page + 1 ?>"
            <?= ($page >= $total_pages || $total == 0 ? 'disabled' : '') ?>>
            <i class="bi bi-chevron-right"></i>
        </button>
    </div>

</div>

<script>
    /**
     * FILTER HANDLERS
     */
    function applyInboxFilters() {
        const triwulan = $('#filterTriwulan').val();
        const semester = $('#filterSemester').val();
        const tahun = $('#filterTahun').val();
        const keyword = $('#searchInbox').val() || '';

        const params = new URLSearchParams();
        if (triwulan) params.set('triwulan', triwulan);
        if (semester) params.set('semester', semester);
        if (tahun) params.set('tahun', tahun);
        if (keyword) params.set('keyword', keyword);

        const queryString = params.toString();
        const url = "<?= site_url('ikprs/form_inbox_karu') ?>" + (queryString ? '?' + queryString : '');

        $('#inbox-wrapper').trigger('processing.inbox', [true]);

        $.get(url, function(res) {
            $('#inbox-wrapper').html(res);
        }).always(function() {
            $('#inbox-wrapper').trigger('processing.inbox', [false]);
        });
    }

    $(document).on('click', '#btnApplyFilter', function() {
        applyInboxFilters();
    });

    $(document).on('click', '#btnClearFilter', function() {
        $('#filterTriwulan').val('');
        $('#filterSemester').val('');
        $('#filterTahun').val('');
        $('#btnClearFilter').hide();
        applyInboxFilters();
    });

    $(document).on('change', '#filterTriwulan, #filterSemester, #filterTahun', function() {
        const triwulan = $('#filterTriwulan').val();
        const semester = $('#filterSemester').val();
        const tahun = $('#filterTahun').val();

        if (triwulan || semester || tahun) {
            $('#btnClearFilter').show();
        } else {
            $('#btnClearFilter').hide();
        }
    });

    /**
     * TOGGLE CHECKBOX "PILIH SEMUA"
     * Sumber kebenaran: CHECKBOX, bukan IKON
     */
    $(document).on('click', '.checkbox-toggle', function() {

        const total = $('.mailbox-checkbox').length;
        const checked = $('.mailbox-checkbox:checked').length;

        const checkAll = checked !== total; // kalau belum semua → centang semua

        $('.mailbox-checkbox').prop('checked', checkAll);

        updateCheckboxIcon();
    });

    /**
     * SAAT CHECKBOX MANUAL DIKLIK
     */
    $(document).on('change', '.mailbox-checkbox', function() {
        updateCheckboxIcon();
    });

    /**
     * UPDATE IKON SESUAI KONDISI CHECKBOX
     */
    function updateCheckboxIcon() {

        const total = $('.mailbox-checkbox').length;
        const checked = $('.mailbox-checkbox:checked').length;
        const $icon = $('.checkbox-toggle i');

        // reset dulu
        $icon.removeClass('bi-square bi-check-square-fill bi-dash-square');

        if (checked === 0) {
            $icon.addClass('bi-square'); // kosong
        } else if (checked === total) {
            $icon.addClass('bi-check-square-fill'); // semua
        } else {
            $icon.addClass('bi-dash-square'); // sebagian (tri-state)
        }
    }

    /**
     * WAJIB: reset ikon setiap konten AJAX reload
     * panggil ini SETELAH loadDraft / reloadDraft
     */
    function resetMailboxSelection() {
        $('.mailbox-checkbox').prop('checked', false);
        updateCheckboxIcon();
    }
</script>


<style>
    /* DEFAULT */
    .btn-inbox-reload {
        background: var(--bs-secondary-bg);
        border: 1px solid var(--bs-border-color);
        color: var(--bs-body-color);
        transition: all 0.2s ease;
    }

    /* HOVER (TIDAK BIRU) */
    .btn-inbox-reload:hover {
        background: var(--bs-tertiary-bg);
        border-color: var(--bs-border-color);
        color: var(--bs-body-color);
    }

    /* ACTIVE (SAAT DIKLIK) */
    .btn-inbox-reload:active {
        background: var(--bs-secondary-bg);
        transform: scale(0.95);
    }

    /* DARK MODE */
    .dark-mode .btn-inbox-reload {
        background: #2b2b2b;
        border: 1px solid #444;
        color: #ddd;
    }

    /* HOVER DARK (TETAP GELAP) */
    .dark-mode .btn-inbox-reload:hover {
        background: #333;
        border-color: #555;
        color: #fff;
    }

    /* ACTIVE DARK */
    .dark-mode .btn-inbox-reload:active {
        background: #262626;
    }

    .btn-inbox-reload:active i {
        animation: spin 0.5s linear;
    }

    @keyframes spin {
        100% {
            transform: rotate(360deg);
        }
    }

    .inbox-row:not(.fw-bold) .mailbox-name {
        font-weight: normal !important;
    }

    .mailbox-table {
        table-layout: fixed;
        width: 100%;
    }

    .mailbox-name {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .mailbox-subject {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>