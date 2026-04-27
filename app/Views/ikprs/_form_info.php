<div class="card-header mailbox-header d-flex align-items-start gap-2 flex-wrap">

    <!-- LEFT TOOLBAR -->
    <div class="d-flex gap-2">
        <button class="btn btn-mailbox btn-sm btn-info-reload" title="Reload">
            <i class="bi bi-arrow-repeat"></i>
        </button>
    </div>

    <!-- PUSH RIGHT -->
    <div class="ms-auto"></div>

    <!-- RIGHT AREA: SEARCH + INFO -->
    <div class="d-flex flex-column align-items-end gap-1">

        <!-- SEARCH -->
        <div class="input-group input-group-sm mailbox-search" style="width: 220px;">
            <input type="text"
                class="form-control"
                id="searchInfo"
                placeholder="Cari notifikasi..."
                value="<?= esc($keyword ?? '') ?>">

            <button class="btn btn-primary btn-search-info" type="button">
                <i class="bi bi-search"></i>
            </button>
        </div>

    </div>
</div>

<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table table-hover mailbox-table mb-0">
            <tbody>

                <?php if (empty($notif)): ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted p-4">
                            Tidak ada notifikasi
                        </td>
                    </tr>
                <?php endif; ?>

                <?php 
                $role = session()->get('user_role');
                foreach ($notif as $row): 
                    // ===================== SAMA DENGAN HEADER DROPDOWN =====================
                    // iconJenis - SAMA dengan di _header.php line 584-604
                    $iconJenis = 'bi bi-info-circle';
                    if ($row['jenis'] == 'KTD') { $iconJenis = 'bi bi-exclamation-triangle-fill text-danger'; }
                    elseif ($row['jenis'] == 'KNC') { $iconJenis = 'bi bi-shield-exclamation text-purple'; }
                    elseif ($row['jenis'] == 'KTC') { $iconJenis = 'bi bi-exclamation-circle-fill text-warning'; }
                    elseif ($row['jenis'] == 'KPC') { $iconJenis = 'bi bi-info-circle-fill text-primary'; }
                    elseif ($row['jenis'] == 'SENTINEL') { $iconJenis = 'bi bi-exclamation-octagon-fill text-danger'; }

                    // Status read logic - SAMA dengan di _header.php line 650-704
                    $status_read = 'Belum Dibaca';
                    $warna_status = 'text-danger';
                    $iconStatus = 'bi bi-circle-fill';

                    if ($role == 'PELAPOR') {
                        if ($row['is_read'] == 1) {
                            $status_read = 'Sudah Dibaca';
                            $warna_status = 'text-primary';
                        }
                    } elseif ($role == 'KARU') {
                        if ($row['is_read'] == 1) {
                            if (!empty($row['komite_read_at'])) {
                                $status_read = 'Telah Dibaca Komite';
                                $warna_status = 'text-success';
                                $iconStatus = 'bi bi-check-circle-fill';
                            } else if (!empty($row['karu_read_at'])) {
                                $status_read = 'Sudah Dibaca';
                                $warna_status = 'text-primary';
                                $iconStatus = 'bi bi-eye-fill';
                            } else {
                                $status_read = 'Sudah Dibaca';
                                $warna_status = 'text-primary';
                            }
                        }
                    } elseif ($role == 'KOMITE') {
                        if (!empty($row['komite_read_at'])) {
                            $status_read = 'Sudah Dibaca';
                            $warna_status = 'text-success';
                            $iconStatus = 'bi bi-check-circle-fill';
                        }
                    }

                    // Status laporan badge
                    $statusLabel = $row['status_laporan'] ?? '-';
                    $statusColor = 'secondary';
                    if ($statusLabel == 'DRAFT') { $statusColor = 'warning'; }
                    elseif ($statusLabel == 'KARU') { $statusColor = 'info'; }
                    elseif ($statusLabel == 'TERKIRIM') { $statusColor = 'primary'; }
                    elseif ($statusLabel == 'INSTALASI') { $statusColor = 'primary'; }
                    elseif ($statusLabel == 'SELESAI') { $statusColor = 'success'; }
                ?>

                    <tr class="info-row <?= $row['is_read'] == 0 ? 'notif-unread' : '' ?>"
                        data-id="<?= esc($row['insiden_id']) ?>">

                        <!-- DOT UNREAD + ICON JENIS -->
                        <td class="mailbox-star text-muted" style="width:40px;">
                            <?php if ($row['is_read'] == 0): ?>
                                <div class="notif-dot"></div>
                            <?php endif; ?>
                            <i class="<?= $iconJenis ?>" style="font-size:15px;"
                                title="<?= esc($row['jenis']) ?>"></i>
                        </td>

                        <!-- JENIS + UNIT -->
                        <td class="mailbox-name" style="min-width:120px;">
                            <div class="notif-title">
                                <strong><?= esc($row['jenis']) ?></strong> - <?= esc($row['unit']) ?>
                            </div>
                            <div class="notif-desc small text-muted">
                                <?= esc($row['status_text']) ?>
                            </div>
                            <div class="notif-status <?= $warna_status ?> small">
                                <i class="<?= $iconStatus ?>"></i> <?= $status_read ?>
                            </div>
                        </td>

                        <!-- STATUS LAPORAN -->
                        <td class="text-center" style="width:100px;">
                            <span class="badge bg-<?= $statusColor ?>">
                                <?= esc($statusLabel) ?>
                            </span>
                        </td>

                        <!-- WAKTU -->
                        <td class="mailbox-name" style="width:120px;">
                            <small class="text-muted mailbox-date">
                                <?= esc($row['waktu_lalu']) ?>
                            </small>
                        </td>

                    </tr>

                <?php endforeach; ?>

            </tbody>
        </table>
    </div>
</div>

<?php
$start = $total > 0 ? (($page - 1) * 20) + 1 : 0;
$end   = $total > 0 ? min($page * 20, $total) : 0;
?>

<?php if ($total_pages > 0): ?>
<div class="card-header mailbox-header d-flex align-items-center gap-2">

    <!-- INFO -->
    <span class="text-muted small">
        <?= $start ?> – <?= $end ?> / <?= $total ?>
    </span>

    <div class="ms-auto"></div>

    <span class="text-muted small">
        <?= $total > 0 ? $page : 0 ?> / <?= $total_pages ?>
    </span>

    <div class="btn-group btn-group-sm">
        <button class="btn btn-mailbox btn-info-prev"
            data-page="<?= $page - 1 ?>"
            <?= ($page <= 1 || $total == 0 ? 'disabled' : '') ?>>
            <i class="bi bi-chevron-left"></i>
        </button>

        <button class="btn btn-mailbox btn-info-next"
            data-page="<?= $page + 1 ?>"
            <?= ($page >= $total_pages || $total == 0 ? 'disabled' : '') ?>>
            <i class="bi bi-chevron-right"></i>
        </button>
    </div>

</div>
<?php endif; ?>