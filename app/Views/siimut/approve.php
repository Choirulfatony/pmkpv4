<style>
    .approve-header {
        background: linear-gradient(135deg, #6f42c1 0%, #5533a3 100%);
        color: white;
        padding: 20px;
        border-radius: 10px;
        margin-bottom: 20px;
    }

    .card-approve {
        border: none;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .card-approve .card-header {
        background: var(--bs-tertiary-bg);
        border-bottom: 2px solid #6f42c1;
        font-weight: bold;
    }

    .table-approve th {
        background: #f8f9fa;
        white-space: nowrap;
        font-size: 0.85rem;
    }

    .table-approve td {
        font-size: 0.85rem;
        vertical-align: middle;
    }

    .badge-draft {
        background-color: #fff3cd;
        color: #856404;
    }

    .badge-approved {
        background-color: #d4edda;
        color: #155724;
    }

    #loadingOverlay {
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(255,255,255,0.8);
        display: none;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }

    #loadingOverlay.show {
        display: flex;
    }

    .approve-stats {
        font-size: 0.9rem;
    }
</style>

<div id="loadingOverlay">
    <div class="text-center">
        <div class="spinner-border text-primary" role="status" style="width:3rem;height:3rem;">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-2 fw-bold">Memproses...</p>
    </div>
</div>

<div class="container-fluid py-4">
    <div class="approve-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h4 class="mb-1"><i class="bi bi-check2-square me-2"></i>Approval <?= esc($moduleTitle) ?></h4>
                <p class="mb-0 opacity-75">Validasi data harian — centang data yang akan di-approve</p>
            </div>
            <div class="col-md-4 text-end">
                <a href="<?= site_url($backUrl) ?>" class="btn btn-light btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Form Input
                </a>
            </div>
        </div>
    </div>

    <div class="card card-approve mb-4">
        <div class="card-header">
            <i class="bi bi-filter me-2"></i>Filter
        </div>
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label fw-bold">Bulan</label>
                    <select class="form-select form-select-sm" id="filter_bulan">
                        <?php for ($m = 1; $m <= 12; $m++): ?>
                            <option value="<?= $m ?>" <?= ((int)$bulan === $m) ? 'selected' : '' ?>><?= $namaBulan[$m] ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Tahun</label>
                    <select class="form-select form-select-sm" id="filter_tahun">
                        <?php for ($y = (int)date('Y'); $y >= 2020; $y--): ?>
                            <option value="<?= $y ?>" <?= ((int)$tahun === $y) ? 'selected' : '' ?>><?= $y ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="button" class="btn btn-primary btn-sm w-100" onclick="loadData()">
                        <i class="bi bi-arrow-clockwise me-1"></i> Tampilkan / Reload
                    </button>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-success btn-sm w-100" id="btnApproveSelected" onclick="approveSelected()" disabled>
                        <i class="bi bi-check2-all me-1"></i> Approve
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div id="loadingIndicator" style="display:none; justify-content:center; align-items:center; min-height:300px; flex-direction:column;">
        <i class="loader" style="display:inline-block; position:relative;"></i>
        <p class="mt-3 text-muted">Memuat data...</p>
    </div>

    <div id="tableContainer">
        <div class="card card-approve">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-list-check me-2"></i>Data Draft Menunggu Approval</span>
                <span class="approve-stats" id="statsInfo">0 data</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-approve mb-0" id="approveTable">
                        <thead>
                            <tr>
                                <th style="width:40px; text-align:center;">
                                    <input type="checkbox" id="checkAll" onchange="toggleAll(this)">
                                </th>
                                <th style="width:50px;">No</th>
                                <th>Indikator</th>
                                <th>Departemen</th>
                                <th>Tanggal</th>
                                <th style="width:80px;">Numerator</th>
                                <th style="width:80px;">Denumerator</th>
                                <th style="width:90px;">Nilai</th>
                                <th style="width:80px;">Target</th>
                                <th style="width:60px;">Status</th>
                                <th style="width:150px;">Input Oleh</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            <tr>
                                <td colspan="11" class="text-center text-muted py-4">
                                    <i class="bi bi-inbox me-1"></i> Pilih periode dan klik Tampilkan
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    var module = '<?= $module ?>';
    var currentUserId = <?= (int) ($profileId ?? 0) ?>;

    var moduleRoutes = {
        inm: {
            getData: '<?= site_url('siimut/approval/ajax-get-data') ?>',
            approve: '<?= site_url('siimut/approval/ajax-approve') ?>'
        },
        imprs: {
            getData: '<?= site_url('siimut/approval/ajax-get-data') ?>',
            approve: '<?= site_url('siimut/approval/ajax-approve') ?>'
        },
        impunit: {
            getData: '<?= site_url('siimut/approval/ajax-get-data') ?>',
            approve: '<?= site_url('siimut/approval/ajax-approve') ?>'
        }
    };

    function loadData() {
        var bulan = document.getElementById('filter_bulan').value;
        var tahun = document.getElementById('filter_tahun').value;

        document.getElementById('loadingIndicator').style.display = 'flex';
        document.getElementById('tableContainer').style.display = 'none';

        var xhr = new XMLHttpRequest();
        xhr.open('POST', moduleRoutes[module].getData, true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.onload = function() {
            document.getElementById('loadingIndicator').style.display = 'none';
            document.getElementById('tableContainer').style.display = '';
            if (xhr.status === 200) {
                try {
                    var response = JSON.parse(xhr.responseText);
                    if (response.status) {
                        renderTable(response.data);
                    } else {
                        document.getElementById('tableBody').innerHTML =
                            '<tr><td colspan="11" class="text-center text-danger py-4">' +
                            '<i class="bi bi-exclamation-circle me-1"></i> ' + (response.message || 'Gagal memuat data') +
                            '</td></tr>';
                    }
                } catch(e) {
                    document.getElementById('tableBody').innerHTML =
                        '<tr><td colspan="11" class="text-center text-danger py-4">' +
                        '<i class="bi bi-exclamation-circle me-1"></i> Gagal memproses data' +
                        '</td></tr>';
                }
            } else {
                document.getElementById('tableBody').innerHTML =
                    '<tr><td colspan="11" class="text-center text-danger py-4">' +
                    '<i class="bi bi-exclamation-circle me-1"></i> HTTP error: ' + xhr.status +
                    '</td></tr>';
            }
        };
        xhr.onerror = function() {
            document.getElementById('loadingIndicator').style.display = 'none';
            document.getElementById('tableContainer').style.display = '';
            document.getElementById('tableBody').innerHTML =
                '<tr><td colspan="11" class="text-center text-danger py-4">' +
                '<i class="bi bi-exclamation-circle me-1"></i> Gagal memuat data' +
                '</td></tr>';
        };
        xhr.send('module=' + module + '&bulan=' + bulan + '&tahun=' + tahun);
    }

    function renderTable(data) {
        var tbody = document.getElementById('tableBody');
        var statsInfo = document.getElementById('statsInfo');

        if (!data || data.length === 0) {
            tbody.innerHTML = '<tr><td colspan="11" class="text-center text-muted py-4">' +
                '<i class="bi bi-inbox me-1"></i> Tidak ada data draft untuk periode ini</td></tr>';
            statsInfo.textContent = '0 data';
            document.getElementById('btnApproveSelected').disabled = true;
            document.getElementById('checkAll').checked = false;
            return;
        }

        var html = '';
        for (var i = 0; i < data.length; i++) {
            var d = data[i];
            var num = parseFloat(d.result_numerator_value) || 0;
            var denum = parseFloat(d.result_denumerator_value) || 0;
            var faktor = parseFloat(d.indicator_factors) || 1;
            var nilai = denum !== 0 ? (num / denum) * faktor : null;
            var nilaiDisplay = nilai !== null ? nilai.toFixed(2) + ' ' + (d.indicator_units || '') : '-';
            var targetDisplay = (d.indicator_target || '-') + ' ' + (d.indicator_units || '');

            var tgl = d.result_period ? d.result_period.split(' ')[0] : '-';

            html += '<tr>' +
                '<td style="text-align:center;"><input type="checkbox" class="rowCheckbox" value="' + d.result_id + '" onchange="updateSelectAll()"></td>' +
                '<td>' + (i + 1) + '</td>' +
                '<td>' + escHtml(d.indicator_element || '-') + '</td>' +
                '<td>' + escHtml(d.department_name || '-') + '</td>' +
                '<td>' + tgl + '</td>' +
                '<td>' + num + '</td>' +
                '<td>' + denum + '</td>' +
                '<td>' + nilaiDisplay + '</td>' +
                '<td>' + targetDisplay + '</td>' +
                '<td><span class="badge badge-draft">Draft</span></td>' +
                '<td><small>' + escHtml(d.profile_fullname || '-') + '</small></td>' +
                '</tr>';
        }

        tbody.innerHTML = html;
        statsInfo.textContent = data.length + ' data draft';
        document.getElementById('checkAll').checked = false;
        updateApproveButton();
    }

    function toggleAll(source) {
        var checkboxes = document.querySelectorAll('.rowCheckbox');
        for (var i = 0; i < checkboxes.length; i++) {
            checkboxes[i].checked = source.checked;
        }
        updateApproveButton();
    }

    function updateSelectAll() {
        var checkboxes = document.querySelectorAll('.rowCheckbox');
        var allChecked = true;
        for (var i = 0; i < checkboxes.length; i++) {
            if (!checkboxes[i].checked) {
                allChecked = false;
                break;
            }
        }
        document.getElementById('checkAll').checked = allChecked && checkboxes.length > 0;
        updateApproveButton();
    }

    function updateApproveButton() {
        var checkboxes = document.querySelectorAll('.rowCheckbox:checked');
        document.getElementById('btnApproveSelected').disabled = checkboxes.length === 0;
    }

    function approveSelected() {
        var checkboxes = document.querySelectorAll('.rowCheckbox:checked');
        if (checkboxes.length === 0) {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'warning',
                title: 'Pilih data terlebih dahulu',
                showConfirmButton: false,
                timer: 2000
            });
            return;
        }

        var ids = [];
        for (var i = 0; i < checkboxes.length; i++) {
            ids.push(checkboxes[i].value);
        }

        Swal.fire({
            title: 'Validasi ' + ids.length + ' data?',
            text: 'Data yang dipilih akan di-approve',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, approve!',
            cancelButtonText: 'Batal'
        }).then(function(result) {
            if (!result.isConfirmed) return;

            document.getElementById('loadingOverlay').classList.add('show');

            var xhr = new XMLHttpRequest();
            xhr.open('POST', moduleRoutes[module].approve, true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    document.getElementById('loadingOverlay').classList.remove('show');
                    var response = JSON.parse(xhr.responseText);
                    if (response.status) {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            iconColor: '#5cb85c',
                            title: response.message,
                            showConfirmButton: false,
                            timer: 3000
                        });
                        loadData();
                    } else {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'error',
                            iconColor: '#d9534f',
                            title: response.message || 'Gagal approve data',
                            showConfirmButton: false,
                            timer: 3000
                        });
                    }
                }
            };
            xhr.send('module=' + module + '&ids[]=' + ids.join('&ids[]='));
        });
    }

    function escHtml(str) {
        var div = document.createElement('div');
        div.appendChild(document.createTextNode(str));
        return div.innerHTML;
    }

    document.addEventListener('DOMContentLoaded', function() {
        loadData();
    });
</script>
