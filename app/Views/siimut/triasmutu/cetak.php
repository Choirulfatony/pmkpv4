<?php
$categoryLabels = [4 => 'INM', 5 => 'IMPRS', 6 => 'IMPUNIT'];
$triwulanLabels = ['', 'I', 'II', 'III', 'IV'];
?>
<div class="container-fluid py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body py-3">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5 class="fw-semibold mb-1">
                        <i class="bi bi-printer me-2"></i>
                        Cetak Trias Mutu
                    </h5>
                    <small class="text-muted">Preview lengkap dokumen analisis mutu sebelum dicetak</small>
                </div>
                <div class="col-md-6">
                    <div class="row g-2 justify-content-end">
                        <div class="col-md-2">
                            <select class="form-select form-select-sm" id="category_id">
                                <option value="4">INM</option>
                                <option value="5">IMPRS</option>
                                <option value="6">IMP Unit</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select form-select-sm" id="unit_id">
                                <option value="">Unit...</option>
                                <?php foreach ($units as $u): ?>
                                    <option value="<?= $u['department_id'] ?>"><?= esc($u['department_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select form-select-sm" id="tahun">
                                <?php foreach ($tahunList as $t): ?>
                                    <option value="<?= $t ?>"><?= $t ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select form-select-sm" id="triwulan">
                                <option value="1">TW I</option>
                                <option value="2">TW II</option>
                                <option value="3">TW III</option>
                                <option value="4">TW IV</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select form-select-sm" id="indicator_id" style="display:none;">
                                <option value="">Indikator...</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-primary btn-sm w-100" id="btnLoad">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="previewArea">
        <?php if ($selected): renderCetakPreview($selected, $measurement, $categoryLabels, $triwulanLabels); ?>
        <?php else: ?>
            <div class="text-center text-muted py-5">
                <i class="bi bi-file-earmark-text fs-1 d-block mb-3"></i>
                <p>Pilih unit, jenis indikator, indikator, triwulan, dan tahun, lalu klik tombol cari untuk menampilkan preview dokumen.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
function renderCetakPreview($d, $m, $categoryLabels, $triwulanLabels)
{
    $ind = $m['indicator'] ?? null;
    $analisis = $d['analisis'] ?? [];
    $pdsa = $d['pdsa'] ?? null;
    $ttd = $d['ttd'] ?? null;
    $bulanNames = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
?>
    <div class="card shadow-sm mb-4" id="dokumenPreview">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="bi bi-file-earmark-text me-2"></i>Preview Dokumen</span>
            <?php if ($d['status'] === 'final'): ?>
                <span class="badge bg-success fs-6 px-3 py-2">
                    <i class="bi bi-check-circle me-1"></i> FINAL
                </span>
            <?php else: ?>
                <span class="badge bg-warning text-dark fs-6 px-3 py-2">
                    <i class="bi bi-pencil me-1"></i> DRAFT
                </span>
            <?php endif; ?>
        </div>
        <div class="card-body" id="cetakContent">
            <div class="border rounded p-3 mb-4 bg-light">
                <h6 class="fw-bold border-bottom pb-2 mb-3"><i class="bi bi-info-circle me-2"></i>Informasi Dokumen</h6>
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless mb-0">
                            <tr><td style="width:140px"><strong>Nama Unit</strong></td><td>: <?= esc($d['unit_name'] ?? '-') ?></td></tr>
                            <tr><td><strong>Jenis Indikator</strong></td><td>: <?= $categoryLabels[$d['indicator_category_id']] ?? '-' ?></td></tr>
                            <tr><td><strong>Nama Indikator</strong></td><td>: <?= esc($ind->indicator_element ?? '-') ?></td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless mb-0">
                            <tr><td style="width:140px"><strong>Periode</strong></td><td>: Triwulan <?= $triwulanLabels[$d['triwulan']] ?? '-' ?></td></tr>
                            <tr><td><strong>Tahun</strong></td><td>: <?= $d['tahun'] ?? '-' ?></td></tr>
                            <tr><td><strong>Status</strong></td><td>: <?= $d['status'] === 'final' ? 'Final' : 'Draft' ?></td></tr>
                        </table>
                    </div>
                </div>
            </div>

            <div class="border rounded p-3 mb-4">
                <h6 class="fw-bold border-bottom pb-2 mb-3"><i class="bi bi-bar-chart me-2"></i>A. Pengukuran Indikator</h6>
                <table class="table table-sm table-borderless mb-3" style="max-width:500px">
                    <tr><td style="width:140px"><strong>Nama Indikator</strong></td><td>: <?= esc($ind->indicator_element ?? '-') ?></td></tr>
                    <tr><td><strong>Numerator</strong></td><td>: <?= $m['total_num'] ?? 0 ?></td></tr>
                    <tr><td><strong>Denominator</strong></td><td>: <?= $m['total_denum'] ?? 0 ?></td></tr>
                    <tr><td><strong>Formula</strong></td><td>: (Numerator / Denominator) &times; Faktor</td></tr>
                    <tr><td><strong>Standar / Target</strong></td><td>: <?= ($ind->indicator_target ?? '0') . ' ' . ($ind->indicator_units ?? '') ?></td></tr>
                    <tr><td><strong>Nilai Triwulan</strong></td><td>: <strong><?= $m['nilai_triwulan'] !== null ? $m['nilai_triwulan'] . ' ' . ($ind->indicator_units ?? '') : 'Tidak ada data' ?></strong></td></tr>
                    <tr>
                        <td><strong>Ketercapaian</strong></td>
                        <td>:
                            <?php if ($m['nilai_triwulan'] !== null): ?>
                                <?php if ((float) $m['nilai_triwulan'] >= (float) ($ind->indicator_target ?? 0)): ?>
                                    <span class="badge bg-success">Tercapai</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Belum Tercapai</span>
                                    <small class="text-danger">(Gap: <?= round((float) ($ind->indicator_target ?? 0) - (float) $m['nilai_triwulan'], 2) ?>)</small>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="badge bg-secondary">Tidak ada data</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>
                <div class="table-responsive mb-3">
                    <table class="table table-bordered table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>Bulan</th>
                                <th>Numerator</th>
                                <th>Denominator</th>
                                <th>Nilai</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (($m['bulanan'] ?? []) as $b): ?>
                                <tr>
                                    <td><?= $bulanNames[$b['bulan']] ?? 'Bulan ' . $b['bulan'] ?></td>
                                    <td><?= $b['num'] ?></td>
                                    <td><?= $b['denum'] ?></td>
                                    <td><?= $b['nilai'] !== null ? $b['nilai'] . ' ' . ($ind->indicator_units ?? '') : '-' ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="table-warning">
                            <tr>
                                <th>Total Triwulan</th>
                                <th><?= $m['total_num'] ?? 0 ?></th>
                                <th><?= $m['total_denum'] ?? 0 ?></th>
                                <th><?= $m['nilai_triwulan'] !== null ? $m['nilai_triwulan'] . ' ' . ($ind->indicator_units ?? '') : '-' ?></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="mb-3">
                    <canvas id="bulananChart" height="200"></canvas>
                </div>
                <div class="alert alert-info mb-0 py-2">
                    <small><i class="bi bi-info-circle me-1"></i>
                        <strong>Analisis Otomatis:</strong>
                        <?php if ($m['nilai_triwulan'] !== null): ?>
                            Nilai Triwulan <?= $triwulanLabels[$d['triwulan']] ?> sebesar
                            <strong><?= $m['nilai_triwulan'] . ' ' . ($ind->indicator_units ?? '') ?></strong>
                            <?php if ((float) $m['nilai_triwulan'] >= (float) ($ind->indicator_target ?? 0)): ?>
                            telah memenuhi target
                            <?php else: ?>
                            belum memenuhi target <?= ($ind->indicator_target ?? '0') . ' ' . ($ind->indicator_units ?? '') ?>
                            (gap <?= round((float) ($ind->indicator_target ?? 0) - (float) $m['nilai_triwulan'], 2) ?>)
                            <?php endif; ?>.
                            <?php
                            $trends = [];
                            foreach (($m['bulanan'] ?? []) as $b) {
                                if ($b['nilai'] !== null) $trends[] = $b['nilai'];
                            }
                            if (count($trends) >= 2):
                                $trend = 'stabil';
                                $allSame = count(array_unique($trends)) === 1;
                                $increasing = end($trends) > reset($trends);
                                $decreasing = end($trends) < reset($trends);
                                if ($allSame) $trend = 'relatif stabil';
                                elseif ($increasing) $trend = 'meningkat';
                                elseif ($decreasing) $trend = 'menurun';
                                echo 'Tren selama triwulan ini ' . $trend . '.';
                            endif; ?>
                        <?php else: ?>
                            Belum ada data pengukuran untuk periode ini.
                        <?php endif; ?>
                    </small>
                </div>
            </div>

            <div class="border rounded p-3 mb-4">
                <h6 class="fw-bold border-bottom pb-2 mb-3"><i class="bi bi-diagram-3 me-2"></i>B. Analisis Penyebab Masalah</h6>
                <?php if (!empty($analisis)): ?>
                    <div class="mb-3">
                        <strong>Permasalahan:</strong>
                        <p class="mb-2"><?= nl2br(esc($analisis[0]['permasalahan'] ?? '-')) ?></p>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:50px">No</th>
                                    <th style="width:150px">Kategori</th>
                                    <th>Penyebab</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; foreach ($analisis as $a): ?>
                                    <tr>
                                        <td class="text-center"><?= $no++ ?></td>
                                        <td><?= esc($a['kategori']) ?></td>
                                        <td><?= nl2br(esc($a['penyebab'])) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted mb-0"><i class="bi bi-pencil me-1"></i> Belum ada data analisis penyebab. <a href="<?= site_url('siimut/trias-mutu/analisis-penyebab?dokumen_id=' . $d['id']) ?>">Isi sekarang</a></p>
                <?php endif; ?>
            </div>

            <div class="border rounded p-3 mb-4">
                <h6 class="fw-bold border-bottom pb-2 mb-3"><i class="bi bi-arrow-repeat me-2"></i>C. Siklus PDSA</h6>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="card h-100 border-primary">
                            <div class="card-header bg-primary text-white py-1"><small><strong>Plan (Rencana)</strong></small></div>
                            <div class="card-body py-2"><small><?= nl2br(esc($pdsa['plan'] ?? '<span class="text-muted">Belum diisi</span>')) ?></small></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card h-100 border-success">
                            <div class="card-header bg-success text-white py-1"><small><strong>Do (Pelaksanaan)</strong></small></div>
                            <div class="card-body py-2"><small><?= nl2br(esc($pdsa['do'] ?? '<span class="text-muted">Belum diisi</span>')) ?></small></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card h-100 border-warning">
                            <div class="card-header bg-warning text-dark py-1"><small><strong>Study (Evaluasi)</strong></small></div>
                            <div class="card-body py-2"><small><?= nl2br(esc($pdsa['study'] ?? '<span class="text-muted">Belum diisi</span>')) ?></small></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card h-100 border-danger">
                            <div class="card-header bg-danger text-white py-1"><small><strong>Act (Tindak Lanjut)</strong></small></div>
                            <div class="card-body py-2"><small><?= nl2br(esc($pdsa['act'] ?? '<span class="text-muted">Belum diisi</span>')) ?></small></div>
                        </div>
                    </div>
                </div>
                <?php if (!$pdsa || (empty($pdsa['plan']) && empty($pdsa['do']) && empty($pdsa['study']) && empty($pdsa['act']))): ?>
                    <p class="text-muted mt-2 mb-0"><i class="bi bi-pencil me-1"></i> Belum ada data PDSA. <a href="<?= site_url('siimut/trias-mutu/pdsa?dokumen_id=' . $d['id']) ?>">Isi sekarang</a></p>
                <?php endif; ?>
            </div>

            <div class="border rounded p-3 mb-4">
                <h6 class="fw-bold border-bottom pb-2 mb-3"><i class="bi bi-pen me-2"></i>D. Tanda Tangan</h6>
                <div class="row">
                    <div class="col-md-4 text-center">
                        <p class="mb-5"><strong>Disusun oleh:</strong></p>
                        <p class="mt-5 pt-4 border-top d-inline-block" style="min-width:200px">
                            <?= esc($ttd['disusun_oleh'] ?? '(....................)') ?>
                            <br><button class="btn btn-sm btn-outline-primary mt-2" onclick="editTtd()"><i class="bi bi-pen"></i></button>
                        </p>
                    </div>
                    <div class="col-md-4 text-center">
                        <p class="mb-5"><strong>Mengetahui:</strong></p>
                        <p class="mt-5 pt-4 border-top d-inline-block" style="min-width:200px">
                            <?= esc($ttd['mengetahui'] ?? '(....................)') ?>
                        </p>
                    </div>
                    <div class="col-md-4 text-center">
                        <p class="mb-5"><strong>Kepala Unit:</strong></p>
                        <p class="mt-5 pt-4 border-top d-inline-block" style="min-width:200px">
                            <?= esc($ttd['kepala_unit'] ?? '(....................)') ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-footer">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <?php if ($d['status'] === 'final'): ?>
                        <div class="d-flex align-items-center gap-3">
                            <span class="badge bg-success fs-6 px-3 py-2">
                                <i class="bi bi-check-circle me-1"></i> FINAL
                            </span>
                            <small class="text-muted">
                                Tanggal: <?= date('d/m/Y H:i', strtotime($d['final_at'])) ?> |
                                Oleh: <?= esc($d['final_by_name'] ?? '-') ?>
                            </small>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="col-md-6 text-md-end">
                    <div class="btn-group">
                        <?php if ($d['status'] !== 'final'): ?>
                            <button class="btn btn-warning" id="btnSimpanDraft"><i class="bi bi-save me-1"></i> Simpan Draft</button>
                            <button class="btn btn-success" id="btnFinalisasi"><i class="bi bi-check2-circle me-1"></i> Finalisasi</button>
                        <?php endif; ?>
                        <a href="<?= site_url('siimut/trias-mutu/cetak-pdf?dokumen_id=' . $d['id']) ?>" class="btn btn-danger" target="_blank"><i class="bi bi-file-pdf me-1"></i> Cetak PDF</a>
                        <button class="btn btn-info" id="btnExportWord"><i class="bi bi-file-word me-1"></i> Export Word</button>
                        <?php if (in_array(session('user_role'), ['ADMINISTRATOR', 'KOMITE'])): ?>
                            <button class="btn btn-outline-danger" id="btnDeleteDokumen"><i class="bi bi-trash me-1"></i> Hapus</button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalTtd" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title">Edit Tanda Tangan</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Disusun oleh</label>
                        <input type="text" class="form-control" id="editDisusunOleh" value="<?= esc($ttd['disusun_oleh'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mengetahui</label>
                        <input type="text" class="form-control" id="editMengetahui" value="<?= esc($ttd['mengetahui'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kepala Unit</label>
                        <input type="text" class="form-control" id="editKepalaUnit" value="<?= esc($ttd['kepala_unit'] ?? '') ?>">
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" id="btnSaveTtd">Simpan</button>
                </div>
            </div>
        </div>
    </div>
<?php } ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    var dokumenId = <?= json_encode($selected['id'] ?? null) ?>;
    var chart = null;

    <?php if ($selected && $measurement): ?>
    renderChart(<?= json_encode($measurement['bulanan'] ?? []) ?>, <?= json_encode($measurement['indicator']->indicator_units ?? '') ?>);
    $('#unit_id').val(<?= json_encode((string)($selected['unit_id'] ?? '')) ?>);
    $('#category_id').val(<?= json_encode((string)($selected['indicator_category_id'] ?? '4')) ?>);
    $('#tahun').val(<?= json_encode((string)($selected['tahun'] ?? '')) ?>);
    $('#triwulan').val(<?= json_encode((string)($selected['triwulan'] ?? '1')) ?>);
    <?php endif; ?>

    $('#unit_id, #category_id, #tahun').on('change', function() {
        loadIndicators();
    });

    function loadIndicators() {
        var unitId = $('#unit_id').val();
        var categoryId = $('#category_id').val();
        var tahun = $('#tahun').val();
        if (!unitId || !categoryId || !tahun) return;

        $.ajax({
            url: '<?= site_url('siimut/trias-mutu/get-indicators') ?>',
            method: 'POST',
            data: { unit_id: unitId, category_id: categoryId, tahun: tahun },
            success: function(indicators) {
                var sel = $('#indicator_id');
                sel.empty().append('<option value="">Indikator...</option>');
                indicators.forEach(function(ind) {
                    sel.append('<option value="' + ind.indicator_id + '">' + ind.indicator_element + '</option>');
                });
                sel.show();
            }
        });
    }

    $('#btnLoad').on('click', function() {
        var unitId = $('#unit_id').val();
        var categoryId = $('#category_id').val();
        var indicatorId = $('#indicator_id').val();
        var tahun = $('#tahun').val();
        var triwulan = $('#triwulan').val();

        if (!unitId || !categoryId || !indicatorId || !tahun || !triwulan) {
            alert('Silakan pilih semua filter');
            return;
        }

        $.ajax({
            url: '<?= site_url('siimut/trias-mutu/get-or-create-dokumen') ?>',
            method: 'POST',
            data: { unit_id: unitId, category_id: categoryId, indicator_id: indicatorId, tahun: tahun, triwulan: triwulan },
            success: function(res) {
                if (res.dokumen_id) {
                    window.location.href = '<?= site_url('siimut/trias-mutu/cetak') ?>?dokumen_id=' + res.dokumen_id;
                }
            }
        });
    });

    function renderChart(data, unit) {
        if (!data || !data.length) return;
        var bulanNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        var labels = [], values = [];
        data.forEach(function(b) {
            labels.push(bulanNames[b.bulan - 1] || 'B' + b.bulan);
            values.push(b.nilai);
        });
        var ctx = document.getElementById('bulananChart');
        if (!ctx) return;
        chart = new Chart(ctx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Nilai Bulanan (' + (unit || '') + ')',
                    data: values,
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
        });
    }

    $('#btnSimpanDraft').on('click', function() {
        if (!dokumenId) return;
        $.ajax({
            url: '<?= site_url('siimut/trias-mutu/simpan-draft') ?>',
            method: 'POST',
            data: { dokumen_id: dokumenId },
            success: function(res) { if (res.success) alert('Dokumen disimpan sebagai Draft'); else alert(res.message); }
        });
    });

    $('#btnFinalisasi').on('click', function() {
        if (!confirm('Yakin akan mem-final dokumen ini? Dokumen tidak dapat diedit lagi.')) return;
        $.ajax({
            url: '<?= site_url('siimut/trias-mutu/finalisasi') ?>',
            method: 'POST',
            data: { dokumen_id: dokumenId },
            success: function(res) { if (res.success) { alert(res.message); location.reload(); } else alert(res.message); }
        });
    });

    $('#btnDeleteDokumen').on('click', function() {
        if (!confirm('Hapus dokumen ini? Semua data analisis dan PDSA akan ikut terhapus.')) return;
        $.ajax({
            url: '<?= site_url('siimut/trias-mutu/delete-dokumen') ?>',
            method: 'POST',
            data: { dokumen_id: dokumenId },
            success: function(res) { if (res.success) { alert('Dokumen berhasil dihapus'); window.location.href = '<?= site_url('siimut/trias-mutu') ?>'; } else alert(res.message); }
        });
    });

    $('#btnExportWord').on('click', function() {
        var content = document.getElementById('cetakContent').innerHTML;
        var style = '<style>body{font-family:Times New Roman,serif;font-size:12pt;}table{border-collapse:collapse;width:100%;margin-bottom:10px;}th,td{border:1px solid #000;padding:4px 8px;}.border{border:1px solid #ddd;}.rounded{border-radius:4px;}.p-3{padding:15px;}.mb-4{margin-bottom:20px;}.bg-light{background:#f8f9fa;}.fw-bold{font-weight:bold;}.text-center{text-align:center;}</style>';
        var html = '<html><head><meta charset="utf-8">' + style + '</head><body>' + content + '</body></html>';
        var blob = new Blob([html], {type: 'application/msword'});
        var a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = 'TriasMutu_<?= ($selected['id'] ?? 0) ?>_TW<?= ($selected['triwulan'] ?? '') ?>_<?= ($selected['tahun'] ?? '') ?>.doc';
        a.click();
    });
});

function editTtd() {
    $('#modalTtd').modal('show');
}

$(document).on('click', '#btnSaveTtd', function() {
    var dokumenId = <?= json_encode($selected['id'] ?? null) ?>;
    $.ajax({
        url: '<?= site_url('siimut/trias-mutu/save-ttd') ?>',
        method: 'POST',
        data: {
            dokumen_id: dokumenId,
            disusun_oleh: $('#editDisusunOleh').val(),
            mengetahui: $('#editMengetahui').val(),
            kepala_unit: $('#editKepalaUnit').val()
        },
        success: function(res) {
            if (res.success) { alert('Tanda tangan disimpan'); location.reload(); }
        }
    });
});
</script>
