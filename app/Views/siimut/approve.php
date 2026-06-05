
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
                    <select class="form-select form-select-sm" id="filter_bulan" onchange="onPeriodeChange()">
                        <?php for ($m = 1; $m <= 12; $m++): ?>
                            <option value="<?= $m ?>" <?= ((int)$bulan === $m) ? 'selected' : '' ?>><?= $namaBulan[$m] ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold">Tahun</label>
                    <select class="form-select form-select-sm" id="filter_tahun" onchange="onPeriodeChange()">
                        <?php for ($y = (int)date('Y'); $y >= 2020; $y--): ?>
                            <option value="<?= $y ?>" <?= ((int)$tahun === $y) ? 'selected' : '' ?>><?= $y ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Departemen</label>
                    <select class="form-select form-select-sm" id="filter_department" onchange="onDepartmentChange()">
                        <option value="0">-- Semua Departemen --</option>
                        <?php foreach (($departments ?? []) as $dept): ?>
                            <option value="<?= (int) $dept['department_id'] ?>" <?= ((int)($departmentId ?? 0) === (int) $dept['department_id']) ? 'selected' : '' ?>>
                                <?= esc($dept['department_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
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
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="recap-header d-flex justify-content-between align-items-center">
        <div>
            <h5 class="mb-1"><i class="bi bi-bar-chart-line me-2"></i>Rekap Bulanan — Indikator dengan Data Belum Di-approve</h5>
            <small class="opacity-75">Menampilkan total num/denum per bulan (Jan–Des) untuk indikator yang masih memiliki data draft di periode terpilih. Nilai = (Σnum / Σdenum) × factor.</small>
        </div>
        <span class="badge bg-light text-dark px-3 py-2" id="recapStatsInfo">0 indikator</span>
    </div>

    <div id="recapLoadingIndicator" style="display:none; justify-content:center; align-items:center; min-height:160px; flex-direction:column;">
        <i class="loader" style="display:inline-block; position:relative;"></i>
        <p class="mt-3 text-muted">Memuat rekap...</p>
    </div>

    <div id="recapContainer" class="card card-recap" style="display:none;">
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: 70vh;">
                <table class="table table-hover table-recap mb-0" id="recapTable">
                    <thead>
                        <tr>
                            <th rowspan="2" style="width:40px;">No</th>
                            <th rowspan="2">Indikator</th>
                            <th rowspan="2" style="width:70px;">Unit</th>
                            <th rowspan="2" style="width:80px;">Target</th>
                            <th colspan="12" class="text-center">Bulan</th>
                        </tr>
                        <tr>
                            <th>Jan</th><th>Feb</th><th>Mar</th><th>Apr</th><th>Mei</th><th>Jun</th>
                            <th>Jul</th><th>Agu</th><th>Sep</th><th>Okt</th><th>Nov</th><th>Des</th>
                        </tr>
                    </thead>
                    <tbody id="recapTableBody">
                    </tbody>
                </table>
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
            getDepartments: '<?= site_url('siimut/approval/ajax-get-departments') ?>',
            getRecap: '<?= site_url('siimut/approval/ajax-get-recap') ?>',
            approve: '<?= site_url('siimut/approval/ajax-approve') ?>'
        },
        imprs: {
            getData: '<?= site_url('siimut/approval/ajax-get-data') ?>',
            getDepartments: '<?= site_url('siimut/approval/ajax-get-departments') ?>',
            getRecap: '<?= site_url('siimut/approval/ajax-get-recap') ?>',
            approve: '<?= site_url('siimut/approval/ajax-approve') ?>'
        },
        impunit: {
            getData: '<?= site_url('siimut/approval/ajax-get-data') ?>',
            getDepartments: '<?= site_url('siimut/approval/ajax-get-departments') ?>',
            getRecap: '<?= site_url('siimut/approval/ajax-get-recap') ?>',
            approve: '<?= site_url('siimut/approval/ajax-approve') ?>'
        }
    };

    /**
     * Dipanggil setiap kali user mengganti bulan atau tahun.
     * 1. Ambil daftar departemen yang punya draft untuk periode baru (AJAX).
     * 2. Reset dropdown departemen ke "Semua Departemen".
     * 3. Auto-load data untuk periode baru.
     */
    function onPeriodeChange() {
        var bulan = document.getElementById('filter_bulan').value;
        var tahun = document.getElementById('filter_tahun').value;

        var xhr = new XMLHttpRequest();
        xhr.open('POST', moduleRoutes[module].getDepartments, true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.onload = function() {
            var dropdown = document.getElementById('filter_department');
            dropdown.innerHTML = '<option value="0">-- Semua Departemen --</option>';
            if (xhr.status === 200) {
                try {
                    var response = JSON.parse(xhr.responseText);
                    if (response.status && response.data) {
                        for (var i = 0; i < response.data.length; i++) {
                            var d = response.data[i];
                            var opt = document.createElement('option');
                            opt.value = d.department_id;
                            opt.textContent = d.department_name;
                            dropdown.appendChild(opt);
                        }
                    }
                } catch (e) {
                    // silent — fallback to "Semua Departemen"
                }
            }
            // Auto-reload data dengan filter baru
            loadData();
            loadRecap();
        };
        xhr.onerror = function() {
            // Tetap load data meski dropdown gagal
            loadData();
            loadRecap();
        };
        xhr.send('module=' + module + '&bulan=' + bulan + '&tahun=' + tahun);
    }

    function onDepartmentChange() {
        loadData();
        loadRecap();
    }

    function loadData() {
        var bulan = document.getElementById('filter_bulan').value;
        var tahun = document.getElementById('filter_tahun').value;
        var department = document.getElementById('filter_department').value;

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
                    console.error('JSON parse error:', e, 'Response:', xhr.responseText.substring(0, 200));
                    document.getElementById('tableBody').innerHTML =
                        '<tr><td colspan="11" class="text-center text-danger py-4">' +
                        '<i class="bi bi-exclamation-circle me-1"></i> Gagal memproses data (lihat console F12)' +
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
        xhr.send('module=' + module + '&bulan=' + bulan + '&tahun=' + tahun + '&department=' + department);
    }

    function loadRecap() {
        var bulan = document.getElementById('filter_bulan').value;
        var tahun = document.getElementById('filter_tahun').value;
        var department = document.getElementById('filter_department').value;

        document.getElementById('recapLoadingIndicator').style.display = 'flex';
        document.getElementById('recapContainer').style.display = 'none';

        var xhr = new XMLHttpRequest();
        xhr.open('POST', moduleRoutes[module].getRecap, true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.onload = function() {
            document.getElementById('recapLoadingIndicator').style.display = 'none';
            document.getElementById('recapContainer').style.display = '';
            if (xhr.status === 200) {
                try {
                    var response = JSON.parse(xhr.responseText);
                    if (response.status) {
                        renderRecap(response.data);
                    } else {
                        document.getElementById('recapTableBody').innerHTML =
                            '<tr><td colspan="16" class="text-center text-danger py-4">' +
                            '<i class="bi bi-exclamation-circle me-1"></i> ' + (response.message || 'Gagal memuat rekap') +
                            '</td></tr>';
                        document.getElementById('recapStatsInfo').textContent = '0 indikator';
                    }
                } catch (e) {
                    console.error('JSON parse error (recap):', e, 'Response:', xhr.responseText.substring(0, 200));
                    document.getElementById('recapTableBody').innerHTML =
                        '<tr><td colspan="16" class="text-center text-danger py-4">' +
                        '<i class="bi bi-exclamation-circle me-1"></i> Gagal memproses rekap (lihat console F12)' +
                        '</td></tr>';
                    document.getElementById('recapStatsInfo').textContent = '0 indikator';
                }
            } else {
                document.getElementById('recapTableBody').innerHTML =
                    '<tr><td colspan="16" class="text-center text-danger py-4">' +
                    '<i class="bi bi-exclamation-circle me-1"></i> HTTP error: ' + xhr.status +
                    '</td></tr>';
                document.getElementById('recapStatsInfo').textContent = '0 indikator';
            }
        };
        xhr.onerror = function() {
            document.getElementById('recapLoadingIndicator').style.display = 'none';
            document.getElementById('recapContainer').style.display = '';
            document.getElementById('recapTableBody').innerHTML =
                '<tr><td colspan="16" class="text-center text-danger py-4">' +
                '<i class="bi bi-exclamation-circle me-1"></i> Gagal memuat rekap' +
                '</td></tr>';
            document.getElementById('recapStatsInfo').textContent = '0 indikator';
        };
        xhr.send('module=' + module + '&bulan=' + bulan + '&tahun=' + tahun + '&department=' + department);
    }

    function renderRecap(data) {
        var tbody = document.getElementById('recapTableBody');
        var stats = document.getElementById('recapStatsInfo');

        if (!data || data.length === 0) {
            tbody.innerHTML = '<tr><td colspan="16" class="text-center text-muted py-4">' +
                '<i class="bi bi-inbox me-1"></i> Tidak ada indikator dengan data belum di-approve pada periode ini</td></tr>';
            stats.textContent = '0 indikator';
            return;
        }

        var html = '';
        for (var i = 0; i < data.length; i++) {
            var ind = data[i];
            var target = ind.indicator_target != null ? ind.indicator_target : '-';
            var unit = ind.indicator_units || '';
            html += '<tr>' +
                '<td>' + (i + 1) + '</td>' +
                '<td>' + escHtml(ind.indicator_element || '-') + '</td>' +
                '<td>' + escHtml(unit) + '</td>' +
                '<td>' + target + ' ' + escHtml(unit) + '</td>';

            for (var m = 1; m <= 12; m++) {
                var cell = ind.months[m] || { num: 0, denum: 0, nilai: null, has_draft: false, has_approved: false };
                var cellClass = 'month-cell';
                if (cell.has_draft) { cellClass += ' cell-has-draft'; }

                if (cell.nilai === null) {
                    html += '<td class="' + cellClass + '"><span class="nilai-na">-</span></td>';
                } else {
                    var nilaiText = cell.nilai.toFixed(2);
                    html += '<td class="' + cellClass + '">' +
                        '<div class="nilai-text">' + nilaiText + '</div>' +
                        '<div class="cell-num-denum">n:' + formatNum(cell.num) + ' d:' + formatNum(cell.denum) + '</div>' +
                        '</td>';
                }
            }
            html += '</tr>';
        }
        tbody.innerHTML = html;
        stats.textContent = data.length + ' indikator';
    }

    function formatNum(n) {
        n = parseFloat(n) || 0;
        if (Number.isInteger(n)) { return n.toString(); }
        return n.toFixed(2);
    }

    var approveTable = null;

    function initApproveTable() {
        if (approveTable) {
            approveTable.clear().draw();
            return;
        }
        // Clear any initial placeholder rows before DataTables init
        $('#approveTable tbody').empty();
        approveTable = $('#approveTable').DataTable({
            responsive: true,
            destroy: true,
            pageLength: 10,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'Semua']],
            language: {
                search: 'Cari:',
                lengthMenu: 'Tampilkan _MENU_ data per halaman',
                info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
                infoEmpty: 'Menampilkan 0 sampai 0 dari 0 data',
                infoFiltered: '(difilter dari _MAX_ total data)',
                paginate: { first: 'Pertama', last: 'Terakhir', next: 'Selanjutnya', previous: 'Sebelumnya' },
                zeroRecords: 'Tidak ditemukan data yang cocok',
                emptyTable: 'Tidak ada data draft untuk periode ini'
            },
            order: [[4, 'asc']],
            columnDefs: [
                { orderable: false, targets: [0, 10] },
                { className: 'text-center', targets: [0, 1, 5, 6, 7, 8, 9, 10] },
                { width: '40px', targets: 0 },
                { width: '50px', targets: 1 },
                { width: '80px', targets: [5, 6, 7, 8, 9] },
                { width: '150px', targets: 10 }
            ],
            drawCallback: function() {
                updateApproveButton();
                document.getElementById('checkAll').checked = false;
            }
        });
    }

    function renderTable(data) {
        var statsInfo = document.getElementById('statsInfo');

        initApproveTable();

        if (!data || data.length === 0) {
            approveTable.clear().draw();
            statsInfo.textContent = '0 data';
            document.getElementById('btnApproveSelected').disabled = true;
            return;
        }

        var rows = [];
        for (var i = 0; i < data.length; i++) {
            var d = data[i];
            var num = parseFloat(d.result_numerator_value) || 0;
            var denum = parseFloat(d.result_denumerator_value) || 0;
            var faktor = parseFloat(d.indicator_factors) || 1;
            var nilai = denum !== 0 ? (num / denum) * faktor : null;
            var nilaiDisplay = nilai !== null ? nilai.toFixed(2) + ' ' + (d.indicator_units || '') : '-';
            var targetDisplay = (d.indicator_target || '-') + ' ' + (d.indicator_units || '');
            var tgl = d.result_period ? d.result_period.split(' ')[0] : '-';

            rows.push([
                '<input type="checkbox" class="rowCheckbox" value="' + d.result_id + '" onchange="updateSelectAll()">',
                (i + 1),
                escHtml(d.indicator_element || '-'),
                escHtml(d.department_name || '-'),
                tgl,
                num,
                denum,
                nilaiDisplay,
                targetDisplay,
                '<span class="badge badge-draft">Draft</span>',
                '<small>' + escHtml(d.profile_fullname || '-') + '</small>'
            ]);
        }

        approveTable.rows.add(rows).draw();
        statsInfo.textContent = data.length + ' data draft';
    }

    function toggleAll(source) {
        if (!approveTable) { return; }
        var nodes = approveTable.rows({ page: 'current' }).nodes();
        $(nodes).find('.rowCheckbox').each(function() {
            this.checked = source.checked;
        });
        updateApproveButton();
    }

    function updateSelectAll() {
        if (!approveTable) { return; }
        var nodes = approveTable.rows({ page: 'current' }).nodes();
        var checkboxes = $(nodes).find('.rowCheckbox');
        var allChecked = checkboxes.length > 0;
        checkboxes.each(function() {
            if (!this.checked) { allChecked = false; }
        });
        document.getElementById('checkAll').checked = allChecked;
        updateApproveButton();
    }

    function updateApproveButton() {
        var checkedCount = $('.rowCheckbox:checked').length;
        document.getElementById('btnApproveSelected').disabled = checkedCount === 0;
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
        loadRecap();
    });
</script>
