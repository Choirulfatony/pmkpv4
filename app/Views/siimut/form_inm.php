<style>
    .form-inm-header {
        background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
        color: white;
        padding: 20px;
        border-radius: 10px;
        margin-bottom: 20px;
    }

    .card-form-inm {
        border: none;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .card-form-inm .card-header {
        background: var(--bs-tertiary-bg);
        border-bottom: 2px solid #28a745;
        font-weight: bold;
    }

    .btn-inm-primary {
        background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
        border: none;
        color: white;
    }

    .btn-inm-primary:hover {
        background: linear-gradient(135deg, #1e7e34 0%, #145523 100%);
        color: white;
    }

    .form-control:focus, .form-select:focus {
        border-color: #28a745;
        box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
    }

    .modal-header.modal-inm {
        background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
        color: white;
    }

    .modal-inm .btn-close {
        filter: brightness(0) invert(1);
    }

    .table-wrap {
        overflow-x: auto;
        max-width: 100%;
        border: 1px solid #dee2e6;
        border-radius: 8px;
    }

    .table-inm {
        margin-bottom: 0;
        border-collapse: separate;
        border-spacing: 0;
    }

    .table-inm > thead {
        background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
        color: white;
    }

    .table-inm > thead th {
        position: sticky;
        top: 0;
        z-index: 2;
        background: inherit;
        border-color: rgba(255,255,255,0.2);
        font-size: 13px;
        padding: 8px 6px;
        text-align: center;
        white-space: nowrap;
    }

    .table-inm > thead th.fixed-col {
        position: sticky;
        left: 0;
        z-index: 3;
        background: #1e7e34;
    }

    .table-inm > thead th.fixed-col2 {
        position: sticky;
        z-index: 3;
        background: #1e7e34;
    }

    .table-inm tbody td {
        padding: 8px 6px;
        border: 1px solid #dee2e6;
        text-align: center;
        vertical-align: middle;
        font-size: 13px;
    }

    .table-inm tbody td.fixed-col {
        position: sticky;
        left: 0;
        z-index: 1;
        background: white;
    }

    .table-inm tbody td.fixed-col2 {
        position: sticky;
        z-index: 1;
        background: white;
    }

    .day-cell {
        cursor: pointer;
        min-width: 70px;
        transition: all 0.15s ease;
    }

    .day-cell:hover {
        transform: scale(1.05);
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        z-index: 1;
        position: relative;
    }

    .legend-box {
        display: inline-block;
        width: 16px;
        height: 16px;
        border-radius: 3px;
        border: 1px solid #dee2e6;
        vertical-align: middle;
        margin-right: 4px;
    }

    .cell-target {
        background-color: #c3e6cb !important;
    }

    .cell-fail {
        background-color: #f8d7da !important;
    }

    .cell-fail .fw-bold,
    .cell-fail .num-denum {
        color: #dc3545 !important;
    }

    .cell-empty {
        background-color: #e2e3e5 !important;
    }

    td.day-cell.cell-inputable {
        cursor: pointer;
    }
    td.day-cell.cell-inputable .fw-bold {
        color: #0d6efd !important;
    }
    td.day-cell.cell-inputable .num-denum {
        color: #0d6efd !important;
    }

    .cell-has-data {
        font-weight: 600;
    }

    .cell-draft {
        background-color: #fff3cd !important;
    }

    .cell-approved {
        background-color: #d4edda !important;
    }

    .day-cell .num-denum {
        font-size: 11px;
        color: #6c757d;
        margin-top: 2px;
    }

    .day-header {
        font-weight: 600;
        font-size: 13px;
    }

    .table-inm tbody tr.indicator-row:hover td {
        background-color: #e8f4fd;
    }

    .table-inm tbody tr.indicator-row:hover td.fixed-col,
    .table-inm tbody tr.indicator-row:hover td.fixed-col2 {
        background-color: #e8f4fd;
    }

    .dataTables_length label,
    .dataTables_filter label {
        font-weight: normal;
        font-size: 13px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .dataTables_length select {
        width: auto;
        display: inline-block;
    }

    .dataTables_filter input {
        width: auto;
        display: inline-block;
        margin-left: 4px;
    }

    .dataTables_info {
        font-size: 13px;
        padding-top: 4px;
    }

    input[type="month"].form-control-sm {
        min-height: 31px;
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
            box-shadow: 0em -1em rgba(225, 20, 98, 0.75), 0em 1em rgba(111, 202, 220, 0.75);
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
            box-shadow: 1em 0em rgba(61, 184, 143, 0.75), -1em 0em rgba(233, 169, 32, 0.75);
        }
        70% {
            height: 1em;
            box-shadow: 1em -2em rgba(61, 184, 143, 0.75), -1em 2em rgba(233, 169, 32, 0.75);
        }
        100% {
            box-shadow: 1em 2em rgba(61, 184, 143, 0.75), -1em -2em rgba(233, 169, 32, 0.75);
        }
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
</style>

<div class="container-fluid py-4">
    <div class="form-inm-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h4 class="mb-1"><i class="bi bi-pencil-square me-2"></i>Form Input Indikator Nasional Mutu (INM)</h4>
                <p class="mb-0 opacity-75">Input data harian — klik sel pada tanggal untuk mengisi</p>
            </div>
            <div class="col-md-4 text-end">
                <a href="<?= site_url('siimut/grafik-inm') ?>" class="btn btn-light btn-sm">
                    <i class="bi bi-graph-up me-1"></i> Lihat Grafik
                </a>
                <a href="<?= site_url('siimut/rekap-periode-inm') ?>" class="btn btn-light btn-sm">
                    <i class="bi bi-file-earmark-bar-graph me-1"></i> Rekap Periode
                </a>
            </div>
        </div>
    </div>

    <div class="card card-form-inm mb-4">
        <div class="card-header">
            <i class="bi bi-filter me-2"></i>Filter
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Periode (Bulan - Tahun)</label>
                    <div class="input-group" id="periodeGroup">
                        <select class="form-select form-select-sm" id="filter_bulan" style="min-width:110px;">
                            <?php $namaBulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']; ?>
                            <?php for ($m = 1; $m <= 12; $m++): ?>
                                <option value="<?= str_pad($m, 2, '0', STR_PAD_LEFT) ?>" <?= (str_pad($m, 2, '0', STR_PAD_LEFT) == $bulan) ? 'selected' : '' ?>><?= $namaBulan[$m] ?></option>
                            <?php endfor; ?>
                        </select>
                        <select class="form-select form-select-sm" id="filter_tahun" style="min-width:85px;">
                            <?php for ($y = date('Y'); $y >= date('Y') - 5; $y--): ?>
                                <option value="<?= $y ?>" <?= ($y == $tahun) ? 'selected' : '' ?>><?= $y ?></option>
                            <?php endfor; ?>
                        </select>
                        <input type="hidden" id="filter_periode" value="<?= $tahun ?>-<?= $bulan ?>">
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Ruangan</label>
                    <div class="input-group">
                        <select class="form-select form-select-sm" id="filter_department" style="min-width:110px;">
                            <?php if (!empty($showAllOption) && $showAllOption): ?>
                                <option value="">-- Semua Ruangan --</option>
                            <?php endif; ?>
                            <?php foreach ($departments as $dept): ?>
                                <?php if (!empty($userDepartmentId) && $dept['department_id'] == $userDepartmentId): ?>
                                    <option value="<?= $dept['department_id'] ?>" selected><?= esc($dept['department_name']) ?></option>
                                <?php else: ?>
                                    <option value="<?= $dept['department_id'] ?>"><?= esc($dept['department_name']) ?></option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="button" class="btn btn-inm-primary w-100" onclick="loadData()">
                        <i class="bi bi-search me-1"></i> Tampilkan
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div id="loadingIndicator" style="display:none; justify-content:center; align-items:center; min-height:300px; flex-direction:column;">
        <i class="loader" style="display:inline-block; position:relative;"></i>
        <p class="mt-3 text-muted">Memuat data...</p>
    </div>

    <div id="tableContainer" class="d-none">
        <div class="card table-card">
            <div class="card-header">
                <i class="bi bi-list-ul me-2"></i>Daftar Indikator INM
            </div>
            <div class="card-body p-2">
                <div class="row mb-2">
                    <div class="col-sm-12 col-md-6">
                        <div class="dataTables_length">
                            <label>Tampilkan
                                <select name="page_length" class="form-select form-select-sm" id="pageLength" onchange="changePageLength()">
                                    <option value="10">10</option>
                                    <option value="25" selected>25</option>
                                    <option value="50">50</option>
                                    <option value="-1">Semua</option>
                                </select> data
                            </label>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <div class="dataTables_filter text-md-end d-flex align-items-center justify-content-end gap-2">
                            <label class="d-flex align-items-center gap-1 mb-0">Cari:
                                <input type="search" class="form-control form-control-sm" id="searchInput" onkeyup="filterIndicators()">
                            </label>
                            <button class="btn btn-sm btn-outline-secondary" onclick="loadData()" title="Reload Data"><i class="bi bi-arrow-clockwise"></i></button>
                        </div>
                    </div>
                </div>
                <div class="table-wrap">
                    <table class="table table-bordered table-inm" id="mainTable">
                        <thead>
                            <tr id="headerRow"></tr>
                        </thead>
                        <tbody id="tableBody"></tbody>
                    </table>
                </div>
                <div class="row mt-2">
                    <div class="col-sm-12 col-md-6">
                        <div class="dataTables_info" id="tableInfo">Menampilkan 0 / 0 indikator</div>
                    </div>
                    <div class="col-sm-12 col-md-6 text-md-end" id="paginationControls">
                    </div>
                </div>
                <div class="d-flex align-items-center gap-4 mt-2 mb-2 flex-wrap">
                    <span><span class="legend-box" style="background:#fff3cd"></span> Draft (D)</span>
                    <span><span class="legend-box" style="background:#d4edda"></span> Approved (A)</span>
                    <span><span class="legend-box" style="background:#c3e6cb"></span> Target Tercapai</span>
                    <span><span class="legend-box" style="background:#f8d7da"></span> Target Tidak Tercapai</span>
                    <span><span class="legend-box" style="background:#e2e3e5"></span> Belum Ada Data</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Input -->
<div class="modal fade" id="modalInput" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header modal-inm">
                <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Input Data Harian INM</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-success mb-3">
                    <strong id="modalIndikatorNama">-</strong>
                    <div class="mt-2">
                        <span class="badge bg-success me-1">Target: <span id="modalTarget">-</span></span>
                        <span class="badge bg-secondary">Satuan: <span id="modalSatuan">-</span></span>
                    </div>
                </div>

                <!-- ===== DETAIL VIEW (read-only) ===== -->
                <div id="detailView" class="d-none">
                    <table class="table table-bordered table-sm mb-3">
                        <tr>
                            <th class="bg-light" style="width:120px;">Tanggal</th>
                            <td id="detailTanggal">-</td>
                        </tr>
                        <tr>
                            <th class="bg-light">Ruangan</th>
                            <td id="detailRuangan">-</td>
                        </tr>
                        <tr>
                            <th class="bg-light">Numerator</th>
                            <td id="detailNumerator">-</td>
                        </tr>
                        <tr>
                            <th class="bg-light">Denumerator</th>
                            <td id="detailDenumerator">-</td>
                        </tr>
                        <tr>
                            <th class="bg-light">Hasil</th>
                            <td id="detailHasil">-</td>
                        </tr>
                        <tr>
                            <th class="bg-light">Kendala</th>
                            <td id="detailKendala">-</td>
                        </tr>
                        <tr>
                            <th class="bg-light">Perbaikan</th>
                            <td id="detailPerbaikan">-</td>
                        </tr>
                        <tr>
                            <th class="bg-light">Disimpan oleh</th>
                            <td id="detailSavedBy">-</td>
                        </tr>
                        <tr>
                            <th class="bg-light">Waktu simpan</th>
                            <td id="detailSavedAt">-</td>
                        </tr>
                    </table>
                </div>

                <!-- ===== REQUEST APPROVAL (trigger + form) ===== -->
                <div id="requestSection" class="d-none">
                    <hr>
                    <div id="requestStatusAlert"></div>
                    <a href="javascript:void(0)" id="btnRequestTrigger" onclick="toggleRequestForm()">
                        <i class="bi bi-send me-1"></i> Kirim Request Approval
                    </a>
                    <div id="requestForm" class="d-none">
                        <div class="alert alert-warning py-2 mb-2">
                            <i class="bi bi-exclamation-triangle me-1"></i> Data sudah melebihi batas input.
                        </div>
                        <div class="mb-2">
                            <label class="form-label fw-bold small mb-1">Alasan</label>
                            <textarea class="form-control" id="requestReason" rows="2" placeholder="Tulis alasan request..."></textarea>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-outline-warning btn-sm" onclick="submitRequest('edit')">
                                <i class="bi bi-pencil-square me-1"></i> Request Edit
                            </button>
                            <button type="button" class="btn btn-outline-danger btn-sm" onclick="submitRequest('delete')">
                                <i class="bi bi-trash me-1"></i> Request Hapus
                            </button>
                        </div>
                    </div>
                </div>

                <!-- ===== FORM EDIT ===== -->
                <div id="formView">
                    <form id="formInputData" class="needs-validation" novalidate>
                        <input type="hidden" id="input_indicator_id" name="indicator_id">
                        <input type="hidden" id="input_department_id" name="department_id">

                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <label class="form-label fw-bold">Tanggal</label>
                                <input type="text" class="form-control" id="input_tanggal" name="tanggal" readonly>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-bold">Ruangan</label>
                                <input type="text" class="form-control" id="input_department_name" readonly>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <label class="form-label fw-bold">Numerator <span class="text-danger">*</span></label>
                                <div class="input-group has-validation">
                                    <input type="number" class="form-control" id="input_numerator" name="numerator" step="any" min="0" placeholder="Nilai numerator" required>
                                    <span class="input-group-text" id="num_unit">-</span>
                                    <div class="invalid-feedback">Numerator wajib diisi</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-bold">Denumerator <span class="text-danger">*</span></label>
                                <div class="input-group has-validation">
                                    <input type="number" class="form-control" id="input_denumerator" name="denumerator" step="any" min="0" placeholder="Nilai denumerator" required>
                                    <span class="input-group-text" id="denum_unit">-</span>
                                    <div class="invalid-feedback">Denumerator wajib diisi</div>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-12">
                                <label class="form-label fw-bold">Kendala <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="input_kendala" name="kendala" rows="2" placeholder="Kendala yang dihadapi (jika ada)" required></textarea>
                                <div class="invalid-feedback">Kendala wajib diisi</div>
                            </div>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-12">
                                <label class="form-label fw-bold">Perbaikan <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="input_perbaikan" name="perbaikan" rows="2" placeholder="Tindakan perbaikan yang dilakukan" required></textarea>
                                <div class="invalid-feedback">Perbaikan wajib diisi</div>
                            </div>
                        </div>

                        <div class="alert alert-secondary" id="hasilHitung">
                            <div class="d-flex justify-content-between align-items-center">
                                <span>Hasil:</span>
                                <strong id="hasilPersen">-</strong>
                            </div>
                        </div>
                        <div class="alert alert-danger d-none py-1 px-2 mb-2" id="warningNumerator"></div>
                    </form>
                </div>
            </div>
            <div class="modal-footer" id="modalFooter">
                <!-- Mode Detail (ada data) -->
                <span id="footerDetail">
                    <button type="button" class="btn btn-outline-danger" id="btnDelete" onclick="deleteData()">
                        <i class="bi bi-trash me-1"></i> Hapus
                    </button>
                    <button type="button" class="btn btn-primary" id="btnEdit" onclick="showEditMode()">
                        <i class="bi bi-pencil me-1"></i> Edit
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i> Tutup
                    </button>
                </span>
                <!-- Mode Form (input/edit) -->
                <span id="footerForm">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i> Batal
                    </button>
                    <button type="button" class="btn btn-inm-primary" id="btnSave" onclick="saveData()">
                        <i class="bi bi-save me-1"></i> Simpan
                    </button>
                </span>
            </div>
        </div>
    </div>
</div>


<script>
    var currentUserId = <?= (int) ($profileId ?? 0) ?>;
    var modalInput = null;

    document.addEventListener('DOMContentLoaded', function() {
        modalInput = new bootstrap.Modal(document.getElementById('modalInput'));

        document.getElementById('input_numerator').addEventListener('input', hitungHasil);
        document.getElementById('input_denumerator').addEventListener('input', hitungHasil);

        document.getElementById('input_numerator').addEventListener('keydown', angkaOnly);
        document.getElementById('input_denumerator').addEventListener('keydown', angkaOnly);

        document.getElementById('filter_department').addEventListener('change', loadData);
        document.getElementById('filter_bulan').addEventListener('change', updatePeriode);
        document.getElementById('filter_tahun').addEventListener('change', updatePeriode);

        loadData();
    });

    function updatePeriode() {
        var tahun = document.getElementById('filter_tahun').value;
        var bulan = document.getElementById('filter_bulan').value;
        document.getElementById('filter_periode').value = tahun + '-' + bulan;
        loadData();
    }

    function loadData() {
        var periode = document.getElementById('filter_periode').value;
        var tahun = periode ? periode.substring(0, 4) : '';
        var bulan = periode ? periode.substring(5, 7) : '';
        var department_id = document.getElementById('filter_department').value;

        if (!tahun || !bulan) return;

        document.getElementById('loadingIndicator').style.display = 'flex';
        document.getElementById('tableContainer').classList.add('d-none');

        var xhr = new XMLHttpRequest();
        xhr.open('POST', '<?= site_url('siimut/load-module-forminput/get-indicators') ?>', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function() {
            document.getElementById('loadingIndicator').style.display = 'none';
            if (xhr.status === 200) {
                try {
                    var response = JSON.parse(xhr.responseText);
                    renderTable(response);
                } catch(e) {
                    toastError('Gagal memproses data: ' + e.message);
                }
            } else {
                toastError('HTTP error: ' + xhr.status);
            }
        };
        xhr.onerror = function() {
            document.getElementById('loadingIndicator').style.display = 'none';
            toastError('Gagal memuat data');
        };
        xhr.send('tahun=' + tahun + '&bulan=' + bulan + '&department_id=' + department_id);
    }

    var _allData = [];
    var _currentPage = 1;

    function renderTable(response) {
        var data = response.indicators;
        var days = response.days || 31;

        if (!data || data.length === 0) {
            document.getElementById('tableContainer').classList.add('d-none');
            return;
        }

        _allData = data;
        document.getElementById('tableInfo').textContent = 'Menampilkan ' + data.length + ' / ' + data.length + ' indikator';

        var headerHtml = '<th class="text-center" style="width:40px;">No</th>' +
            '<th style="min-width:250px;">Indikator</th>' +
            '<th style="width:80px;" class="text-center">Target</th>';
        for (var d = 1; d <= days; d++) {
            headerHtml += '<th class="text-center" style="width:70px;">' + d + '</th>';
        }
        document.getElementById('headerRow').innerHTML = headerHtml;

        document.getElementById('tableBody').innerHTML = buildRows(data, days);
        document.getElementById('tableContainer').classList.remove('d-none');
        document.getElementById('searchInput').value = '';
        applyPagination();
    }

    function buildRows(data, days) {
        var html = '';
        var periode = document.getElementById('filter_periode').value;
        var tahun = parseInt(periode.substring(0, 4));
        var bulan = parseInt(periode.substring(5, 7));
        var today = new Date();
        today.setHours(0, 0, 0, 0);

        function isInputable(day, freq) {
            if (freq === 'W' || freq === 'M' || freq === 'Y') return true;
            var tglDate = new Date(tahun, bulan - 1, day);
            tglDate.setHours(0, 0, 0, 0);
            var diff = Math.round((today - tglDate) / (1000 * 60 * 60 * 24));
            return diff >= 0 && diff <= 30;
        }

        for (var i = 0; i < data.length; i++) {
            var row = data[i];
            var daily = row.daily || [];
            var freq = row.indicator_frequency || 'D';
            html += '<tr class="indicator-row">';
            html += '<td class="text-center fw-bold">' + (i + 1) + '</td>';
            html += '<td class="text-start">' + escHtml(row.indicator_element) + ' <span class="badge bg-secondary ms-1">' + freq + '</span></td>';
            html += '<td class="text-center">' + escHtml(row.indicator_target) + ' ' + escHtml(row.indicator_units) + '</td>';

            if (freq === 'M' || freq === 'Y') {
                var item = daily[0] || { nilai: null, num: 0, denum: 0, status: '', hari: 1 };
                var cellClass = 'day-cell text-center';
                var nilaiDisplay = '-';

                if (item.nilai !== null) {
                    nilaiDisplay = Number(item.nilai).toFixed(2) + ' ' + escHtml(row.indicator_units);
                    cellClass += ' cell-has-data';
                    if (item.status === 'A') cellClass += ' cell-approved';
                    else if (item.status === 'D') cellClass += ' cell-draft';
                    if (item.tercapai === true) cellClass += ' cell-target';
                    else if (item.tercapai === false) cellClass += ' cell-fail';
                } else {
                    cellClass += (item.num > 0 || item.denum > 0) ? ' cell-fail' : ' cell-empty';
                }

                if (isInputable(item.hari, freq)) {
                    cellClass += ' cell-inputable';
                }

                html += '<td class="' + cellClass + '" colspan="' + days + '" ' +
                    'onclick="openModal(' + row.indicator_id + ', ' + row.department_id + ', \'' + escJs(row.department_name) + '\', ' + item.hari + ', \'' + escJs(row.indicator_element) + '\', \'' + escJs(row.indicator_target) + '\', \'' + escJs(row.indicator_units) + '\', \'' + escJs(row.indicator_target_unit || '') + '\', \'' + freq + '\')">' +
                    '<div class="fw-bold">' + nilaiDisplay + '</div>' +
                    '<div class="num-denum">' + (item.num || 0) + ' / ' + (item.denum || 0) + '</div></td>';
            } else if (freq === 'W') {
                for (var w = 0; w < daily.length; w++) {
                    var item = daily[w];
                    var cellClass = 'day-cell text-center';
                    var nilaiDisplay = '-';
                    var colspan = item.colspan || 7;

                    if (item.nilai !== null) {
                        nilaiDisplay = Number(item.nilai).toFixed(2) + ' ' + escHtml(row.indicator_units);
                        cellClass += ' cell-has-data';
                        if (item.status === 'A') cellClass += ' cell-approved';
                        else if (item.status === 'D') cellClass += ' cell-draft';
                        if (item.tercapai === true) cellClass += ' cell-target';
                        else if (item.tercapai === false) cellClass += ' cell-fail';
                    } else {
                        cellClass += (item.num > 0 || item.denum > 0) ? ' cell-fail' : ' cell-empty';
                    }

                    if (isInputable(item.hari, freq)) {
                        cellClass += ' cell-inputable';
                    }

                    var weekLabel = 'Mg ' + (item.week || (w + 1));
                    html += '<td class="' + cellClass + '" colspan="' + colspan + '" ' +
                        'onclick="openModal(' + row.indicator_id + ', ' + row.department_id + ', \'' + escJs(row.department_name) + '\', ' + item.hari + ', \'' + escJs(row.indicator_element) + '\', \'' + escJs(row.indicator_target) + '\', \'' + escJs(row.indicator_units) + '\', \'' + escJs(row.indicator_target_unit || '') + '\', \'' + freq + '\')">' +
                        '<div class="fw-bold">' + weekLabel + ': ' + nilaiDisplay + '</div>' +
                        '<div class="num-denum">' + (item.num || 0) + ' / ' + (item.denum || 0) + '</div></td>';
                }
            } else {
                for (var d = 0; d < daily.length; d++) {
                    var item = daily[d];
                    var cellClass = 'day-cell text-center';
                    var nilaiDisplay = '-';

                    if (item.nilai !== null) {
                        nilaiDisplay = Number(item.nilai).toFixed(2) + ' ' + escHtml(row.indicator_units);
                        cellClass += ' cell-has-data';
                        if (item.status === 'A') cellClass += ' cell-approved';
                        else if (item.status === 'D') cellClass += ' cell-draft';
                        if (item.tercapai === true) cellClass += ' cell-target';
                        else if (item.tercapai === false) cellClass += ' cell-fail';
                    } else {
                        cellClass += (item.num > 0 || item.denum > 0) ? ' cell-fail' : ' cell-empty';
                    }

                    if (isInputable(item.hari, freq)) {
                        cellClass += ' cell-inputable';
                    }

                    html += '<td class="' + cellClass + '" ' +
                        'onclick="openModal(' + row.indicator_id + ', ' + row.department_id + ', \'' + escJs(row.department_name) + '\', ' + item.hari + ', \'' + escJs(row.indicator_element) + '\', \'' + escJs(row.indicator_target) + '\', \'' + escJs(row.indicator_units) + '\', \'' + escJs(row.indicator_target_unit || '') + '\', \'' + freq + '\')">' +
                        '<div class="fw-bold">' + nilaiDisplay + '</div>' +
                        '<div class="num-denum">' + (item.num || 0) + ' / ' + (item.denum || 0) + '</div></td>';
                }
            }
            html += '</tr>';
        }
        return html;
    }

    function applyPagination() {
        var keyword = document.getElementById('searchInput').value.toLowerCase();
        var limit = parseInt(document.getElementById('pageLength').value);
        var rows = document.querySelectorAll('#tableBody .indicator-row');
        var filtered = [];

        rows.forEach(function(row, idx) {
            var text = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
            var match = text.indexOf(keyword) !== -1;
            row.dataset.filtered = match ? '1' : '0';
            if (match) filtered.push(idx);
        });

        var totalFiltered = filtered.length;
        var totalAll = _allData.length;
        document.getElementById('tableInfo').textContent = 'Menampilkan ' + totalFiltered + ' / ' + totalAll + ' indikator';

        var show = (limit === -1) ? totalFiltered : limit;
        var totalPages = (limit === -1) ? 1 : Math.ceil(totalFiltered / limit);

        if (_currentPage > totalPages) _currentPage = totalPages;
        if (_currentPage < 1) _currentPage = 1;

        var start = (_currentPage - 1) * show;
        var end = start + show;

        rows.forEach(function(row, idx) {
            if (row.dataset.filtered === '0') {
                row.style.display = 'none';
            } else {
                var pos = filtered.indexOf(idx);
                row.style.display = (pos >= start && pos < end) ? '' : 'none';
            }
        });

        // Render pagination buttons
        var controls = document.getElementById('paginationControls');
        var html = '';
        if (totalPages > 1) {
            html += '<button class="btn btn-sm btn-outline-secondary me-1" onclick="changePage(-1)" ' + (_currentPage <= 1 ? 'disabled' : '') + '>Sebelumnya</button>';
            html += '<span class="mx-2 fw-semibold">' + _currentPage + ' / ' + totalPages + '</span>';
            html += '<button class="btn btn-sm btn-outline-secondary" onclick="changePage(1)" ' + (_currentPage >= totalPages ? 'disabled' : '') + '>Berikutnya</button>';
        }
        controls.innerHTML = html;
    }

    function changePage(delta) {
        _currentPage += delta;
        applyPagination();
    }

    function changePageLength() {
        _currentPage = 1;
        applyPagination();
    }

    function filterIndicators() {
        _currentPage = 1;
        applyPagination();
    }

    function openModal(indicatorId, departmentId, departmentName, hari, indicatorName, target, units, targetUnit, freq) {
        var periode = document.getElementById('filter_periode').value;
        var tahun = periode.substring(0, 4);
        var bulan = periode.substring(5, 7);
        var tanggal = tahun + '-' + bulan + '-' + String(hari).padStart(2, '0');

        // Validasi tanggal
        var today = new Date();
        var tglDate = new Date(parseInt(tahun), parseInt(bulan) - 1, hari);
        today.setHours(0, 0, 0, 0);
        tglDate.setHours(0, 0, 0, 0);
        var diffDays = Math.round((today - tglDate) / (1000 * 60 * 60 * 24));

        // Masa depan
        if (diffDays < 0) {
            toastError('Tidak bisa input untuk tanggal yang akan datang');
            return;
        }

        // W/M/Y: selalu via AJAX check (skip 30 hari shortcut)
        if (freq === 'W' || freq === 'M' || freq === 'Y') {
            var xhr = new XMLHttpRequest();
            xhr.open('POST', '<?= site_url('siimut/load-module-forminput/check-input-allowed') ?>', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    var resp = JSON.parse(xhr.responseText);
                    if (resp.allowed) {
                        _openModalContinue(indicatorId, departmentId, departmentName, tanggal, indicatorName, target, units, targetUnit, resp.restricted, resp.approved_action || null);
                    } else {
                        toastError(resp.message || 'Tidak bisa input untuk tanggal ini');
                    }
                }
            };
            xhr.send('indicator_id=' + indicatorId + '&department_id=' + departmentId + '&tanggal=' + tanggal);
            return;
        }

        // Dalam 30 hari
        if (diffDays <= 30) {
            _openModalContinue(indicatorId, departmentId, departmentName, tanggal, indicatorName, target, units, targetUnit, false, null);
            return;
        }

        // Lebih dari 30 hari → cek group_days
        var xhr = new XMLHttpRequest();
        xhr.open('POST', '<?= site_url('siimut/load-module-forminput/check-input-allowed') ?>', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                var resp = JSON.parse(xhr.responseText);
                if (resp.allowed) {
                    _openModalContinue(indicatorId, departmentId, departmentName, tanggal, indicatorName, target, units, targetUnit, resp.restricted, resp.approved_action || null);
                } else {
                    toastError(resp.message || 'Tidak bisa input untuk tanggal ini');
                }
            }
        };
        xhr.send('indicator_id=' + indicatorId + '&department_id=' + departmentId + '&tanggal=' + tanggal);
    }

    function _openModalContinue(indicatorId, departmentId, departmentName, tanggal, indicatorName, target, units, targetUnit, restricted, approvedAction) {
        document.getElementById('input_indicator_id').value = indicatorId;
        document.getElementById('input_department_id').value = departmentId;
        document.getElementById('input_department_name').value = departmentName;
        document.getElementById('input_tanggal').value = tanggal;

        document.getElementById('modalIndikatorNama').textContent = indicatorName || '-';
        document.getElementById('modalTarget').textContent = target || '-';
        document.getElementById('modalSatuan').textContent = units || '-';
        document.getElementById('num_unit').textContent = units || '-';
        document.getElementById('denum_unit').textContent = targetUnit || '-';

        showFormMode();

        var xhr = new XMLHttpRequest();
        xhr.open('POST', '<?= site_url('siimut/load-module-forminput/get-indicator-detail') ?>', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                var response = JSON.parse(xhr.responseText);
                var hasData = response.existing_data && response.existing_data.length > 0;

                document.getElementById('requestSection').classList.add('d-none');
                document.getElementById('requestForm').classList.add('d-none');
                document.getElementById('requestReason').value = '';
                document.getElementById('requestStatusAlert').innerHTML = '';
                document.getElementById('btnRequestTrigger').classList.remove('d-none');

                if (hasData) {
                    var last = response.existing_data[response.existing_data.length - 1];
                    var num = last.result_numerator_value || '0';
                    var den = last.result_denumerator_value || '0';
                    var kendala = (response.rencana_perbaikan && response.rencana_perbaikan.kendala) || '-';
                    var perbaikan = (response.rencana_perbaikan && response.rencana_perbaikan.perbaikan) || '-';
                    var faktor = parseFloat(target) > 0 ? 1 : 1;
                    var hasil = parseFloat(den) > 0 ? (parseFloat(num) / parseFloat(den) * 100).toFixed(2) + '%' : '-';

                    document.getElementById('detailTanggal').textContent = tanggal;
                    document.getElementById('detailRuangan').textContent = departmentName;
                    document.getElementById('detailNumerator').textContent = num;
                    document.getElementById('detailDenumerator').textContent = den;
                    document.getElementById('detailHasil').textContent = hasil;
                    document.getElementById('detailKendala').textContent = kendala;
                    document.getElementById('detailPerbaikan').textContent = perbaikan;
                    document.getElementById('detailSavedBy').textContent = last.profile_fullname || last.result_insert_by || '-';
                    document.getElementById('detailSavedAt').textContent = last.result_insert_date || '-';

                    document.getElementById('input_numerator').value = num;
                    document.getElementById('input_denumerator').value = den;
                    if (response.rencana_perbaikan) {
                        document.getElementById('input_kendala').value = response.rencana_perbaikan.kendala || '';
                        document.getElementById('input_perbaikan').value = response.rencana_perbaikan.perbaikan || '';
                    } else {
                        document.getElementById('input_kendala').value = '';
                        document.getElementById('input_perbaikan').value = '';
                    }

                    if (restricted) {
                        document.getElementById('btnEdit').style.display = 'none';
                        document.getElementById('btnDelete').style.display = 'none';
                        document.getElementById('requestSection').classList.remove('d-none');
                        showRequestStatus(response.request_status);
                    } else {
                        document.getElementById('btnEdit').style.display = (approvedAction && approvedAction !== 'edit') ? 'none' : '';
                        document.getElementById('btnDelete').style.display = (approvedAction && approvedAction !== 'delete') ? 'none' : '';
                        document.getElementById('requestSection').classList.add('d-none');
                    }

                    showDetailMode();
                } else {
                    document.getElementById('input_numerator').value = '';
                    document.getElementById('input_denumerator').value = '';
                    document.getElementById('input_kendala').value = '';
                    document.getElementById('input_perbaikan').value = '';
                    showFormMode();
                }
                hitungHasil();
                modalInput.show();
            }
        };
        xhr.send('indicator_id=' + indicatorId + '&department_id=' + departmentId + '&tanggal=' + tanggal);
    }

    function showDetailMode() {
        document.getElementById('detailView').classList.remove('d-none');
        document.getElementById('formView').classList.add('d-none');
        document.getElementById('footerDetail').classList.remove('d-none');
        document.getElementById('footerForm').classList.add('d-none');
    }

    function showFormMode() {
        document.getElementById('detailView').classList.add('d-none');
        document.getElementById('formView').classList.remove('d-none');
        document.getElementById('requestSection').classList.add('d-none');
        document.getElementById('footerDetail').classList.add('d-none');
        document.getElementById('footerForm').classList.remove('d-none');
    }

    function showEditMode() {
        showFormMode();
    }

    function angkaOnly(e) {
        if (e.key === '.' || e.key === ',' || e.key === 'Backspace' || e.key === 'Delete' || e.key === 'Tab' || e.key === 'ArrowLeft' || e.key === 'ArrowRight') return;
        if (e.ctrlKey || e.metaKey) return;
        if (!/^[0-9]$/.test(e.key)) e.preventDefault();
    }

    function hitungHasil() {
        var num = parseFloat(document.getElementById('input_numerator').value) || 0;
        var denum = parseFloat(document.getElementById('input_denumerator').value) || 0;
        var hasil = document.getElementById('hasilPersen');
        var warning = document.getElementById('warningNumerator');
        if (denum > 0) {
            hasil.textContent = num + ' / ' + denum + ' = ' + (num / denum * 100).toFixed(2) + '%';
        } else {
            hasil.textContent = num > 0 ? num + ' / 0 = ~' : '-';
        }
        var satuan = (document.getElementById('modalSatuan').textContent || '').trim();
        var numInput = document.getElementById('input_numerator');
        var denInput = document.getElementById('input_denumerator');
        var numFeedback = numInput.parentElement.querySelector('.invalid-feedback');
        var denFeedback = denInput.parentElement.querySelector('.invalid-feedback');
        var btnSave = document.getElementById('btnSave');
        if ((satuan === '%' || satuan.toLowerCase().includes('persen')) && denum > 0 && num > denum) {
            warning.textContent = 'Numerator tidak boleh melebihi Denumerator untuk satuan ' + satuan;
            warning.classList.remove('d-none');
            numInput.classList.add('is-invalid');
            denInput.classList.add('is-invalid');
            numFeedback.textContent = 'Numerator tidak boleh melebihi Denumerator';
            denFeedback.textContent = 'Denumerator harus lebih besar dari Numerator';
            btnSave.disabled = true;
        } else {
            warning.classList.add('d-none');
            numInput.classList.remove('is-invalid');
            denInput.classList.remove('is-invalid');
            numFeedback.textContent = 'Numerator wajib diisi';
            denFeedback.textContent = 'Denumerator wajib diisi';
            btnSave.disabled = false;
        }
    }

    function saveData() {
        var form = document.getElementById('formInputData');

        if (!form.checkValidity()) {
            form.classList.add('was-validated');
            return;
        }

        var num = parseFloat(document.getElementById('input_numerator').value) || 0;
        var den = parseFloat(document.getElementById('input_denumerator').value) || 0;
        var satuan = (document.getElementById('modalSatuan').textContent || '').trim();
        if ((satuan === '%' || satuan.toLowerCase().includes('persen')) && num > den) {
            toastError('Numerator tidak boleh melebihi Denumerator untuk satuan ' + satuan);
            return;
        }

        var data = new FormData(form);

        var xhr = new XMLHttpRequest();
        xhr.open('POST', '<?= site_url('siimut/load-module-forminput/save') ?>', true);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                form.classList.remove('was-validated');
                var response = JSON.parse(xhr.responseText);
                if (response.status) {
                    toastSuccess('Data berhasil disimpan');
                    modalInput.hide();
                    loadData();
                } else {
                    toastError('Gagal: ' + (response.message || 'Unknown error'));
                }
            }
        };
        xhr.send(data);
    }

    function deleteData() {
        var indicator_id = document.getElementById('input_indicator_id').value;
        var department_id = document.getElementById('input_department_id').value;
        var tanggal = document.getElementById('input_tanggal').value;

        Swal.fire({
            title: 'Hapus data?',
            text: 'Data pada tanggal ' + tanggal + ' akan dihapus',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then(function(result) {
            if (!result.isConfirmed) return;

            var xhr = new XMLHttpRequest();
            xhr.open('POST', '<?= site_url('siimut/load-module-forminput/delete') ?>', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    var response = JSON.parse(xhr.responseText);
                    if (response.status) {
                        toastSuccess('Data berhasil dihapus');
                        modalInput.hide();
                        loadData();
                    } else {
                        toastError('Gagal: ' + (response.message || 'Unknown error'));
                    }
                }
            };
            xhr.send('indicator_id=' + indicator_id + '&department_id=' + department_id + '&tanggal=' + tanggal);
        });
    }

    function validateData() {
        var indicator_id = document.getElementById('input_indicator_id').value;
        var department_id = document.getElementById('input_department_id').value;
        var tanggal = document.getElementById('input_tanggal').value;

        Swal.fire({
            title: 'Validasi data?',
            text: 'Data pada tanggal ' + tanggal + ' akan divalidasi (Approved)',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, validasi!',
            cancelButtonText: 'Batal'
        }).then(function(result) {
            if (!result.isConfirmed) return;

            var xhr = new XMLHttpRequest();
            xhr.open('POST', '<?= site_url('siimut/load-module-forminput/validasi') ?>', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    var response = JSON.parse(xhr.responseText);
                    if (response.status) {
                        toastSuccess('Data berhasil divalidasi');
                        modalInput.hide();
                        loadData();
                    } else {
                        toastError('Gagal: ' + (response.message || 'Unknown error'));
                    }
                }
            };
            xhr.send('indicator_id=' + indicator_id + '&department_id=' + department_id + '&tanggal=' + tanggal);
        });
    }

    function toggleRequestForm() {
        document.getElementById('btnRequestTrigger').classList.add('d-none');
        document.getElementById('requestForm').classList.remove('d-none');
    }

    function showRequestStatus(data) {
        var alertBox = document.getElementById('requestStatusAlert');
        if (!data || !data.ar_status) return;
        if (data.ar_status === 'pending') {
            alertBox.innerHTML = '<div class="alert alert-info py-1 px-2 small mb-2"><i class="bi bi-clock me-1"></i> Request menunggu persetujuan admin</div>';
            document.getElementById('btnRequestTrigger').classList.add('d-none');
        } else if (data.ar_status === 'approved') {
            alertBox.innerHTML = '<div class="alert alert-success py-1 px-2 small mb-2"><i class="bi bi-check-circle me-1"></i> Disetujui oleh <strong>' + (data.approve_by_name || '-') + '</strong> pada ' + (data.ar_approve_date || '-') + '</div>';
            document.getElementById('btnRequestTrigger').classList.add('d-none');
        } else if (data.ar_status === 'rejected') {
            alertBox.innerHTML = '<div class="alert alert-danger py-1 px-2 small mb-2"><i class="bi bi-x-circle me-1"></i> Ditolak oleh <strong>' + (data.approve_by_name || '-') + '</strong> pada ' + (data.ar_approve_date || '-') + '<br><em class="small">Alasan: ' + (data.ar_notes || '-') + '</em></div>';
        } else if (data.ar_status === 'completed') {
            alertBox.innerHTML = '<div class="alert alert-secondary py-1 px-2 small mb-2"><i class="bi bi-check2-all me-1"></i> Request telah digunakan</div>';
        }
    }

    function submitRequest(action) {
        var indicator_id = document.getElementById('input_indicator_id').value;
        var department_id = document.getElementById('input_department_id').value;
        var tanggal = document.getElementById('input_tanggal').value;
        var reason = document.getElementById('requestReason').value.trim();

        if (!reason) {
            toastError('Alasan harus diisi');
            return;
        }

        var actionText = (action === 'edit') ? 'mengedit' : 'menghapus';

        Swal.fire({
            title: 'Kirim Request ' + (action === 'edit' ? 'Edit' : 'Hapus') + '?',
            text: 'Data pada tanggal ' + tanggal + ' akan di-' + actionText,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, kirim',
            cancelButtonText: 'Batal'
        }).then(function(result) {
            if (!result.isConfirmed) return;

            var xhr = new XMLHttpRequest();
            xhr.open('POST', '<?= site_url('siimut/load-module-forminput/request-approval') ?>', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    var resp = JSON.parse(xhr.responseText);
                    if (resp.status) {
                        toastSuccess(resp.message);
                        modalInput.hide();
                    } else {
                        toastError(resp.message);
                    }
                }
            };
            xhr.send('indicator_id=' + indicator_id + '&department_id=' + department_id + '&tanggal=' + tanggal + '&reason=' + encodeURIComponent(reason) + '&action_type=' + action);
        });
    }

    function toastSuccess(msg) {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            iconColor: '#5cb85c',
            title: msg,
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
    }

    function toastError(msg) {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'error',
            iconColor: '#d9534f',
            title: msg,
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
    }

    function escHtml(str) {
        if (!str) return '';
        var div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    function escJs(str) {
        if (!str) return '';
        return String(str).replace(/\\/g, '\\\\').replace(/'/g, "\\'").replace(/"/g, "\\\"");
    }
</script>
