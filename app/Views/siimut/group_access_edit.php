<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-diagram-3"></i> Atur Departemen: <strong><?= esc($group->group_name) ?></strong>
                    </h6>
                    <a href="<?= site_url('siimut/group-access') ?>" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>
                <div class="card-body">
                    <form id="form-group-access">
                        <input type="hidden" name="group_id" value="<?= $group->group_id ?>">

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Pilih Departemen yang Dapat Diakses:</label>
                            <div class="row g-2" style="max-height: 400px; overflow-y: auto;">
                                <?php if (!empty($departments)): ?>
                                    <?php foreach ($departments as $d): ?>
                                        <div class="col-md-4 col-sm-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox"
                                                       name="department_ids[]"
                                                       value="<?= esc($d->department_id) ?>"
                                                       id="dept-<?= $d->department_id ?>"
                                                       <?= in_array($d->department_id, $assignedDeptIds) ? 'checked' : '' ?>>
                                                <label class="form-check-label small" for="dept-<?= $d->department_id ?>">
                                                    <?= esc($d->department_name) ?>
                                                </label>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="col-12">
                                        <p class="text-muted mb-0">Tidak ada departemen tersedia</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <hr>
                        <div class="d-flex justify-content-end gap-2">
                            <a href="<?= site_url('siimut/group-access') ?>" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-x-lg"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-sm btn-primary" id="btn-save">
                                <i class="bi bi-check-lg"></i> Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#form-group-access').on('submit', function(e) {
        e.preventDefault();

        var groupId = $('input[name="group_id"]').val();
        var checked = $('input[name="department_ids[]"]:checked').length;

        Swal.fire({
            title: 'Simpan Pengaturan?',
            text: checked + ' departemen akan diberikan akses ke grup ini.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Simpan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= site_url('siimut/group-access/update/' . $group->group_id) ?>',
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
                                window.location.href = '<?= site_url('siimut/group-access') ?>';
                            });
                        } else {
                            toastError(res.message);
                        }
                    },
                    error: function() {
                        toastError('Gagal menyimpan pengaturan');
                    }
                });
            }
        });
    });
});
</script>
