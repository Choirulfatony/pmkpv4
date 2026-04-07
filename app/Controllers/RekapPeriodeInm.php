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

    public function getAjaxRekapPeriode()
    {
        $post = $this->request->getPost();
        $tahun = isset($post['tahun']) ? (int) $post['tahun'] : (int) date('Y');

        log_message('error', 'REKAP PERIODE INM: tahun=' . $tahun);

        $data = $this->rekapModel->getRekapPeriode($tahun);

        log_message('error', 'REKAP PERIODE INM: data count=' . count($data));

        return $this->response->setJSON([
            'draw' => $post['draw'] ?? 1,
            'recordsTotal' => count($data),
            'recordsFiltered' => count($data),
            'data' => $data,
        ]);
    }
}