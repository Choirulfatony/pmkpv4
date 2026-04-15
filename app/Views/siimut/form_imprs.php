<style>
    .form-imprs-header {
        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
        color: white;
        padding: 20px;
        border-radius: 10px;
        margin-bottom: 20px;
    }

    .card-form-imprs {
        border: none;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .card-form-imprs .card-header {
        background: var(--bs-tertiary-bg);
        border-bottom: 2px solid #007bff;
        font-weight: bold;
    }

    .input-group-text {
        background-color: #e9ecef;
    }

    .btn-imprs-primary {
        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
        border: none;
        color: white;
    }

    .btn-imprs-primary:hover {
        background: linear-gradient(135deg, #0056b3 0%, #004494 100%);
        color: white;
    }

    .form-control:focus, .form-select:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    .table-imprs > thead {
        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
        color: white;
    }

    .status-tercapai {
        background-color: #d4edda;
        color: #155724;
    }

    .status-tidak-tercapai {
        background-color: #f8d7da;
        color: #721c24;
    }

    .modal-header.modal-imprs {
        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
        color: white;
    }

    .modal-imprs .btn-close {
        filter: brightness(0) invert(1);
    }
</style>

<div class="container-fluid py-4">
    <div class="form-imprs-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h4 class="mb-1"><i class="bi bi-pencil-square me-2"></i>Form Input Indikator Mutu Prioritas RS (IMPRS)</h4>
                <p class="mb-0 opacity-75">Input data numerasi dan denumerasi untuk indikator mutu prioritas rumah sakit</p>
            </div>
            <div class="col-md-4 text-end">
                <a href="<?= site_url('siimut/grafik-imprs') ?>" class="btn btn-light btn-sm">
                    <i class="bi bi-graph-up me-1"></i> Lihat Grafik
                </a>
                <a href="<?= site_url('siimut/rekap-periode-imprs') ?>" class="btn btn-light btn-sm">
                    <i class="bi bi-file-earmark-bar-graph me-1"></i> Rekap Periode
                </a>
            </div>
        </div>
    </div>

    <div class="card card-form-imprs mb-4">
        <div class="card-header">
            <i class="bi bi-filter me-2"></i>Filter Data
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-bold">Tahun</label>
                    <select class="form-select" id="filter_tahun">
                        <?php for ($y = date('Y'); $y >= date('Y') - 5; $y--): ?>
                            <option value="<?= $y ?>" <?= ($y == $tahun) ? 'selected' : '' ?>><?= $y ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Ruangan</label>
                    <select class="form-select" id="filter_department">
                        <option value="">-- Semua Ruangan --</option>
                        <?php foreach ($departments as $dept): ?>
                            <option value="<?= $dept->department_id ?>"><?= esc($dept->department_name) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="button" class="btn btn-imprs-primary w-100" onclick="loadIndicators()">
                        <i class="bi bi-search me-1"></i> Tampilkan
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-form-imprs">
        <div class="card-header">
            <i class="bi bi-list-ul me-2"></i>Daftar Indikator IMPRS
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-imprs" id="tabelIndikator" style="width: 100%;">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 50px;">No</th>
                            <th>Indikator</th>
                            <th>Ruangan</th>
                            <th class="text-center">Target</th>
                            <th class="text-center" style="width: 150px;">Pilih Ruangan</th>
                            <th class="text-center" style="width: 120px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tabelBody">
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="bi bi-arrow-down-circle me-2"></i>Pilih tahun dan ruangan, lalu klik "Tampilkan"
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalInput" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header modal-imprs">
                <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Input Data Indikator</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info mb-3">
                    <strong id="modalIndikatorNama">-</strong>
                    <div class="mt-2">
                        <span class="badge bg-primary me-1">Target: <span id="modalTarget">-</span></span>
                        <span class="badge bg-secondary">Satuan: <span id="modalSatuan">-</span></span>
                    </div>
                </div>

                <form id="formInputData">
                    <input type="hidden" id="input_indicator_id" name="indicator_id">
                    <input type="hidden" id="input_department_id" name="department_id">

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Tanggal Input</label>
                            <input type="date" class="form-control" id="input_tanggal" name="tanggal" value="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Ruangan</label>
                            <input type="text" class="form-control" id="input_department_name" readonly>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Numerator (Pembilang)</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="input_numerator" name="numerator" step="any" min="0" placeholder="Masukkan nilai numerator">
                                <span class="input-group-text" id="num_unit">-</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Denumerator (Penyebut)</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="input_denumerator" name="denumerator" step="any" min="0" placeholder="Masukkan nilai denumerator">
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

                    <hr>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Rencana Perbaikan (Opsional)</label>
                        <textarea class="form-control" id="input_rencana" name="rencana_perbaikan" rows="3" placeholder="Masukkan rencana perbaikan jika diperlukan"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i> Batal
                </button>
                <button type="button" class="btn btn-imprs-primary" onclick="saveData()">
                    <i class="bi bi-save me-1"></i> Simpan Data
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    var modalInput = null;
    var currentIndicatorData = null;

    document.addEventListener('DOMContentLoaded', function() {
        modalInput = new bootstrap.Modal(document.getElementById('modalInput'));
        
        document.getElementById('input_numerator').addEventListener('input', hitungHasil);
        document.getElementById('input_denumerator').addEventListener('input', hitungHasil);
        document.getElementById('input_tanggal').addEventListener('change', loadExistingData);
    });

    function loadIndicators() {
        var tahun = document.getElementById('filter_tahun').value;
        var department_id = document.getElementById('filter_department').value;

        var tbody = document.getElementById('tabelBody');
        tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4"><i class="bi bi-hourglass-split me-2"></i>Memuat data...</td></tr>';

        var xhr = new XMLHttpRequest();
        xhr.open('GET', '<?= site_url('form-imprs/get-indicators') ?>?tahun=' + tahun + '&department_id=' + department_id, true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                var response = JSON.parse(xhr.responseText);
                renderIndicators(response);
            }
        };
        xhr.send();
    }

    function renderIndicators(data) {
        var tbody = document.getElementById('tabelBody');
        
        if (!data || data.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4"><i class="bi bi-folder2-open me-2"></i>Tidak ada indikator untuk periode ini</td></tr>';
            return;
        }

        var html = '';
        data.forEach(function(item, index) {
            html += '<tr>';
            html += '<td class="text-center">' + (index + 1) + '</td>';
            html += '<td>' + escapeHtml(item.indicator_element) + '</td>';
            html += '<td>' + escapeHtml(item.department_name) + '</td>';
            html += '<td class="text-center">' + item.indicator_target + ' ' + escapeHtml(item.indicator_units) + '</td>';
            html += '<td>';
            html += '<select class="form-select form-select-sm" onchange="onDepartmentChange(this, ' + item.indicator_id + ')">';
            html += '<option value="' + item.department_id + '" selected>' + escapeHtml(item.department_name) + '</option>';
            html += '</select>';
            html += '</td>';
            html += '<td class="text-center">';
            html += '<button class="btn btn-primary btn-sm" onclick="showInputForm(' + item.indicator_id + ', ' + item.department_id + ', \'' + escapeHtml(item.indicator_element) + '\', \'' + item.indicator_target + '\', \'' + escapeHtml(item.indicator_units) + '\', \'' + escapeHtml(item.department_name) + '\')">';
            html += '<i class="bi bi-pencil"></i> Input';
            html += '</button>';
            html += '</td>';
            html += '</tr>';
        });

        tbody.innerHTML = html;
    }

    function onDepartmentChange(select, indicatorId) {
        var departmentId = select.value;
    }

    function showInputForm(indicatorId, departmentId, indicatorName, target, units, deptName) {
        document.getElementById('input_indicator_id').value = indicatorId;
        document.getElementById('input_department_id').value = departmentId;
        document.getElementById('modalIndikatorNama').textContent = indicatorName;
        document.getElementById('modalTarget').textContent = target;
        document.getElementById('modalSatuan').textContent = units;
        document.getElementById('input_department_name').value = deptName;
        document.getElementById('num_unit').textContent = units;
        document.getElementById('denum_unit').textContent = units;
        
        document.getElementById('input_numerator').value = '';
        document.getElementById('input_denumerator').value = '';
        document.getElementById('input_rencana').value = '';
        document.getElementById('hasilPersen').textContent = '-';

        loadExistingData();
        modalInput.show();
    }

    function loadExistingData() {
        var indicatorId = document.getElementById('input_indicator_id').value;
        var departmentId = document.getElementById('input_department_id').value;
        var tanggal = document.getElementById('input_tanggal').value;

        if (!indicatorId || !departmentId || !tanggal) return;

        var xhr = new XMLHttpRequest();
        xhr.open('GET', '<?= site_url('form-imprs/get-indicator-detail') ?>?indicator_id=' + indicatorId + '&department_id=' + departmentId + '&tanggal=' + tanggal, true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                var response = JSON.parse(xhr.responseText);
                
                if (response.existing_data && response.existing_data.num !== null) {
                    document.getElementById('input_numerator').value = response.existing_data.num;
                    document.getElementById('input_denumerator').value = response.existing_data.denum;
                    hitungHasil();
                }

                if (response.monthly_total && response.monthly_total.num) {
                    var totalNum = response.monthly_total.num;
                    var totalDenum = response.monthly_total.denum;
                    var total = response.monthly_total.total || '-';
                    document.getElementById('hasilHitung').innerHTML = 
                        '<div class="d-flex justify-content-between align-items-center">' +
                        '<span>Bulan ini:</span>' +
                        '<strong>' + total + '</strong>' +
                        '</div>' +
                        '<div class="small text-muted mt-1">Num: ' + totalNum + ' | Denum: ' + totalDenum + '</div>';
                }
            }
        };
        xhr.send();
    }

    function hitungHasil() {
        var num = parseFloat(document.getElementById('input_numerator').value) || 0;
        var denum = parseFloat(document.getElementById('input_denumerator').value) || 0;
        var units = document.getElementById('modalSatuan').textContent;

        if (denum > 0) {
            var hasil = (num / denum) * 100;
            document.getElementById('hasilPersen').textContent = hasil.toFixed(2) + ' ' + units;
        } else {
            document.getElementById('hasilPersen').textContent = '-';
        }
    }

    function saveData() {
        var indicatorId = document.getElementById('input_indicator_id').value;
        var departmentId = document.getElementById('input_department_id').value;
        var tanggal = document.getElementById('input_tanggal').value;
        var numerator = document.getElementById('input_numerator').value;
        var denumerator = document.getElementById('input_denumerator').value;
        var rencana = document.getElementById('input_rencana').value;

        if (!tanggal) {
            alert('Tanggal harus dipilih');
            return;
        }

        var formData = new FormData();
        formData.append('indicator_id', indicatorId);
        formData.append('department_id', departmentId);
        formData.append('tanggal', tanggal);
        formData.append('numerator', numerator);
        formData.append('denumerator', denumerator);
        formData.append('rencana_perbaikan', rencana);

        var xhr = new XMLHttpRequest();
        xhr.open('POST', '<?= site_url('form-imprs/save') ?>', true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4) {
                if (xhr.status === 200) {
                    var response = JSON.parse(xhr.responseText);
                    if (response.status) {
                        alert('Data berhasil disimpan');
                        modalInput.hide();
                        loadExistingData();
                    } else {
                        alert('Error: ' + response.message);
                    }
                } else {
                    alert('Error menyimpan data');
                }
            }
        };
        xhr.send(formData);
    }

    function escapeHtml(text) {
        if (!text) return '';
        var div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
</script>
