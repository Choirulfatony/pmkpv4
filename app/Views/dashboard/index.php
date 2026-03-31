<div class="content-wrapper">
    <?= view('siimut/dashboard_home', [
        'userName' => session('nama_lengkap') ?? session('hris_full_name') ?? 'User',
        'userRole' => session('role') ?? session('user_role') ?? '',
        'department' => session('department_name') ?? '',
        'loginSource' => session('login_source') ?? 'APP',
    ]) ?>
</div>
