<div class="card-header mailbox-header d-flex align-items-start gap-2 flex-wrap">

    <!-- LEFT TOOLBAR -->
    <div class="d-flex gap-2">
        <button class="btn btn-mailbox btn-sm btn-draft-reload" title="Reload">
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
                id="searchDraft"
                placeholder="Cari draft..."
                value="<?= esc($keyword ?? '') ?>">

            <button class="btn btn-primary btn-search-draft" type="button">
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
                            Tidak ada laporan draft
                        </td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($list as $row): ?>
                    <tr class="draft-row"
                        data-id="<?= esc($row['id']) ?>"
                        style="cursor:pointer">

                        <!-- CHECKBOX -->
                        <td class="mailbox-check" onclick="event.stopPropagation();">
                            <input class="form-check-input mailbox-checkbox check-item"
                                type="checkbox"
                                value="<?= esc($row['id']) ?>">
                        </td>

                        <!-- ICON -->
                        <td class="mailbox-star text-muted">
                            <i class="bi bi-file-earmark-text me-2 text-warning"></i>
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
                            <span class="text-muted d-block text-truncate">
                                <?= esc(substr(strip_tags($row['insiden']), 0, 50)) ?>...
                            </span>
                        </td>

                        <!-- STATUS -->
                        <td class="text-center">
                            <span class="badge bg-warning">Draft</span>
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
        <button class="btn btn-mailbox btn-draft-prev"
            data-page="<?= $page - 1 ?>"
            <?= ($page <= 1 || $total == 0 ? 'disabled' : '') ?>>
            <i class="bi bi-chevron-left"></i>
        </button>

        <button class="btn btn-mailbox btn-draft-next"
            data-page="<?= $page + 1 ?>"
            <?= ($page >= $total_pages || $total == 0 ? 'disabled' : '') ?>>
            <i class="bi bi-chevron-right"></i>
        </button>
    </div>

</div>
<script>
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
     * FILTER HANDLERS
     */
    function applyDraftFilters() {
        const triwulan = $('#filterTriwulan').val();
        const semester = $('#filterSemester').val();
        const tahun = $('#filterTahun').val();
        const keyword = $('#searchDraft').val() || '';

        const params = new URLSearchParams();
        if (triwulan) params.set('triwulan', triwulan);
        if (semester) params.set('semester', semester);
        if (tahun) params.set('tahun', tahun);
        if (keyword) params.set('keyword', keyword);

        const queryString = params.toString();
        const url = "<?= site_url('ikprs/form_drafts') ?>" + (queryString ? '?' + queryString : '');

        $('#inbox-wrapper').trigger('processing.inbox', [true]);

        $.get(url, function(res) {
            $('#inbox-wrapper').html(res);
        }).always(function() {
            $('#inbox-wrapper').trigger('processing.inbox', [false]);
        });
    }

    $(document).on('click', '#btnApplyFilter', function() {
        applyDraftFilters();
    });

    $(document).on('click', '#btnClearFilter', function() {
        $('#filterTriwulan').val('');
        $('#filterSemester').val('');
        $('#filterTahun').val('');
        $('#btnClearFilter').hide();
        applyDraftFilters();
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