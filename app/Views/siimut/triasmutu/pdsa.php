<div class="container-fluid py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body py-3">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5 class="fw-semibold mb-1">
                        <i class="bi bi-arrow-repeat me-2"></i>
                        Siklus PDSA
                    </h5>
                    <small class="text-muted">Plan-Do-Study-Act: Siklus perbaikan mutu berkelanjutan</small>
                </div>
            </div>
        </div>
    </div>

    <?php if ($selected): ?>
        <div class="alert alert-info">
            <strong><?= esc($selected['unit_name']) ?></strong> &mdash;
            <?= esc($selected['indicator']->indicator_element ?? '-') ?> &mdash;
            Triwulan <?= $selected['triwulan'] ?> / <?= $selected['tahun'] ?>
            <a href="<?= site_url('siimut/trias-mutu/cetak?dokumen_id=' . $selected['id']) ?>" class="float-end btn btn-sm btn-info">
                <i class="bi bi-printer"></i> Cetak
            </a>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-header">
            <i class="bi bi-pen me-2"></i>Form Siklus PDSA
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary">
                        <i class="bi bi-lightbulb me-1"></i> Plan (Rencana)
                    </label>
                    <textarea class="form-control" id="plan" rows="5"
                        placeholder="Uraikan rencana perbaikan yang akan dilakukan..."><?= esc($selected['pdsa']['plan'] ?? '') ?></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-success">
                        <i class="bi bi-gear me-1"></i> Do (Pelaksanaan)
                    </label>
                    <textarea class="form-control" id="do" rows="5"
                        placeholder="Uraikan pelaksanaan dari rencana tersebut..."><?= esc($selected['pdsa']['do'] ?? '') ?></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-warning">
                        <i class="bi bi-search me-1"></i> Study (Evaluasi)
                    </label>
                    <textarea class="form-control" id="study" rows="5"
                        placeholder="Uraikan hasil evaluasi dari pelaksanaan..."><?= esc($selected['pdsa']['study'] ?? '') ?></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-danger">
                        <i class="bi bi-check2-square me-1"></i> Act (Tindak Lanjut)
                    </label>
                    <textarea class="form-control" id="act" rows="5"
                        placeholder="Uraikan tindak lanjut berdasarkan hasil evaluasi..."><?= esc($selected['pdsa']['act'] ?? '') ?></textarea>
                </div>
            </div>

            <hr>
            <div class="text-end">
                <button class="btn btn-primary" id="btnSavePdsa">
                    <i class="bi bi-save me-1"></i> Simpan PDSA
                </button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#btnSavePdsa').on('click', function() {
        var dokumenId = <?= json_encode($selected['id'] ?? null) ?>;
        if (!dokumenId) {
            alert('Dokumen belum dipilih. Silakan buat dokumen dari menu Pengukuran Indikator terlebih dahulu.');
            return;
        }

        $.ajax({
            url: '<?= site_url('siimut/trias-mutu/save-pdsa') ?>',
            method: 'POST',
            data: {
                dokumen_id: dokumenId,
                plan: $('#plan').val(),
                do: $('#do').val(),
                study: $('#study').val(),
                act: $('#act').val()
            },
            beforeSend: function() {
                $('#btnSavePdsa').prop('disabled', true).html('<i class="bi bi-hourglass"></i> Menyimpan...');
            },
            success: function(res) {
                if (res.success) {
                    alert('PDSA berhasil disimpan');
                } else {
                    alert(res.message || 'Gagal menyimpan');
                }
            },
            error: function() {
                alert('Terjadi kesalahan');
            },
            complete: function() {
                $('#btnSavePdsa').prop('disabled', false).html('<i class="bi bi-save me-1"></i> Simpan PDSA');
            }
        });
    });
});
</script>
