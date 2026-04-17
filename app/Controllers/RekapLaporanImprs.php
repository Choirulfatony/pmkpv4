<?php

namespace App\Controllers;

use App\Models\SiimutMenuModel;
use App\Models\RekapLaporanImprsModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class RekapLaporanImprs extends AppController
{
    protected $rekapModel;

    public function __construct()
    {
        $this->rekapModel = new RekapLaporanImprsModel();
    }

    public function index()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/auth');
        }

        $this->disableCache();

        $role = session()->get('user_role');
        $menuModel = new SiimutMenuModel();
        $menus = $menuModel->getMenuByRole($role);

        $tahun = $this->request->getGet('tahun') ?? date('Y');
        $indicatorId = $this->request->getGet('indicator_id');

        // Jika ada indicator_id, tampilkan detail
        if ($indicatorId) {
            $detail = $this->rekapModel->getDetailByIdImprs((int) $indicatorId);
            return $this->render('siimut/rekap_laporan_imprs_detail', [
                'judul'       => 'Detail Rekap Imprs',
                'icon'        => '<i class="bi bi-bar-chart"></i>',
                '_content'    => view('siimut/rekap_laporan_imprs_detail', [
                    'tahun'        => $tahun,
                    'indicatorId'  => $indicatorId,
                    'detail'       => $detail,
                ]),
                'menus'       => $menus
            ]);
        }

        // Default: tampilkan rekap utama
        return $this->render('siimut/rekap_laporan_imprs', [
            'judul'    => 'Rekap Laporan Imprs',
            'icon'     => '<i class="bi bi-bar-chart"></i>',
            '_content' => view('siimut/rekap_laporan_imprs', [
                'tahun' => $tahun,
            ]),
            'menus'    => $menus
        ]);
    }

    /**
     * AJAX: Ambil data rekap Imprs (untuk DataTables) - Optimized
     */
    public function getAjaxDataRekapImprs()
    {
        $post = $this->request->getPost();

        log_message('error', 'getAjaxDataRekapImprs called, post: ' . json_encode($post));

        if (!$post) {
            log_message('error', 'getAjaxDataRekapImprs - POST is empty or null');
            return $this->response->setJSON(['error' => 'Invalid request - no POST data', 'post_data' => $post]);
        }

        $indicators = $this->rekapModel->getIndicatorImprs($post);
        log_message('error', 'Indicators count: ' . count($indicators));
        
        // Clear cache untuk memastikan data terbaru
        $this->rekapModel->clearCache();
        
        $tahun = isset($post['vtahun']) ? (int) $post['vtahun'] : (int) date('Y');

        // Ambil SEMUA data sekaligus (1 query saja)
        $indicatorIds = array_column($indicators, 'indicator_id');

        $allData = $this->rekapModel->getAllMonthlyData($indicatorIds, $tahun);

        $data = [];
        $no = isset($post['start']) ? (int) $post['start'] : 0;

        foreach ($indicators as $indicator) {
            $no++;
            $row = [];

            $row[] = '<div class="fw-bold">' . $no . '</div>';

            $row[] = '<div class="py-1 text-start ps-2">
                <a href="javascript:void(0);" class="fw-semibold text-decoration-none" title="Detail Rekapan Ruangan" onclick="view_detail_imprs(' . $indicator->indicator_id . ');">'
                . esc($indicator->indicator_element) . '
                </a>
            </div>';

            $row[] = '<div class="py-1">
                <span id="target">' . esc($indicator->indicator_target) . '</span>
                <span id="factor" style="display:none">' . esc($indicator->factors) . '</span>
                <span id="operator" style="display:none">' . esc($indicator->operator) . '</span>
                <span class="small text-muted ms-1">' . esc($indicator->indicator_units) . '</span>
            </div>';

            // Ambil data dari array (bukan query)
            for ($bulan = 1; $bulan <= 12; $bulan++) {
                $key = $indicator->indicator_id . '_' . $bulan;
                $list = isset($allData[$key]) ? $allData[$key] : null;

                if ($list == null) {
                    $row[] = '<div class="py-1">
                        <span class="text-muted">-</span>
                        <span style="display:none" id="num">0</span>
                        <span style="display:none" id="denum">0</span>
                    </div>';
                } else {
                    $total = $list->total ?? '';
                    $num = $list->num ?? 0;
                    $denum = $list->denum ?? 0;

                    $row[] = '<div class="py-1">
                        <span id="total">' . esc($total) . '</span>
                        <div class="small text-muted mt-1">
                            <span id="num">' . esc($num) . '</span> | <span id="denum">' . esc($denum) . '</span>
                        </div>
                    </div>';
                }
            }

            $data[] = $row;
        }

        return $this->response->setJSON([
            'draw'            => $post['draw'] ?? 1,
            'recordsTotal'    => $this->rekapModel->getTotalRecords(),
            'recordsFiltered' => $this->rekapModel->getTotalRecords(),
            'data'            => $data,
        ]);
    }

    /**
     * AJAX: Detail Imprs per ruangan (untuk DataTables)
     */
    public function getAjaxDataRekapImprsDetail()
    {
        try {
            $post = $this->request->getPost();

            log_message('error', 'DETAIL called: ' . json_encode($post));

            if (!$post) {
                return $this->response->setJSON(['error' => 'Invalid request']);
            }

            $indicatorId = isset($post['indicator_id']) ? (int) $post['indicator_id'] : 0;
            $tahun = isset($post['vtahun']) ? (int) $post['vtahun'] : (int) date('Y');

            log_message('error', 'DETAIL: indicatorId=' . $indicatorId . ', tahun=' . $tahun);

            if ($indicatorId == 0) {
                return $this->response->setJSON(['error' => 'Invalid indicator_id']);
            }

            // Ambil semua ruangan untuk indicator ini
            $departments = $this->rekapModel->getDepartmentsByIndicator($indicatorId, $tahun, $post);
            log_message('error', 'DETAIL: departments count=' . count($departments));

            // Ambil semua data detail sekaligus
            $allDetailData = $this->rekapModel->getAllDetailData($indicatorId, $tahun);
            log_message('error', 'DETAIL: allDetailData count=' . count($allDetailData));

            // Ambil info indicator
            $indicatorInfo = $this->rekapModel->getDetailByIdImprs($indicatorId);
            $target = $indicatorInfo ? ($indicatorInfo->indicator_target ?? 0) : 0;
            $factors = $indicatorInfo ? ($indicatorInfo->indicator_factors ?? 1) : 1;
            $operatorCalc = $indicatorInfo ? ($indicatorInfo->indicator_target_calculation ?? '>=') : '>=';
            $units = $indicatorInfo ? ($indicatorInfo->indicator_units ?? '%') : '%';

            log_message('error', 'DETAIL: indicatorInfo target=' . $target . ', factor=' . $factors . ', operator=' . $operatorCalc);

            $data = [];
            $no = isset($post['start']) ? (int) $post['start'] : 0;

            foreach ($departments as $dept) {
                $no++;
                $row = [];

                // No
                $row[] = '<div class="fw-bold">' . $no . '</div>';

                // Ruangan
                $row[] = '<div class="py-1 text-start ps-2">' . esc($dept->department_name) . '</div>';

                // Target - tampilkan dengan units
                $row[] = '<div class="py-1">
                    <span id="target_det">' . esc($target) . '</span>
                    <span class="small text-muted ms-1">' . esc($units) . '</span>
                    <span id="factor_det" style="display:none">' . esc($factors) . '</span>
                    <span id="operator_det" style="display:none">' . esc($operatorCalc) . '</span>
                </div>';

                // Bulan 1-12
                for ($bulan = 1; $bulan <= 12; $bulan++) {
                    $key = $dept->department_id . '_' . $bulan;
                    $list = isset($allDetailData[$key]) ? $allDetailData[$key] : null;

                    if ($list == null) {
                        $row[] = '<div class="py-1">
                            <span class="text-muted">-</span>
                            <span style="display:none" id="num_det">0</span>
                            <span style="display:none" id="denum_det">0</span>
                        </div>';
                    } else {
                        $total = $list->total ?? '';
                        $num = $list->num ?? 0;
                        $denum = $list->denum ?? 0;

                        $row[] = '<div class="py-1">
                            <span id="total_det">' . esc($total) . '</span>
                            <div class="small text-muted mt-1">
                                <span id="num_det">' . esc($num) . '</span> | <span id="denum_det">' . esc($denum) . '</span>
                            </div>
                        </div>';
                    }
                }

                $data[] = $row;
            }

            return $this->response->setJSON([
                'draw'            => $post['draw'] ?? 1,
                'recordsTotal'    => count($departments),
                'recordsFiltered' => count($departments),
                'data'            => $data,
            ]);
        } catch (\Exception $e) {
            log_message('error', 'DETAIL ERROR: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
            return $this->response->setStatusCode(500)->setJSON([
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * AJAX: Detail Imprs per ruangan (untuk modal detail) - Optimized
     */
    public function viewDetailImprs($indicatorId = null)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Invalid request']);
        }

        $data = $this->rekapModel->getDetailByIdImprs($indicatorId);
        if ($data) {
            return $this->response->setJSON($data);
        }
        return $this->response->setJSON(['status' => false]);
    }

    /**
     * Export Excel Rekap Imprs - Format PMKP
     */
    public function exportExcel()
    {
        $tahun = $this->request->getGet('tahun') ?? date('Y');

        // Clean any previous output
        if (ob_get_level()) {
            ob_end_clean();
        }

        try {
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('IMPRS Rekap ' . $tahun);

            // Fungsi helper untuk get column letter dari index
            $getColLetter = function ($colIdx) {
                $letter = '';
                while ($colIdx > 0) {
                    $mod = ($colIdx - 1) % 26;
                    $letter = chr(65 + $mod) . $letter;
                    $colIdx = (int)(($colIdx - $mod) / 26);
                }
                return $letter;
            };

            // ==================== HEADER LAPORAN ====================
            // Baris 1: Judul Utama
            $sheet->setCellValue('A1', 'Capaian Indikator Mutu Prioritas RS (IMPRS)');
            $sheet->mergeCells('A1:AB1');
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
            $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('A1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

            // Baris 2: Nama Rumah Sakit
            $sheet->setCellValue('A2', 'RSUD dr. SOEDONO PROVINSI JAWA TIMUR');
            $sheet->mergeCells('A2:AB2');
            $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(12);
            $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('A2')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

            // Baris 3: Tahun
            $sheet->setCellValue('A3', 'TAHUN ' . $tahun);
            $sheet->mergeCells('A3:AB3');
            $sheet->getStyle('A3')->getFont()->setBold(true)->setSize(12);
            $sheet->getStyle('A3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('A3')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

            // ==================== HEADER TABEL - BARIS 5 & 6 (Multi-level) ====================
            // Baris 5: Kolom utama
            $sheet->setCellValue('A5', 'No.');
            $sheet->setCellValue('B5', 'Judul Indikator');
            $sheet->setCellValue('C5', 'Standar');
            $sheet->setCellValue('D5', 'Num/Denum');
            $sheet->setCellValue('E5', 'BULAN');

            // Merge sel E5 untuk span 24 kolom (12 bulan x 2)
            $sheet->mergeCells('E5:AB5');

            // Merge kolom A-D (No, Judul, Standar, Num/Denum) - 2 baris
            $sheet->mergeCells('A5:A6');
            $sheet->mergeCells('B5:B6');
            $sheet->mergeCells('C5:C6');
            $sheet->mergeCells('D5:D6');

            // Style header baris 5 - Semua center
            foreach (['A5', 'B5', 'C5', 'D5'] as $cell) {
                $sheet->getStyle($cell)->getFont()->setBold(true);
                $sheet->getStyle($cell)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle($cell)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getStyle($cell)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            }

            // Wrap text untuk D5
            $sheet->getStyle('D5')->getAlignment()->setWrapText(true);

            $sheet->getStyle('E5')->getFont()->setBold(true);
            $sheet->getStyle('E5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('E5')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

            // ==================== HEADER TABEL - BARIS 6 (Sub-header bulan) ====================
            $bulanNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            $colStart = 5; // Kolom E (index 5)

            // Baris 6: Nama bulan dan Capaian (kolom E onwards)
            for ($i = 0; $i < count($bulanNames); $i++) {
                $colIdx = $colStart + ($i * 2); // 5, 7, 9, 11...
                $colIdx2 = $colIdx + 1; // 6, 8, 10, 12...

                $col1 = $getColLetter($colIdx);
                $col2 = $getColLetter($colIdx2);

                // Kolom 1: Nama Bulan (Nilai)
                $sheet->setCellValue($col1 . '6', $bulanNames[$i]);
                $sheet->getStyle($col1 . '6')->getFont()->setBold(true);
                $sheet->getStyle($col1 . '6')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle($col1 . '6')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

                // Kolom 2: Capaian
                $sheet->setCellValue($col2 . '6', 'Capaian');
                $sheet->getStyle($col2 . '6')->getFont()->setBold(true);
                $sheet->getStyle($col2 . '6')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle($col2 . '6')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            }

            // ==================== DATA ====================
            $indicators = $this->rekapModel->getIndicatorImprs(['vtahun' => $tahun]);
            $indicatorIds = array_column($indicators, 'indicator_id');
            $allData = $this->rekapModel->getAllMonthlyData($indicatorIds, $tahun);

            $no = 1;
            $row = 7;

            foreach ($indicators as $ind) {
                // Baris pertama: Num (Numerator)
                $sheet->setCellValue('A' . $row, $no);
                $sheet->setCellValue('B' . $row, $ind->indicator_element);

                // Format Standar
                $targetVal = $ind->indicator_target ?? '';
                $operatorRaw = $ind->operator ?? '';
                $operator = trim($operatorRaw);
                $factorsVal = $ind->factors ?? '';

                // Jika operator "=" gunakan factor, selain itu gunakan target
                if ($operator === '=' && !empty($factorsVal)) {
                    $displayVal = $factorsVal;
                } else {
                    $displayVal = $targetVal;
                }

                $units = $ind->indicator_units ?? '%';
                // Jika operator "=" tidak ditampilkan, langsung tampilkan value
                if ($operator === '=') {
                    $standar = $displayVal . ' ' . $units;
                } else {
                    $standar = $operator . ' ' . $displayVal . ' ' . $units;
                }
                $sheet->setCellValue('C' . $row, trim($standar));
                $sheet->setCellValue('D' . $row, 'Num');

                // Merge Judul Indikator (kolom B) - rata kiri
                $sheet->mergeCells('B' . $row . ':B' . ($row + 1));
                $sheet->getStyle('B' . $row)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getStyle('B' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle('B' . $row)->getAlignment()->setWrapText(true);

                // Merge Standar (kolom C) - center
                $sheet->mergeCells('C' . $row . ':C' . ($row + 1));
                $sheet->getStyle('C' . $row)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getStyle('C' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('C' . $row)->getAlignment()->setWrapText(true);

                // Merge No (kolom A) - center
                $sheet->mergeCells('A' . $row . ':A' . ($row + 1));
                $sheet->getStyle('A' . $row)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A' . $row)->getAlignment()->setWrapText(true);

                // Data bulan untuk Num
                $capaianData = []; // Simpan data capaian untuk merge nanti
                for ($bulan = 1; $bulan <= 12; $bulan++) {
                    $key = $ind->indicator_id . '_' . $bulan;
                    $val = isset($allData[$key]) ? $allData[$key] : null;

                    $colIdx = 5 + (($bulan - 1) * 2);
                    $colIdx2 = $colIdx + 1;
                    $col1 = $getColLetter($colIdx);
                    $col2 = $getColLetter($colIdx2);

                    // Nilai Num
                    if ($val && $val->num > 0) {
                        $sheet->setCellValue($col1 . $row, $val->num);
                    } else {
                        $sheet->setCellValue($col1 . $row, '-');
                    }
                    $sheet->getStyle($col1 . $row)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

                    // Simpan data capaian untuk ditampilkan di merged cell
                    $capaianData[$col2] = null;
                    if ($val && $val->num > 0 && $val->denum > 0) {
                        $nilai = number_format($val->total_value ?? 0, 2);
                        $target = $ind->indicator_target;
                        $capaianData[$col2] = $nilai . '%';
                    }
                }

                // Style baris Num
                $sheet->getStyle('A' . $row . ':D' . $row)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                $sheet->getStyle('A' . $row . ':D' . $row)->getFont()->setBold(true);

                // Baris kedua: Denum
                $row++;
                $sheet->setCellValue('D' . $row, 'Denum');
                $sheet->getStyle('D' . $row)->getFont()->setBold(true);

                // Data bulan untuk Denum
                for ($bulan = 1; $bulan <= 12; $bulan++) {
                    $key = $ind->indicator_id . '_' . $bulan;
                    $val = isset($allData[$key]) ? $allData[$key] : null;

                    $colIdx = 5 + (($bulan - 1) * 2);
                    $colIdx2 = $colIdx + 1;
                    $col1 = $getColLetter($colIdx);
                    $col2 = $getColLetter($colIdx2);

                    // Nilai Denum
                    if ($val && $val->denum > 0) {
                        $sheet->setCellValue($col1 . $row, $val->denum);
                    } else {
                        $sheet->setCellValue($col1 . $row, '-');
                    }
                    $sheet->getStyle($col1 . $row)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

                    // Merge dan center kolom Capaian (Num/Denum jadi satu)
                    $sheet->mergeCells($col2 . ($row - 1) . ':' . $col2 . $row);
                    $sheet->getStyle($col2 . ($row - 1))->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                    $sheet->getStyle($col2 . ($row - 1))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                    // Isi nilai Capaian di merged cell
                    if ($capaianData[$col2] !== null) {
                        $sheet->setCellValue($col2 . ($row - 1), $capaianData[$col2]);
                    } else {
                        $sheet->setCellValue($col2 . ($row - 1), '-');
                    }
                    $sheet->getStyle($col2 . ($row - 1))->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                }

                // Style baris Denum
                $sheet->getStyle('A' . $row . ':D' . $row)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                $sheet->getStyle('A' . $row . ':D' . $row)->getFont()->setBold(true);

                $no++;
                $row++;
            }

            // ==================== SET COLUMN WIDTH ====================
            $sheet->getColumnDimension('A')->setWidth(5);
            $sheet->getColumnDimension('B')->setWidth(70);
            $sheet->getColumnDimension('C')->setWidth(12);
            $sheet->getColumnDimension('D')->setWidth(10);
            $sheet->getStyle('D')->getAlignment()->setWrapText(true);

            // Set row height untuk wrap text
            for ($r = 6; $r < $row; $r++) {
                $sheet->getRowDimension($r)->setRowHeight(30);
            }

            // Kolom bulan (E-AB)
            for ($i = 5; $i <= 28; $i++) {
                $colLetter = $getColLetter($i);
                $sheet->getColumnDimension($colLetter)->setWidth(12);
            }

            // ==================== FULL BORDER ALL ====================
            // Border untuk seluruh area tabel (semua border)
            $lastRow = $row - 1;
            if ($lastRow >= 5) {
                $sheet->getStyle('A5:' . $getColLetter(28) . $lastRow)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            }

            // ==================== SET FONT ====================
            $sheet->getStyle('A1:' . $getColLetter(28) . $lastRow)->getFont()->setName('Arial');
            $sheet->getStyle('A1:' . $getColLetter(28) . $lastRow)->getFont()->setSize(11);

            // ==================== SET ZOOM ====================
            $sheet->getSheetView()->setZoomScale(70);

            // Download
            $filename = 'Capaian Indikator Mutu Prioritas RS (IMPRS) ' . $tahun . ' ' . date('YmdHis');
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
            header('Cache-Control: max-age=0');

            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save('php://output');
            exit;
        } catch (\Exception $e) {
            log_message('error', 'Export Excel Error: ' . $e->getMessage());
            echo 'Error: ' . $e->getMessage();
            exit;
        }
    }

    /**
     * Export Excel per Indikator Imprs - Detail per Ruangan
     */
    public function exportExcelIndicator($indicatorId)
    {
        $tahun = $this->request->getGet('tahun') ?? date('Y');

        if (ob_get_level()) {
            ob_end_clean();
        }

        try {
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

            $getColLetter = function ($colIdx) {
                $letter = '';
                while ($colIdx > 0) {
                    $mod = ($colIdx - 1) % 26;
                    $letter = chr(65 + $mod) . $letter;
                    $colIdx = (int)(($colIdx - $mod) / 26);
                }
                return $letter;
            };

            // Get indicator detail
            $indicator = $this->rekapModel->getDetailByIdImprs((int) $indicatorId);
            $indicatorName = $indicator->indicator_element ?? 'Detail';

            // Get data per department
            $departments = $this->rekapModel->getDepartmentsByIndicator((int) $indicatorId, $tahun, []);
            $allDetailData = $this->rekapModel->getAllDetailData((int) $indicatorId, $tahun);

            // Format Standar
            $targetVal = $indicator->indicator_target ?? '';
            $operator = trim($indicator->operator ?? '');
            $factorsVal = $indicator->factors ?? '';
            $units = $indicator->indicator_units ?? '%';

            if ($operator === '=') {
                $standar = $factorsVal . ' ' . $units;
            } else {
                $standar = $operator . ' ' . $targetVal . ' ' . $units;
            }

            // ==================== SHEET: DETAIL ====================
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Detail ' . substr($indicatorName, 0, 20)); // Set title sheet dengan nama indikator (max 31 char)

            $sheet->setCellValue('A1', 'CAPAIAN INDIKATOR MUTU RS (IMPRS)');
            $sheet->mergeCells('A1:AB1');
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
            $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $sheet->setCellValue('A2', 'RSUD dr. SOEDONO PROVINSI JAWA TIMUR');
            $sheet->mergeCells('A2:AB2');
            $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(12);
            $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $sheet->setCellValue('A3', 'TAHUN ' . $tahun);
            $sheet->mergeCells('A3:AB3');
            $sheet->getStyle('A3')->getFont()->setBold(true)->setSize(12);
            $sheet->getStyle('A3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $sheet->setCellValue('A4', '');
            $sheet->mergeCells('A4:AB4');

            $sheet->setCellValue('A6', 'INDIKATOR: ' . $indicatorName);
            $sheet->mergeCells('A6:AB6');
            $sheet->getStyle('A6')->getFont()->setBold(true)->setSize(11);
            $sheet->getStyle('A6')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle('A6')->getAlignment()->setIndent(0);
            $sheet->getRowDimension(6)->setRowHeight(20);

            // Header Tabel
            $sheet->setCellValue('A8', 'No.');
            $sheet->setCellValue('B8', 'Ruangan');
            $sheet->setCellValue('C8', 'Standar');
            $sheet->setCellValue('D8', 'Num/Denum');
            $sheet->setCellValue('E8', 'BULAN');
            $sheet->mergeCells('E8:AB8');
            $sheet->mergeCells('A8:A9');
            $sheet->mergeCells('B8:B9');
            $sheet->mergeCells('C8:C9');
            $sheet->mergeCells('D8:D9');

            foreach (['A8', 'B8', 'C8', 'D8'] as $cell) {
                $sheet->getStyle($cell)->getFont()->setBold(true);
                $sheet->getStyle($cell)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle($cell)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getStyle($cell)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            }

            $sheet->getStyle('E8')->getFont()->setBold(true);
            $sheet->getStyle('E8')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('E8')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

            // Beri border untuk range E8:AB9
            $sheet->getStyle('E8:AB9')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

            // Hapus warna background (no fill) untuk header BULAN
            $sheet->getStyle('E8:AB9')->applyFromArray([
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_NONE,
                ],
            ]);

            // Sub-header bulan - Row 9
            $bulanNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            for ($i = 0; $i < count($bulanNames); $i++) {
                $colIdx = 5 + ($i * 2);
                $colIdx2 = $colIdx + 1;
                $col1 = $getColLetter($colIdx);
                $col2 = $getColLetter($colIdx2);

                $sheet->setCellValue($col1 . '9', $bulanNames[$i]);
                $sheet->getStyle($col1 . '9')->getFont()->setBold(true);
                $sheet->getStyle($col1 . '9')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle($col1 . '9')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

                $sheet->setCellValue($col2 . '9', 'Capaian');
                $sheet->getStyle($col2 . '9')->getFont()->setBold(true);
                $sheet->getStyle($col2 . '9')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle($col2 . '9')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            }

            // Data Detail
            $no = 1;
            $row = 10;

            foreach ($departments as $dept) {
                $sheet->setCellValue('A' . $row, $no);
                $sheet->setCellValue('B' . $row, $dept->department_name);
                $sheet->setCellValue('C' . $row, $standar);
                $sheet->setCellValue('D' . $row, 'Num');

                $sheet->mergeCells('B' . $row . ':B' . ($row + 1));
                $sheet->getStyle('B' . $row)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getStyle('B' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

                $sheet->mergeCells('C' . $row . ':C' . ($row + 1));
                $sheet->getStyle('C' . $row)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

                $sheet->mergeCells('A' . $row . ':A' . ($row + 1));
                $sheet->getStyle('A' . $row)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

                $capaianData = [];
                for ($bulan = 1; $bulan <= 12; $bulan++) {
                    $key = $dept->department_id . '_' . $bulan;
                    $val = isset($allDetailData[$key]) ? $allDetailData[$key] : null;

                    $colIdx = 5 + (($bulan - 1) * 2);
                    $colIdx2 = $colIdx + 1;
                    $col1 = $getColLetter($colIdx);
                    $col2 = $getColLetter($colIdx2);

                    if ($val && $val->num > 0) {
                        $sheet->setCellValue($col1 . $row, $val->num);
                    } else {
                        $sheet->setCellValue($col1 . $row, '-');
                    }
                    $sheet->getStyle($col1 . $row)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

                    $capaianData[$col2] = null;
                    if ($val && $val->num > 0 && $val->denum > 0) {
                        $capaianData[$col2] = number_format($val->total_value ?? 0, 2) . '%';
                    }
                }

                $sheet->getStyle('A' . $row . ':D' . $row)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                $sheet->getStyle('A' . $row . ':D' . $row)->getFont()->setBold(true);

                $row++;
                $sheet->setCellValue('D' . $row, 'Denum');
                $sheet->getStyle('D' . $row)->getFont()->setBold(true);

                for ($bulan = 1; $bulan <= 12; $bulan++) {
                    $key = $dept->department_id . '_' . $bulan;
                    $val = isset($allDetailData[$key]) ? $allDetailData[$key] : null;

                    $colIdx = 5 + (($bulan - 1) * 2);
                    $colIdx2 = $colIdx + 1;
                    $col1 = $getColLetter($colIdx);
                    $col2 = $getColLetter($colIdx2);

                    if ($val && $val->denum > 0) {
                        $sheet->setCellValue($col1 . $row, $val->denum);
                    } else {
                        $sheet->setCellValue($col1 . $row, '-');
                    }
                    $sheet->getStyle($col1 . $row)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

                    $sheet->mergeCells($col2 . ($row - 1) . ':' . $col2 . $row);
                    $sheet->getStyle($col2 . ($row - 1))->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                    $sheet->getStyle($col2 . ($row - 1))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                    if ($capaianData[$col2] !== null) {
                        $sheet->setCellValue($col2 . ($row - 1), $capaianData[$col2]);
                    } else {
                        $sheet->setCellValue($col2 . ($row - 1), '-');
                    }
                    $sheet->getStyle($col2 . ($row - 1))->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                }

                $sheet->getStyle('A' . $row . ':D' . $row)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                $sheet->getStyle('A' . $row . ':D' . $row)->getFont()->setBold(true);

                $no++;
                $row++;
            }

            $sheet->getColumnDimension('A')->setWidth(5);
            $sheet->getStyle('A')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getColumnDimension('B')->setWidth(30);
            $sheet->getColumnDimension('C')->setWidth(12);
            $sheet->getColumnDimension('D')->setWidth(10);
            $sheet->getStyle('C')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('D')->getAlignment()->setWrapText(true);
            for ($i = 5; $i <= 28; $i++) {
                $sheet->getColumnDimension($getColLetter($i))->setWidth(12);
            }

            for ($r = 8; $r < $row; $r++) {
                $sheet->getRowDimension($r)->setRowHeight(25);
            }

            $lastRow = $row - 1;
            $sheet->getStyle('A8:' . $getColLetter(28) . $lastRow)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $sheet->getStyle('A1:' . $getColLetter(28) . $lastRow)->getFont()->setName('Arial');
            $sheet->getStyle('A1:' . $getColLetter(28) . $lastRow)->getFont()->setSize(11);

            // Ensure A6 is left aligned
            $sheet->getStyle('A6')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

            // Set zoom scale to 70%
            $sheet->getSheetView()->setZoomScale(70);

            // Download
            $filename = 'IMPRS_' . $indicatorName . '_' . $tahun . '_' . date('YmdHis');
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
            header('Cache-Control: max-age=0');

            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save('php://output');
            exit;
        } catch (\Exception $e) {
            log_message('error', 'Export Excel Indicator Error: ' . $e->getMessage());
            echo 'Error: ' . $e->getMessage();
            exit;
        }
    }

    private function hitungPeriode($num, $den, $start, $end)
    {
        $totalNum = 0;
        $totalDen = 0;
        for ($i = $start; $i <= $end; $i++) {
            $totalNum += $num[$i];
            $totalDen += $den[$i];
        }
        if ($totalDen == 0) return null;
        return ($totalNum / $totalDen) * 100;
    }

    /**
     * Menentukan status capaian PMKP berdasarkan nilai realisasi, target, dan operator.
     *
     * @param float|null $capaian Nilai capaian tahunan (dalam persen)
     * @param float $target Nilai target
     * @param string $operator Operator perbandingan ('>=', '<=', '>', '<', '=')
     * @return string 'TERCAPAI', 'TIDAK TERCAPAI', atau 'TIDAK ADA DATA'
     */
    private function getStatusPMKP(?float $capaian, float $target, string $operator): string
    {
        if ($capaian === null) {
            return 'TIDAK ADA DATA';
        }

        switch ($operator) {
            case '>=':
                return $capaian >= $target ? 'TERCAPAI' : 'TIDAK TERCAPAI';
            case '<=':
                return $capaian <= $target ? 'TERCAPAI' : 'TIDAK TERCAPAI';
            case '>':
                return $capaian > $target ? 'TERCAPAI' : 'TIDAK TERCAPAI';
            case '<':
                return $capaian < $target ? 'TERCAPAI' : 'TIDAK TERCAPAI';
            case '=':
                return $capaian == $target ? 'TERCAPAI' : 'TIDAK TERCAPAI';
            default:
                return 'TIDAK ADA DATA';
        }
    }
}
