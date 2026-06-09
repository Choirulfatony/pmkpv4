<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-10">

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <?= session()->getFlashdata('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="card card-outline">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-person-gear"></i> Edit Data Staf
                    </h6>
                    <a href="<?= site_url('siimut/staf') ?>" class="btn btn-sm btn-outline-secondary float-end">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>
                <div class="card-body">
                    <form id="form-edit-staf" novalidate>
                        <input type="hidden" name="profile_id" value="<?= $staff->profile_id ?>">

                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-muted mb-2"><i class="bi bi-person"></i> Data Diri</h6>

                                <div class="mb-2">
                                    <label class="form-label small">Nama Lengkap <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-sm" name="profile_fullname" id="profile_fullname" value="<?= esc($staff->profile_fullname) ?>">
                                    <div class="text-danger small d-none" id="profile_fullname-error"></div>
                                </div>

                                <div class="mb-2">
                                    <label class="form-label small">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control form-control-sm" name="profile_email" id="profile_email" value="<?= esc($staff->profile_email) ?>">
                                    <div class="text-danger small d-none" id="profile_email-error"></div>
                                </div>

                                <div class="mb-2">
                                    <label class="form-label small">NIP / Employee ID <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-sm" name="profile_employee_id" id="profile_employee_id" value="<?= esc($staff->profile_employee_id) ?>">
                                    <div class="text-danger small d-none" id="profile_employee_id-error"></div>
                                </div>

                                <div class="mb-2">
                                    <label class="form-label small">Jenis Kelamin <span class="text-danger">*</span></label>
                                    <select class="form-select form-select-sm" name="profile_gender" id="profile_gender">
                                        <option value="">-- Pilih --</option>
                                        <option value="1" <?= $staff->profile_gender == 1 ? 'selected' : '' ?>>Laki-laki</option>
                                        <option value="2" <?= $staff->profile_gender == 2 ? 'selected' : '' ?>>Perempuan</option>
                                    </select>
                                    <div class="text-danger small d-none" id="profile_gender-error"></div>
                                </div>

                                <div class="mb-2">
                                    <label class="form-label small">Tanggal Lahir <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control form-control-sm" name="profile_dob" id="profile_dob" value="<?= $staff->profile_dob ?>">
                                    <div class="text-danger small d-none" id="profile_dob-error"></div>
                                </div>

                                <div class="mb-2">
                                    <label class="form-label small">Handphone <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-sm" name="profile_handphone1" id="profile_handphone1" value="<?= esc($staff->profile_handphone1) ?>">
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
                                            <option value="<?= esc($g->group_id) ?>" <?= $staff->profile_group_id == $g->group_id ? 'selected' : '' ?>>
                                                <?= esc($g->group_name) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="mb-2">
                                    <label class="form-label small">Unit Kerja / Departemen</label>
                                    <select class="form-select form-select-sm select2" name="profile_department_id">
                                        <option value="">-- Pilih Unit --</option>
                                        <?php foreach ($departments as $d): ?>
                                            <option value="<?= esc($d->department_id) ?>" <?= $staff->profile_department_id == $d->department_id ? 'selected' : '' ?>>
                                                <?= esc($d->department_name) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="mb-2">
                                    <label class="form-label small">Catatan</label>
                                    <textarea class="form-control form-control-sm" name="profile_note" rows="3"><?= esc($staff->profile_note) ?></textarea>
                                </div>

                                <hr>
                                <h6 class="text-muted mb-2"><i class="bi bi-info-circle"></i> Info Akun</h6>

                                <div class="row mb-2">
                                    <div class="col-6">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="toggle_disable"
                                                <?= $staff->profile_disable ? 'checked' : '' ?>
                                                onchange="toggleAccountStatus(this, 'disable')">
                                            <label class="form-check-label small" for="toggle_disable">Nonaktif Akun</label>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small">Status Online</label>
                                        <div>
                                            <?php if ($staff->profile_online_status): ?>
                                                <span class="badge bg-success"><i class="bi bi-circle-fill"></i> Online</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Offline</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-2">
                                    <label class="form-label small">Terakhir Login</label>
                                    <div class="text-muted">
                                        <?= $staff->profile_last_login ? date('d M Y H:i:s', strtotime($staff->profile_last_login)) : '-' ?>
                                    </div>
                                </div>

                                <div class="mb-2">
                                    <label class="form-label small">Terakhir Update</label>
                                    <div class="text-muted">
                                        <?= $staff->profile_update_date ? date('d M Y H:i:s', strtotime($staff->profile_update_date)) : '-' ?>
                                    </div>
                                </div>
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
});
</script>
