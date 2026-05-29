<style>
    .form-inm-header {
        background: linear-gradient(135deg, #17a2b8 0%, #0f7c8f 100%);
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
        border-bottom: 2px solid #17a2b8;
        font-weight: bold;
    }

    .btn-inm-primary {
        background: linear-gradient(135deg, #17a2b8 0%, #0f7c8f 100%);
        border: none;
        color: white;
    }

    .btn-inm-primary:hover {
        background: linear-gradient(135deg, #0f7c8f 0%, #0a5d6b 100%);
        color: white;
    }

    .form-control:focus, .form-select:focus {
        border-color: #17a2b8;
        box-shadow: 0 0 0 0.2rem rgba(23, 162, 184, 0.25);
    }

    .modal-header.modal-inm {
        background: linear-gradient(135deg, #17a2b8 0%, #0f7c8f 100%);
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
        background: linear-gradient(135deg, #17a2b8 0%, #0f7c8f 100%);
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
        background: #0f7c8f;
    }

    .table-inm > thead th.fixed-col2 {
        position: sticky;
        z-index: 3;
        background: #0f7c8f;
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

    .cell-target {
        background-color: #d4edda !important;
    }

    .cell-fail {
        background-color: #f8d7da !important;
    }

    .cell-empty {
        background-color: #fff3cd !important;
    }

    .cell-has-data {
        font-weight: 600;
        background-color: #d1ecf1 !important;
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
</style>

<div class="container-fluid py-4">
    <div class="form-inm-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h4 class="mb-1"><i class="bi bi-pencil-square me-2"></i>Form Input Indikator Mutu Unit (IMPUNIT)</h4>
                <p class="mb-0 opacity-75">Input data harian — klik sel pada tanggal untuk mengisi</p>
            </div>
            <div class="col-md-4 text-end">
                <a href="<?= site_url('siimut/grafik-impunit') ?>" class="btn btn-light btn-sm">
                    <i class="bi bi-graph-up me-1"></i> Lihat Grafik
                </a>
                <a href="<?= site_url('siimut/rekap-periode-impunit') ?>" class="btn btn-light btn-sm">
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
                    <select class="form-select" id="filter_department">
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
                <div class="col-md-2 d-flex align-items-end">
                    <button type="button" class="btn btn-inm-primary w-100" onclick="loadData()">
                        <i class="bi bi-search me-1"></i> Tampilkan
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div id="loadingIndicator" class="text-center py-5 d-none">
        <span class="spinner-border text-info" style="width:3rem;height:3rem;" role="status"></span>
        <p class="mt-3 text-muted">Memuat data...</p>
    </div>

    <div id="tableContainer" class="d-none">
        <div class="card table-card">
            <div class="card-header">
                <i class="bi bi-list-ul me-2"></i>Daftar Indikator IMPUNIT
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
                        <div class="dataTables_filter text-md-end">
                            <label>Cari:
                                <input type="search" class="form-control form-control-sm" id="searchInput" onkeyup="filterIndicators()">
                            </label>
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
            </div>
        </div>
    </div>
</div>

<!-- Modal Input -->
<div class="modal fade" id="modalInput" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header modal-inm">
                <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Input Data Harian IMPUNIT</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info mb-3">
                    <strong id="modalIndikatorNama">-</strong>
                    <div class="mt-2">
                        <span class="badge bg-info me-1">Target: <span id="modalTarget">-</span></span>
                        <span class="badge bg-secondary">Satuan: <span id="modalSatuan">-</span></span>
                    </div>
                </div>

                <form id="formInputData">
                    <input type="hidden" id="input_indicator_id" name="indicator_id">
                    <input type="hidden" id="input_department_id" name="department_id">

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Tanggal</label>
                            <input type="text" class="form-control" id="input_tanggal" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Ruangan</label>
                            <input type="text" class="form-control" id="input_department_name" readonly>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Numerator</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="input_numerator" name="numerator" step="any" min="0" placeholder="Nilai numerator">
                                <span class="input-group-text" id="num_unit">-</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Denumerator</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="input_denumerator" name="denumerator" step="any" min="0" placeholder="Nilai denumerator">
                                <span class="input-group-text" id="denum_unit">-</span>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-secondary" id="hasilHitung">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Hasil:</span>
                            <strong id="hasilPersen">-</strong>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="bi bi-x-circle me-1"></i> Batal</button>
                <button type="button" class="btn btn-inm-primary" onclick="saveData()"><i class="bi bi-save me-1"></i> Simpan</button>
            </div>
        </div>
    </div>
</div>


<script>
    var modalInput = null;

    document.addEventListener('DOMContentLoaded', function() {
        modalInput = new bootstrap.Modal(document.getElementById('modalInput'));

        document.getElementById('input_numerator').addEventListener('input', hitungHasil);
        document.getElementById('input_denumerator').addEventListener('input', hitungHasil);

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

        document.getElementById('loadingIndicator').classList.remove('d-none');
        document.getElementById('tableContainer').classList.add('d-none');

        var xhr = new XMLHttpRequest();
        xhr.open('POST', '<?= site_url('siimut/impunit/get-indicators') ?>', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function() {
            document.getElementById('loadingIndicator').classList.add('d-none');
            if (xhr.status === 200) {
                try {
                    var response = JSON.parse(xhr.responseText);
                    renderTable(response);
                } catch(e) {
                    alert('Gagal parse response');
                }
            } else {
                alert('HTTP error: ' + xhr.status);
            }
        };
        xhr.onerror = function() {
            document.getElementById('loadingIndicator').classList.add('d-none');
            alert('Gagal memuat data');
        };
        xhr.send('tahun=' + tahun + '&bulan=' + bulan + '&department_id=' + department_id);
    }

    var _allData = [];

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
        for (var i = 0; i < data.length; i++) {
            var row = data[i];
            var daily = row.daily || [];
            html += '<tr class="indicator-row">';
            html += '<td class="text-center fw-bold">' + (i + 1) + '</td>';
            html += '<td class="text-start">' + escHtml(row.indicator_element) + '</td>';
            html += '<td class="text-center">' + escHtml(row.indicator_target) + ' ' + escHtml(row.indicator_units) + '</td>';

            for (var d = 0; d < daily.length; d++) {
                var item = daily[d];
                var cellClass = 'day-cell text-center';
                var nilaiDisplay = '-';

                if (item.nilai !== null) {
                    nilaiDisplay = item.nilai + ' ' + escHtml(row.indicator_units);
                    if (item.tercapai === true) cellClass += ' cell-target cell-has-data';
                    else if (item.tercapai === false) cellClass += ' cell-fail cell-has-data';
                    else cellClass += ' cell-has-data';
                } else {
                    cellClass += (item.num > 0 || item.denum > 0) ? ' cell-fail' : ' cell-empty';
                }

                html += '<td class="' + cellClass + '" ' +
                    'onclick="openModal(' + row.indicator_id + ', ' + row.department_id + ', \'' + escJs(row.department_name) + '\', ' + item.hari + ', \'' + escJs(row.indicator_element) + '\', \'' + escJs(row.indicator_target) + '\', \'' + escJs(row.indicator_units) + '\', \'' + escJs(row.indicator_target_unit || '') + '\')">' +
                    '<div class="fw-bold">' + nilaiDisplay + '</div>' +
                    '<div class="num-denum">' + (item.num || 0) + ' / ' + (item.denum || 0) + '</div></td>';
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

        rows.forEach(function(row, idx) {
            if (row.dataset.filtered === '0') {
                row.style.display = 'none';
            } else {
                var pos = filtered.indexOf(idx);
                row.style.display = (pos < show) ? '' : 'none';
            }
        });
    }

    function changePageLength() {
        applyPagination();
    }

    function filterIndicators() {
        applyPagination();
    }

    function openModal(indicatorId, departmentId, departmentName, hari, indicatorName, target, units, targetUnit) {
        var periode = document.getElementById('filter_periode').value;
        var tahun = periode.substring(0, 4);
        var bulan = periode.substring(5, 7);
        var tanggal = tahun + '-' + bulan + '-' + String(hari).padStart(2, '0');

        document.getElementById('input_indicator_id').value = indicatorId;
        document.getElementById('input_department_id').value = departmentId;
        document.getElementById('input_department_name').value = departmentName;
        document.getElementById('input_tanggal').value = tanggal;

        document.getElementById('modalIndikatorNama').textContent = indicatorName || '-';
        document.getElementById('modalTarget').textContent = target || '-';
        document.getElementById('modalSatuan').textContent = units || '-';
        document.getElementById('num_unit').textContent = units || '-';
        document.getElementById('denum_unit').textContent = targetUnit || '-';

        var xhr = new XMLHttpRequest();
        xhr.open('POST', '<?= site_url('siimut/impunit/get-indicator-detail') ?>', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                var response = JSON.parse(xhr.responseText);
                if (response.existing_data && response.existing_data.length > 0) {
                    var last = response.existing_data[response.existing_data.length - 1];
                    document.getElementById('input_numerator').value = last.result_numerator_value || '';
                    document.getElementById('input_denumerator').value = last.result_denumerator_value || '';
                } else {
                    document.getElementById('input_numerator').value = '';
                    document.getElementById('input_denumerator').value = '';
                }
                hitungHasil();
                modalInput.show();
            }
        };
        xhr.send('indicator_id=' + indicatorId + '&department_id=' + departmentId + '&tanggal=' + tanggal);
    }

    function hitungHasil() {
        var num = parseFloat(document.getElementById('input_numerator').value) || 0;
        var denum = parseFloat(document.getElementById('input_denumerator').value) || 0;
        var hasil = document.getElementById('hasilPersen');
        if (denum > 0) {
            hasil.textContent = num + ' / ' + denum + ' = ' + (num / denum * 100).toFixed(2) + '%';
        } else {
            hasil.textContent = num > 0 ? num + ' / 0 = ~' : '-';
        }
    }

    function saveData() {
        var form = document.getElementById('formInputData');
        var data = new FormData(form);

        var xhr = new XMLHttpRequest();
        xhr.open('POST', '<?= site_url('siimut/impunit/save') ?>', true);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                var response = JSON.parse(xhr.responseText);
                if (response.status) {
                    modalInput.hide();
                    loadData();
                } else {
                    alert('Gagal: ' + (response.message || 'Unknown error'));
                }
            }
        };
        xhr.send(data);
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
