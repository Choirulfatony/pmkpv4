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
            INM | IMPRS | IMPUNIT | IKP
        </div>
    </div>
</div>

<script>
    var table;

    $(document).ready(function() {
        table = $('#table-unit').DataTable({
            processing: true,
            serverSide: true,
            searching: false,
            ajax: {
                url: '<?= site_url('siimut/unit/ajax-get-data') ?>',
                type: 'POST',
                data: function(d) {
                    d.search = {
                        value: $('#cari_unit').val()
                    };
                }
            },
            columns: [{
                    data: 'no',
                    className: 'text-center',
                    orderable: false
                },
                {
                    data: 'nama'
                },
                {
                    data: 'keterangan'
                },
                {
                    data: 'akses',
                    className: 'text-center',
                    orderable: false
                },
                {
                    data: 'status',
                    className: 'text-center',
                    orderable: false
                },
                {
                    data: 'actions',
                    className: 'text-center',
                    orderable: false
                }
            ],
            order: [
                [1, 'asc']
            ],
            language: {
                processing: "Memuat...",
                emptyTable: "Tidak ada data unit/bagian",
                zeroRecords: "Data tidak ditemukan",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                infoEmpty: "Menampilkan 0 - 0 dari 0 data",
                paginate: {
                    first: "Awal",
                    last: "Akhir",
                    next: "Selanjutnya",
                    previous: "Sebelumnya"
                }
            },
            lengthMenu: [
                [10, 25, 50, 100],
                [10, 25, 50, 100]
            ]
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
<div class="modal fade" id="modal-indikator" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-fullscreen-lg-down">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">
                    <i class="bi bi-bar-chart"></i> <span id="modal-indicator-title">Indikator</span>
                </h6>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-sm btn-primary" id="btn-add-indicator" title="Tambah Indikator">
                        <i class="bi bi-plus-circle"></i> Tambah
                    </button>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="modal-body p-2">
                <div class="table-responsive">
                    <table class="table table-sm table-striped table-bordered mb-0" id="table-indicator-modal" style="width:100%;">
                        <thead>
                            <tr>
                                <th class="text-center" style="width:40px;">#</th>
                                <th>Periode</th>
                                <th>Judul Indikator</th>
                                <th class="text-center">Group Days</th>
                                <th class="text-center">Status</th>
                                <th class="text-center text-nowrap">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="indicator-modal-body"></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer justify-content-between flex-wrap gap-1">
                <small class="text-muted"><i class="bi bi-pencil"></i> edit · <i class="bi bi-trash"></i> hapus</small>
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Form Add/Edit -->
<div class="modal fade" id="modal-form-indicator" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-fullscreen-lg-down">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="modal-form-title"><i class="bi bi-plus-circle"></i> Tambah Indikator</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form-indicator-group" novalidate>
                <div class="modal-body">
                    <input type="hidden" name="action_type" id="form-action-type" value="add">
                    <input type="hidden" name="group_id" id="form-edit-group-id" value="0">
                    <input type="hidden" name="department_id" id="form-department-id" value="0">
                    <input type="hidden" name="group_type" id="form-group-type" value="0">

                    <div class="mb-2" id="indicator-select-wrapper">
                        <label class="form-label small">Pilih Indikator <span class="text-danger">*</span></label>
                        <select class="form-select form-select-sm" name="indicator_id" id="form-indicator-id">
                            <option value="">-- Pilih --</option>
                        </select>
                    </div>

                    <div class="mb-2">
                        <label class="form-label small">Periode / Tahun <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm" name="group_period" id="form-period" placeholder="Contoh: 2026" maxlength="4">
                        <div class="text-danger small d-none" id="form-period-error"></div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label small">Group Days</label>
                        <input type="number" class="form-control form-control-sm" name="group_days" id="form-days" value="0">
                        <div class="text-danger small d-none" id="form-days-error"></div>
                    </div>

                    <div class="mb-0">
                        <label class="form-label small">Kode Institusi</label>
                        <input type="text" class="form-control form-control-sm" name="institution_code" id="form-institution-code" value="RSSM" maxlength="11">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-check-lg"></i> Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    var currentDeptId = 0;
    var currentGroupType = 0;
    var currentLabel = '';
    var dtIndicator = null;

    // Open modal when indicator button clicked
    $(document).on('click', '.btn-show-indicators', function() {
        var btn = $(this);
        currentDeptId = btn.data('dept-id');
        currentGroupType = btn.data('type');
        currentLabel = btn.data('label');

        // Destroy previous DataTable
        if (dtIndicator) {
            dtIndicator.destroy();
            dtIndicator = null;
        }

        $('#modal-indicator-title').text(currentLabel + ' — ' + btn.data('dept-name'));
        $('#indicator-modal-body').html('<tr><td colspan="6" class="text-center text-muted py-3">Memuat data...</td></tr>');
        new bootstrap.Modal(document.getElementById('modal-indikator')).show();

        loadIndicatorList();
    });

    function loadIndicatorList() {
        // Destroy previous DataTable instance
        if (dtIndicator) {
            dtIndicator.destroy();
            dtIndicator = null;
        }
        $.ajax({
            url: '<?= site_url('siimut/unit/ajax-get-indicators') ?>',
            type: 'POST',
            data: {
                department_id: currentDeptId,
                group_type: currentGroupType
            },
            dataType: 'json',
            success: function(res) {
                var tbody = $('#indicator-modal-body');
                tbody.empty();
                if (res.data && res.data.length > 0) {
                    $.each(res.data, function(i, row) {
                        tbody.append(
                            '<tr>' +
                            '<td class="text-center">' + (i + 1) + '</td>' +
                            '<td><strong>' + row.group_period + '</strong></td>' +
                            '<td>' + row.indicator_element + '</td>' +
                            '<td class="text-center">' + row.group_days + '</td>' +
                            '<td class="text-center">' + (row.group_record_status === 'A' ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-danger">Nonaktif</span>') + '</td>' +
                            '<td class="text-center text-nowrap">' +
                            '<button type="button" class="btn btn-sm btn-outline-primary btn-edit-indicator me-1" title="Edit" data-group-id="' + row.group_id + '" data-period="' + row.group_period + '" data-days="' + row.group_days + '"><i class="bi bi-pencil"></i></button>' +
                            '<button type="button" class="btn btn-sm btn-outline-danger btn-delete-indicator" title="Hapus" data-group-id="' + row.group_id + '" data-name="' + row.indicator_element + '"><i class="bi bi-trash"></i></button>' +
                            '</td>' +
                            '</tr>'
                        );
                    });

                    // Init DataTable
                    dtIndicator = $('#table-indicator-modal').DataTable({
                        paging: true,
                        searching: true,
                        info: true,
                        lengthChange: true,
                        pageLength: 10,
                        lengthMenu: [
                            [5, 10, 25, 50],
                            [5, 10, 25, 50]
                        ],
                        autoWidth: false,
                        order: [],
                        columnDefs: [
                            { targets: [0], width: '40px' },
                            { targets: [3, 4], className: 'text-center' },
                            { targets: [5], className: 'text-center text-nowrap' }
                        ],
                        language: {
                            search: 'Cari:',
                            searchPlaceholder: 'Ketik kata kunci...',
                            lengthMenu: 'Tampilkan _MENU_',
                            info: '_START_ - _END_ dari _TOTAL_',
                            infoEmpty: '0 - 0 dari 0',
                            infoFiltered: '(difilter dari _MAX_ total)',
                            zeroRecords: 'Data tidak ditemukan',
                            paginate: {
                                first: 'Awal',
                                last: 'Akhir',
                                next: '<i class="bi bi-chevron-right"></i>',
                                previous: '<i class="bi bi-chevron-left"></i>'
                            }
                        },
                        dom: '<"d-flex justify-content-between align-items-center gap-2 px-1 mb-2"<"d-flex align-items-center gap-2"l><"d-flex align-items-center"f>>rt<"d-flex justify-content-between align-items-center gap-2 px-1 mt-2"<i><p>>'
                    });

                } else {
                    tbody.html('<tr><td colspan="6" class="text-center text-muted py-3">Belum ada indikator untuk unit ini</td></tr>');
                }
            },
            error: function() {
                $('#indicator-modal-body').html('<tr><td colspan="6" class="text-center text-danger py-3">Gagal memuat data</td></tr>');
            }
        });
    }

    // Open ADD form
    $('#btn-add-indicator').on('click', function() {
        $('#form-action-type').val('add');
        $('#form-edit-group-id').val('0');
        $('#form-department-id').val(currentDeptId);
        $('#form-group-type').val(currentGroupType);
        $('#modal-form-title').html('<i class="bi bi-plus-circle"></i> Tambah Indikator ' + currentLabel);
        $('#indicator-select-wrapper').show();
        $('#form-indicator-id').val('').trigger('change');
        $('#form-period').val(new Date().getFullYear().toString());
        $('#form-days').val(0);
        $('#form-institution-code').val('RSSM');
        $('.text-danger.small').addClass('d-none');
        $('#form-indicator-id').html('<option value="">-- Pilih --</option>');

        // Load available indicators
        $.ajax({
            url: '<?= site_url('siimut/unit/ajax-get-available-indicators') ?>',
            type: 'POST',
            data: {
                group_type: currentGroupType
            },
            dataType: 'json',
            success: function(res) {
                var sel = $('#form-indicator-id');
                sel.find('option:not(:first)').remove();
                if (res.data) {
                    $.each(res.data, function(i, row) {
                        sel.append('<option value="' + row.indicator_id + '">' + row.indicator_element + '</option>');
                    });
                }
            }
        });

        new bootstrap.Modal(document.getElementById('modal-form-indicator')).show();
    });

    // Open EDIT form
    $(document).on('click', '.btn-edit-indicator', function() {
        var btn = $(this);
        $('#form-action-type').val('edit');
        $('#form-edit-group-id').val(btn.data('group-id'));
        $('#form-department-id').val(currentDeptId);
        $('#form-group-type').val(currentGroupType);
        $('#modal-form-title').html('<i class="bi bi-pencil"></i> Edit Indikator');
        $('#indicator-select-wrapper').hide();
        $('#form-period').val(btn.data('period'));
        $('#form-days').val(btn.data('days'));
        $('#form-institution-code').val('RSSM');
        $('.text-danger.small').addClass('d-none');

        new bootstrap.Modal(document.getElementById('modal-form-indicator')).show();
    });

    // Delete indicator
    $(document).on('click', '.btn-delete-indicator', function() {
        var btn = $(this);
        var name = btn.data('name');

        Swal.fire({
            title: 'Hapus Indikator?',
            text: 'Yakin ingin menghapus "' + name + '" dari daftar ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= site_url('siimut/unit/ajax-delete-group') ?>',
                    type: 'POST',
                    data: {
                        group_id: btn.data('group-id'),
                        group_type: currentGroupType
                    },
                    dataType: 'json',
                    success: function(res) {
                        if (res.status) {
                            toastSuccess(res.message);
                            loadIndicatorList();
                        } else {
                            toastError(res.message);
                        }
                    },
                    error: function() {
                        toastError('Gagal menghapus');
                    }
                });
            }
        });
    });

    // Submit form add/edit
    $('#form-indicator-group').on('submit', function(e) {
        e.preventDefault();
        $('.text-danger.small').addClass('d-none');

        var actionType = $('#form-action-type').val();
        var period = $('#form-period').val().trim();
        var days = parseInt($('#form-days').val()) || 0;
        var valid = true;

        if (!period) {
            $('#form-period').addClass('is-invalid');
            $('#form-period-error').removeClass('d-none').text('Periode wajib diisi');
            valid = false;
        } else if (!/^\d{4}$/.test(period)) {
            $('#form-period').addClass('is-invalid');
            $('#form-period-error').removeClass('d-none').text('Format periode harus 4 digit angka');
            valid = false;
        }

        if (days < 0) {
            $('#form-days').addClass('is-invalid');
            $('#form-days-error').removeClass('d-none').text('Group days tidak boleh minus');
            valid = false;
        }

        if (actionType === 'add') {
            var indicatorId = $('#form-indicator-id').val();
            if (!indicatorId) {
                $('#form-indicator-id').addClass('is-invalid');
                valid = false;
            }
        }

        if (!valid) return;

        var url = actionType === 'add' ?
            '<?= site_url('siimut/unit/ajax-add-group') ?>' :
            '<?= site_url('siimut/unit/ajax-update-group') ?>';

        var postData = $(this).serialize();
        if (actionType === 'edit') {
            postData += '&group_type=' + currentGroupType;
        }

        Swal.fire({
            title: 'Simpan?',
            text: 'Data akan disimpan.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Simpan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: postData,
                    dataType: 'json',
                    success: function(res) {
                        if (res.status) {
                            bootstrap.Modal.getInstance(document.getElementById('modal-form-indicator')).hide();
                            toastSuccess(res.message);
                            loadIndicatorList();
                        } else {
                            toastError(res.message);
                        }
                    },
                    error: function() {
                        toastError('Gagal menyimpan');
                    }
                });
            }
        });
    });
</script>