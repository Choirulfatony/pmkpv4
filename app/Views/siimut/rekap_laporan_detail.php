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
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
</div>

<!-- ==================== CARD DETAIL ==================== -->
<div class="row">
    <div class="col-12">
        <div class="card card-outline card-success">
            <!-- HEADER -->
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-table me-2"></i>
                    Detail Per Ruangan
                </h3>
                <div class="card-tools d-flex align-items-center gap-2">
                    <!-- Tombol Back -->
                    <a href="<?= site_url('siimut/rekap-laporan?tahun=' . $tahun) ?>" class="btn btn-sm btn-outline-secondary">
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
                    </div>
                </div>
            </div>

            <!-- BODY -->
            <div class="card-body p-0">
                <!-- Loading Overlay -->
                <div id="loading_overlay" class="d-none position-absolute w-100 h-100 bg-white bg-opacity-75 d-flex justify-content-center align-items-center" style="z-index: 10; min-height: 200px;">
                    <div class="spinner-border text-success" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>

                <!-- Tabel -->
                <div class="table-responsive p-3">
                    <table id="ajax_detail" class="table table-bordered table-hover table-striped mb-0" style="width: 100%;">
                        <thead>
                            <tr class="align-middle">
                                <th style="width: 50px;" class="text-center">#</th>
                                <th style="min-width: 200px; text-align: left !important; padding-left: 15px !important;">Ruangan</th>
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
</div>

<!-- ==================== SCRIPT ==================== -->
<script>
var table_detail;
var vtahun = '<?= $tahun ?>';
var indicatorId = '<?= $indicatorId ?>';
var target, factor, operator;

$(document).ready(function() {
    // Init DataTable
    table_detail = $('#ajax_detail').DataTable({
        processing: false,
        serverSide: true,
        autoWidth: false,
        pageLength: 25,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Semua"]],
        ajax: {
            url: '<?= site_url('siimut/rekap-laporan/ajax-detail') ?>',
            type: 'POST',
            data: function(d) {
                d.vtahun = vtahun;
                d.indicator_id = indicatorId;
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
                    } catch(e) {}
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
});

function gantiTahun() {
    vtahun = $('#tahun').val();
    if (table_detail) {
        table_detail.ajax.url('<?= site_url('siimut/rekap-laporan/ajax-detail') ?>').load();
    }
}

function reload_table() {
    table_detail.ajax.reload();
}
</script>
