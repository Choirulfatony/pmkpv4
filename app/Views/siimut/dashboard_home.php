<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h4 class="mb-1">Selamat Datang, <?= esc(session('nama_lengkap') ?? session('hris_full_name') ?? 'User') ?>!</h4>
            <p class="text-muted">
                Anda login sebagai <strong><?= esc(session('role') ?? session('user_role') ?? '-') ?></strong>
                dari unit <strong><?= esc(session('department_name') ?? '-') ?></strong>
                &mdash; Periode: <?= $bulan ?>/<?= $tahun ?>
            </p>
        </div>
    </div>

    <div class="row">
        <?php
        $colors = [
            1 => ['primary', 'bi-flag-fill', 'border-primary'],
            5 => ['success', 'bi-building-fill', 'border-success'],
            6 => ['warning', 'bi-diagram-3-fill', 'border-warning'],
            7 => ['info', 'bi-heart-pulse-fill', 'border-info'],
        ];
        $icons = [
            1 => 'bi-flag-fill',
            5 => 'bi-building-fill',
            6 => 'bi-diagram-3-fill',
            7 => 'bi-heart-pulse-fill',
        ];
        ?>
        <?php foreach ([1, 5, 6, 7] as $type): ?>
            <?php $c = $colors[$type] ?? ['secondary', 'bi-question', 'border-secondary']; ?>
            <?php $s = $summary[$type] ?? ['label' => '-', 'count' => 0]; ?>
            <div class="col-lg-3 col-6 mb-3">
                <div class="card <?= $c[2] ?> h-100">
                    <div class="card-body d-flex align-items-center justify-content-between py-3">
                        <div>
                            <p class="text-muted mb-0 small"><?= $s['label'] ?></p>
                            <h3 class="mb-0 fw-bold"><?= $s['count'] ?></h3>
                            <small class="text-muted">Indikator Aktif</small>
                        </div>
                        <div class="fs-1 text-<?= $c[0] ?> opacity-50">
                            <i class="<?= $c[1] ?>"></i>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <?php
    $targetTercapai = 0;
    $targetTidak = 0;
    $targetNoData = 0;
    foreach ([1, 5, 6, 7] as $type) {
        $ts = $targetStatus[$type] ?? [];
        $targetTercapai += $ts['tercapai'] ?? 0;
        $targetTidak += $ts['tidak_tercapai'] ?? 0;
        $targetNoData += ($ts['tidak_ada_data'] ?? 0) + ($ts['belum_input'] ?? 0);
    }
    ?>

    <div class="row">
    <?php if (in_array(session('user_role'), ['ADMINISTRATOR', 'KENDALI_MUTU'])): ?>
    <?php
    $draftLinks = [1 => 'inm', 5 => 'imprs', 6 => 'impunit', 7 => 'ikp'];
    $draftBgs = [1 => 'text-bg-warning', 5 => 'text-bg-success', 6 => 'text-bg-danger', 7 => 'text-bg-info'];
    $draftIcons = [1 => 'bi-flag-fill', 5 => 'bi-building-fill', 6 => 'bi-diagram-3-fill', 7 => 'bi-heart-pulse-fill'];
    $isAdmin = session('user_role') === 'ADMINISTRATOR';
    ?>
        <div class="col-md-4 col-lg-3 mb-3">
            <div class="row">
                <div class="col-12 mb-2">
                    <h5 class="mb-0"><i class="bi bi-file-earmark-text me-1"></i>Draft Menunggu Approval</h5>
                </div>
                <?php foreach ([1, 5, 6, 7] as $type): ?>
                    <?php $d = $draftCounts[$type] ?? ['label' => '-', 'draft' => 0]; ?>
                    <div class="col-12">
                    <?php if ($isAdmin): ?>
                        <a href="<?= site_url('siimut/approval/' . $draftLinks[$type]) ?>" class="text-decoration-none">
                    <?php endif; ?>
                            <div class="info-box mb-3 <?= $draftBgs[$type] ?>">
                                <span class="info-box-icon"><i class="<?= $draftIcons[$type] ?>"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text"><?= $d['label'] ?></span>
                                    <span class="info-box-number"><?= $d['draft'] ?></span>
                                </div>
                            </div>
                    <?php if ($isAdmin): ?>
                        </a>
                    <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
        <div class="<?= in_array(session('user_role'), ['ADMINISTRATOR', 'KENDALI_MUTU']) ? 'col-md-8 col-lg-9' : 'col-12' ?> mb-3">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="bi bi-graph-up me-1"></i>Progress Pengisian &amp; Pencapaian Target
                    </h5>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-lte-toggle="card-collapse">
                            <i data-lte-icon="expand" class="bi bi-plus-lg"></i>
                            <i data-lte-icon="collapse" class="bi bi-dash-lg"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <p class="text-center mb-3"><strong>Progress Pengisian per Jenis Indikator</strong></p>
                            <?php foreach ([1, 5, 6, 7] as $type): ?>
                                <?php $c = $colors[$type]; ?>
                                <?php $p = $progress[$type] ?? ['label' => '-', 'total' => 0, 'filled' => 0, 'pct' => 0]; ?>
                                <div class="progress-group mb-3">
                                    <?= $p['label'] ?>
                                    <span class="float-end"><b><?= $p['filled'] ?></b>/<?= $p['total'] ?> (<?= $p['pct'] ?>%)</span>
                                    <div class="progress progress-sm">
                                        <div class="progress-bar bg-<?= $c[0] ?>" style="width: <?= $p['pct'] ?>%"></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="col-md-4">
                            <p class="text-center mb-3"><strong>Pencapaian Target Bulan Ini</strong></p>
                            <canvas id="targetChart" style="max-height: 200px;"></canvas>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-sm-3 col-6">
                            <div class="text-center">
                                <span class="text-success"><i class="bi bi-check-circle-fill"></i> Tercapai</span>
                                <h5 class="fw-bold mb-0 text-success"><?= $targetTercapai ?? 0 ?></h5>
                            </div>
                        </div>
                        <div class="col-sm-3 col-6">
                            <div class="text-center">
                                <span class="text-danger"><i class="bi bi-x-circle-fill"></i> Tidak Tercapai</span>
                                <h5 class="fw-bold mb-0 text-danger"><?= $targetTidak ?? 0 ?></h5>
                            </div>
                        </div>
                        <div class="col-sm-3 col-6">
                            <div class="text-center">
                                <span class="text-secondary"><i class="bi bi-question-circle-fill"></i> Belum Input</span>
                                <h5 class="fw-bold mb-0 text-secondary"><?= $targetNoData ?? 0 ?></h5>
                            </div>
                        </div>
                        <div class="col-sm-3 col-6">
                            <div class="text-center">
                                <small class="text-muted">* Nilai = (Total Numerator / Total Denumerator) &times; Faktor</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12 mb-3">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-bar-chart-line me-1"></i>Tren Pengisian per Bulan
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="trendChart" style="max-height: 300px;"></canvas>
                </div>
                <div class="card-footer bg-transparent py-1">
                    <small class="text-muted">* Rata-rata nilai indikator per bulan. Nilai = (Total Numerator / Total Denumerator) &times; Faktor</small>
                </div>
            </div>
        </div>
    </div>

    <?php if (session('user_role') === 'ADMINISTRATOR'): ?>
    <div class="row">
        <div class="col-lg-4 mb-3">
            <div class="card h-100 position-relative">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-building-x me-1"></i>Unit Belum Menginput
                        <span class="badge bg-danger position-absolute top-0 end-0 mt-2 me-2"><?= count($deptWithoutInput) ?></span>
                    </h5>
                </div>
                <div class="card-body p-0" style="max-height: 310px; overflow-y: auto;">
                    <?php if (count($deptWithoutInput) > 0): ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($deptWithoutInput as $d): ?>
                                <div class="list-group-item d-flex justify-content-between align-items-center py-1">
                                    <div>
                                        <i class="bi bi-building text-muted me-2"></i>
                                        <span><?= esc($d->department_name) ?></span>
                                    </div>
                                    <div class="text-nowrap">
                                        <?php foreach ($d->missing_types as $t): ?>
                                            <span class="badge bg-warning text-dark me-1"><?= $t ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center text-success py-4">
                            <i class="bi bi-check-circle-fill fs-1"></i>
                            <p class="mb-0 mt-2">Semua unit sudah menginput</p>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="card-footer bg-transparent py-1">
                    <small class="text-muted">* Tipe indikator (INM/IMPRS/IMPUNIT/IKP) yg belum diisi periode <?= $bulan ?>/<?= $tahun ?></small>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-3">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0 d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-trophy text-success me-1"></i>5 Terbaik (INM)</span>
                        <?php if ($topInm['total_count'] > 5): ?><small class="text-muted fw-normal">dari <?= $topInm['total_count'] ?> total</small><?php endif; ?>
                    </h5>
                </div>
                <div class="card-body p-0" style="max-height: 310px; overflow-y: auto;">
                    <?php if (count($topInm['top']) > 0): ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($topInm['top'] as $i => $item): ?>
                                <div class="list-group-item py-2">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="small text-truncate me-2" style="max-width: 75%;">
                                            <span class="badge bg-success me-1">#<?= $i + 1 ?></span>
                                            <?= esc($item['indicator_element']) ?>
                                            <br><small class="text-muted"><?= esc($item['department_name'] ?? '-') ?></small>
                                        </div>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle fs-6"><?= $item['nilai'] ?>%</span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center text-muted py-4"><i class="bi bi-inbox fs-1"></i><p class="mb-0 mt-2">Belum ada data</p></div>
                    <?php endif; ?>
                </div>
                <div class="card-footer bg-transparent py-1">
                    <small class="text-muted">* Diurutkan dari nilai tertinggi. Nilai = (Total Numerator / Total Denumerator) &times; Faktor</small>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-3">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0 d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-exclamation-triangle text-danger me-1"></i>5 Terendah (INM)</span>
                        <?php if ($topInm['total_count'] > 5): ?><small class="text-muted fw-normal">dari <?= $topInm['total_count'] ?> total</small><?php endif; ?>
                    </h5>
                </div>
                <div class="card-body p-0" style="max-height: 310px; overflow-y: auto;">
                    <?php if (count($topInm['bottom']) > 0): ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($topInm['bottom'] as $i => $item): ?>
                                <div class="list-group-item py-2">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="small text-truncate me-2" style="max-width: 75%;">
                                            <span class="badge bg-danger me-1">#<?= $i + 1 ?></span>
                                            <?= esc($item['indicator_element']) ?>
                                            <br><small class="text-muted"><?= esc($item['department_name'] ?? '-') ?></small>
                                        </div>
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle fs-6"><?= $item['nilai'] ?>%</span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center text-muted py-4"><i class="bi bi-inbox fs-1"></i><p class="mb-0 mt-2">Belum ada data</p></div>
                    <?php endif; ?>
                </div>
                <div class="card-footer bg-transparent py-1">
                    <small class="text-muted">* Diurutkan dari nilai terendah. Nilai = (Total Numerator / Total Denumerator) &times; Faktor</small>
                </div>
            </div>
        </div>
    </div>
    <?php elseif (in_array(session('user_role'), ['KENDALI_MUTU', 'VALIDATOR', 'KOMITE'])): ?>
    <div class="row">
        <div class="col-lg-6 mb-3">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0 d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-trophy text-success me-1"></i>5 Terbaik (INM)</span>
                        <?php if ($topInm['total_count'] > 5): ?><small class="text-muted fw-normal">dari <?= $topInm['total_count'] ?> total</small><?php endif; ?>
                    </h5>
                </div>
                <div class="card-body p-0" style="max-height: 310px; overflow-y: auto;">
                    <?php if (count($topInm['top']) > 0): ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($topInm['top'] as $i => $item): ?>
                                <div class="list-group-item py-2">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="small text-truncate me-2" style="max-width: 75%;">
                                            <span class="badge bg-success me-1">#<?= $i + 1 ?></span>
                                            <?= esc($item['indicator_element']) ?>
                                            <br><small class="text-muted"><?= esc($item['department_name'] ?? '-') ?></small>
                                        </div>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle fs-6"><?= $item['nilai'] ?>%</span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center text-muted py-4"><i class="bi bi-inbox fs-1"></i><p class="mb-0 mt-2">Belum ada data</p></div>
                    <?php endif; ?>
                </div>
                <div class="card-footer bg-transparent py-1">
                    <small class="text-muted">* Diurutkan dari nilai tertinggi. Nilai = (Total Numerator / Total Denumerator) &times; Faktor</small>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-3">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0 d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-exclamation-triangle text-danger me-1"></i>5 Terendah (INM)</span>
                        <?php if ($topInm['total_count'] > 5): ?><small class="text-muted fw-normal">dari <?= $topInm['total_count'] ?> total</small><?php endif; ?>
                    </h5>
                </div>
                <div class="card-body p-0" style="max-height: 310px; overflow-y: auto;">
                    <?php if (count($topInm['bottom']) > 0): ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($topInm['bottom'] as $i => $item): ?>
                                <div class="list-group-item py-2">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="small text-truncate me-2" style="max-width: 75%;">
                                            <span class="badge bg-danger me-1">#<?= $i + 1 ?></span>
                                            <?= esc($item['indicator_element']) ?>
                                            <br><small class="text-muted"><?= esc($item['department_name'] ?? '-') ?></small>
                                        </div>
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle fs-6"><?= $item['nilai'] ?>%</span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center text-muted py-4"><i class="bi bi-inbox fs-1"></i><p class="mb-0 mt-2">Belum ada data</p></div>
                    <?php endif; ?>
                </div>
                <div class="card-footer bg-transparent py-1">
                    <small class="text-muted">* Diurutkan dari nilai terendah. Nilai = (Total Numerator / Total Denumerator) &times; Faktor</small>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php if (in_array(session('user_role'), ['ADMINISTRATOR', 'KENDALI_MUTU', 'VALIDATOR', 'KOMITE'])): ?>
    <div class="row">
        <div class="col-lg-6 mb-3">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0 d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-trophy text-success me-1"></i>5 Terbaik (IMPRS)</span>
                        <?php if ($topImprs['total_count'] > 5): ?><small class="text-muted fw-normal">dari <?= $topImprs['total_count'] ?> total</small><?php endif; ?>
                    </h5>
                </div>
                <div class="card-body p-0" style="max-height: 310px; overflow-y: auto;">
                    <?php if (count($topImprs['top']) > 0): ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($topImprs['top'] as $i => $item): ?>
                                <div class="list-group-item py-2">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="small text-truncate me-2" style="max-width: 75%;">
                                            <span class="badge bg-success me-1">#<?= $i + 1 ?></span>
                                            <?= esc($item['indicator_element']) ?>
                                            <br><small class="text-muted"><?= esc($item['department_name'] ?? '-') ?></small>
                                        </div>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle fs-6"><?= $item['nilai'] ?>%</span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center text-muted py-4"><i class="bi bi-inbox fs-1"></i><p class="mb-0 mt-2">Belum ada data</p></div>
                    <?php endif; ?>
                </div>
                <div class="card-footer bg-transparent py-1">
                    <small class="text-muted">* Diurutkan dari nilai tertinggi. Nilai = (Total Numerator / Total Denumerator) &times; Faktor</small>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-3">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0 d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-exclamation-triangle text-danger me-1"></i>5 Terendah (IMPRS)</span>
                        <?php if ($topImprs['total_count'] > 5): ?><small class="text-muted fw-normal">dari <?= $topImprs['total_count'] ?> total</small><?php endif; ?>
                    </h5>
                </div>
                <div class="card-body p-0" style="max-height: 310px; overflow-y: auto;">
                    <?php if (count($topImprs['bottom']) > 0): ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($topImprs['bottom'] as $i => $item): ?>
                                <div class="list-group-item py-2">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="small text-truncate me-2" style="max-width: 75%;">
                                            <span class="badge bg-danger me-1">#<?= $i + 1 ?></span>
                                            <?= esc($item['indicator_element']) ?>
                                            <br><small class="text-muted"><?= esc($item['department_name'] ?? '-') ?></small>
                                        </div>
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle fs-6"><?= $item['nilai'] ?>%</span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center text-muted py-4"><i class="bi bi-inbox fs-1"></i><p class="mb-0 mt-2">Belum ada data</p></div>
                    <?php endif; ?>
                </div>
                <div class="card-footer bg-transparent py-1">
                    <small class="text-muted">* Diurutkan dari nilai terendah. Nilai = (Total Numerator / Total Denumerator) &times; Faktor</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6 mb-3">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0 d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-trophy text-success me-1"></i>5 Terbaik (IMPUNIT)</span>
                        <?php if ($topImpunit['total_count'] > 5): ?><small class="text-muted fw-normal">dari <?= $topImpunit['total_count'] ?> total</small><?php endif; ?>
                    </h5>
                </div>
                <div class="card-body p-0" style="max-height: 310px; overflow-y: auto;">
                    <?php if (count($topImpunit['top']) > 0): ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($topImpunit['top'] as $i => $item): ?>
                                <div class="list-group-item py-2">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="small text-truncate me-2" style="max-width: 75%;">
                                            <span class="badge bg-success me-1">#<?= $i + 1 ?></span>
                                            <?= esc($item['indicator_element']) ?>
                                            <br><small class="text-muted"><?= esc($item['department_name'] ?? '-') ?></small>
                                        </div>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle fs-6"><?= $item['nilai'] ?>%</span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center text-muted py-4"><i class="bi bi-inbox fs-1"></i><p class="mb-0 mt-2">Belum ada data</p></div>
                    <?php endif; ?>
                </div>
                <div class="card-footer bg-transparent py-1">
                    <small class="text-muted">* Diurutkan dari nilai tertinggi. Nilai = (Total Numerator / Total Denumerator) &times; Faktor</small>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-3">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0 d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-exclamation-triangle text-danger me-1"></i>5 Terendah (IMPUNIT)</span>
                        <?php if ($topImpunit['total_count'] > 5): ?><small class="text-muted fw-normal">dari <?= $topImpunit['total_count'] ?> total</small><?php endif; ?>
                    </h5>
                </div>
                <div class="card-body p-0" style="max-height: 310px; overflow-y: auto;">
                    <?php if (count($topImpunit['bottom']) > 0): ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($topImpunit['bottom'] as $i => $item): ?>
                                <div class="list-group-item py-2">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="small text-truncate me-2" style="max-width: 75%;">
                                            <span class="badge bg-danger me-1">#<?= $i + 1 ?></span>
                                            <?= esc($item['indicator_element']) ?>
                                            <br><small class="text-muted"><?= esc($item['department_name'] ?? '-') ?></small>
                                        </div>
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle fs-6"><?= $item['nilai'] ?>%</span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center text-muted py-4"><i class="bi bi-inbox fs-1"></i><p class="mb-0 mt-2">Belum ada data</p></div>
                    <?php endif; ?>
                </div>
                <div class="card-footer bg-transparent py-1">
                    <small class="text-muted">* Diurutkan dari nilai terendah. Nilai = (Total Numerator / Total Denumerator) &times; Faktor</small>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php
$monthNames = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

$trendLabels = json_encode(array_slice($monthNames, 1));
$defaultMonthly = array_fill(1, 12, ['avg' => 0]);
$trendInm = json_encode(array_values(array_map(fn($m) => $m['avg'], ($trend[1]['monthly'] ?? $defaultMonthly))));
$trendImprs = json_encode(array_values(array_map(fn($m) => $m['avg'], ($trend[5]['monthly'] ?? $defaultMonthly))));
$trendImpunit = json_encode(array_values(array_map(fn($m) => $m['avg'], ($trend[6]['monthly'] ?? $defaultMonthly))));
$trendIkp = json_encode(array_values(array_map(fn($m) => $m['avg'], ($trend[7]['monthly'] ?? $defaultMonthly))));

?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const baseColor = (alpha = 1) => {
        const isDark = document.documentElement.getAttribute('data-bs-theme') === 'dark';
        return isDark ? `rgba(255,255,255,${alpha})` : `rgba(33,37,41,${alpha})`;
    };

    new Chart(document.getElementById('targetChart'), {
        type: 'doughnut',
        data: {
            labels: ['Tercapai', 'Tidak Tercapai', 'Belum Input / No Data'],
            datasets: [{
                data: [<?= $targetTercapai ?>, <?= $targetTidak ?>, <?= $targetNoData ?>],
                backgroundColor: [
                    'rgba(40, 167, 69, 0.85)',
                    'rgba(220, 53, 69, 0.85)',
                    'rgba(108, 117, 125, 0.5)',
                ],
                borderColor: baseColor(0.1),
                borderWidth: 2,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        boxWidth: 12,
                        padding: 8,
                        font: { size: 11 },
                    },
                },
            },
            cutout: '65%',
        },
    });

    new Chart(document.getElementById('trendChart'), {
        type: 'bar',
        data: {
            labels: <?= $trendLabels ?>,
            datasets: [
                {
                    label: 'INM',
                    data: <?= $trendInm ?>,
                    backgroundColor: 'rgba(13, 110, 253, 0.7)',
                    borderColor: 'rgba(13, 110, 253, 1)',
                    borderWidth: 1,
                },
                {
                    label: 'IMPRS',
                    data: <?= $trendImprs ?>,
                    backgroundColor: 'rgba(25, 135, 84, 0.7)',
                    borderColor: 'rgba(25, 135, 84, 1)',
                    borderWidth: 1,
                },
                {
                    label: 'IMPUNIT',
                    data: <?= $trendImpunit ?>,
                    backgroundColor: 'rgba(255, 193, 7, 0.7)',
                    borderColor: 'rgba(255, 193, 7, 1)',
                    borderWidth: 1,
                },
                {
                    label: 'IKP',
                    data: <?= $trendIkp ?>,
                    backgroundColor: 'rgba(13, 202, 240, 0.7)',
                    borderColor: 'rgba(13, 202, 240, 1)',
                    borderWidth: 1,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: {
                x: { grid: { display: false } },
                y: {
                    beginAtZero: true,
                    grid: { color: baseColor(0.08) },
                },
            },
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        boxWidth: 12,
                        font: { size: 11 },
                    },
                },
            },
        },
    });
});
</script>

<?php if (!empty($showGmailModal)): ?>
<style>
.input-group:has(.is-invalid) .btn-outline-secondary {
    border-color: #dc3545;
}
</style>
<div class="modal fade" id="gmailModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white border-0">
                <h5 class="modal-title"><i class="bi bi-google me-2"></i>Hubungkan Akun Google</h5>
            </div>
            <div class="modal-body text-center pt-2 pb-4">
                <h6 class="text-start">⚠️ Lengkapi Data Akun Anda</h6>
                <p class="text-muted small mb-3 text-start">
                    Selamat datang di SIIMUT (Sistem Informasi Indikator Mutu).<br><br>
                    Untuk meningkatkan keamanan akun, setiap pengguna wajib melengkapi data akun dengan:
                </p>
                <ul class="text-muted small text-start mb-3">
                    <li>Menghubungkan Email Google (Gmail) yang masih aktif.</li>
                    <li>Mengisi Nomor Telepon/WhatsApp yang masih aktif.</li>
                </ul>
                <p class="text-muted small mb-3 text-start">
                    Sinkronisasi ini hanya dilakukan satu kali dan bertujuan untuk:
                </p>
                <ul class="text-muted small text-start mb-3">
                    <li>Meningkatkan keamanan akun.</li>
                    <li>Mempermudah proses pemulihan akun apabila mengalami kendala login.</li>
                    <li>Mendukung pengembangan fitur dan layanan SIIMUT di masa mendatang.</li>
                </ul>
                <p class="text-muted small mb-3 text-start">
                    Silakan klik "Lengkapi Sekarang" untuk melanjutkan proses sinkronisasi akun.
                </p>
                <form method="post" action="<?= site_url('auth/pre-sync') ?>" autocomplete="off" novalidate>
                    <?= csrf_field() ?>
                    <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger py-2 small mb-3" role="alert">
                        <i class="bi bi-exclamation-triangle me-1"></i><?= session()->getFlashdata('error') ?>
                    </div>
                    <?php endif; ?>
                    <div class="text-start mb-3">
                        <label class="form-label small fw-semibold">Nomor Telepon/WhatsApp <span class="text-danger">*</span></label>
                        <input type="tel" name="profile_handphone1" class="form-control form-control-sm" placeholder="08xxxxxxxxxx" required oninput="validatePhone(this)">
                        <div id="phone-warning" class="text-danger small mt-1" style="display:none;">Nomor telepon tidak valid. Masukkan nomor aktif (contoh: 081234567890)</div>
                    </div>
                    <div class="text-start mb-3">
                        <label class="form-label small fw-semibold">Password Baru <span class="text-danger">*</span></label>
                        <div class="input-group input-group-sm">
                            <input type="password" name="new_password" class="form-control" placeholder="Min. 8 karakter, kombinasi huruf besar/kecil, angka, simbol" minlength="8" required aria-label="Min. 8 karakter, kombinasi huruf besar/kecil, angka, simbol" oninput="validatePassword(this)">
                            <button type="button" class="btn btn-outline-secondary" onclick="togglePassword(this, 'new_password')" tabindex="-1">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        <div id="password-warning" class="text-danger small mt-1" style="display:none;">Password minimal 8 karakter, kombinasi huruf besar/kecil, angka, dan simbol</div>
                    </div>
                    <div class="text-start mb-3">
                        <label class="form-label small fw-semibold">Konfirmasi Password Baru <span class="text-danger">*</span></label>
                        <div class="input-group input-group-sm">
                            <input type="password" name="confirm_password" class="form-control" placeholder="Ulangi password baru" required aria-label="Ulangi password baru" oninput="validateConfirmPassword(this)">
                            <button type="button" class="btn btn-outline-secondary" onclick="togglePassword(this, 'confirm_password')" tabindex="-1">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        <div id="confirm-password-warning" class="text-danger small mt-1" style="display:none;">Password tidak sama dengan password baru</div>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-google me-2"></i>Lengkapi Sekarang
                        </button>
                        <a href="<?= site_url('auth/logout') ?>" class="btn btn-outline-secondary btn-sm">Nanti Saja</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
function togglePassword(btn, name) {
    var input = btn.closest('.input-group').querySelector('input[name="' + name + '"]');
    if (input.type === 'password') {
        input.type = 'text';
        btn.querySelector('i').className = 'bi bi-eye-slash';
    } else {
        input.type = 'password';
        btn.querySelector('i').className = 'bi bi-eye';
    }
}
function validatePassword(input) {
    var warning = document.getElementById('password-warning');
    if (input.value.length > 0 && !/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z\d]).{8,}$/.test(input.value)) {
        warning.style.display = 'block';
        return false;
    } else {
        warning.style.display = 'none';
        return true;
    }
}
function validateConfirmPassword(input) {
    var warning = document.getElementById('confirm-password-warning');
    var pw = document.querySelector('input[name="new_password"]');
    if (input.value.length > 0 && input.value !== pw.value) {
        warning.style.display = 'block';
        return false;
    } else {
        warning.style.display = 'none';
        return true;
    }
}
function validatePhone(input) {
    var warning = document.getElementById('phone-warning');
    var valid = /^08\d{7,12}$/.test(input.value);
    if (input.value.length > 0 && !valid) {
        warning.style.display = 'block';
        return false;
    } else {
        warning.style.display = 'none';
        return true;
    }
}
document.addEventListener('DOMContentLoaded', function() {
    var el = document.getElementById('gmailModal');
    if (el) {
        var modal = new bootstrap.Modal(el);
        modal.show();
        el.addEventListener('shown.bs.modal', function() {
            var form = el.querySelector('form');
            if (form) form.reset();
        });
        var form = el.querySelector('form');
        if (form) {
            form.addEventListener('submit', function(e) {
                var pw = form.querySelector('input[name="new_password"]');
                var cpw = form.querySelector('input[name="confirm_password"]');
                var phone = form.querySelector('input[name="profile_handphone1"]');
                var valid = true;
                if (!phone.value.trim() || !validatePhone(phone)) { phone.classList.add('is-invalid'); validatePhone(phone); valid = false; }
                if (!pw.value.trim() || !validatePassword(pw)) { pw.classList.add('is-invalid'); valid = false; }
                if (!cpw.value.trim() || pw.value !== cpw.value) { cpw.classList.add('is-invalid'); validateConfirmPassword(cpw); valid = false; }
                if (!valid) e.preventDefault();
            });
            form.querySelectorAll('input').forEach(function(inp) {
                inp.addEventListener('input', function() { this.classList.remove('is-invalid'); });
            });
        }
    }
});
</script>
<?php endif; ?>
