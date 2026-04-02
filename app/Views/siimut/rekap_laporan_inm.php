<style>
    .card-maximized {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        width: 100vw !important;
        height: 100vh !important;
        z-index: 1050;
        margin: 0;
        border-radius: 0;
    }
    .card-maximized .card-body {
        overflow: auto;
        max-height: calc(100vh - 60px);
    }
    .badge-info {
        padding: 4px 8px;
        font-size: 11px;
        border-radius: 4px;
    }
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
    .legend-dot {
        width: 12px;
        height: 12px;
        border-radius: 3px;
        display: inline-block;
    }
    #ajax_data_rekap td,
    #ajax_data_rekap th {
        font-size: 13px;
        vertical-align: middle;
        white-space: nowrap;
        padding: 12px 10px !important;
    }
    #ajax_data_rekap th {
        background-color: #3d9970 !important;
        color: #fff;
        text-align: center;
        font-weight: 600;
        padding: 12px 10px !important;
    }
    #ajax_data_rekap td a {
        color: #000;
        text-decoration: none;
        font-weight: 600;
    }
    #ajax_data_rekap td a:hover {
        color: #007bff;
        text-decoration: underline;
    }
    #ajax_data_rekap td {
        text-align: center;
        padding: 10px 8px !important;
    }
    #ajax_data_rekap td:nth-child(2) {
        text-align: left !important;
        min-width: 250px;
        padding-left: 15px !important;
    }
</style>

<!-- ==================== HEADER INFO ==================== -->
<div class="row mb-3">
    <div class="col-12">
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <div class="d-flex align-items-start">
                <div class="me-3">
                    <i class="fas fa-info-circle fa-2x text-warning"></i>
                </div>
                <div class="flex-grow-1">
                    <h5 class="mb-1"><strong>Informasi Rekap Indikator Nasional Mutu (INM)</strong></h5>
                    <p class="mb-2">Untuk melihat detail hasil per ruangan, silahkan klik pada indikator yang diinginkan.</p>
                    <div class="d-flex flex-wrap gap-3">
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
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
</div>

<!-- ==================== CARD TABEL REKAP ==================== -->
<div class="row">
    <div class="col-12">
        <div class="card card-outline card-warning" id="detail_satu">
            <!-- HEADER -->
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-bar me-2"></i>
                    Rekap INM per Bulan
                </h3>
                <div class="card-tools d-flex align-items-center gap-2">
                    <!-- Tahun Picker -->
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
                        <button type="button" class="btn btn-outline-secondary" onclick="maximizeCard(this)" title="Fullscreen">
                            <i class="fas fa-expand"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- BODY -->
            <div class="card-body p-0">
                <!-- Loading Overlay -->
                <div id="loading_overlay" class="d-none position-absolute w-100 h-100 bg-white bg-opacity-75 d-flex justify-content-center align-items-center" style="z-index: 10; min-height: 200px;">
                    <div class="spinner-border text-warning" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>

                <!-- Tabel -->
                <div class="table-responsive p-3">
                    <table id="ajax_data_rekap" class="table table-bordered table-hover table-striped mb-0" style="width: 100%;">
                        <thead>
                            <tr class="align-middle">
                                <th style="width: 50px;" class="text-center">#</th>
                                <th style="min-width: 250px; text-align: left !important; padding-left: 15px !important;">Indikator</th>
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
</div>

<!-- ==================== SCRIPT ==================== -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/css/tempusdominus-bootstrap-4.min.css" />

<script>
var table_loquin;
var vtahun = '<?= $tahun ?>';
var target, factor, operator;

$(document).ready(function() {

    // Init DataTable
    table_loquin = $('#ajax_data_rekap').DataTable({
        processing: false, // Disable DataTables built-in processing
        serverSide: true,
        autoWidth: false,
        pageLength: 10, // Kurangi default ke 10 untuk loading lebih cepat
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Semua"]],
        ajax: {
            url: '<?= site_url('siimut/rekap-laporan-inm/ajax') ?>',
            type: 'POST',
            data: function(d) {
                d.vtahun = vtahun;
                return d;
            },
            beforeSend: function() {
                $('#loading_overlay').removeClass('d-none');
            },
            complete: function() {
                $('#loading_overlay').addClass('d-none');
            }
        },
        columnDefs: [{
            targets: [0],
            orderable: false,
            className: 'text-center'
        }, {
            targets: [-1, -2, -3, -4, -5, -6, -7, -8, -9, -10, -11, -12, -13],
            orderable: false,
            className: 'text-center',
            createdCell: function(td, cellData, rowData, row, col) {
                if (col == 1) {
                    try {
                        let parser = new DOMParser();
                        const doc = parser.parseFromString(cellData, 'text/html');
                        var targetEl = doc.getElementById('target');
                        var factorEl = doc.getElementById('factor');
                        var operatorEl = doc.getElementById('operator');
                        if (targetEl) target = targetEl.innerText;
                        if (factorEl) factor = factorEl.innerText;
                        if (operatorEl) operator = operatorEl.innerText;
                    } catch(e) {}
                }
                if (col !== 1 && col !== 0) {
                    try {
                        let parser = new DOMParser();
                        const doc = parser.parseFromString(cellData, 'text/html');
                        var numEl = doc.getElementById('num');
                        var denumEl = doc.getElementById('denum');

                        if (numEl && denumEl) {
                            var num = parseInt(numEl.innerText) || 0;
                            var denum = parseInt(denumEl.innerText) || 0;

                            if (num == 0 && denum == 0) {
                                $(td).addClass('cell-empty');
                            } else {
                                var nilai = Math.floor((num / denum) * (factor || 1) || 0);
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
                    } catch(e) {}
                }
            }
        }],
        language: {
            processing: '<div class="spinner-border spinner-border-sm text-warning" role="status"><span class="visually-hidden">Loading...</span></div>',
            emptyTable: 'Tidak ada data',
            info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
            infoEmpty: 'Menampilkan 0 sampai 0 dari 0 data',
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
});

function gantiTahun() {
    vtahun = $('#tahun').val();
    if (table_loquin) {
        // Reset URL tanpa query param, data di-pass via POST body
        table_loquin.ajax.url('<?= site_url('siimut/rekap-laporan-inm/ajax') ?>').load();
    }
}

function reload_table() {
    // Reset ke tahun saat ini
    vtahun = new Date().getFullYear();
    $('#tahun').val(vtahun);
    console.log('reload_table called, resetting to vtahun:', vtahun);
    table_loquin.ajax.reload(null, false); // false = keep current paging
}

function maximizeCard(button) {
    const card = button.closest('.card');
    if (card.classList.contains('card-maximized')) {
        card.classList.remove('card-maximized');
        button.innerHTML = '<i class="fas fa-expand"></i>';
    } else {
        card.classList.add('card-maximized');
        button.innerHTML = '<i class="fas fa-compress"></i>';
    }
}

function view_detail_inm(indicator_id) {
    window.location.href = '<?= site_url('siimut/rekap-laporan-inm?indicator_id=') ?>' + indicator_id + '&tahun=' + vtahun;
}
</script>
