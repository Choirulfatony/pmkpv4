<div class="container-fluid">
    <style>
        #table-staf_paginate .pagination .page-link {
            padding: 2px 8px;
            font-size: 12px;
        }
        #table-staf_paginate .pagination {
            margin-bottom: 0;
        }
        #table-staf_length select {
            padding-top: 2px;
            padding-bottom: 2px;
        }
        #table-staf_filter input {
            padding-top: 2px;
            padding-bottom: 2px;
        }
    </style>
    <div class="row mb-3">
        <div class="col-md-3 col-sm-6">
            <div class="info-box">
                <span class="info-box-icon bg-info"><i class="bi bi-people"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Staf</span>
                    <span class="info-box-number"><?= $total ?></span>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="info-box">
                <span class="info-box-icon bg-success"><i class="bi bi-person-check"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Online Saat Ini</span>
                    <span class="info-box-number"><?= $active ?></span>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-outline">
        <div class="card-header">
            <div class="card-tools d-flex flex-wrap justify-content-between w-100 align-items-center gap-2">
                <form onsubmit="event.preventDefault(); table.draw();" class="mb-0">
                    <div class="input-group input-group-sm" style="max-width: 260px;">
                        <input type="text" id="cari_staf" class="form-control" placeholder="Cari nama, email, NIP...">
                        <button class="btn btn-outline-secondary" type="submit" title="Cari">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
                <div class="d-flex gap-1">
                    <a href="<?= site_url('siimut/staf/create') ?>" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-plus-circle"></i> Tambah Staf
                    </a>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="table.draw()" title="Refresh">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body" style="overflow-x:auto;">
            <div class="table-responsive">
                <table id="table-staf" class="table table-striped table-bordered" style="width:100%;">
                    <thead>
                        <tr>
                            <th class="text-center" style="width:40px;">#</th>
                            <th>Nama</th>
                            <th>Grup Akses</th>
                            <th>Email</th>
                            <th>Unit Kerja</th>
                            <th class="text-center">Akun</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Terakhir Login</th>
                            <th class="text-center" style="width:160px;">Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal View Staf -->
<div class="modal fade" id="viewStafModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h6 class="modal-title"><i class="bi bi-person me-2"></i>Detail Staf</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4 text-center mb-3">
                        <img id="vPhoto" src="" alt="Photo" class="img-fluid rounded-circle mb-2" style="width:120px;height:120px;object-fit:cover;border:3px solid #dee2e6;">
                        <h6 id="vFullname" class="mb-1"></h6>
                        <small id="vGroup" class="text-muted"></small>
                    </div>
                    <div class="col-md-8">
                        <table class="table table-sm table-borderless mb-0">
                            <tr><td style="width:130px"><strong>Unit</strong></td><td id="vDepartment"></td></tr>
                            <tr><td><strong>NIP</strong></td><td id="vNip"></td></tr>
                            <tr><td><strong>Email</strong></td><td id="vEmail"></td></tr>
                            <tr><td><strong>Jenis Kelamin</strong></td><td id="vGender"></td></tr>
                            <tr><td><strong>Tanggal Lahir</strong></td><td id="vDob"></td></tr>
                            <tr><td><strong>Handphone</strong></td><td id="vHandphone"></td></tr>
                            <tr><td><strong>Status Akun</strong></td><td id="vStatus"></td></tr>
                            <tr><td><strong>Status Online</strong></td><td id="vOnline"></td></tr>
                            <tr><td><strong>Terdaftar</strong></td><td id="vTerdaftar"></td></tr>
                            <tr><td><strong>Terakhir Login</strong></td><td id="vLastLogin"></td></tr>
                            <tr><td><strong>Password</strong></td>
                                <td>
                                    <div class="input-group input-group-sm">
                                        <input type="text" class="form-control pw-mask" id="vPassword" value="" readonly style="font-size:inherit;max-width:200px;">
                                        <button class="btn btn-outline-secondary btn-toggle-vpw" type="button">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr><td><strong>Catatan</strong></td><td id="vNote"></td></tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
var table;

$(document).ready(function() {
    table = $('#table-staf').DataTable({
        processing: true,
        serverSide: true,
        searching: false,
        ajax: {
            url: '<?= site_url('siimut/staf/ajax-get-data') ?>',
            type: 'POST',
            data: function(d) {
                d.search = { value: $('#cari_staf').val() };
            }
        },
        columns: [
            { data: 'no', className: 'text-center', orderable: false },
            { data: 'nama' },
            { data: 'grup' },
            { data: 'email' },
            { data: 'unit_kerja' },
            { data: 'akun', className: 'text-center', orderable: false },
            { data: 'online', className: 'text-center', orderable: false },
            { data: 'last_login', className: 'text-center' },
            { data: 'actions', className: 'text-center', orderable: false }
        ],
        order: [[1, 'asc']],
        language: {
            processing:  "Memuat...",
            emptyTable:  "Tidak ada data staf",
            zeroRecords: "Data tidak ditemukan",
            lengthMenu:  "Tampilkan _MENU_ data",
            info:        "Menampilkan _START_ - _END_ dari _TOTAL_ data",
            infoEmpty:   "Menampilkan 0 - 0 dari 0 data",
            paginate: {
                first:    "Awal",
                last:     "Akhir",
                next:     "Selanjutnya",
                previous: "Sebelumnya"
            }
        },
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]]
    });

    $('<style>.pw-mask{-webkit-text-security:disc}.pw-mask::-webkit-text-security{auto}</style>').appendTo('head');

    $('#cari_staf').on('keyup', function() {
        table.draw();
    });

    // Toggle Disable via switch
    $(document).on('change', '.btn-toggle-disable', function() {
        var el = $(this);
        var id = el.data('id');
        var action = el.is(':checked') ? 'menonaktifkan' : 'mengaktifkan';

        Swal.fire({
            title: 'Ubah Status Akun?',
            text: 'Anda yakin ingin ' + action + ' akun ini?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Ubah!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= site_url('siimut/staf/toggle-disable/') ?>' + id,
                    type: 'POST',
                    dataType: 'json',
                    success: function(res) {
                        if (res.status) {
                            toastSuccess(res.message);
                            table.draw();
                        } else {
                            toastError(res.message);
                            el.prop('checked', !el.is(':checked'));
                        }
                    },
                    error: function() {
                        toastError('Gagal mengubah status akun');
                        el.prop('checked', !el.is(':checked'));
                    }
                });
            } else {
                el.prop('checked', !el.is(':checked'));
            }
        });
    });

    // View
    $(document).on('click', '.btn-view-staf', function() {
        var id = $(this).data('id');
        $.ajax({
            url: '<?= site_url('siimut/staf/ajax-get-staff/') ?>' + id,
            type: 'POST',
            dataType: 'json',
            success: function(res) {
                if (!res.status) { toastError(res.message); return; }
                var d = res.data;
                $('#vPhoto').attr('src', d.photo);
                $('#vFullname').text(d.fullname);
                $('#vGroup').text(d.group);
                $('#vDepartment').text(d.department);
                $('#vNip').text(d.nip);
                $('#vEmail').text(d.email);
                $('#vGender').text(d.gender);
                $('#vDob').text(d.dob);
                $('#vHandphone').text(d.handphone);
                $('#vStatus').text(d.status);
                $('#vOnline').html(d.online);
                $('#vTerdaftar').text(d.terdaftar);
                $('#vLastLogin').text(d.last_login);
                $('#vNote').html(d.note);
                $('#vPassword').val(d.password).addClass('pw-mask');
                $('#viewStafModal').modal('show');
            },
            error: function() { toastError('Gagal memuat data staf'); }
        });
    });

    // Toggle password view
    $(document).on('click', '.btn-toggle-vpw', function() {
        var input = $('#vPassword');
        var icon = $(this).find('i');
        input.toggleClass('pw-mask');
        icon.toggleClass('bi-eye bi-eye-slash');
    });

    // Delete
    $(document).on('click', '.btn-delete', function() {
        var id = $(this).data('id');
        var name = $(this).data('name');

        Swal.fire({
            title: 'Hapus Staf?',
            text: 'Anda yakin ingin menghapus "' + name + '"? Data akan dihapus secara permanen.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= site_url('siimut/staf/delete/') ?>' + id,
                    type: 'POST',
                    dataType: 'json',
                    success: function(res) {
                        if (res.status) {
                            toastSuccess(res.message);
                            table.draw();
                        } else {
                            toastError(res.message);
                        }
                    },
                    error: function() {
                        toastError('Gagal menghapus staf');
                    }
                });
            }
        });
    });
});
</script>
