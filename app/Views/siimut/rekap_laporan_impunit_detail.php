
<!-- ==================== HEADER INFO ==================== -->
<div class="row mb-3">
    <div class="col-12">
        <div class="alert alert-light border alert-dismissible fade show" role="alert">
            <div class="d-flex align-items-start">
                <div class="me-3">
                    <i class="fas fa-info-circle fa-2x text-secondary"></i>
                </div>
                <div class="flex-grow-1">
                    <h5 class="mb-1"><strong>Detail Rekap Indikator Mutu Prioritas Unit (IMPUnit)</strong></h5>
                    <p class="mb-0">Indikator: <strong><?= isset($detail->indicator_element) ? esc($detail->indicator_element) : 'Data Detail' ?><?php if (isset($detail->indicator_record_status) && $detail->indicator_record_status === 'D'): ?> <span class="badge bg-secondary ms-1" style="font-size:10px;vertical-align:middle;">Non-Aktif</span><?php endif; ?></strong></p>
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
<div class="row">
    <div class="col-12">
        <div class="card card-outline card-secondary" style="border-top: 3px solid #363636;">

            <!-- HEADER -->
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-table me-2"></i>
                    Detail Per Ruangan
                    <?php if (isset($detail->indicator_element)): ?>
                        <small class="ms-2">- <?= esc($detail->indicator_element) ?></small>
                        <?php if (isset($detail->indicator_record_status) && $detail->indicator_record_status === 'D'): ?>
                            <span class="badge bg-secondary ms-1" style="font-size:10px;vertical-align:middle;">Non-Aktif</span>
                        <?php endif; ?>
                    <?php endif; ?>
                </h3>
                <div class="card-tools d-flex align-items-center gap-2">
                    <!-- Tombol Back -->
                    <a href="<?= site_url('siimut/rekap-laporan-impunit?tahun=' . $tahun) ?>" class="btn btn-sm btn-outline-secondary">
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
                        <button type="button" class="btn btn-outline-secondary" onclick="reload_table_impunit()" title="Refresh">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                        <a href="#" id="btn-export" class="btn btn-outline-secondary" title="Download Excel">
                            <i class="fas fa-file-excel"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- BODY -->
            <div class="card-body p-0">
                <!-- Tabel -->
                <div class="table-responsive p-3">
                    <div class="overlay-wrapper" id="loading_overlay_detail_impunit" style="display: none;">
                        <div class="overlay">
                            <i class="loader"></i>
                        </div>
                    </div>
                    <table id="ajax_detail_impunit" class="table table-bordered table-hover table-striped mb-0" style="width: 100%;">
                        <thead>
                            <tr class="align-middle">
                                <th style="min-width: 40px;" class="text-center">#</th>
                                <th style="min-width: 150px; text-align: left !important; padding-left: 15px !important;">Ruangan</th>
                                <th class="text-center" style="min-width: 80px;">Target</th>
                                <th class="text-center" style="min-width: 75px;">Jan</th>
                                <th class="text-center" style="min-width: 75px;">Feb</th>
                                <th class="text-center" style="min-width: 75px;">Mar</th>
                                <th class="text-center" style="min-width: 75px;">Apr</th>
                                <th class="text-center" style="min-width: 75px;">Mei</th>
                                <th class="text-center" style="min-width: 75px;">Jun</th>
                                <th class="text-center" style="min-width: 75px;">Jul</th>
                                <th class="text-center" style="min-width: 75px;">Ags</th>
                                <th class="text-center" style="min-width: 75px;">Sep</th>
                                <th class="text-center" style="min-width: 75px;">Okt</th>
                                <th class="text-center" style="min-width: 75px;">Nov</th>
                                <th class="text-center" style="min-width: 75px;">Des</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                    <div id="no_data_message" class="alert alert-info mt-3 mb-0" style="display: none;">
                        <i class="bi bi-info-circle"></i> <strong>Informasi:</strong> Indikator ini tidak memiliki ruangan terkait. Silakan hubungi administrator untuk menambahkan ruangan.
                    </div>
                </div>
            </div>

            <!-- FOOTER -->
            <div class="card-footer">
                <div class="d-flex flex-wrap gap-3 align-items-center">
                    <div class="d-flex align-items-center">
                        <span class="legend-dot me-2" style="background-color: rgba(41, 185, 92);"></span>
                        <small class="text-muted">Mencapai target</small>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="legend-dot me-2" style="background-color: rgba(255, 222, 60);"></span>
                        <small class="text-muted">Belum terisi</small>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="legend-dot me-2" style="background-color: rgba(220, 57, 57);"></span>
                        <small class="text-muted">Tidak tercapai</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
                <div class="p-3" style="position:relative; min-height:120px;">
                    <div class="overlay-wrapper" id="daily-loading" style="display:none;">
                        <div class="overlay">
                            <i class="loader"></i>
                        </div>
                    </div>
                    <div id="daily-empty" class="text-center py-4" style="display:none;">
                        <i class="fas fa-info-circle fa-2x text-muted mb-2"></i>
                        <p class="text-muted">Belum ada data untuk bulan ini</p>
                    </div>
                    <table id="daily-table" class="table table-inm-inm table-sm mb-0" style="display:none;">
                        <thead>
                            <tr id="daily-headers"></tr>
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
        table_detail = $('#ajax_detail_impunit').DataTable({
            processing: false,
            serverSide: true,
            autoWidth: false,
            pageLength: 25,
            lengthMenu: [
                [10, 25, 50, -1],
                [10, 25, 50, "Semua"]
            ],
            ajax: {
                url: '<?= site_url('siimut/rekap-laporan-impunit/ajax-detail-impunit') ?>',
                type: 'POST',
                data: function(d) {
                    d.vtahun = vtahun;
                    d.indicator_id = indicatorId;
                    return d;
                },
                beforeSend: function() {
                    $('#loading_overlay_detail_impunit').show();
                },
                complete: function() {
                    $('#loading_overlay_detail_impunit').hide();
                },
                error: function(xhr, error, thrown) {
                    $('#loading_overlay_detail_impunit').hide();
                    console.error('DataTables error:', error, thrown);
                    alert('Gagal memuat data detail. Silakan refresh halaman.');
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
            },
            initComplete: function(settings, json) {
                if (json && json.recordsTotal === 0) {
                    $('#no_data_message').show();
                    $('#ajax_detail_impunit').hide();
                }
            }
        });
        
        // Handle DataTables draw event
        table_detail.on('draw.dt', function() {
            var info = table_detail.page.info();
            if (info.recordsTotal === 0) {
                $('#no_data_message').show();
                $('#ajax_detail_impunit').hide();
            } else {
                $('#no_data_message').hide();
                $('#ajax_detail_impunit').show();
            }
        });

        // Cegah race condition: request counter
        var dailyRequestId = 0;

        // Click handler untuk cell bulan -> tampilkan daily detail per departemen
        $('#ajax_detail_impunit tbody').on('click', 'td.cell-clickable', function() {
            var $cell = $(this);
            var col = $cell.index(); // 3=Jan, 4=Feb, ... 14=Des
            var bulan = col - 2; // 1=Jan, 2=Feb, ... 12=Des

            // Ambil department dari HTML cell itu sendiri (data-dept-id dari server)
            var $cellHtml = $('<div>').html($cell.html());
            var deptId = $cellHtml.find('[data-dept-id]').attr('data-dept-id') || 0;
            var deptName = $cellHtml.find('[data-dept-name]').attr('data-dept-name') || '';

            var bulanNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            var bulanLabel = bulanNames[bulan - 1];

            // Tandai request terbaru
            var myReqId = ++dailyRequestId;

            // Tampilkan daily di bawah tabel
            $('#daily-title').text('Detail Harian - ' + deptName + ' - ' + bulanLabel + ' ' + vtahun);
            $('#daily-info').text('Memuat data...');
            $('#daily-target-info').html('');
            $('#daily-table').hide();
            $('#daily-body').empty();
            $('#daily-empty').hide();
            $('#daily-loading').show();
            $('#daily-section').show();

            // AJAX fetch daily data (per departemen)
            $.ajax({
                url: '<?= site_url('siimut/rekap-laporan-impunit/ajax-daily-detail-impunit') ?>',
                type: 'POST',
                data: {
                    indicator_id: indicatorId,
                    tahun: vtahun,
                    bulan: bulan,
                    department_id: deptId
                },
                dataType: 'json',
                success: function(resp) {
                    // Abaikan response stale (dari request yang lebih lama)
                    if (myReqId !== dailyRequestId) return;

                    $('#daily-loading').hide();

                    if (!resp || !resp.dept_data || resp.dept_data.length === 0) {
                        $('#daily-empty').show();
                        return;
                    }

                    var targetText = resp.target || 0;
                    var operatorText = resp.operator || '>=';
                    var unitsText = resp.units || '%';
                    var operatorDisplay = operatorText;
                    if (operatorDisplay === '>=') operatorDisplay = '???';
                    if (operatorDisplay === '<=') operatorDisplay = '???';

                    $('#daily-info').text(resp.indicator || '');
                    var deptLabel = resp.dept_data.length === 1
                        ? resp.dept_data[0].department_name
                        : resp.dept_data.length + ' departemen';
                    $('#daily-target-info').html(
                        'Target: ' + operatorDisplay + ' ' + targetText + ' ' + unitsText +
                        ' | ' + deptLabel
                    );

                    var days = resp.days || 31;

                    // Bangun header: No, Ruangan, 1, 2, 3, ... , days
                    var headerHtml = '<th class="text-center" style="width:40px;">No</th><th style="min-width:250px;">Ruangan</th>';
                    for (var d = 1; d <= days; d++) {
                        headerHtml += '<th class="text-center" style="width:70px;">' + d + '</th>';
                    }

                    // Bangun baris per departemen
                    var bodyHtml = '';
                    $.each(resp.dept_data, function(idx, dept) {
                        bodyHtml += '<tr class="indicator-row">';
                        bodyHtml += '<td class="text-center fw-bold">' + (idx + 1) + '</td>';
                        bodyHtml += '<td class="text-start">' + dept.department_name + '</td>';

                        $.each(dept.daily, function(i, item) {
                            var cellClass = 'day-cell text-center';
                            var nilaiDisplay = '-';

                            if (item.nilai !== null) {
                                nilaiDisplay = item.nilai + ' ' + unitsText;
                                cellClass += ' cell-has-data';
                                if (item.tercapai === true) cellClass += ' cell-target';
                                else if (item.tercapai === false) cellClass += ' cell-fail';
                            } else {
                                cellClass += (item.num > 0 || item.denum > 0) ? ' cell-fail' : ' cell-empty';
                            }

                            var kpIcon = '';
                            if (item.kendala || item.perbaikan) {
                                kpIcon = '<i class="fas fa-exclamation-circle text-warning ms-1 kp-icon" style="font-size:10px;cursor:pointer;" data-dept-idx="' + idx + '" data-hari="' + item.hari + '"></i>';
                            }

                            bodyHtml += '<td class="' + cellClass + '" data-hari="' + item.hari + '">' +
                                '<div class="fw-bold">' + nilaiDisplay + kpIcon + '</div>' +
                                '<div class="num-denum">' + (item.num || 0) + ' / ' + (item.denum || 0) + '</div>' +
                                '</td>';
                        });

                        bodyHtml += '</tr>';

                    });

                    // Update konten
                    $('#daily-body').html(bodyHtml);
                    $('#daily-headers').html(headerHtml);

                    $('#daily-table').css('display', 'table');
                    if (!$('#daily-table').parent().is('.daily-table-scroll')) {
                        $('#daily-table').wrap('<div class="daily-table-scroll" style="overflow-x:auto;max-width:100%;"></div>');
                    }

                    // Click handler: icon warning -> toggle child row kendala/perbaikan
                    $('#daily-body').off('click', '.kp-icon').on('click', '.kp-icon', function() {
                        var deptIdx = $(this).data('dept-idx');
                        var hari = $(this).data('hari');
                        var deptData = resp.dept_data[deptIdx];
                        if (!deptData) return;

                        var dayData = null;
                        $.each(deptData.daily, function(i, d) {
                            if (d.hari === hari) dayData = d;
                        });
                        if (!dayData) return;

                        var tr = $(this).closest('tr');
                        var activeKey = deptIdx + '-' + hari;
                        var nextTr = tr.next('.kp-detail-row');

                        if (nextTr.length && nextTr.data('active-key') === activeKey) {
                            nextTr.remove();
                            tr.removeClass('kp-row-open');
                            return;
                        }

                        $('#daily-body').find('.kp-detail-row').remove();
                        $('#daily-body').find('.kp-row-open').removeClass('kp-row-open');

                        var colSpan = tr.find('td').length;
                        var detailHtml = '<tr class="kp-detail-row" data-active-key="' + activeKey + '">' +
                            '<td colspan="' + colSpan + '" class="p-0">' +
                            '<div class="p-3 bg-light" style="border-top:2px solid #ffc107;">' +
                            '<div class="d-flex align-items-start gap-3 flex-wrap">' +
                            '<div><strong>Ruangan:</strong> ' + $('<span>').text(deptData.department_name).html() + '</div>' +
                            '<div class="badge bg-warning text-dark fs-6">Hari ke-' + hari + '</div>' +
                            '</div>' +
                            '<hr class="my-2">' +
                            '<div class="d-flex align-items-start gap-3 flex-wrap">' +
                            '<div><strong>Kendala:</strong> ' + $('<span>').text(dayData.kendala || '-').html() + '</div>' +
                            '<div><strong>Perbaikan:</strong> ' + $('<span>').text(dayData.perbaikan || '-').html() + '</div>' +
                            '</div></div></td></tr>';

                        tr.after(detailHtml);
                        tr.addClass('kp-row-open');
                    });
                },
                error: function() {
                    if (myReqId !== dailyRequestId) return;
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

    function reload_table_impunit() {
        closeDaily();
        if (table_detail) {
            table_detail.ajax.reload();
        }
    }

    $(document).on('click', '#btn-export', function(e) {
        e.preventDefault();
        var exportUrl = '<?= site_url('siimut/rekap-laporan-impunit/export-indicator/' . $indicatorId) ?>?tahun=' + vtahun;
        window.location.href = exportUrl;
    });

    // Handle browser back button - force reload
    window.addEventListener('pageshow', function(event) {
        if (event.persisted || (window.performance && window.performance.navigation.type === 2)) {
            window.location.reload();
        }
    });
</script>