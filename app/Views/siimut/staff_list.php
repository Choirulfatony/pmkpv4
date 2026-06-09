<div class="container-fluid">
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
                            <th class="text-center" style="width:130px;">Aksi</th>
                        </tr>
                    </thead>
                </table>
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

    $('#cari_staf').on('keyup', function() {
        table.draw();
    });

    // Toggle Disable
    $(document).on('click', '.btn-toggle-disable', function() {
        var id = $(this).data('id');
        var currentStatus = $(this).data('status');
        var action = currentStatus == 1 ? 'mengaktifkan' : 'menonaktifkan';

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
                        }
                    },
                    error: function() {
                        toastError('Gagal mengubah status akun');
                    }
                });
            }
        });
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
