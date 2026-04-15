<style>
    #ajax_data_periode_imprs td,
    #ajax_data_periode_imprs th {
        font-size: 12px;
        vertical-align: middle;
        text-align: center;
        padding: 12px 8px !important;
        white-space: nowrap;
    }

    #ajax_data_periode_imprs th {
        background-color: #0d6efd !important;
        color: #fff;
        white-space: nowrap;
    }

    #ajax_data_periode_imprs td:first-child {
        text-align: left;
        white-space: nowrap;
    }

    .cell-target {
        background-color: rgba(13, 110, 253, 0.9) !important;
        color: #fff !important;
    }

    .cell-empty {
        background-color: rgba(255, 193, 7, 0.9) !important;
        color: #000 !important;
    }

    .cell-fail {
        background-color: rgba(220, 53, 69, 0.9) !important;
        color: #fff !important;
    }

    .dataTables_wrapper .dataTables_processing {
        display: none !important;
    }

    table.dataTable.loading {
        opacity: 0.2;
    }

    .dataTables_scrollBody {
        background-color: transparent !important;
    }

    [data-bs-theme="dark"] .dataTables_scrollBody {
        background-color: transparent !important;
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
</style>

<div class="container-fluid py-4">
    <div class="row mb-3 align-items-end">
        <div class="col-md-4">
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

        <div class="col-md-8">
            <label class="form-label fw-semibold mb-1">Filter Periode</label>
            <div class="d-flex flex-wrap align-items-center gap-2">
                <button type="button" class="btn btn-outline-primary btn-filter" data-type="all" onclick="filterPeriode('all')">Semua</button>
                <button type="button" class="btn btn-outline-primary btn-filter" data-type="triwulan" onclick="filterPeriode('triwulan')">Triwulan</button>
                <button type="button" class="btn btn-outline-primary btn-filter" data-type="semester" onclick="filterPeriode('semester')">Semester</button>
                <button type="button" class="btn btn-outline-primary btn-filter" data-type="tahun" onclick="filterPeriode('tahun')">Tahun</button>

                <div class="vr mx-2"></div>

                <a href="#" id="btn-export-periode" class="btn btn-primary" title="Download Excel">
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
                            <span class="badge bg-primary me-2">Terjadi</span>
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
                        <table id="ajax_data_periode_imprs" class="table table-bordered table-striped" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th rowspan="2" class="align-middle">No</th>
                                    <th rowspan="2" class="align-middle text-start">Indikator</th>
                                    <th rowspan="2" class="align-middle">Target</th>
                                    <th colspan="4" class="text-center bg-primary">Triwulan</th>
                                    <th colspan="2" class="text-center bg-info">Semester</th>
                                    <th rowspan="2" class="align-middle bg-primary">Tahun</th>
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
    });

    $(document).on('keydown', function(e) {
        if (e.key === 'F5') {
            e.preventDefault();
            vtahun = <?= date('Y') ?>;
            $('#tahun').val(vtahun);
            var newAjaxUrl = '<?= site_url('siimut/rekap-periode-imprs/ajax_imprs-') ?>' + vtahun;
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
        var newAjaxUrl = '<?= site_url('siimut/rekap-periode-imprs/ajax_imprs-') ?>' + vtahun;
        table_periode.ajax.url(newAjaxUrl);
        table_periode.ajax.reload();
    }

    function initTable() {
        var tableWrapper = $('#ajax_data_periode_imprs').closest('.table-responsive');

        table_periode = $('#ajax_data_periode_imprs').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: '<?= site_url('siimut/rekap-periode-imprs/ajax_imprs-') ?>' + vtahun,
                type: 'POST',
                data: function(d) {
                    d.tahun = vtahun;
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
                        return '<div class="text-start">' + data + '</div>';
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

        var newAjaxUrl = '<?= site_url('siimut/rekap-periode-imprs/ajax_imprs-') ?>' + vtahun;
        table_periode.ajax.url(newAjaxUrl);

        var tableWrapper = $('#ajax_data_periode_imprs').closest('.table-responsive');
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
        var exportUrl = '<?= site_url('siimut/rekap-periode-imprs/export') ?>?tahun=' + vtahun;
        window.location.href = exportUrl;
    });
</script>
