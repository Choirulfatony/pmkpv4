<?php

namespace App\Controllers;

use App\Models\SiimutMenuModel;
use App\Models\RekapLaporanImpunitModel;

class GrafikImpunit extends AppController
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
        $indicatorId = $this->request->getGet('indicator_id');

        $indicators = $this->rekapModel->getIndicatorImpunit(['vtahun' => (int) $tahun]);

        return $this->render('siimut/grafik_impunit', [
            'judul'    => 'Grafik Tren IMPUnit',
            'icon'     => '<i class="bi bi-graph-up"></i>',
            '_content' => view('siimut/grafik_impunit', [
                'tahun'       => $tahun,
                'indicators'  => $indicators,
                'indicatorId' => $indicatorId,
            ]),
            'menus'    => $menus
        ]);
    }

    public function getIndicatorsByYear()
    {
        $tahun = $this->request->getPost('tahun') ?? date('Y');
        $indicators = $this->rekapModel->getIndicatorImpunit(['vtahun' => (int) $tahun]);
        return $this->response->setJSON($indicators);
    }

    public function getDataGrafik()
    {
        $post = $this->request->getPost();
        $tahun = isset($post['tahun']) ? (int) $post['tahun'] : (int) date('Y');
        $indicatorId = isset($post['indicator_id']) ? (int) $post['indicator_id'] : null;

        if (!$indicatorId) {
            return $this->response->setJSON(['error' => 'Indicator ID diperlukan']);
        }

        $role = session()->get('user_role') ?? '';
        $sessionDeptId = session()->get('department_id') ?? null;
        $departmentId = null;

        if (in_array($role, ['ADMINISTRATOR', 'KOMITE'])) {
            $departmentId = isset($post['department_id']) && $post['department_id'] !== ''
                ? (int) $post['department_id']
                : null;
        } else {
            $departmentId = $sessionDeptId;
        }

        $departments = $this->rekapModel->getDepartmentsByIndicator($indicatorId, $tahun);

        $monthlyData = $this->rekapModel->getMonthlyDataByIndicator($indicatorId, $tahun, $departmentId);
        $indicator = $this->rekapModel->getDetailByIdImpunit($indicatorId);

        if (!$indicator) {
            return $this->response->setJSON(['error' => 'Data indikator tidak ditemukan']);
        }

        $triwulan = $this->rekapModel->getNilaiTriwulan($indicatorId, $tahun, $departmentId);
        $semester = $this->rekapModel->getNilaiSemester($indicatorId, $tahun, $departmentId);
        $tahunan = $this->rekapModel->getNilaiTahun($indicatorId, $tahun, $departmentId);
        $perTahun = $this->rekapModel->getNilaiPerTahun($indicatorId, $tahun, $departmentId);

        return $this->response->setJSON([
            'indicator'     => $indicator,
            'bulanan'       => $monthlyData,
            'triwulan'      => $triwulan,
            'semester'      => $semester,
            'tahunan'       => $tahunan,
            'per_tahun'     => $perTahun,
            'tahun'         => $tahun,
            'departments'   => $departments,
            'user_role'     => $role,
            'user_department_id' => $sessionDeptId,
        ]);
    }

    public function exportExcel()
    {
        $tahun = $this->request->getGet('tahun') ?? date('Y');
        $indicatorId = $this->request->getGet('indicator_id');
        if (!$indicatorId) {
            return redirect()->to('grafik-impunit')->with('error', 'Pilih indikator terlebih dahulu');
        }

        $role = session()->get('user_role') ?? '';
        $sessionDeptId = session()->get('department_id') ?? null;
        $departmentId = null;

        if (in_array($role, ['ADMINISTRATOR', 'KOMITE'])) {
            $departmentId = $this->request->getGet('department_id');
            $departmentId = ($departmentId !== null && $departmentId !== '') ? (int) $departmentId : null;
        } else {
            $departmentId = $sessionDeptId;
        }

        $monthlyData = $this->rekapModel->getMonthlyDataByIndicator((int) $indicatorId, (int) $tahun, $departmentId);
        $indicator = $this->rekapModel->getDetailByIdImpunit((int) $indicatorId);

        if (!$indicator) {
            return redirect()->to('grafik-impunit')->with('error', 'Indikator tidak ditemukan');
        }

        $triwulan = $this->rekapModel->getNilaiTriwulan((int) $indicatorId, (int) $tahun, $departmentId);
        $semester = $this->rekapModel->getNilaiSemester((int) $indicatorId, (int) $tahun, $departmentId);
        $tahunan = $this->rekapModel->getNilaiTahun((int) $indicatorId, (int) $tahun, $departmentId);

        if (ob_get_level()) {
            ob_end_clean();
        }

        $bulanNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

        try {
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Grafik ' . $tahun);

            $sheet->setCellValue('A1', 'GRAFIK TREN IMPUnit');
            $sheet->mergeCells('A1:F1');
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
            $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $sheet->setCellValue('A2', 'RSUD dr. SOEDONO PROVINSI JAWA TIMUR');
            $sheet->mergeCells('A2:F2');
            $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(12);
            $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $sheet->setCellValue('A3', 'TAHUN ' . $tahun);
            $sheet->mergeCells('A3:F3');
            $sheet->getStyle('A3')->getFont()->setBold(true)->setSize(12);
            $sheet->getStyle('A3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $sheet->setCellValue('A5', 'INDIKATOR: ' . ($indicator->indicator_element ?? '-'));
            $sheet->mergeCells('A5:F5');
            $sheet->getStyle('A5')->getFont()->setBold(true)->setSize(11);

            $targetText = '';
            $operator = trim($indicator->indicator_target_calculation ?? '');
            if ($operator === '=') {
                $targetText = $indicator->indicator_factors . ' ' . ($indicator->indicator_units ?? '%');
            } else {
                $targetText = $operator . ' ' . ($indicator->indicator_target ?? '') . ' ' . ($indicator->indicator_units ?? '%');
            }
            $sheet->setCellValue('A6', 'STANDAR: ' . $targetText);
            $sheet->mergeCells('A6:F6');
            $sheet->getStyle('A6')->getFont()->setBold(true)->setSize(11);

            $row = 8;
            $headers = ['Bulan', 'Numerator', 'Denumerator', 'Capaian', 'Status'];
            $cols = ['A', 'B', 'C', 'D', 'E'];
            foreach ($headers as $i => $h) {
                $sheet->setCellValue($cols[$i] . $row, $h);
                $sheet->getStyle($cols[$i] . $row)->getFont()->setBold(true);
                $sheet->getStyle($cols[$i] . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle($cols[$i] . $row)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            }

            $target = (float) ($indicator->indicator_target ?? 0);
            $factors = (float) ($indicator->indicator_factors ?? 1);
            $operator = $indicator->indicator_target_calculation ?? '>=';

            $row = 9;
            for ($b = 0; $b < 12; $b++) {
                $bln = $b + 1;
                $data = $monthlyData[$bln] ?? null;
                $num = $data ? $data['num'] : 0;
                $denum = $data ? $data['denum'] : 0;
                $nilai = $data ? $data['nilai'] : null;

                $sheet->setCellValue('A' . $row, $bulanNames[$b]);
                $sheet->setCellValue('B' . $row, $num);
                $sheet->setCellValue('C' . $row, $denum);
                $sheet->setCellValue('D' . $row, $nilai !== null ? number_format($nilai, 2) : 'N/A');

                $status = 'N/A';
                if ($nilai !== null) {
                    $tercapai = ($operator === '>=')
                        ? ($nilai >= $target)
                        : ($operator === '<='
                            ? ($nilai <= $target)
                            : ($nilai == $target));
                    $status = $tercapai ? 'Tercapai' : 'Tidak Tercapai';
                }
                $sheet->setCellValue('E' . $row, $status);

                for ($c = 0; $c < 5; $c++) {
                    $sheet->getStyle($cols[$c] . $row)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                    $sheet->getStyle($cols[$c] . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                }
                $row++;
            }

            // Separator
            $row++;
            $sheet->setCellValue('A' . $row, 'RINGKASAN PERIODE');
            $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(12);
            $sheet->mergeCells('A' . $row . ':E' . $row);
            $row++;

            $periodeHeader = ['Periode', 'Num', 'Denum', 'Capaian', 'Status'];
            foreach ($periodeHeader as $i => $h) {
                $sheet->setCellValue($cols[$i] . $row, $h);
                $sheet->getStyle($cols[$i] . $row)->getFont()->setBold(true);
                $sheet->getStyle($cols[$i] . $row)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                $sheet->getStyle($cols[$i] . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            }
            $row++;

            $periods = [
                ['TW I', $triwulan[1] ?? null],
                ['TW II', $triwulan[2] ?? null],
                ['TW III', $triwulan[3] ?? null],
                ['TW IV', $triwulan[4] ?? null],
                ['Semester I', $semester[1] ?? null],
                ['Semester II', $semester[2] ?? null],
                ['Tahunan', $tahunan[1] ?? null],
            ];

            foreach ($periods as $p) {
                $sheet->setCellValue('A' . $row, $p[0]);
                $val = $p[1];
                $sheet->setCellValue('B' . $row, $val ? $val['num'] : '-');
                $sheet->setCellValue('C' . $row, $val ? $val['denum'] : '-');
                $sheet->setCellValue('D' . $row, $val && $val['nilai'] !== null ? number_format($val['nilai'], 2) : 'N/A');
                $sheet->setCellValue('E' . $row, $val ? ($val['tercap'] ? 'Tercapai' : 'Tidak Tercapai') : 'N/A');

                for ($c = 0; $c < 5; $c++) {
                    $sheet->getStyle($cols[$c] . $row)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                    $sheet->getStyle($cols[$c] . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                }
                $row++;
            }

            $sheet->getColumnDimension('A')->setWidth(15);
            $sheet->getColumnDimension('B')->setWidth(15);
            $sheet->getColumnDimension('C')->setWidth(15);
            $sheet->getColumnDimension('D')->setWidth(15);
            $sheet->getColumnDimension('E')->setWidth(18);

            $filename = 'Grafik_IMPUnit_' . $tahun . '_' . date('YmdHis');
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
            header('Cache-Control: max-age=0');

            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save('php://output');
            exit;
        } catch (\Exception $e) {
            log_message('error', 'Export Grafik IMPUnit Excel Error: ' . $e->getMessage());
            echo 'Error: ' . $e->getMessage();
            exit;
        }
    }
}