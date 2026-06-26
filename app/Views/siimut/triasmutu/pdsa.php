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
                <div class="col-md-6 text-end">
                    <a href="<?= site_url('siimut/trias-mutu/pengukuran') ?>" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Kembali
                    </a>
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
            <?php
            $defaultTools = '';
            if ($selected && empty($selected['pdsa']['tools'])) {
                $ind = $selected['indicator'] ?? null;
                if ($ind) {
                    $parts = [];
                    $parts[] = 'Indikator: ' . ($ind->indicator_element ?? '-');
                    $parts[] = 'Unit: ' . ($selected['unit_name'] ?? '-');
                    $parts[] = 'Periode: Triwulan ' . ($selected['triwulan'] ?? '-') . ' / ' . ($selected['tahun'] ?? '-');
                    if (!empty($ind->indicator_target)) {
                        $parts[] = 'Target: ' . $ind->indicator_target . ' ' . ($ind->indicator_target_unit ?? '');
                    }
                    $defaultTools = implode("\n", $parts);
                }
            }
            ?>
            <h6 class="fw-bold text-primary mb-3">A. TOOLS</h6>
            <div class="mb-4">
                <textarea class="form-control" id="tools" rows="3"
                    placeholder="Tulis judul indikator / tools..."><?= esc($selected['pdsa']['tools'] ?? $defaultTools) ?></textarea>
                <small class="text-danger field-error" id="error-tools" style="display:none;">Field Tools harus diisi</small>
            </div>

            <hr>
            <h6 class="fw-bold text-success mb-3">B. STEP</h6>
            <div id="stepsContainer">
                <?php
                $steps = !empty($selected['pdsa']['steps']) ? json_decode($selected['pdsa']['steps'], true) : [];
                if (empty($steps)) $steps = [''];
                foreach ($steps as $i => $step):
                ?>
                <div class="input-group mb-2 step-item">
                    <span class="input-group-text step-number"><?= $i + 1 ?></span>
                    <textarea class="form-control step-text" rows="2" placeholder="Langkah <?= $i + 1 ?>..."><?= esc($step) ?></textarea>
                    <button class="btn btn-outline-danger btn-remove-step" type="button"><i class="bi bi-x"></i></button>
                </div>
                <?php endforeach; ?>
            </div>
            <button class="btn btn-sm btn-outline-success mb-4" id="btnAddStep">
                <i class="bi bi-plus-lg me-1"></i> Tambah Langkah
            </button>
            <small class="text-danger field-error" id="error-steps" style="display:none;">Minimal 1 langkah harus diisi</small>

            <hr>
            <h6 class="fw-bold text-info mb-3">C. SIKLUS PDSA</h6>

            <div class="card border-primary mb-3">
                <div class="card-header bg-primary text-white fw-semibold">PLAN</div>
                <div class="card-body">
                    <label class="form-label fw-semibold">Rencana</label>
                    <textarea class="form-control mb-3" id="plan_rencana" rows="4"
                        placeholder="Uraikan rencana..."><?= esc($selected['pdsa']['plan_rencana'] ?? $selected['pdsa']['plan'] ?? '') ?></textarea>
                    <small class="text-danger field-error" id="error-plan_rencana" style="display:none;">Field Rencana harus diisi</small>
                    <label class="form-label fw-semibold">Target</label>
                    <textarea class="form-control" id="plan_target" rows="3"
                        placeholder="Uraikan target..."><?= esc($selected['pdsa']['plan_target'] ?? '') ?></textarea>
                    <small class="text-danger field-error" id="error-plan_target" style="display:none;">Field Target harus diisi</small>
                </div>
            </div>

            <div class="card border-success mb-3">
                <div class="card-header bg-success text-white fw-semibold">DO</div>
                <div class="card-body">
                    <label class="form-label fw-semibold">Hasil Pengamatan</label>
                    <textarea class="form-control" id="do_hasil" rows="4"
                        placeholder="Uraikan hasil pengamatan..."><?= esc($selected['pdsa']['do_hasil'] ?? $selected['pdsa']['do'] ?? '') ?></textarea>
                    <small class="text-danger field-error" id="error-do_hasil" style="display:none;">Field Hasil Pengamatan harus diisi</small>
                </div>
            </div>

            <div class="card border-warning mb-3">
                <div class="card-header bg-warning text-white fw-semibold">STUDY</div>
                <div class="card-body">
                    <label class="form-label fw-semibold">Hasil Pengamatan Disesuaikan Dengan Tujuan</label>
                    <textarea class="form-control" id="study_hasil" rows="4"
                        placeholder="Uraikan hasil pengamatan disesuaikan dengan tujuan..."><?= esc($selected['pdsa']['study_hasil'] ?? $selected['pdsa']['study'] ?? '') ?></textarea>
                    <small class="text-danger field-error" id="error-study_hasil" style="display:none;">Field Study harus diisi</small>
                </div>
            </div>

            <div class="card border-danger mb-3">
                <div class="card-header bg-danger text-white fw-semibold">ACT</div>
                <div class="card-body">
                    <label class="form-label fw-semibold">Kesimpulan Dalam Siklus Ini</label>
                    <textarea class="form-control mb-3" id="act_kesimpulan" rows="3"
                        placeholder="Tulis kesimpulan..."><?= esc($selected['pdsa']['act_kesimpulan'] ?? $selected['pdsa']['act'] ?? '') ?></textarea>
                    <small class="text-danger field-error" id="error-act_kesimpulan" style="display:none;">Field Kesimpulan harus diisi</small>
                    <label class="form-label fw-semibold">Tindak Lanjut Perbaikan</label>
                    <textarea class="form-control" id="act_tindak_lanjut" rows="3"
                        placeholder="Tulis tindak lanjut..."><?= esc($selected['pdsa']['act_tindak_lanjut'] ?? '') ?></textarea>
                    <small class="text-danger field-error" id="error-act_tindak_lanjut" style="display:none;">Field Tindak Lanjut harus diisi</small>
                </div>
            </div>

            <hr>
            <div class="d-flex justify-content-between">
                <button class="btn btn-outline-secondary" id="btnGeneratePdsa">
                    <i class="bi bi-magic me-1"></i> Generate PDSA
                </button>
                <button class="btn btn-primary" id="btnSavePdsa">
                    <i class="bi bi-save me-1"></i> Simpan PDSA
                </button>
                <small class="text-danger" id="formWarning" style="display:none;">Lengkapi semua field dan minimal 1 langkah untuk menyimpan</small>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="confirmGenerateModal" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center py-4">
                <i class="bi bi-question-circle text-warning" style="font-size:2.5rem;"></i>
                <p class="mt-2 mb-0">Generate PDSA akan mengisi semua form. Lanjutkan?</p>
            </div>
            <div class="modal-footer justify-content-center border-0 pt-0">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Tidak</button>
                <button class="btn btn-primary" id="btnConfirmGenerate">Ya, Generate</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    var dokumenId = <?= json_encode($selected['id'] ?? null) ?>;

    // Hilangkan error saat user mengetik
    $(document).on('input', 'textarea, input', function() {
        $(this).removeClass('is-invalid');
        var id = $(this).attr('id');
        if (id) $('#error-' + id).css('display', 'none');
        if ($(this).hasClass('step-text')) {
            $('#stepsContainer').removeClass('is-invalid');
            $('#error-steps').css('display', 'none');
        }
        $('#formWarning').hide();
    });

    function getSteps() {
        var steps = [];
        $('#stepsContainer .step-text').each(function() {
            steps.push($(this).val());
        });
        return steps;
    }

    function updateStepNumbers() {
        $('#stepsContainer .step-item').each(function(i) {
            $(this).find('.step-number').text(i + 1);
            $(this).find('.step-text').attr('placeholder', 'Langkah ' + (i + 1) + '...');
        });
    }

    $('#btnAddStep').on('click', function() {
        var num = $('#stepsContainer .step-item').length + 1;
        var html = '<div class="input-group mb-2 step-item">' +
            '<span class="input-group-text step-number">' + num + '</span>' +
            '<textarea class="form-control step-text" rows="2" placeholder="Langkah ' + num + '..."></textarea>' +
            '<button class="btn btn-outline-danger btn-remove-step" type="button"><i class="bi bi-x"></i></button>' +
            '</div>';
        $('#stepsContainer').append(html);
    });

    $(document).on('click', '.btn-remove-step', function() {
        if ($('#stepsContainer .step-item').length <= 1) return;
        $(this).closest('.step-item').remove();
        updateStepNumbers();
    });

    $('#btnGeneratePdsa').on('click', function() {
        if (!dokumenId) {
            toastWarning('Dokumen belum dipilih.');
            return;
        }
        $('#confirmGenerateModal').modal('show');
    });

    $('#btnConfirmGenerate').on('click', function() {
        $('#confirmGenerateModal').modal('hide');

        $.ajax({
            url: '<?= site_url('siimut/trias-mutu/generate-pdsa') ?>',
            method: 'POST',
            data: { dokumen_id: dokumenId },
            beforeSend: function() {
                $('#btnGeneratePdsa').prop('disabled', true).html('<i class="bi bi-hourglass"></i> Generating...');
            },
            success: function(res) {
                if (res.success) {
                    $('#tools').val(res.tools);
                    if (res.steps && res.steps.length) {
                        $('#stepsContainer').empty();
                        $.each(res.steps, function(i, s) {
                            var num = i + 1;
                            var html = '<div class="input-group mb-2 step-item">' +
                                '<span class="input-group-text step-number">' + num + '</span>' +
                                '<textarea class="form-control step-text" rows="2" placeholder="Langkah ' + num + '...">' + s + '</textarea>' +
                                '<button class="btn btn-outline-danger btn-remove-step" type="button"><i class="bi bi-x"></i></button>' +
                                '</div>';
                            $('#stepsContainer').append(html);
                        });
                    }
                    $('#plan_rencana').val(res.plan_rencana);
                    $('#plan_target').val(res.plan_target);
                    $('#do_hasil').val(res.do_hasil);
                    $('#study_hasil').val(res.study_hasil);
                    $('#act_kesimpulan').val(res.act_kesimpulan);
                    $('#act_tindak_lanjut').val(res.act_tindak_lanjut);
                } else {
                    toastWarning(res.message || 'Gagal generate');
                }
            },
            error: function() {
                toastError('Terjadi kesalahan');
            },
            complete: function() {
                $('#btnGeneratePdsa').prop('disabled', false).html('<i class="bi bi-magic me-1"></i> Generate PDSA');
            }
        });
    });

    $('#btnSavePdsa').on('click', function() {
        if (!dokumenId) {
            toastWarning('Dokumen belum dipilih.');
            return;
        }

        $('.field-error').css('display', 'none');
        $('textarea.is-invalid').removeClass('is-invalid');
        $('#stepsContainer').removeClass('is-invalid');
        $('#formWarning').hide();

        var fields = {
            tools: $('#tools').val().trim(),
            plan_rencana: $('#plan_rencana').val().trim(),
            plan_target: $('#plan_target').val().trim(),
            do_hasil: $('#do_hasil').val().trim(),
            study_hasil: $('#study_hasil').val().trim(),
            act_kesimpulan: $('#act_kesimpulan').val().trim(),
            act_tindak_lanjut: $('#act_tindak_lanjut').val().trim()
        };

        var valid = true;
        $.each(fields, function(id, val) {
            if (!val) {
                $('#' + id).addClass('is-invalid');
                $('#error-' + id).css('display', 'block');
                valid = false;
            }
        });

        var stepsValid = false;
        $('#stepsContainer .step-text').each(function() {
            if ($(this).val().trim()) stepsValid = true;
        });
        if (!stepsValid) {
            $('#stepsContainer').addClass('is-invalid');
            $('#error-steps').css('display', 'block');
            valid = false;
        }

        if (!valid) {
            $('#formWarning').show();
            return;
        }

        $.ajax({
            url: '<?= site_url('siimut/trias-mutu/save-pdsa') ?>',
            method: 'POST',
            data: {
                dokumen_id: dokumenId,
                tools: fields.tools,
                steps: JSON.stringify(getSteps()),
                plan_rencana: fields.plan_rencana,
                plan_target: fields.plan_target,
                do_hasil: fields.do_hasil,
                study_hasil: fields.study_hasil,
                act_kesimpulan: fields.act_kesimpulan,
                act_tindak_lanjut: fields.act_tindak_lanjut
            },
            beforeSend: function() {
                $('#btnSavePdsa').prop('disabled', true).html('<i class="bi bi-hourglass"></i> Menyimpan...');
            },
            success: function(res) {
                if (res.success) {
                    toastSuccess('PDSA berhasil disimpan');
                } else {
                    toastWarning(res.message || 'Gagal menyimpan');
                }
            },
            error: function() {
                toastError('Terjadi kesalahan');
            },
            complete: function() {
                $('#btnSavePdsa').prop('disabled', false).html('<i class="bi bi-save me-1"></i> Simpan PDSA');
            }
        });
    });
});
</script>
