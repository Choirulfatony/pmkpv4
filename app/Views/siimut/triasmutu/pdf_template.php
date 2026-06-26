<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Trias Mutu - <?= esc($dokumen['unit_name'] ?? '') ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.0;
            color: #000;
            margin: 1.27cm;
        }

        p {
            margin: 0 0 2px 0;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-table td {
            border: none;
        }

        .logo {
            width: 70px;
            vertical-align: middle;
        }

        .logo img {
            width: 65px;
            height: auto;
            display: block;
        }

        .spacer {
            width: 70px;
        }

        .header-text {
            text-align: center;
            vertical-align: middle;
        }

        .header-text div {
            line-height: 1.0;
            margin: 0;
        }

        .provinsi {
            font: normal 12pt Arial;
        }

        .dinas {
            font: normal 12pt Arial;
        }

        .rs {
            font: bold 16pt Arial;
            line-height: 1;
        }

        .alamat {
            font: 10pt Arial;
            white-space: nowrap;
        }

        hr {
            border: 0;
            border-top: 2px solid #000;
            margin-top: 6px;
            margin-bottom: 20px;
        }

        .judul {
            text-align: center;
            font-weight: bold;
            font-size: 14pt;
            text-transform: uppercase;
            line-height: 1.5;
            margin-top: 18px;
            margin-bottom: 20px;
        }

        .section-title {
            font-size: 12pt;
            font-weight: bold;
            margin: 14px 0 6px;
        }

        .subsection-title {
            font-size: 11pt;
            font-weight: bold;
            margin: 10px 0 4px;
        }

        .rata-kanan {
            text-align: right;
        }

        .rata-tengah {
            text-align: center;
        }

        .bold {
            font-weight: bold;
        }

        .ttd {
            text-align: right;
            margin-top: 40px;
        }

        .report-table {
            width: 100%;
            border-collapse: collapse;
            font-family: 'Times New Roman', serif;
            font-size: 11pt;
        }

        .report-table th,
        .report-table td {
            border: 1px solid #000;
            padding: 5px;
            vertical-align: top;
        }

        .report-table th {
            text-align: center;
            font-weight: bold;
            vertical-align: middle;
        }

        .report-table tr {
            page-break-inside: avoid;
        }

        .text-start {
            text-align: left;
        }

        .text-center {
            text-align: center;
        }

        .align-middle {
            vertical-align: middle;
        }

        h4 {
            font-size: 12pt;
            font-weight: bold;
            margin: 14px 0 6px;
        }

        .page-break {
            page-break-before: always;
        }
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
    $bulanNames = [
        '',
        'Januari',
        'Februari',
        'Maret',
        'April',
        'Mei',
        'Juni',
        'Juli',
        'Agustus',
        'September',
        'Oktober',
        'November',
        'Desember'
    ];
    $bulanSingkat = [
        '',
        'JAN',
        'FEB',
        'MAR',
        'APR',
        'MEI',
        'JUN',
        'JUL',
        'AGS',
        'SEP',
        'OKT',
        'NOV',
        'DES'
    ];
    $numTeks = $numdenum['numerator'] ?? '';
    $denTeks = $numdenum['denominator'] ?? '';
    $triwulanRomawi = $triwulanLabels[$dokumen['triwulan']] ?? 'I';
    $tahun = $dokumen['tahun'] ?? date('Y');
    $targetNilai = $ind->indicator_target ?? 0;
    $satuan = $ind->indicator_units ?? '%';
    $faktor = $ind->indicator_factors ?? 100;
    $capaians = [];
    foreach (($measurement['bulanan'] ?? []) as $b) {
        $capaians[] = $b['nilai'];
    }
    $rataCapaian = $measurement['nilai_triwulan'];
    $targetFloat = (float) $targetNilai;
    $tercapai = $rataCapaian !== null && (float) $rataCapaian >= $targetFloat;
    $gap = $rataCapaian !== null ? round($targetFloat - (float) $rataCapaian, 2) : null;
    $chartTitle = $ind->indicator_element ?? 'Indikator';
    $bulanLabels = [];
    for ($i = 0; $i < 3; $i++) {
        $bulanLabels[] = $bulanSingkat[$i + 1] ?? 'BLN' . ($i + 1);
    }
    ?>

    <table class="header-table">
        <tr>
            <td class="logo">
                <?php if ($logoSrc): ?>
                    <img src="<?= $logoSrc ?>" alt="Logo Jatim">
                <?php endif; ?>
            </td>
            <td class="header-text">
                <div class="provinsi">PEMERINTAH PROVINSI JAWA TIMUR</div>
                <div class="dinas">DINAS KESEHATAN</div>
                <div class="rs">RUMAH SAKIT UMUM DAERAH dr. SOEDONO</div>
                <div class="alamat">Jalan Dr. Sutomo Nomor 59, Kartoharjo, Kota Madiun 63116</div>
                <div class="alamat">Telepon (0351) 464325, Laman rssoedono.jatimprov.go.id, Pos-el rsu_soedonomdn@jatimprov.go.id</div>
            </td>
            <td class="spacer">&nbsp;</td>
        </tr>
    </table>

    <hr>

    <div class="judul">UPAYA PERBAIKAN BERKELANJUTAN<br>MELALUI IMPLEMENTASI TRIAS MUTU</div>

    <p><strong>Nama Unit</strong> : <?= esc($dokumen['unit_name'] ?? '-') ?></p>

    <h4>A. PENGUKURAN INDIKATOR (STRUKTUR / PROSES / OUTCOME)</h4>

    <table class="report-table">
        <tbody>
            <tr>
                <th width="20%" colspan="2" class="text-start" scope="row">Judul Indikator</th>
                <td class="text-start" colspan="8"><?= esc($ind->indicator_element ?? '-') ?></td>
            </tr>
            <tr>
                <th class="text-start" colspan="2" scope="row">Numerator</th>
                <td class="text-start" colspan="8"><?= esc($numTeks ?: $measurement['total_num'] ?? '0') ?></td>
            </tr>
            <tr>
                <th class="text-start" colspan="2" scope="row">Denominator</th>
                <td class="text-start" colspan="8"><?= esc($denTeks ?: $measurement['total_denum'] ?? '0') ?></td>
            </tr>
            <tr>
                <th class="text-start" colspan="2" scope="row">Formula</th>
                <td class="text-center" colspan="8">
                    <div class="text-center p-3" style="background:#f8f9fa;border-radius:4px;font-family:'Times New Roman',serif;">
                        <div style="display:inline-flex;align-items:center;gap:8px;font-size:1.1rem;">
                            <span>Hasil Capaian =</span>
                            <span style="display:inline-flex;flex-direction:column;align-items:center;">
                                <span style="border-bottom:2px solid #000;padding:2px 12px;font-style:italic;"><?= esc($numTeks ?: 'Numerator') ?></span>
                                <span style="padding:2px 12px;font-style:italic;"><?= esc($denTeks ?: 'Denominator') ?></span>
                            </span>
                            <span>&times; <?= $faktor ?>%</span>
                        </div>
                        <div class="mt-2 small text-muted">Standar: &gt;= <?= $targetNilai . ' ' . $satuan ?></div>
                    </div>
                </td>
            </tr>
            <tr>
                <th class="text-start" colspan="2" scope="row">Standar</th>
                <td class="text-start" colspan="8"><?= $targetNilai . ' ' . $satuan ?></td>
            </tr>
            <tr>
                <th rowspan="2" colspan="2" class="text-center" scope="row">Hasil<br>Pengukuran</th>
                <th colspan="6">Bulan</th>
                <th rowspan="2" colspan="2">Rata-Rata<br>Triwulan <?= $triwulanRomawi ?><br>Tahun <?= $tahun ?></th>
            </tr>
            <tr>
                <?php
                $bulanData = $measurement['bulanan'] ?? [];
                $bulanList = [];
                foreach ($bulanData as $b) {
                    $bulanList[] = $b;
                }
                $bulanNama = [$bulanNames[1], $bulanNames[2], $bulanNames[3]];
                for ($i = 0; $i < count($bulanList); $i++):
                ?>
                    <th colspan="2" scope="row"><?= $bulanNama[$i] ?? 'Bulan' . ($i + 1) ?></th>
                <?php endfor; ?>
                <?php for ($i = count($bulanList); $i < 3; $i++): ?>
                    <th colspan="2" scope="row"><?= $bulanNama[$i] ?? 'Bulan' . ($i + 1) ?></th>
                <?php endfor; ?>
            </tr>
            <tr>
                <th class="text-start" scope="row">Num</th>
                <th rowspan="2" style="vertical-align:middle;width:40px;text-align:center;">
                    <div style="font-weight:bold;transform:rotate(-90deg);white-space:nowrap;">Capaian</div>
                </th>
                <?php foreach ($bulanList as $b): ?>
                    <td><?= (int) $b['num'] ?></td>
                    <td rowspan="2"><?= $b['nilai'] !== null ? $b['nilai'] . '%' : '-' ?></td>
                <?php endforeach; ?>
                <?php for ($i = count($bulanList); $i < 3; $i++): ?>
                    <td>-</td>
                    <td rowspan="2">-</td>
                <?php endfor; ?>
                <td><?= (int) ($measurement['total_num'] ?? 0) ?></td>
                <td rowspan="2"><?= $rataCapaian !== null ? $rataCapaian . '%' : '-' ?></td>
            </tr>
            <tr>
                <th class="text-start" scope="row">Denum</th>
                <?php foreach ($bulanList as $b): ?>
                    <td><?= number_format((int) $b['denum']) ?></td>
                <?php endforeach; ?>
                <?php for ($i = count($bulanList); $i < 3; $i++): ?>
                    <td>-</td>
                <?php endfor; ?>
                <td><?= number_format((int) ($measurement['total_denum'] ?? 0)) ?></td>
            </tr>
        </tbody>
    </table>

    <?php
    $chartW = 650;
    $chartH = 280;
    $padL = 55;
    $padR = 25;
    $padT = 30;
    $padB = 45;
    $plotW = $chartW - $padL - $padR;
    $plotH = $chartH - $padT - $padB;

    $allVals = $capaians;
    if ($rataCapaian !== null) $allVals[] = (float) $rataCapaian;
    $allVals[] = $targetFloat;
    $minY = 0;
    $maxY = max($allVals) * 1.2;
    if ($maxY <= 0) $maxY = 100;

    $labels = [$bulanLabels[0], $bulanLabels[1], $bulanLabels[2], 'RATA-RATA TW ' . $triwulanRomawi . ' ' . $tahun];
    $dataPoints = [];
    for ($i = 0; $i < count($capaians); $i++) {
        $dataPoints[] = $capaians[$i] !== null ? (float) $capaians[$i] : 0;
    }
    $dataPoints[] = $rataCapaian !== null ? (float) $rataCapaian : 0;
    $count = count($dataPoints);

    function valToY($v, $minY, $maxY, $plotH, $padT)
    {
        if ($maxY <= $minY) return $padT + $plotH / 2;
        return $padT + $plotH - (($v - $minY) / ($maxY - $minY)) * $plotH;
    }
    function idxToX($i, $count, $plotW, $padL)
    {
        if ($count <= 1) return $padL + $plotW / 2;
        return $padL + ($i / ($count - 1)) * $plotW;
    }

    $targetY = valToY($targetFloat, $minY, $maxY, $plotH, $padT);
    $targetX1 = $padL;
    $targetX2 = $padL + $plotW;
    $stepY = ($maxY - $minY) / 4;
    if ($stepY <= 0) $stepY = 25;

    $linePoints = '';
    $circleTags = '';
    foreach ($dataPoints as $i => $v) {
        $x = idxToX($i, $count, $plotW, $padL);
        $y = valToY($v, $minY, $maxY, $plotH, $padT);
        $linePoints .= ($linePoints ? ' ' : '') . $x . ',' . $y;
        $circleTags .= '<circle cx="' . $x . '" cy="' . $y . '" r="4" fill="#d32f2f" stroke="#fff" stroke-width="1.5"/>';
        $circleTags .= '<text x="' . $x . '" y="' . ($y - 8) . '" text-anchor="middle" font-size="8" fill="#d32f2f" font-weight="bold">' . $v . '</text>';
    }
    ?>

    <div class="subsection-title">Grafik :</div>

    <div style="text-align:center; margin:6px 0;">
        <svg width="<?= $chartW ?>" height="<?= $chartH ?>" style="border:1px solid #ccc;">
            <text x="<?= $chartW / 2 ?>" y="14" text-anchor="middle" font-size="10" font-weight="bold"><?= esc($chartTitle) ?></text>
            <?php for ($yi = 0; $yi <= 4; $yi++): ?>
                <?php $yv = $minY + $stepY * $yi;
                $yy = valToY($yv, $minY, $maxY, $plotH, $padT); ?>
                <line x1="<?= $padL ?>" y1="<?= $yy ?>" x2="<?= $padL + $plotW ?>" y2="<?= $yy ?>" stroke="#ddd" stroke-width="0.5" />
                <text x="<?= $padL - 6 ?>" y="<?= $yy + 3 ?>" text-anchor="end" font-size="8"><?= round($yv, 1) ?></text>
            <?php endfor; ?>
            <?php foreach ($labels as $i => $l): ?>
                <?php $x = idxToX($i, $count, $plotW, $padL); ?>
                <text x="<?= $x ?>" y="<?= $chartH - 8 ?>" text-anchor="middle" font-size="8" transform="rotate(-30,<?= $x ?>,<?= $chartH - 8 ?>)"><?= $l ?></text>
            <?php endforeach; ?>
            <line x1="<?= $targetX1 ?>" y1="<?= $targetY ?>" x2="<?= $targetX2 ?>" y2="<?= $targetY ?>" stroke="#1a73e8" stroke-width="1.5" stroke-dasharray="4,2" />
            <text x="<?= $targetX2 + 3 ?>" y="<?= $targetY - 3 ?>" font-size="8" fill="#1a73e8">STANDAR <?= $targetFloat ?></text>
            <polyline points="<?= $linePoints ?>" fill="none" stroke="#d32f2f" stroke-width="1.5" />
            <?= $circleTags ?>
            <rect x="<?= $padL + $plotW - 130 ?>" y="3" width="125" height="22" fill="white" stroke="#ccc" stroke-width="0.5" rx="2" />
            <line x1="<?= $padL + $plotW - 122 ?>" y1="13" x2="<?= $padL + $plotW - 96 ?>" y2="13" stroke="#1a73e8" stroke-width="1.5" stroke-dasharray="4,2" />
            <text x="<?= $padL + $plotW - 90 ?>" y="15" font-size="7" fill="#1a73e8">STANDAR (<?= $targetFloat ?>)</text>
            <line x1="<?= $padL + $plotW - 122 ?>" y1="20" x2="<?= $padL + $plotW - 96 ?>" y2="20" stroke="#d32f2f" stroke-width="1.5" />
            <text x="<?= $padL + $plotW - 90 ?>" y="22" font-size="7" fill="#d32f2f">CAPAIAN</text>
        </svg>
    </div>

    <div class="subsection-title">Analisis :</div>
    <p style="text-align:justify;">
        Capaian indikator <strong><?= esc($ind->indicator_element ?? '-') ?></strong>
        pada Triwulan <?= $triwulanRomawi ?> Tahun <?= $tahun ?>
        menunjukkan nilai <strong><?= $rataCapaian !== null ? $rataCapaian . ' ' . $satuan : 'tidak ada data' ?></strong>
        dengan standar <?= $targetFloat . ' ' . $satuan ?>.
        <?php if ($rataCapaian !== null): ?>
            <?php if ($tercapai): ?>
                Capaian telah memenuhi standar yang ditetapkan.
            <?php else: ?>
                Capaian belum memenuhi standar yang ditetapkan (gap <?= $gap . ' ' . $satuan ?>).
                Hal ini perlu menjadi perhatian untuk dilakukan perbaikan pada periode selanjutnya.
            <?php endif; ?>
        <?php endif; ?>
        <?php if (!empty($analisisOtomatis)): ?>
            <?= esc(strip_tags($analisisOtomatis)) ?>
        <?php endif; ?>
    </p>

    <div class="section-title">B. ANALISIS PENYEBAB MASALAH</div>

    <?php if (!empty($analisis)): ?>
        <p><strong>Permasalahan :</strong><br><?= nl2br(esc($analisis[0]['permasalahan'] ?? '-')) ?></p>
        <table class="report-table">
            <tr>
                <td class="rata-tengah bold" style="width:30px">No</td>
                <td class="bold" style="width:100px">Klasifikasi</td>
                <td class="bold">Penyebab Masalah</td>
            </tr>
            <?php
            $klasifikasi7M = ['Man', 'Machine', 'Method', 'Material', 'Mothernature/Lingkungan', 'Measurement', 'Money'];
            $grouped = [];
            foreach ($analisis as $a) {
                $kat = $a['kategori'] ?? '';
                if (!isset($grouped[$kat])) $grouped[$kat] = [];
                $grouped[$kat][] = $a;
            }
            $no = 1;
            foreach ($klasifikasi7M as $kls):
                $items = $grouped[$kls] ?? [];
            ?>
                <tr>
                    <td class="rata-tengah"><?= $no++ ?></td>
                    <td><?= esc($kls) ?></td>
                    <td>
                        <?php if (!empty($items)): ?>
                            <?php $first = true;
                            foreach ($items as $item): ?>
                                <?php if (!$first): ?><br><?php endif;
                                                        $first = false; ?>
                                <?= nl2br(esc($item['penyebab'])) ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p><em>Belum ada data analisis penyebab masalah.</em></p>
    <?php endif; ?>

    <div class="section-title">C. SIKLUS PDSA</div>

    <?php
    $pdsaTools = $pdsa['tools'] ?? '';
    $pdsaSteps = !empty($pdsa['steps']) ? json_decode($pdsa['steps'], true) : [];
    $pdsaPlan = $pdsa['plan_rencana'] ?? $pdsa['plan'] ?? '';
    $pdsaTarget = $pdsa['plan_target'] ?? '';
    $pdsaDo = $pdsa['do_hasil'] ?? $pdsa['do'] ?? '';
    $pdsaStudy = $pdsa['study_hasil'] ?? $pdsa['study'] ?? '';
    $pdsaAct = $pdsa['act_kesimpulan'] ?? $pdsa['act'] ?? '';
    $pdsaTindakLanjut = $pdsa['act_tindak_lanjut'] ?? '';
    $hasPdsa = $pdsaTools || $pdsaSteps || $pdsaPlan || $pdsaTarget || $pdsaDo || $pdsaStudy || $pdsaAct || $pdsaTindakLanjut;
    ?>

    <?php if ($hasPdsa): ?>
        <table class="report-table">
            <tr>
                <td colspan="2"><strong>Tools</strong></td>
                <td><?= esc($pdsaTools ?: $ind->indicator_element ?? '-') ?></td>
            </tr>
            <?php if ($pdsaSteps): ?>
                <tr>
                    <td colspan="2"><strong>Step</strong></td>
                    <td>
                        <ol style="margin:0; padding-left:20px;">
                            <?php foreach ($pdsaSteps as $step): ?>
                                <li><?= esc($step) ?></li>
                            <?php endforeach; ?>
                        </ol>
                    </td>
                </tr>
            <?php endif; ?>
            <tr>
                <td colspan="3"><strong>Siklus</strong></td>
            </tr>
            <tr>
                <td rowspan="2" class="rata-tengah bold" style="width:12%; vertical-align:middle;">PLAN</td>
                <td style="width:20%"><strong>Rencana</strong></td>
                <td><?= nl2br(esc($pdsaPlan)) ?: '<em>Belum diisi</em>' ?></td>
            </tr>
            <tr>
                <td><strong>Target</strong></td>
                <td><?= nl2br(esc($pdsaTarget)) ?: '<em>Belum diisi</em>' ?></td>
            </tr>
            <tr>
                <td class="rata-tengah bold" style="vertical-align:middle;">DO</td>
                <td><strong>Hasil Pengamatan</strong></td>
                <td><?= nl2br(esc($pdsaDo)) ?: '<em>Belum diisi</em>' ?></td>
            </tr>
            <tr>
                <td class="rata-tengah bold" style="vertical-align:middle;">STUDY</td>
                <td><strong>Hasil Pengamatan Disesuaikan dengan Tujuan</strong></td>
                <td><?= nl2br(esc($pdsaStudy)) ?: '<em>Belum diisi</em>' ?></td>
            </tr>
            <tr>
                <td rowspan="2" class="rata-tengah bold" style="vertical-align:middle;">ACT</td>
                <td><strong>Kesimpulan dalam Siklus Ini</strong></td>
                <td><?= nl2br(esc($pdsaAct)) ?: '<em>Belum diisi</em>' ?></td>
            </tr>
            <tr>
                <td><strong>Tindak Lanjut Perbaikan</strong></td>
                <td><?= nl2br(esc($pdsaTindakLanjut)) ?: '<em>Belum diisi</em>' ?></td>
            </tr>
        </table>
    <?php else: ?>
        <p><em>Belum ada data PDSA.</em></p>
    <?php endif; ?>

    <div class="ttd">
        <p><strong>Kepala <?= esc($dokumen['unit_name'] ?? 'Unit') ?></strong></p>
        <br><br><br>
        <p><?= esc($ttd['kepala_unit'] ?? '(....................)') ?></p>
    </div>

</body>

</html>