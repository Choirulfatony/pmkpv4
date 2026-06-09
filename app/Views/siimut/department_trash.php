<div class="container-fluid">
    <div class="card card-outline">
        <div class="card-header">
            <div class="card-tools d-flex flex-wrap justify-content-between w-100 align-items-center gap-2">
                <div class="d-flex gap-1">
                    <a href="<?= site_url('siimut/unit') ?>" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>
                <small class="text-muted">Data dengan status <code>X</code> (dihapus) — dapat dipulihkan atau dihapus permanen</small>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm table-bordered table-striped" id="table-trash" style="width:100%;">
                    <thead>
                        <tr>
                            <th class="text-center" style="width:40px;">#</th>
                            <th>Unit / Bagian</th>
                            <th class="text-center" style="width:70px;">Tipe</th>
                            <th>Indikator</th>
                            <th class="text-center">Periode</th>
                            <th class="text-center">Group Days</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        var tableTrash = $('#table-trash').DataTable({
            processing: true,
            serverSide: true,
            searching: true,
            ajax: {
                url: '<?= site_url('siimut/unit/ajax-get-trash') ?>',
                type: 'POST'
            },
            columns: [
                { data: 'no', className: 'text-center', orderable: false },
                { data: 'unit' },
                { data: 'tipe', className: 'text-center' },
                { data: 'indikator' },
                { data: 'periode', className: 'text-center' },
                { data: 'days', className: 'text-center' },
                { data: 'aksi', className: 'text-center', orderable: false }
            ],
            order: [[4, 'desc']],
            language: {
                processing: "Memuat...",
                emptyTable: "Tidak ada data di tempat sampah",
                zeroRecords: "Data tidak ditemukan",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                infoEmpty: "Menampilkan 0 - 0 dari 0 data",
                search: "Cari:",
                paginate: {
                    first: "Awal",
                    last: "Akhir",
                    next: "<i class='bi bi-chevron-right'></i>",
                    previous: "<i class='bi bi-chevron-left'></i>"
                }
            },
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]]
        });

        // Restore
        $(document).on('click', '.btn-restore-trash', function() {
            var btn = $(this);
            Swal.fire({
                title: 'Pulihkan Indikator?',
                text: 'Data akan dikembalikan ke daftar aktif unit terkait.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                confirmButtonText: 'Ya, Pulihkan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '<?= site_url('siimut/unit/ajax-restore-group') ?>',
                        type: 'POST',
                        data: { group_id: btn.data('id'), group_type: btn.data('type') },
                        dataType: 'json',
                        success: function(res) {
                            if (res.status) {
                                toastSuccess(res.message);
                                tableTrash.draw();
                            } else {
                                toastError(res.message);
                            }
                        },
                        error: function() { toastError('Gagal memulihkan'); }
                    });
                }
            });
        });

        // Permanent delete
        $(document).on('click', '.btn-delete-trash', function() {
            var btn = $(this);
            var name = btn.data('name');
            Swal.fire({
                title: 'Hapus Permanen?',
                html: 'Data indikator <strong>' + name + '</strong> akan dihapus permanent dan tidak bisa dikembalikan.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Ya, Hapus Permanen!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '<?= site_url('siimut/unit/ajax-permanent-delete') ?>',
                        type: 'POST',
                        data: { group_id: btn.data('id'), group_type: btn.data('type') },
                        dataType: 'json',
                        success: function(res) {
                            if (res.status) {
                                toastSuccess(res.message);
                                tableTrash.draw();
                            } else {
                                toastError(res.message);
                            }
                        },
                        error: function() { toastError('Gagal menghapus permanen'); }
                    });
                }
            });
        });
    });
</script>
