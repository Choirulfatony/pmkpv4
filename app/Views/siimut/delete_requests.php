<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h5 class="mb-0">
                        <i class="bi bi-exclamation-triangle me-1"></i>Permintaan Hapus Dokumen
                    </h5>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-outline border-warning">
        <div class="card-header bg-warning bg-opacity-10 p-0">
            <ul class="nav nav-tabs card-header-tabs m-0 border-0">
                <li class="nav-item">
                    <a class="nav-link active px-3 py-2" id="tabPending" href="#tabPendingContent" onclick="switchTab('pending');return false;">
                        <i class="bi bi-hourglass-split me-1"></i>Pending
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-3 py-2" id="tabHistory" href="#tabHistoryContent" onclick="switchTab('history');return false;">
                        <i class="bi bi-clock-history me-1"></i>Riwayat
                    </a>
                </li>
            </ul>
        </div>
        <div class="card-body p-0">
            <div id="tabPendingContent">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="pendingRequestsTable" role="table">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">File</th>
                                <th scope="col" style="width:100px">Pemohon</th>
                                <th scope="col">Alasan</th>
                                <th scope="col" style="width:160px">Tanggal</th>
                                <th scope="col" style="width:140px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <div id="tabHistoryContent" style="display:none">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="historyTable" role="table">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">File</th>
                                <th scope="col" style="width:100px">Pemohon</th>
                                <th scope="col" style="width:90px">Status</th>
                                <th scope="col" style="width:120px">Diproses</th>
                                <th scope="col" style="width:160px">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Reject -->
<div class="modal fade" id="rejectModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-secondary text-white">
                <h6 class="modal-title"><i class="bi bi-x-circle me-2"></i>Tolak Permintaan</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formReject">
                <div class="modal-body">
                    <input type="hidden" name="id" value="">
                    <div class="mb-0">
                        <label class="form-label small fw-semibold">Catatan <span class="text-danger">*</span></label>
                        <textarea name="notes" class="form-control form-control-sm" rows="2" placeholder="Alasan penolakan" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-secondary">Tolak</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
var activeTab = 'pending';

function switchTab(tab) {
    activeTab = tab;
    document.getElementById('tabPending').classList.toggle('active', tab === 'pending');
    document.getElementById('tabHistory').classList.toggle('active', tab === 'history');
    document.getElementById('tabPendingContent').style.display = tab === 'pending' ? '' : 'none';
    document.getElementById('tabHistoryContent').style.display = tab === 'history' ? '' : 'none';
    if (tab === 'history') loadHistory();
}


function escHtml(str) {
    return $('<div>').text(str || '').html();
}

function loadHistory() {
    $.ajax({
        url: '<?= site_url('siimut/dokumen-mutu/ajax-get-delete-history') ?>',
        type: 'POST',
        success: function(res) {
            if (!res.status || !res.data.length) {
                $('#historyTable tbody').html('<tr><td colspan="5" class="text-center text-muted py-4"><i class="bi bi-inbox fs-3 d-block mb-2"></i>Belum ada riwayat</td></tr>');
                return;
            }
            var tbody = $('#historyTable tbody').empty();
            $.each(res.data, function(i, req) {
                var name = req.file_name || '(dihapus)';
                var typeLabel = req.is_folder == '1' ? 'Folder' : 'File';
                var statusBadge = req.fmr_status == 'approved'
                    ? '<span class="badge bg-success">Disetujui</span>'
                    : (req.fmr_status == 'rejected'
                        ? '<span class="badge bg-danger">Ditolak</span>'
                        : '<span class="badge bg-warning text-dark">Pending</span>');
                tbody.append('<tr>\n' +
                    '  <td>' + escHtml(name) + ' <span class="badge bg-secondary">' + typeLabel + '</span></td>\n' +
                    '  <td>' + escHtml(req.request_by_name) + '</td>\n' +
                    '  <td>' + statusBadge + '</td>\n' +
                    '  <td>' + escHtml(req.approve_by_name) + '</td>\n' +
                    '  <td>' + req.fmr_approve_date + '</td>\n' +
                    '</tr>');
            });
        }
    });
}

$(document).ready(function() {
    function loadPendingRequests() {
        $.ajax({
            url: '<?= site_url('siimut/dokumen-mutu/ajax-get-delete-requests') ?>',
            type: 'POST',
            success: function(res) {
                if (!res.status || !res.data.length) {
                    $('#pendingRequestsTable tbody').html('<tr><td colspan="5" class="text-center text-muted py-4"><i class="bi bi-inbox fs-3 d-block mb-2"></i>Tidak ada permintaan</td></tr>');
                    return;
                }
                var tbody = $('#pendingRequestsTable tbody').empty();
                $.each(res.data, function(i, req) {
                    var name = req.file_name || '(dihapus)';
                    var typeLabel = req.is_folder == '1' ? 'Folder' : 'File';
                    tbody.append('<tr>\n' +
                        '  <td>' + escHtml(name) + ' <span class="badge bg-secondary">' + typeLabel + '</span></td>\n' +
                        '  <td>' + escHtml(req.request_by_name) + '</td>\n' +
                        '  <td>' + escHtml(req.fmr_reason) + '</td>\n' +
                        '  <td>' + req.fmr_request_date + '</td>\n' +
                        '  <td>\n' +
                        '    <button class="btn btn-sm btn-outline-success me-1 btn-approve-req" data-id="' + req.id + '" title="Setujui"><i class="bi bi-check-lg"></i></button>\n' +
                        '    <button class="btn btn-sm btn-outline-danger btn-reject-req" data-id="' + req.id + '" title="Tolak"><i class="bi bi-x-lg"></i></button>\n' +
                        '  </td>\n' +
                        '</tr>');
                });
            }
        });
    }

    $(document).on('click', '.btn-approve-req', function() {
        var id = $(this).data('id');
        Swal.fire({
            title: 'Setujui penghapusan?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            confirmButtonText: 'Ya, Setujui',
            cancelButtonText: 'Batal'
        }).then(function(result) {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= site_url('siimut/dokumen-mutu/approve-delete-request') ?>',
                    type: 'POST',
                    data: { id: id },
                    success: function(res) {
                        if (res.status) { toastSuccess(res.message); loadPendingRequests(); }
                        else { toastError(res.message); }
                    },
                    error: function() { toastError('Gagal menyetujui'); }
                });
            }
        });
    });

    $(document).on('click', '.btn-reject-req', function() {
        var id = $(this).data('id');
        $('#formReject input[name="id"]').val(id);
        $('#rejectModal').modal('show');
    });

    $('#formReject').on('submit', function(e) {
        e.preventDefault();
        var btn = $(this).find('button[type="submit"]');
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>');
        $.ajax({
            url: '<?= site_url('siimut/dokumen-mutu/reject-delete-request') ?>',
            type: 'POST',
            data: $(this).serialize(),
            success: function(res) {
                if (res.status) {
                    toastSuccess(res.message);
                    $('#rejectModal').modal('hide');
                    loadPendingRequests();
                } else {
                    toastError(res.message);
                }
            },
            error: function() { toastError('Gagal menolak'); },
            complete: function() { btn.prop('disabled', false).text('Tolak'); }
        });
    });

    $('#rejectModal').on('hidden.bs.modal', function() {
        $(this).find('form')[0].reset();
    });

    loadPendingRequests();
    setInterval(loadPendingRequests, 30000);
});
</script>
