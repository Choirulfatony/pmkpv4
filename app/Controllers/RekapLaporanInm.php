<?php

namespace App\Controllers;

use App\Models\SiimutMenuModel;
use App\Models\RekapLaporanInmModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class RekapLaporanInm extends AppController
{
    protected $rekapModel;

    public function __construct()
    {
        $this->rekapModel = new RekapLaporanInmModel();
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
            $detail = $this->rekapModel->getDetailByIdInm((int) $indicatorId);
            return $this->render('siimut/rekap_laporan_detail', [
                'judul'       => 'Detail Rekap INM',
                'icon'        => '<i class="bi bi-bar-chart"></i>',
                '_content'    => view('siimut/rekap_laporan_detail', [
                    'tahun'        => $tahun,
                    'indicatorId'  => $indicatorId,
                    'detail'       => $detail,
                ]),
                'menus'       => $menus
            ]);
        }

        // Default: tampilkan rekap utama
        return $this->render('siimut/rekap_laporan_inm', [
            'judul'    => 'Rekap Laporan INM',
            'icon'     => '<i class="bi bi-bar-chart"></i>',
            '_content' => view('siimut/rekap_laporan_inm', [
                'tahun' => $tahun,
            ]),
            'menus'    => $menus
        ]);
    }

    /**
     * AJAX: Ambil data rekap INM (untuk DataTables) - Optimized
     */
    public function getAjaxDataRekapInm()
    {
        $post = $this->request->getPost();
        
        log_message('error', 'getAjaxDataRekapInm called, post: ' . json_encode($post));
        
        if (!$post) {
            log_message('error', 'getAjaxDataRekapInm - POST is empty or null');
            return $this->response->setJSON(['error' => 'Invalid request - no POST data', 'post_data' => $post]);
        }

        $indicators = $this->rekapModel->getIndicatorInm($post);
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
                <a href="javascript:void(0);" class="fw-semibold text-decoration-none" title="Detail Rekapan Ruangan" onclick="view_detail_inm(' . $indicator->indicator_id . ');">' 
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
            'recordsTotal'    => $this->rekapModel->countAllRekapInm($post),
            'recordsFiltered' => $this->rekapModel->countFilteredRekapInm($post),
            'data'            => $data,
        ]);
    }

    /**
     * AJAX: Detail INM per ruangan (untuk DataTables)
     */
    public function getAjaxDataRekapInmDetail()
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
            $indicatorInfo = $this->rekapModel->getDetailByIdInm($indicatorId);
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
     * AJAX: Detail INM
     */
    public function viewDetailInm($indicatorId = null)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Invalid request']);
        }

        $data = $this->rekapModel->getDetailByIdInm($indicatorId);
        if ($data) {
            return $this->response->setJSON($data);
        }
        return $this->response->setJSON(['status' => false]);
    }

/**
     * Export Excel Rekap INM
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
            
            // Header
            $sheet->setCellValue('A1', 'LAPORAN INDIKATOR NASIONAL MUTU (INM)');
            $sheet->setCellValue('A2', 'Tahun: ' . $tahun);
            $sheet->setCellValue('A3', 'RSUD Dr. Soetomo');
            
            // Style header
            $sheet->mergeCells('A1:O1');
            $sheet->mergeCells('A2:O2');
            $sheet->mergeCells('A3:O3');
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
            $sheet->getStyle('A2')->getFont()->setSize(12);
            $sheet->getStyle('A3')->getFont()->setSize(12);
            $sheet->getStyle('A1:O3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            
            // Headers tabel - tanpa TOTAL
            $headers = ['No', 'Indikator', 'Target', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
            $col = 'A';
            foreach ($headers as $header) {
                $sheet->setCellValue($col . '5', $header);
                $col++;
            }
            
            // Style header tabel
            $sheet->getStyle('A5:O5')->getFont()->setBold(true);
            $sheet->getStyle('A5:O5')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
            $sheet->getStyle('A5:O5')->getFill()->getStartColor()->setRGB('28A745');
            $sheet->getStyle('A5:O5')->getFont()->getColor()->setRGB('FFFFFF');
            $sheet->getStyle('A5:O5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            
            // Border header
            $sheet->getStyle('A5:O5')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            
            // Data
            $indicators = $this->rekapModel->getIndicatorInm(['vtahun' => $tahun]);
            $indicatorIds = array_column($indicators, 'indicator_id');
            $allData = $this->rekapModel->getAllMonthlyData($indicatorIds, $tahun);
            
            $no = 1;
            $row = 6;
            foreach ($indicators as $ind) {
                $sheet->setCellValue('A' . $row, $no);
                $sheet->setCellValue('B' . $row, $ind->indicator_element);
                $sheet->setCellValue('C' . $row, $ind->indicator_target . ' ' . $ind->indicator_units);
                
                // Data bulan
                for ($bulan = 1; $bulan <= 12; $bulan++) {
                    $key = $ind->indicator_id . '_' . $bulan;
                    $val = isset($allData[$key]) ? $allData[$key] : null;
                    $col = chr(67 + $bulan); // D=4, E=5, F=6, ... O=15
                    
                    if ($val && $val->num > 0 && $val->denum > 0) {
                        $nilai = $val->total_value ?? 0;
                        $sheet->setCellValue($col . $row, $nilai);
                    } else {
                        $sheet->setCellValue($col . $row, '-');
                    }
                }
                
                // Border row
                $sheet->getStyle('A' . $row . ':O' . $row)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                
                $no++;
                $row++;
            }
            
            // Auto size columns
            foreach (range('A', 'O') as $colID) {
                $sheet->getColumnDimension($colID)->setAutoSize(true);
            }
            
            // Download
            $filename = 'INM_' . $tahun . '_' . date('YmdHis');
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
}
