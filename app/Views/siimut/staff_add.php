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
                                    <input type="text" class="form-control form-control-sm" name="profile_fullname" id="profile_fullname">
                                    <div class="text-danger small d-none" id="profile_fullname-error"></div>
                                </div>

                                <div class="mb-2">
                                    <label class="form-label small">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control form-control-sm" name="profile_email" id="profile_email">
                                    <div class="text-danger small d-none" id="profile_email-error"></div>
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
                                    <label class="form-label small">NIP / Employee ID <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-sm" name="profile_employee_id" id="profile_employee_id">
                                    <div class="text-danger small d-none" id="profile_employee_id-error"></div>
                                </div>

                                <div class="mb-2">
                                    <label class="form-label small">Jenis Kelamin <span class="text-danger">*</span></label>
                                    <select class="form-select form-select-sm" name="profile_gender" id="profile_gender">
                                        <option value="">-- Pilih --</option>
                                        <option value="1">Laki-laki</option>
                                        <option value="2">Perempuan</option>
                                    </select>
                                    <div class="text-danger small d-none" id="profile_gender-error"></div>
                                </div>

                                <div class="mb-2">
                                    <label class="form-label small">Tanggal Lahir <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control form-control-sm" name="profile_dob" id="profile_dob">
                                    <div class="text-danger small d-none" id="profile_dob-error"></div>
                                </div>

                                <div class="mb-2">
                                    <label class="form-label small">Handphone <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-sm" name="profile_handphone1" id="profile_handphone1">
                                    <div class="text-danger small d-none" id="profile_handphone1-error"></div>
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

    function clearErrors() {
        $('.is-invalid').removeClass('is-invalid');
        $('.text-danger.small').addClass('d-none').text('');
    }

    function showError(inputId, errorId, msg) {
        $(inputId).addClass('is-invalid');
        $(errorId).removeClass('d-none').text(msg);
    }

    $('#form-add-staf').on('submit', function(e) {
        e.preventDefault();
        clearErrors();

        var name = $('#profile_fullname').val().trim();
        var email = $('#profile_email').val().trim();
        var pw = $('#profile_password').val();
        var nip = $('#profile_employee_id').val().trim();
        var gender = $('#profile_gender').val();
        var dob = $('#profile_dob').val();
        var hp = $('#profile_handphone1').val().trim();
        var valid = true;

        if (!name) { showError('#profile_fullname', '#profile_fullname-error', 'Nama lengkap wajib diisi'); valid = false; }
        if (!email) { showError('#profile_email', '#profile_email-error', 'Email wajib diisi'); valid = false; }
        else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) { showError('#profile_email', '#profile_email-error', 'Format email tidak valid'); valid = false; }
        if (!pw || pw.length < 6) { showError('#profile_password', '#profile_password-error', 'Password minimal 6 karakter'); valid = false; }
        if (!nip) { showError('#profile_employee_id', '#profile_employee_id-error', 'NIP wajib diisi'); valid = false; }
        if (!gender) { showError('#profile_gender', '#profile_gender-error', 'Jenis kelamin wajib dipilih'); valid = false; }
        if (!dob) { showError('#profile_dob', '#profile_dob-error', 'Tanggal lahir wajib diisi'); valid = false; }
        if (!hp) { showError('#profile_handphone1', '#profile_handphone1-error', 'Nomor handphone wajib diisi'); valid = false; }

        if (!valid) return;

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
