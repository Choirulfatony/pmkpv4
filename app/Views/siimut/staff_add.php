<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-10">

            <div class="card card-outline">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-person-plus"></i> Tambah Staf Baru
                    </h6>
                    <a href="<?= site_url('siimut/staf') ?>" class="btn btn-sm btn-outline-secondary float-end">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>
                <div class="card-body">
                    <form id="form-add-staf" novalidate>
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-muted mb-2"><i class="bi bi-person"></i> Data Diri</h6>

                                <div class="mb-2">
                                    <label class="form-label small">Nama Lengkap <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-sm" name="profile_fullname" required>
                                </div>

                                <div class="mb-2">
                                    <label class="form-label small">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control form-control-sm" name="profile_email" required>
                                </div>

                                <div class="mb-2">
                                    <label class="form-label small">Password <span class="text-danger">*</span> <small class="text-muted">(min. 6 karakter)</small></label>
                                    <div class="input-group input-group-sm">
                                        <input type="password" class="form-control form-control-sm" name="profile_password" id="profile_password">
                                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword()" title="Tampilkan/Sembunyikan">
                                            <i class="bi bi-eye" id="toggle-pw-icon"></i>
                                        </button>
                                    </div>
                                    <div class="text-danger small d-none" id="profile_password-error"></div>
                                </div>

                                <div class="mb-2">
                                    <label class="form-label small">NIP / Employee ID</label>
                                    <input type="text" class="form-control form-control-sm" name="profile_employee_id">
                                </div>

                                <div class="mb-2">
                                    <label class="form-label small">Jenis Kelamin</label>
                                    <select class="form-select form-select-sm" name="profile_gender">
                                        <option value="">-- Pilih --</option>
                                        <option value="1">Laki-laki</option>
                                        <option value="2">Perempuan</option>
                                    </select>
                                </div>

                                <div class="mb-2">
                                    <label class="form-label small">Tanggal Lahir</label>
                                    <input type="date" class="form-control form-control-sm" name="profile_dob">
                                </div>

                                <div class="mb-2">
                                    <label class="form-label small">Handphone</label>
                                    <input type="text" class="form-control form-control-sm" name="profile_handphone1">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <h6 class="text-muted mb-2"><i class="bi bi-building"></i> Kepegawaian</h6>

                                <div class="mb-2">
                                    <label class="form-label small">Grup Akses</label>
                                    <select class="form-select form-select-sm select2" name="profile_group_id">
                                        <option value="">-- Pilih Grup --</option>
                                        <?php foreach ($groups as $g): ?>
                                            <option value="<?= esc($g->group_id) ?>"><?= esc($g->group_name) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="mb-2">
                                    <label class="form-label small">Unit Kerja / Departemen</label>
                                    <select class="form-select form-select-sm select2" name="profile_department_id">
                                        <option value="">-- Pilih Unit --</option>
                                        <?php foreach ($departments as $d): ?>
                                            <option value="<?= esc($d->department_id) ?>"><?= esc($d->department_name) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <hr>
                        <div class="d-flex justify-content-end gap-2">
                            <a href="<?= site_url('siimut/staf') ?>" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-x-lg"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-sm btn-primary" id="btn-save">
                                <i class="bi bi-check-lg"></i> Simpan Staf Baru
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function togglePassword() {
    var pw = document.getElementById('profile_password');
    var icon = document.getElementById('toggle-pw-icon');
    if (pw.type === 'password') {
        pw.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
    } else {
        pw.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
    }
}

$(document).ready(function() {
    $('.select2').select2({
        theme: 'bootstrap-5',
        width: '100%',
        dropdownParent: $('#form-add-staf')
    });

    $('#form-add-staf').on('submit', function(e) {
        e.preventDefault();

        var name = $('[name="profile_fullname"]').val().trim();
        var email = $('[name="profile_email"]').val().trim();
        var pw = $('#profile_password').val();
        var pwErr = $('#profile_password-error');
        var valid = true;

        pwErr.addClass('d-none').text('');
        $('#profile_password').removeClass('is-invalid');

        if (!name) {
            toastError('Nama lengkap wajib diisi');
            $('[name="profile_fullname"]').focus();
            return;
        }
        if (!email) {
            toastError('Email wajib diisi');
            $('[name="profile_email"]').focus();
            return;
        }
        if (!pw || pw.length < 6) {
            $('#profile_password').addClass('is-invalid');
            pwErr.removeClass('d-none').text('Password minimal 6 karakter');
            return;
        }

        Swal.fire({
            title: 'Simpan Staf Baru?',
            text: 'Akun baru akan dibuat dan bisa langsung digunakan.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Simpan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= site_url('siimut/staf/store') ?>',
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
                                window.location.href = '<?= site_url('siimut/staf') ?>';
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
