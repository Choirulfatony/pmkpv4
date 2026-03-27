<style>
    /* =========================
   ISOLATED LOGIN PAGE
   AdminLTE 4 + Bootstrap 5
   ========================= */

    .login-page-custom .form-control {
        color: #212529;
    }

    .login-page-custom .form-control::placeholder {
        color: #6c757d;
    }

    .login-page-custom .form-control:focus {
        color: #212529;
        border-color: #ced4da;
        box-shadow: none;
    }

    .login-page-custom .input-group:focus-within .form-control {
        color: #212529;
    }

    /* matikan efek validasi hijau */
    .login-page-custom .is-valid,
    .login-page-custom .was-validated .form-control:valid {
        border-color: #ced4da;
        background-image: none;
    }

    /* icon input group tetap netral */
    .login-page-custom .input-group-text {
        background-color: #f8f9fa;
        color: #6c757d;
    }
</style>

<div id="areaLogin">
    <div class="login-page-custom">
        <div class="login-overlay">
            <div class="login-container">
                <div class="card card-outline card-primary mx-auto"
                    style="max-width:400px;width:100%;">

                    <div class="card-header text-center">
                        <h4><b>Login IKPRS</b></h4>
                    </div>

                    <div class="card-body">
                        <form id="formLoginHris">

                            <div class="input-group mb-3">
                                <input type="text" name="nip" class="form-control"
                                    placeholder="NIP" required>
                                <span class="input-group-text">
                                    <i class="bi bi-person-circle"></i>
                                </span>
                            </div>

                            <div class="input-group mb-3">
                                <input type="password" name="password" class="form-control"
                                    placeholder="Password" required>
                                <span class="input-group-text">
                                    <i class="bi bi-lock"></i>
                                </span>
                            </div>

                            <button class="btn btn-primary w-100">
                                Login IKPRS
                            </button>

                        </form>
                    </div>

                </div>

            </div>
        </div>

    </div>
</div>



<script>
    $(document).on("submit", "#formLoginHris", function(e) {
        e.preventDefault();
        $.ajax({
            url: "<?= site_url('ikprs/login-process') ?>",
            type: "POST",
            data: $(this).serialize(),
            dataType: "json",
            success: function(res) {

                if (res.status === "success") {
                    // startIdleTimer();
                    $("#ikprs-wrapper").load("<?= site_url('ikprs/data-login') ?>");
                } else {
                    Swal.fire('Login gagal', res.message, 'error');
                }
            }
        });
    });
</script>