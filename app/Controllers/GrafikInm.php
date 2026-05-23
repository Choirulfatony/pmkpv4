<?php

namespace App\Controllers;

use App\Models\SiimutMenuModel;
use App\Models\RekapLaporanInmModel;

class GrafikInm extends AppController
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

        // Ambil semua indikator untuk dropdown - dengan sorting: indikator dengan data di atas
        $indicators = $this->rekapModel->getIndicatorInm(['vtahun' => (int) $tahun]);

        return $this->render('siimut/grafik_inm', [
            'judul'    => 'Grafik Tren INM',
            'icon'     => '<i class="bi bi-graph-up"></i>',
            '_content' => view('siimut/grafik_inm', [
                'tahun'       => $tahun,
                'indicators'  => $indicators,
                'indicatorId' => $indicatorId,
            ]),
            'menus'    => $menus
        ]);
    }

    public function getDataGrafik()
    {
        $post = $this->request->getPost();
        $tahun = isset($post['tahun']) ? (int) $post['tahun'] : (int) date('Y');
        $indicatorId = isset($post['indicator_id']) ? (int) $post['indicator_id'] : null;

        if (!$indicatorId) {
            return $this->response->setJSON(['error' => 'Indicator ID diperlukan']);
        }

        // ADMINISTRATOR & KOMITE → bisa filter per departemen atau global
        // KENDALI_MUTU, APP → filter by department login saja
        $role = session()->get('user_role') ?? '';
        $sessionDeptId = session()->get('department_id') ?? null;
        $departmentId = null;

        if (in_array($role, ['ADMINISTRATOR', 'KOMITE'])) {
            // Ambil dari POST jika ada, null = semua departemen
            $departmentId = isset($post['department_id']) && $post['department_id'] !== ''
                ? (int) $post['department_id']
                : null;
        } else {
            // User departemen: paksa pakai department login
            $departmentId = $sessionDeptId;
        }

        // Ambil daftar departemen untuk indikator ini (untuk dropdown)
        $departments = $this->rekapModel->getDepartmentsByIndicator($indicatorId, $tahun);

        // Ambil data bulanan (filter by department jika bukan ADMIN)
        $monthlyData = $this->rekapModel->getMonthlyDataByIndicator($indicatorId, $tahun, $departmentId);
        
        // Ambil detail indikator
        $indicator = $this->rekapModel->getDetailByIdInm($indicatorId);

        // Hitung triwulan dan semester (filter by department jika bukan ADMIN)
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
}