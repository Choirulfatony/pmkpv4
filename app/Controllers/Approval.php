<?php

namespace App\Controllers;

use App\Models\LoadModuleForminputModel;

class Approval extends AppController
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

        if (!isset($this->modules[$module])) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $this->disableCache();

        $cfg = $this->modules[$module];
        $tahun = $this->request->getGet('tahun') ?? date('Y');
        $bulan = $this->request->getGet('bulan') ?? date('m');
        $tanggal = $this->request->getGet('tanggal') ?? '';

        $backUrls = [
            'inm'    => 'siimut/form-inm',
            'imprs'  => 'siimut/imprs',
            'impunit' => 'siimut/impunit',
        ];

        $namaBulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        return $this->render('siimut/approve', [
            'judul'     => 'Approval ' . $cfg['title'],
            'icon'      => '<i class="bi bi-check2-square"></i>',
            '_content'  => view('siimut/approve', [
                'module'     => $module,
                'moduleTitle'=> $cfg['title'],
                'backUrl'    => $backUrls[$module],
                'tahun'      => $tahun,
                'bulan'      => $bulan,
                'tanggal'    => $tanggal,
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

        $module = $this->request->getPost('module') ?? 'inm';
        $tahun = (int) ($this->request->getPost('tahun') ?? date('Y'));
        $bulan = (int) ($this->request->getPost('bulan') ?? date('m'));
        $tanggal = $this->request->getPost('tanggal') ?? '';

        if (!isset($this->modules[$module])) {
            return $this->response->setJSON(['status' => false, 'message' => 'Modul tidak valid']);
        }

        $cfg = $this->modules[$module];
        $model = new LoadModuleForminputModel($cfg['prefix'], $cfg['categoryId']);
        $data = $model->getPendingApproval($tahun, $bulan);

        if (!empty($tanggal)) {
            $data = array_filter($data, function ($row) use ($tanggal) {
                $rowDate = explode(' ', $row->result_period)[0];
                return $rowDate === $tanggal;
            });
            $data = array_values($data);
        }

        return $this->response->setJSON(['status' => true, 'data' => $data]);
    }

    public function ajaxApprove()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => false, 'message' => 'Invalid request']);
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
        $userId = session('profile_id') ?? 0;
        $affected = $model->approveBatch($ids, $userId);

        if ($affected > 0) {
            return $this->response->setJSON(['status' => true, 'message' => $affected . ' data berhasil divalidasi']);
        } else {
            return $this->response->setJSON(['status' => false, 'message' => 'Tidak ada data yang divalidasi']);
        }
    }
}
