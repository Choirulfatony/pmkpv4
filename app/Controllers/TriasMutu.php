<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\TriasMutuDokumenModel;

class TriasMutu extends AppController
{
    protected $dokumenModel;
    
    public function __construct()
    {
        $this->dokumenModel = new TriasMutuDokumenModel();
        helper('text');
    }

    private function generateAnalisisOtomatis(array $measurement, array $dokumen): string
    {
        $ind = $measurement['indicator'] ?? null;
        if (!$ind) return '';

        $namaIndikator = $ind->indicator_element ?? 'Indikator';
        $target = (float) ($ind->indicator_target ?? 0);
        $triwulanLabels = ['', 'I', 'II', 'III', 'IV'];
        $twLabel = $triwulanLabels[$dokumen['triwulan']] ?? '';
        $tahun = $dokumen['tahun'] ?? '';

        $bulanan = $measurement['bulanan'] ?? [];
        $nilaiBulan = [];
        foreach ($bulanan as $b) {
            if ($b['nilai'] !== null) {
                $nilaiBulan[] = (float) $b['nilai'];
            }
        }
        $avgCap = count($nilaiBulan) > 0 ? array_sum($nilaiBulan) / count($nilaiBulan) : 0;

        $status = $avgCap >= $target ? 'memenuhi' : 'belum memenuhi';
        $analisis = "Capaian indikator {$namaIndikator} pada Triwulan {$twLabel} Tahun {$tahun} {$status} standar yang ditetapkan sebesar {$target}%.";

        if (count($nilaiBulan) >= 2) {
            $bulanNames = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            $allSame = count(array_unique(array_map(function($v) { return round($v, 2); }, $nilaiBulan))) === 1;

            if ($allSame) {
                $firstMonth = $bulanNames[$bulanan[0]['bulan']] ?? '';
                $lastMonth = $bulanNames[$bulanan[count($bulanan) - 1]['bulan']] ?? '';
                $analisis .= " Seluruh bulan {$firstMonth} sampai {$lastMonth} menunjukkan rata-rata nilai {$avgCap}%.";
            } elseif ($nilaiBulan[count($nilaiBulan) - 1] > $nilaiBulan[0]) {
                $firstMonth = $bulanNames[$bulanan[0]['bulan']] ?? '';
                $lastMonth = $bulanNames[$bulanan[count($bulanan) - 1]['bulan']] ?? '';
                $analisis .= " Terjadi peningkatan capaian dari bulan {$firstMonth} hingga {$lastMonth}.";
            } else {
                $analisis .= " Terjadi penurunan capaian pada periode pengukuran.";
            }
        }

        $analisisData = $dokumen['analisis'] ?? [];
        if (!empty($analisisData)) {
            $semuaPenyebab = [];
            foreach ($analisisData as $a) {
                if (!empty($a['penyebab'])) {
                    $semuaPenyebab[] = trim($a['penyebab']);
                }
            }
            if (!empty($semuaPenyebab)) {
                $analisis .= " Hal ini disebabkan karena " . implode(', ', $semuaPenyebab) . ".";
            }

            $rencana = $analisisData[0]['rencana_perbaikan'] ?? '';
            if (!empty(trim($rencana))) {
                $analisis .= " Rencana perbaikan: " . trim($rencana) . ".";
            }
        }

        return $analisis;
    }

    public function index()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/auth');
        }
        $this->disableCache();

        $db = db_connect();
        $userId = session()->get('user_id');
        $role = session()->get('user_role');
        $deptId = session()->get('department_id');

        $builder = $db->table('triasmutu_dokumen td');
        $builder->select("
            td.*,
            mid.department_name AS unit_name,
            COALESCE(qi.indicator_element, lqi.indicator_element) AS indicator_name,
            u.nama AS created_by_name,
            uf.nama AS final_by_name
        ");
        $builder->join('master_institution_department mid', 'mid.department_id = td.unit_id', 'left');
        $builder->join('quality_indicator qi', 'qi.indicator_id = td.indicator_id AND td.indicator_category_id = 4', 'left');
        $builder->join('local_quality_indicator lqi', 'lqi.indicator_id = td.indicator_id AND td.indicator_category_id != 4', 'left');
        $builder->join('users u', 'u.id_user = td.created_by', 'left');
        $builder->join('users uf', 'uf.id_user = td.final_by', 'left');

        if (!in_array($role, ['ADMINISTRATOR', 'KOMITE'])) {
            $builder->where('td.created_by', $userId);
        }

        $builder->orderBy('td.created_at', 'DESC');
        $documents = $builder->get()->getResultArray();

        return $this->render('siimut/triasmutu/index', [
            'judul'     => 'Trias Mutu',
            'icon'      => '<i class="bi bi-file-earmark-text"></i>',
            'documents' => $documents,
        ]);
    }

    public function pengukuran()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/auth');
        }
        $this->disableCache();

        $db = db_connect();
        $role = session()->get('user_role');
        $deptId = session()->get('department_id');

        if (in_array($role, ['ADMINISTRATOR', 'KOMITE'])) {
            $units = $db->table('master_institution_department mid')
                ->select('mid.department_id, mid.department_name')
                ->where('mid.department_record_status', 'A')
                ->groupStart()
                    ->where("mid.department_id IN (SELECT DISTINCT group_department_id FROM quality_indicator_group WHERE group_record_status = 'A')", null, false)
                    ->orWhere("mid.department_id IN (SELECT DISTINCT group_department_id FROM local_quality_indicator_group WHERE group_record_status = 'A')", null, false)
                ->groupEnd()
                ->orderBy('mid.department_name', 'ASC')
                ->get()
                ->getResultArray();
        } else {
            $units = $db->table('master_institution_department')
                ->select('department_id, department_name')
                ->where('department_id', $deptId)
                ->where('department_record_status', 'A')
                ->get()
                ->getResultArray();
        }

        $tahunList = range(date('Y'), date('Y') - 5);

        $dokumenId = $this->request->getGet('dokumen_id');
        $measurement = null;
        $selected = null;

        if ($dokumenId) {
            $selected = $this->dokumenModel->getDokumenWithRelations((int) $dokumenId);
            if ($selected) {
                $measurement = $this->dokumenModel->getMeasurementData(
                    (int) $selected['indicator_category_id'],
                    (int) $selected['indicator_id'],
                    (int) $selected['tahun'],
                    (int) $selected['triwulan'],
                    (int) $selected['unit_id']
                );
            }
        }

        return $this->render('siimut/triasmutu/pengukuran', [
            'judul'       => 'Pengukuran Indikator',
            'icon'        => '<i class="bi bi-bar-chart"></i>',
            'units'       => $units,
            'tahunList'   => $tahunList,
            'selected'    => $selected,
            'measurement' => $measurement,
            'userRole'    => $role,
        ]);
    }

    public function analisisAi()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Not authenticated']);
        }

        $categoryId = (int) $this->request->getPost('category_id');
        $indicatorId = (int) $this->request->getPost('indicator_id');
        $tahun = (int) $this->request->getPost('tahun');
        $triwulan = (int) $this->request->getPost('triwulan');
        $unitId = (int) $this->request->getPost('unit_id');

        $measurement = $this->dokumenModel->getMeasurementData(
            $categoryId, $indicatorId, $tahun, $triwulan, $unitId
        );

        if (!$measurement || !$measurement['indicator']) {
            return $this->response->setJSON(['success' => false, 'message' => 'Data pengukuran tidak ditemukan']);
        }

        $db = db_connect();
        $unit = $db->table('master_institution_department')
            ->select('department_name')
            ->where('department_id', $unitId)
            ->get()
            ->getRowArray();
        $measurement['unit_name'] = $unit['department_name'] ?? '-';

        $analisis = $this->generateLocalAnalisis($measurement, $triwulan, $tahun);

        return $this->response->setJSON([
            'success'  => true,
            'analisis' => $analisis,
        ]);
    }

    public function saveAnalisisAi()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Not authenticated']);
        }

        $dokumenId = (int) $this->request->getPost('dokumen_id');
        $analisis = $this->request->getPost('analisis_ai');

        if (!$dokumenId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Dokumen tidak valid']);
        }

        $this->dokumenModel->update($dokumenId, ['analisis_ai' => $analisis]);

        return $this->response->setJSON(['success' => true]);
    }

    private function generateLocalAnalisis(array $measurement, int $triwulan, int $tahun): string
    {
        $ind = $measurement['indicator'];
        $target = (float) ($measurement['target'] ?? 0);
        $nilaiTriwulan = $measurement['nilai_triwulan'];
        $bulanan = $measurement['bulanan'] ?? [];

        $bulanNama = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                       'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $triwulanRomawi = ['', 'I', 'II', 'III', 'IV'];
        $twLabel = $triwulanRomawi[$triwulan] ?? '';

        $out = '';
        $out .= "=== ANALISIS INDIKATOR MUTU ===\n";
        $out .= "Indikator: {$ind->indicator_element}\n";
        $out .= "Unit: " . ($measurement['unit_name'] ?? '-') . "\n";
        $out .= "Periode: Triwulan {$twLabel} Tahun {$tahun}\n";
        $out .= "Target: {$target} {$ind->indicator_target_unit}\n\n";

        // --- 1. Evaluasi Capaian ---
        $out .= "--- EVALUASI CAPAIAN ---\n";

        if ($nilaiTriwulan !== null) {
            $selisih = $nilaiTriwulan - $target;
            $tercapai = $nilaiTriwulan >= $target;
            $persenCapaian = $target > 0 ? round(($nilaiTriwulan / $target) * 100, 2) : 0;

            $out .= "Nilai Triwulan: {$nilaiTriwulan}%\n";
            $out .= "Selisih terhadap target: " . ($selisih >= 0 ? '+' : '') . round($selisih, 2) . "%\n";
            $out .= "Persentase capaian: {$persenCapaian}% dari target\n";
            $out .= "Status: " . ($tercapai ? "TERCAPAI ✓" : "BELUM TERCAPAI ✗") . "\n\n";

            if ($tercapai) {
                if ($selisih > 10) {
                    $out .= "Capaian sangat baik, melampaui target dengan selisih " . round($selisih, 2) . "%. Pertahankan konsistensi.\n\n";
                } elseif ($selisih > 0) {
                    $out .= "Capaian memenuhi target dengan selisih " . round($selisih, 2) . "%. Terus tingkatkan untuk hasil yang lebih baik.\n\n";
                } else {
                    $out .= "Capaian tepat sesuai target. Perlu dipertahankan dan dievaluasi konsistensinya.\n\n";
                }
            } else {
                $gap = round(abs($selisih), 2);
                if ($gap > 20) {
                    $out .= "Capaian masih jauh di bawah target (kesenjangan {$gap}%). Diperlukan strategi perbaikan yang signifikan.\n\n";
                } elseif ($gap > 10) {
                    $out .= "Capaian belum memenuhi target (kesenjangan {$gap}%). Perlu identifikasi hambatan dan tindakan korektif.\n\n";
                } else {
                    $out .= "Capaian mendekati target (kesenjangan hanya {$gap}%). Dengan sedikit perbaikan, target dapat tercapai.\n\n";
                }
            }
        } else {
            $out .= "Belum ada data capaian untuk triwulan ini.\n\n";
        }

        // --- 2. Analisis Tren Bulanan ---
        $out .= "--- ANALISIS TREN BULANAN ---\n";

        if (count($bulanan) >= 2) {
            $nilaiBulan = [];
            foreach ($bulanan as $b) {
                $nilaiBulan[$b['bulan']] = [
                    'nilai' => $b['nilai'],
                    'num'   => $b['num'],
                    'denum' => $b['denum'],
                    'bulan' => $bulanNama[$b['bulan']] ?? "Bulan {$b['bulan']}",
                ];
            }

            foreach ($nilaiBulan as $bData) {
                $n = $bData['nilai'] !== null ? round($bData['nilai'], 2) . '%' : 'Tidak ada data';
                $out .= "{$bData['bulan']}: Capaian = {$n}, Numerator = {$bData['num']}, Denominator = {$bData['denum']}\n";
            }

            $capaianValues = [];
            foreach ($bulanan as $b) {
                if ($b['nilai'] !== null) {
                    $capaianValues[] = (float) $b['nilai'];
                }
            }

            if (count($capaianValues) >= 2) {
                $first = $capaianValues[0];
                $last = $capaianValues[count($capaianValues) - 1];
                $trend = $last - $first;
                $changes = [];
                for ($i = 1; $i < count($capaianValues); $i++) {
                    $changes[] = $capaianValues[$i] - $capaianValues[$i - 1];
                }
                $avgChange = count($changes) > 0 ? round(array_sum($changes) / count($changes), 2) : 0;

                $allSame = count(array_unique(array_map(function($v) { return round($v, 2); }, $capaianValues))) === 1;

                if ($allSame) {
                    $out .= "\nTren: STABIL — Nilai capaian konsisten di seluruh bulan (" . round($first, 2) . "%).\n";
                } elseif ($trend > 0) {
                    $out .= "\nTren: MENINGKAT — Terjadi peningkatan dari " . round($first, 2) . "% ke " . round($last, 2) . "%.\n";
                    if ($avgChange > 5) {
                        $out .= "Peningkatan signifikan dengan rata-rata kenaikan {$avgChange}% per bulan.\n";
                    } else {
                        $out .= "Peningkatan bertahap dengan rata-rata kenaikan {$avgChange}% per bulan.\n";
                    }
                } elseif ($trend < 0) {
                    $out .= "\nTren: MENURUN — Terjadi penurunan dari " . round($first, 2) . "% ke " . round($last, 2) . "%.\n";
                    if (abs($avgChange) > 5) {
                        $out .= "Penurunan signifikan dengan rata-rata penurunan " . abs($avgChange) . "% per bulan. Perlu perhatian segera.\n";
                    } else {
                        $out .= "Penurunan bertahap dengan rata-rata penurunan " . abs($avgChange) . "% per bulan.\n";
                    }
                }

                // Volatility check
                $maxVal = max($capaianValues);
                $minVal = min($capaianValues);
                $range = $maxVal - $minVal;
                if ($range > 15) {
                    $out .= "Fluktuasi: TINGGI — Rentang nilai antar bulan mencapai {$range}% (terendah {$minVal}%, tertinggi {$maxVal}%). Perlu evaluasi konsistensi proses.\n";
                } elseif ($range > 5) {
                    $out .= "Fluktuasi: SEDANG — Rentang nilai antar bulan {$range}% (terendah {$minVal}%, tertinggi {$maxVal}%).\n";
                } else {
                    $out .= "Fluktuasi: RENDAH — Data relatif konsisten dengan rentang hanya {$range}%.\n";
                }
            } else {
                $out .= "\nData bulanan belum mencukupi untuk analisis tren.\n";
            }
        } elseif (count($bulanan) == 1) {
            $b = $bulanan[0];
            $n = $b['nilai'] !== null ? round($b['nilai'], 2) . '%' : 'Tidak ada data';
            $out .= "{$bulanNama[$b['bulan']]}: Capaian = {$n}\n";
            $out .= "Hanya tersedia data 1 bulan, belum dapat dianalisis tren.\n";
        } else {
            $out .= "Tidak ada data bulanan.\n";
        }

        // --- 3. Analisis Numerator/Denominator ---
        $out .= "\n--- ANALISIS NUMERATOR & DENOMINATOR ---\n";

        $totalNum = 0;
        $totalDenum = 0;
        $validMonths = 0;
        foreach ($bulanan as $b) {
            if ($b['num'] > 0 || $b['denum'] > 0) {
                $totalNum += $b['num'];
                $totalDenum += $b['denum'];
                $validMonths++;
            }
        }

        if ($validMonths > 0) {
            $out .= "Total Numerator: " . round($totalNum, 2) . "\n";
            $out .= "Total Denominator: " . round($totalDenum, 2) . "\n";
            $out .= "Rata-rata Numerator/bulan: " . round($totalNum / $validMonths, 2) . "\n";
            $out .= "Rata-rata Denominator/bulan: " . round($totalDenum / $validMonths, 2) . "\n\n";

            // Check for zero denominator issues
            $zeroDenum = false;
            foreach ($bulanan as $b) {
                if ($b['denum'] == 0 && $b['num'] > 0) {
                    $zeroDenum = true;
                    break;
                }
            }
            if ($zeroDenum) {
                $out .= "Catatan: Terdapat bulan dengan denominator 0 namun numerator > 0. Periksa kelengkapan data.\n";
            }
        } else {
            $out .= "Belum ada data numerator/denominator.\n";
        }

        // --- 4. Rekomendasi ---
        $out .= "\n--- REKOMENDASI ---\n";

        if ($nilaiTriwulan !== null) {
            if ($nilaiTriwulan >= $target) {
                $out .= "✓ Capaian sudah memenuhi target. Rekomendasi:\n";
                $out .= "  1. Pertahankan konsistensi proses yang sudah berjalan.\n";
                $out .= "  2. Dokumentasikan faktor-faktor keberhasilan sebagai best practice.\n";
                $out .= "  3. Tingkatkan target jika capaian konsisten melampaui target saat ini.\n";
                $out .= "  4. Diseminasi praktik baik ke unit/indikator lain.\n";
            } else {
                $gap = round(abs($nilaiTriwulan - $target), 2);
                $out .= "✗ Capaian belum memenuhi target (gap {$gap}%). Rekomendasi:\n";
                $out .= "  1. Lakukan analisis akar masalah (fishbone/5 why) untuk identifikasi penyebab.\n";
                $out .= "  2. Libatkan seluruh tim dalam penyusunan rencana tindak lanjut.\n";
                $out .= "  3. Tetapkan target bertahap untuk mencapai target utama.\n";
                $out .= "  4. Monitoring lebih ketat dengan frekuensi evaluasi mingguan.\n";

                if (count($capaianValues ?? []) >= 2 && $trend < 0) {
                    $out .= "  5. Segera lakukan intervensi karena tren menunjukkan penurunan.\n";
                }
            }
        } else {
            $out .= "Belum ada data untuk memberikan rekomendasi. Lengkapi data pengukuran terlebih dahulu.\n";
        }

        $out .= "\n---\n";
        $out .= "Analisis ini dibuat berdasarkan data pengukuran yang tersedia.";

        return $out;
    }

    public function analisisPenyebab()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/auth');
        }
        $this->disableCache();

        $db = db_connect();
        $role = session()->get('user_role');
        $deptId = session()->get('department_id');

        $units = $db->table('master_institution_department')
            ->select('department_id, department_name')
            ->where('department_record_status', 'A')
            ->orderBy('department_name', 'ASC')
            ->get()
            ->getResultArray();

        $tahunList = range(date('Y'), date('Y') - 5);

        $dokumenId = $this->request->getGet('dokumen_id');
        if (!$dokumenId) {
            return redirect()->to(site_url('siimut/trias-mutu/pengukuran'));
        }

        $selected = $this->dokumenModel->getDokumenWithRelations((int) $dokumenId);

        return $this->render('siimut/triasmutu/analisis_penyebab', [
            'judul'     => 'Analisis Penyebab Masalah',
            'icon'      => '<i class="bi bi-diagram-3"></i>',
            'units'     => $units,
            'tahunList' => $tahunList,
            'selected'  => $selected,
        ]);
    }

    public function pdsa()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/auth');
        }
        $this->disableCache();

        $db = db_connect();
        $units = $db->table('master_institution_department')
            ->select('department_id, department_name')
            ->where('department_record_status', 'A')
            ->orderBy('department_name', 'ASC')
            ->get()
            ->getResultArray();

        $tahunList = range(date('Y'), date('Y') - 5);

        $dokumenId = $this->request->getGet('dokumen_id');
        $selected = null;

        if ($dokumenId) {
            $selected = $this->dokumenModel->getDokumenWithRelations((int) $dokumenId);
        }

        return $this->render('siimut/triasmutu/pdsa', [
            'judul'     => 'Siklus PDSA',
            'icon'      => '<i class="bi bi-arrow-repeat"></i>',
            'units'     => $units,
            'tahunList' => $tahunList,
            'selected'  => $selected,
        ]);
    }

    public function cetak()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/auth');
        }
        $this->disableCache();

        $db = db_connect();
        $role = session()->get('user_role');
        $deptId = session()->get('department_id');

        if (in_array($role, ['ADMINISTRATOR', 'KOMITE'])) {
            $units = $db->table('master_institution_department mid')
                ->select('mid.department_id, mid.department_name')
                ->where('mid.department_record_status', 'A')
                ->groupStart()
                    ->where("mid.department_id IN (SELECT DISTINCT group_department_id FROM quality_indicator_group WHERE group_record_status = 'A')", null, false)
                    ->orWhere("mid.department_id IN (SELECT DISTINCT group_department_id FROM local_quality_indicator_group WHERE group_record_status = 'A')", null, false)
                ->groupEnd()
                ->orderBy('mid.department_name', 'ASC')
                ->get()
                ->getResultArray();
        } else {
            $units = $db->table('master_institution_department')
                ->select('department_id, department_name')
                ->where('department_id', $deptId)
                ->where('department_record_status', 'A')
                ->get()
                ->getResultArray();
        }

        $tahunList = range(date('Y'), date('Y') - 5);

        $dokumenId = $this->request->getGet('dokumen_id');
        $selected = null;
        $measurement = null;

        if ($dokumenId) {
            $selected = $this->dokumenModel->getDokumenWithRelations((int) $dokumenId);
            if ($selected) {
                $measurement = $this->dokumenModel->getMeasurementData(
                    (int) $selected['indicator_category_id'],
                    (int) $selected['indicator_id'],
                    (int) $selected['tahun'],
                    (int) $selected['triwulan'],
                    (int) $selected['unit_id']
                );
            }
        }

        $numdenum = [];
        $analisisOtomatis = '';
        if ($dokumenId && $selected) {
            $numdenum = $this->dokumenModel->getIndicatorNumDenum(
                (int) $selected['indicator_category_id'],
                (int) $selected['indicator_id']
            );
            if (!empty($selected['analisis_ai'])) {
                $analisisOtomatis = $selected['analisis_ai'];
            } elseif ($measurement) {
                $analisisOtomatis = $this->generateAnalisisOtomatis($measurement, $selected);
            }
        }

        return $this->render('siimut/triasmutu/cetak', [
            'judul'        => 'Cetak Trias Mutu',
            'icon'         => '<i class="bi bi-printer"></i>',
            'units'        => $units,
            'tahunList'    => $tahunList,
            'selected'     => $selected,
            'measurement'  => $measurement,
            'userRole'     => $role,
            'numdenum'     => $numdenum,
            'analisisOtomatis' => $analisisOtomatis,
        ]);
    }

    public function getIndicators()
    {
        try {
            $categoryId = (int) $this->request->getPost('category_id');
            $tahun = (int) $this->request->getPost('tahun');
            $unitId = $this->request->getPost('unit_id');

            $table = $categoryId == 4 ? 'quality_indicator' : 'local_quality_indicator';
            $tableResult = $categoryId == 4 ? 'quality_indicator_result' : 'local_quality_indicator_result';

            $db = db_connect();

            $builder = $db->table("$table qi");
            $builder->select("DISTINCT qi.indicator_id, qi.indicator_element", false);
            $builder->join("$tableResult qir", 'qi.indicator_id = qir.result_indicator_id', 'inner');
            $builder->where('qi.indicator_category_id', $categoryId);
            $builder->whereIn("qi.indicator_record_status", ['A', 'D']);
            $builder->where('YEAR(qir.result_period)', $tahun);
            $builder->where('qir.result_record_status', 'A');
            if ($unitId && $unitId > 0) {
                $builder->where('qir.result_department_id', $unitId);
            }
            $builder->orderBy('qi.indicator_element', 'ASC');

            $indicators = $builder->get()->getResultArray();
            return $this->response->setJSON($indicators);
        } catch (\Throwable $e) {
            log_message('error', '[getIndicators] ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function getPengukuranData()
    {
        $categoryId = (int) $this->request->getPost('category_id');
        $indicatorId = (int) $this->request->getPost('indicator_id');
        $tahun = (int) $this->request->getPost('tahun');
        $triwulan = (int) $this->request->getPost('triwulan');
        $unitId = (int) $this->request->getPost('unit_id');

        $measurement = $this->dokumenModel->getMeasurementData(
            $categoryId, $indicatorId, $tahun, $triwulan, $unitId
        );

        $selected = null;
        $dokumen = $this->dokumenModel->where([
            'unit_id'               => $unitId,
            'indicator_category_id' => $categoryId,
            'indicator_id'          => $indicatorId,
            'triwulan'              => $triwulan,
            'tahun'                 => $tahun,
        ])->first();

        if ($dokumen) {
            $selected = $this->dokumenModel->getDokumenWithRelations((int) $dokumen['id']);
        } elseif ($measurement && $measurement['indicator'] ?? null) {
            $data = [
                'unit_id'               => $unitId,
                'indicator_category_id' => $categoryId,
                'indicator_id'          => $indicatorId,
                'triwulan'              => $triwulan,
                'tahun'                 => $tahun,
            ];
            $id = $this->dokumenModel->getOrCreateDokumen($data);
            if ($id) {
                $selected = $this->dokumenModel->getDokumenWithRelations((int) $id);
            }
        }

        $numdenum = [];
        if ($selected) {
            $numdenum = $this->dokumenModel->getIndicatorNumDenum(
                (int) $selected['indicator_category_id'],
                (int) $selected['indicator_id']
            );
        }

        return $this->response->setJSON([
            'measurement' => $measurement,
            'selected'    => $selected,
            'numdenum'    => $numdenum,
            'categoryLabels' => [4 => 'INM', 5 => 'IMPRS', 6 => 'IMPUNIT'],
            'triwulanLabels' => ['', 'I', 'II', 'III', 'IV'],
        ]);
    }

    public function getOrCreateDokumen()
    {
        $data = [
            'unit_id'               => (int) $this->request->getPost('unit_id'),
            'indicator_category_id' => (int) $this->request->getPost('category_id'),
            'indicator_id'          => (int) $this->request->getPost('indicator_id'),
            'triwulan'              => (int) $this->request->getPost('triwulan'),
            'tahun'                 => (int) $this->request->getPost('tahun'),
        ];

        $id = $this->dokumenModel->getOrCreateDokumen($data);
        return $this->response->setJSON(['dokumen_id' => $id]);
    }

    public function saveAnalisis()
    {
        $dokumenId = (int) $this->request->getPost('dokumen_id');
        $permasalahan = $this->request->getPost('permasalahan');
        $kategori = $this->request->getPost('kategori');
        $penyebab = $this->request->getPost('penyebab');
        $rencanaPerbaikan = $this->request->getPost('rencana_perbaikan');

        if (!$dokumenId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Dokumen tidak valid']);
        }

        $db = db_connect();
        $db->table('triasmutu_analisis')->where('dokumen_id', $dokumenId)->delete();

        if (!empty($kategori) && is_array($kategori)) {
            $data = [];
            foreach ($kategori as $i => $kat) {
                if (!empty($kat) && !empty($penyebab[$i])) {
                    $row = [
                        'dokumen_id'    => $dokumenId,
                        'permasalahan'  => $permasalahan,
                        'kategori'      => $kat,
                        'penyebab'      => $penyebab[$i],
                        'created_at'    => date('Y-m-d H:i:s'),
                    ];
                    if ($rencanaPerbaikan !== null && is_array($rencanaPerbaikan)) {
                        $row['rencana_perbaikan'] = $rencanaPerbaikan[$i] ?? '';
                    } elseif ($rencanaPerbaikan !== null) {
                        $row['rencana_perbaikan'] = $rencanaPerbaikan;
                    }
                    $data[] = $row;
                }
            }
            if (!empty($data)) {
                $db->table('triasmutu_analisis')->insertBatch($data);
            }
        }

        return $this->response->setJSON(['success' => true]);
    }

    public function savePdsa()
    {
        $dokumenId = (int) $this->request->getPost('dokumen_id');

        if (!$dokumenId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Dokumen tidak valid']);
        }

        $db = db_connect();
        $existing = $db->table('triasmutu_pdsa')
            ->where('dokumen_id', $dokumenId)
            ->get()
            ->getRowArray();

        $planRencana    = $this->request->getPost('plan_rencana');
        $planTarget     = $this->request->getPost('plan_target');
        $doHasil        = $this->request->getPost('do_hasil');
        $studyHasil     = $this->request->getPost('study_hasil');
        $actKesimpulan  = $this->request->getPost('act_kesimpulan');
        $actTindakLanjut = $this->request->getPost('act_tindak_lanjut');

        $data = [
            'tools'             => $this->request->getPost('tools'),
            'steps'             => $this->request->getPost('steps'),
            'plan_rencana'      => $planRencana,
            'plan_target'       => $planTarget,
            'plan'              => $planRencana . ($planTarget ? "\n\nTarget: " . $planTarget : ''),
            'do_hasil'          => $doHasil,
            'do'                => $doHasil,
            'study_hasil'       => $studyHasil,
            'study'             => $studyHasil,
            'act_kesimpulan'    => $actKesimpulan,
            'act_tindak_lanjut' => $actTindakLanjut,
            'act'               => $actKesimpulan . ($actTindakLanjut ? "\n\nTindak Lanjut: " . $actTindakLanjut : ''),
        ];

        if ($existing) {
            $data['updated_at'] = date('Y-m-d H:i:s');
            $db->table('triasmutu_pdsa')
                ->where('dokumen_id', $dokumenId)
                ->update($data);
        } else {
            $data['dokumen_id'] = $dokumenId;
            $data['created_at'] = date('Y-m-d H:i:s');
            $db->table('triasmutu_pdsa')->insert($data);
        }

        return $this->response->setJSON(['success' => true]);
    }

    public function generatePdsa()
    {
        $dokumenId = (int) $this->request->getPost('dokumen_id');
        if (!$dokumenId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Dokumen tidak valid']);
        }

        $selected = $this->dokumenModel->getDokumenWithRelations($dokumenId);
        if (!$selected) {
            return $this->response->setJSON(['success' => false, 'message' => 'Dokumen tidak ditemukan']);
        }

        $measurement = $this->dokumenModel->getMeasurementData(
            (int) $selected['indicator_category_id'],
            (int) $selected['indicator_id'],
            (int) $selected['tahun'],
            (int) $selected['triwulan'],
            (int) $selected['unit_id']
        );

        $indicator = $selected['indicator'];
        $namaIndikator = $indicator->indicator_element ?? 'Indikator';
        $target = (float) ($measurement['target'] ?? 0);
        $nilai = $measurement['nilai_triwulan'];
        $selisih = $nilai !== null ? round($nilai - $target, 2) : null;
        $status = ($nilai !== null && $nilai >= $target) ? 'Tercapai' : 'Belum Tercapai';
        $gap = $selisih !== null ? abs($selisih) : 0;
        $unitName = $selected['unit_name'] ?? 'Unit';
        $triwulan = $selected['triwulan'];
        $tahun = $selected['tahun'];
        $triwulanRomawi = ['', 'I', 'II', 'III', 'IV'];
        $twLabel = $triwulanRomawi[$triwulan] ?? '';

        $bulanan = $measurement['bulanan'] ?? [];
        $bulanNama = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                       'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        $tools = "SIKLUS PDSA Tools $namaIndikator";

        $analisisAI = $selected['analisis_ai'] ?? '';

        if (!empty($analisisAI)) {
            // Parse rekomendasi dari AI analysis
            $rekomendasi = [];
            if (preg_match('/--- REKOMENDASI ---(.*?)(?:\n---|\z)/s', $analisisAI, $m)) {
                $lines = explode("\n", trim($m[1]));
                foreach ($lines as $line) {
                    $line = trim($line);
                    if (!empty($line) && preg_match('/^\d+\.\s*(.+)/', $line, $lm)) {
                        $rekomendasi[] = trim($lm[1]);
                    } elseif (!empty($line) && strpos($line, '✗') === 0) {
                        // skip status line
                    }
                }
            }

            // Parse tren dari AI analysis
            $tren = '';
            if (preg_match('/Tren:\s*(.+?)(?:\n|$)/', $analisisAI, $m)) {
                $tren = trim($m[1]);
            }

            // Parse evaluasi capaian
            $evaluasi = '';
            if (preg_match('/--- EVALUASI CAPAIAN ---(.*?)(?:\n---|\z)/s', $analisisAI, $m)) {
                $evaluasi = trim($m[1]);
            }

            $steps = ["Menyampaikan hasil kepada pimpinan rumah sakit"];
            if (!empty($rekomendasi)) {
                foreach ($rekomendasi as $r) {
                    $steps[] = $r;
                }
            }

            $planRencana = '';
            if (!empty($rekomendasi)) {
                foreach ($rekomendasi as $i => $r) {
                    $planRencana .= ($i + 1) . ". $r\n";
                }
            } else {
                $planRencana = "1. Menyampaikan hasil kepada pimpinan rumah sakit\n";
                $planRencana .= "2. Melakukan analisis akar masalah terkait belum tercapainya indikator $namaIndikator\n";
                $planRencana .= "3. Menyusun rencana tindak lanjut dan perbaikan";
            }

            $planTarget = "1. Hasil indikator mutu tersampaikan kepada atasan dalam 7 hari setelah laporan TW selesai\n";
            if (!empty($rekomendasi)) {
                $planTarget .= "2. " . $rekomendasi[0];
                if (count($rekomendasi) > 1) {
                    $planTarget .= "\n3. " . $rekomendasi[1];
                }
            }

            $doHasil = "1. Melaksanakan pertemuan tim untuk membahas pencapaian indikator\n";
            $doHasil .= "2. Melakukan identifikasi penyebab ketidaktercapaian menggunakan metode fishbone/5 why\n";
            $doHasil .= "3. Melaksanakan intervensi perbaikan sesuai rencana yang telah disusun\n";
            $doHasil .= "4. Monitoring dan dokumentasi setiap tahapan pelaksanaan";

            $studyHasil = "1. Hasil capaian indikator $namaIndikator: ";
            $studyHasil .= ($nilai !== null ? "$nilai%" : "belum ada data") . " (target $target%)\n";
            $studyHasil .= "2. Status: $status\n";
            if (!empty($tren)) {
                $studyHasil .= "3. Tren: $tren\n";
            }
            if (!empty($evaluasi)) {
                $studyHasil .= "4. Evaluasi: " . str_replace("\n", " ", $evaluasi);
            }

            $actKesimpulan = "Berdasarkan hasil evaluasi, indikator $namaIndikator ";
            if ($status === 'Tercapai') {
                $actKesimpulan .= "telah mencapai target ($target%). Perlu dipertahankan dan dimonitoring secara berkelanjutan.";
            } else {
                $actKesimpulan .= "belum mencapai target ($target%) dengan capaian $nilai%. ";
                $actKesimpulan .= "Perlu dilakukan perbaikan dan tindak lanjut yang lebih intensif.";
            }

            $actTindakLanjut = "1. Implementasi rencana perbaikan yang telah disusun\n";
            $actTindakLanjut .= "2. Evaluasi berkala setiap bulan\n";
            $actTindakLanjut .= "3. Laporkan hasil perbaikan kepada pimpinan\n";
            $actTindakLanjut .= "4. Lanjutkan ke siklus PDSA berikutnya jika diperlukan";

        } else {
            // Fallback: gunakan data measurement saja
            $steps = [
                "Menyampaikan hasil kepada pimpinan rumah sakit",
                "Membuat permohonan kerjasama dengan BPJS terkait pelayanan",
                "Mengusulkan permohonan kerjasama dengan penyedia terkait",
                "Sosialisasi pada pasien tentang pelayanan yang tersedia",
            ];

            $planRencana = "1. Menyampaikan hasil kepada pimpinan rumah sakit\n";
            $planRencana .= "2. Membuat permohonan kerjasama dengan BPJS terkait pelayanan $namaIndikator\n";
            $planRencana .= "3. Mengusulkan permohonan kerjasama dengan penyedia terkait\n";
            $planRencana .= "4. Sosialisasi pada pasien terkait pelayanan $namaIndikator";

            $planTarget = "1. Hasil indikator mutu tersampaikan kepada atasan (7 hari) setelah laporan TW selesai\n";
            $planTarget .= "2. Penyampaian nota dinas pada atasan terkait permohonan kerjasama\n";
            $planTarget .= "3. Sosialisasi pada pasien secara berkala";

            $doHasil = "1. Hasil capaian indikator mutu telah disampaikan kepada atasan rumah sakit.\n";
            $doHasil .= "2. Hasil evaluasi menunjukkan masih perlu perbaikan.";

            $studyHasil = "1. Penyampaian hasil indikator kepada atasan telah meningkatkan perhatian manajemen.\n";
            $studyHasil .= "2. Sosialisasi kepada pasien bertujuan meningkatkan pengetahuan dan pemahaman.";

            $actKesimpulan = "Belum adanya perbaikan pada target layanan $namaIndikator ";
            $actKesimpulan .= "karena capaian masih $nilai% (target $target%). ";
            $actKesimpulan .= "Perlu tindak lanjut lebih lanjut.";

            $actTindakLanjut = "1. Berencana menyampaikan nota dinas kepada atasan terkait permohonan kerjasama\n";
            $actTindakLanjut .= "2. Lanjut Siklus berikutnya PDSA";
        }

        return $this->response->setJSON([
            'success' => true,
            'tools'             => $tools,
            'steps'             => $steps,
            'plan_rencana'      => $planRencana,
            'plan_target'       => $planTarget,
            'do_hasil'          => $doHasil,
            'study_hasil'       => $studyHasil,
            'act_kesimpulan'    => $actKesimpulan,
            'act_tindak_lanjut' => $actTindakLanjut,
        ]);
    }

    public function simpanDraft()
    {
        $dokumenId = (int) $this->request->getPost('dokumen_id');
        if (!$dokumenId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Dokumen tidak valid']);
        }

        $this->dokumenModel->update($dokumenId, ['status' => 'draft']);
        return $this->response->setJSON(['success' => true]);
    }

    public function finalisasi()
    {
        $dokumenId = (int) $this->request->getPost('dokumen_id');
        if (!$dokumenId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Dokumen tidak valid']);
        }

        $dokumen = $this->dokumenModel->find($dokumenId);
        if (!$dokumen) {
            return $this->response->setJSON(['success' => false, 'message' => 'Dokumen tidak ditemukan']);
        }

        if ($dokumen['status'] === 'final') {
            return $this->response->setJSON(['success' => false, 'message' => 'Dokumen sudah di-final']);
        }

        $this->dokumenModel->update($dokumenId, [
            'status'   => 'final',
            'final_by' => session()->get('user_id'),
            'final_at' => date('Y-m-d H:i:s'),
        ]);

        return $this->response->setJSON(['success' => true, 'message' => 'Dokumen berhasil di-final']);
    }

    public function deleteDokumen()
    {
        $dokumenId = (int) $this->request->getPost('dokumen_id');
        if (!$dokumenId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Dokumen tidak valid']);
        }

        $role = session()->get('user_role');
        if (!in_array($role, ['ADMINISTRATOR', 'KOMITE'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Hanya Admin dan Komite Mutu yang dapat menghapus dokumen']);
        }

        $db = db_connect();
        $db->table('triasmutu_analisis')->where('dokumen_id', $dokumenId)->delete();
        $db->table('triasmutu_pdsa')->where('dokumen_id', $dokumenId)->delete();
        $db->table('triasmutu_ttd')->where('dokumen_id', $dokumenId)->delete();
        $db->table('triasmutu_dokumen')->where('id', $dokumenId)->delete();

        return $this->response->setJSON(['success' => true, 'message' => 'Dokumen berhasil dihapus']);
    }

    public function saveTtd()
    {
        $dokumenId = (int) $this->request->getPost('dokumen_id');
        if (!$dokumenId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Dokumen tidak valid']);
        }

        $db = db_connect();
        $existing = $db->table('triasmutu_ttd')
            ->where('dokumen_id', $dokumenId)
            ->get()
            ->getRowArray();

        $data = [
            'disusun_oleh' => $this->request->getPost('disusun_oleh'),
            'mengetahui'   => $this->request->getPost('mengetahui'),
            'kepala_unit'  => $this->request->getPost('kepala_unit'),
        ];

        if ($existing) {
            $data['updated_at'] = date('Y-m-d H:i:s');
            $db->table('triasmutu_ttd')
                ->where('dokumen_id', $dokumenId)
                ->update($data);
        } else {
            $data['dokumen_id'] = $dokumenId;
            $data['created_at'] = date('Y-m-d H:i:s');
            $db->table('triasmutu_ttd')->insert($data);
        }

        return $this->response->setJSON(['success' => true]);
    }

    public function cetakPdf()
    {
        $dokumenId = (int) $this->request->getGet('dokumen_id');
        if (!$dokumenId) {
            return redirect()->to('siimut/trias-mutu/cetak');
        }

        $selected = $this->dokumenModel->getDokumenWithRelations($dokumenId);
        if (!$selected) {
            return redirect()->to('siimut/trias-mutu/cetak');
        }

        $measurement = $this->dokumenModel->getMeasurementData(
            (int) $selected['indicator_category_id'],
            (int) $selected['indicator_id'],
            (int) $selected['tahun'],
            (int) $selected['triwulan'],
            (int) $selected['unit_id']
        );

        $categoryLabels = [4 => 'INM', 5 => 'IMPRS', 6 => 'IMPUNIT'];
        $triwulanLabels = ['', 'I', 'II', 'III', 'IV'];

        $analisisOtomatis = $this->generateAnalisisOtomatis($measurement, $selected);
        $numdenum = $this->dokumenModel->getIndicatorNumDenum(
            (int) $selected['indicator_category_id'],
            (int) $selected['indicator_id']
        );

        $logoSrc = '';
        $logoPath = ROOTPATH . 'assets/img/logo-jatim.png';
        if (file_exists($logoPath)) {
            $logoSrc = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
        }

        $html = view('siimut/triasmutu/pdf_template', [
            'dokumen'        => $selected,
            'measurement'    => $measurement,
            'numdenum'       => $numdenum,
            'categoryLabel'  => $categoryLabels[$selected['indicator_category_id']] ?? '-',
            'triwulanLabel'  => $triwulanLabels[$selected['triwulan']] ?? '-',
            'analisisOtomatis' => $analisisOtomatis,
            'logoSrc'        => $logoSrc,
        ]);

        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $isView = $this->request->getGet('view') === '1';
        $disposition = $isView ? 'inline' : 'attachment';

        $this->response
            ->setContentType('application/pdf')
            ->setHeader('Content-Disposition', $disposition . '; filename="trias_mutu_' . $dokumenId . '.pdf"')
            ->setHeader('Cache-Control', 'public, must-revalidate, max-age=0')
            ->setHeader('Pragma', 'public')
            ->setHeader('Content-Transfer-Encoding', 'binary')
            ->setBody($dompdf->output())
            ->send();
        exit;
    }

    public function getDokumenDetail()
    {
        $dokumenId = (int) $this->request->getPost('dokumen_id');
        $selected = $this->dokumenModel->getDokumenWithRelations($dokumenId);

        if (!$selected) {
            return $this->response->setJSON(['success' => false]);
        }

        $measurement = $this->dokumenModel->getMeasurementData(
            (int) $selected['indicator_category_id'],
            (int) $selected['indicator_id'],
            (int) $selected['tahun'],
            (int) $selected['triwulan'],
            (int) $selected['unit_id']
        );

        $categoryLabels = [4 => 'INM', 5 => 'IMPRS', 6 => 'IMPUNIT'];

        return $this->response->setJSON([
            'success'     => true,
            'dokumen'     => $selected,
            'measurement' => $measurement,
            'categoryLabel' => $categoryLabels[$selected['indicator_category_id']] ?? '-',
        ]);
    }
}
