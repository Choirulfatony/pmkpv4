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

<!-- Modal Indikator -->
<div class="modal fade" id="modal-indikator" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title"><i class="bi bi-bar-chart"></i> <span id="modal-indicator-title">Indikator</span></h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="indicator-list"></div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).on('click', '.btn-show-indicators', function() {
    var btn = $(this);
    var deptId = btn.data('dept-id');
    var deptName = btn.data('dept-name');
    var type = btn.data('type');
    var label = btn.data('label');

    $('#modal-indicator-title').text(label + ' — ' + deptName);
    var container = $('#indicator-list');
    container.html('<div class="text-center text-muted py-4"><i class="bi bi-hourglass-split"></i> Memuat data...</div>');
    var modal = new bootstrap.Modal(document.getElementById('modal-indikator'));
    modal.show();

    $.ajax({
        url: '<?= site_url('siimut/unit/ajax-get-indicators') ?>',
        type: 'POST',
        data: { department_id: deptId, group_type: type },
        dataType: 'json',
        success: function(res) {
            container.empty();
            if (res.data && res.data.length > 0) {
                $.each(res.data, function(i, row) {
                    var targetDisplay = row.indicator_target + ' ' + row.indicator_units;
                    if (row.indicator_target_unit) {
                        targetDisplay += ' / ' + row.indicator_target_unit;
                    }
                    var html = '<div class="card card-outline card-outline-brand mb-3">'
                        + '<div class="card-header p-2">'
                        + '<div class="d-flex justify-content-between align-items-center">'
                        + '<h6 class="mb-0"><span class="badge bg-secondary me-1">' + (i+1) + '</span> ' + row.indicator_element + '</h6>'
                        + '<span class="badge bg-info">' + row.indicator_frequency + '</span>'
                        + '</div>'
                        + '</div>'
                        + '<div class="card-body p-2">'
                        + '<div class="row text-sm">'
                        + '<div class="col-md-6">'
                        + '<table class="table table-sm table-borderless mb-0">'
                        + '<tr><td class="text-muted" style="width:160px;">Target</td><td><strong>' + targetDisplay + '</strong></td></tr>'
                        + '<tr><td class="text-muted">Kriteria Tercapai</td><td>' + row.indicator_calc_label + ' Target</td></tr>'
                        + '<tr><td class="text-muted">Pembagi (Factors)</td><td>' + row.indicator_factors + '</td></tr>'
                        + '<tr><td class="text-muted">Satuan</td><td>' + row.indicator_units + '</td></tr>'
                        + '<tr><td class="text-muted">Area Monitoring</td><td>' + row.indicator_monitoring_area + '</td></tr>'
                        + '</table>'
                        + '</div>'
                        + '<div class="col-md-6">'
                        + '<table class="table table-sm table-borderless mb-0">'
                        + '<tr><td class="text-muted" style="width:160px;">Definisi</td><td>' + row.indicator_definition + '</td></tr>'
                        + '<tr><td class="text-muted">Kriteria Inklusif</td><td>' + row.indicator_criteria_inclusive + '</td></tr>'
                        + '<tr><td class="text-muted">Kriteria Eksklusif</td><td>' + row.indicator_criteria_exclusive + '</td></tr>'
                        + '<tr><td class="text-muted">Sumber Data</td><td>' + row.indicator_source_of_data + '</td></tr>'
                        + '<tr><td class="text-muted">Nilai Standar</td><td>' + row.indicator_value_standard + '</td></tr>'
                        + '<tr><td class="text-muted">LCL / UCL</td><td>' + row.indicator_lcl + ' / ' + row.indicator_ucl + '</td></tr>'
                        + '</table>'
                        + '</div>'
                        + '</div>'
                        + '</div>'
                        + '</div>';
                    container.append(html);
                });
            } else {
                container.html('<div class="text-center text-muted py-4"><i class="bi bi-inbox"></i> Tidak ada data indikator</div>');
            }
        },
        error: function() {
            container.html('<div class="text-center text-danger py-4"><i class="bi bi-exclamation-triangle"></i> Gagal memuat data</div>');
        }
    });
});
</script>
