<div class="d-flex align-items-center mb-3 flex-wrap gap-2">
    <ul class="nav nav-tabs mb-0" id="requestTabs">
        <li class="nav-item">
            <a class="nav-link active" id="tabPending" href="#tabPendingContent" onclick="switchTab('pending');return false;">Pending</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="tabHistory" href="#tabHistoryContent" onclick="switchTab('history');return false;">Riwayat</a>
        </li>
    </ul>
    <button type="button" class="btn btn-outline-secondary btn-sm ms-auto" onclick="reloadData()" title="Muat ulang data">
        <i class="bi bi-arrow-clockwise"></i> Refresh
    </button>
</div>

<div id="tabPendingContent">
    <div class="card shadow-sm">
        <div class="card-body p-2 p-md-3">
            <div id="loadingPending" style="display:flex; justify-content:center; align-items:center; min-height:200px; flex-direction:column;">
                <i class="loader" style="display:inline-block; position:relative;"></i>
                <p class="mt-3 text-muted">Memuat data...</p>
            </div>
            <div id="tablePendingContainer" class="table-responsive" style="display:none;">
                <table id="tablePending" class="table table-striped" style="width:100%;">
                    <thead>
                        <tr class="header-row">
                            <th class="text-center" style="width:1px;">No</th>
                            <th>Peminta</th>
                            <th>Indikator</th>
                            <th>Group</th>
                            <th class="text-center">Tindakan</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div id="tabHistoryContent" style="display:none">
    <div class="card shadow-sm">
        <div class="card-body p-2 p-md-3">
            <div id="loadingHistory" style="display:flex; justify-content:center; align-items:center; min-height:200px; flex-direction:column;">
                <i class="loader" style="display:inline-block; position:relative;"></i>
                <p class="mt-3 text-muted">Memuat data...</p>
            </div>
            <div id="tableHistoryContainer" class="table-responsive" style="display:none;">
                <table id="tableHistory" class="table table-striped" style="width:100%;">
                    <thead>
                        <tr class="header-row">
                            <th class="text-center">No</th>
                            <th>Peminta</th>
                            <th>Indikator</th>
                            <th>Group</th>
                            <th class="text-center">Tindakan</th>
                            <th class="text-center">Status</th>
                            <th>Diproses</th>
                            <th class="text-center">Detail</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail -->
<div class="modal fade" id="modalDetail" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title"><i class="bi bi-info-circle me-1"></i> Detail Permintaan</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <table class="table table-sm table-borderless mb-0">
                    <tr><td class="text-muted small" style="width:100px;">Tgl Request</td><td id="dt-tgl">-</td></tr>
                    <tr><td class="text-muted small">Peminta</td><td id="dt-peminta">-</td></tr>
                    <tr><td class="text-muted small">Indikator</td><td id="dt-indikator">-</td></tr>
                    <tr><td class="text-muted small">Departemen</td><td id="dt-departemen">-</td></tr>
                    <tr><td class="text-muted small">Group</td><td id="dt-group">-</td></tr>
                    <tr><td class="text-muted small">Periode Mulai</td><td id="dt-mulai">-</td></tr>
                    <tr><td class="text-muted small">Periode Selesai</td><td id="dt-selesai">-</td></tr>
                    <tr><td class="text-muted small">Alasan</td><td id="dt-alasan">-</td></tr>
                    <tr><td class="text-muted small">Tindakan</td><td id="dt-tindakan">-</td></tr>
                    <tr><td class="text-muted small">Status</td><td id="dt-status">-</td></tr>
                </table>
            </div>
            <div class="modal-footer" id="dt-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Alasan Tolak -->
<div class="modal fade" id="modalRejectReason" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title"><i class="bi bi-x-circle text-danger me-1"></i> Alasan Penolakan</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <textarea class="form-control" id="rejectNotes" rows="3" placeholder="Tulis alasan penolakan..."></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-sm btn-danger" onclick="confirmReject()"><i class="bi bi-hand-thumbs-down"></i> Tolak</button>
            </div>
        </div>
    </div>
</div>

<script>
    var tablePending = null;
    var tableHistory = null;
    var selectedRequestId = null;
    var _pendingData = [];
    var _historyData = [];

    function reloadData() {
        loadPendingTable();
        if (tableHistory) loadHistoryTable();
    }

    function switchTab(tab) {
        document.getElementById('tabPending').classList.toggle('active', tab === 'pending');
        document.getElementById('tabHistory').classList.toggle('active', tab === 'history');
        document.getElementById('tabPendingContent').style.display = tab === 'pending' ? '' : 'none';
        document.getElementById('tabHistoryContent').style.display = tab === 'history' ? '' : 'none';
        if (tab === 'history' && !tableHistory) loadHistoryTable();
    }

    function groupLabel(grpType) {
        if (grpType == 1) return 'INM';
        if (grpType == 5) return 'IMPRS';
        if (grpType == 6) return 'IMPUNIT';
        if (grpType == 7) return 'IKP';
        return 'Lainnya';
    }

    function actionBadge(actionType) {
        if (actionType === 'delete') return '<span class="badge bg-warning text-dark"><i class="bi bi-trash me-1"></i>Hapus</span>';
        if (actionType === 'open_period') return '<span class="badge bg-primary"><i class="bi bi-unlock me-1"></i>Buka Periode</span>';
        return '<span class="badge bg-info text-dark"><i class="bi bi-pencil me-1"></i>Edit</span>';
    }

    function statusBadge(status) {
        if (status === 'approved') return '<span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Disetujui</span>';
        if (status === 'completed') return '<span class="badge bg-secondary"><i class="bi bi-check2-all me-1"></i>Selesai</span>';
        if (status === 'rejected') return '<span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Ditolak</span>';
        return '<span class="badge bg-secondary">' + (status || '-') + '</span>';
    }

    function showDetail(idx) {
        var row = _pendingData[idx];
        if (!row) return;
        document.getElementById('dt-tgl').textContent = row.ar_request_date || '-';
        document.getElementById('dt-peminta').textContent = row.request_by_name || '-';
        document.getElementById('dt-indikator').textContent = row.indicator_name || '-';
        document.getElementById('dt-departemen').textContent = row.department_name || '-';
        document.getElementById('dt-group').textContent = groupLabel(row.ar_group_type);
        document.getElementById('dt-mulai').textContent = row.ar_period || '-';
        document.getElementById('dt-selesai').textContent = row.ar_period_end || '-';
        document.getElementById('dt-alasan').textContent = row.ar_reason || '-';
        document.getElementById('dt-tindakan').innerHTML = actionBadge(row.ar_action_type);

        var footer = document.getElementById('dt-footer');
        footer.innerHTML = '<button type="button" class="btn btn-sm btn-outline-success" onclick="approveRequest(' + row.id + ')"><i class="bi bi-check-circle-fill"></i> Setujui</button>' +
                           '<button type="button" class="btn btn-sm btn-outline-danger" onclick="bootstrap.Modal.getInstance(document.getElementById(\'modalDetail\')).hide();showRejectModal(' + row.id + ')"><i class="bi bi-x-circle-fill"></i> Tolak</button>';

        var modal = new bootstrap.Modal(document.getElementById('modalDetail'));
        modal.show();
    }

    function showHistoryDetail(idx) {
        var row = _historyData[idx];
        if (!row) return;
        document.getElementById('dt-tgl').textContent = row.ar_request_date || '-';
        document.getElementById('dt-peminta').textContent = row.request_by_name || '-';
        document.getElementById('dt-indikator').textContent = row.indicator_name || '-';
        document.getElementById('dt-departemen').textContent = row.department_name || '-';
        document.getElementById('dt-group').textContent = groupLabel(row.ar_group_type);
        document.getElementById('dt-mulai').textContent = row.ar_period || '-';
        document.getElementById('dt-selesai').textContent = row.ar_period_end || '-';
        document.getElementById('dt-alasan').textContent = row.ar_reason || '-';
        document.getElementById('dt-tindakan').innerHTML = actionBadge(row.ar_action_type);
        document.getElementById('dt-status').innerHTML = statusBadge(row.ar_status);

        document.getElementById('dt-footer').innerHTML = '<button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Tutup</button>';

        var modal = new bootstrap.Modal(document.getElementById('modalDetail'));
        modal.show();
    }

    function loadPendingTable() {
        if (tablePending) { tablePending.destroy(); tablePending = null; }
        document.getElementById('loadingPending').style.display = 'flex';
        document.getElementById('tablePendingContainer').style.display = 'none';
        tablePending = $('#tablePending').DataTable({
            autoWidth: true,
            processing: false,
            serverSide: false,
            ajax: {
                url: '<?= site_url('siimut/approval/ajax-get-requests-data') ?>',
                type: 'POST',
                dataType: 'json',
                dataSrc: function(json) { _pendingData = json.data || []; return _pendingData; }
            },
            columns: [
                { data: null, className: 'dt-center', render: function(data, type, row, meta) { return meta.row + 1; } },
                { data: 'request_by_name' },
                { data: 'indicator_name', render: function(data) { return data || '-'; } },
                { data: null, className: 'dt-center', render: function(data, type, row) { return groupLabel(row.ar_group_type); } },
                { data: null, className: 'dt-center', render: function(data, type, row) { return actionBadge(row.ar_action_type); } },
                { data: null, className: 'dt-center', orderable: false, render: function(data, type, row, meta) {
                    var idx = meta.row;
                    return '<button class="btn btn-outline-info btn-sm py-0 px-1" onclick="showDetail(' + idx + ')" title="Lihat"><i class="bi bi-eye"></i></button>';
                }}
            ],
            order: [[0, 'asc']],
            columnDefs: [
                { targets: 0, width: '5px' }
            ],
            language: {
                search: 'Cari:', searchPlaceholder: 'Ketik kata kunci...', lengthMenu: 'Tampilkan _MENU_',
                info: '_START_ - _END_ dari _TOTAL_', infoEmpty: '0 - 0 dari 0', infoFiltered: '(difilter dari _MAX_ total)',
                zeroRecords: 'Tidak ada data', emptyTable: 'Tidak ada permintaan pending',
                paginate: { first: 'Awal', last: 'Akhir', next: '<i class="bi bi-chevron-right"></i>', previous: '<i class="bi bi-chevron-left"></i>' }
            },
            initComplete: function() {
                document.getElementById('loadingPending').style.display = 'none';
                document.getElementById('tablePendingContainer').style.display = '';
            }
        });
    }

    function loadHistoryTable() {
        if (tableHistory) { tableHistory.destroy(); tableHistory = null; }
        document.getElementById('loadingHistory').style.display = 'flex';
        document.getElementById('tableHistoryContainer').style.display = 'none';
        tableHistory = $('#tableHistory').DataTable({
            processing: false,
            serverSide: false,
            ajax: {
                url: '<?= site_url('siimut/approval/ajax-get-all-requests-data') ?>',
                type: 'POST',
                dataType: 'json'
            },
            dataSrc: function(json) {
                _historyData = json.data ? json.data.filter(function(row) { return row.ar_status !== 'pending'; }) : [];
                return _historyData;
            },
            columns: [
                { data: null, className: 'dt-center', render: function(data, type, row, meta) { return meta.row + 1; } },
                { data: 'request_by_name' },
                { data: 'indicator_name', render: function(data) { return data || '-'; } },
                { data: null, className: 'dt-center', render: function(data, type, row) { return groupLabel(row.ar_group_type); } },
                { data: null, className: 'dt-center', render: function(data, type, row) { return actionBadge(row.ar_action_type); } },
                { data: null, className: 'dt-center', render: function(data, type, row) { return statusBadge(row.ar_status); } },
                { data: 'approve_by_name', render: function(data) { return data || '-'; } },
                { data: null, className: 'dt-center', orderable: false, render: function(data, type, row, meta) {
                    return '<button class="btn btn-outline-info btn-sm py-0 px-1" onclick="showHistoryDetail(' + meta.row + ')" title="Lihat"><i class="bi bi-eye"></i></button>';
                }}
            ],
            order: [[0, 'asc']],
            columnDefs: [
                { targets: 0, width: '5px' }
            ],
            language: {
                search: 'Cari:', searchPlaceholder: 'Ketik kata kunci...', lengthMenu: 'Tampilkan _MENU_',
                info: '_START_ - _END_ dari _TOTAL_', infoEmpty: '0 - 0 dari 0', infoFiltered: '(difilter dari _MAX_ total)',
                zeroRecords: 'Tidak ada data', emptyTable: 'Belum ada riwayat',
                paginate: { first: 'Awal', last: 'Akhir', next: '<i class="bi bi-chevron-right"></i>', previous: '<i class="bi bi-chevron-left"></i>' }
            },
            initComplete: function() {
                document.getElementById('loadingHistory').style.display = 'none';
                document.getElementById('tableHistoryContainer').style.display = '';
            }
        });
    }

    function approveRequest(id) {
        Swal.fire({
            title: 'Setujui permintaan?',
            text: 'Data akan diproses sesuai tindakan yang diminta.',
            icon: 'question', showCancelButton: true,
            confirmButtonText: '<i class="bi bi-check-circle"></i> Ya, setujui',
            cancelButtonText: 'Batal', confirmButtonColor: '#28a745'
        }).then(function(result) {
            if (!result.isConfirmed) return;
            var xhr = new XMLHttpRequest();
            xhr.open('POST', '<?= site_url('siimut/approval/ajax-approve-request') ?>', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    var resp = JSON.parse(xhr.responseText);
                    toastAlert(resp.status ? 'success' : 'error', resp.message);
                    if (resp.status) {
                        bootstrap.Modal.getInstance(document.getElementById('modalDetail'))?.hide();
                        loadPendingTable();
                        if (tableHistory) loadHistoryTable();
                    }
                }
            };
            xhr.send('id=' + id);
        });
    }

    function showRejectModal(id) {
        selectedRequestId = id;
        document.getElementById('rejectNotes').value = '';
        var modal = new bootstrap.Modal(document.getElementById('modalRejectReason'));
        modal.show();
    }

    function confirmReject() {
        var notes = document.getElementById('rejectNotes').value.trim();
        if (!notes) { toastAlert('warning', 'Alasan penolakan harus diisi'); return; }
        var xhr = new XMLHttpRequest();
        xhr.open('POST', '<?= site_url('siimut/approval/ajax-reject-request') ?>', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                var resp = JSON.parse(xhr.responseText);
                toastAlert(resp.status ? 'success' : 'error', resp.message);
                if (resp.status) {
                    bootstrap.Modal.getInstance(document.getElementById('modalRejectReason')).hide();
                    loadPendingTable();
                    if (tableHistory) loadHistoryTable();
                }
            }
        };
        xhr.send('id=' + selectedRequestId + '&notes=' + encodeURIComponent(notes));
    }

    document.addEventListener('DOMContentLoaded', function() { loadPendingTable(); });

    function toastAlert(icon, msg) {
        Swal.fire({ toast: true, position: 'top-end', icon: icon, title: msg, showConfirmButton: false, timer: 3000, timerProgressBar: true });
    }
</script>
