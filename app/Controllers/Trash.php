<?php

namespace App\Controllers;

use App\Models\LoadModuleForminputModel;

class Trash extends AppController
{
    protected $modules = [
        'inm'    => ['prefix' => '',       'categoryId' => '4', 'title' => 'INM'],
        'imprs'  => ['prefix' => 'local_', 'categoryId' => '5', 'title' => 'IMPRS'],
        'impunit' => ['prefix' => 'local_', 'categoryId' => '6', 'title' => 'IMPUNIT'],
    ];

    public function index(string $module = 'inm')
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/auth');
        }

        $role = session()->get('user_role');
        if ($role !== 'ADMINISTRATOR') {
            return redirect()->to('/siimut/dashboard')->with('error', 'Hanya untuk Administrator');
        }

        if (!isset($this->modules[$module])) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $this->disableCache();

        $cfg = $this->modules[$module];
        $tahun = $this->request->getGet('tahun') ?? date('Y');
        $bulan = $this->request->getGet('bulan') ?? date('m');

        $backUrls = [
            'inm'    => 'siimut/form-inm',
            'imprs'  => 'siimut/imprs',
            'impunit' => 'siimut/impunit',
        ];

        $namaBulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        return $this->render('siimut/trash', [
            'judul'     => 'Trash ' . $cfg['title'],
            'icon'      => '<i class="bi bi-trash3"></i>',
            '_content'  => view('siimut/trash', [
                'module'     => $module,
                'moduleTitle'=> $cfg['title'],
                'backUrl'    => $backUrls[$module],
                'tahun'      => $tahun,
                'bulan'      => $bulan,
                'namaBulan'  => $namaBulan,
                'profileId'  => session('profile_id') ?? 0
            ])
        ]);
    }

    public function ajaxGetData()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => false, 'message' => 'Invalid request']);
        }

        $role = session()->get('user_role');
        if ($role !== 'ADMINISTRATOR') {
            return $this->response->setJSON(['status' => false, 'message' => 'Unauthorized']);
        }

        $module = $this->request->getPost('module') ?? 'inm';
        $tahun = (int) ($this->request->getPost('tahun') ?? date('Y'));
        $bulan = (int) ($this->request->getPost('bulan') ?? date('m'));

        if (!isset($this->modules[$module])) {
            return $this->response->setJSON(['status' => false, 'message' => 'Modul tidak valid']);
        }

        $cfg = $this->modules[$module];
        $model = new LoadModuleForminputModel($cfg['prefix'], $cfg['categoryId']);
        $data = $model->getDeletedData($tahun, $bulan);

        return $this->response->setJSON(['status' => true, 'data' => $data]);
    }

    public function ajaxPermanentDelete()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => false, 'message' => 'Invalid request']);
        }

        $role = session()->get('user_role');
        if ($role !== 'ADMINISTRATOR') {
            return $this->response->setJSON(['status' => false, 'message' => 'Unauthorized']);
        }

        $module = $this->request->getPost('module') ?? 'inm';
        $ids = $this->request->getPost('ids');

        if (!isset($this->modules[$module])) {
            return $this->response->setJSON(['status' => false, 'message' => 'Modul tidak valid']);
        }

        if (empty($ids) || !is_array($ids)) {
            return $this->response->setJSON(['status' => false, 'message' => 'Tidak ada data dipilih']);
        }

        $cfg = $this->modules[$module];
        $model = new LoadModuleForminputModel($cfg['prefix'], $cfg['categoryId']);
        $affected = $model->permanentDelete($ids);

        if ($affected > 0) {
            return $this->response->setJSON(['status' => true, 'message' => $affected . ' data berhasil dihapus permanen']);
        } else {
            return $this->response->setJSON(['status' => false, 'message' => 'Tidak ada data yang dihapus']);
        }
    }
}
