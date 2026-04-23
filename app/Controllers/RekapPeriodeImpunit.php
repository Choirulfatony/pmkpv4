<?php

namespace App\Controllers;

use App\Models\SiimutMenuModel;
use App\Models\RekapLaporanImpunitModel;

class RekapPeriodeImpunit extends AppController
{
    protected $rekapModel;

    public function __construct()
    {
        $this->rekapModel = new RekapLaporanImpunitModel();
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

        return $this->render('siimut/rekap_periode_impunit', [
            'judul'    => 'Rekap IMPUnit per Periode',
            'icon'     => '<i class="bi bi-calendar-range"></i>',
            '_content' => view('siimut/rekap_periode_impunit', [
                'tahun' => $tahun,
            ]),
            'menus'    => $menus
        ]);
    }

    public function getAjaxRekapPeriode($tahun = null)
    {
        $post = $this->request->getPost();

        $tahun = $tahun ? (int) $tahun : (isset($post['tahun']) ? (int) $post['tahun'] : (int) date('Y'));

        log_message('error', 'REKAP PERIODE IMPUNIT: tahun=' . $tahun);

        try {
            $data = $this->rekapModel->getRekapPeriode($tahun);

            log_message('error', 'REKAP PERIODE IMPUNIT: data count=' . count($data));

            return $this->response
                ->setContentType('application/json')
                ->setJSON([
                    'draw' => $post['draw'] ?? 1,
                    'recordsTotal' => count($data),
                    'recordsFiltered' => count($data),
                    'data' => $data,
                ]);
        } catch (\Exception $e) {
            log_message('error', 'REKAP PERIODE IMPUNIT ERROR: ' . $e->getMessage());
            return $this->response
                ->setStatusCode(500)
                ->setContentType('application/json')
                ->setJSON([
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

            $sheet1 = $spreadsheet->getActiveSheet();
            $sheet1->setTitle('Triwulan');

            $this->buildSheet($sheet1, $data, 'triwulan', $tahun);

            $sheet2 = $spreadsheet->createSheet();
            $sheet2->setTitle('Semester');
            $this->buildSheet($sheet2, $data, 'semester', $tahun);

            $sheet3 = $spreadsheet->createSheet();
            $sheet3->setTitle('Tahun');
            $this->buildSheet($sheet3, $data, 'tahun', $tahun);

            $filename = 'Rekap_IMPUnit_Periode_' . $tahun . '.xlsx';
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');

            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save('php://output');
            exit;
        } catch (\Exception $e) {
            log_message('error', 'EXPORT EXCEL IMPUNIT ERROR: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal export Excel');
        }
    }

    private function buildSheet($sheet, $data, $type, $tahun)
    {
        $headers = ['No', 'Indikator', 'Target'];
        if ($type === 'triwulan') {
            $headers = array_merge($headers, ['TW 1', 'TW 2', 'TW 3', 'TW 4']);
        } elseif ($type === 'semester') {
            $headers = array_merge($headers, ['Semester 1', 'Semester 2']);
        } else {
            $headers[] = 'Tahun';
        }

        $col = 1;
        foreach ($headers as $header) {
            $sheet->setCellValueByColumnAndRow($col, 1, $header);
            $col++;
        }

        $sheet->getStyle('A1:' . $sheet->getHighestColumn() . '1')
            ->applyFromArray([
                'font' => ['bold' => true],
                'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'CCCCCC']],
                'alignment' => ['horizontal' => 'center', 'vertical' => 'center']
            ]);

        $row = 2;
        $no = 1;
        foreach ($data as $item) {
            $sheet->setCellValueByColumnAndRow(1, $row, $no);
            $sheet->setCellValueByColumnAndRow(2, $row, $item['indicator_element']);
            $sheet->setCellValueByColumnAndRow(3, $row, $item['target'] . ' ' . $item['satuan']);

            $colIndex = 4;
            if ($type === 'triwulan') {
                for ($i = 1; $i <= 4; $i++) {
                    $val = $item['triwulan'][$i] ?? [];
                    $nilai = $val['nilai'] ?? '-';
                    $sheet->setCellValueByColumnAndRow($colIndex, $row, $nilai . ' ' . $item['satuan']);
                    $colIndex++;
                }
            } elseif ($type === 'semester') {
                for ($i = 1; $i <= 2; $i++) {
                    $val = $item['semester'][$i] ?? [];
                    $nilai = $val['nilai'] ?? '-';
                    $sheet->setCellValueByColumnAndRow($colIndex, $row, $nilai . ' ' . $item['satuan']);
                    $colIndex++;
                }
            } else {
                $val = $item['tahun'] ?? [];
                $nilai = $val['nilai'] ?? '-';
                $sheet->setCellValueByColumnAndRow($colIndex, $row, $nilai . ' ' . $item['satuan']);
            }

            $row++;
            $no++;
        }

        foreach (range('A', $sheet->getHighestColumn()) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }
}