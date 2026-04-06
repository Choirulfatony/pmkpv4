<style>
    #ajax_data_periode td, #ajax_data_periode th {
        font-size: 12px;
        vertical-align: middle;
        text-align: center;
        padding: 8px 4px !important;
    }
    #ajax_data_periode th {
        background-color: #198754 !important;
        color: #fff;
        white-space: nowrap;
    }
    #ajax_data_periode td:first-child {
        text-align: left;
        white-space: nowrap;
    }
    .badge-tercap {
        background-color: #28a745;
        color: white;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 11px;
    }
    .badge-tidak-tercap {
        background-color: #dc3545;
        color: white;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 11px;
    }
    .badge-kosong {
        background-color: #ffc107;
        color: #000;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 11px;
    }
    .dataTables_wrapper .dataTables_processing {
        background: transparent !important;
        border: none !important;
        box-shadow: none !important;
    }
    .dataTables_wrapper .dataTables_processing::before,
    .dataTables_wrapper .dataTables_processing::after {
        display: none !important;
    }
    table.dataTable tbody tr.odd {
        background-color: #f9f9f9;
    }
    table.dataTable tbody tr.even {
        background-color: #fff;
    }
    .dataTables_wrapper .dataTables_processing {
        display: none !important;
    }
    .custom-loading {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 100;
        background: rgba(255,255,255,0.9);
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    .custom-loading img {
        width: 50px;
        height: 50px;
    }
    .custom-loading-text {
        margin-top: 10px;
        color: #198754;
        font-weight: 600;
    }
    .dataTables_wrapper .dataTables_processing {
        display: none !important;
    }
</style>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h4 class="mb-1"><i class="bi bi-calendar-range me-2"></i>Rekap INM per Periode</h4>
            <p class="text-muted">Laporan indikator nasional mutu per Triwulan, Semester, dan Tahun</p>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-4">
            <label class="form-label fw-bold">Pilih Tahun</label>
            <select class="form-select" id="tahun" onchange="gantiTahun()">
                <?php for ($y = date('Y'); $y >= date('Y') - 5; $y--): ?>
                    <option value="<?= $y ?>" <?= ($y == $tahun) ? 'selected' : '' ?>><?= $y ?></option>
                <?php endfor; ?>
            </select>
        </div>
        <div class="col-md-8">
            <label class="form-label fw-bold">Filter Periode</label>
            <div>
                <button class="btn btn-primary me-1" onclick="filterPeriode('all')">Semua</button>
                <button class="btn btn-success me-1" onclick="filterPeriode('triwulan')">Triwulan</button>
                <button class="btn btn-warning" onclick="filterPeriode('semester')">Semester</button>
                <button class="btn btn-info me-1" onclick="filterPeriode('tahun')">Tahun</button>
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
                        <table id="ajax_data_periode" class="table table-bordered table-striped" style="width: 100%;">
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
let vtahun = <?= $tahun ?: date('Y') ?>;
let vFilter = 'all';

$(document).ready(function() {
    console.log('Initializing table...');
    console.log('Tahun:', vtahun);
    
    // Set dropdown to match URL parameter
    $('#tahun').val(vtahun);
    
    initTable();
});

function initTable() {
    console.log('Loading data from:', '<?= site_url('siimut/rekap-laporan-inm/rekap-periode-ajax') ?>');
    
    var tableWrapper = $('#ajax_data_periode').closest('.table-responsive');
    
    table_periode = $('#ajax_data_periode').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: '<?= site_url('siimut/rekap-laporan-inm/rekap-periode-ajax') ?>',
            type: 'POST',
            data: function(d) {
                d.tahun = vtahun;
                return d;
            },
            dataSrc: 'data',
            beforeSend: function(xhr) {
                tableWrapper.append(
                    '<div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center bg-white bg-opacity-75 loading-overlay" style="z-index:10;">' +
                    '<div class="text-center">' +
                    '<div class="spinner-border text-success" role="status"></div>' +
                    '<div class="mt-2 small text-muted">Memuat data...</div>' +
                    '</div>' +
                    '</div>'
                );
            },
            complete: function() {
                $('.loading-overlay').remove();
            }
        },
        columns: [
            { 
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
                data: 'indicator_target',
                render: function(data, type, row) {
                    return data + ' ' + row.indicator_units;
                }
            },
            { 
                data: 'triwulan.1.nilai',
                render: function(data, type, row) {
                    var tercapai = row.triwulan && row.triwulan[1] ? row.triwulan[1].tercap : false;
                    return renderCell(data, tercapai);
                }
            },
            { 
                data: 'triwulan.2.nilai',
                render: function(data, type, row) {
                    var tercapai = row.triwulan && row.triwulan[2] ? row.triwulan[2].tercap : false;
                    return renderCell(data, tercapai);
                }
            },
            { 
                data: 'triwulan.3.nilai',
                render: function(data, type, row) {
                    var tercapai = row.triwulan && row.triwulan[3] ? row.triwulan[3].tercap : false;
                    return renderCell(data, tercapai);
                }
            },
            { 
                data: 'triwulan.4.nilai',
                render: function(data, type, row) {
                    var tercapai = row.triwulan && row.triwulan[4] ? row.triwulan[4].tercap : false;
                    return renderCell(data, tercapai);
                }
            },
            { 
                data: 'semester.1.nilai',
                render: function(data, type, row) {
                    var tercapai = row.semester && row.semester[1] ? row.semester[1].tercap : false;
                    return renderCell(data, tercapai);
                }
            },
            { 
                data: 'semester.2.nilai',
                render: function(data, type, row) {
                    var tercapai = row.semester && row.semester[2] ? row.semester[2].tercap : false;
                    return renderCell(data, tercapai);
                }
            },
            { 
                data: 'tahun.nilai',
                render: function(data, type, row) {
                    var tercapai = row.tahun ? row.tahun.tercap : false;
                    return renderCell(data, tercapai);
                }
            }
        ]
    });
}

function renderCell(nilai, tercapai) {
    if (!nilai) {
        return '<span class="badge badge-kosong">Kosong</span>';
    }
    
    var badge = tercapai 
        ? '<span class="badge badge-tercap">Tercapai</span>' 
        : '<span class="badge badge-tidak-tercap">Tidak</span>';
    
    return nilai + '<div class="mt-1">' + badge + '</div>';
}

function gantiTahun() {
    vtahun = $('#tahun').val();
    var url = new URL(window.location.href);
    url.searchParams.set('tahun', vtahun);
    window.history.pushState({}, '', url);
    
    var tableWrapper = $('#ajax_data_periode').closest('.table-responsive');
    
    tableWrapper.append(
        '<div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center bg-white bg-opacity-75 loading-overlay" style="z-index:10;">' +
        '<div class="text-center">' +
        '<div class="spinner-border text-success" role="status"></div>' +
        '<div class="mt-2 small text-muted">Memuat data...</div>' +
        '</div>' +
        '</div>'
    );
    
    table_periode.ajax.reload(function() {
        $('.loading-overlay').remove();
    });
}

function filterPeriode(type) {
    vFilter = type;
    console.log('Filter clicked:', type);
    
    // Kolom: 0=No, 1=Indikator, 2=Target, 3-6=Triwulan, 7-8=Semester, 9=Tahun
    var colDasar = [0, 1, 2]; // No, Indikator, Target - selalu tampil
    var colTriwulan = [3, 4, 5, 6];
    var colSemester = [7, 8];
    var colTahun = [9];
    
    if (type === 'all') {
        table_periode.columns().visible(true);
        console.log('Showing all columns');
    } else if (type === 'triwulan') {
        table_periode.columns().visible(false);
        table_periode.columns(colDasar.concat(colTriwulan)).visible(true);
        console.log('Showing Triwulan columns');
    } else if (type === 'semester') {
        table_periode.columns().visible(false);
        table_periode.columns(colDasar.concat(colSemester)).visible(true);
        console.log('Showing Semester columns');
    } else if (type === 'tahun') {
        table_periode.columns().visible(false);
        table_periode.columns(colDasar.concat(colTahun)).visible(true);
        console.log('Showing Tahun column');
    }
}
</script>