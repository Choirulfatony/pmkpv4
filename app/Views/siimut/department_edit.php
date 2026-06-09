<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <?= session()->getFlashdata('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="card card-outline">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-building-gear"></i> Edit Unit / Bagian
                    </h6>
                    <a href="<?= site_url('siimut/unit') ?>" class="btn btn-sm btn-outline-secondary float-end">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>
                <div class="card-body">
                    <form id="form-edit-unit" novalidate>
                        <input type="hidden" name="department_id" value="<?= $department->department_id ?>">

                        <div class="mb-3">
                            <label class="form-label">Nama Unit / Bagian <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="department_name" id="department_name" value="<?= esc($department->department_name) ?>">
                            <div class="text-danger small d-none" id="department_name-error"></div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Keterangan</label>
                            <textarea class="form-control" name="department_description" id="department_description" rows="3"><?= esc($department->department_description) ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Kode Institusi</label>
                            <input type="text" class="form-control" name="department_institution_code" id="department_institution_code" value="<?= esc($department->department_institution_code) ?>">
                        </div>

                        <hr>

                        <h6 class="text-muted mb-3"><i class="bi bi-speedometer2"></i> Akses Indikator</h6>
                        <div class="row g-3 mb-3">
                            <?php
                            $indicatorTypes = [
                                4 => ['label' => 'INM',     'color' => 'primary', 'icon' => 'graph-up'],
                                5 => ['label' => 'IMPRS',   'color' => 'success', 'icon' => 'bar-chart-line'],
                                6 => ['label' => 'IMPUNIT', 'color' => 'warning', 'icon' => 'clipboard-data'],
                                7 => ['label' => 'IKP',     'color' => 'info',    'icon' => 'bug'],
                            ];
                            foreach ($indicatorTypes as $type => $cfg):
                                $active = $indicatorAccess[$type] ?? false;
                            ?>
                            <div class="col-md-3 col-6">
                                <div class="card <?= $active ? 'border-' . $cfg['color'] : 'border-secondary' ?> <?= $active ? '' : 'opacity-75' ?>">
                                    <div class="card-body text-center py-3">
                                        <i class="bi bi-<?= $cfg['icon'] ?> fs-3 text-<?= $active ? $cfg['color'] : 'secondary' ?>"></i>
                                        <h6 class="mt-1 mb-0"><?= $cfg['label'] ?></h6>
                                        <small class="text-muted"><?= $active ? 'Aktif' : 'Nonaktif' ?></small>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label small">Status</label>
                                <div>
                                    <?php if ($department->department_record_status === 'A'): ?>
                                        <span class="badge bg-success">Aktif</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Nonaktif</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <hr>
                        <div class="d-flex justify-content-end gap-2">
                            <a href="<?= site_url('siimut/unit') ?>" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-x-lg"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-sm btn-primary" id="btn-save">
                                <i class="bi bi-check-lg"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
var deptId = <?= $department->department_id ?>;

$(document).ready(function() {
    function clearErrors() {
        $('.is-invalid').removeClass('is-invalid');
        $('.text-danger.small').addClass('d-none').text('');
    }

    function showError(inputId, errorId, msg) {
        $(inputId).addClass('is-invalid');
        $(errorId).removeClass('d-none').text(msg);
    }

    $('#form-edit-unit').on('submit', function(e) {
        e.preventDefault();
        clearErrors();

        var name = $('#department_name').val().trim();
        var valid = true;

        if (!name) { showError('#department_name', '#department_name-error', 'Nama unit/bagian wajib diisi'); valid = false; }

        if (!valid) return;

        Swal.fire({
            title: 'Simpan Perubahan?',
            text: 'Data unit/bagian akan diperbarui.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Simpan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= site_url('siimut/unit/update/') ?>' + deptId,
                    type: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(res) {
                        if (res.status) {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: res.message,
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.href = '<?= site_url('siimut/unit') ?>';
                            });
                        } else {
                            toastError(res.message);
                        }
                    },
                    error: function() {
                        toastError('Gagal menyimpan data');
                    }
                });
            }
        });
    });
});
</script>
