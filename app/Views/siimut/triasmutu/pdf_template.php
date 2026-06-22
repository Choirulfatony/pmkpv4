<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Trias Mutu - <?= esc($dokumen['unit_name'] ?? '') ?></title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; font-size: 12pt; line-height: 1.5; color: #000; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header h2 { margin: 0; font-size: 16pt; }
        .header p { margin: 2px 0; font-size: 11pt; }
        h4 { font-size: 13pt; margin: 15px 0 8px; border-bottom: 1px solid #333; padding-bottom: 4px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; font-size: 11pt; }
        th, td { border: 1px solid #000; padding: 4px 6px; text-align: left; }
        th { background-color: #eaeaea; }
        .info-table th, .info-table td { border: none; padding: 2px 4px; }
        .info-table { width: auto; }
        .ttd { margin-top: 30px; }
        .ttd .col { display: inline-block; width: 30%; text-align: center; vertical-align: top; }
        .ttd .sign-line { margin-top: 50px; padding-top: 4px; border-top: 1px solid #000; display: inline-block; min-width: 180px; }
        .footer { margin-top: 30px; font-size: 10pt; text-align: center; color: #666; border-top: 1px solid #ccc; padding-top: 8px; }
        .badge { display: inline-block; padding: 2px 8px; font-size: 10pt; font-weight: bold; }
        .badge-success { background: #28a745; color: #fff; }
        .badge-danger { background: #dc3545; color: #fff; }
        .badge-secondary { background: #6c757d; color: #fff; }
        .page-break { page-break-before: always; }
    </style>
</head>
<body>
    <?php
    $categoryLabels = [4 => 'INM', 5 => 'IMPRS', 6 => 'IMPUNIT'];
    $triwulanLabels = ['', 'I', 'II', 'III', 'IV'];
    $ind = $measurement['indicator'] ?? null;
    $analisis = $dokumen['analisis'] ?? [];
    $pdsa = $dokumen['pdsa'] ?? null;
    $ttd = $dokumen['ttd'] ?? null;
    $bulanNames = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    ?>

    <div class="header">
        <h2>LAPORAN TRIAS MUTU</h2>
        <p>Rumah Sakit Soedono Madiun</p>
        <p>Periode: Triwulan <?= $triwulanLabels[$dokumen['triwulan']] ?? '-' ?> Tahun <?= $dokumen['tahun'] ?? '-' ?></p>
        <p><strong>Status: <?= $dokumen['status'] === 'final' ? 'FINAL' : 'DRAFT' ?></strong></p>
    </div>

    <h4>1. Informasi Dokumen</h4>
    <table class="info-table">
        <tr><td style="width:140px"><strong>Nama Unit</strong></td><td>: <?= esc($dokumen['unit_name'] ?? '-') ?></td></tr>
        <tr><td><strong>Jenis Indikator</strong></td><td>: <?= $categoryLabels[$dokumen['indicator_category_id']] ?? '-' ?></td></tr>
        <tr><td><strong>Nama Indikator</strong></td><td>: <?= esc($ind->indicator_element ?? '-') ?></td></tr>
        <tr><td><strong>Periode</strong></td><td>: Triwulan <?= $triwulanLabels[$dokumen['triwulan']] ?? '-' ?> Tahun <?= $dokumen['tahun'] ?? '-' ?></td></tr>
        <tr><td><strong>Status</strong></td><td>: <?= $dokumen['status'] === 'final' ? 'Final' : 'Draft' ?></td></tr>
    </table>

    <h4>2. Pengukuran Indikator</h4>
    <table class="info-table">
        <tr><td style="width:140px"><strong>Nama Indikator</strong></td><td>: <?= esc($ind->indicator_element ?? '-') ?></td></tr>
        <tr><td><strong>Numerator</strong></td><td>: <?= $measurement['total_num'] ?? 0 ?></td></tr>
        <tr><td><strong>Denominator</strong></td><td>: <?= $measurement['total_denum'] ?? 0 ?></td></tr>
        <tr><td><strong>Formula</strong></td><td>: (Numerator / Denominator) x Faktor</td></tr>
        <tr><td><strong>Standar / Target</strong></td><td>: <?= ($ind->indicator_target ?? '0') . ' ' . ($ind->indicator_units ?? '') ?></td></tr>
        <tr>
            <td><strong>Nilai Triwulan</strong></td>
            <td>: <strong><?= $measurement['nilai_triwulan'] !== null ? $measurement['nilai_triwulan'] . ' ' . ($ind->indicator_units ?? '') : 'Tidak ada data' ?></strong></td>
        </tr>
        <tr>
            <td><strong>Ketercapaian</strong></td>
            <td>:
                <?php if ($measurement['nilai_triwulan'] !== null): ?>
                    <?php if ((float) $measurement['nilai_triwulan'] >= (float) ($ind->indicator_target ?? 0)): ?>
                        <span>Tercapai</span>
                    <?php else: ?>
                        <span>Belum Tercapai (Gap: <?= round((float) ($ind->indicator_target ?? 0) - (float) $measurement['nilai_triwulan'], 2) ?>)</span>
                    <?php endif; ?>
                <?php else: ?>
                    <span>Tidak ada data</span>
                <?php endif; ?>
            </td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th>Bulan</th>
                <th>Numerator</th>
                <th>Denominator</th>
                <th>Nilai</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach (($measurement['bulanan'] ?? []) as $b): ?>
                <tr>
                    <td><?= $bulanNames[$b['bulan']] ?? 'Bulan ' . $b['bulan'] ?></td>
                    <td style="text-align:center"><?= $b['num'] ?></td>
                    <td style="text-align:center"><?= $b['denum'] ?></td>
                    <td style="text-align:center"><?= $b['nilai'] !== null ? $b['nilai'] . ' ' . ($ind->indicator_units ?? '') : '-' ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr style="font-weight:bold; background:#f0f0f0;">
                <td>Total Triwulan <?= $triwulanLabels[$dokumen['triwulan']] ?? '' ?></td>
                <td style="text-align:center"><?= $measurement['total_num'] ?? 0 ?></td>
                <td style="text-align:center"><?= $measurement['total_denum'] ?? 0 ?></td>
                <td style="text-align:center"><?= $measurement['nilai_triwulan'] !== null ? $measurement['nilai_triwulan'] . ' ' . ($ind->indicator_units ?? '') : '-' ?></td>
            </tr>
        </tfoot>
    </table>

    <p><strong>Analisis:</strong>
        <?php if ($measurement['nilai_triwulan'] !== null): ?>
            Nilai Triwulan <?= $triwulanLabels[$dokumen['triwulan']] ?> sebesar
            <strong><?= $measurement['nilai_triwulan'] . ' ' . ($ind->indicator_units ?? '') ?></strong>
            <?php if ((float) $measurement['nilai_triwulan'] >= (float) ($ind->indicator_target ?? 0)): ?>
                telah memenuhi target
            <?php else: ?>
                belum memenuhi target <?= ($ind->indicator_target ?? '0') . ' ' . ($ind->indicator_units ?? '') ?>
            <?php endif; ?>.
        <?php else: ?>
            Belum ada data pengukuran untuk periode ini.
        <?php endif; ?>
    </p>

    <div class="page-break"></div>

    <h4>3. Analisis Penyebab Masalah</h4>
    <?php if (!empty($analisis)): ?>
        <p><strong>Permasalahan:</strong> <?= nl2br(esc($analisis[0]['permasalahan'] ?? '-')) ?></p>
        <table>
            <thead>
                <tr>
                    <th style="width:40px">No</th>
                    <th style="width:130px">Kategori</th>
                    <th>Penyebab</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; foreach ($analisis as $a): ?>
                    <tr>
                        <td style="text-align:center"><?= $no++ ?></td>
                        <td><?= esc($a['kategori']) ?></td>
                        <td><?= nl2br(esc($a['penyebab'])) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p><em>Belum ada data analisis penyebab.</em></p>
    <?php endif; ?>

    <h4>4. Siklus PDSA</h4>
    <table>
        <tr>
            <td style="width:15%; font-weight:bold; vertical-align:top;">Plan</td>
            <td><?= nl2br(esc($pdsa['plan'] ?? '<em>Belum diisi</em>')) ?></td>
        </tr>
        <tr>
            <td style="font-weight:bold; vertical-align:top;">Do</td>
            <td><?= nl2br(esc($pdsa['do'] ?? '<em>Belum diisi</em>')) ?></td>
        </tr>
        <tr>
            <td style="font-weight:bold; vertical-align:top;">Study</td>
            <td><?= nl2br(esc($pdsa['study'] ?? '<em>Belum diisi</em>')) ?></td>
        </tr>
        <tr>
            <td style="font-weight:bold; vertical-align:top;">Act</td>
            <td><?= nl2br(esc($pdsa['act'] ?? '<em>Belum diisi</em>')) ?></td>
        </tr>
    </table>

    <h4>5. Tanda Tangan</h4>
    <div class="ttd">
        <table>
            <tr>
                <td style="text-align:center; width:33%; border:none;">
                    <p><strong>Disusun oleh:</strong></p>
                    <br><br><br>
                    <div class="sign-line"><?= esc($ttd['disusun_oleh'] ?? '(....................)') ?></div>
                </td>
                <td style="text-align:center; width:33%; border:none;">
                    <p><strong>Mengetahui:</strong></p>
                    <br><br><br>
                    <div class="sign-line"><?= esc($ttd['mengetahui'] ?? '(....................)') ?></div>
                </td>
                <td style="text-align:center; width:33%; border:none;">
                    <p><strong>Kepala Unit:</strong></p>
                    <br><br><br>
                    <div class="sign-line"><?= esc($ttd['kepala_unit'] ?? '(....................)') ?></div>
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <p>Dokumen ini dicetak pada <?= date('d/m/Y H:i') ?> melalui Sistem Informasi Indikator Mutu (SIIMUT) - RSSM</p>
    </div>
</body>
</html>
