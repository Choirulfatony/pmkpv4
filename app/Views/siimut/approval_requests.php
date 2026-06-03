<style>
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
</style>
<div class="d-flex align-items-center mb-3">
    <ul class="nav nav-tabs mb-0" id="requestTabs">
        <li class="nav-item">
            <a class="nav-link active" id="tabPending" data-toggle="tab" href="#tabPendingContent" onclick="switchTab('pending')">Pending</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="tabHistory" data-toggle="tab" href="#tabHistoryContent" onclick="switchTab('history')">Riwayat</a>
        </li>
    </ul>
</div>

<div id="tabPendingContent">
    <div class="card">
        <div class="card-body">
            <div id="loadingPending" style="display:flex; justify-content:center; align-items:center; min-height:300px; flex-direction:column;">
                <i class="loader" style="display:inline-block; position:relative;"></i>
                <p class="mt-3 text-muted">Memuat data...</p>
            </div>
            <div class="table-responsive" id="tablePendingContainer" style="display:none;">
                <table id="tablePending" class="table table-bordered table-striped table-hover w-100">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal Request</th>
                            <th>Peminta</th>
                            <th>Indikator</th>
                            <th>Departemen</th>
                            <th>Periode</th>
                            <th>Alasan</th>
                            <th>Tindakan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div id="tabHistoryContent" style="display:none">
    <div class="card">
        <div class="card-body">
            <div id="loadingHistory" style="display:flex; justify-content:center; align-items:center; min-height:300px; flex-direction:column;">
                <i class="loader" style="display:inline-block; position:relative;"></i>
                <p class="mt-3 text-muted">Memuat data...</p>
            </div>
            <div class="table-responsive" id="tableHistoryContainer" style="display:none;">
                <table id="tableHistory" class="table table-bordered table-striped table-hover w-100">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal Request</th>
                            <th>Peminta</th>
                            <th>Indikator</th>
                            <th>Departemen</th>
                            <th>Periode</th>
                            <th>Alasan</th>
                            <th>Tindakan</th>
                            <th>Status</th>
                            <th>Diproses Oleh</th>
                            <th>Tanggal Proses</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Alasan Tolak -->
<div class="modal fade" id="modalRejectReason" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Alasan Penolakan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <textarea class="form-control" id="rejectNotes" rows="3" placeholder="Tulis alasan penolakan..."></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" onclick="confirmReject()">Tolak</button>
            </div>
        </div>
    </div>
</div>

<script>
    var tablePending = null;
    var tableHistory = null;
    var selectedRequestId = null;

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

    function loadPendingTable() {
        if (tablePending) tablePending.destroy();
        document.getElementById('loadingPending').style.display = 'flex';
        document.getElementById('tablePendingContainer').style.display = 'none';
        tablePending = $('#tablePending').DataTable({
            processing: false,
            serverSide: false,
            ajax: {
                url: '<?= site_url('siimut/approval/ajax-get-requests-data') ?>',
                type: 'POST',
                dataType: 'json'
            },
            columns: [
                { data: null, render: function(data, type, row, meta) { return meta.row + 1; } },
                { data: 'ar_request_date' },
                { data: 'request_by_name' },
                { data: 'indicator_name' },
                { data: 'department_name' },
                { data: 'ar_period' },
                { data: 'ar_reason' },
                { data: null, render: function(data, type, row) {
                    if (row.ar_action_type === 'delete') return '<span class="badge bg-warning text-dark">Hapus</span>';
                    return '<span class="badge bg-info text-dark">Edit</span>';
                }},
                { data: null, orderable: false, render: function(data, type, row) {
                    return '<button class="btn btn-success btn-sm me-1" onclick="approveRequest(' + row.id + ')"><i class="bi bi-check-circle"></i> Setujui</button>' +
                           '<button class="btn btn-danger btn-sm" onclick="showRejectModal(' + row.id + ')"><i class="bi bi-x-circle"></i> Tolak</button>';
                }}
            ],
            order: [[1, 'desc']],
            initComplete: function() {
                var api = this.api();
                $(api.table().container()).find('.dataTables_filter').append(
                    '<button type="button" class="btn btn-outline-secondary btn-sm ms-2" onclick="reloadData()" title="Muat ulang data"><i class="bi bi-arrow-clockwise"></i></button>'
                );
                document.getElementById('loadingPending').style.display = 'none';
                document.getElementById('tablePendingContainer').style.display = '';
            }
        });
    }

    function loadHistoryTable() {
        if (tableHistory) tableHistory.destroy();
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
                return json.data ? json.data.filter(function(row) { return row.ar_status !== 'pending'; }) : [];
            },
            columns: [
                { data: null, render: function(data, type, row, meta) { return meta.row + 1; } },
                { data: 'ar_request_date' },
                { data: 'request_by_name' },
                { data: 'indicator_name' },
                { data: 'department_name' },
                { data: 'ar_period' },
                { data: 'ar_reason' },
                { data: null, render: function(data, type, row) {
                    if (row.ar_action_type === 'delete') return '<span class="badge bg-warning text-dark">Hapus</span>';
                    return '<span class="badge bg-info text-dark">Edit</span>';
                }},
                { data: null, render: function(data, type, row) {
                    if (row.ar_status === 'approved') return '<span class="badge bg-success">Disetujui</span>';
                    if (row.ar_status === 'completed') return '<span class="badge bg-secondary">Selesai</span>';
                    return '<span class="badge bg-danger">Ditolak</span>';
                }},
                { data: 'approve_by_name' },
                { data: 'ar_approve_date' },
                { data: 'ar_notes' }
            ],
            order: [[1, 'desc']],
            initComplete: function() {
                var api = this.api();
                $(api.table().container()).find('.dataTables_filter').append(
                    '<button type="button" class="btn btn-outline-secondary btn-sm ms-2" onclick="reloadData()" title="Muat ulang data"><i class="bi bi-arrow-clockwise"></i></button>'
                );
                document.getElementById('loadingHistory').style.display = 'none';
                document.getElementById('tableHistoryContainer').style.display = '';
            }
        });
    }

    function approveRequest(id) {
        Swal.fire({
            title: 'Setujui permintaan?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, setujui',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#28a745'
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
                    if (resp.status) { loadPendingTable(); if (tableHistory) loadHistoryTable(); }
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
        if (!notes) {
            toastAlert('warning', 'Alasan penolakan harus diisi');
            return;
        }
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

    document.addEventListener('DOMContentLoaded', function() {
        loadPendingTable();
    });

    function toastAlert(icon, msg) {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: icon,
            title: msg,
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
    }
</script>