<?php

namespace App\Controllers;

use App\Models\SiimutMenuModel;
use App\Models\RekapLaporanInmModel;

class RekapLaporan extends AppController
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
        
        if (!$post) {
            return $this->response->setJSON(['error' => 'Invalid request']);
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

            $row[] = '<div class="py-1">
                <a href="javascript:void(0);" class="text-dark fw-semibold text-decoration-none" title="Detail Rekapan Ruangan" onclick="view_detail_inm(' . $indicator->indicator_id . ');">' 
                    . esc($indicator->indicator_element) . '
                </a>
                <div class="mt-1">
                    <span class="text-muted small">Target</span>
                    <span class="badge bg-success ms-1">
                        <span id="operator">' . esc($indicator->operator) . '</span>
                        <span id="factor" style="display:none">' . esc($indicator->factors) . '</span>
                        <span id="target">' . esc($indicator->indicator_target) . '</span>' . esc($indicator->indicator_units) . '
                    </span>
                </div>
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
                    $units = $list->units ?? '';
                    $num = $list->num ?? 0;
                    $denum = $list->denum ?? 0;

                    $row[] = '<div class="py-1">
                        <span id="total">' . esc($total) . '</span>
                        <span id="unit">' . esc($units) . '</span>
                        <div class="small text-muted mt-1">
                            <span id="num">' . esc($num) . '</span> | <span id="denum">' . esc($denum) . '</span>
                        </div>
                    </div>';
                }
            }

            $data[] = $row;
        }

        return $this->response->setJSON([
            'draw'            => $post['draw'],
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
        $post = $this->request->getPost();
        
        if (!$post) {
            return $this->response->setJSON(['error' => 'Invalid request']);
        }

        $indicatorId = isset($post['indicator_id']) ? (int) $post['indicator_id'] : 0;
        $tahun = isset($post['vtahun']) ? (int) $post['vtahun'] : (int) date('Y');
        
        if ($indicatorId == 0) {
            return $this->response->setJSON(['error' => 'Invalid indicator_id']);
        }

        // Ambil semua ruangan untuk indicator ini
        $departments = $this->rekapModel->getDepartmentsByIndicator($indicatorId, $tahun, $post);
        
        // Ambil semua data detail sekaligus
        $allDetailData = $this->rekapModel->getAllDetailData($indicatorId, $tahun);
        
        // Ambil info indicator
        $indicatorInfo = $this->rekapModel->getDetailByIdInm($indicatorId);
        $target = $indicatorInfo->indicator_target ?? 0;

        $data = [];
        $no = isset($post['start']) ? (int) $post['start'] : 0;

        foreach ($departments as $dept) {
            $no++;
            $row = [];

            // No
            $row[] = '<div class="fw-bold">' . $no . '</div>';

            // Ruangan
            $row[] = '<div class="py-1 text-start ps-2">' . esc($dept->department_name) . '</div>';

            // Target
            $row[] = '<div class="py-1">
                <span id="target_det">' . $target . '</span>
                <span id="factor_det" style="display:none">1</span>
                <span id="operator_det" style="display:none">>=</span>
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
                    $num = $list->num ?? 0;
                    $denum = $list->denum ?? 0;

                    $row[] = '<div class="py-1">
                        <span id="num_det">' . esc($num) . '</span> | <span id="denum_det">' . esc($denum) . '</span>
                    </div>';
                }
            }

            $data[] = $row;
        }

        return $this->response->setJSON([
            'draw'            => $post['draw'],
            'recordsTotal'    => $this->rekapModel->countDepartmentsByIndicator($indicatorId, $tahun),
            'recordsFiltered' => $this->rekapModel->countDepartmentsByIndicator($indicatorId, $tahun, $post),
            'data'            => $data,
        ]);
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
}
