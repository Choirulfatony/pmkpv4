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

    .cell-target *,
    .cell-fail * {
        color: #fff !important;
    }

    .cell-empty * {
        color: #000 !important;
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
        padding: 10px 8px !important;
    }

    #ajax_data_rekap th {
        background-color: #363636 !important;
        color: #fff;
        text-align: center;
        font-weight: 600;
    }

    #ajax_data_rekap td a {
        color: #000;
        text-decoration: none;
        font-weight: 600;
    }

    #ajax_data_rekap td a:hover {
        color: #363636;
        text-decoration: underline;
    }

    [data-bs-theme="dark"] #ajax_data_rekap td,
    [data-bs-theme="dark"] #ajax_data_rekap th {
        color: #fff !important;
    }

    [data-bs-theme="dark"] #ajax_data_rekap td a {
        color: #fff !important;
    }

    [data-bs-theme="dark"] #ajax_data_rekap td a:hover {
        color: #80bdff !important;
    }

    [data-bs-theme="dark"] #ajax_data_rekap td .text-muted {
        color: #adb5bd !important;
    }

    [data-bs-theme="dark"] #ajax_data_rekap td .small {
        color: #ced4da !important;
    }

    [data-bs-theme="dark"] #ajax_data_rekap td span#total {
        color: #fff !important;
    }

    [data-bs-theme="dark"] #ajax_data_rekap td span#num,
    [data-bs-theme="dark"] #ajax_data_rekap td span#denum {
        color: #ced4da !important;
    }

    .dataTables_wrapper .dataTables_processing {
        display: none !important;
    }

    table.dataTable {
        opacity: 1;
        transition: opacity 0.1s ease;
    }

    table.dataTable.loading {
        opacity: 0.2;
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
        z-index: 9999;
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

<div class="row mb-3">
    <div class="col-12">
        <div class="alert alert-light border alert-dismissible fade show" role="alert">
            <div class="d-flex align-items-start">
                <div class="me-3">
                    <i class="fas fa-info-circle fa-2x text-secondary" ></i>
                </div>
                <div class="flex-grow-1">
                    <h5 class="mb-1"><strong>Informasi Rekap Indikator Mutu Prioritas Unit (IMPUnit)</strong></h5>
                    <p class="mb-2">Untuk melihat detail hasil per ruangan, sila klik pada indikator yang diinginkan.</p>
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

<div class="row">
    <div class="col-12">
        <!-- <div class="card card-outline card-warning" id="detail_satu"> -->
            <div class="card card-outline" style="border-top: 3px solid #363636;"  id="detail_satu">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-bar me-2"></i>
                    Rekap IMPUnit per Bulan
                </h3>
                <div class="card-tools d-flex align-items-center gap-2">
                    <div class="input-group input-group-sm" style="width: 130px;">
                        <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                        <select class="form-select form-select-sm" id="tahun" onchange="gantiTahun()">
                            <?php for ($y = date('Y'); $y >= date('Y') - 5; $y--): ?>
                                <option value="<?= $y ?>" <?= $y == $tahun ? 'selected' : '' ?>><?= $y ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-secondary" onclick="reload_table_impunit()" title="Refresh">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                        <a href="#" id="btn-export" class="btn btn-outline-secondary" title="Download Excel">
                            <i class="fas fa-file-excel"></i>
                        </a>
                        <button type="button" class="btn btn-outline-secondary" onclick="maximizeCard(this)" title="Fullscreen">
                            <i class="fas fa-expand"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive p-3">
                    <div class="overlay-wrapper" id="loading_overlay_rekap" style="display: none;">
                        <div class="overlay">
                            <i class="loader"></i>
                        </div>
                    </div>
                    <table id="ajax_data_rekap" class="table table-bordered table-hover table-striped mb-0" style="width: 100%;">
                        <thead>
                            <tr class="align-middle">
                                <th style="width: 50px;" class="text-center">#</th>
                                <th style="min-width: 250px; text-align: left !important; padding-left: 15px !important;">Indikator</th>
                                <th style="min-width: 80px;" class="text-center">Target</th>
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


<script>
    var table_loquin;
    var vtahun = <?= isset($tahun) ? $tahun : "new Date().getFullYear()" ?>;
    var target, factor, operator;

    $(document).ready(function() {
        $('#tahun').val(vtahun);
        $('#btn-export').attr('href', '<?= site_url('siimut/rekap-laporan-impunit/export') ?>?tahun=' + vtahun);

        table_loquin = $('#ajax_data_rekap').DataTable({
            processing: false,
            serverSide: true,
            autoWidth: false,
            pageLength: 10,
            lengthMenu: [
                [10, 25, 50, -1],
                [10, 25, 50, "Semua"]
            ],
            ajax: {
                url: '<?= site_url('siimut/rekap-laporan-impunit/ajax_rekap_impunit') ?>',
                type: 'POST',
                data: function(d) {
                    d.vtahun = vtahun;
                    return d;
                },
                beforeSend: function(xhr) {
                    $('#loading_overlay_rekap').show();
                },
                complete: function() {
                    $('#loading_overlay_rekap').hide();
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
                    if (col == 2) {
                        try {
                            let parser = new DOMParser();
                            const doc = parser.parseFromString(cellData, 'text/html');
                            var targetEl = doc.getElementById('target');
                            var factorEl = doc.getElementById('factor');
                            var operatorEl = doc.getElementById('operator');
                            if (targetEl) target = targetEl.innerText;
                            if (factorEl) factor = factorEl.innerText;
                            if (operatorEl) operator = operatorEl.innerText;
                            $(td).addClass('cell-target');
                        } catch (e) {}
                    }
                    if (col > 2) {
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
                                    var totalEl = doc.getElementById('total');
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
                        } catch (e) {}
                    }
                }
            }],
            language: {
                processing: '<div class="spinner-border spinner-border-sm text-primary" role="status"><span class="visually-hidden">Loading...</span></div>',
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
        $('#btn-export').attr('href', '<?= site_url('siimut/rekap-laporan-impunit/export') ?>?tahun=' + vtahun);
        if (table_loquin) {
            table_loquin.ajax.url('<?= site_url('siimut/rekap-laporan-impunit/ajax_rekap_impunit') ?>').load();
        }
    }

    function reload_table_impunit() {
        vtahun = $('#tahun').val();
        console.log('reload_table_impunit called, vtahun:', vtahun);
        table_loquin.ajax.reload(null, false);
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

    function view_detail_impunit(indicator_id) {
        window.location.href = '<?= site_url('siimut/rekap-laporan-impunit?indicator_id=') ?>' + indicator_id + '&tahun=' + vtahun;
    }
</script>