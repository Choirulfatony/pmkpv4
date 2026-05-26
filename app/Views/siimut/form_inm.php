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

    .input-group-text {
        background-color: #e9ecef;
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

    .table-inm > thead {
        background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
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

    .modal-header.modal-inm {
        background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
        color: white;
    }

    .modal-inm .btn-close {
        filter: brightness(0) invert(1);
    }
</style>

<div class="container-fluid py-4">
    <div class="form-inm-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h4 class="mb-1"><i class="bi bi-pencil-square me-2"></i>Form Input Indikator Nasional Mutu (INM)</h4>
                <p class="mb-0 opacity-75">Input data numerasi dan denumerasi untuk indikator mutu nasional</p>
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
                    <button type="button" class="btn btn-inm-primary w-100" onclick="loadIndicators()">
                        <i class="bi bi-search me-1"></i> Tampilkan
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-form-inm">
        <div class="card-header">
            <i class="bi bi-list-ul me-2"></i>Daftar Indikator INM
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-inm" id="tabelIndikator" style="width: 100%;">
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
            <div class="modal-header modal-inm">
                <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Input Data Indikator INM</h5>
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
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i> Batal
                </button>
                <button type="button" class="btn btn-inm-primary" onclick="saveData()">
                    <i class="bi bi-save me-1"></i> Simpan Data
                </button>
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
        document.getElementById('input_tanggal').addEventListener('change', loadExistingData);
    });

    function loadIndicators() {
        var tahun = document.getElementById('filter_tahun').value;
        var department_id = document.getElementById('filter_department').value;

        var tbody = document.getElementById('tabelBody');
        tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4"><i class="bi bi-hourglass-split me-2"></i>Memuat data...</td></tr>';

        var xhr = new XMLHttpRequest();
        xhr.open('GET', '<?= site_url('load-module-forminput/get-indicators') ?>?tahun=' + tahun + '&department_id=' + department_id, true);
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
            tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4"><i class="bi bi-info-circle me-2"></i>Tidak ada data indikator</td></tr>';
            return;
        }

        var html = '';
        for (var i = 0; i < data.length; i++) {
            var row = data[i];
            var no = i + 1;
            var departmentOptions = '<option value="">-- Pilih Ruangan --</option>';
            departmentOptions += '<option value="' + row.department_id + '" selected>' + escHtml(row.department_name) + '</option>';

            html += '<tr>';
            html += '<td class="text-center">' + no + '</td>';
            html += '<td>' + escHtml(row.indicator_element) + '</td>';
            html += '<td>' + escHtml(row.department_name) + '</td>';
            html += '<td class="text-center">' + escHtml(row.indicator_target) + ' ' + escHtml(row.indicator_units) + '</td>';
            html += '<td class="text-center"><select class="form-select form-select-sm department-select" data-indicator="' + row.indicator_id + '">' + departmentOptions + '</select></td>';
            html += '<td class="text-center"><button type="button" class="btn btn-success btn-sm" onclick="showInputForm(' + row.indicator_id + ', ' + row.department_id + ')"><i class="bi bi-pencil"></i> Input</button></td>';
            html += '</tr>';
        }
        tbody.innerHTML = html;

        document.querySelectorAll('.department-select').forEach(function(sel) {
            sel.addEventListener('change', function() {
                var indicatorId = this.getAttribute('data-indicator');
                var deptId = this.value;
                var btn = this.closest('tr').querySelector('button');
                if (deptId) {
                    btn.setAttribute('onclick', 'showInputForm(' + indicatorId + ', ' + deptId + ')');
                    btn.disabled = false;
                } else {
                    btn.disabled = true;
                }
            });
        });
    }

    function showInputForm(indicatorId, departmentId) {
        document.getElementById('input_indicator_id').value = indicatorId;
        document.getElementById('input_department_id').value = departmentId;

        var xhr = new XMLHttpRequest();
        xhr.open('GET', '<?= site_url('load-module-forminput/get-indicator-detail') ?>?indicator_id=' + indicatorId + '&department_id=' + departmentId + '&tanggal=' + document.getElementById('input_tanggal').value, true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                var response = JSON.parse(xhr.responseText);
                if (response.indicator) {
                    document.getElementById('modalIndikatorNama').textContent = response.indicator.indicator_element || '-';
                    document.getElementById('modalTarget').textContent = response.indicator.indicator_target || '-';
                    document.getElementById('modalSatuan').textContent = response.indicator.indicator_units || '-';
                    document.getElementById('num_unit').textContent = response.indicator.indicator_units || '-';
                    document.getElementById('denum_unit').textContent = response.indicator.indicator_target_unit || '-';
                }

                var deptSelect = document.querySelector('.department-select');
                if (deptSelect) {
                    var deptName = deptSelect.closest('tr').querySelector('td:nth-child(3)').textContent;
                    document.getElementById('input_department_name').value = deptName.trim();
                }

                if (response.existing_data && response.existing_data.length > 0) {
                    var last = response.existing_data[response.existing_data.length - 1];
                    document.getElementById('input_numerator').value = last.result_numerator_value || '';
                    document.getElementById('input_denumerator').value = last.result_denumerator_value || '';
                } else {
                    document.getElementById('input_numerator').value = '';
                    document.getElementById('input_denumerator').value = '';
                }

                if (response.monthly_total) {
                    var hasil = document.getElementById('hasilPersen');
                    var num = parseFloat(response.monthly_total.num) || 0;
                    var denum = parseFloat(response.monthly_total.denum) || 0;
                    if (denum > 0) {
                        hasil.textContent = num + ' / ' + denum + ' = ' + (num / denum * 100).toFixed(2) + '%';
                    } else {
                        hasil.textContent = '-';
                    }
                }

                hitungHasil();
                modalInput.show();
            }
        };
        xhr.send();
    }

    function loadExistingData() {
        var indicatorId = document.getElementById('input_indicator_id').value;
        var departmentId = document.getElementById('input_department_id').value;
        var tanggal = document.getElementById('input_tanggal').value;
        if (!indicatorId || !departmentId || !tanggal) return;

        var xhr = new XMLHttpRequest();
        xhr.open('GET', '<?= site_url('load-module-forminput/get-indicator-detail') ?>?indicator_id=' + indicatorId + '&department_id=' + departmentId + '&tanggal=' + tanggal, true);
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
            }
        };
        xhr.send();
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
        xhr.open('POST', '<?= site_url('load-module-forminput/save') ?>', true);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                var response = JSON.parse(xhr.responseText);
                if (response.status) {
                    modalInput.hide();
                    alert('Data berhasil disimpan');
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
</script>
