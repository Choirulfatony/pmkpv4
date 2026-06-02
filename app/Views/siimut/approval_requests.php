<?= $this->extend('_layout/_template') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table id="tableApprovalRequests" class="table table-bordered table-striped table-hover w-100">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal Request</th>
                        <th>Peminta</th>
                        <th>Indikator ID</th>
                        <th>Departemen</th>
                        <th>Periode</th>
                        <th>Alasan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
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
    var tableRequests = null;
    var selectedRequestId = null;

    function loadTable() {
        if (tableRequests) {
            tableRequests.destroy();
        }

        tableRequests = $('#tableApprovalRequests').DataTable({
            processing: true,
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
                { data: 'ar_indicator_id' },
                { data: 'ar_department_id' },
                { data: 'ar_period' },
                { data: 'ar_reason' },
                { data: null, orderable: false, render: function(data, type, row) {
                    return '<button class="btn btn-success btn-sm me-1" onclick="approveRequest(' + row.id + ')"><i class="bi bi-check-circle"></i> Setujui</button>' +
                           '<button class="btn btn-danger btn-sm" onclick="showRejectModal(' + row.id + ')"><i class="bi bi-x-circle"></i> Tolak</button>';
                }}
            ],
            order: [[1, 'desc']]
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
                    Swal.fire({ title: resp.status ? 'Berhasil' : 'Gagal', text: resp.message, icon: resp.status ? 'success' : 'error' });
                    if (resp.status) loadTable();
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
            Swal.fire({ title: 'Info', text: 'Alasan penolakan harus diisi', icon: 'warning' });
            return;
        }

        var xhr = new XMLHttpRequest();
        xhr.open('POST', '<?= site_url('siimut/approval/ajax-reject-request') ?>', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                var resp = JSON.parse(xhr.responseText);
                Swal.fire({ title: resp.status ? 'Berhasil' : 'Gagal', text: resp.message, icon: resp.status ? 'success' : 'error' });
                if (resp.status) {
                    bootstrap.Modal.getInstance(document.getElementById('modalRejectReason')).hide();
                    loadTable();
                }
            }
        };
        xhr.send('id=' + selectedRequestId + '&notes=' + encodeURIComponent(notes));
    }

    document.addEventListener('DOMContentLoaded', function() {
        loadTable();
    });
</script>
<?= $this->endSection() ?>
