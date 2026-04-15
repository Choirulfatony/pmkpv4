<style>
    html,
    body {
        height: 100%;
    }

    .login-wallpaper {
        min-height: 100vh;
        background: url("<?= base_url('assets/img/rsud.png') ?>") no-repeat;
        background-size: 100% 100%;
    }


    .login-overlay {
        min-height: 100vh;
        background: rgba(43, 185, 15, 0.15);
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 20px 0;
    }

    .login-container {
        width: 100%;
        padding: 30px;
        display: flex;
        justify-content: center;
    }

    .login-card {
        width: 100%;
        max-width: 420px;
        background: #fff;
        border-radius: 10px;
        padding: 35px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.24);
    }

    @media (max-width: 480px) {
        .login-card {
            padding: 25px 20px;
        }
    }

    .captcha-box {
        display: flex;
        align-items: center;
        gap: 5px;
        font-size: 18px;
        font-weight: 600;
        white-space: nowrap;
    }

    .captcha-box br {
        display: none;
    }

    .captcha-input {
        border-radius: 8px;
        font-size: 14px;
        padding: 6px;
    }

    .captcha-wrapper {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }

    .captcha-box {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 38px;
        padding: 0 10px;
        font-size: 18px;
        font-weight: 600;
        white-space: nowrap;
    }

    .captcha-input {
        height: 38px;
        width: 130px;
        border-radius: 8px;
    }
</style>

<section class="login-wallpaper">
    <div class="login-overlay">

        <div class="login-card">

            <h4 class="text-center mb-4 fw-bold">Login Sistem</h4>

            <?php if (session()->getFlashdata('error')) : ?>
                <div class="alert alert-danger text-center">
                    <?= session()->getFlashdata('error', false) ?>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('success')) : ?>
                <div class="alert alert-success text-center">
                    <?= session()->getFlashdata('success', false) ?>
                </div>
            <?php endif; ?>

            <form action="<?= site_url('auth/process') ?>" method="post">

                <div class="divider d-flex align-items-center my-4">
                    <p class="text-center fw-bold mx-3 mb-0"></p>
                </div>

                <div class="form-outline mb-3">
                    <input
                        type="text"
                        name="identity"
                        id="identityInput"
                        class="form-control form-control-lg"
                        placeholder="Email atau NIP"
                        value="<?php 
                        $savedIdentity = '';
                        if (isset($_COOKIE['remember_credential'])) {
                            $decoded = base64_decode($_COOKIE['remember_credential']);
                            $parts = explode('|', $decoded);
                            if (isset($parts[0])) {
                                $savedIdentity = esc($parts[0]);
                            }
                        }
                        echo $savedIdentity;
                        ?>"
                        required>
                    <label class="form-label" id="identityLabel">
                        Email / NIP
                    </label>
                </div>

                <div class="form-outline mb-3">
                    <div class="input-group">
                        <input type="password" name="password" id="passwordInput" class="form-control form-control-lg"
                            placeholder="Password" required>
                        <button type="button" class="btn btn-outline-secondary" id="togglePassword" title="Tampilkan password">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                    <label class="form-label">Password</label>
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <small class="text-muted">Verifikasi</small>
                        <button type="button"
                            class="btn btn-sm btn-light"
                            id="btnRefreshCaptcha">
                            <i class="bi bi-arrow-clockwise"></i>
                        </button>
                    </div>

                    <div class="d-flex align-items-center gap-2 captcha-wrapper">

                        <div id="captcha_container" class="captcha-box">
                            <?= $captcha_html ?? session()->get('captcha_html') ?>
                        </div>

                        <input type="text"
                            class="form-control text-center captcha-input"
                            name="captcha"
                            placeholder="Jawaban"
                            required>

                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center">
                    <div class="form-check mb-0">
                        <input class="form-check-input me-2" type="checkbox" name="remember" id="rememberMe" <?= (isset($_COOKIE['remember_credential']) && $_COOKIE['remember_credential']) ? 'checked' : '' ?>>
                        <label class="form-check-label">Remember me</label>
                    </div>
                </div>

                <div class="text-center text-lg-start mt-4 pt-2">
                    <button type="submit" class="btn btn-primary btn-lg w-100">
                        Login
                    </button>
                </div>

            </form>

            <div class="divider d-flex align-items-center my-3">
                <hr class="flex-grow-1">
                <span class="px-3 text-muted small">atau</span>
                <hr class="flex-grow-1">
            </div>

            <div class="text-center">
                <a href="<?= site_url('auth/google-login') ?>" class="btn btn-outline-danger btn-lg w-100 d-flex align-items-center justify-content-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 48 48">
                        <path fill="#FFC107" d="M43.611,20.083H42V20H24v8h11.303c-1.649,4.657-6.08,8-11.303,8c-6.627,0-12-5.373-12-12c0-6.627,5.373-12,12-12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C12.955,4,4,12.955,4,24c0,11.045,8.955,20,20,20c11.045,0,20-8.955,20-20C44,22.659,43.862,21.35,43.611,20.083z" />
                        <path fill="#FF3D00" d="M6.306,14.691l6.571,4.819C14.655,15.108,18.961,12,24,12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C16.318,4,9.656,8.337,6.306,14.691z" />
                        <path fill="#4CAF50" d="M24,44c5.166,0,9.86-1.977,13.409-5.192l-6.19-5.238C29.211,35.091,26.715,36,24,36c-5.202,0-9.619-3.317-11.283-7.946l-6.522,5.025C9.505,39.556,16.227,44,24,44z" />
                        <path fill="#1976D2" d="M43.611,20.083H42V20H24v8h11.303c-0.792,2.237-2.231,4.166-4.087,5.571c0.001-0.001,0.002-0.001,0.003-0.002l6.19,5.238C36.971,39.205,44,34,44,24C44,22.659,43.862,21.35,43.611,20.083z" />
                    </svg>
                    Login dengan Google
                </a>
            </div>

        </div>

    </div>
</section>

<?php
$registerEmail = session('register_email');
$registerName = session('register_name');
?>
<div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
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
                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-person-fill me-1 text-primary"></i>Nama Lengkap <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                    name="profile_fullname"
                                    class="form-control form-control-lg"
                                    value="<?= esc($registerName ?? '') ?>"
                                    placeholder="Masukkan nama lengkap sesuai KTP"
                                    required>
                                <div class="invalid-feedback">Nama lengkap wajib diisi</div>
                            </div>
                        </div>

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
                                        value="<?= esc($registerEmail ?? '') ?>"
                                        readonly>
                                </div>
                                <small class="text-muted">Email dari Google, tidak dapat diubah</small>
                            </div>
                        </div>

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
                    Sudah punya akun? <a href="#" onclick="closeRegisterModal(); return false;" class="text-primary text-decoration-none fw-semibold">Login di sini</a>
                </small>
            </div>
        </div>
    </div>
</div>

<script>
    function closeRegisterModal() {
        const modal = bootstrap.Modal.getInstance(document.getElementById('registerModal'));
        if (modal) {
            modal.hide();
        }
        fetch("<?= site_url('auth/clear_register_session') ?>", {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        const btn = document.getElementById('btnRefreshCaptcha');
        if (btn) {
            btn.addEventListener('click', function() {
                fetch("<?= site_url('auth/refresh-captcha') ?>")
                    .then(res => res.json())
                    .then(function(data) {
                        document.getElementById('captcha_container').innerHTML = data.captcha_html;
                        document.querySelector("input[name='captcha']").value = '';
                    });
            });
        }

        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('passwordInput');
        if (togglePassword && passwordInput) {
            togglePassword.addEventListener('click', function() {
                const type = passwordInput.type === 'password' ? 'text' : 'password';
                passwordInput.type = type;
                this.querySelector('i').className = type === 'password' ? 'bi bi-eye' : 'bi bi-eye-slash';
            });
        }

        const urlParams = new URLSearchParams(window.location.search);
        const showRegister = urlParams.get('show_register');
        const registerEmail = "<?= session('register_email') ?? '' ?>";

        if (showRegister === '1' || registerEmail) {
            const registerModal = new bootstrap.Modal(document.getElementById('registerModal'));
            registerModal.show();

            const cleanUrl = window.location.protocol + '//' + window.location.host + window.location.pathname;
            window.history.replaceState({ path: cleanUrl }, '', cleanUrl);
        }

        const registerForm = document.getElementById('registerForm');
        if (registerForm) {
            registerForm.addEventListener('submit', function(event) {
                if (!registerForm.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                registerForm.classList.add('was-validated');
            });
        }
    });

    (function() {
        window.addEventListener('pageshow', function(event) {
            if (event.persisted) {
                window.location.replace("<?= site_url('auth') ?>");
            }
        });

        if (window.history && window.history.pushState) {
            window.history.pushState(null, '', window.location.href);
            window.addEventListener('popstate', function() {
                window.location.replace("<?= site_url('auth') ?>");
            });
        }
    })();
</script>