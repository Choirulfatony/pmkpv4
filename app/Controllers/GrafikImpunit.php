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

        return $this->response
            ->setContentType('application/json')
            ->setJSON([
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
}