<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-3 col-sm-6">
            <div class="info-box">
                <span class="info-box-icon bg-info"><i class="bi bi-building"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Unit / Bagian</span>
                    <span class="info-box-number"><?= $total ?></span>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-outline">
        <div class="card-header">
            <div class="card-tools d-flex flex-wrap justify-content-between w-100 align-items-center gap-2">
                <form onsubmit="event.preventDefault(); table.draw();" class="mb-0">
                    <div class="input-group input-group-sm" style="max-width: 260px;">
                        <input type="text" id="cari_unit" class="form-control" placeholder="Cari nama unit...">
                        <button class="btn btn-outline-secondary" type="submit" title="Cari">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
                <div class="d-flex gap-1">
                    <a href="<?= site_url('siimut/unit/create') ?>" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-plus-circle"></i> Tambah Unit
                    </a>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="table.draw()" title="Refresh">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body" style="overflow-x:auto;">
            <div class="table-responsive">
                <table id="table-unit" class="table table-striped table-bordered" style="width:100%;">
                    <thead>
                        <tr>
                            <th class="text-center" style="width:40px;">#</th>
                            <th>Nama Unit / Bagian</th>
                            <th>Keterangan</th>
                            <th>Akses Indikator</th>
                            <th class="text-center">Status</th>
                            <th class="text-center" style="width:100px;">Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
        <div class="card-footer text-muted small">
            <i class="bi bi-info-circle"></i>
            <strong>Keterangan Akses Indikator:</strong>
            <span class="badge bg-success ms-1">Hijau</span> = Unit/bagian memiliki data indikator |
            <span class="badge bg-secondary ms-1">Abu-abu</span> = Belum ada data indikator |
            INM (group_type=1) | IMPRS (group_type=5) | IMPUNIT (group_type=6) | IKP (group_type=7)
        </div>
    </div>
</div>

<script>
var table;

$(document).ready(function() {
    table = $('#table-unit').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '<?= site_url('siimut/unit/ajax-get-data') ?>',
            type: 'POST',
            data: function(d) {
                d.search = { value: $('#cari_unit').val() };
            }
        },
        columns: [
            { data: 'no', className: 'text-center', orderable: false },
            { data: 'nama' },
            { data: 'keterangan' },
            { data: 'akses', className: 'text-center', orderable: false },
            { data: 'status', className: 'text-center', orderable: false },
            { data: 'actions', className: 'text-center', orderable: false }
        ],
        order: [[1, 'asc']],
        language: {
            processing:  "Memuat...",
            emptyTable:  "Tidak ada data unit/bagian",
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

    $('#cari_unit').on('keyup', function() {
        table.draw();
    });

    // Toggle Disable
    $(document).on('change', '.btn-toggle-disable', function() {
        var el = $(this);
        var id = el.data('id');
        var action = el.is(':checked') ? 'mengaktifkan' : 'menonaktifkan';

        Swal.fire({
            title: 'Ubah Status?',
            text: 'Anda yakin ingin ' + action + ' unit/bagian ini?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Ubah!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= site_url('siimut/unit/toggle-disable/') ?>' + id,
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
                        toastError('Gagal mengubah status');
                        el.prop('checked', !el.is(':checked'));
                    }
                });
            } else {
                el.prop('checked', !el.is(':checked'));
            }
        });
    });

    // Delete
    $(document).on('click', '.btn-delete', function() {
        var id = $(this).data('id');
        var name = $(this).data('name');

        Swal.fire({
            title: 'Hapus Unit?',
            text: 'Anda yakin ingin menghapus "' + name + '"?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= site_url('siimut/unit/delete/') ?>' + id,
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
                        toastError('Gagal menghapus unit');
                    }
                });
            }
        });
    });
});
</script>
