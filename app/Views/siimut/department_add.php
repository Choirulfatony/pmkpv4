<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            <div class="card card-outline">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-building-add"></i> Tambah Unit / Bagian
                    </h6>
                    <a href="<?= site_url('siimut/unit') ?>" class="btn btn-sm btn-outline-secondary float-end">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>
                <div class="card-body">
                    <form id="form-add-unit" novalidate>
                        <div class="mb-3">
                            <label class="form-label">Nama Unit / Bagian <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="department_name" id="department_name" placeholder="Contoh: Instalasi Rawat Jalan">
                            <div class="text-danger small d-none" id="department_name-error"></div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Keterangan</label>
                            <textarea class="form-control" name="department_description" id="department_description" rows="3" placeholder="Deskripsi singkat unit/bagian..."></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Kode Institusi</label>
                            <input type="text" class="form-control" name="department_institution_code" id="department_institution_code" placeholder="Contoh: RSSM">
                        </div>

                        <hr>
                        <div class="d-flex justify-content-end gap-2">
                            <a href="<?= site_url('siimut/unit') ?>" class="btn btn-sm btn-outline-secondary">
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
    function clearErrors() {
        $('.is-invalid').removeClass('is-invalid');
        $('.text-danger.small').addClass('d-none').text('');
    }

    function showError(inputId, errorId, msg) {
        $(inputId).addClass('is-invalid');
        $(errorId).removeClass('d-none').text(msg);
    }

    $('#form-add-unit').on('submit', function(e) {
        e.preventDefault();
        clearErrors();

        var name = $('#department_name').val().trim();
        var valid = true;

        if (!name) { showError('#department_name', '#department_name-error', 'Nama unit/bagian wajib diisi'); valid = false; }

        if (!valid) return;

        Swal.fire({
            title: 'Simpan Unit Baru?',
            text: 'Unit/bagian baru akan ditambahkan.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Simpan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= site_url('siimut/unit/store') ?>',
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
