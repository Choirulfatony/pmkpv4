<div class="container-fluid">
    <div class="row g-2 mb-3">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3><?= count($allMenus) ?></h3>
                    <p>Total Menu</p>
                </div>
                <div class="icon"><i class="bi bi-list-ul"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3><?= count(array_filter($allMenus, fn($m) => empty($m['parent_id']))) ?></h3>
                    <p>Menu Utama</p>
                </div>
                <div class="icon"><i class="bi bi-folder"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3><?= count(array_filter($allMenus, fn($m) => !empty($m['parent_id']))) ?></h3>
                    <p>Sub Menu</p>
                </div>
                <div class="icon"><i class="bi bi-substack"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3><?= count(array_unique(array_merge(...array_map(fn($m) => array_map('trim', explode(',', $m['role_access'] ?? '')), $allMenus)))) ?></h3>
                    <p>Role Terdaftar</p>
                </div>
                <div class="icon"><i class="bi bi-shield-lock"></i></div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2 py-2">
            <div class="d-flex align-items-center gap-2">
                <input type="text" id="cari_menu" class="form-control form-control-sm" placeholder="Cari menu..." style="width:200px">
            </div>
            <button class="btn btn-sm btn-primary" id="btnTambahMenu"><i class="bi bi-plus-lg"></i> Tambah Menu</button>
        </div>
        <div class="card-body p-2">
            <div class="table-responsive">
                <table class="table table-sm table-hover align-middle mb-0" id="table-menu" style="width:100%">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width:40px">No</th>
                            <th style="width:50px">ID</th>
                            <th>Nama Menu</th>
                            <th style="width:140px" class="d-none d-lg-table-cell">URL</th>
                            <th style="width:70px" class="d-none d-md-table-cell">Icon</th>
                            <th style="width:110px" class="d-none d-md-table-cell">Parent</th>
                            <th style="width:40px" class="text-center">Urut</th>
                            <th style="width:150px" class="d-none d-xl-table-cell">Role Access</th>
                            <th style="width:90px" class="d-none d-lg-table-cell">Dibuat</th>
                            <th class="text-center" style="width:90px">Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Add/Edit Menu -->
<div class="modal fade" id="modalMenu" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h6 class="modal-title"><i class="bi bi-gear me-2"></i><span id="modalMenuTitle">Tambah Menu</span></h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formMenu">
                <input type="hidden" name="id_menu" id="edit-id-menu" value="">
                <div class="modal-body">
                    <div class="mb-2">
                        <label class="form-label small">Nama Menu <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm" name="nama_menu" id="edit-nama-menu" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small">URL</label>
                        <input type="text" class="form-control form-control-sm" name="url" id="edit-url" placeholder="siimut/dashboard">
                    </div>
                    <div class="row g-2 mb-2">
                        <div class="col-md-6">
                            <label class="form-label small">Icon (Bootstrap Icons)</label>
                            <input type="text" class="form-control form-control-sm" name="icon" id="edit-icon" placeholder="bi bi-speedometer2">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">Urutan</label>
                            <input type="number" class="form-control form-control-sm" name="urutan" id="edit-urutan" value="0" min="0">
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small">Induk Menu</label>
                        <select class="form-select form-select-sm" name="parent_id" id="edit-parent-id">
                            <option value="">-- Root (Menu Utama) --</option>
                            <?php foreach ($allMenus as $m): ?>
                            <option value="<?= $m['id_menu'] ?>"><?= esc($m['nama_menu']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-0">
                        <label class="form-label small">Akses Role</label>
                        <div class="d-flex flex-wrap gap-2 p-2 border rounded bg-light" id="role-checkbox-group">
                            <?php foreach ($roles as $role): ?>
                            <div class="form-check form-check-inline mb-0">
                                <input class="form-check-input" type="checkbox" name="role_access[]" value="<?= $role ?>" id="role-<?= $role ?>">
                                <label class="form-check-label small" for="role-<?= $role ?>"><?= $role ?></label>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-check-lg"></i> Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Hapus -->
<div class="modal fade" id="modalHapus" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h6 class="modal-title"><i class="bi bi-exclamation-triangle me-2"></i>Hapus Menu</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-4">
                <p class="mb-0">Yakin ingin menghapus <strong id="hapusNama"></strong>?</p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-sm btn-danger" id="btnConfirmHapus"><i class="bi bi-trash"></i> Hapus</button>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="<?= base_url('assets/adminlte/css/responsive.bootstrap5.min.css') ?>">
<script src="<?= base_url('assets/adminlte/js/dataTables.responsive.min.js') ?>"></script>
<script>
    var table;

    $(document).ready(function() {
        table = $('#table-menu').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            searching: false,
            paging: true,
            lengthChange: true,
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
            ajax: {
                url: '<?= site_url('siimut/menu-manager/ajax-get-data') ?>',
                type: 'POST',
                data: function(d) {
                    d.search = { value: $('#cari_menu').val() };
                }
            },
            columns: [
                { data: 'no', className: 'text-center', orderable: false },
                { data: 'id_menu', className: 'text-center', orderable: true },
                { data: 'nama_menu', orderable: true },
                { data: 'url', className: 'd-none d-lg-table-cell', orderable: true, render: function(d) { return d ? '<code class="small">' + escHtml(d) + '</code>' : '-'; } },
                { data: 'icon', className: 'd-none d-md-table-cell', orderable: true, render: function(d) { return d ? '<span class="badge bg-light text-dark border small"><i class="' + escHtml(d) + '"></i></span>' : '-'; } },
                { data: 'parent_nama', className: 'd-none d-md-table-cell', orderable: true },
                { data: 'urutan', className: 'text-center', orderable: true },
                { data: 'role_access', className: 'd-none d-xl-table-cell', orderable: false, render: function(d) {
                    if (!d) return '<span class="text-muted small">-</span>';
                    return d.split(',').map(function(r) {
                        return '<span class="badge bg-secondary" style="font-size:10px">' + r.trim() + '</span>';
                    }).join(' ');
                }},
                { data: 'created_at', className: 'd-none d-lg-table-cell small', orderable: true, render: function(d) { return d ? d : '-'; } },
                { data: null, className: 'text-center', orderable: false, render: function(row) {
                    return '<button class="btn btn-sm btn-outline-primary btn-edit-menu me-1" data-id="' + row.id_menu + '" title="Edit"><i class="bi bi-pencil"></i></button>' +
                           '<button class="btn btn-sm btn-outline-danger btn-delete-menu" data-id="' + row.id_menu + '" data-name="' + escHtml(row.nama_menu) + '" title="Hapus"><i class="bi bi-trash"></i></button>';
                }}
            ],
            order: [[6, 'asc']],
            columnDefs: [
                { targets: [7], orderable: false }
            ]
        });

        $('#cari_menu').on('keyup', function() {
            table.ajax.reload();
        });
    });

    // Tambah Menu
    $('#btnTambahMenu').on('click', function() {
        resetForm();
        $('#modalMenuTitle').text('Tambah Menu');
        $('#modalMenu').modal('show');
    });

    // Edit Menu
    $(document).on('click', '.btn-edit-menu', function() {
        var id = $(this).data('id');
        $.get('<?= site_url('siimut/menu-manager/get-menu/') ?>' + id, function(res) {
            if (!res.status) return toastError(res.message);
            var d = res.data;
            $('#edit-id-menu').val(d.id_menu);
            $('#edit-nama-menu').val(d.nama_menu);
            $('#edit-url').val(d.url);
            $('#edit-icon').val(d.icon);
            $('#edit-urutan').val(d.urutan);
            $('#edit-parent-id').val(d.parent_id || '');

            // Check role checkboxes
            var roles = d.role_access ? d.role_access.split(',').map(function(r) { return r.trim(); }) : [];
            $('#role-checkbox-group input[type="checkbox"]').each(function() {
                $(this).prop('checked', roles.indexOf($(this).val()) !== -1);
            });

            $('#modalMenuTitle').text('Edit Menu');
            $('#modalMenu').modal('show');
        });
    });

    // Submit form
    $('#formMenu').on('submit', function(e) {
        e.preventDefault();
        var btn = $(this).find('button[type="submit"]');
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');

        $.ajax({
            url: '<?= site_url('siimut/menu-manager/store') ?>',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(res) {
                if (res.status) {
                    toastSuccess(res.message);
                    $('#modalMenu').modal('hide');
                    table.ajax.reload();
                } else {
                    toastError(res.message);
                }
            },
            error: function() { toastError('Gagal menyimpan menu'); },
            complete: function() {
                btn.prop('disabled', false).html('<i class="bi bi-check-lg"></i> Simpan');
            }
        });
    });

    // Hapus Menu
    var hapusId = 0;
    $(document).on('click', '.btn-delete-menu', function() {
        hapusId = $(this).data('id');
        $('#hapusNama').text($(this).data('name'));
        $('#modalHapus').modal('show');
    });

    $('#btnConfirmHapus').on('click', function() {
        var btn = $(this);
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');

        $.ajax({
            url: '<?= site_url('siimut/menu-manager/delete/') ?>' + hapusId,
            type: 'POST',
            dataType: 'json',
            success: function(res) {
                if (res.status) {
                    toastSuccess(res.message);
                    $('#modalHapus').modal('hide');
                    table.ajax.reload();
                } else {
                    toastError(res.message);
                }
            },
            error: function() { toastError('Gagal menghapus menu'); },
            complete: function() {
                btn.prop('disabled', false).html('<i class="bi bi-trash"></i> Hapus');
            }
        });
    });

    $('#modalMenu').on('hidden.bs.modal', function() { resetForm(); });

    function resetForm() {
        $('#formMenu')[0].reset();
        $('#edit-id-menu').val('');
        $('#role-checkbox-group input[type="checkbox"]').prop('checked', false);
    }

    function escHtml(str) {
        return $('<div>').text(str || '').html();
    }
</script>
