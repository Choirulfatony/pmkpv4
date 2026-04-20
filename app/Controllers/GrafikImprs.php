<?php

namespace App\Controllers;

use App\Models\SiimutMenuModel;
use App\Models\RekapLaporanImprsModel;

class GrafikImprs extends AppController
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

        $indicators = $this->rekapModel->getIndicatorImprs(['vtahun' => (int) $tahun]);

        return $this->render('siimut/grafik_imprs', [
            'judul'    => 'Grafik Tren IMPRS',
            'icon'     => '<i class="bi bi-graph-up-arrow"></i>',
            '_content' => view('siimut/grafik_imprs', [
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

        $monthlyData = $this->rekapModel->getMonthlyDataByIndicator($indicatorId, $tahun);
        $indicator = $this->rekapModel->getDetailByIdImprs($indicatorId);

        // Handle case where indicator is not found
        if (!$indicator) {
            return $this->response->setJSON(['error' => 'Data indikator tidak ditemukan']);
        }

        $triwulan = $this->rekapModel->getNilaiTriwulan($indicatorId, $tahun);
        $semester = $this->rekapModel->getNilaiSemester($indicatorId, $tahun);
        $tahunan = $this->rekapModel->getNilaiTahun($indicatorId, $tahun);
        $perTahun = $this->rekapModel->getNilaiPerTahun($indicatorId, $tahun);

        return $this->response->setJSON([
            'indicator'  => $indicator,
            'bulanan'    => $monthlyData,
            'triwulan'   => $triwulan,
            'semester'   => $semester,
            'tahunan'    => $tahunan,
            'per_tahun'  => $perTahun,
            'tahun'      => $tahun
        ]);
    }
}
