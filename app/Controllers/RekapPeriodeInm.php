<?php

namespace App\Controllers;

use App\Models\SiimutMenuModel;
use App\Models\RekapLaporanInmModel;

class RekapPeriodeInm extends AppController
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

        return $this->render('siimut/rekap_periode_inm', [
            'judul'    => 'Rekap INM per Periode',
            'icon'     => '<i class="bi bi-calendar-range"></i>',
            '_content' => view('siimut/rekap_periode_inm', [
                'tahun' => $tahun,
            ]),
            'menus'    => $menus
        ]);
    }

    public function getAjaxRekapPeriode($tahun = null)
    {
        $post = $this->request->getPost();
        
        // Get tahun from route parameter first, then POST, then default to current year
        $tahun = $tahun ? (int) $tahun : (isset($post['tahun']) ? (int) $post['tahun'] : (int) date('Y'));

        log_message('error', 'REKAP PERIODE INM: tahun=' . $tahun);

        try {
            $data = $this->rekapModel->getRekapPeriode($tahun);

            log_message('error', 'REKAP PERIODE INM: data count=' . count($data));

            if (empty($data)) {
                log_message('error', 'REKAP PERIODE INM: WARNING - No data returned');
            } else {
                $firstIndicator = $data[0];
                log_message('error', 'REKAP PERIODE INM: first indicator=' . json_encode($firstIndicator));
            }

            return $this->response->setJSON([
                'draw' => $post['draw'] ?? 1,
                'recordsTotal' => count($data),
                'recordsFiltered' => count($data),
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            log_message('error', 'REKAP PERIODE INM ERROR: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'draw' => $post['draw'] ?? 1,
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => $e->getMessage()
            ]);
        }
    }

    public function exportExcel()
    {
        $tahun = $this->request->getGet('tahun') ?? date('Y');
        
        log_message('error', 'EXPORT EXCEL PERIODE: tahun=' . $tahun);
        
        if (ob_get_level()) {
            ob_end_clean();
        }
        
        try {
            $data = $this->rekapModel->getRekapPeriode($tahun);
            
            log_message('error', 'EXPORT EXCEL PERIODE: data count=' . count($data));
            if (!empty($data)) {
                log_message('error', 'EXPORT EXCEL PERIODE: first item=' . json_encode($data[0]));
            }
            
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
            
            // ==================== HEADER ====================
            $sheet->setCellValue('A1', 'REKAP INDIKATOR NASIONAL MUTU PER PERIODE');
            $sheet->mergeCells('A1:L1');
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
            $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            
            $sheet->setCellValue('A2', 'RSUD dr. SOEDONO PROVINSI JAWA TIMUR');
            $sheet->mergeCells('A2:L2');
            $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(12);
            $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            
            $sheet->setCellValue('A3', 'TAHUN ' . $tahun);
            $sheet->mergeCells('A3:L3');
            $sheet->getStyle('A3')->getFont()->setBold(true)->setSize(12);
            $sheet->getStyle('A3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            
            // ==================== HEADER TABLE ====================
            $row = 5;
            $sheet->setCellValue('A' . $row, 'No.');
            $sheet->setCellValue('B' . $row, 'Indikator');
            $sheet->setCellValue('C' . $row, 'Target');
            $sheet->setCellValue('D' . $row, 'TW 1');
            $sheet->setCellValue('E' . $row, 'TW 2');
            $sheet->setCellValue('F' . $row, 'SM 1');
            $sheet->setCellValue('G' . $row, 'TW 3');
            $sheet->setCellValue('H' . $row, 'TW 4');
            $sheet->setCellValue('I' . $row, 'SM 2');
            $sheet->setCellValue('J' . $row, 'Thn');
            $sheet->setCellValue('K' . $row, 'Status');
            
            foreach (range('A', 'K') as $col) {
                $sheet->getStyle($col . $row)->getFont()->setBold(true);
                $sheet->getStyle($col . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle($col . $row)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            }
            
            // ==================== DATA ====================
            $row = 6;
            $no = 1;
            
            foreach ($data as $item) {
                $sheet->setCellValue('A' . $row, $no);
                $sheet->setCellValue('B' . $row, $item['indicator_element'] ?? '-');
                $sheet->setCellValue('C' . $row, ($item['target'] ?? '-') . '%');
                
                // TW 1-4
                $sheet->setCellValue('D' . $row, isset($item['triwulan'][1]['nilai']) ? $item['triwulan'][1]['nilai'] . '%' : '-');
                $sheet->setCellValue('E' . $row, isset($item['triwulan'][2]['nilai']) ? $item['triwulan'][2]['nilai'] . '%' : '-');
                $sheet->setCellValue('G' . $row, isset($item['triwulan'][3]['nilai']) ? $item['triwulan'][3]['nilai'] . '%' : '-');
                $sheet->setCellValue('H' . $row, isset($item['triwulan'][4]['nilai']) ? $item['triwulan'][4]['nilai'] . '%' : '-');
                
                // SM 1-2
                $sheet->setCellValue('F' . $row, isset($item['semester'][1]['nilai']) ? $item['semester'][1]['nilai'] . '%' : '-');
                $sheet->setCellValue('I' . $row, isset($item['semester'][2]['nilai']) ? $item['semester'][2]['nilai'] . '%' : '-');
                
                // Tahun & Status
                $sheet->setCellValue('J' . $row, isset($item['tahun']['nilai']) ? $item['tahun']['nilai'] . '%' : '-');
                $sheet->setCellValue('K' . $row, $item['tahun']['status'] ?? '-');
                
                // Style center
                foreach (range('D', 'K') as $col) {
                    $sheet->getStyle($col . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                }
                
                // Borders
                foreach (range('A', 'K') as $col) {
                    $sheet->getStyle($col . $row)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                }
                
                $row++;
                $no++;
            }
            
            // ==================== STYLE ====================
            $sheet->getColumnDimension('A')->setWidth(5);
            $sheet->getColumnDimension('B')->setWidth(35);
            $sheet->getColumnDimension('C')->setWidth(8);
            foreach (range('D', 'K') as $col) {
                $sheet->getColumnDimension($col)->setWidth(8);
            }
            
            $lastRow = $row - 1;
            if ($lastRow >= 5) {
                $sheet->getStyle('A1:K' . $lastRow)->getFont()->setName('Arial')->setSize(10);
            }
            
            // ==================== DOWNLOAD ====================
            $filename = 'REKAP_PERIODE_INM_' . $tahun . '_' . date('YmdHis');
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
            header('Cache-Control: max-age=0');
            
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save('php://output');
            exit;
        } catch (\Exception $e) {
            log_message('error', 'Export Excel Rekap Periode Error: ' . $e->getMessage());
            echo 'Error: ' . $e->getMessage();
            exit;
        }
    }
}