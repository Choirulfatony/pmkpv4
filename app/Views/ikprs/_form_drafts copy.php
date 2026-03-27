<div class="table-responsive mailbox-messages">

    <!-- ================= TOOLBAR ================= -->
    <div class="mailbox-controls mb-2 d-flex justify-content-between align-items-center">

        <div>
            <!-- Check All -->
            <button type="button"
                class="btn btn-light btn-sm checkbox-toggle"
                title="Pilih semua">
                <i class="far fa-square"></i>
            </button>

            <div class="btn-group ms-1">
                <button type="button"
                    class="btn btn-light btn-sm"
                    onclick="deleteSelectedDraft()"
                    title="Hapus Draft Terpilih">
                    <i class="far fa-trash-alt"></i>
                </button>

                <button type="button"
                    class="btn btn-light btn-sm"
                    onclick="reloadDraft()"
                    title="Reload">
                    <i class="fas fa-sync-alt"></i>
                </button>
            </div>
        </div>

        <small class="text-muted">
            Total:
            <?= (($page - 1) * 10) + 1 ?>
            –
            <?= min($page * 10, $total) ?>
            dari <?= $total ?> data
        </small>

    </div>

    <!-- ================= TABLE ================= -->
    <table class="table table-hover table-striped align-middle">
        <tbody>

            <?php if (empty($list)): ?>
                <tr>
                    <td colspan="6" class="text-center text-muted p-4">
                        Tidak ada laporan draft
                    </td>
                </tr>
            <?php endif; ?>

            <?php foreach ($list as $row): ?>
                <tr class="draft-row"
                    data-id="<?= esc($row['id']) ?>"
                    style="cursor:pointer">

                    <!-- checkbox -->
                    <td width="30" onclick="event.stopPropagation();">
                        <input type="checkbox"
                            class="form-check-input check-item"
                            value="<?= esc($row['id']) ?>">
                    </td>

                    <!-- icon -->
                    <td width="30" class="text-center">
                        <i class="far fa-file text-secondary"></i>
                    </td>

                    <!-- pasien -->
                    <td width="220">
                        <div class="fw-semibold">
                            <?= esc($row['nama_pasien']) ?>
                        </div>
                        <small class="text-muted">
                            <?= esc($row['kd_pasien']) ?>
                        </small>
                    </td>

                    <!-- isi -->
                    <td class="text-wrap">
                        <div class="fw-semibold">
                            <?= esc($row['jenis_insiden']) ?>
                        </div>
                        <small>
                            <?= nl2br(esc(strip_tags($row['kronologis_insiden']))) ?>
                        </small>
                    </td>

                    <!-- status -->
                    <td width="90" class="text-center">
                        <span class="badge bg-secondary">
                            Draft
                        </span>
                    </td>

                    <!-- tanggal -->
                    <td width="150" class="text-muted text-nowrap">
                        <?= date('d M Y H:i', strtotime($row['created_at'])) ?>
                    </td>

                </tr>
            <?php endforeach; ?>

        </tbody>
    </table>

    <!-- ================= PAGINATION ================= -->
    <div class="card-footer p-0">
        <div class="mailbox-controls d-flex justify-content-end">

            <div class="btn-group">

                <!-- PREVIOUS -->
                <button type="button"
                    class="btn btn-light btn-sm btn-draft-prev <?= ($page <= 1 ? 'disabled' : '') ?>"
                    data-page="<?= $page - 1 ?>"
                    <?= ($page <= 1 ? 'disabled' : '') ?>>
                    <i class="fas fa-chevron-left"></i>
                </button>

                <!-- INFO PAGE -->

                <button type="button"
                    class="btn btn-light btn-sm disabled">
                    <?= $page ?> / <?= max(1, $total_pages) ?>
                </button>

                <!-- NEXT -->
                <button type="button"
                    class="btn btn-light btn-sm btn-draft-next <?= ($page >= $total_pages ? 'disabled' : '') ?>"
                    data-page="<?= $page + 1 ?>"
                    <?= ($page >= $total_pages ? 'disabled' : '') ?>>
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>

        </div>
    </div>

</div>