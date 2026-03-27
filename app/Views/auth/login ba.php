<style>
    html,
    body {
        height: 100%;
    }

    .login-wallpaper {
        min-height: 100vh;
        background: url("<?= base_url('assets/img/rsud.png') ?>") center / cover no-repeat;
    }

    .login-overlay {
        min-height: 100vh;
        background: rgba(30, 148, 7, 0.65);
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

<section class="login-wallpaper">
    <div class="login-overlay">

        <div class="login-container">
            <div class="login-card">

                <form action="<?= site_url('auth/process') ?>" method="post">

                    <div class="d-flex flex-row align-items-center justify-content-center justify-content-lg-start">
                        <p class="lead fw-normal mb-0 me-3">Sign in with</p>
                    </div>

                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger text-center">
                            <?= session()->getFlashdata('error'); ?>
                        </div>
                    <?php endif; ?>


                    <div class="divider d-flex align-items-center my-4">
                        <p class="text-center fw-bold mx-3 mb-0"></p>
                    </div>
                    
                    <div class="form-outline mb-4">
                        <input type="email" name="profile_email" class="form-control form-control-lg"
                            placeholder="Email address" required>
                        <label class="form-label">Email address</label>
                    </div>

                    <div class="form-outline mb-3">
                        <input type="password" name="password" class="form-control form-control-lg"
                            placeholder="Password" required>
                        <label class="form-label">Password</label>
                    </div>



                    <div class="form-outline mb-3">
                        <div class="row g-2 align-items-center">
                            <div class="col-5" id="captcha_container">
                                <?= $captcha_image ?>
                            </div>


                            <div class="col-4">
                                <input type="text"
                                    class="form-control"
                                    name="captcha"
                                    placeholder="Enter Captcha..."
                                    required>
                            </div>

                            <div class="col-3">
                                <button type="button"
                                    class="btn btn-outline-secondary w-100"
                                    style="width:42px; height:42px;"
                                    title="Reload"
                                    id="btnRefreshCaptcha">
                                    <i class="bi bi-arrow-clockwise"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- onclick="window.location.href='<?= site_url('auth/refresh_captcha') ?>' -->

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

    </div>
</section>

<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script> -->

<script>
    // $(document).ready(function() {

    //     $("#btnRefreshCaptcha").click(function() {

    //         $.ajax({
    //             url: "<?= base_url('auth/refresh-captcha') ?>",
    //             type: "GET",
    //             dataType: "json",
    //             success: function(response) {

    //                 // Ganti isi div captcha
    //                 $("#captcha_container").html(response.captcha_image);

    //                 // Kosongkan input captcha
    //                 $("input[name='captcha']").val('');
    //             },
    //             error: function(xhr, status, error) {
    //                 console.log(xhr.responseText);
    //                 alert("Gagal refresh captcha");
    //             }
    //         });

    //     });

    //     $('#show-password').click(function() {

    //         let passwordInput = $('#password');
    //         let icon = $(this).find('i');

    //         if (passwordInput.attr('type') === 'password') {
    //             passwordInput.attr('type', 'text');
    //             icon.removeClass('fa-eye').addClass('fa-eye-slash');
    //         } else {
    //             passwordInput.attr('type', 'password');
    //             icon.removeClass('fa-eye-slash').addClass('fa-eye');
    //         }

    //     });
    // });
    document.addEventListener('DOMContentLoaded', function() {

        const btn = document.getElementById('btnRefreshCaptcha');
        if (!btn) return;

        btn.addEventListener('click', function() {
            fetch("<?= site_url('auth/refresh-captcha') ?>")
                .then(res => res.json())
                .then(data => {
                    document.getElementById('captcha_container').innerHTML = data.captcha_image;
                    document.querySelector("input[name='captcha']").value = '';
                })
                .catch(err => {
                    console.error(err);
                    alert('Gagal refresh captcha');
                });
        });

    });
</script>