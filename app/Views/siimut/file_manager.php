<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h5 class="mb-0">
                        <i class="bi bi-folder2 me-1"></i>Dokumen Mutu
                    </h5>
                    <small class="text-muted">Kelola dokumen, SOP, pedoman, eviden, dan laporan mutu.</small>
                    <?php if (!empty($breadcrumbs)): ?>
                    <nav style="--bs-breadcrumb-divider: '>'" class="mt-1">
                        <ol class="breadcrumb small mb-0">
                            <li class="breadcrumb-item"><a href="<?= site_url('siimut/dokumen-mutu') ?>" class="text-decoration-none">Dokumen Mutu</a></li>
                            <?php foreach ($breadcrumbs as $crumb): ?>
                            <li class="breadcrumb-item <?= $crumb->id === ($currentFolder->id ?? null) ? 'active' : '' ?>">
                                <?php if ($crumb->id !== ($currentFolder->id ?? null)): ?>
                                <a href="<?= site_url('siimut/dokumen-mutu?folder=' . $crumb->id) ?>" class="text-decoration-none"><?= esc($crumb->file_name) ?></a>
                                <?php else: ?>
                                <?= esc($crumb->file_name) ?>
                                <?php endif; ?>
                            </li>
                            <?php endforeach; ?>
                        </ol>
                    </nav>
                    <?php endif; ?>
                </div>
                <div class="d-flex gap-2">
                    <?php if ($currentFolder ?? null): ?>
                    <a href="<?= site_url('siimut/dokumen-mutu' . ($currentFolder->parent_id ? '?folder=' . $currentFolder->parent_id : '')) ?>" class="btn btn-sm btn-outline-secondary" title="Kembali">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                    <?php endif; ?>
                    <?php if (in_array(session('user_role'), ['ADMINISTRATOR', 'KOMITE', 'KENDALI_MUTU'])): ?>
                    <button class="btn btn-outline-secondary btn-sm" id="btnOpenFolder">
                        <i class="bi bi-folder-plus me-1"></i>Folder Baru
                    </button>
                    <?php endif; ?>
                    <?php if (session('user_role') === 'KENDALI_MUTU'): ?>
                    <button class="btn btn-outline-info btn-sm" onclick="openRiwayatModal()">
                        <i class="bi bi-clock-history me-1"></i>Riwayat
                    </button>
                    <?php endif; ?>
                    <button class="btn btn-danger btn-sm" id="btnOpenUpload">
                        <i class="bi bi-cloud-upload me-1"></i>Upload Dokumen
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-outline">
        <div class="card-body p-0">
            <div class="table-responsive-lg">
                <table id="fileTable" class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nama Dokumen</th>
                            <th style="width:80px">Jenis</th>
                            <th style="width:90px">Ukuran</th>
                            <th class="d-none d-lg-table-cell" style="width:150px">Diunggah Oleh</th>
                            <th class="d-none d-sm-table-cell" style="width:140px">Tanggal Upload</th>
                            <th class="d-none d-md-table-cell">Keterangan</th>
                            <th style="width:130px" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                        <tr>
                            <td>
                                <?php if ($item->is_folder): ?>
                                <i class="bi bi-folder-fill text-warning me-1 fs-5"></i>
                                <a href="<?= site_url('siimut/dokumen-mutu?folder=' . $item->id) ?>" class="text-decoration-none fw-medium"><?= esc($item->file_name) ?></a>
                                <?php else: ?>
                                <i class="bi bi-file-earmark-text text-primary me-1 fs-5"></i>
                                <?= esc($item->file_name) ?>
                                <?php endif; ?>
                            </td>
                            <td><?= $item->is_folder ? '<span class="badge bg-secondary"><i class="bi bi-folder me-1"></i>' . (int) $item->child_count . ' Dokumen</span>' : strtoupper(pathinfo($item->file_name, PATHINFO_EXTENSION)) ?></td>
                            <td><?= $item->is_folder ? '-' : $item->size_formatted ?></td>
                            <td class="small d-none d-lg-table-cell"><?= $item->uploader_name ?: '-' ?></td>
                            <td class="small d-none d-sm-table-cell"><?= $item->created_at ? date('d/m/Y H:i', strtotime($item->created_at)) : '-' ?></td>
                            <td class="small text-muted d-none d-md-table-cell" style="max-width:200px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;"><?= esc($item->description ?? '-') ?></td>
                            <td class="text-nowrap text-center">
                                <div class="d-flex gap-1 justify-content-center">
                                <?php if ($item->is_folder && in_array(session('user_role'), ['ADMINISTRATOR', 'KOMITE', 'KENDALI_MUTU'])): ?>
                                <button class="btn btn-sm btn-outline-secondary btn-rename-folder" data-id="<?= $item->id ?>" data-name="<?= esc($item->file_name, 'attr') ?>" title="Ubah Nama"><i class="bi bi-pencil"></i></button>
                                <?php elseif (!$item->is_folder): ?>
                                <a href="<?= site_url('siimut/dokumen-mutu/download/' . $item->id) ?>" class="btn btn-sm btn-outline-info" title="Download"><i class="bi bi-download"></i></a>
                                <?php $ext = strtolower(pathinfo($item->file_name, PATHINFO_EXTENSION)); ?>
                                <?php if (in_array($ext, ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'webp', 'txt', 'csv'])): ?>
                                <a href="<?= base_url('uploads/file_manager/' . $item->file_path) ?>" class="btn btn-sm btn-outline-primary" target="_blank" title="View"><i class="bi bi-eye"></i></a>
                                <?php elseif ($ext === 'docx'): ?>
                                <a href="https://docs.google.com/gview?url=<?= urlencode(base_url('uploads/file_manager/' . $item->file_path)) ?>" target="_blank" class="btn btn-sm btn-outline-primary" title="Preview via Google"><i class="bi bi-eye"></i></a>
                                <?php else: ?>
                                <a href="<?= site_url('siimut/dokumen-mutu/download/' . $item->id) ?>" class="btn btn-sm btn-outline-primary" title="Download & Buka"><i class="bi bi-eye"></i></a>
                                <?php endif; ?>
                                <?php endif; ?>
                                <button class="btn btn-sm btn-outline-warning btn-request-delete" data-id="<?= $item->id ?>" data-type="<?= $item->is_folder ? 'folder' : 'file' ?>" title="Minta Hapus"><i class="bi bi-send"></i></button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($items)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">
                                <i class="bi bi-folder2-open fs-1 d-block mb-3"></i>
                                <p class="mb-1 fw-medium">Folder ini masih kosong</p>
                                <small>Klik "Upload Dokumen" atau "Folder Baru" untuk mulai menyimpan dokumen.</small>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Buat Folder -->
<div class="modal fade" id="folderModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-secondary text-white">
                <h6 class="modal-title"><i class="bi bi-folder-plus me-2"></i>Folder Baru</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formFolder">
                <div class="modal-body">
                    <input type="hidden" name="parent_id" value="<?= $currentFolder->id ?? '' ?>">
                    <div class="mb-0">
                        <label class="form-label small fw-semibold">Nama Folder Baru</label>
                        <input type="text" name="folder_name" class="form-control form-control-sm" placeholder="Masukkan nama folder" required autocomplete="off">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-dark">Buat</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Upload -->
<div class="modal fade" id="uploadModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h6 class="modal-title"><i class="bi bi-cloud-upload me-2"></i>Upload Dokumen</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formUpload" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="parent_id" value="<?= $currentFolder->id ?? '' ?>">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Pilih File <span class="text-danger">*</span></label>
                        <input type="file" name="file" class="form-control form-control-sm" required>
                        <small class="text-muted">Maks: 12MB</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Keterangan <span class="text-danger">*</span></label>
                        <textarea name="description" class="form-control form-control-sm" rows="2" placeholder="Jelaskan isi dokumen" required></textarea>
                    </div>
                    <div id="uploadProgress" class="progress d-none" style="height:6px;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" style="width:0%"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-sm btn-danger" id="btnUpload">
                        <i class="bi bi-cloud-upload me-1"></i>Upload
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Request Delete -->
<div class="modal fade" id="requestDeleteModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h6 class="modal-title"><i class="bi bi-send me-2"></i>Minta Hapus</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formRequestDelete">
                <div class="modal-body">
                    <input type="hidden" name="file_id" value="">
                    <div class="mb-0">
                        <label class="form-label small fw-semibold">Alasan <span class="text-danger">*</span></label>
                        <textarea name="reason" class="form-control form-control-sm" rows="3" placeholder="Jelaskan alasan mengapa file perlu dihapus" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-warning" id="btnRequestDelete">
                        <i class="bi bi-send me-1"></i>Kirim Permintaan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Rename Folder -->
<div class="modal fade" id="renameModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-secondary text-white">
                <h6 class="modal-title"><i class="bi bi-pencil me-2"></i>Ubah Nama</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formRename">
                <div class="modal-body">
                    <input type="hidden" name="folder_id" value="">
                    <div class="mb-0">
                        <label class="form-label small fw-semibold">Nama Folder <span class="text-danger">*</span></label>
                        <input type="text" name="folder_name" class="form-control form-control-sm" required maxlength="255">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-secondary" id="btnRename">
                        <i class="bi bi-check me-1"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Modal Riwayat -->
<div class="modal fade" id="riwayatModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-secondary text-white">
                <h6 class="modal-title"><i class="bi bi-clock-history me-2"></i>Riwayat Permintaan Hapus</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="riwayatTable">
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

<script>
function escHtml(str) {
    return $('<div>').text(str || '').html();
}

function openRiwayatModal() {
    $('#riwayatModal').modal('show');
    $('#riwayatModal').off('shown.bs.modal').on('shown.bs.modal', function() {
        $.ajax({
            url: '<?= site_url('siimut/dokumen-mutu/ajax-get-delete-history') ?>',
            type: 'POST',
            success: function(res) {
                if (!res.status || !res.data.length) {
                    $('#riwayatTable tbody').html('<tr><td colspan="5" class="text-center text-muted py-4"><i class="bi bi-inbox fs-3 d-block mb-2"></i>Belum ada riwayat</td></tr>');
                    return;
                }
                var tbody = $('#riwayatTable tbody').empty();
                $.each(res.data, function(i, req) {
                    var name = req.file_name || '(dihapus)';
                    var typeLabel = req.is_folder == '1' ? 'Folder' : 'File';
                    var statusBadge = req.fmr_status == 'approved'
                        ? '<span class="badge bg-success">Disetujui</span>'
                        : '<span class="badge bg-danger">Ditolak</span>';
                    tbody.append('<tr><td>' + escHtml(name) + ' <span class="badge bg-secondary">' + typeLabel + '</span></td><td>' + escHtml(req.request_by_name) + '</td><td>' + statusBadge + '</td><td>' + escHtml(req.approve_by_name) + '</td><td>' + req.fmr_approve_date + '</td></tr>');
                });
            }
        });
    });
}

$(document).ready(function() {
    var uploadModal = new bootstrap.Modal(document.getElementById('uploadModal'), {
        backdrop: 'static',
        keyboard: false
    });

    var folderModal = new bootstrap.Modal(document.getElementById('folderModal'), {
        backdrop: 'static',
        keyboard: false
    });

    $('#btnOpenUpload').on('click', function() {
        $('#formUpload')[0].reset();
        $('#uploadProgress').addClass('d-none');
        uploadModal.show();
    });

    $('#btnOpenFolder').on('click', function() {
        folderModal.show();
    });

    $('#formFolder').on('submit', function(e) {
        e.preventDefault();
        var btn = $(this).find('button[type="submit"]');
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Membuat...');
        $.ajax({
            url: '<?= site_url('siimut/dokumen-mutu/create-folder') ?>',
            type: 'POST',
            data: $(this).serialize(),
            success: function(res) {
                if (res.status) {
                    toastSuccess(res.message);
                    folderModal.hide();
                    setTimeout(function() { location.reload(); }, 500);
                } else {
                    toastError(res.message);
                }
            },
            error: function() { toastError('Gagal membuat folder'); },
            complete: function() { btn.prop('disabled', false).text('Buat'); }
        });
    });

    $('#formUpload').on('submit', function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        var btn = $('#btnUpload');
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Mengupload...');
        $('#uploadProgress').removeClass('d-none');

        $.ajax({
            url: '<?= site_url('siimut/dokumen-mutu/upload') ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            xhr: function() {
                var xhr = new XMLHttpRequest();
                xhr.upload.onprogress = function(e) {
                    if (e.lengthComputable) {
                        $('#uploadProgress .progress-bar').css('width', Math.round(e.loaded / e.total * 100) + '%');
                    }
                };
                return xhr;
            },
            success: function(res) {
                if (res.status) {
                    toastSuccess(res.message);
                    uploadModal.hide();
                    setTimeout(function() { location.reload(); }, 500);
                } else {
                    toastError(res.message);
                }
            },
            error: function() { toastError('Gagal upload file'); },
            complete: function() {
                btn.prop('disabled', false).html('<i class="bi bi-cloud-upload me-1"></i>Upload');
                $('#uploadProgress').addClass('d-none').find('.progress-bar').css('width', '0%');
            }
        });
    });

    $('#uploadModal, #folderModal').on('hidden.bs.modal', function() {
        $(this).find('form')[0].reset();
        $('#uploadProgress').addClass('d-none').find('.progress-bar').css('width', '0%');
    });

    $(document).on('click', '.btn-request-delete', function() {
        var id = $(this).data('id');
        $('#formRequestDelete input[name="file_id"]').val(id);
        $('#requestDeleteModal').modal('show');
    });

    $('#formRequestDelete').on('submit', function(e) {
        e.preventDefault();
        var btn = $('#btnRequestDelete');
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Mengirim...');
        $.ajax({
            url: '<?= site_url('siimut/dokumen-mutu/request-delete') ?>',
            type: 'POST',
            data: $(this).serialize(),
            success: function(res) {
                if (res.status) {
                    toastSuccess(res.message);
                } else {
                    toastError(res.message);
                }
                $('#requestDeleteModal').modal('hide');
            },
            error: function() { toastError('Gagal mengirim permintaan'); },
            complete: function() {
                btn.prop('disabled', false).html('<i class="bi bi-send me-1"></i>Kirim Permintaan');
            }
        });
    });

    $('#requestDeleteModal').on('hidden.bs.modal', function() {
        $(this).find('form')[0].reset();
    });

    $(document).on('click', '.btn-rename-folder', function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        $('#formRename input[name="folder_id"]').val(id);
        $('#formRename input[name="folder_name"]').val(name);
        $('#renameModal').modal('show');
    });

    $('#formRename').on('submit', function(e) {
        e.preventDefault();
        var btn = $('#btnRename');
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Menyimpan...');
        $.ajax({
            url: '<?= site_url('siimut/dokumen-mutu/rename-folder') ?>',
            type: 'POST',
            data: $(this).serialize(),
            success: function(res) {
                if (res.status) {
                    toastSuccess(res.message);
                    $('#renameModal').modal('hide');
                    setTimeout(function() { location.reload(); }, 500);
                } else {
                    toastError(res.message);
                }
            },
            error: function() { toastError('Gagal mengubah nama folder'); },
            complete: function() {
                btn.prop('disabled', false).html('<i class="bi bi-check me-1"></i>Simpan');
            }
        });
    });

    $('#renameModal').on('hidden.bs.modal', function() {
        $(this).find('form')[0].reset();
    });
});
</script>
