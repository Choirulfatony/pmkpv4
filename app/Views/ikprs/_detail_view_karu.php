<style>
    /* ===============================
    GMAIL STYLE - CLEAN UI
    ================================ */


    /* WRAPPER OPTIONAL */
    .insiden-wrapper {
        max-width: 1200px;
        margin: auto;
        padding: 20px;
    }

    /* HEADER */
    .insiden-header {
        padding: 18px 20px;
        border-radius: 14px;
        background: var(--bs-body-bg);
        border: 1px solid var(--bs-border-color);
        margin-bottom: 16px;
        border-left: 6px solid var(--bs-primary);
        background: linear-gradient(to right, rgba(13, 110, 253, 0.08), transparent);
    }

    /* FLEX */
    .insiden-container {
        display: flex;
        justify-content: space-between;
        gap: 12px;
    }

    .insiden-left {
        flex: 1;
    }

    .insiden-right {
        text-align: right;
    }

    /* TEXT */
    .insiden-title {
        font-size: 18px;
        font-weight: 600;
        color: var(--bs-body-color);
    }

    .insiden-desc,
    .insiden-meta,
    .insiden-label {
        color: var(--bs-secondary-color);
    }

    .insiden-meta {
        font-size: 12px;
        margin-top: 4px;
    }

    .insiden-value {
        font-size: 14px;
        font-weight: 500;
        color: var(--bs-body-color);
    }

    /* SECTION (NO BORDER, ONLY SPACING) */
    .insiden-section {
        padding: 12px 4px;
        border: none !important;
        border-bottom: 1px solid var(--bs-border-color);
        padding-bottom: 16px;
        margin-bottom: 16px;
    }

    /* TITLE */
    .insiden-section-title {
        font-size: 13px;
        font-weight: 600;
        color: var(--bs-secondary-color);
        margin-bottom: 8px;
    }

    /* BOX */
    .insiden-box {
        background: var(--bs-body-bg);
        border: 1px solid var(--bs-border-color);
        border-radius: 16px;
        padding: 16px;
        line-height: 1.8;
        margin-top: 6px;
        margin-bottom: 16px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        transition: all 0.2s ease;
    }

    .insiden-box:hover {
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
    }

    /* AKSEN */
    .insiden-tempat {
        /* border-left: 4px solid #0d6efd; */

        border-left: 4px solid #0d6efd;
        background: rgba(92, 155, 238, 0.05);
        padding: 20px;
        font-size: 15px;
        line-height: 1.9;
    }

    /* TEXT BIAR ENAK DIBACA */
    .insiden-tempat {
        font-size: 14px;
        white-space: pre-line;
    }

    /* DARK MODE */
    .dark-mode .insiden-tempat {
        background: #1e1e1e;
        border-color: #444;
    }

    /* .insiden-kronologi {
        border-left: 4px solid #6f42c1;
    } */
    .insiden-kronologi {
        border-left: 6px solid #6f42c1;
        background: rgba(111, 66, 193, 0.05);
        padding: 20px;
        font-size: 15px;
        line-height: 1.9;
    }

    /* TEXT BIAR ENAK DIBACA */
    .insiden-kronologi {
        font-size: 14px;
        white-space: pre-line;
    }

    /* DARK MODE */
    .dark-mode .insiden-kronologi {
        background: #1e1e1e;
        border-color: #444;
    }

    /* TOOLBAR (HILANGKAN GARIS) */
    .mailbox-header {
        border: none !important;
        background: transparent !important;
        padding: 6px 0;
        margin-bottom: 10px;
    }

    /* BUTTON */
    .btn-mailbox {
        background: transparent;
        border: 1px solid var(--bs-border-color);
        color: var(--bs-body-color);
        border-radius: 8px;
        padding: 6px 10px;
        transition: all 0.2s ease;
    }

    .btn-mailbox:hover {
        background: var(--bs-secondary-bg);
        transform: translateY(-1px);
    }

    /* DARK MODE */
    .dark-mode .insiden-box {
        background: #1e1e1e;
    }

    .dark-mode .btn-mailbox {
        border-color: #444;
        color: #ddd;
    }

    .dark-mode .btn-mailbox:hover {
        background: #2b2b2b;
    }

    .dark-mode .insiden-section-title {
        color: #adb5bd;
    }

    /* RESPONSIVE */
    @media (max-width: 768px) {
        .insiden-container {
            flex-direction: column;
        }

        .insiden-right {
            text-align: left;
            margin-top: 10px;
        }
    }


    /* DARK MODE */
    .dark-mode .timeline-dot {
        border-color: #1e1e1e;
    }

    .dark-mode .timeline-content {
        background: #1e1e1e;
    }

    .timeline-item:nth-child(2) .timeline-dot {
        background: #198754;
    }

    .timeline-item:nth-child(3) .timeline-dot {
        background: #ffc107;
    }

    .timeline-item:nth-child(4) .timeline-dot {
        background: #dc3545;
    }
</style>

<div class="insiden-wrapper">


    <div class="card-header mailbox-header d-flex align-items-center">

        <!-- LEFT TOOLBAR -->
        <div class="d-flex align-items-center gap-2">

            <!-- BACK -->
            <button class="btn btn-mailbox btn-sm btn-back" data-tipe="<?= esc($tipe) ?>">
                <i class="bi bi-arrow-left"></i>
            </button>

            <!-- RELOAD -->
            <button class="btn btn-mailbox btn-sm btn-detail-reload"
                data-id="<?= $insiden['id'] ?>" data-tipe="<?= esc($tipe ?? 'inbox') ?>">
                <i class="bi bi-arrow-repeat"></i>
            </button>
        </div>

        <!-- PUSH RIGHT -->
        <div class="ms-auto d-flex align-items-center gap-3">

            <!-- INFO -->
            <!-- <small class="text-muted">

        </small> -->

            <!-- PAGINATION -->
            <div class="btn-group btn-group-sm">
                <!-- PREV -->
                <button class="btn btn-mailbox btn-insiden-prev"
                    data-id="<?= $prev_id ?? '' ?>"
                    <?= empty($prev_id) ? 'disabled' : '' ?>>
                    <i class="bi bi-chevron-left"></i>
                </button>
                <!-- NEXT -->
                <button class="btn btn-mailbox btn-insiden-next"
                    data-id="<?= $next_id ?? '' ?>"
                    <?= empty($next_id) ? 'disabled' : '' ?>>
                    <i class="bi bi-chevron-right"></i>
                </button>
            </div>

        </div>

    </div>

    <!-- HEADER -->
    <?php

    $jenisInsidenText = [
        'KNC' => 'Kejadian Nyaris Cedera (Near Miss)',
        'KTD' => 'Kejadian Tidak Diharapkan (Adverse Event)',
        'KTC' => 'Kejadian Tidak Cedera',
        'KPC' => 'Kejadian Potensi Cedera',
        'Sentinel' => 'Kejadian Sentinel'
    ];

    $jenis_insiden = $insiden['jenis_insiden'] ?? '-';
    $jenis_insiden_full = $jenisInsidenText[$jenis_insiden] ?? $jenis_insiden;

    $grading = $insiden['grading_risiko'] ?? null;
    $status  = $insiden['status_laporan'] ?? 'DRAFT';

    $gradingBg = [
        'BIRU'   => 'rgba(13,110,253,0.15)',
        'HIJAU'  => 'rgba(25,135,84,0.15)',
        'KUNING' => 'rgba(255,193,7,0.15)',
        'MERAH'  => 'rgba(220,53,69,0.15)'
    ];

    $gradingBorder = [
        'BIRU'   => '#0d6efd',
        'HIJAU'  => '#198754',
        'KUNING' => '#ffc107',
        'MERAH'  => '#dc3545'
    ];

    $gradingIcon = [
        'BIRU'   => 'bi-shield',
        'HIJAU'  => 'bi-shield-check',
        'KUNING' => 'bi-exclamation-triangle',
        'MERAH'  => 'bi-exclamation-octagon'
    ];

    $headerBg   = $gradingBg[$grading] ?? '#f8f9fa';
    $borderLeft = $gradingBorder[$grading] ?? '#dee2e6';
    $icon       = $gradingIcon[$grading] ?? 'bi-info-circle';

    $statusColor = [
        'DRAFT'     => 'secondary',
        'KARU'      => 'info',
        'INSTALASI' => 'primary',
        'PROSES'    => 'warning',
        'SELESAI'   => 'success'
    ];

    $statusText = [
        'DRAFT'     => 'Menunggu verifikasi KARU',
        'KARU'      => 'Telah diverifikasi KARU',
        'INSTALASI' => 'Sedang dianalisa PMKP',
        'PROSES'    => 'Sedang diproses',
        'SELESAI'   => 'Laporan selesai'
    ];

    $badge = $statusColor[$status] ?? 'secondary';
    $statusLabel = $statusText[$status] ?? $status;

    $roleBadge = [
        'PELAPOR' => '<span class="badge bg-info"><i class="bi bi-person me-1"></i>Pelapor</span>',
        'KARU'    => '<span class="badge bg-warning text-dark"><i class="bi bi-person-badge me-1"></i>Kepala Ruangan</span>',
        'KOMITE'  => '<span class="badge bg-success"><i class="bi bi-shield-check me-1"></i>Komite PMKP</span>'
    ];

    $currentRoleBadge = $roleBadge[$user_role] ?? '';

    ?>

    <div class="insiden-header"
        style="border-left:6px solid <?= $borderLeft ?>">

        <div class="insiden-container">

            <!-- KIRI -->
            <div class="insiden-left">

                <div class="insiden-title">
                    <?= esc($jenis_insiden) ?>
                </div>

                <div class="insiden-desc">
                    <?= esc($jenis_insiden_full) ?>
                </div>
                <div class="insiden-meta">
                    <div>
                        <?= date('d M Y H:i', strtotime($insiden['created_at'])) ?>
                        <br>
                        Pelapor :
                        <strong><?= esc($insiden['nama_petugas']) ?></strong>
                    </div>
                </div>
            </div>

            <!-- KANAN -->
            <div class="insiden-right">

                <div class="insiden-badge mb-2">
                    <?= $currentRoleBadge ?>
                </div>

                <div class="insiden-badge">

                    <!-- STATUS -->
                    <span class="badge bg-<?= $badge ?>">
                        <?= esc($statusLabel) ?>
                    </span>
                    <br>
                    <!-- GRADING -->
                    <?php if ($grading): ?>
                        <span class="badge border bg-body text-body mt-1 d-block">
                            <i class="bi <?= $icon ?>"></i>
                            Risiko <?= esc($grading) ?>
                        </span>
                    <?php endif; ?>


                </div>



            </div>

        </div>

    </div>

    <!-- DATA PASIEN -->
    <div class="insiden-section">

        <div class="insiden-section-title">
            Data Pasien
        </div>

        <div class="row">

            <div class="col-md-6">
                <div class="insiden-label">Nama Pasien</div>
                <div class="insiden-value"><?= esc($insiden['nama_pasien']) ?></div>
            </div>

            <div class="col-md-6">
                <div class="insiden-label">No RM</div>
                <div class="insiden-value"><?= esc($insiden['kd_pasien']) ?></div>
            </div>

            <?php

            $umur = (int) ($insiden['umur_tahun'] ?? 0);

            if ($umur < 1) {
                $kelompok = '0-1 bulan';
            } elseif ($umur <= 1) {
                $kelompok = '> 1 bulan – 1 tahun';
            } elseif ($umur <= 5) {
                $kelompok = '> 1 tahun – 5 tahun';
            } elseif ($umur <= 15) {
                $kelompok = '> 5 tahun – 15 tahun';
            } elseif ($umur <= 30) {
                $kelompok = '> 15 tahun – 30 tahun';
            } elseif ($umur <= 65) {
                $kelompok = '> 30 tahun – 65 tahun';
            } else {
                $kelompok = '> 65 tahun';
            }

            ?>
            <div class="col-md-6 mt-2">
                <div class="insiden-label">Kelompok Umur</div>
                <div class="insiden-value"><?= esc($kelompok) ?></div>
            </div>

            <div class="col-md-6 mt-2">
                <div class="insiden-label">Jenis Kelamin</div>
                <div class="insiden-value"><?= esc($insiden['kelamin']) ?></div>
            </div>

            <div class="col-md-6 mt-2">
                <div class="insiden-label">Penjamin</div>
                <div class="insiden-value"><?= esc($insiden['penjamin']) ?></div>
            </div>

            <div class="col-md-6 mt-2">
                <div class="insiden-label">Unit / Ruangan</div>
                <div class="insiden-value"><?= esc($insiden['nama_kamar']) ?></div>

            </div>

        </div>
    </div>

    <!-- Data Kejadian --->
    <div class="insiden-section">
        <div class="insiden-section-title">
            Data Kejadian
        </div>

        <div class="row">

            <!-- Tanggal Insiden -->
            <div class="col-md-6">
                <div class="insiden-label">Tanggal Insiden</div>
                <div class="insiden-value">
                    <?= esc($insiden['tgl_insiden']) ?>
                </div>
            </div>

            <!-- Waktu Insiden -->
            <div class="col-md-6">
                <div class="insiden-label">Waktu Insiden</div>
                <div class="insiden-value">
                    <?= esc($insiden['jam_insiden']) ?>
                </div>
            </div>

        </div>


    </div>

    <!-- Tempat Insiden --->
    <div class="insiden-section">

        <div class="insiden-section-title">
            Tempat Insiden
        </div>

        <div class="insiden-box insiden-tempat">
            <?= nl2br(esc($insiden['insiden'])) ?>
        </div>

        <div class="insiden-section-title">
            Kronologi Kejadian
        </div>

        <div class="insiden-box insiden-kronologi text-justify">
            <?= nl2br(esc($insiden['kronologis_insiden'])) ?>
        </div>

    </div>


    <?php

    $pelapor = $insiden['pelapor_insiden'] ?? '-';

    if ($pelapor == 'Lain-lain' && !empty($insiden['pelapor_lain_text'])) {
        $pelapor = $insiden['pelapor_lain_text'];
    }

    $insiden_pada = $insiden['insiden_pada'] ?? '-';

    $spesialisasi = $insiden['spesialisasi_pasien'] ?? '-';

    if ($spesialisasi == 'Lain-lain' && !empty($insiden['spesialisasi_lain'])) {
        $spesialisasi = $insiden['spesialisasi_lain'];
    }

    ?>


    <?php

    $pelapor = $insiden['pelapor_insiden'] ?? '-';

    if ($pelapor == 'Lain-lain' && !empty($insiden['pelapor_lain_text'])) {
        $pelapor = $insiden['pelapor_lain_text'];
    }

    $insiden_pada = $insiden['insiden_pada'] ?? '-';

    $spesialisasi = $insiden['spesialisasi_pasien'] ?? '-';

    if ($spesialisasi == 'Lain-lain' && !empty($insiden['spesialisasi_lain'])) {
        $spesialisasi = $insiden['spesialisasi_lain'];
    }

    ?>

    <!-- Pelapor Insiden -->
    <div class="insiden-section">

        <div class="row mt-3">

            <!-- Pelapor Insiden -->
            <div class="col-md-6">
                <div class="insiden-label">Pelapor Insiden</div>
                <div class="insiden-value">
                    <?= esc($pelapor) ?>
                </div>
            </div>

            <!-- Insiden Terjadi Pada -->
            <div class="col-md-6">
                <div class="insiden-label">Insiden Terjadi Pada</div>
                <div class="insiden-value">
                    <?= esc($insiden_pada) ?>
                </div>
            </div>

        </div>

        <div class="row mt-2">

            <!-- Spesialisasi Pasien -->
            <div class="col-md-6">
                <div class="insiden-label">Spesialisasi Pasien</div>
                <div class="insiden-value">
                    <?= esc($spesialisasi) ?>
                </div>
            </div>

            <div class="col-md-6">
                <div class="insiden-label">Akibat Insiden</div>
                <div class="insiden-value">
                    <?= esc($insiden['akibat_insiden']) ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Tindakan segera -->
    <div class="insiden-section">

        <div class="insiden-section-title">
            Tindakan Segera Setelah Kejadian
        </div>

        <div class="row">

            <!-- Tindakan segera -->
            <div class="col-md-12 mb-3">
                <div class="insiden-label">Tindakan segera & hasilnya</div>
                <div class="insiden-value">
                    <?= nl2br(esc($insiden['tindakan_segera'])) ?>
                </div>
            </div>

            <!-- Dilakukan oleh -->
            <div class="col-md-6">
                <div class="insiden-label">Tindakan dilakukan oleh</div>
                <div class="insiden-value">
                    <?= esc($insiden['tindakan_oleh']) ?>
                </div>
            </div>

            <!-- Tim -->
            <div class="col-md-6">
                <div class="insiden-label">Tindakan dilakukan oleh tim</div>
                <div class="insiden-value">
                    <?= esc($insiden['tindakan_tim']) ?>
                </div>
            </div>

            <!-- Petugas lain -->
            <div class="col-md-6 mt-2">
                <div class="insiden-label">Petugas lainnya</div>
                <div class="insiden-value">
                    <?= esc($insiden['tindakan_petugas_lain']) ?>
                </div>
            </div>

            <!-- Pernah terjadi -->
            <div class="col-md-6 mt-2">

                <div class="insiden-label">
                    Kejadian serupa pernah terjadi
                </div>

                <div class="insiden-value">
                    <?= esc($insiden['pernah_terjadi']) ?>
                </div>

                <?php if (!empty($insiden['tindakan_lanjutan'])): ?>

                    <div class="insiden-label mt-2">
                        Tindakan Lanjutan
                    </div>

                    <div class="insiden-value">
                        <?= nl2br(esc($insiden['tindakan_lanjutan'])) ?>
                    </div>

                <?php endif; ?>

            </div>

            <!--Hasil Verifikasi KARU-->
            <?php if (
                $insiden['status_laporan'] != 'DRAFT' &&
                (!empty($insiden['grading_risiko']) || !empty($insiden['catatan_atasan']))
            ): ?>
                <div class="insiden-section">

                    <div class="insiden-section-title">
                        <i class="bi bi-check2-square me-1"></i>Hasil Verifikasi KARU
                    </div>

                    <div class="insiden-box border-start border-4 border-info">

                        <div class="row">

                            <div class="col-md-12 mb-2">
                                <div class="insiden-label">Diverifikasi oleh :</div>
                                <div class="insiden-value">
                                    <i class="bi bi-person-badge me-1"></i>
                                    <strong><?= isset($karu_user->full_name) ? esc($karu_user->full_name) : 'KARU' ?></strong>
                                    <!-- <small class="text-muted">(<?= isset($karu_user->nip) ? esc($karu_user->nip) : '-' ?>)</small> -->
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="insiden-label">Grading Risiko :</div>
                                <div class="insiden-value">
                                    <?php 
                                    $gr = strtolower($insiden['grading_risiko'] ?? '');
                                    $grClass = $gr == 'merah' ? 'danger' : ($gr == 'kuning' ? 'warning' : ($gr == 'hijau' ? 'success' : 'primary'));
                                    ?>
                                    <span class="badge bg-<?= $grClass ?>">
                                        <?= esc($insiden['grading_risiko'] ?? '-') ?>
                                    </span>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="insiden-label">Tanggal Verifikasi :</div>
                                <div class="insiden-value">
                                    <?= date('d M Y H:i', strtotime($insiden['tgl_terima'] ?? $insiden['updated_at'])) ?>
                                </div>
                            </div>

                            <div class="col-md-12 mb-3">
                                <div class="insiden-label">Catatan KARU :</div>
                                <div class="insiden-value">
                                    <?= nl2br(esc($insiden['catatan_atasan'])) ?>
                                </div>
                            </div>

                        </div>

                    </div>

                </div>

            <?php endif; ?>


            <?php if ($insiden['status_laporan'] == 'SELESAI'): ?>

                <div class="insiden-section">

                    <div class="insiden-section-title">
                        <i class="bi bi-check2-square me-1"></i> Hasil Validasi Komite PMKP
                    </div>

                    <div class="insiden-box border-start border-4 border-success">

                        <!-- PENGERJA -->
                        <div class="mb-3">
                            <div class="insiden-label">Divalidasi/diselesaikan oleh :</div>
                            <div class="insiden-value">
                                <span class="badge bg-success">
                                    <i class="bi bi-shield-check me-1"></i>
                                    <?= isset($komite_user->full_name) ? esc($komite_user->full_name) : 'Komite PMKP' ?>
                                </span>
                                <small class="text-muted ms-1">
                                    <!-- (<?= isset($komite_user->nip) ? esc($komite_user->nip) : '-' ?>) -->
                                </small>
                            </div>
                        </div>

                        
                        <!-- GRADING FINAL -->
                        <div class="mb-2">
                            <div class="insiden-label">Grading Risiko Final :</div>
                            <div class="insiden-value">
                                <?= esc($insiden['grading_final'] ?? '-') ?>
                            </div>
                        </div>

                        <!-- PERBANDINGAN -->
                        <div class="mb-2">
                            <div class="insiden-label">Perbandingan Grading :</div>
                            <div class="insiden-value">
                                KARU : <?= esc($insiden['grading_risiko'] ?? '-') ?> <br>
                                KOMITE : <strong><?= esc($insiden['grading_final'] ?? '-') ?></strong>
                            </div>
                        </div>

                        <!-- CATATAN -->
                        <div class="mb-2">
                            <div class="insiden-label">Catatan Komite :</div>
                            <div class="insiden-value">
                                <?= nl2br(esc($insiden['catatan_komite'] ?? '')) ?>
                            </div>
                        </div>

                        <!-- WAKTU -->
                        <div class="mt-3 pt-2 border-top">
                            <i class="bi bi-clock me-1"></i>
                            Diselesaikan pada:
                            <?= date('d M Y H:i', strtotime($insiden['validated_at'] ?? date('Y-m-d H:i:s'))) ?>
                        </div>

                    </div>

                </div>

            <?php endif; ?>
        </div>

        <?php if ($user_role === 'KOMITE' && $insiden['status_laporan'] == 'INSTALASI'): ?>

            <div class="insiden-section">

                <div class="insiden-section-title">
                    <i class="bi bi-shield-check me-1"></i>Validasi Komite PMKP
                </div>

                <div class="alert alert-success">
                    <i class="bi bi-info-circle me-1"></i>
                    Anda sebagai <strong>Komite PMKP</strong>. Silakan lakukan analisa dan validasi terhadap laporan ini.
                </div>

                <input type="hidden" id="insiden_id" value="<?= $insiden['id'] ?>">

                <!-- CATATAN KOMITE -->
                <div class="mb-3">
                    <label class="insiden-label">Catatan Komite</label>
                    <textarea class="form-control"
                        id="catatan_komite"
                        rows="4"
                        placeholder="Tulis hasil analisa / validasi..."></textarea>
                </div>

                <!-- GRADING FINAL -->
                <label class="insiden-label mb-2">
                    Grading Risiko (Final)
                </label>

                <?php
                $gradingList = ['BIRU', 'HIJAU', 'KUNING', 'MERAH'];
                ?>

                <?php foreach ($gradingList as $g): ?>
                    <div class="form-check">
                        <input class="form-check-input"
                            type="radio"
                            name="grading_komite"
                            value="<?= $g ?>"
                            <?= ($insiden['grading_risiko'] == $g) ? 'checked' : '' ?>>
                        <label class="form-check-label">
                            <?= $g ?>
                        </label>
                    </div>
                <?php endforeach; ?>

                <!-- ERROR -->
                <div id="komite_error" class="text-danger mt-2"></div>

                <!-- ACTION BUTTON -->
                <div class="d-flex gap-2 mt-3">

                    <!-- SETUJUI -->
                    <button class="btn btn-success"
                        onclick="validasiKomite(this)"
                        data-aksi="setujui"
                        data-id="<?= $insiden['id'] ?>">
                        <i class="bi bi-check-circle"></i>
                        Selesai
                    </button>

                </div>

            </div>

        <?php endif; ?>
    </div>

    <div class="card-header mailbox-header d-flex align-items-center">

        <!-- LEFT TOOLBAR -->
        <div class="d-flex align-items-center gap-2">

            <!-- BACK -->
            <button class="btn btn-mailbox btn-sm btn-back" data-tipe="<?= esc($tipe) ?>">
                <i class="bi bi-arrow-left"></i>
            </button>

            <!-- RELOAD -->
            <button class="btn btn-mailbox btn-sm btn-detail-reload"
                data-id="<?= $insiden['id'] ?>" data-tipe="<?= esc($tipe ?? 'inbox') ?>">
                <i class="bi bi-arrow-repeat"></i>
            </button>
        </div>

        <!-- PUSH RIGHT -->
        <div class="ms-auto d-flex align-items-center gap-3">
            <!-- PAGINATION -->
            <div class="btn-group btn-group-sm">

                <?php if ($user_role === 'KARU' && in_array($insiden['status_laporan'], ['DRAFT', 'INBOX'])): ?>

                    <button class="btn btn-mailbox btn-sm"
                        data-bs-toggle="collapse"
                        data-bs-target="#formVerifikasi">
                        <i class="bi bi-reply"></i>
                        Verifikasi
                    </button>

                <?php endif; ?>
            </div>
        </div>
    </div>


    <?php if ($user_role === 'KARU' && in_array($insiden['status_laporan'], ['DRAFT', 'INBOX'])): ?>
        <div class="collapse" id="formVerifikasi">

            <div class="insiden-section">

                <div class="insiden-section-title">
                    <i class="bi bi-check2-square me-1"></i>Verifikasi / Balasan KARU
                </div>

                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-1"></i>
                    Anda sebagai <strong>Kepala Ruangan</strong>. Silakan verifikasi laporan insiden ini.
                </div>

                <input type="hidden" id="insiden_id" value="<?= $insiden['id'] ?>">

                <div class="mb-3">
                    <label class="insiden-label">Catatan KARU</label>
                    <textarea class="form-control"
                        id="catatan_karu"
                        rows="4"
                        placeholder="Tulis verifikasi..."></textarea>
                </div>

                <label class="insiden-label mb-2">
                    Grading Risiko
                </label>

                <div class="form-check">
                    <input class="form-check-input" type="radio" name="grading" value="BIRU" id="grading_biru">
                    <label class="form-check-label" for="grading_biru">
                        Biru
                    </label>
                </div>

                <div class="form-check">
                    <input class="form-check-input" type="radio" name="grading" value="HIJAU" id="grading_hijau">
                    <label class="form-check-label" for="grading_hijau">
                        Hijau
                    </label>
                </div>

                <div class="form-check">
                    <input class="form-check-input" type="radio" name="grading" value="KUNING" id="grading_kuning">
                    <label class="form-check-label" for="grading_kuning">
                        Kuning
                    </label>
                </div>

                <div class="form-check">
                    <input class="form-check-input" type="radio" name="grading" value="MERAH" id="grading_merah">
                    <label class="form-check-label" for="grading_merah">
                        Merah
                    </label>
                </div>
                <div id="verifikasi_error" class="text-danger mt-2"></div>

                <button class="btn btn-success mt-3 btn-kirim-verifikasi"
                    data-id="<?= $insiden['id'] ?>">
                    <i class="bi bi-send"></i>
                    Kirim Verifikasi
                </button>
            </div>

        </div>

    <?php endif; ?>

</div>