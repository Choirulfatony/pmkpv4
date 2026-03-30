<div class="card-header mailbox-header d-flex align-items-start gap-2 flex-wrap">

    <!-- LEFT TOOLBAR -->
    <div class="d-flex gap-2">
        <button class="btn btn-mailbox btn-sm btn-send-reload" title="Reload">
            <i class="bi bi-arrow-repeat"></i>
        </button>
    </div>

    <!-- FILTERS -->
    <div class="d-flex gap-2 align-items-center">
        <select class="form-select form-select-sm" id="filterStatus" style="width: 130px;">
            <option value="">Status</option>
            <option value="DRAFT" <?= ($filters['status'] ?? '') == 'DRAFT' ? 'selected' : '' ?>>Draft</option>
            <option value="KARU" <?= ($filters['status'] ?? '') == 'KARU' ? 'selected' : '' ?>>KARU</option>
            <option value="INSTALASI" <?= ($filters['status'] ?? '') == 'INSTALASI' ? 'selected' : '' ?>>Instalasi</option>
            <option value="SELESAI" <?= ($filters['status'] ?? '') == 'SELESAI' ? 'selected' : '' ?>>Selesai</option>
        </select>

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

        <button class="btn btn-sm btn-outline-danger" id="btnClearFilter" title="Hapus Filter" <?= empty($filters['tahun']) && empty($filters['semester']) && empty($filters['triwulan']) && empty($filters['status']) ? 'style="display:none"' : '' ?>>
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
                id="searchSend"
                placeholder="Cari send..."
                value="<?= esc($keyword ?? '') ?>">

            <button class="btn btn-primary btn-search-send" type="button">
                <i class="bi bi-search"></i>
            </button>
        </div>

    </div>
</div>

<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table table-hover mailbox-table mb-0">
            <tbody>

                <?php if (empty($list)): ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted p-4">
                            Tidak ada laporan yang terkirim.
                        </td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($list as $row): ?>
                    <tr class="send-row"
                        data-id="<?= esc($row['id']) ?>"
                        style="cursor:pointer">



                        <?php
                        $warna = '';
                        $icon  = '';

                        $grading = $row['grading_risiko'] ?? '';

                        if ($grading) {

                            switch (strtoupper($grading)) {

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
                            <i class="bi bi-send me-2 text-success"></i>
                        </td>

                        <!-- PASIEN -->
                        <td class="mailbox-name">
                            <div class="fw-semibold">
                                <?= esc($row['nama_pasien']) ?>
                            </div>
                            <small class="text-muted">
                                <?= esc($row['kd_pasien']) ?>
                            </small>
                        </td>

                        <!-- ISI -->
                        <td class="mailbox-subject">
                            <strong><?= esc($row['jenis_insiden']) ?></strong>
                            <span class="text-muted d-block text-truncate" style="max-width: 500px;">
                                <?= esc(substr(strip_tags($row['kronologis_insiden']), 0, 50)) ?>...
                            </span>
                        </td>

                        <!-- STATUS -->
                        <td class="text-center">
                            <span class="badge bg-success">Send</span>
                        </td>

                        <!-- TANGGAL -->
                        <td class="mailbox-date text-nowrap">
                            <?= date('d M Y H:i', strtotime($row['created_at'])) ?>
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
        <button class="btn btn-mailbox btn-send-prev"
            data-page="<?= $page - 1 ?>"
            <?= ($page <= 1 || $total == 0 ? 'disabled' : '') ?>>
            <i class="bi bi-chevron-left"></i>
        </button>

        <button class="btn btn-mailbox btn-send-next"
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
    function applySendFilters() {
        const triwulan = $('#filterTriwulan').val();
        const semester = $('#filterSemester').val();
        const tahun = $('#filterTahun').val();
        const status = $('#filterStatus').val();
        const keyword = $('#searchSend').val() || '';

        const params = new URLSearchParams();
        if (triwulan) params.set('triwulan', triwulan);
        if (semester) params.set('semester', semester);
        if (tahun) params.set('tahun', tahun);
        if (status) params.set('status', status);
        if (keyword) params.set('keyword', keyword);

        const queryString = params.toString();
        const url = "<?= site_url('ikprs/form_send') ?>" + (queryString ? '?' + queryString : '');

        $('#inbox-wrapper').trigger('processing.inbox', [true]);

        $.get(url, function(res) {
            $('#inbox-wrapper').html(res);
        }).always(function() {
            $('#inbox-wrapper').trigger('processing.inbox', [false]);
        });
    }

    $(document).on('click', '#btnApplyFilter', function() {
        applySendFilters();
    });

    $(document).on('click', '#btnClearFilter', function() {
        $('#filterTriwulan').val('');
        $('#filterSemester').val('');
        $('#filterTahun').val('');
        $('#filterStatus').val('');
        $('#btnClearFilter').hide();
        applySendFilters();
    });

    $(document).on('change', '#filterTriwulan, #filterSemester, #filterTahun, #filterStatus', function() {
        const triwulan = $('#filterTriwulan').val();
        const semester = $('#filterSemester').val();
        const tahun = $('#filterTahun').val();
        const status = $('#filterStatus').val();

        if (triwulan || semester || tahun || status) {
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
     * panggil ini SETELAH loadSend / reloadSend
     */
    function resetMailboxSelection() {
        $('.mailbox-checkbox').prop('checked', false);
        updateCheckboxIcon();
    }
</script>

<style>
    /* ===============================
    GLOBAL BUTTON (SEMUA MODE)
    ================================ */

    .btn-mailbox {
        background: var(--bs-secondary-bg);
        border: 1px solid var(--bs-border-color);
        color: var(--bs-body-color);
        transition: all 0.2s ease;
    }

    /* HOVER */
    .btn-mailbox:hover {
        background: var(--bs-tertiary-bg);
        border-color: var(--bs-border-color);
        color: var(--bs-body-color);
    }

    /* ACTIVE */
    .btn-mailbox:active {
        background: var(--bs-secondary-bg);
        transform: scale(0.95);
    }

    /* ===============================
    DARK MODE
    ================================ */

    .dark-mode .btn-mailbox {
        background: #2b2b2b;
        border: 1px solid #444;
        color: #ddd;
    }

    .dark-mode .btn-mailbox:hover {
        background: #333;
        border-color: #555;
        color: #fff;
    }

    .dark-mode .btn-mailbox:active {
        background: #262626;
    }


    .mailbox-name .fw-semibold {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>