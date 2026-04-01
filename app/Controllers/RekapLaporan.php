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
