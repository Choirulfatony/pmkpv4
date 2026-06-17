<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-10">

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <?= session()->getFlashdata('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            
            <div class="row">
                <div class="col-md-4 mb-3">
                    <div class="card">
                        <div class="card-body text-center">
                            <?php
                            helper('profile');
                            $profilePic = $user->profile_photo ?: session('profile_picture');
                            $photoUrl = get_profile_picture($profilePic, $user->profile_id, $user->profile_fullname);
                            ?>
                            <img src="<?= $photoUrl ?>" alt="Photo" class="img-fluid rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover; border: 3px solid #dee2e6;">
                            <h5 class="mb-0"><?= esc($user->profile_fullname) ?></h5>
                            <p class="text-muted small"><?= esc($user->group_name ?? '-') ?></p>

                            <hr>
                            <button type="button" class="btn btn-outline-primary btn-sm" id="btn-upload-photo">
                                <i class="bi bi-camera"></i> Ganti Foto
                            </button>
                            <form id="form-upload-photo" style="display:none;">
                                <input type="file" name="profile_photo" id="profile_photo" accept="image/jpeg,image/png,image/gif,image/webp" style="display:none;">
                            </form>

                            <?php if (session('logged_in')): ?>
                            <hr>
                            <a href="<?= site_url('auth/google-sync') ?>" class="btn btn-outline-danger btn-sm">
                                <i class="bi bi-google"></i> Sync dari Google
                            </a>
                            <?php endif; ?>

                            <hr>

                            <div class="text-start small">
                                <div class="mb-1"><strong>Unit:</strong> <?= esc($user->department_name ?? '-') ?></div>
                                <div class="mb-1"><strong>Role:</strong> <?= esc(session('user_role') ?? '-') ?></div>
                                <div class="mb-1"><strong>NIP:</strong> <?= esc($user->profile_employee_id ?? '-') ?></div>
                                <div class="mb-1"><strong>Email:</strong> <?= esc($user->profile_email ?? '-') ?></div>
                                <div class="mb-1"><strong>Terdaftar:</strong> <?= $user->profile_insert_date ? date('d M Y', strtotime($user->profile_insert_date)) : '-' ?></div>
                                <div class="mb-1"><strong>Terakhir Login:</strong> <?= $user->profile_last_login ? date('d M Y H:i', strtotime($user->profile_last_login)) : '-' ?></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="card mb-3">
                        <div class="card-header">
                            <h6 class="card-title mb-0"><i class="bi bi-person"></i> Data Diri</h6>
                        </div>
                        <div class="card-body">
                            <form id="form-update-profile">
                                <div class="row g-2">
                                    <div class="col-lg-4 col-md-6">
                                        <label class="form-label small mb-1">Nama Lengkap <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control form-control-sm" name="profile_fullname" id="p_fullname" value="<?= esc($user->profile_fullname) ?>">
                                        <div class="text-danger small d-none" id="p_fullname-error"></div>
                                    </div>
                                    <div class="col-lg-4 col-md-6">
                                        <label class="form-label small mb-1">Email <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control form-control-sm" name="profile_email" id="p_email" value="<?= esc($user->profile_email) ?>">
                                        <div class="text-danger small d-none" id="p_email-error"></div>
                                    </div>
                                    <div class="col-lg-4 col-md-6">
                                        <label class="form-label small mb-1">NIP / Employee ID</label>
                                        <input type="text" class="form-control form-control-sm" name="profile_employee_id" value="<?= esc($user->profile_employee_id) ?>">
                                    </div>
                                    <div class="col-lg-4 col-md-6">
                                        <label class="form-label small mb-1">Jenis Kelamin</label>
                                        <select class="form-select form-select-sm" name="profile_gender">
                                            <option value="">-- Pilih --</option>
                                            <option value="1" <?= $user->profile_gender == 1 ? 'selected' : '' ?>>Laki-laki</option>
                                            <option value="2" <?= $user->profile_gender == 2 ? 'selected' : '' ?>>Perempuan</option>
                                        </select>
                                    </div>
                                    <div class="col-lg-4 col-md-6">
                                        <label class="form-label small mb-1">Tempat Lahir</label>
                                        <input type="text" class="form-control form-control-sm" name="profile_birth_place" value="<?= esc($user->profile_birth_place) ?>">
                                    </div>
                                    <div class="col-lg-4 col-md-6">
                                        <label class="form-label small mb-1">Tanggal Lahir</label>
                                        <input type="date" class="form-control form-control-sm" name="profile_dob" value="<?= $user->profile_dob ?>">
                                    </div>
                                    <div class="col-lg-4 col-md-6">
                                        <label class="form-label small mb-1">No. Handphone</label>
                                        <input type="text" class="form-control form-control-sm" name="profile_handphone1" value="<?= esc($user->profile_handphone1) ?>">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label small mb-1">Alamat</label>
                                        <textarea class="form-control form-control-sm" name="profile_address" rows="2"><?= esc($user->profile_address) ?></textarea>
                                    </div>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary btn-sm" id="btn-save-profile">
                                        <i class="bi bi-check-lg"></i> Simpan Perubahan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0"><i class="bi bi-lock"></i> Ubah Password</h6>
                        </div>
                        <div class="card-body">
                            <form id="form-change-password" autocomplete="off">
                                <div class="row g-2">
                                    <div class="col-lg-4 col-md-6">
                                        <label class="form-label small mb-1">Password Saat Ini</label>
                                        <div class="input-group input-group-sm">
                                            <div class="pw-field flex-fill" data-name="current_password" data-id="cp_current"></div>
                                            <button class="btn btn-outline-secondary btn-toggle-pw" type="button" data-target="cp_current">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </div>
                                        <div class="text-danger small d-none" id="cp_current-error"></div>
                                    </div>
                                    <div class="col-lg-4 col-md-6">
                                        <label class="form-label small mb-1">Password Baru</label>
                                        <div class="input-group input-group-sm">
                                            <div class="pw-field flex-fill" data-name="new_password" data-id="cp_new"></div>
                                            <button class="btn btn-outline-secondary btn-toggle-pw" type="button" data-target="cp_new">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </div>
                                        <div class="text-danger small d-none" id="cp_new-error"></div>
                                    </div>
                                    <div class="col-lg-4 col-md-6">
                                        <label class="form-label small mb-1">Konfirmasi Password Baru</label>
                                        <div class="input-group input-group-sm">
                                            <div class="pw-field flex-fill" data-name="confirm_password" data-id="cp_confirm"></div>
                                            <button class="btn btn-outline-secondary btn-toggle-pw" type="button" data-target="cp_confirm">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </div>
                                        <div class="text-danger small d-none" id="cp_confirm-error"></div>
                                    </div>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-warning btn-sm" id="btn-change-password">
                                        <i class="bi bi-key"></i> Ubah Password
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('<style>.pw-mask{-webkit-text-security:disc}.pw-mask::-webkit-text-security{auto}</style>').appendTo('head');
    setTimeout(function() {
        $('.pw-field').each(function() {
            var name = $(this).data('name');
            var id = $(this).data('id');
            var input = $('<input>', {
                type: 'text',
                class: 'form-control pw-mask',
                name: name,
                id: id
            });
            $(this).replaceWith(input);
        });
    }, 100);

    $(document).on('click', '.btn-toggle-pw', function() {
        var target = $('#' + $(this).data('target'));
        var icon = $(this).find('i');
        target.toggleClass('pw-mask');
        if (target.hasClass('pw-mask')) {
            icon.removeClass('bi-eye-slash').addClass('bi-eye');
        } else {
            icon.removeClass('bi-eye').addClass('bi-eye-slash');
        }
    });

    function clearErrors() {
        $('.is-invalid').removeClass('is-invalid');
        $('.text-danger.small').addClass('d-none').text('');
    }

    function showError(inputId, errorId, msg) {
        $(inputId).addClass('is-invalid');
        $(errorId).removeClass('d-none').text(msg);
    }

    $('#btn-upload-photo').on('click', function() {
        $('#profile_photo').click();
    });

    $('#profile_photo').on('change', function() {
        var file = this.files[0];
        if (!file) return;

        var formData = new FormData();
        formData.append('profile_photo', file);

        Swal.fire({
            title: 'Mengupload...',
            text: 'Mohon tunggu',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        $.ajax({
            url: '<?= site_url('siimut/profile/update-photo') ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
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
                        location.reload();
                    });
                } else {
                    Swal.fire('Gagal', res.message, 'error');
                }
            },
            error: function() {
                Swal.fire('Gagal', 'Gagal mengupload foto', 'error');
            }
        });
    });

    $('#form-update-profile').on('submit', function(e) {
        e.preventDefault();
        clearErrors();

        var name = $('#p_fullname').val().trim();
        var email = $('#p_email').val().trim();
        var valid = true;

        if (!name) { showError('#p_fullname', '#p_fullname-error', 'Nama wajib diisi'); valid = false; }
        if (!email) { showError('#p_email', '#p_email-error', 'Email wajib diisi'); valid = false; }
        else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) { showError('#p_email', '#p_email-error', 'Format email tidak valid'); valid = false; }

        if (!valid) return;

        Swal.fire({
            title: 'Simpan Perubahan?',
            text: 'Data profil Anda akan diperbarui.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Simpan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= site_url('siimut/profile/update') ?>',
                    type: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(res) {
                        if (res.status) {
                            toastSuccess(res.message);
                            setTimeout(() => location.reload(), 1500);
                        } else {
                            toastError(res.message);
                        }
                    },
                    error: function() {
                        toastError('Gagal menyimpan profil');
                    }
                });
            }
        });
    });

    $('#form-change-password').on('submit', function(e) {
        e.preventDefault();
        clearErrors();

        var current = $('#cp_current').val().trim();
        var newPw = $('#cp_new').val().trim();
        var confirm = $('#cp_confirm').val().trim();
        var valid = true;

        if (!current) { showError('#cp_current', '#cp_current-error', 'Password saat ini wajib diisi'); valid = false; }
        if (!newPw) { showError('#cp_new', '#cp_new-error', 'Password baru wajib diisi'); valid = false; }
        else if (!/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^a-zA-Z\d]).{8,}$/.test(newPw)) { showError('#cp_new', '#cp_new-error', 'Minimal 8 karakter, huruf besar/kecil, angka, dan simbol'); valid = false; }
        if (!confirm) { showError('#cp_confirm', '#cp_confirm-error', 'Konfirmasi password wajib diisi'); valid = false; }
        else if (newPw !== confirm) { showError('#cp_confirm', '#cp_confirm-error', 'Tidak cocok'); valid = false; }

        if (!valid) return;

        Swal.fire({
            title: 'Ubah Password?',
            text: 'Anda akan diminta login ulang setelah ini.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Ubah!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= site_url('siimut/profile/change-password') ?>',
                    type: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(res) {
                        if (res.status) {
                            toastSuccess(res.message);
                            setTimeout(function() {
                                window.location.href = '<?= site_url('auth/logout') ?>';
                            }, 1500);
                        } else {
                            toastError(res.message);
                        }
                    },
                    error: function() {
                        toastError('Gagal mengubah password');
                    }
                });
            }
        });
    });
});
</script>
