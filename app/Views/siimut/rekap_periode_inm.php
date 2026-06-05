
<div class="container-fluid py-4">
    <!-- <div class="row mb-3">
        <div class="col-md-4 d-flex align-items-center">
            <label class="form-label fw-bold me-2">Pilih Tahun</label>
            <div class="input-group input-group-sm" style="width: 200px;">
                <select class="form-select form-select-sm" id="tahun" onchange="gantiTahun()">
                    <?php for ($y = date('Y'); $y >= date('Y') - 5; $y--): ?>
                        <option value="<?= $y ?>" <?= ($y == $tahun) ? 'selected' : '' ?>><?= $y ?></option>
                    <?php endfor; ?>
                </select>
                <button class="btn btn-outline-secondary" type="button" onclick="refreshPage()">
                    <i class="bi bi-arrow-clockwise"></i>
                </button>
            </div>
        </div>
        <div class="col-md-8">
            <label class="form-label fw-bold mb-2">Filter Periode</label>

            <div class="d-flex flex-wrap align-items-center gap-2">
                <button type="button" class="btn btn-outline-primary" onclick="filterPeriode('all')">
                    Semua
                </button>

                <button type="button" class="btn btn-outline-success" onclick="filterPeriode('triwulan')">
                    Triwulan
                </button>

                <button type="button" class="btn btn-outline-warning" onclick="filterPeriode('semester')">
                    Semester
                </button>

                <button type="button" class="btn btn-outline-info" onclick="filterPeriode('tahun')">
                    Tahun
                </button>

                <div class="vr mx-1"></div>

                <a href="#" id="btn-export-periode" class="btn btn-success" title="Download Excel">
                    <i class="bi bi-file-earmark-excel me-1"></i> Export
                </a>
            </div>
        </div>
    </div> -->
    <div class="row mb-3 align-items-end">

        <!-- PILIH TAHUN -->
        <div class="col-md-3">
            <label class="form-label fw-semibold mb-1">Pilih Tahun</label>

            <div class="input-group input-group-sm" style="max-width: 220px;">
                <select class="form-select" id="tahun" onchange="gantiTahun()">
                    <?php for ($y = date('Y'); $y >= date('Y') - 5; $y--): ?>
                        <option value="<?= $y ?>" <?= ($y == $tahun) ? 'selected' : '' ?>>
                            <?= $y ?>
                        </option>
                    <?php endfor; ?>
                </select>

                <button class="btn btn-outline-secondary" type="button" onclick="refreshPage()">
                    <i class="bi bi-arrow-clockwise"></i>
                </button>
            </div>
        </div>

        <?php if (!empty($showDepartmentFilter)): ?>
        <!-- FILTER DEPARTEMEN -->
        <div class="col-md-3">
            <label class="form-label fw-semibold mb-1">Pilih Ruangan</label>
            <select class="form-select form-select-sm" id="filter_department" onchange="gantiDepartemen()" style="max-width: 280px;">
                <option value="">-- Semua Ruangan --</option>
                <?php if (!empty($departments)): ?>
                    <?php foreach ($departments as $dept): ?>
                        <?php
                        $draftInfo = '';
                        if (!empty($draftCounts)) {
                            foreach ($draftCounts as $dc) {
                                if ((int)$dc['department_id'] === (int)$dept->department_id) {
                                    $draftInfo = ' (Draft: ' . $dc['total_draft'] . ')';
                                    break;
                                }
                            }
                        }
                        ?>
                        <option value="<?= esc($dept->department_id) ?>"><?= esc($dept->department_name) ?><?= $draftInfo ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>
        <?php endif; ?>

        <!-- BADGE DRAFT -->
        <div class="col-md-<?= !empty($showDepartmentFilter) ? '3' : '9' ?> d-flex align-items-end">
            <?php if (!empty($totalDraft) && $totalDraft > 0): ?>
                <?php
                $namaBulan = [1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'Mei',6=>'Jun',7=>'Jul',8=>'Agu',9=>'Sep',10=>'Okt',11=>'Nov',12=>'Des'];
                $breakdownLines = [];
                foreach (($draftByMonth ?? []) as $bln => $cnt) {
                    if ($cnt > 0) {
                        $breakdownLines[] = $namaBulan[$bln] . ': ' . $cnt;
                    }
                }
                $tooltipText = 'Tahun ' . $tahun . " | " . implode(' | ', $breakdownLines);
                ?>
                <a href="<?= site_url('siimut/approval') ?>" class="badge bg-warning text-dark text-decoration-none"
                   title="<?= esc($tooltipText) ?>"
                   data-bs-toggle="tooltip" data-bs-placement="bottom"
                   style="font-size: 12px; padding: 8px 12px; cursor: pointer;">
                    <i class="bi bi-clock me-1"></i> Draft Menunggu Approval: <?= $totalDraft ?>
                </a>
            <?php endif; ?>
        </div>

        <!-- FILTER PERIODE -->
        <div class="col-md-<?= !empty($showDepartmentFilter) ? '6' : '9' ?>">
            <label class="form-label fw-semibold mb-1">Filter Periode</label>

            <div class="d-flex flex-wrap align-items-center gap-2">

                <button type="button" class="btn btn-outline-primary btn-filter" data-type="all"
                    onclick="filterPeriode('all')">
                    Semua
                </button>

                <button type="button" class="btn btn-outline-success btn-filter" data-type="triwulan"
                    onclick="filterPeriode('triwulan')">
                    Triwulan
                </button>

                <button type="button" class="btn btn-outline-warning btn-filter" data-type="semester"
                    onclick="filterPeriode('semester')">
                    Semester
                </button>

                <button type="button" class="btn btn-outline-info btn-filter" data-type="tahun"
                    onclick="filterPeriode('tahun')">
                    Tahun
                </button>

                <div class="vr mx-2"></div>

                <a href="#" id="btn-export-periode" class="btn btn-success" title="Download Excel">
                    <i class="bi bi-file-earmark-excel me-1"></i> Export Excel
                </a>
            </div>
        </div>

    </div>


    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex gap-3 flex-wrap">
                        <div class="d-flex align-items-center">
                            <span class="badge bg-success me-2">Terjadi</span>
                            <small class="text-muted">Target Tercapai</small>
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="badge bg-danger me-2">Tidak</span>
                            <small class="text-muted">Target Tidak Tercapai</small>
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="badge bg-warning text-dark me-2">Kosong</span>
                            <small class="text-muted">Belum Ada Data</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="ajax_data_periode_inm" class="table table-bordered table-striped" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th rowspan="2" class="align-middle">No</th>
                                    <th rowspan="2" class="align-middle text-start">Indikator</th>
                                    <th rowspan="2" class="align-middle">Target</th>
                                    <th colspan="4" class="text-center bg-primary">Triwulan</th>
                                    <th colspan="2" class="text-center bg-info">Semester</th>
                                    <th rowspan="2" class="align-middle bg-success">Tahun</th>
                                </tr>
                                <tr>
                                    <th>T1</th>
                                    <th>T2</th>
                                    <th>T3</th>
                                    <th>T4</th>
                                    <th>S1</th>
                                    <th>S2</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data akan di-load via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let table_periode;
    let vtahun = <?= date('Y') ?>;
    let vFilter = 'all';

    $(document).ready(function() {
        vtahun = <?= date('Y') ?>;
        $('#tahun').val(vtahun);
        initTable();

        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (el) { return new bootstrap.Tooltip(el); });
    });



    $(document).on('keydown', function(e) {
        if (e.key === 'F5') {
            e.preventDefault();
            vtahun = <?= date('Y') ?>;
            $('#tahun').val(vtahun);

            // Update AJAX URL to current year
            var newAjaxUrl = '<?= site_url('siimut/rekap-periode-inm/ajax_inm-') ?>' + vtahun;
            table_periode.ajax.url(newAjaxUrl);
            table_periode.ajax.reload();
        }
    });

    function filterPeriode(type) {
        document.querySelectorAll('.btn-filter').forEach(btn => {
            btn.classList.remove('active');
        });

        document.querySelector(`[data-type="${type}"]`).classList.add('active');
    }

    function refreshPage() {
        vtahun = $('#tahun').val();

        // Update AJAX URL with selected year
        var newAjaxUrl = '<?= site_url('siimut/rekap-periode-inm/ajax_inm-') ?>' + vtahun;
        table_periode.ajax.url(newAjaxUrl);
        table_periode.ajax.reload();
    }

    function gantiDepartemen() {
        refreshPage();
    }

    function initTable() {
        var tableWrapper = $('#ajax_data_periode_inm').closest('.table-responsive');

        table_periode = $('#ajax_data_periode_inm').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: '<?= site_url('siimut/rekap-periode-inm/ajax_inm-') ?>' + vtahun,
                type: 'POST',
                data: function(d) {
                    d.tahun = vtahun;
                    d.department_id = $('#filter_department').length ? $('#filter_department').val() : '';
                    return d;
                },
                dataSrc: 'data',
                beforeSend: function(xhr) {
                    if ($('#loading_overlay_periode').length === 0) {
                        tableWrapper.append(
                            '<div class="overlay-wrapper" id="loading_overlay_periode">' +
                            '<div class="overlay">' +
                            '<i class="loader"></i>' +
                            '</div>' +
                            '</div>'
                        );
                    }
                },
                complete: function() {
                    $('#loading_overlay_periode').remove();
                }
            },
            columnDefs: [{
                targets: [0, 1, 2],
                orderable: false
            }, {
                targets: '_all',
                createdCell: function(td, cellData, rowData, rowIndex, colIndex) {
                    if (colIndex > 2) {
                        var periodeData;
                        var colName = table_periode.column(colIndex).dataSrc();

                        if (colName.includes('triwulan')) {
                            var twNum = parseInt(colName.split('.')[1]);
                            periodeData = rowData.triwulan ? rowData.triwulan[twNum] : null;
                        } else if (colName.includes('semester')) {
                            var smNum = parseInt(colName.split('.')[1]);
                            periodeData = rowData.semester ? rowData.semester[smNum] : null;
                        } else if (colName.includes('tahun')) {
                            periodeData = rowData.tahun;
                        }

                        if (periodeData) {
                            var status = periodeData.status || '';
                            if (status === 'TIDAK ADA DATA' || periodeData.nilai === null) {
                                $(td).addClass('cell-empty');
                            } else if (status === 'TERCAPAI') {
                                $(td).addClass('cell-target');
                            } else {
                                $(td).addClass('cell-fail');
                            }
                        }
                    }
                }
            }],
            columns: [{
                    data: null,
                    render: function(data, type, row, meta) {
                        return meta.row + 1;
                    }
                },
                {
                    data: 'indicator_element',
                    render: function(data, type, row) {
                        var badge = '';
                        if (row.indicator_record_status === 'D') {
                            badge = ' <span class="badge bg-secondary ms-1" style="font-size:10px;vertical-align:middle;">Non-Aktif</span>';
                        }
                        return '<div class="text-start">' + data + badge + '</div>';
                    }
                },
                {
                    data: 'target',
                    render: function(data, type, row) {
                        return data + ' ' + row.satuan;
                    }
                },
                {
                    data: 'triwulan.1.nilai',
                    render: function(data, type, row) {
                        var tw = row.triwulan && row.triwulan[1] ? row.triwulan[1] : {};
                        var status = tw.status || '';
                        var num = tw.num || 0;
                        var denum = tw.denum || 0;
                        var units = row.satuan || '';
                        return renderCell(data, status, num, denum, units);
                    }
                },
                {
                    data: 'triwulan.2.nilai',
                    render: function(data, type, row) {
                        var tw = row.triwulan && row.triwulan[2] ? row.triwulan[2] : {};
                        var status = tw.status || '';
                        var num = tw.num || 0;
                        var denum = tw.denum || 0;
                        var units = row.satuan || '';
                        return renderCell(data, status, num, denum, units);
                    }
                },
                {
                    data: 'triwulan.3.nilai',
                    render: function(data, type, row) {
                        var tw = row.triwulan && row.triwulan[3] ? row.triwulan[3] : {};
                        var status = tw.status || '';
                        var num = tw.num || 0;
                        var denum = tw.denum || 0;
                        var units = row.satuan || '';
                        return renderCell(data, status, num, denum, units);
                    }
                },
                {
                    data: 'triwulan.4.nilai',
                    render: function(data, type, row) {
                        var tw = row.triwulan && row.triwulan[4] ? row.triwulan[4] : {};
                        var status = tw.status || '';
                        var num = tw.num || 0;
                        var denum = tw.denum || 0;
                        var units = row.satuan || '';
                        return renderCell(data, status, num, denum, units);
                    }
                },
                {
                    data: 'semester.1.nilai',
                    render: function(data, type, row) {
                        var sm = row.semester && row.semester[1] ? row.semester[1] : {};
                        var status = sm.status || '';
                        var num = sm.num || 0;
                        var denum = sm.denum || 0;
                        var units = row.satuan || '';
                        return renderCell(data, status, num, denum, units);
                    }
                },
                {
                    data: 'semester.2.nilai',
                    render: function(data, type, row) {
                        var sm = row.semester && row.semester[2] ? row.semester[2] : {};
                        var status = sm.status || '';
                        var num = sm.num || 0;
                        var denum = sm.denum || 0;
                        var units = row.satuan || '';
                        return renderCell(data, status, num, denum, units);
                    }
                },
                {
                    data: 'tahun.nilai',
                    render: function(data, type, row) {
                        var th = row.tahun || {};
                        var status = th.status || '';
                        var num = th.num || 0;
                        var denum = th.denum || 0;
                        var units = row.satuan || '';
                        return renderCell(data, status, num, denum, units);
                    }
                }
            ]
        });
    }

    function renderCell(nilai, status, num, denum, units) {
        if (nilai === null || nilai === undefined) {
            return '<div class="text-muted">-</div><div class="small opacity-75">0 / 0</div>';
        }

        return '<div class="fw-semibold">' + nilai + ' ' + units + '</div>' +
            '<div class="small opacity-75">' + num + ' / ' + denum + '</div>';
    }

    function gantiTahun() {
        vtahun = $('#tahun').val();
        var url = new URL(window.location.href);
        url.searchParams.set('tahun', vtahun);
        window.history.pushState({}, '', url);

        // Update AJAX URL with new tahun
        var newAjaxUrl = '<?= site_url('siimut/rekap-periode-inm/ajax_inm-') ?>' + vtahun;
        table_periode.ajax.url(newAjaxUrl);

        var tableWrapper = $('#ajax_data_periode_inm').closest('.table-responsive');
        tableWrapper.find('.overlay-wrapper').remove();

        tableWrapper.append(
            '<div class="overlay-wrapper" id="loading_overlay_periode">' +
            '<div class="overlay">' +
            '<i class="loader"></i>' +
            '</div>' +
            '</div>'
        );

        table_periode.ajax.reload(function() {
            $('#loading_overlay_periode').remove();
        }, false);
    }

    function filterPeriode(type) {
        vFilter = type;

        var colDasar = [0, 1, 2];
        var colTriwulan = [3, 4, 5, 6];
        var colSemester = [7, 8];
        var colTahun = [9];

        if (type === 'all') {
            table_periode.columns().visible(true);
        } else if (type === 'triwulan') {
            table_periode.columns().visible(false);
            table_periode.columns(colDasar.concat(colTriwulan)).visible(true);
        } else if (type === 'semester') {
            table_periode.columns().visible(false);
            table_periode.columns(colDasar.concat(colSemester)).visible(true);
        } else if (type === 'tahun') {
            table_periode.columns().visible(false);
            table_periode.columns(colDasar.concat(colTahun)).visible(true);
        }
    }

    $(document).on('click', '#btn-export-periode', function(e) {
        e.preventDefault();
        var deptParam = '';
        if ($('#filter_department').length && $('#filter_department').val()) {
            deptParam = '&department_id=' + $('#filter_department').val();
        }
        var exportUrl = '<?= site_url('siimut/rekap-periode-inm/export') ?>?tahun=' + vtahun + deptParam;
        window.location.href = exportUrl;
    });
</script>