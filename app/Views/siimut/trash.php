
<div class="container-fluid py-4">
    <div class="trash-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h4 class="mb-1"><i class="bi bi-trash3 me-2"></i>Trash <?= esc($moduleTitle) ?></h4>
                <p class="mb-0 opacity-75">Data yang telah dihapus — hanya untuk Administrator</p>
            </div>
            <div class="col-md-4 text-end">
                <a href="<?= site_url($backUrl) ?>" class="btn btn-light btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Form Input
                </a>
            </div>
        </div>
    </div>

    <div class="card card-trash mb-4">
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
                    <button type="button" class="btn btn-danger btn-sm w-100" id="btnDeletePermanent" onclick="deletePermanent()" disabled>
                        <i class="bi bi-trash3-fill me-1"></i> Hapus Permanen
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div id="loadingIndicator">
        <i class="loader" style="display:inline-block; position:relative;"></i>
        <p class="mt-3 text-muted">Memuat data...</p>
    </div>

    <div id="tableContainer">
        <div class="card card-trash">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-trash3 me-2"></i>Data Terhapus</span>
                <span id="statsInfo">0 data</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-trash mb-0">
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
                                <th>Dihapus Oleh</th>
                                <th>Tanggal Hapus</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            <tr>
                                <td colspan="10" class="text-center text-muted py-4">
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

    var ajaxUrl = '<?= site_url('siimut/trash/ajax-get-data') ?>';
    var deleteUrl = '<?= site_url('siimut/trash/ajax-permanent-delete') ?>';

    function loadData() {
        var bulan = document.getElementById('filter_bulan').value;
        var tahun = document.getElementById('filter_tahun').value;

        document.getElementById('loadingIndicator').style.display = 'flex';
        document.getElementById('tableContainer').style.display = 'none';

        var xhr = new XMLHttpRequest();
        xhr.open('POST', ajaxUrl, true);
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
                            '<tr><td colspan="10" class="text-center text-danger py-4">' +
                            '<i class="bi bi-exclamation-circle me-1"></i> ' + (response.message || 'Gagal memuat data') +
                            '</td></tr>';
                    }
                } catch(e) {
                    document.getElementById('tableBody').innerHTML =
                        '<tr><td colspan="10" class="text-center text-danger py-4">' +
                        '<i class="bi bi-exclamation-circle me-1"></i> Gagal memproses data' +
                        '</td></tr>';
                }
            } else {
                document.getElementById('tableBody').innerHTML =
                    '<tr><td colspan="10" class="text-center text-danger py-4">' +
                    '<i class="bi bi-exclamation-circle me-1"></i> HTTP error: ' + xhr.status +
                    '</td></tr>';
            }
        };
        xhr.onerror = function() {
            document.getElementById('loadingIndicator').style.display = 'none';
            document.getElementById('tableContainer').style.display = '';
            document.getElementById('tableBody').innerHTML =
                '<tr><td colspan="10" class="text-center text-danger py-4">' +
                '<i class="bi bi-exclamation-circle me-1"></i> Gagal memuat data' +
                '</td></tr>';
        };
        xhr.send('module=' + module + '&bulan=' + bulan + '&tahun=' + tahun);
    }

    function renderTable(data) {
        var tbody = document.getElementById('tableBody');
        var statsInfo = document.getElementById('statsInfo');

        if (!data || data.length === 0) {
            tbody.innerHTML = '<tr><td colspan="10" class="text-center text-muted py-4">' +
                '<i class="bi bi-inbox me-1"></i> Tidak ada data terhapus untuk periode ini</td></tr>';
            statsInfo.textContent = '0 data';
            document.getElementById('btnDeletePermanent').disabled = true;
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

            var tgl = d.result_period ? d.result_period.split(' ')[0] : '-';
            var delDate = d.result_delete_date ? d.result_delete_date.split(' ')[0] : '-';

            html += '<tr>' +
                '<td style="text-align:center;"><input type="checkbox" class="rowCheckbox" value="' + d.result_id + '" onchange="updateSelectAll()"></td>' +
                '<td>' + (i + 1) + '</td>' +
                '<td>' + escHtml(d.indicator_element || '-') + '</td>' +
                '<td>' + escHtml(d.department_name || '-') + '</td>' +
                '<td>' + tgl + '</td>' +
                '<td>' + num + '</td>' +
                '<td>' + denum + '</td>' +
                '<td>' + nilaiDisplay + '</td>' +
                '<td><small>' + escHtml(d.profile_fullname || '-') + '</small></td>' +
                '<td><small>' + delDate + '</small></td>' +
                '</tr>';
        }

        tbody.innerHTML = html;
        statsInfo.textContent = data.length + ' data terhapus';
        document.getElementById('checkAll').checked = false;
        updateDeleteButton();
    }

    function toggleAll(source) {
        var checkboxes = document.querySelectorAll('.rowCheckbox');
        for (var i = 0; i < checkboxes.length; i++) {
            checkboxes[i].checked = source.checked;
        }
        updateDeleteButton();
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
        updateDeleteButton();
    }

    function updateDeleteButton() {
        var checkboxes = document.querySelectorAll('.rowCheckbox:checked');
        document.getElementById('btnDeletePermanent').disabled = checkboxes.length === 0;
    }

    function deletePermanent() {
        var checkboxes = document.querySelectorAll('.rowCheckbox:checked');
        if (checkboxes.length === 0) return;

        var ids = [];
        for (var i = 0; i < checkboxes.length; i++) {
            ids.push(checkboxes[i].value);
        }

        Swal.fire({
            title: 'Hapus permanen ' + ids.length + ' data?',
            text: 'Data akan dihapus permanen dan tidak dapat dikembalikan!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, hapus permanen!',
            cancelButtonText: 'Batal'
        }).then(function(result) {
            if (!result.isConfirmed) return;

            document.getElementById('loadingIndicator').style.display = 'flex';
            document.getElementById('tableContainer').style.display = 'none';

            var xhr = new XMLHttpRequest();
            xhr.open('POST', deleteUrl, true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            xhr.onload = function() {
                document.getElementById('loadingIndicator').style.display = 'none';
                document.getElementById('tableContainer').style.display = '';
                if (xhr.status === 200) {
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
                            title: response.message || 'Gagal menghapus data',
                            showConfirmButton: false,
                            timer: 3000
                        });
                    }
                }
            };
            xhr.onerror = function() {
                document.getElementById('loadingIndicator').style.display = 'none';
                document.getElementById('tableContainer').style.display = '';
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
