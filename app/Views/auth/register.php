<div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="registerModalLabel">
                    <i class="bi bi-person-plus-fill me-2"></i>Registrasi Akun Baru
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <?php if (session()->getFlashdata('error')) : ?>
                <div class="px-4 pt-3">
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <?= session()->getFlashdata('error') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                </div>
            <?php endif; ?>

            <div class="modal-body p-4">
                <div class="alert alert-info d-flex align-items-center mb-4" role="alert">
                    <i class="bi bi-info-circle-fill me-2 fs-5"></i>
                    <div>
                        Lengkapi data berikut untuk menyelesaikan pendaftaran. Email telah terisi otomatis dari akun Google Anda.
                    </div>
                </div>

                <form action="<?= site_url('auth/register/process') ?>" method="post" id="registerForm">
                    <div class="row g-3">
                        <!-- Nama Lengkap -->
                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-person-fill me-1 text-primary"></i>Nama Lengkap <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       name="profile_fullname" 
                                       class="form-control form-control-lg" 
                                       value="<?= esc($name ?? '') ?>" 
                                       placeholder="Masukkan nama lengkap sesuai KTP"
                                       required>
                                <div class="invalid-feedback">Nama lengkap wajib diisi</div>
                            </div>
                        </div>

                        <!-- Email Google -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-envelope-fill me-1 text-primary"></i>Email Google <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-google text-danger"></i></span>
                                    <input type="email" 
                                           name="profile_email" 
                                           class="form-control form-control-lg" 
                                           value="<?= esc($email ?? '') ?>" 
                                           readonly>
                                </div>
                                <small class="text-muted">Email dari Google, tidak dapat diubah</small>
                            </div>
                        </div>

                        <!-- Jenis Kelamin -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-gender-ambiguous me-1 text-primary"></i>Jenis Kelamin
                                </label>
                                <select name="profile_gender" class="form-select form-select-lg">
                                    <option value="">-- Pilih --</option>
                                    <option value="1">Laki-laki</option>
                                    <option value="2">Perempuan</option>
                                </select>
                            </div>
                        </div>

                        <!-- Tempat Lahir -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-building me-1 text-primary"></i>Tempat Lahir
                                </label>
                                <input type="text" 
                                       name="profile_birth_place" 
                                       class="form-control form-control-lg" 
                                       placeholder="Contoh: Surabaya">
                            </div>
                        </div>

                        <!-- Tanggal Lahir -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-calendar-event me-1 text-primary"></i>Tanggal Lahir
                                </label>
                                <input type="date" 
                                       name="profile_dob" 
                                       class="form-control form-control-lg">
                            </div>
                        </div>

                        <!-- Nomor HP 1 -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-phone-fill me-1 text-primary"></i>Nomor HP 1
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-whatsapp text-success"></i></span>
                                    <input type="text" 
                                           name="profile_handphone1" 
                                           class="form-control form-control-lg" 
                                           placeholder="08xxxxxxxxxx">
                                </div>
                            </div>
                        </div>

                        <!-- Nomor HP 2 -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-phone me-1 text-primary"></i>Nomor HP 2 <small class="text-muted">(opsional)</small>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                                    <input type="text" 
                                           name="profile_handphone2" 
                                           class="form-control form-control-lg" 
                                           placeholder="08xxxxxxxxxx">
                                </div>
                            </div>
                        </div>

                        <!-- Info Password -->
                        <div class="col-12">
                            <div class="alert alert-light border mb-0">
                                <i class="bi bi-shield-lock-fill text-success me-2"></i>
                                <strong>Informasi:</strong> Password default adalah tanggal lahir Anda (format: YYYY-MM-DD). Anda dapat mengganti password setelah login.
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-check-circle-fill me-2"></i>Daftar Sekarang
                        </button>
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-2"></i>Batal
                        </button>
                    </div>
                </form>
            </div>
            
            <div class="modal-footer justify-content-center bg-light">
                <small class="text-muted">
                    Sudah punya akun? <a href="<?= site_url('auth') ?>" class="text-primary text-decoration-none fw-semibold">Login di sini</a>
                </small>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Show modal if registration data exists
    <?php if (!empty($email)) : ?>
    var registerModal = new bootstrap.Modal(document.getElementById('registerModal'));
    registerModal.show();
    <?php endif; ?>
    
    // Form validation
    const form = document.getElementById('registerForm');
    form.addEventListener('submit', function(event) {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        }
        form.classList.add('was-validated');
    });
});
</script>
