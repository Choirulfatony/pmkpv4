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
                            $staffPic = $staff->profile_photo ?? '';
                            $photoUrl = get_profile_picture($staffPic, $staff->profile_id, $staff->profile_fullname);
                            ?>
                            <img src="<?= $photoUrl ?>" alt="Photo" class="img-fluid rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover; border: 3px solid #dee2e6;">
                            <h5 class="mb-0"><?= esc($staff->profile_fullname) ?></h5>
                            <p class="text-muted small"><?= esc($staff->group_name ?? '-') ?></p>

                            <hr>

                            <div class="d-flex justify-content-center gap-2 mb-2">
                                <div>
                                    <?php if ($staff->profile_disable): ?>
                                        <span class="badge bg-danger">Nonaktif</span>
                                    <?php else: ?>
                                        <span class="badge bg-success">Aktif</span>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <?php if ($staff->profile_online_status): ?>
                                        <span class="badge bg-success"><i class="bi bi-circle-fill"></i> Online</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Offline</span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <hr>

                            <div class="text-start small">
                                <div class="mb-1"><strong>Unit:</strong> <?= esc($staff->department_name ?? '-') ?></div>
                                <div class="mb-1"><strong>NIP:</strong> <?= esc($staff->profile_employee_id ?? '-') ?></div>
                                <div class="mb-1"><strong>Email:</strong> <?= esc($staff->profile_email ?? '-') ?></div>
                                <div class="mb-1"><strong>Gender:</strong> <?= $staff->profile_gender == 1 ? 'Laki-laki' : ($staff->profile_gender == 2 ? 'Perempuan' : '-') ?></div>
                                <div class="mb-1"><strong>Handphone:</strong> <?= esc($staff->profile_handphone1 ?? '-') ?></div>
                                <div class="mb-1"><strong>Terdaftar:</strong> <?= $staff->profile_insert_date ? date('d M Y', strtotime($staff->profile_insert_date)) : '-' ?></div>
                                <div class="mb-1"><strong>Terakhir Login:</strong> <?= $staff->profile_last_login ? date('d M Y H:i', strtotime($staff->profile_last_login)) : '-' ?></div>
                                <div class="mb-1"><strong>Password:</strong>
                                    <div class="input-group input-group-sm mt-1">
                                        <input type="text" class="form-control pw-mask" id="showPassword" value="<?= esc($staff->profile_confirm_password ?? '') ?>" readonly style="font-size:inherit;">
                                        <button class="btn btn-outline-secondary btn-toggle-pw-show" type="button">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="card mb-3">
                        <div class="card-header">
                            <h6 class="card-title mb-0"><i class="bi bi-person-gear"></i> Edit Data Staf</h6>
                            <a href="<?= site_url('siimut/staf') ?>" class="btn btn-sm btn-outline-secondary float-end">
                                <i class="bi bi-arrow-left"></i> Kembali
                            </a>
                        </div>
                        <div class="card-body">
                            <form id="form-edit-staf" novalidate>
                                <input type="hidden" name="profile_id" value="<?= $staff->profile_id ?>">
                                <div class="row g-2">
                                    <div class="col-lg-4 col-md-6">
                                        <label class="form-label small mb-1">Nama Lengkap <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control form-control-sm" name="profile_fullname" id="profile_fullname" value="<?= esc($staff->profile_fullname) ?>">
                                        <div class="text-danger small d-none" id="profile_fullname-error"></div>
                                    </div>
                                    <div class="col-lg-4 col-md-6">
                                        <label class="form-label small mb-1">Email <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control form-control-sm" name="profile_email" id="profile_email" value="<?= esc($staff->profile_email) ?>">
                                        <div class="text-danger small d-none" id="profile_email-error"></div>
                                    </div>
                                    <div class="col-lg-4 col-md-6">
                                        <label class="form-label small mb-1">NIP / Employee ID <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control form-control-sm" name="profile_employee_id" id="profile_employee_id" value="<?= esc($staff->profile_employee_id) ?>">
                                        <div class="text-danger small d-none" id="profile_employee_id-error"></div>
                                    </div>
                                    <div class="col-lg-4 col-md-6">
                                        <label class="form-label small mb-1">Jenis Kelamin <span class="text-danger">*</span></label>
                                        <select class="form-select form-select-sm" name="profile_gender" id="profile_gender">
                                            <option value="">-- Pilih --</option>
                                            <option value="1" <?= $staff->profile_gender == 1 ? 'selected' : '' ?>>Laki-laki</option>
                                            <option value="2" <?= $staff->profile_gender == 2 ? 'selected' : '' ?>>Perempuan</option>
                                        </select>
                                        <div class="text-danger small d-none" id="profile_gender-error"></div>
                                    </div>
                                    <div class="col-lg-4 col-md-6">
                                        <label class="form-label small mb-1">Tanggal Lahir <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control form-control-sm" name="profile_dob" id="profile_dob" value="<?= $staff->profile_dob ?>">
                                        <div class="text-danger small d-none" id="profile_dob-error"></div>
                                    </div>
                                    <div class="col-lg-4 col-md-6">
                                        <label class="form-label small mb-1">Handphone <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control form-control-sm" name="profile_handphone1" id="profile_handphone1" value="<?= esc($staff->profile_handphone1) ?>">
                                        <div class="text-danger small d-none" id="profile_handphone1-error"></div>
                                    </div>
                                    <div class="col-lg-4 col-md-6">
                                        <label class="form-label small mb-1">Grup Akses</label>
                                        <select class="form-select form-select-sm select2" name="profile_group_id">
                                            <option value="">-- Pilih Grup --</option>
                                            <?php foreach ($groups as $g): ?>
                                                <option value="<?= esc($g->group_id) ?>" <?= $staff->profile_group_id == $g->group_id ? 'selected' : '' ?>>
                                                    <?= esc($g->group_name) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-lg-4 col-md-6">
                                        <label class="form-label small mb-1">Unit Kerja / Departemen</label>
                                        <select class="form-select form-select-sm select2" name="profile_department_id">
                                            <option value="">-- Pilih Unit --</option>
                                            <?php foreach ($departments as $d): ?>
                                                <option value="<?= esc($d->department_id) ?>" <?= $staff->profile_department_id == $d->department_id ? 'selected' : '' ?>>
                                                    <?= esc($d->department_name) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label small mb-1">Catatan</label>
                                        <textarea class="form-control form-control-sm" name="profile_note" rows="2"><?= esc($staff->profile_note) ?></textarea>
                                    </div>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="<?= site_url('siimut/staf') ?>" class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-x-lg"></i> Batal
                                    </a>
                                    <button type="submit" class="btn btn-sm btn-primary" id="btn-save">
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
                            <form id="form-change-password-staf" autocomplete="off">
                                <input type="hidden" name="profile_id" value="<?= $staff->profile_id ?>">
                                <div class="row g-2">
                                    <div class="col-lg-4 col-md-6">
                                        <label class="form-label small mb-1">Password Saat Ini <span class="text-danger">*</span></label>
                                        <div class="input-group input-group-sm">
                                            <div class="pw-field flex-fill" data-name="current_password" data-id="cp_current"></div>
                                            <button class="btn btn-outline-secondary btn-toggle-pw" type="button" data-target="cp_current">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </div>
                                        <div class="text-danger small d-none" id="cp_current-error"></div>
                                    </div>
                                    <div class="col-lg-4 col-md-6">
                                        <label class="form-label small mb-1">Password Baru <span class="text-danger">*</span></label>
                                        <div class="input-group input-group-sm">
                                            <div class="pw-field flex-fill" data-name="new_password" data-id="cp_new"></div>
                                            <button class="btn btn-outline-secondary btn-toggle-pw" type="button" data-target="cp_new">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </div>
                                        <div class="text-danger small d-none" id="cp_new-error"></div>
                                    </div>
                                    <div class="col-lg-4 col-md-6">
                                        <label class="form-label small mb-1">Konfirmasi Password Baru <span class="text-danger">*</span></label>
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
var profileId = <?= $staff->profile_id ?>;

function toggleAccountStatus(el, type) {
    var url = type === 'disable'
        ? '<?= site_url('siimut/staf/toggle-disable/') ?>' + profileId
        : '<?= site_url('siimut/staf/toggle-online/') ?>' + profileId;

    $.ajax({
        url: url,
        type: 'POST',
        dataType: 'json',
        success: function(res) {
            if (res.status) {
                toastSuccess(res.message);
            } else {
                toastError(res.message);
                el.checked = !el.checked;
            }
        },
        error: function() {
            toastError('Gagal mengubah status');
            el.checked = !el.checked;
        }
    });
}

$(document).ready(function() {
    $('.select2').select2({
        theme: 'bootstrap-5',
        width: '100%',
        dropdownParent: $('#form-edit-staf')
    });

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

    $(document).on('click', '.btn-toggle-pw-show', function() {
        var input = $('#showPassword');
        var icon = $(this).find('i');
        input.toggleClass('pw-mask');
        icon.toggleClass('bi-eye bi-eye-slash');
    });

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

    $('#form-edit-staf').on('submit', function(e) {
        e.preventDefault();
        clearErrors();

        var name = $('#profile_fullname').val().trim();
        var email = $('#profile_email').val().trim();
        var nip = $('#profile_employee_id').val().trim();
        var gender = $('#profile_gender').val();
        var dob = $('#profile_dob').val();
        var hp = $('#profile_handphone1').val().trim();
        var valid = true;

        if (!name) { showError('#profile_fullname', '#profile_fullname-error', 'Nama lengkap wajib diisi'); valid = false; }
        if (!email) { showError('#profile_email', '#profile_email-error', 'Email wajib diisi'); valid = false; }
        else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) { showError('#profile_email', '#profile_email-error', 'Format email tidak valid'); valid = false; }
        if (!nip) { showError('#profile_employee_id', '#profile_employee_id-error', 'NIP wajib diisi'); valid = false; }
        if (!gender) { showError('#profile_gender', '#profile_gender-error', 'Jenis kelamin wajib dipilih'); valid = false; }
        if (!dob) { showError('#profile_dob', '#profile_dob-error', 'Tanggal lahir wajib diisi'); valid = false; }
        if (!hp) { showError('#profile_handphone1', '#profile_handphone1-error', 'Nomor handphone wajib diisi'); valid = false; }

        if (!valid) return;

        var id = $('input[name="profile_id"]').val();

        Swal.fire({
            title: 'Simpan Perubahan?',
            text: 'Data staf akan diperbarui.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Simpan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= site_url('siimut/staf/update/') ?>' + id,
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

    $('#form-change-password-staf').on('submit', function(e) {
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

        var id = $('input[name="profile_id"]').val();

        Swal.fire({
            title: 'Ubah Password?',
            text: 'Password staf ' + profileId + ' akan diubah.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Ubah!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= site_url('siimut/staf/change-password/') ?>' + id,
                    type: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(res) {
                        if (res.status) {
                            toastSuccess(res.message);
                            $('#cp_current').val('');
                            $('#cp_new').val('');
                            $('#cp_confirm').val('');
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
