<style>
    .cell-target {
        background-color: rgba(41, 185, 92) !important;
        font-weight: bold;
    }

    .cell-empty {
        background-color: rgba(255, 222, 60) !important;
        font-weight: bold;
    }

    .cell-fail {
        background-color: rgba(220, 57, 57) !important;
        color: #fff !important;
        font-weight: bold;
    }

    .cell-clickable {
        cursor: pointer !important;
        position: relative;
    }

    .cell-clickable:hover::after {
        content: "\f133";
        font-family: "Font Awesome 6 Free", "Font Awesome 5 Free", "Font Awesome 6 Pro";
        font-weight: 900;
        position: absolute;
        top: 2px;
        right: 4px;
        font-size: 10px;
        color: rgba(0, 0, 0, 0.3);
        opacity: 0.6;
    }

    .legend-dot {
        width: 12px;
        height: 12px;
        border-radius: 3px;
        display: inline-block;
    }

    #ajax_detail td,
    #ajax_detail th {
        font-size: 13px;
        vertical-align: middle;
        white-space: nowrap;
        padding: 10px 8px !important;
    }

    #ajax_detail th {
        background-color: #28a745 !important;
        color: #fff;
        text-align: center;
        font-weight: 600;
    }

    .table-responsive {
        position: relative;
    }

    .overlay-wrapper {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: transparent;
    }

    .overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: transparent;
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }

    .loader {
        width: 3em;
        height: 3em;
        transform: rotate(165deg);
    }

    .loader:before,
    .loader:after {
        content: "";
        position: absolute;
        top: 50%;
        left: 50%;
        display: block;
        width: 1em;
        height: 1em;
        border-radius: 0.5em;
        transform: translate(-50%, -50%);
    }

    .loader:before {
        animation: before8 2s infinite;
    }

    .loader:after {
        animation: after6 2s infinite;
    }

    @keyframes before8 {
        0% {
            width: 1em;
            box-shadow: 2em -1em rgba(225, 20, 98, 0.75), -2em 1em rgba(111, 202, 220, 0.75);
        }

        35% {
            width: 4em;
            box-shadow: 0 -1em rgba(225, 20, 98, 0.75), 0 1em rgba(111, 202, 220, 0.75);
        }

        70% {
            width: 1em;
            box-shadow: -2em -1em rgba(225, 20, 98, 0.75), 2em 1em rgba(111, 202, 220, 0.75);
        }

        100% {
            box-shadow: 2em -1em rgba(225, 20, 98, 0.75), -2em 1em rgba(111, 202, 220, 0.75);
        }
    }

    @keyframes after6 {
        0% {
            height: 1em;
            box-shadow: 1em 2em rgba(61, 184, 143, 0.75), -1em -2em rgba(233, 169, 32, 0.75);
        }

        35% {
            height: 4em;
            box-shadow: 1em 0 rgba(61, 184, 143, 0.75), -1em 0 rgba(233, 169, 32, 0.75);
        }

        70% {
            height: 1em;
            box-shadow: 1em -2em rgba(61, 184, 143, 0.75), -1em 2em rgba(233, 169, 32, 0.75);
        }

        100% {
            box-shadow: 1em 2em rgba(61, 184, 143, 0.75), -1em -2em rgba(233, 169, 32, 0.75);
        }
    }

    #daily-table td,
    #daily-table th {
        font-size: 13px;
        vertical-align: middle;
        text-align: center;
        padding: 8px 6px !important;
    }

    .daily-tercapai {
        background-color: rgba(41, 185, 92) !important;
        font-weight: bold;
    }

    .daily-tidak-tercapai {
        background-color: rgba(220, 57, 57) !important;
        color: #fff !important;
        font-weight: bold;
    }

    .daily-tanpa-data {
        background-color: rgba(255, 222, 60) !important;
        font-weight: bold;
    }
</style>

<!-- ==================== HEADER INFO ==================== -->
<div class="row mb-3">
    <div class="col-12">
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <div class="d-flex align-items-start">
                <div class="me-3">
                    <i class="fas fa-info-circle fa-2x text-success"></i>
                </div>
                <div class="flex-grow-1">
                    <h5 class="mb-1"><strong>Detail Rekap Indikator Nasional Mutu (INM)</strong></h5>
                    <p class="mb-0">Indikator: <strong><?= isset($detail->indicator_element) ? esc($detail->indicator_element) : 'Data Detail' ?></strong></p>
                    <p class="mb-0">Target: <strong><?= isset($detail->indicator_target) ? esc($detail->indicator_target) : '-' ?></strong>
                        <span class="text-muted"><?= isset($detail->indicator_units) ? esc($detail->indicator_units) : '' ?></span>
                    </p>
                    <input type="hidden" id="target_det" value="<?= isset($detail->indicator_target) ? esc($detail->indicator_target) : '' ?>">
                    <input type="hidden" id="factor_det" value="<?= isset($detail->indicator_factors) ? esc($detail->indicator_factors) : '' ?>">
                    <input type="hidden" id="operator_det" value="<?= isset($detail->indicator_target_calculation) ? esc($detail->indicator_target_calculation) : '>=' ?>">
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
</div>

<!-- ==================== CARD DETAIL ==================== -->
<div class="row" id="detail-row">
    <div class="col-12" id="table-col">
        <div class="card card-outline card-success">
            <!-- HEADER -->
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-table me-2"></i>
                    Detail Per Ruangan
                </h3>
                <div class="card-tools d-flex align-items-center gap-2">
                    <!-- Tombol Back -->
                    <a href="<?= site_url('siimut/rekap-laporan-inm?tahun=' . $tahun) ?>" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Kembali
                    </a>
                    <!-- Tahun -->
                    <div class="input-group input-group-sm" style="width: 130px;">
                        <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                        <select class="form-select form-select-sm" id="tahun" onchange="gantiTahun()">
                            <?php for ($y = date('Y'); $y >= date('Y') - 5; $y--): ?>
                                <option value="<?= $y ?>" <?= $y == $tahun ? 'selected' : '' ?>><?= $y ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <!-- Tombol Aksi -->
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-secondary" onclick="reload_table()" title="Refresh">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                        <a href="#" id="btn-export" class="btn btn-outline-success" title="Download Excel">
                            <i class="fas fa-file-excel"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- BODY -->
            <div class="card-body p-0">
                <!-- Tabel -->
                <div class="table-responsive p-3">
                    <div class="overlay-wrapper" id="loading_overlay_detail" style="display: none;">
                        <div class="overlay">
                            <i class="loader"></i>
                        </div>
                    </div>
                    <table id="ajax_detail" class="table table-bordered table-hover table-striped mb-0" style="width: 100%; table-layout: fixed;">
                        <thead>
                            <tr class="align-middle">
                                <th style="width: 50px;" class="text-center">#</th>
                                <th style="width: 200px; text-align: left !important; padding-left: 15px !important;">Ruangan</th>
                                <th class="text-center">Target</th>
                                <th class="text-center">Jan</th>
                                <th class="text-center">Feb</th>
                                <th class="text-center">Mar</th>
                                <th class="text-center">Apr</th>
                                <th class="text-center">Mei</th>
                                <th class="text-center">Jun</th>
                                <th class="text-center">Jul</th>
                                <th class="text-center">Ags</th>
                                <th class="text-center">Sep</th>
                                <th class="text-center">Okt</th>
                                <th class="text-center">Nov</th>
                                <th class="text-center">Des</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>

            <!-- FOOTER -->
            <div class="card-footer">
                <div class="d-flex flex-wrap gap-3 align-items-center">
                    <div class="d-flex align-items-center">
                        <span class="legend-dot me-2" style="background-color: rgba(41, 185, 92);"></span>
                        <small>Mencapai target</small>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="legend-dot me-2" style="background-color: rgba(255, 222, 60);"></span>
                        <small>Belum terisi</small>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="legend-dot me-2" style="background-color: rgba(220, 57, 57);"></span>
                        <small>Tidak tercapai</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ==================== DAILY DETAIL SECTION ==================== -->
</div>

<!-- ==================== DAILY DETAIL SECTION ==================== -->
<div class="row mt-3" id="daily-section" style="display:none;">
    <div class="col-12">
        <div class="card card-outline card-info">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-calendar-day me-2"></i>
                    <span id="daily-title">Detail Harian</span>
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="closeDaily()" title="Tutup">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="px-3 pt-3 pb-1">
                    <h6 id="daily-info" class="mb-1"></h6>
                    <small class="text-muted" id="daily-target-info"></small>
                </div>
                <div class="p-3">
                    <div id="daily-loading" class="text-center py-4" style="display:none;">
                        <div class="spinner-border text-info" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted">Memuat data harian...</p>
                    </div>
                    <div id="daily-empty" class="text-center py-4" style="display:none;">
                        <i class="fas fa-info-circle fa-2x text-muted mb-2"></i>
                        <p class="text-muted">Belum ada data untuk bulan ini</p>
                    </div>
                    <table id="daily-table" class="table table-bordered table-hover table-sm mb-0" style="display:none; table-layout:fixed;">
                        <thead>
                            <tr id="daily-headers">
                                <th style="width: 50px;" class="text-center">#</th>
                                <th style="width: 200px; text-align: left !important; padding-left: 15px !important;">Ruangan</th>
                                <th class="text-center" style="width:90px;">Nilai</th>
                                <th class="text-center" style="width:90px;">Num/Denum</th>
                                <th>Kendala</th>
                                <th>Perbaikan</th>
                            </tr>
                        </thead>
                        <tbody id="daily-body"></tbody>
                    </table>

                </div>
            </div>
            <div class="card-footer">
                <div class="d-flex flex-wrap gap-3 align-items-center">
                    <div class="d-flex align-items-center">
                        <span class="legend-dot me-2" style="background-color: rgba(41, 185, 92);"></span>
                        <small>Mencapai target</small>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="legend-dot me-2" style="background-color: rgba(255, 222, 60);"></span>
                        <small>Belum terisi</small>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="legend-dot me-2" style="background-color: rgba(220, 57, 57);"></span>
                        <small>Tidak tercapai</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ==================== SCRIPT ==================== -->
<script>
    var table_detail;
    var urlParams = new URLSearchParams(window.location.search);
    var vtahun = urlParams.get('tahun') || <?= json_encode($tahun) ?>;
    var indicatorId = '<?= $indicatorId ?>';
    var target = '<?= isset($detail->indicator_target) ? $detail->indicator_target : 0 ?>';
    var factor = '<?= isset($detail->indicator_factors) ? $detail->indicator_factors : 1 ?>';
    var operator = '<?= isset($detail->indicator_target_calculation) ? $detail->indicator_target_calculation : '>=' ?>';

    // Sync dropdown with URL parameter on load
    $(document).ready(function() {
        if (urlParams.get('tahun')) {
            $('#tahun').val(urlParams.get('tahun'));
            vtahun = urlParams.get('tahun');
        }

        // Init DataTable
        table_detail = $('#ajax_detail').DataTable({
            processing: false,
            serverSide: true,
            autoWidth: false,
            pageLength: 25,
            lengthMenu: [
                [10, 25, 50, -1],
                [10, 25, 50, "Semua"]
            ],
            ajax: {
                url: '<?= site_url('siimut/rekap-laporan-inm/ajax-detail-inm') ?>',
                type: 'POST',
                data: function(d) {
                    d.vtahun = vtahun;
                    d.indicator_id = indicatorId;
                    return d;
                },
                beforeSend: function() {
                    $('#loading_overlay_detail').show();
                },
                complete: function() {
                    $('#loading_overlay_detail').hide();
                }
            },
            columnDefs: [{
                targets: [0, 2],
                orderable: false,
                className: 'text-center'
            }, {
                targets: [-1, -2, -3, -4, -5, -6, -7, -8, -9, -10, -11, -12, -13, -14],
                orderable: false,
                className: 'text-center',
                createdCell: function(td, cellData, rowData, row, col) {
                    // Kolom Target
                    if (col == 2) {
                        try {
                            let parser = new DOMParser();
                            const doc = parser.parseFromString(cellData, 'text/html');
                            var targetEl = doc.getElementById('target_det');
                            var factorEl = doc.getElementById('factor_det');
                            var operatorEl = doc.getElementById('operator_det');
                            if (targetEl) target = targetEl.innerText;
                            if (factorEl) factor = factorEl.innerText;
                            if (operatorEl) operator = operatorEl.innerText;
                            $(td).addClass('cell-target');
                        } catch (e) {}
                    }
                    // Kolom Bulan
                    if (col > 2) {
                        try {
                            let parser = new DOMParser();
                            const doc = parser.parseFromString(cellData, 'text/html');
                            var numEl = doc.getElementById('num_det');
                            var denumEl = doc.getElementById('denum_det');

                            if (numEl && denumEl) {
                                var num = parseInt(numEl.innerText) || 0;
                                var denum = parseInt(denumEl.innerText) || 0;

                                if (num == 0 && denum == 0) {
                                    $(td).addClass('cell-empty');
                                } else {
                                    var totalEl = doc.getElementById('total_det');
                                    var nilai = totalEl ? parseFloat(totalEl.innerText) || 0 : 0;
                                    var tgt = parseInt(target) || 0;

                                    if (operator == "<=") {
                                        if (nilai <= tgt) {
                                            $(td).addClass('cell-target');
                                        } else {
                                            $(td).addClass('cell-fail');
                                        }
                                    } else {
                                        if (nilai >= tgt) {
                                            $(td).addClass('cell-target');
                                        } else {
                                            $(td).addClass('cell-fail');
                                        }
                                    }
                                }
                            }
                            // Buat cell bisa diklik untuk daily detail
                            $(td).addClass('cell-clickable');
                            $(td).attr('title', 'Klik untuk lihat detail harian');
                        } catch (e) {}
                    }
                }
            }],
            language: {
                emptyTable: 'Tidak ada data',
                info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
                lengthMenu: 'Tampilkan _MENU_ data',
                search: 'Cari:',
                paginate: {
                    first: 'Pertama',
                    last: 'Terakhir',
                    next: 'Berikutnya',
                    previous: 'Sebelumnya'
                }
            }
        });

        // Click handler untuk cell bulan -> tampilkan daily detail di samping
        $('#ajax_detail tbody').on('click', 'td.cell-clickable', function() {
            var $cell = $(this);
            var col = $cell.index(); // 3=Jan, 4=Feb, ... 14=Des
            var bulan = col - 2; // 1=Jan, 2=Feb, ... 12=Des

            var bulanNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            var bulanLabel = bulanNames[bulan - 1];

            // Tampilkan daily di bawah tabel
            $('#daily-title').text('Detail Harian - ' + bulanLabel + ' ' + vtahun);
            $('#daily-info').text('Memuat data...');
            $('#daily-target-info').html('');
            $('#daily-table').hide();
            $('#daily-body').empty();
            $('#daily-empty').hide();
            $('#daily-loading').show();
            $('#daily-section').show();

            // AJAX fetch daily data (semua departemen)
            $.ajax({
                url: '<?= site_url('siimut/rekap-laporan-inm/ajax-daily-detail') ?>',
                type: 'POST',
                data: {
                    indicator_id: indicatorId,
                    tahun: vtahun,
                    bulan: bulan
                },
                dataType: 'json',
                success: function(resp) {
                    $('#daily-loading').hide();

                    if (!resp || !resp.dept_data || resp.dept_data.length === 0) {
                        $('#daily-empty').show();
                        return;
                    }

                    var targetText = resp.target || 0;
                    var operatorText = resp.operator || '>=';
                    var unitsText = resp.units || '%';
                    var operatorDisplay = operatorText;
                    if (operatorDisplay === '>=') operatorDisplay = '≥';
                    if (operatorDisplay === '<=') operatorDisplay = '≤';

                    $('#daily-info').text(resp.indicator || '');
                    $('#daily-target-info').html(
                        'Target: ' + operatorDisplay + ' ' + targetText + ' ' + unitsText +
                        ' | Ruangan: ' + resp.dept_data.length + ' departemen'
                    );

                    var days = resp.days || 31;

                    // Bangun header: #, Ruangan, 1, 2, 3, ... , days
                    var headerHtml = '<th style="width:50px;" class="text-center">#</th><th style="width:200px;text-align:left!important;padding-left:15px!important;">Ruangan</th>';
                    for (var d = 1; d <= days; d++) {
                        headerHtml += '<th class="text-center" style="width:60px;">' + d + '</th>';
                    }

                    // Bangun baris per departemen + row detail kendala/perbaikan
                    var bodyHtml = '';
                    $.each(resp.dept_data, function(idx, dept) {
                        bodyHtml += '<tr class="daily-dept-row" data-dept-idx="' + idx + '">';
                        bodyHtml += '<td class="text-center fw-bold" style="width:50px;">' + (idx + 1) + '</td>';
                        bodyHtml += '<td class="text-center"><div class="py-1 text-start ps-2 text-nowrap" style="overflow:hidden;text-overflow:ellipsis;">' + dept.department_name + '</div></td>';

                        $.each(dept.daily, function(i, item) {
                            var cellClass = 'text-center text-nowrap';
                            var nilaiDisplay = '-';

                            if (item.nilai !== null) {
                                nilaiDisplay = item.nilai + ' ' + unitsText;
                                if (item.tercapai === true) {
                                    cellClass += ' cell-target';
                                } else if (item.tercapai === false) {
                                    cellClass += ' cell-fail';
                                }
                            } else {
                                if (item.num > 0 || item.denum > 0) {
                                    cellClass += ' cell-fail';
                                } else {
                                    cellClass += ' cell-empty';
                                }
                            }

                            var kpIcon = '';
                            if (item.kendala || item.perbaikan) {
                                kpIcon = '<i class="fas fa-exclamation-circle text-warning ms-1 kp-icon" style="font-size:10px;cursor:pointer;" data-dept-idx="' + idx + '" data-hari="' + item.hari + '"></i>';
                            }

                            bodyHtml += '<td class="' + cellClass + '" style="width:60px;" data-hari="' + item.hari + '">' +
                                '<div class="py-1"><span class="fw-bold" style="font-size:13px;">' + nilaiDisplay + kpIcon + '</span>' +
                                '<div class="small text-muted mt-1">' +
                                '<span>' + (item.num || 0) + '</span> | <span>' + (item.denum || 0) + '</span>' +
                                '</div></div>' +
                                '</td>';
                        });

                        bodyHtml += '</tr>';

                    });

                    $('#daily-body').html(bodyHtml);
                    $('#daily-headers').html(headerHtml);

                    // Hancurkan DataTable lama jika ada
                    if ($.fn.DataTable.isDataTable('#daily-table')) {
                        $('#daily-table').DataTable().destroy();
                    }

                    $('#daily-table').show();
                    if (!$('#daily-table').parent().is('.daily-table-scroll')) {
                        $('#daily-table').wrap('<div class="daily-table-scroll" style="overflow-x:auto;max-width:100%;"></div>');
                    }

                    // Inisialisasi DataTable untuk daily table
                    var dailyTable = $('#daily-table').DataTable({
                        autoWidth: false,
                        pageLength: 25,
                        lengthMenu: [
                            [10, 25, 50, -1],
                            [10, 25, 50, 'Semua']
                        ],
                        language: {
                            emptyTable: 'Tidak ada data',
                            info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
                            lengthMenu: 'Tampilkan _MENU_ data',
                            search: 'Cari:',
                            paginate: {
                                first: 'Pertama',
                                last: 'Terakhir',
                                next: 'Berikutnya',
                                previous: 'Sebelumnya'
                            }
                        },
                        columnDefs: [{
                            orderable: false,
                            targets: '_all'
                        }],
                        destroy: true
                    });

                    // Click handler: icon warning -> toggle child row kendala/perbaikan
                    $('#daily-body').off('click', '.kp-icon').on('click', '.kp-icon', function() {
                        var deptIdx = $(this).data('dept-idx');
                        var hari = $(this).data('hari');
                        var deptData = resp.dept_data[deptIdx];
                        if (!deptData) return;

                        // Cari data hari itu
                        var dayData = null;
                        $.each(deptData.daily, function(i, d) {
                            if (d.hari === hari) dayData = d;
                        });
                        if (!dayData) return;

                        // Cari baris DataTable berdasarkan dept-idx
                        var tr = $(this).closest('tr');
                        var row = dailyTable.row(tr);
                        var activeKey = deptIdx + '-' + hari;

                        // Jika child row sudah terbuka untuk data yg sama -> tutup
                        if (row.child.isShown() && tr.data('active-key') === activeKey) {
                            row.child.hide();
                            tr.removeData('active-key');
                            tr.toggleClass('kp-row-open');
                            return;
                        }

                        // Tutup semua child row lain
                        dailyTable.rows().every(function() {
                            if (this.child.isShown()) {
                                this.child.hide();
                                $(this.node()).removeData('active-key');
                                $(this.node()).removeClass('kp-row-open');
                            }
                        });

                        var html = '<div class="kp-accordion-body p-3 bg-light" style="border-top:2px solid #ffc107;">';
                        html += '<div class="d-flex align-items-start gap-3 flex-wrap">';
                        html += '<div><strong>Ruangan:</strong> ' + $('<span>').text(deptData.department_name).html() + '</div>';
                        html += '<div class="badge bg-warning text-dark fs-6">Hari ke-' + hari + '</div>';
                        html += '</div>';
                        html += '<hr class="my-2">';
                        html += '<div class="d-flex align-items-start gap-3 flex-wrap">';
                        html += '<div><strong>Kendala:</strong> ' + $('<span>').text(dayData.kendala || '-').html() + '</div>';
                        html += '<div><strong>Perbaikan:</strong> ' + $('<span>').text(dayData.perbaikan || '-').html() + '</div>';
                        html += '</div></div>';

                        row.child(html).show();
                        tr.data('active-key', activeKey).addClass('kp-row-open');
                    });
                },
                error: function() {
                    $('#daily-loading').hide();
                    $('#daily-empty').show();
                    $('#daily-empty').html('<i class="fas fa-exclamation-triangle fa-2x text-danger mb-2"></i><p class="text-danger">Gagal memuat data</p>');
                    toastr.error('Gagal memuat data harian');
                }
            });
        });
    });

    function closeDaily() {
        $('#daily-section').slideUp(300);
    }

    function gantiTahun() {
        vtahun = $('#tahun').val();
        closeDaily();
        if (table_detail) {
            table_detail.ajax.reload();
        }
    }

    function reload_table() {
        closeDaily();
        if (table_detail) {
            table_detail.ajax.reload();
        }
    }

    $(document).on('click', '#btn-export', function(e) {
        e.preventDefault();
        var exportUrl = '<?= site_url('siimut/rekap-laporan-inm/export-indicator/' . $indicatorId) ?>?tahun=' + vtahun;
        window.location.href = exportUrl;
    });
</script>