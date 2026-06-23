<div class="container-fluid py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body py-3">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5 class="fw-semibold mb-1">
                        <i class="bi bi-diagram-3 me-2"></i>
                        Analisis Penyebab Masalah
                    </h5>
                    <small class="text-muted">Identifikasi akar penyebab masalah menggunakan diagram tulang ikan (Fishbone)</small>
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
            <i class="bi bi-pen me-2"></i>Form Analisis Penyebab
        </div>
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label">Permasalahan</label>
                <textarea class="form-control" id="permasalahan" rows="2"><?= esc($selected['analisis'][0]['permasalahan'] ?? '') ?></textarea>
                <div class="field-error text-danger small mt-1" style="display:none;"><i class="bi bi-exclamation-circle me-1"></i>Permasalahan wajib diisi</div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered" id="fishboneTable">
                    <thead class="table-light">
                        <tr>
                            <th style="width:20%">Kategori</th>
                            <th>Penyebab</th>
                            <th style="width:60px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $kategoriList = ['Man', 'Machine', 'Method', 'Material', 'Mothernature/Lingkungan', 'Measurement', 'Money']; ?>
                        <?php if ($selected && !empty($selected['analisis'])): ?>
                            <?php foreach ($selected['analisis'] as $row): ?>
                                <tr>
                                    <td>
                                        <select class="form-select form-select-sm kategori">
                                            <?php foreach ($kategoriList as $kat): ?>
                                                <option value="<?= $kat ?>" <?= $row['kategori'] === $kat ? 'selected' : '' ?>><?= $kat ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td>
                                        <textarea class="form-control form-control-sm penyebab" rows="2"><?= esc($row['penyebab']) ?></textarea>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-danger btnRemoveRow"><i class="bi bi-x"></i></button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td>
                                    <select class="form-select form-select-sm kategori">
                                        <?php foreach ($kategoriList as $kat): ?>
                                            <option value="<?= $kat ?>"><?= $kat ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td>
                                    <textarea class="form-control form-control-sm penyebab" rows="2"></textarea>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-danger btnRemoveRow"><i class="bi bi-x"></i></button>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="mt-2 mb-3">
                <button class="btn btn-sm btn-outline-secondary" id="btnAddRow">
                    <i class="bi bi-plus-lg"></i> Tambah Baris
                </button>
                <div class="field-error text-danger small mt-1" style="display:none;"><i class="bi bi-exclamation-circle me-1"></i>Kategori & Penyebab wajib diisi</div>
            </div>

            <hr>
            <div class="d-flex justify-content-between align-items-center">
                <small class="text-danger"><i class="bi bi-exclamation-circle me-1"></i> Semua kolom harus diisi</small>
                <div>
                    <button class="btn btn-primary" id="btnSaveAnalisis">
                        <i class="bi bi-save me-1"></i> Simpan Analisis
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    var kategoriList = ['Man', 'Machine', 'Method', 'Material', 'Mothernature/Lingkungan', 'Measurement', 'Money'];

    $('#btnAddRow').on('click', function() {
        var options = '';
        kategoriList.forEach(function(k) {
            options += '<option value="' + k + '">' + k + '</option>';
        });
        var tr = '<tr>' +
            '<td><select class="form-select form-select-sm kategori">' + options + '</select></td>' +
            '<td><textarea class="form-control form-control-sm penyebab" rows="2"></textarea></td>' +
            '<td class="text-center"><button class="btn btn-sm btn-danger btnRemoveRow"><i class="bi bi-x"></i></button></td>' +
            '</tr>';
        $('#fishboneTable tbody').append(tr);
    });

    $(document).on('click', '.btnRemoveRow', function() {
        if ($('#fishboneTable tbody tr').length > 1) {
            $(this).closest('tr').remove();
        } else {
            toastWarning('Minimal harus ada satu baris');
        }
    });

    $('#permasalahan').on('input', function() { $('.field-error').first().hide(); });
    $(document).on('input', '.penyebab', function() { $('.field-error').last().hide(); });
    $(document).on('change', '.kategori', function() { $('.field-error').last().hide(); });

    $('#btnSaveAnalisis').on('click', function() {
        var dokumenId = <?= json_encode($selected['id'] ?? null) ?>;
        if (!dokumenId) {
            toastWarning('Dokumen belum dipilih. Silakan buat dokumen dari menu Pengukuran Indikator terlebih dahulu.');
            return;
        }

        var kategori = [];
        var penyebab = [];
        var permasalahan = $('#permasalahan').val().trim();
        $('.field-error').hide();

        if (!permasalahan) {
            $('.field-error').first().show();
            return;
        }

        var valid = true;
        $('#fishboneTable tbody tr').each(function() {
            var kat = $(this).find('.kategori').val();
            var pen = $(this).find('.penyebab').val().trim();
            if (!kat || !pen) {
                valid = false;
                return false;
            }
            kategori.push(kat);
            penyebab.push(pen);
        });

        if (!valid) {
            $('.field-error').last().show();
            return;
        }

        $.ajax({
            url: '<?= site_url('siimut/trias-mutu/save-analisis') ?>',
            method: 'POST',
            data: {
                dokumen_id: dokumenId,
                permasalahan: permasalahan,
                kategori: kategori,
                penyebab: penyebab
            },
            beforeSend: function() {
                $('#btnSaveAnalisis').prop('disabled', true).html('<i class="bi bi-hourglass"></i> Menyimpan...');
            },
            success: function(res) {
                if (res.success) {
                    $('.field-error').hide();
                    toastSuccess('Analisis penyebab berhasil disimpan');
                } else {
                    toastError(res.message || 'Gagal menyimpan');
                }
            },
            error: function() {
                toastError('Terjadi kesalahan');
            },
            complete: function() {
                $('#btnSaveAnalisis').prop('disabled', false).html('<i class="bi bi-save me-1"></i> Simpan Analisis');
            }
        });
    });
});
</script>
