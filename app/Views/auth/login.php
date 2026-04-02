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
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted small">Verifikasi</span>
                        <button type="button" 
                                class="btn btn-sm btn-outline-secondary" 
                                id="btnRefreshCaptcha"
                                title="Ganti pertanyaan">
                            <i class="bi bi-arrow-clockwise"></i>
                        </button>
                    </div>
                    <div class="bg-light rounded-2 p-3 text-center" id="captcha_container">
                        <?= $captcha_html ?? session()->get('captcha_html') ?>
                    </div>
                    <div class="mx-3">
                        <input type="number"
                            class="form-control text-center mt-2"
                            name="captcha"
                            placeholder="Jawaban"
                            autocomplete="off"
                            required>
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

            <!-- Divider -->
            <div class="divider d-flex align-items-center my-3">
                <hr class="flex-grow-1">
                <span class="px-3 text-muted small">atau</span>
                <hr class="flex-grow-1">
            </div>

            <!-- Google Login Button -->
            <div class="text-center">
                <a href="<?= site_url('auth/google-login') ?>" class="btn btn-outline-danger btn-lg w-100 d-flex align-items-center justify-content-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 48 48">
                        <path fill="#FFC107" d="M43.611,20.083H42V20H24v8h11.303c-1.649,4.657-6.08,8-11.303,8c-6.627,0-12-5.373-12-12c0-6.627,5.373-12,12-12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C12.955,4,4,12.955,4,24c0,11.045,8.955,20,20,20c11.045,0,20-8.955,20-20C44,22.659,43.862,21.35,43.611,20.083z"/>
                        <path fill="#FF3D00" d="M6.306,14.691l6.571,4.819C14.655,15.108,18.961,12,24,12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C16.318,4,9.656,8.337,6.306,14.691z"/>
                        <path fill="#4CAF50" d="M24,44c5.166,0,9.86-1.977,13.409-5.192l-6.19-5.238C29.211,35.091,26.715,36,24,36c-5.202,0-9.619-3.317-11.283-7.946l-6.522,5.025C9.505,39.556,16.227,44,24,44z"/>
                        <path fill="#1976D2" d="M43.611,20.083H42V20H24v8h11.303c-0.792,2.237-2.231,4.166-4.087,5.571c0.001-0.001,0.002-0.001,0.003-0.002l6.19,5.238C36.971,39.205,44,34,44,24C44,22.659,43.862,21.35,43.611,20.083z"/>
                    </svg>
                    Login dengan Google
                </a>
            </div>

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