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

        if ($indicatorId) {
            $detail = $this->rekapModel->getDetailByIdImprs((int) $indicatorId);
            return $this->render('siimut/rekap_laporan_imprs_detail', [
                'judul'       => 'Detail Rekap IMPRS',
                'icon'        => '<i class="bi bi-bar-chart"></i>',
                '_content'    => view('siimut/rekap_laporan_imprs_detail', [
                    'tahun'        => $tahun,
                    'indicatorId'  => $indicatorId,
                    'detail'       => $detail,
                ]),
                'menus'       => $menus
            ]);
        }

        return $this->render('siimut/rekap_laporan_imprs', [
            'judul'    => 'Rekap Laporan IMPRS',
            'icon'     => '<i class="bi bi-bar-chart"></i>',
            '_content' => view('siimut/rekap_laporan_imprs', [
                'tahun' => $tahun,
            ]),
            'menus'    => $menus
        ]);
    }

    public function getAjaxDataRekapImprs()
    {
        $post = $this->request->getPost();
        
        log_message('error', 'getAjaxDataRekapImprs called, post: ' . json_encode($post));
        
        if (!$post) {
            log_message('error', 'getAjaxDataRekapImprs - POST is empty or null');
            return $this->response->setJSON(['error' => 'Invalid request - no POST data', 'post_data' => $post]);
        }

        $indicators = $this->rekapModel->getIndicatorImprs($post);
        $tahun = isset($post['vtahun']) ? (int) $post['vtahun'] : (int) date('Y');
        
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
            'recordsTotal'    => $this->rekapModel->countAllRekapImprs($post),
            'recordsFiltered' => $this->rekapModel->countFilteredRekapImprs($post),
            'data'            => $data,
        ]);
    }

    public function getAjaxDataRekapImprsDetail()
    {
        try {
            $post = $this->request->getPost();
            
            log_message('error', 'IMPRS DETAIL called: ' . json_encode($post));
            
            if (!$post) {
                return $this->response->setJSON(['error' => 'Invalid request']);
            }

            $indicatorId = isset($post['indicator_id']) ? (int) $post['indicator_id'] : 0;
            $tahun = isset($post['vtahun']) ? (int) $post['vtahun'] : (int) date('Y');
            
            log_message('error', 'IMPRS DETAIL: indicatorId=' . $indicatorId . ', tahun=' . $tahun);
            
            if ($indicatorId == 0) {
                return $this->response->setJSON(['error' => 'Invalid indicator_id']);
            }

            $departments = $this->rekapModel->getDepartmentsByIndicatorImprs($indicatorId, $tahun, $post);
            log_message('error', 'IMPRS DETAIL: departments count=' . count($departments));
            
            $allDetailData = $this->rekapModel->getAllDetailData($indicatorId, $tahun);
            log_message('error', 'IMPRS DETAIL: allDetailData count=' . count($allDetailData));
            
            $indicatorInfo = $this->rekapModel->getDetailByIdImprs($indicatorId);
            $target = $indicatorInfo ? ($indicatorInfo->indicator_target ?? 0) : 0;
            $factors = $indicatorInfo ? ($indicatorInfo->indicator_factors ?? 1) : 1;
            $operatorCalc = $indicatorInfo ? ($indicatorInfo->indicator_target_calculation ?? '>=') : '>=';
            $units = $indicatorInfo ? ($indicatorInfo->indicator_units ?? '%') : '%';
            
            log_message('error', 'IMPRS DETAIL: indicatorInfo target=' . $target . ', factor=' . $factors . ', operator=' . $operatorCalc);

            $data = [];
            $no = isset($post['start']) ? (int) $post['start'] : 0;

            foreach ($departments as $dept) {
                $no++;
                $row = [];

                $row[] = '<div class="fw-bold">' . $no . '</div>';
                $row[] = '<div class="py-1 text-start ps-2">' . esc($dept->department_name) . '</div>';

                $row[] = '<div class="py-1">
                    <span id="target_det">' . esc($target) . '</span>
                    <span class="small text-muted ms-1">' . esc($units) . '</span>
                    <span id="factor_det" style="display:none">' . esc($factors) . '</span>
                    <span id="operator_det" style="display:none">' . esc($operatorCalc) . '</span>
                </div>';

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
            log_message('error', 'IMPRS DETAIL ERROR: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
            return $this->response->setStatusCode(500)->setJSON([
                'error' => $e->getMessage()
            ]);
        }
    }

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

    public function exportExcel()
    {
        $tahun = $this->request->getGet('tahun') ?? date('Y');
        
        if (ob_get_level()) {
            ob_end_clean();
        }
        
        try {
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            $getColLetter = function($colIdx) {
                $letter = '';
                while ($colIdx > 0) {
                    $mod = ($colIdx - 1) % 26;
                    $letter = chr(65 + $mod) . $letter;
                    $colIdx = (int)(($colIdx - $mod) / 26);
                }
                return $letter;
            };
            
            // Header
            $sheet->setCellValue('A1', 'CAPAIAN INDIKATOR MUTU PRIORITAS RS (IMPRS)');
            $sheet->mergeCells('A1:AB1');
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
            $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('A1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            
            $sheet->setCellValue('A2', 'RSUD dr. SOEDONO PROVINSI JAWA TIMUR');
            $sheet->mergeCells('A2:AB2');
            $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(12);
            $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('A2')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            
            $sheet->setCellValue('A3', 'TAHUN ' . $tahun);
            $sheet->mergeCells('A3:AB3');
            $sheet->getStyle('A3')->getFont()->setBold(true)->setSize(12);
            $sheet->getStyle('A3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('A3')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            
            // Header Tabel
            $sheet->setCellValue('A5', 'No.');
            $sheet->setCellValue('B5', 'Judul Indikator Mutu Prioritas RS');
            $sheet->setCellValue('C5', 'Standar');
            $sheet->setCellValue('D5', 'Num/Denum');
            $sheet->setCellValue('E5', 'BULAN');
            $sheet->mergeCells('E5:AB5');
            $sheet->mergeCells('A5:A6');
            $sheet->mergeCells('B5:B6');
            $sheet->mergeCells('C5:C6');
            $sheet->mergeCells('D5:D6');
            
            foreach (['A5', 'B5', 'C5', 'D5'] as $cell) {
                $sheet->getStyle($cell)->getFont()->setBold(true);
                $sheet->getStyle($cell)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle($cell)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getStyle($cell)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            }
            
            $sheet->getStyle('D5')->getAlignment()->setWrapText(true);
            $sheet->getStyle('E5')->getFont()->setBold(true);
            $sheet->getStyle('E5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('E5')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            
            // Sub-header bulan
            $bulanNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            $colStart = 5;
            
            for ($i = 0; $i < count($bulanNames); $i++) {
                $colIdx = $colStart + ($i * 2);
                $colIdx2 = $colIdx + 1;
                $col1 = $getColLetter($colIdx);
                $col2 = $getColLetter($colIdx2);
                
                $sheet->setCellValue($col1 . '6', $bulanNames[$i]);
                $sheet->getStyle($col1 . '6')->getFont()->setBold(true);
                $sheet->getStyle($col1 . '6')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle($col1 . '6')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

                $sheet->setCellValue($col2 . '6', 'Capaian');
                $sheet->getStyle($col2 . '6')->getFont()->setBold(true);
                $sheet->getStyle($col2 . '6')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle($col2 . '6')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            }
            
            // Data
            $indicators = $this->rekapModel->getIndicatorImprs(['vtahun' => $tahun]);
            $indicatorIds = array_column($indicators, 'indicator_id');
            $allData = $this->rekapModel->getAllMonthlyData($indicatorIds, $tahun);
            
            $no = 1;
            $row = 7;
            
            foreach ($indicators as $ind) {
                $sheet->setCellValue('A' . $row, $no);
                $sheet->setCellValue('B' . $row, $ind->indicator_element);
                
                $targetVal = $ind->indicator_target ?? '';
                $operatorRaw = $ind->operator ?? '';
                $operator = trim($operatorRaw);
                $factorsVal = $ind->factors ?? '';
                $units = $ind->indicator_units ?? '%';

                if ($operator === '=' && !empty($factorsVal)) {
                    $displayVal = $factorsVal;
                } else {
                    $displayVal = $targetVal;
                }

                if ($operator === '=') {
                    $standar = $displayVal . ' ' . $units;
                } else {
                    $standar = $operator . ' ' . $displayVal . ' ' . $units;
                }
                $sheet->setCellValue('C' . $row, trim($standar));
                $sheet->setCellValue('D' . $row, 'Num');
                
                $sheet->mergeCells('B' . $row . ':B' . ($row + 1));
                $sheet->getStyle('B' . $row)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getStyle('B' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle('B' . $row)->getAlignment()->setWrapText(true);
                
                $sheet->mergeCells('C' . $row . ':C' . ($row + 1));
                $sheet->getStyle('C' . $row)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getStyle('C' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('C' . $row)->getAlignment()->setWrapText(true);
                
                $sheet->mergeCells('A' . $row . ':A' . ($row + 1));
                $sheet->getStyle('A' . $row)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A' . $row)->getAlignment()->setWrapText(true);
                
                $capaianData = [];
                for ($bulan = 1; $bulan <= 12; $bulan++) {
                    $key = $ind->indicator_id . '_' . $bulan;
                    $val = isset($allData[$key]) ? $allData[$key] : null;
                    
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
                        $nilai = number_format($val->total_value ?? 0, 2);
                        $capaianData[$col2] = $nilai . '%';
                    }
                }
                
                $sheet->getStyle('A' . $row . ':D' . $row)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                $sheet->getStyle('A' . $row . ':D' . $row)->getFont()->setBold(true);
                
                $row++;
                $sheet->setCellValue('D' . $row, 'Denum');
                $sheet->getStyle('D' . $row)->getFont()->setBold(true);
                
                for ($bulan = 1; $bulan <= 12; $bulan++) {
                    $key = $ind->indicator_id . '_' . $bulan;
                    $val = isset($allData[$key]) ? $allData[$key] : null;
                    
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
            
            // Column widths
            $sheet->getColumnDimension('A')->setWidth(5);
            $sheet->getColumnDimension('B')->setWidth(70);
            $sheet->getColumnDimension('C')->setWidth(12);
            $sheet->getColumnDimension('D')->setWidth(10);
            $sheet->getStyle('D')->getAlignment()->setWrapText(true);
            
            for ($r = 6; $r < $row; $r++) {
                $sheet->getRowDimension($r)->setRowHeight(30);
            }
            
            for ($i = 5; $i <= 28; $i++) {
                $colLetter = $getColLetter($i);
                $sheet->getColumnDimension($colLetter)->setWidth(12);
            }
            
            $lastRow = $row - 1;
            if ($lastRow >= 5) {
                $sheet->getStyle('A5:' . $getColLetter(28) . $lastRow)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            }
            
            $sheet->getStyle('A1:' . $getColLetter(28) . $lastRow)->getFont()->setName('Arial');
            $sheet->getStyle('A1:' . $getColLetter(28) . $lastRow)->getFont()->setSize(11);
            $sheet->getSheetView()->setZoomScale(70);
            
            // Download
            $filename = 'CAPAIAN IMPRS ' . $tahun . ' ' . date('YmdHis');
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
            header('Cache-Control: max-age=0');
            
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save('php://output');
            exit;
        } catch (\Exception $e) {
            log_message('error', 'Export Excel IMPRS Error: ' . $e->getMessage());
            echo 'Error: ' . $e->getMessage();
            exit;
        }
    }

    public function exportExcelIndicator($indicatorId)
    {
        $tahun = $this->request->getGet('tahun') ?? date('Y');
        
        if (ob_get_level()) {
            ob_end_clean();
        }
        
        try {
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            
            $getColLetter = function($colIdx) {
                $letter = '';
                while ($colIdx > 0) {
                    $mod = ($colIdx - 1) % 26;
                    $letter = chr(65 + $mod) . $letter;
                    $colIdx = (int)(($colIdx - $mod) / 26);
                }
                return $letter;
            };
            
            $indicator = $this->rekapModel->getDetailByIdImprs((int) $indicatorId);
            $indicatorName = $indicator->indicator_element ?? 'Detail';
            
            $departments = $this->rekapModel->getDepartmentsByIndicatorImprs((int) $indicatorId, $tahun, []);
            $allDetailData = $this->rekapModel->getAllDetailData((int) $indicatorId, $tahun);

            $targetVal = $indicator->indicator_target ?? '';
            $operator = trim($indicator->indicator_target_calculation ?? '');
            $factorsVal = $indicator->indicator_factors ?? '';
            $units = $indicator->indicator_units ?? '%';

            if ($operator === '=') {
                $standar = $factorsVal . ' ' . $units;
            } else {
                $standar = $operator . ' ' . $targetVal . ' ' . $units;
            }

            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Detail ' . substr($indicatorName, 0, 20));

            $sheet->setCellValue('A1', 'CAPAIAN INDIKATOR MUTU PRIORITAS RS (IMPRS)');
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
            $sheet->getStyle('E8:AB9')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

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
            $sheet->getStyle('A6')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
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
            log_message('error', 'Export Excel IMPRS Indicator Error: ' . $e->getMessage());
            echo 'Error: ' . $e->getMessage();
            exit;
        }
    }

    private function hitungPeriode($num, $den, $start, $end) {
        $totalNum = 0;
        $totalDen = 0;
        for ($i = $start; $i <= $end; $i++) {
            $totalNum += $num[$i];
            $totalDen += $den[$i];
        }
        if ($totalDen == 0) return null;
        return ($totalNum / $totalDen) * 100;
    }

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
