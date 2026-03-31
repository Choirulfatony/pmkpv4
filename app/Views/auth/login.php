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
        background: rgba(43, 185, 15, 0.15);
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
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.24);
    }
</style>

<section class="login-wallpaper">
    <div class="login-overlay">

        <div class="login-card">

            <h4 class="text-center mb-4 fw-bold">Login Sistem</h4>

            <?php if (session()->getFlashdata('error')) : ?>
                <div class="alert alert-danger text-center">
                    <?= session()->getFlashdata('error') ?>
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
                        required>
                    <label class="form-label" id="identityLabel">
                        Email / NIP
                    </label>
                </div>

                <div class="form-outline mb-3">
                    <input type="password" name="password" class="form-control form-control-lg"
                        placeholder="Password" required>
                    <label class="form-label">Password</label>
                </div>

                <!-- CAPTCHA MATH -->
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted small fw-medium">Verifikasi Keamanan</span>
                            <button type="button" 
                                    class="btn btn-sm btn-outline-primary rounded-pill px-3" 
                                    id="btnRefreshCaptcha"
                                    title="Ganti pertanyaan">
                                <i class="bi bi-arrow-clockwise"></i> Baru
                            </button>
                        </div>
                        <div class="bg-light rounded-3 p-4 text-center mb-3" id="captcha_container">
                            <?= $captcha_html ?>
                        </div>
                        <input type="number"
                            class="form-control form-control-lg text-center fw-bold fs-5"
                            name="captcha"
                            placeholder="?"
                            autocomplete="off"
                            required>
                        <div class="text-center mt-2">
                            <small class="text-muted">Masukkan hasil perhitungan di atas</small>
                        </div>
                    </div>
                </div>


                <div class="d-flex justify-content-between align-items-center">
                    <div class="form-check mb-0">
                        <input class="form-check-input me-2" type="checkbox" name="remember">
                        <label class="form-check-label">Remember me</label>
                    </div>
                    <a href="#" class="text-body">Forgot password?</a>
                </div>

                <div class="text-center text-lg-start mt-4 pt-2">
                    <button type="submit" class="btn btn-primary btn-lg w-100">
                        Login
                    </button>
                </div>

            </form>
        </div>

    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const btn = document.getElementById('btnRefreshCaptcha');
        if (!btn) return;

        btn.addEventListener('click', () => {
            fetch("<?= site_url('auth/refresh-captcha') ?>")
                .then(res => res.json())
                .then(data => {
                    document.getElementById('captcha_container').innerHTML = data.captcha_html;
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