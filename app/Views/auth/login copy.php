<style>
    html,
    body {
        height: 100%;
    }

    .login-wallpaper {
        min-height: 100vh;
        /* Memaksa lebar 100% dan tinggi 100% */
        background: url("<?= base_url('assets/img/rsud.png') ?>") no-repeat;
        background-size: 100% 100%;
    }


    .login-overlay {
        min-height: 100vh;
        background: rgba(30, 148, 7, 0.42);
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .login-container {
        width: 100%;
        padding: 30px;
        display: flex;
        justify-content: center;
    }

    .login-card {
        width: 380px;
        background: #fff;
        border-radius: 10px;
        padding: 35px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, .25);
    }
</style>

<main class="main">
    <section class="hero section position-relative overflow-hidden vh-100 d-flex align-items-center text-white">
        <img src="<?= base_url('public/assetslogin/img/hero-bg-2.jpg') ?>" alt="Background" class="position-absolute top-0 start-0 w-100 h-100" style="object-fit: cover; z-index: -2;" />
        <div class="position-absolute top-0 start-0 w-100 h-100" style="background: rgba(28, 129, 8, 0.65); z-index: -1;"></div>

        <div class="container">
            <div class="row gy-5 align-items-center justify-content-between">

                <div class="col-lg-6 order-2 order-lg-1" data-aos="fade-up">
                    <?php if (isset($error)) : ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= $error ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <h1 class="display-4 fw-bold mb-0">RSUD dr. SOEDONO</h1>
                    <h3 class="h2 fw-light mb-4">Prov Jawa Timur</h3>
                    <h2 class="display-6 fw-bold text-uppercase tracking-wider mb-3">SIIMUT</h2>
                    <p class="lead mb-4 opacity-75">
                        SI-imut (Sistem Informasi Indikator Mutu Rumah Sakit)
                    </p>

                    <div class="d-flex">
                        <button id="bton-login" class="btn btn-light btn-lg rounded-pill px-5 fw-bold shadow-sm">
                            Login <i class="fa fa-arrow-right ms-2"></i>
                        </button>
                    </div>
                </div>

                <div class="col-lg-5 order-1 order-lg-2">

                    <div id="tampil_login" class="card border-0 shadow-lg rounded-4 overflow-hidden d-none">
                        <div class="card-body p-4 p-md-5 text-dark">
                            <h4 class="text-center fw-bold mb-4">Masuk ke Sistem</h4>

                            <form action="<?= base_url('main/onproces') ?>" method="POST">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Username</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fa fa-user text-muted"></i></span>
                                        <input type="text" class="form-control bg-light border-start-0" name="username" id="username" placeholder="Masukkan username..." required>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Password</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fa fa-lock text-muted"></i></span>
                                        <input type="password" class="form-control bg-light border-start-0 border-end-0" name="password" id="password" placeholder="••••••••" required>
                                        <span class="input-group-text bg-light border-start-0 cursor-pointer show-pass" id="show-password" style="cursor: pointer;">
                                            <i class="fa fa-eye text-muted"></i>
                                        </span>
                                    </div>
                                </div>

                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-success btn-lg fw-bold rounded-3">Masuk</button>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mt-3 small">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="remember">
                                        <label for="remember" class="form-check-label text-muted">Ingatkan saya</label>
                                    </div>
                                    <a href="#" onclick="forget();" class="text-decoration-none text-success fw-semibold">Lupa Password?</a>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div id="tampil_logo" class="text-center text-lg-end">
                        <img src="<?= base_url('public/assetslogin/img/hero-img.png') ?>" class="img-fluid animated" alt="Hero Image" style="max-height: 450px;">
                    </div>

                </div>
            </div>
        </div>
    </section>
</main>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const btnLogin = document.getElementById('bton-login');
        const divLogin = document.getElementById('tampil_login');
        const divLogo = document.getElementById('tampil_logo');

        if (btnLogin) {
            btnLogin.addEventListener('click', function() {
                // 1. Beri efek pudar pada logo
                divLogo.style.transition = "opacity 0.5s ease";
                divLogo.style.opacity = "0";

                setTimeout(() => {
                    // 2. Sembunyikan logo sepenuhnya & munculkan container login
                    divLogo.classList.add('d-none');
                    divLogin.classList.remove('d-none');

                    // 3. Efek muncul perlahan (fade in) untuk form login
                    divLogin.style.opacity = "0";
                    divLogin.style.transition = "opacity 0.5s ease";

                    // Trigger reflow untuk menjalankan transisi
                    divLogin.offsetHeight;
                    divLogin.style.opacity = "1";
                }, 500);
            });
        }

        // Skrip toggle lihat password
        const togglePassword = document.getElementById('show-password');
        const passwordInput = document.getElementById('password');

        if (togglePassword) {
            togglePassword.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                this.querySelector('i').classList.toggle('fa-eye');
                this.querySelector('i').classList.toggle('fa-eye-slash');
            });
        }
    });



    document.addEventListener('DOMContentLoaded', () => {
        const btn = document.getElementById('btnRefreshCaptcha');
        if (!btn) return;

        btn.addEventListener('click', () => {
            fetch("<?= site_url('auth/refresh-captcha') ?>")
                .then(res => res.json())
                .then(data => {
                    document.getElementById('captcha_container').innerHTML = data.captcha_image;
                    document.querySelector("input[name='captcha']").value = '';
                });
        });
    });

    (function() {
        // Cegah BFCache
        window.addEventListener('pageshow', function(event) {
            if (event.persisted) {
                window.location.replace("<?= site_url('auth') ?>");
            }
        });

        // Cegah tombol back
        if (window.history && window.history.pushState) {
            window.history.pushState(null, '', window.location.href);
            window.addEventListener('popstate', function() {
                window.location.replace("<?= site_url('auth') ?>");
            });
        }
    })();
</script>