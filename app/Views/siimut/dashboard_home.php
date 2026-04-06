<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h4 class="mb-1">Selamat Datang, <?= esc(session('nama_lengkap') ?? session('hris_full_name') ?? 'User') ?>!</h4>
            <p class="text-muted">Anda login sebagai <strong><?= esc(session('role') ?? session('user_role') ?? '-') ?></strong> dari unit <strong><?= esc(session('department_name') ?? '-') ?></strong></p>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="card border-info mb-3">
                <div class="card-body d-flex align-items-center flex-column flex-sm-row overflow-hidden">
                    <div class="me-3 flex-shrink-0">
                        <i class="bi bi-person-fill text-info" style="font-size: 2.5rem;"></i>
                    </div>
                    <div class="overflow-hidden" style="min-width: 0;">
                        <p class="card-text text-muted mb-0">Nama Lengkap</p>
                        <h5 class="card-title mb-0" style="word-wrap: break-word; overflow-wrap: break-word;" title="<?= esc(session('nama_lengkap') ?? session('hris_full_name') ?? 'User') ?>">
                            <?= esc(session('nama_lengkap') ?? session('hris_full_name') ?? 'User') ?>
                        </h5>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="card border-success mb-3">
                <div class="card-body d-flex align-items-center flex-column flex-sm-row overflow-hidden">
                    <div class="me-3 flex-shrink-0">
                        <i class="bi bi-person-badge-fill text-success" style="font-size: 2.5rem;"></i>
                    </div>
                    <div class="overflow-hidden" style="min-width: 0;">
                        <p class="card-text text-muted mb-0">Role</p>
                        <h5 class="card-title mb-0" style="word-wrap: break-word; overflow-wrap: break-word;" title="<?= esc(session('role') ?? session('user_role') ?? '-') ?>">
                            <?= esc(session('role') ?? session('user_role') ?? '-') ?>
                        </h5>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="card border-warning mb-3">
                <div class="card-body d-flex align-items-center flex-column flex-sm-row overflow-hidden">
                    <div class="me-3 flex-shrink-0">
                        <i class="bi bi-building-fill text-warning" style="font-size: 2.5rem;"></i>
                    </div>
                    <div class="overflow-hidden" style="min-width: 0;">
                        <p class="card-text text-muted mb-0">Unit/Departemen</p>
                        <h5 class="card-title mb-0" style="word-wrap: break-word; overflow-wrap: break-word;" title="<?= esc(session('department_name') ?? '-') ?>">
                            <?= esc(session('department_name') ?? '-') ?>
                        </h5>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="card border-primary mb-3">
                <div class="card-body d-flex align-items-center flex-column flex-sm-row overflow-hidden">
                    <div class="me-3 flex-shrink-0">
                        <i class="bi bi-box-arrow-in-right text-primary" style="font-size: 2.5rem;"></i>
                    </div>
                    <div class="overflow-hidden" style="min-width: 0;">
                        <p class="card-text text-muted mb-0">Sumber Login</p>
                        <h5 class="card-title mb-0" style="word-wrap: break-word; overflow-wrap: break-word;" title="<?= esc(session('login_source') ?? 'APP') ?>">
                            <?= esc(session('login_source') ?? 'APP') ?>
                        </h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
