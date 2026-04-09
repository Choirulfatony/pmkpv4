<style>
    #ajax_data_periode td, #ajax_data_periode th {
        font-size: 12px;
        vertical-align: middle;
        text-align: center;
        padding: 12px 8px !important;
        white-space: nowrap;
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
    .cell-target {
        background-color: rgba(40, 167, 69, 0.9) !important;
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

    /* Table loading state - prevent white/black flash */
    table.dataTable.loading {
        opacity: 0.2;
    }

    /* Prevent white flash in dark mode */
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
    <!-- <div class="row mb-4">
        <div class="col-12">
            <h4 class="mb-1"><i class="bi bi-calendar-range me-2"></i>Rekap INM per Periode</h4>
            <p class="text-muted">Laporan indikator nasional mutu per Triwulan, Semester, dan Tahun</p>
        </div>
    </div> -->

    <div class="row mb-3">
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

// F5 refresh should reset to current year
$(document).on('keydown', function(e) {
    if (e.key === 'F5') {
        // Reset to current year when F5 is pressed
        vtahun = <?= date('Y') ?>;
        $('#tahun').val(vtahun);
        // Update URL without page reload
        var url = new URL(window.location.href);
        url.searchParams.set('tahun', vtahun);
        window.history.pushState({}, '', url);
    }
});

function refreshPage() {
    // Reload the page with currently selected year
    var selectedYear = $('#tahun').val();
    var url = new URL(window.location.href);
    url.searchParams.set('tahun', selectedYear);
    window.location.href = url.toString();
}

function initTable() {
     console.log('Loading data from:', '<?= site_url('siimut/rekap-laporan-inm/rekap-periode-inm') ?>');
     
     var tableWrapper = $('#ajax_data_periode').closest('.table-responsive');
     
     table_periode = $('#ajax_data_periode').DataTable({
         processing: true,
         serverSide: false,
         ajax: {
             url: '<?= site_url('siimut/rekap-laporan-inm/rekap-periode-inm') ?>',
             type: 'POST',
             data: function(d) {
                 d.tahun = vtahun;
                 return d;
             },
             dataSrc: 'data',
            beforeSend: function(xhr) {
                if ($('#loading_overlay_detail').length === 0) {
                    tableWrapper.append(
                        '<div class="overlay-wrapper" id="loading_overlay_detail">' +
                        '<div class="overlay">' +
                        '<i class="loader"></i>' +
                        '</div>' +
                        '</div>'
                    );
                }
            },
            complete: function() {
                $('#loading_overlay_detail').remove();
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
                        if (!periodeData.nilai || periodeData.nilai === 0) {
                            $(td).addClass('cell-empty');
                        } else if (periodeData.tercap) {
                            $(td).addClass('cell-target');
                        } else {
                            $(td).addClass('cell-fail');
                        }
                    }
                }
            }
        }],
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
                    var tw = row.triwulan && row.triwulan[1] ? row.triwulan[1] : {};
                    var tercapai = tw.tercap || false;
                    var num = tw.num || 0;
                    var denum = tw.denum || 0;
                    var units = row.indicator_units || '';
                    return renderCell(data, tercapai, num, denum, units);
                }
            },
            { 
                data: 'triwulan.2.nilai',
                render: function(data, type, row) {
                    var tw = row.triwulan && row.triwulan[2] ? row.triwulan[2] : {};
                    var tercapai = tw.tercap || false;
                    var num = tw.num || 0;
                    var denum = tw.denum || 0;
                    var units = row.indicator_units || '';
                    return renderCell(data, tercapai, num, denum, units);
                }
            },
            { 
                data: 'triwulan.3.nilai',
                render: function(data, type, row) {
                    var tw = row.triwulan && row.triwulan[3] ? row.triwulan[3] : {};
                    var tercapai = tw.tercap || false;
                    var num = tw.num || 0;
                    var denum = tw.denum || 0;
                    var units = row.indicator_units || '';
                    return renderCell(data, tercapai, num, denum, units);
                }
            },
            { 
                data: 'triwulan.4.nilai',
                render: function(data, type, row) {
                    var tw = row.triwulan && row.triwulan[4] ? row.triwulan[4] : {};
                    var tercapai = tw.tercap || false;
                    var num = tw.num || 0;
                    var denum = tw.denum || 0;
                    var units = row.indicator_units || '';
                    return renderCell(data, tercapai, num, denum, units);
                }
            },
            { 
                data: 'semester.1.nilai',
                render: function(data, type, row) {
                    var sm = row.semester && row.semester[1] ? row.semester[1] : {};
                    var tercapai = sm.tercap || false;
                    var num = sm.num || 0;
                    var denum = sm.denum || 0;
                    var units = row.indicator_units || '';
                    return renderCell(data, tercapai, num, denum, units);
                }
            },
            { 
                data: 'semester.2.nilai',
                render: function(data, type, row) {
                    var sm = row.semester && row.semester[2] ? row.semester[2] : {};
                    var tercapai = sm.tercap || false;
                    var num = sm.num || 0;
                    var denum = sm.denum || 0;
                    var units = row.indicator_units || '';
                    return renderCell(data, tercapai, num, denum, units);
                }
            },
            { 
                data: 'tahun.nilai',
                render: function(data, type, row) {
                    var th = row.tahun || {};
                    var tercapai = th.tercap || false;
                    var num = th.num || 0;
                    var denum = th.denum || 0;
                    var units = row.indicator_units || '';
                    return renderCell(data, tercapai, num, denum, units);
                }
            }
        ]
    });
}

function renderCell(nilai, tercapai, num, denum, units) {
    return '<div class="fw-semibold">' + nilai + ' ' + units + '</div>' +
           '<div class="small opacity-75">' + num + ' / ' + denum + '</div>';
}

function gantiTahun() {
    vtahun = $('#tahun').val();
    var url = new URL(window.location.href);
    url.searchParams.set('tahun', vtahun);
    window.history.pushState({}, '', url);
    
    var tableWrapper = $('#ajax_data_periode').closest('.table-responsive');
    
    if ($('#loading_overlay_detail').length === 0) {
        tableWrapper.append(
            '<div class="overlay-wrapper" id="loading_overlay_detail">' +
            '<div class="overlay">' +
            '<i class="loader"></i>' +
            '</div>' +
            '</div>'
        );
    }
    
    table_periode.ajax.reload(function() {
        $('#loading_overlay_detail').remove();
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