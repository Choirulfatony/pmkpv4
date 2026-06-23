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
                            <select class="form-select form-select-sm" id="category_id" autocomplete="off">
                                <option value="">Pilih Jenis...</option>
                                <option value="4">INM</option>
                                <option value="5">IMPRS</option>
                                <option value="6">IMP Unit</option>
                            </select>
                        </div>
                        <div class="col-md-5">
                            <select class="form-select form-select-sm" id="unit_id" autocomplete="off">
                                <option value="">Unit...</option>
                                <?php foreach ($units as $u): ?>
                                    <option value="<?= $u['department_id'] ?>"><?= esc($u['department_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select form-select-sm" id="tahun" autocomplete="off">
                                <?php foreach ($tahunList as $t): ?>
                                    <option value="<?= $t ?>"><?= $t ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select form-select-sm" id="triwulan" autocomplete="off">
                                <option value="1">TW I</option>
                                <option value="2">TW II</option>
                                <option value="3">TW III</option>
                                <option value="4">TW IV</option>
                            </select>
                        </div>
                    </div>
                    <div class="row g-2 mt-1 justify-content-end">
                        <div class="col-md-10">
                            <select class="form-select form-select-sm" id="indicator_id" autocomplete="off">
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
        <?php if ($selected): renderCetakPreview($selected, $measurement, $categoryLabels, $triwulanLabels, $numdenum); ?>
        <?php else: ?>
            <div class="text-center text-muted py-5">
                <i class="bi bi-file-earmark-text fs-1 d-block mb-3"></i>
                <p>Pilih unit, jenis indikator, indikator, triwulan, dan tahun, lalu klik tombol cari untuk menampilkan preview dokumen.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
function renderCetakPreview($d, $m, $categoryLabels, $triwulanLabels, $numdenum)
{
    $ind = $m['indicator'] ?? null;
    $analisis = $d['analisis'] ?? [];
    $pdsa = $d['pdsa'] ?? null;
    $ttd = $d['ttd'] ?? null;
    $numTeks = $numdenum['numerator'] ?? '';
    $denTeks = $numdenum['denominator'] ?? '';
    $bulanNames = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
?>
    <div class="card shadow-sm mb-4" id="dokumenPreview">
        <div class="card-header position-relative">
            <span><i class="bi bi-file-earmark-text me-2"></i>Preview Dokumen</span>
            <?php if ($d['status'] === 'final'): ?>
                <span class="badge bg-success fs-6 px-3 py-2 position-absolute end-0 top-50 translate-middle-y me-2">
                    <i class="bi bi-check-circle me-1"></i> FINAL
                </span>
            <?php else: ?>
                <span class="badge bg-warning text-dark fs-6 px-3 py-2 position-absolute end-0 top-50 translate-middle-y me-2">
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

<style>
.table-cetak {
    font-size: 14px;
}
.table-cetak th,
.table-cetak td {
    border: 1px solid #666 !important;
}
.table-cetak th {
    font-weight: bold;
}
</style>
            <div class="border rounded p-3 mb-4">
                <h5 class="fw-bold mb-2 text-uppercase">A. PENGUKURAN INDIKATOR (STRUKTUR / PROSES / OUTCOME)</h5>
                <hr class="mt-0 mb-3">
                <div class="table-responsive mb-3">
                    <table class="table table-bordered align-middle text-center table-cetak">
                        <tbody>
                            <tr>
                                <th width="20%" colspan="2" class="text-start" scope="row">Judul Indikator</th>
                                <td class="text-start" colspan="9"><?= esc($ind->indicator_element ?? '-') ?></td>
                            </tr>
                            <tr>
                                <th class="text-start" colspan="2" scope="row">Numerator</th>
                                <td class="text-start" colspan="9"><?= esc($numTeks ?: $ind->indicator_element ?? '') ?></td>
                            </tr>
                            <tr>
                                <th class="text-start" colspan="2" scope="row">Denominator</th>
                                <td class="text-start" colspan="9"><?= esc($denTeks ?: 'Total ' . ($ind->indicator_element ?? '')) ?></td>
                            </tr>
                            <tr>
                                <th class="text-start" colspan="2" scope="row">Formula</th>
                                <td class="text-center" colspan="9">
                                    <div class="text-center p-3" style="background:#f8f9fa;border-radius:4px;font-family:'Times New Roman',serif;">
                                        <div style="display:inline-flex;align-items:center;gap:8px;font-size:1.1rem;">
                                            <span>Hasil Capaian =</span>
                                            <span style="display:inline-flex;flex-direction:column;align-items:center;">
                                                <span style="border-bottom:2px solid #000;padding:2px 12px;font-style:italic;"><?= esc($numTeks ?: $ind->indicator_element ?? '') ?> (<?= esc($ind->indicator_units ?? '') ?>)</span>
                                                <span style="padding:2px 12px;font-style:italic;"><?= esc($denTeks ?: 'Total ' . ($ind->indicator_element ?? '')) ?> (<?= esc($ind->indicator_units ?? '') ?>)</span>
                                            </span>
                                            <span>&times; 100%</span>
                                        </div>
                                        <div class="mt-2 small text-muted">Standar: &gt;= <?= esc($ind->indicator_target ?? '0') ?> <?= esc($ind->indicator_units ?? '%') ?></div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th class="text-start" colspan="2" scope="row">Standar</th>
                                <td class="text-start" colspan="9">
                                    <?= esc($ind->indicator_target ?? '0') ?><?= esc($ind->indicator_units ?? '%') ?>
                                </td>
                            </tr>
                            <tr>
                                <th rowspan="2" colspan="2" class="text-center" scope="row">
                                    Hasil<br>Pengukuran
                                </th>
                                <th colspan="6">Bulan</th>
                                <th rowspan="2" colspan="2">
                                    Rata-Rata<br>
                                    Triwulan <?= $triwulanLabels[$d['triwulan']] ?? '' ?><br>
                                    Tahun <?= $d['tahun'] ?? '' ?>
                                </th>
                            </tr>
                            <tr>
                                <?php foreach (($m['bulanan'] ?? []) as $b): ?>
                                    <th colspan="2" scope="row">
                                        <?= $bulanNames[$b['bulan']] ?? 'B' . $b['bulan'] ?>
                                    </th>
                                <?php endforeach; ?>
                            </tr>
                            <?php
                            $totalNum = 0; $totalDenum = 0; $capSum = 0; $capCount = 0;
                            foreach (($m['bulanan'] ?? []) as $b):
                                $totalNum += $b['num'];
                                $totalDenum += $b['denum'];
                                if ($b['nilai'] !== null) { $capSum += $b['nilai']; $capCount++; }
                            endforeach;
                            $avgNum = $totalNum / max(count($m['bulanan'] ?? []), 1);
                            $avgDenum = $totalDenum / max(count($m['bulanan'] ?? []), 1);
                            $avgCap = $capCount > 0 ? $capSum / $capCount : 0;
                            ?>
                            <tr>
                                <th class="text-start" scope="row">Num</th><th rowspan="2" style="vertical-align:middle;width:40px;text-align:center;"><div style="display:inline-block;writing-mode:vertical-rl;transform:rotate(180deg);font-weight:bold;">Capaian</div></th>
                                <?php foreach (($m['bulanan'] ?? []) as $b): ?>
                                    <td><?= $b['num'] ?></td>
                                    <td rowspan="2">
                                        <?= $b['nilai'] !== null ? number_format($b['nilai'], 0) . '%' : '-' ?>
                                    </td>
                                <?php endforeach; ?>
                                <td><?= number_format($avgNum, 0) ?></td>
                                <td rowspan="2">
                                    <?= number_format($avgCap, 0) ?>%
                                </td>
                            </tr>
                            <tr>
                                <th class="text-start" scope="row">Denum</th>
                                <?php foreach (($m['bulanan'] ?? []) as $b): ?>
                                    <td><?= $b['denum'] ?></td>
                                <?php endforeach; ?>
                                <td><?= number_format($avgDenum, 0) ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="border rounded p-3 mb-4">
                <h6 class="fw-bold border-bottom pb-2 mb-3"><i class="bi bi-bar-chart me-2"></i>Grafik</h6>
                <div class="chart-container"><canvas id="lineChart"></canvas></div>
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
Chart.register({
    id: 'customCetak',
    afterDraw: function(c) {
        var cfg = c.config.options.custom || {};
        var tgt = parseFloat(cfg.target) || 0;
        var unit = cfg.unit || '%';
        var ctx = c.ctx;
        var meta = c.getDatasetMeta(0);
        if (meta && meta.data) {
            meta.data.forEach(function(pt, i) {
                var v = c.data.datasets[0].data[i];
                if (v === null || v === undefined) return;
                ctx.fillStyle = '#333';
                ctx.font = '10px Arial';
                ctx.textAlign = 'center';
                ctx.fillText(v + '%', pt.x, pt.y - 10);
            });
        }
        var yTgt = c.scales.y.getPixelForValue(tgt);
        ctx.save();
        ctx.setLineDash([6, 4]);
        ctx.strokeStyle = '#e74c3c';
        ctx.lineWidth = 2;
        ctx.beginPath();
        ctx.moveTo(c.chartArea.left, yTgt);
        ctx.lineTo(c.chartArea.right, yTgt);
        ctx.stroke();
        ctx.restore();
        ctx.fillStyle = '#e74c3c';
        ctx.font = 'bold 10px Arial';
        ctx.textAlign = 'right';
        ctx.fillText('Standar: ' + tgt + unit, c.chartArea.right - 4, yTgt - 6);
    }
});

$(document).ready(function() {
    var dokumenId = <?= json_encode($selected['id'] ?? null) ?>;
    var userRole = <?= json_encode($userRole) ?>;
    var isKendali = !['ADMINISTRATOR', 'KOMITE'].includes(userRole);
    var chart = null;

    if (isKendali) {
        var firstReal = $('#unit_id option:first').next();
        if (firstReal.length) {
            $('#unit_id').val(firstReal.val());
        }
        $('#unit_id').prop('disabled', true);
        $('#unit_id').select2({ theme: 'bootstrap-5', width: '100%', disabled: true });
    } else {
        $('#unit_id').select2({ theme: 'bootstrap-5', width: '100%', placeholder: 'Unit...', allowClear: true });
    }
    $('#indicator_id').select2({ theme: 'bootstrap-5', width: '100%', placeholder: 'Indikator...', allowClear: true });

    <?php if ($selected && $measurement): ?>
    $('#unit_id').val(<?= json_encode((string)($selected['unit_id'] ?? '')) ?>);
    $('#category_id').val(<?= json_encode((string)($selected['indicator_category_id'] ?? '')) ?>);
    $('#tahun').val(<?= json_encode((string)($selected['tahun'] ?? '')) ?>);
    $('#triwulan').val(<?= json_encode((string)($selected['triwulan'] ?? '1')) ?>);
    loadIndicators(function() {
        $('#indicator_id').val(<?= json_encode((string)($selected['indicator_id'] ?? '')) ?>).trigger('change');
    });
    renderChart(<?= json_encode($measurement['bulanan'] ?? []) ?>, <?= json_encode($measurement['indicator']->indicator_units ?? '') ?>, <?= json_encode($measurement['target'] ?? 0) ?>, <?= json_encode($measurement['nilai_triwulan'] ?? null) ?>);
    <?php endif; ?>

    $('#unit_id, #category_id, #tahun').on('change', function() {
        loadIndicators();
    });

    function loadIndicators(callback) {
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
                sel.select2('destroy').select2({ theme: 'bootstrap-5', width: '100%', placeholder: 'Indikator...', allowClear: true });
                if (typeof callback === 'function') callback(indicators);
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
            toastWarning('Silakan pilih semua filter');
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

    function renderChart(data, unit, target, avgTriwulan) {
        if (!data || !data.length) return;
        var bulanNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        var labels = [], values = [];
        data.forEach(function(b) {
            labels.push(bulanNames[b.bulan - 1] || 'B' + b.bulan);
            values.push(b.nilai);
        });
        var maxVal = Math.max(...values, target, avgTriwulan || 0) * 1.3 || 100;
        var ctx = document.getElementById('lineChart');
        if (!ctx) return;
        if (chart) chart.destroy();

        var datasets = [{
            label: 'Nilai Bulanan (' + (unit || '') + ')',
            data: values,
            backgroundColor: 'rgba(54, 162, 235, 0.2)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 2,
            fill: true,
            tension: 0.3,
            pointBackgroundColor: 'rgba(54, 162, 235, 1)',
            pointRadius: 4
        }];

        if (avgTriwulan !== null && avgTriwulan !== undefined && !isNaN(avgTriwulan)) {
            var avgData = labels.map(function() { return avgTriwulan; });
            datasets.push({
                label: 'Rata-Rata Triwulan: ' + (avgTriwulan % 1 === 0 ? avgTriwulan : avgTriwulan.toFixed(2)) + (unit || '%'),
                data: avgData,
                borderColor: '#3498db',
                borderWidth: 2,
                borderDash: [4, 4],
                fill: false,
                tension: 0,
                pointRadius: 0,
                pointHitRadius: 0
            });
        }

        chart = new Chart(ctx.getContext('2d'), {
            type: 'line',
            data: { labels: labels, datasets: datasets },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                custom: { target: target, unit: (unit || '%'), avgTriwulan: avgTriwulan },
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, max: maxVal, title: { display: true, text: 'Capaian (%)' } } }
            }
        });
    }

    $('#btnSimpanDraft').on('click', function() {
        if (!dokumenId) return;
        $.ajax({
            url: '<?= site_url('siimut/trias-mutu/simpan-draft') ?>',
            method: 'POST',
            data: { dokumen_id: dokumenId },
            success: function(res) { if (res.success) toastSuccess('Dokumen disimpan sebagai Draft'); else toastError(res.message); }
        });
    });

    $('#btnFinalisasi').on('click', function() {
        if (!confirm('Yakin akan mem-final dokumen ini? Dokumen tidak dapat diedit lagi.')) return;
        $.ajax({
            url: '<?= site_url('siimut/trias-mutu/finalisasi') ?>',
            method: 'POST',
            data: { dokumen_id: dokumenId },
            success: function(res) { if (res.success) { toastSuccess(res.message); location.reload(); } else toastError(res.message); }
        });
    });

    $('#btnDeleteDokumen').on('click', function() {
        if (!confirm('Hapus dokumen ini? Semua data analisis dan PDSA akan ikut terhapus.')) return;
        $.ajax({
            url: '<?= site_url('siimut/trias-mutu/delete-dokumen') ?>',
            method: 'POST',
            data: { dokumen_id: dokumenId },
            success: function(res) { if (res.success) { toastSuccess('Dokumen berhasil dihapus'); window.location.href = '<?= site_url('siimut/trias-mutu') ?>'; } else toastError(res.message); }
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
            if (res.success) { toastSuccess('Tanda tangan disimpan'); location.reload(); }
        }
    });
});
</script>
