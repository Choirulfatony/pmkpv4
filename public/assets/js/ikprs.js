// LOGIN
$(document).on("submit", "#formLoginHris", function (e) {
    e.preventDefault();

    $.post("/ikprs/login-process", $(this).serialize(), function (res) {
        if (res.status === "success") {
            startIdleTimer();
            $("#ikprs-wrapper").load("/ikprs/data-login");
        } else {
            Swal.fire('Login gagal', res.message, 'error');
        }
    }, "json");
});

// LOGOUT
$(document).on("click", "#btnLogout", function () {
    stopIdleTimer();
    $.post("/ikprs/logout-hris", function () {
        location.reload();
    });
});

// LOAD INBOX
$(document).on("ikprs:ready", function () {
    $("#inbox-wrapper").load("/load_module_ikp/inbox_table");
});
