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

        if (ob_get_level()) {
            ob_end_clean();
        }

        try {
            $data = $this->rekapModel->getRekapPeriode($tahun);

            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

            // ==================== SHEET 1: TRIWULAN ====================
            $sheet1 = $spreadsheet->getActiveSheet();
            $sheet1->setTitle('Triwulan');

            $this->buildSheet($sheet1, $data, 'triwulan', $tahun);

            // ==================== SHEET 2: SEMESTER ====================
            $sheet2 = $spreadsheet->createSheet();
            $sheet2->setTitle('Semester');

            $this->buildSheet($sheet2, $data, 'semester', $tahun);

            // ==================== SHEET 3: TAHUN ====================
            $sheet3 = $spreadsheet->createSheet();
            $sheet3->setTitle('Tahun');

            $this->buildSheet($sheet3, $data, 'tahun', $tahun);

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

    private function val($value)
    {
        return ($value === null || $value === '' || $value == 0) ? '-' : $value;
    }

    private function buildSheet($sheet, $data, $type, $tahun)
    {
        // HEADER
        $sheet->setCellValue('A1', 'REKAP INDIKATOR NASIONAL MUTU (INM) - TRIWULAN');
        $sheet->mergeCells('A1:O1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('A2', 'RSUD dr. SOEDONO PROVINSI JAWA TIMUR');
        $sheet->mergeCells('A2:O2');
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('A3', 'TAHUN ' . $tahun);
        $sheet->mergeCells('A3:O3');
        $sheet->getStyle('A3')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // HEADER TABLE - ROW 5
        $row = 5;
        $sheet->setCellValue('A' . $row, 'No.');
        $sheet->setCellValue('B' . $row, 'Indikator');
        $sheet->setCellValue('C' . $row, 'Standar');
        $sheet->setCellValue('D' . $row, 'Num / Denum');


        if ($type === 'triwulan') {
            // TW 1
            $sheet->mergeCells('E5:F5');
            $sheet->setCellValue('E5', 'TW 1');

            // CENTER (horizontal + vertical)
            $sheet->getStyle('E5')->getAlignment()->setHorizontal(
                \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            );
            $sheet->getStyle('E5')->getAlignment()->setVertical(
                \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            );

            // BOLD (biar tegas)
            $sheet->getStyle('E5')->getFont()->setBold(true);

            // BORDER (block kotak)
            $sheet->getStyle('E5:F5')->getBorders()->getAllBorders()->setBorderStyle(
                \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
            );

            // TW 2
            $sheet->mergeCells('H5:I5');
            $sheet->setCellValue('H5', 'TW 2');

            // CENTER (horizontal + vertical)
            $sheet->getStyle('H5')->getAlignment()->setHorizontal(
                \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            );
            $sheet->getStyle('H5')->getAlignment()->setVertical(
                \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            );

            // BOLD (biar tegas)
            $sheet->getStyle('H5')->getFont()->setBold(true);

            // BORDER (block kotak)
            $sheet->getStyle('H5:I5')->getBorders()->getAllBorders()->setBorderStyle(
                \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
            );

            // TW 3
            $sheet->mergeCells('K5:L5');
            $sheet->setCellValue('K5', 'TW 3');

            // CENTER (horizontal + vertical)
            $sheet->getStyle('K5')->getAlignment()->setHorizontal(
                \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            );
            $sheet->getStyle('K5')->getAlignment()->setVertical(
                \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            );

            // BOLD (biar tegas)
            $sheet->getStyle('K5')->getFont()->setBold(true);

            // BORDER (block kotak)
            $sheet->getStyle('K5:L5')->getBorders()->getAllBorders()->setBorderStyle(
                \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
            );

            // TW 4
            $sheet->mergeCells('N5:O5');
            $sheet->setCellValue('N5', 'TW 4');

            // CENTER (horizontal + vertical)
            $sheet->getStyle('N5')->getAlignment()->setHorizontal(
                \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            );
            $sheet->getStyle('N5')->getAlignment()->setVertical(
                \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            );

            // BOLD (biar tegas)
            $sheet->getStyle('N5')->getFont()->setBold(true);

            // BORDER (block kotak)
            $sheet->getStyle('N5:O5')->getBorders()->getAllBorders()->setBorderStyle(
                \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
            );

            $lastCol = 'O';
        } elseif ($type === 'semester') {

            // SM 1
            $sheet->mergeCells('E5:F5');
            $sheet->setCellValue('E5', 'SM 1');

            // SM 2
            $sheet->mergeCells('H5:I5');
            $sheet->setCellValue('H5', 'SM 2');

            // STYLE
            foreach (['E5:F5', 'H5:I5'] as $range) {
                $cell = explode(':', $range)[0];

                $sheet->getStyle($cell)->getAlignment()->setHorizontal(
                    \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                );
                $sheet->getStyle($cell)->getAlignment()->setVertical(
                    \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
                );
                $sheet->getStyle($cell)->getFont()->setBold(true);

                $sheet->getStyle($range)->getBorders()->getAllBorders()->setBorderStyle(
                    \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                );
            }

            $lastCol = 'I';
        } else {

            // Tahun
            $sheet->mergeCells('E5:F5');
            $sheet->setCellValue('E5', 'Tahun');

            // STYLE
            $sheet->getStyle('E5')->getAlignment()->setHorizontal(
                \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            );
            $sheet->getStyle('E5')->getAlignment()->setVertical(
                \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            );
            $sheet->getStyle('E5')->getFont()->setBold(true);

            $sheet->getStyle('E5:F5')->getBorders()->getAllBorders()->setBorderStyle(
                \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
            );

            $lastCol = 'F';
        }

        // Merge basic columns (A-C) for 2 rows
        $sheet->mergeCells('A5:A6');
        $sheet->mergeCells('B5:B6');
        $sheet->mergeCells('C5:C6');
        $sheet->mergeCells('D5:D6');

        // Style row 5 basic columns
        foreach (['A5', 'B5', 'C5', 'D5'] as $cell) {
            $sheet->getStyle($cell)->getFont()->setBold(true);
            $sheet->getStyle($cell)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle($cell)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            $sheet->getStyle($cell)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $sheet->getStyle('D5')->getAlignment()->setWrapText(true);
        }

        $sheet->getStyle('C5')->getAlignment()->setWrapText(true);

        // Style period headers (individual cells in row 5)
        if ($type === 'triwulan') {
            foreach (['D5', 'G5', 'J5', 'M5'] as $cell) {
                $sheet->getStyle($cell)->getFont()->setBold(true);
                $sheet->getStyle($cell)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle($cell)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            }
        } elseif ($type === 'semester') {
            foreach (['D5', 'G5'] as $cell) {
                $sheet->getStyle($cell)->getFont()->setBold(true);
                $sheet->getStyle($cell)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle($cell)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            }
        } else {
            $sheet->getStyle('D5')->getFont()->setBold(true);
            $sheet->getStyle('D5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('D5')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        }

        // ROW 6 - Sub headers: Num, Nilai Total, %
        $row = 6;

        if ($type === 'triwulan') {
            // TW1: D=Num, E=Nilai Total, F=%
            $sheet->setCellValue('E' . $row, 'Nilai Total');
            $sheet->getStyle('E' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            $sheet->setCellValue('F' . $row, 'Capaian');

            // TW2: G=Num, H=Nilai Total, I=%
            $sheet->setCellValue('H' . $row, 'Nilai Total');
            $sheet->getStyle('H' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            $sheet->setCellValue('I' . $row, 'Capaian');

            // TW3: J=Num, K=Nilai Total, L=%
            $sheet->setCellValue('K' . $row, 'Nilai Total');
            $sheet->getStyle('K' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            $sheet->setCellValue('L' . $row, 'Capaian');

            // TW4: M=Num, N=Nilai Total, O=%
            $sheet->setCellValue('N' . $row, 'Nilai Total');
            $sheet->getStyle('N' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            $sheet->setCellValue('O' . $row, 'Capaian');

            $lastCol = 'O';
        } elseif ($type === 'semester') {
            // SM1: D=Num, E=Nilai Total, F=%
            $sheet->setCellValue('E' . $row, 'Nilai Total');
            $sheet->getStyle('E' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            $sheet->setCellValue('F' . $row, 'Capaian');

            // SM2: G=Num, H=Nilai Total, I=%
            $sheet->setCellValue('H' . $row, 'Nilai Total');
            $sheet->getStyle('H' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            $sheet->setCellValue('I' . $row, 'Capaian');

            $lastCol = 'I';
        } else {
            // Tahun: D=Num, E=Nilai Total, F=%
            $sheet->setCellValue('E' . $row, 'Nilai Total');
            $sheet->getStyle('E' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            $sheet->setCellValue('F' . $row, 'Capaian');

            $lastCol = 'F';
        }

        // Style row 6 headers (Num, Nilai Total, %)
        foreach (range('A', $lastCol) as $col) {
            if (!$sheet->getStyle($col . $row)->getFont()->getBold()) {
                $sheet->getStyle($col . $row)->getFont()->setBold(true);
            }
            $sheet->getStyle($col . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            $sheet->getStyle($col . $row)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        }

        // DATA - start from row 7
        $row = 7;
        $no = 1;

        foreach ($data as $item) {
            // Row 2 for Denum (will be updated after setting row 1)
            $row2 = $row + 1;

            // =========================
            // BARIS 1 (NUM)
            // =========================
            $sheet->setCellValue('A' . $row, $no);
            $sheet->setCellValue('B' . $row, $item['indicator_element'] ?? '-');
            $sheet->setCellValue('C' . $row, ($item['target'] ?? '-') . '%');

            if ($type === 'triwulan') {
                // TW1: D=Num/Denum label, E=num value, F=%(merged)
                $tw1 = $item['triwulan'][1] ?? [];
                $sheet->setCellValue('D' . $row, 'Num');
                $sheet->setCellValue('E' . $row, $this->val($tw1['num'] ?? null));
                $sheet->getStyle('E' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->setCellValue('F' . $row, isset($tw1['nilai']) ? $tw1['nilai'] . '%' : '-');

                // Row 8 - Denum
                $sheet->setCellValue('D' . $row2, 'Denum');
                $sheet->setCellValue('E' . $row2, $this->val($tw1['denum'] ?? null));
                $sheet->getStyle('E' . $row2)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->setCellValue('F' . $row2, '');

                // TW2: G=Num/Denum label, H=num value, I=%(merged)
                $tw2 = $item['triwulan'][2] ?? [];
                $sheet->setCellValue('G' . $row, 'Num');
                $sheet->setCellValue('H' . $row, $this->val($tw2['num'] ?? null));
                $sheet->getStyle('H' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->setCellValue('I' . $row, isset($tw2['nilai']) ? $tw2['nilai'] . '%' : '-');

                $sheet->setCellValue('G' . $row2, 'Denum');
                $sheet->setCellValue('H' . $row2, $this->val($tw2['denum'] ?? null));
                $sheet->getStyle('H' . $row2)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->setCellValue('I' . $row2, '');

                // TW3: J=Num/Denum label, K=num value, L=%(merged)
                $tw3 = $item['triwulan'][3] ?? [];
                $sheet->setCellValue('J' . $row, 'Num');
                $sheet->setCellValue('K' . $row, $this->val($tw3['num'] ?? null));
                $sheet->getStyle('K' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->setCellValue('L' . $row, isset($tw3['nilai']) ? $tw3['nilai'] . '%' : '-');

                $sheet->setCellValue('J' . $row2, 'Denum');
                $sheet->setCellValue('K' . $row2, $this->val($tw3['denum'] ?? null));
                $sheet->getStyle('K' . $row2)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->setCellValue('L' . $row2, '');

                // TW4: M=Num/Denum label, N=num value, O=%(merged)
                $tw4 = $item['triwulan'][4] ?? [];
                $sheet->setCellValue('M' . $row, 'Num');
                $sheet->setCellValue('N' . $row, $this->val($tw4['num'] ?? null));
                $sheet->getStyle('N' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->setCellValue('O' . $row, isset($tw4['nilai']) ? $tw4['nilai'] . '%' : '-');

                $sheet->setCellValue('M' . $row2, 'Denum');
                $sheet->setCellValue('N' . $row2, $this->val($tw4['denum'] ?? null));
                $sheet->getStyle('N' . $row2)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->setCellValue('O' . $row2, '');

                $lastCol = 'O';
            } elseif ($type === 'semester') {
                // SM1: D=Num/Denum label, E=num value, F=%(merged)
                $sm1 = $item['semester'][1] ?? [];
                $sheet->setCellValue('D' . $row, 'Num');
                $sheet->getStyle('D' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->setCellValue('E' . $row, $this->val($sm1['num'] ?? null));
                $sheet->getStyle('E' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->setCellValue('F' . $row, isset($sm1['nilai']) ? $sm1['nilai'] . '%' : '-');

                $sheet->setCellValue('D' . $row2, 'Denum');
                $sheet->getStyle('D' . $row2)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->setCellValue('E' . $row2, $this->val($sm1['denum'] ?? null));
                $sheet->getStyle('E' . $row2)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->setCellValue('F' . $row2, '');

                // SM2: G=Num/Denum label, H=num value, I=%(merged)
                $sm2 = $item['semester'][2] ?? [];
                $sheet->setCellValue('G' . $row, 'Num');
                $sheet->getStyle('G' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->setCellValue('H' . $row, $this->val($sm2['num'] ?? null));
                $sheet->getStyle('H' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->setCellValue('I' . $row, isset($sm2['nilai']) ? $sm2['nilai'] . '%' : '-');

                $sheet->setCellValue('G' . $row2, 'Denum');
                $sheet->getStyle('G' . $row2)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->setCellValue('H' . $row2, $this->val($sm2['denum'] ?? null));
                $sheet->getStyle('H' . $row2)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->setCellValue('I' . $row2, '');

                $lastCol = 'I';
            }

            // =========================
            // MERGE KOLOM UTAMA (2 BARIS)
            // =========================
            $sheet->mergeCells('A' . $row . ':A' . $row2);
            $sheet->mergeCells('B' . $row . ':B' . $row2);
            $sheet->mergeCells('C' . $row . ':C' . $row2);

            // Style merged cells for A, B, C
            $sheet->getStyle('A' . $row)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            $sheet->getStyle('B' . $row)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER)->setWrapText(true);
            $sheet->getStyle('C' . $row)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER)->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setWrapText(true);

            // Center align D, G, J, M (Num/Denum labels)
            $sheet->getStyle('D' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            $sheet->getStyle('D' . $row2)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            if ($type === 'triwulan') {
                $sheet->getStyle('G' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getStyle('G' . $row2)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getStyle('J' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getStyle('J' . $row2)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getStyle('M' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getStyle('M' . $row2)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            } elseif ($type === 'semester') {
                $sheet->getStyle('G' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getStyle('G' . $row2)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            }

            // Merge % columns (F, I, L, O for triwulan)
            if ($type === 'triwulan') {
                $sheet->mergeCells('F' . $row . ':F' . $row2);
                $sheet->getStyle('F' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->mergeCells('I' . $row . ':I' . $row2);
                $sheet->getStyle('I' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->mergeCells('L' . $row . ':L' . $row2);
                $sheet->getStyle('L' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->mergeCells('O' . $row . ':O' . $row2);
                $sheet->getStyle('O' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            } elseif ($type === 'semester') {
                $sheet->mergeCells('F' . $row . ':F' . $row2);
                $sheet->getStyle('F' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->mergeCells('I' . $row . ':I' . $row2);
                $sheet->getStyle('I' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            } else {
                $sheet->mergeCells('F' . $row . ':F' . $row2);
                $sheet->getStyle('F' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            }

            // Style merged cells
            $sheet->getStyle('A' . $row)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            $sheet->getStyle('B' . $row)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER)->setWrapText(true);
            $sheet->getStyle('C' . $row)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER)->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setWrapText(true);

            // Style both rows
            foreach (range('A', $lastCol) as $col) {
                $sheet->getStyle($col . $row)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                $sheet->getStyle($col . $row2)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

                if ($col !== 'B' && $col !== 'C') {
                    $sheet->getStyle($col . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle($col . $row2)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                }
            }

            $sheet->getRowDimension($row)->setRowHeight(12);
            $sheet->getRowDimension($row2)->setRowHeight(12);

            // Naik 2 baris untuk next indicator
            $row += 2;
            $no++;
        }

        // COLUMN WIDTH
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(65.28);
        $sheet->getColumnDimension('C')->setWidth(8);
        foreach (range('D', $lastCol) as $col) {
            $sheet->getColumnDimension($col)->setWidth(10);
        }

        $lastRow = $row - 1;
        if ($lastRow >= 5) {
            $sheet->getStyle('A1:' . $lastCol . $lastRow)->getFont()->setName('Arial')->setSize(10);
        }
    }
}
