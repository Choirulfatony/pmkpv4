
<style>
    td.day-cell.cell-disabled { opacity: 0.5; cursor: default; }
    td.day-cell.cell-disabled * { pointer-events: none; }
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
                <button type="button" class="btn btn-light btn-sm float-end" onclick="openRequestBukaPeriodeModal()">
                    <i class="bi bi-unlock me-1"></i> Minta Buka Periode
                </button>
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
                            <button class="btn btn-sm btn-outline-info" onclick="openHistoryModal()" title="Riwayat Permintaan"><i class="bi bi-clock-history"></i></button>
                        </div>
                    </div>
                </div>
                <div class="table-wrap">
                    <table class="table table-bordered table-inm-inm" id="mainTable">
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
                    <span style="color:#0d6efd;font-weight:600;"><i class="bi bi-pencil-square"></i> Dapat Diinput</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Minta Buka Periode -->
<div class="modal fade" id="modalBukaPeriode" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title"><i class="bi bi-unlock me-1"></i> Minta Buka Periode</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-2">
                    <label class="form-label fw-bold small mb-1">Indikator</label>
                    <select class="form-select form-select-sm" id="bp-indicator">
                        <option value="">-- Pilih Indikator --</option>
                    </select>
                </div>
                <div class="mb-2">
                    <label class="form-label fw-bold small mb-1">Periode Mulai</label>
                    <input type="date" class="form-control form-control-sm" id="bp-period-start">
                </div>
                <div class="mb-2">
                    <label class="form-label fw-bold small mb-1">Periode Selesai</label>
                    <input type="date" class="form-control form-control-sm" id="bp-period-end">
                </div>
                <div class="mb-2">
                    <label class="form-label fw-bold small mb-1">Alasan</label>
                    <textarea class="form-control" id="bp-reason" rows="3" placeholder="Tulis alasan permintaan buka periode..."></textarea>
                </div>
            </div>
            <div class="modal-footer d-flex flex-wrap gap-1">
                <small class="text-muted"><i class="bi bi-info-circle"></i> Hari = Tgl Selesai &minus; Tgl Mulai</small>
                <div class="d-flex gap-1 ms-auto">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-sm btn-primary" onclick="submitBukaPeriode()"><i class="bi bi-send me-1"></i> Kirim</button>
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


<!-- Modal Riwayat Permintaan -->
<div class="modal fade" id="modalHistory" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title"><i class="bi bi-clock-history me-1"></i> Riwayat Permintaan</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="historyModalBody">
                <div class="text-center py-3 text-muted">Memuat data...</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal View Detail Permintaan -->
<div class="modal fade" id="modalViewRequest" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title"><i class="bi bi-info-circle me-1"></i> Detail Permintaan</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <table class="table table-sm table-borderless mb-0">
                    <tr><td class="text-muted small" style="width:100px;">ID</td><td id="view-request-id">-</td></tr>
                    <tr><td class="text-muted small">Tgl Request</td><td id="view-request-date">-</td></tr>
                    <tr><td class="text-muted small">Periode Mulai</td><td id="view-period-start">-</td></tr>
                    <tr><td class="text-muted small">Periode Selesai</td><td id="view-period-end">-</td></tr>
                    <tr><td class="text-muted small">Alasan</td><td id="view-reason">-</td></tr>
                    <tr><td class="text-muted small">Status</td><td id="view-status">-</td></tr>
                    <tr><td class="text-muted small">Disetujui Oleh</td><td id="view-approved-by">-</td></tr>
                    <tr><td class="text-muted small">Tgl Proses</td><td id="view-approved-date">-</td></tr>
                    <tr><td class="text-muted small">Catatan</td><td id="view-notes">-</td></tr>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>


<script>
    var currentUserId = <?= (int) ($profileId ?? 0) ?>;
    var modalInput = null;
    var catId = '4';
    var groupTypeMap = {4:1, 5:5, 6:6, 7:7};

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

        function isInputable(day, freq, maxDays) {
            if (freq === 'W' || freq === 'M' || freq === 'Y') return true;
            var tglDate = new Date(tahun, bulan - 1, day);
            tglDate.setHours(0, 0, 0, 0);
            var diff = Math.round((today - tglDate) / (1000 * 60 * 60 * 24));
            var limit = (maxDays && maxDays > 0) ? maxDays : 30;
            return diff >= 0 && diff <= limit;
        }

        for (var i = 0; i < data.length; i++) {
            var row = data[i];
            var daily = row.daily || [];
            var freq = row.indicator_frequency || 'D';
            var isInactive = row.indicator_record_status === 'D';
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

                if (!isInactive && isInputable(item.hari, freq, row.group_days)) {
                    cellClass += ' cell-inputable';
                }
                if (isInactive) {
                    cellClass += ' cell-disabled';
                }

                html += '<td class="' + cellClass + '" colspan="' + days + '"';
                if (!isInactive) {
                    html += ' onclick="openModal(' + row.indicator_id + ', ' + row.department_id + ', \'' + escJs(row.department_name) + '\', ' + item.hari + ', \'' + escJs(row.indicator_element) + '\', \'' + escJs(row.indicator_target) + '\', \'' + escJs(row.indicator_units) + '\', \'' + escJs(row.indicator_target_unit || '') + '\', \'' + freq + '\', \'' + escJs(row.num_unit || '') + '\', \'' + escJs(row.den_unit || '') + '\')"';
                }
                html += '>' +
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

                    if (!isInactive && isInputable(item.hari, freq, row.group_days)) {
                        cellClass += ' cell-inputable';
                    }
                    if (isInactive) {
                        cellClass += ' cell-disabled';
                    }

                    var weekLabel = 'Mg ' + (item.week || (w + 1));
                    html += '<td class="' + cellClass + '" colspan="' + colspan + '"';
                    if (!isInactive) {
                        html += ' onclick="openModal(' + row.indicator_id + ', ' + row.department_id + ', \'' + escJs(row.department_name) + '\', ' + item.hari + ', \'' + escJs(row.indicator_element) + '\', \'' + escJs(row.indicator_target) + '\', \'' + escJs(row.indicator_units) + '\', \'' + escJs(row.indicator_target_unit || '') + '\', \'' + freq + '\', \'' + escJs(row.num_unit || '') + '\', \'' + escJs(row.den_unit || '') + '\')"';
                    }
                    html += '>' +
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

                    if (!isInactive && isInputable(item.hari, freq, row.group_days)) {
                        cellClass += ' cell-inputable';
                    }
                    if (isInactive) {
                        cellClass += ' cell-disabled';
                    }

                    html += '<td class="' + cellClass + '"';
                    if (!isInactive) {
                        html += ' onclick="openModal(' + row.indicator_id + ', ' + row.department_id + ', \'' + escJs(row.department_name) + '\', ' + item.hari + ', \'' + escJs(row.indicator_element) + '\', \'' + escJs(row.indicator_target) + '\', \'' + escJs(row.indicator_units) + '\', \'' + escJs(row.indicator_target_unit || '') + '\', \'' + freq + '\', \'' + escJs(row.num_unit || '') + '\', \'' + escJs(row.den_unit || '') + '\')"';
                    }
                    html += '>' +
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

    function openModal(indicatorId, departmentId, departmentName, hari, indicatorName, target, units, targetUnit, freq, numUnit, denUnit) {
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
                        _openModalContinue(indicatorId, departmentId, departmentName, tanggal, indicatorName, target, units, targetUnit, resp.restricted, resp.approved_action || null, numUnit, denUnit);
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
            _openModalContinue(indicatorId, departmentId, departmentName, tanggal, indicatorName, target, units, targetUnit, false, null, numUnit, denUnit);
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
                    _openModalContinue(indicatorId, departmentId, departmentName, tanggal, indicatorName, target, units, targetUnit, resp.restricted, resp.approved_action || null, numUnit, denUnit);
                } else {
                    toastError(resp.message || 'Tidak bisa input untuk tanggal ini');
                }
            }
        };
        xhr.send('indicator_id=' + indicatorId + '&department_id=' + departmentId + '&tanggal=' + tanggal);
    }

    function _openModalContinue(indicatorId, departmentId, departmentName, tanggal, indicatorName, target, units, targetUnit, restricted, approvedAction, numUnit, denUnit) {
        document.getElementById('input_indicator_id').value = indicatorId;
        document.getElementById('input_department_id').value = departmentId;
        document.getElementById('input_department_name').value = departmentName;
        document.getElementById('input_tanggal').value = tanggal;

        document.getElementById('modalIndikatorNama').textContent = indicatorName || '-';
        document.getElementById('modalTarget').textContent = target || '-';
        document.getElementById('modalSatuan').textContent = units || '-';
        document.getElementById('num_unit').textContent = numUnit || units || '-';
        document.getElementById('denum_unit').textContent = denUnit || targetUnit || '-';

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
                        checkRequestStatus();
                    } else {
                        toastError(resp.message);
                    }
                }
            };
            xhr.send('indicator_id=' + indicator_id + '&department_id=' + department_id + '&tanggal=' + tanggal + '&reason=' + encodeURIComponent(reason) + '&action_type=' + action);
        });
    }

    function openRequestBukaPeriodeModal() {
        document.getElementById('bp-period-start').value = '';
        document.getElementById('bp-period-end').value = '';
        document.getElementById('bp-reason').value = '';

        var sel = document.getElementById('bp-indicator');
        sel.innerHTML = '<option value="">-- Pilih Indikator --</option>';
        if (_allData && _allData.length > 0) {
            for (var i = 0; i < _allData.length; i++) {
                var opt = document.createElement('option');
                opt.value = _allData[i].indicator_id + '|' + _allData[i].department_id;
                opt.textContent = _allData[i].indicator_element || 'Indikator #' + (i + 1);
                sel.appendChild(opt);
            }
        }

        var modal = new bootstrap.Modal(document.getElementById('modalBukaPeriode'));
        modal.show();
    }

    function submitBukaPeriode() {
        var val = document.getElementById('bp-indicator').value;
        if (!val || val.indexOf('|') === -1) {
            toastError('Pilih indikator terlebih dahulu');
            return;
        }
        var parts = val.split('|');
        var indicatorId = parts[0];
        var departmentId = parts[1];
        var periodStart = document.getElementById('bp-period-start').value;
        var periodEnd = document.getElementById('bp-period-end').value;
        var reason = document.getElementById('bp-reason').value.trim();

        if (!periodStart || !periodEnd || !reason) {
            toastError('Semua field harus diisi');
            return;
        }
        if (periodStart > periodEnd) {
            toastError('Periode mulai tidak boleh melebihi periode selesai');
            return;
        }

        Swal.fire({
            title: 'Kirim permintaan buka periode?',
            text: 'Periode ' + periodStart + ' s.d. ' + periodEnd,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, kirim',
            cancelButtonText: 'Batal'
        }).then(function(result) {
            if (!result.isConfirmed) return;

            var xhr = new XMLHttpRequest();
            xhr.open('POST', '<?= site_url('siimut/unit/ajax-request-open-period') ?>', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    var resp = JSON.parse(xhr.responseText);
                    if (resp.status) {
                        toastSuccess(resp.message);
                        bootstrap.Modal.getInstance(document.getElementById('modalBukaPeriode')).hide();
                        checkRequestStatus();
                    } else {
                        toastError(resp.message);
                    }
                }
            };
            var grpType = groupTypeMap[catId] || '';
            xhr.send('indicator_id=' + indicatorId + '&department_id=' + departmentId + '&period_start=' + periodStart + '&period_end=' + periodEnd + '&reason=' + encodeURIComponent(reason) + '&group_type=' + grpType);
        });
    }

    function checkRequestStatus() {
        var xhr = new XMLHttpRequest();
        xhr.open('POST', '<?= site_url('siimut/unit/ajax-get-request-status') ?>', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                var resp = JSON.parse(xhr.responseText);
                if (resp.status && resp.data && resp.data.length > 0) {
                    var html = '';
                    for (var i = 0; i < resp.data.length; i++) {
                        var r = resp.data[i];
                        var statusBadge = r.ar_status === 'approved' ? '<span class="badge bg-success">Disetujui</span>' :
                                          r.ar_status === 'rejected' ? '<span class="badge bg-danger">Ditolak</span>' :
                                          '<span class="badge bg-warning text-dark">Pending</span>';
                        var actionLabel = r.ar_action_type === 'open_period' ? 'Buka Periode' :
                                          r.ar_action_type === 'delete' ? 'Hapus' : 'Edit';
                        html += '<div class="accordion-item">';
                        html += '    <h2 class="accordion-header">';
                        html += '        <button class="accordion-button collapsed py-2" type="button" data-bs-toggle="collapse" data-bs-target="#hist-' + i + '">';
                        html += '            <div class="d-flex w-100 justify-content-between align-items-center me-3">';
                        html += '                <span><strong>' + (r.ar_request_date || '-') + '</strong> — ' + actionLabel + '</span>';
                        html += '                <span>' + statusBadge + '</span>';
                        html += '            </div>';
                        html += '        </button>';
                        html += '    </h2>';
                        html += '    <div id="hist-' + i + '" class="accordion-collapse collapse" data-bs-parent="#historyAccordion">';
                        html += '        <div class="accordion-body py-2">';
                        html += '            <table class="table table-sm table-borderless mb-0">';
                        html += '                <tr><td class="text-muted" style="width:110px;">Indikator</td><td>' + escHtml(r.indicator_name || '-') + '</td></tr>';
                        html += '                <tr><td class="text-muted">Periode</td><td>' + (r.ar_period || '-') + (r.ar_period_end ? ' s.d. ' + r.ar_period_end : '') + '</td></tr>';
                        html += '                <tr><td class="text-muted">Alasan</td><td>' + escHtml(r.ar_reason) + '</td></tr>';
                        html += '                <tr><td class="text-muted">Status</td><td>' + statusBadge + '</td></tr>';
                        html += '                <tr><td class="text-muted">Diproses</td><td>' + escHtml(r.approve_by_name || '-') + '</td></tr>';
                        if (r.ar_notes) html += '<tr><td class="text-muted">Catatan</td><td>' + escHtml(r.ar_notes) + '</td></tr>';
                        html += '            </table>';
                        html += '        </div>';
                        html += '    </div>';
                        html += '</div>';
                    }
                    document.getElementById('historyModalBody').innerHTML = '<div class="accordion" id="historyAccordion">' + html + '</div>';
                } else {
                    document.getElementById('historyModalBody').innerHTML = '<div class="text-center py-3 text-muted">Belum ada riwayat permintaan</div>';
                }
            }
        };
        var grpType = groupTypeMap[catId] || '';
        xhr.send('action_type=open_period&department_id=' + document.getElementById('filter_department').value + '&category_id=' + catId + '&group_type=' + grpType);
    }

    function openHistoryModal() {
        checkRequestStatus();
        var modal = new bootstrap.Modal(document.getElementById('modalHistory'));
        modal.show();
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


