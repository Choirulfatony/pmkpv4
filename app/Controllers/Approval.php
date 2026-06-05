<?php

namespace App\Controllers;

use App\Models\LoadModuleForminputModel;
use App\Models\ApprovalRequestModel;
use App\Models\SiimutMenuModel;

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
        $departmentId = (int) ($this->request->getGet('department') ?? 0);

        $backUrls = [
            'inm'    => 'siimut/form-inm',
            'imprs'  => 'siimut/imprs',
            'impunit' => 'siimut/impunit',
        ];

        $namaBulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        $model = new LoadModuleForminputModel($cfg['prefix'], $cfg['categoryId']);
        $departments = $model->getActiveDepartmentsWithDraft((int) $tahun, (int) $bulan);

        return $this->render('siimut/approve', [
            'judul'     => 'Approval ' . $cfg['title'],
            'icon'      => '<i class="bi bi-check2-square"></i>',
            '_content'  => view('siimut/approve', [
                'module'      => $module,
                'moduleTitle' => $cfg['title'],
                'backUrl'     => $backUrls[$module],
                'tahun'       => $tahun,
                'bulan'       => $bulan,
                'tanggal'     => $tanggal,
                'namaBulan'   => $namaBulan,
                'departments' => $departments,
                'departmentId'=> $departmentId,
                'profileId'   => session('profile_id') ?? 0
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
        $departmentId = (int) ($this->request->getPost('department') ?? 0);

        if (!isset($this->modules[$module])) {
            return $this->response->setJSON(['status' => false, 'message' => 'Modul tidak valid']);
        }

        $cfg = $this->modules[$module];
        $model = new LoadModuleForminputModel($cfg['prefix'], $cfg['categoryId']);
        $data = $model->getPendingApproval($tahun, $bulan, $departmentId > 0 ? $departmentId : null);

        if (!empty($tanggal)) {
            $data = array_filter($data, function ($row) use ($tanggal) {
                $rowDate = explode(' ', $row->result_period)[0];
                return $rowDate === $tanggal;
            });
            $data = array_values($data);
        }

        return $this->response->setJSON(['status' => true, 'data' => $data]);
    }

    /**
     * Ambil daftar departemen yang punya data draft untuk kombinasi bulan+tahun+module.
     * Dipanggil via AJAX saat user mengganti bulan/tahun.
     */
    public function ajaxGetDepartments()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => false, 'message' => 'Invalid request']);
        }

        $module = $this->request->getPost('module') ?? 'inm';
        $tahun = (int) ($this->request->getPost('tahun') ?? date('Y'));
        $bulan = (int) ($this->request->getPost('bulan') ?? date('m'));

        if (!isset($this->modules[$module])) {
            return $this->response->setJSON(['status' => false, 'message' => 'Modul tidak valid']);
        }

        $cfg = $this->modules[$module];
        $model = new LoadModuleForminputModel($cfg['prefix'], $cfg['categoryId']);
        $departments = $model->getActiveDepartmentsWithDraft($tahun, $bulan);

        return $this->response->setJSON(['status' => true, 'data' => $departments]);
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

    // ========== Approval Requests (user minta edit/hapus) ==========

    public function requests_list()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/auth');
        }

        $this->disableCache();

        $role = session()->get('user_role');
        $menuModel = new SiimutMenuModel();
        $menus = $menuModel->getMenuByRole($role);

        return $this->render('siimut/approval_requests', [
            'judul'    => 'Approval Request',
            'icon'     => '<i class="bi bi-envelope-open"></i>',
            'menus'    => $menus,
            '_content' => view('siimut/approval_requests', [
                'profileId' => session('profile_id') ?? 0
            ])
        ]);
    }

    public function ajaxGetRequestsData()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => false, 'message' => 'Invalid request']);
        }

        $model = new ApprovalRequestModel();
        $data = $model->getPendingRequests();

        return $this->response->setJSON(['status' => true, 'data' => $data]);
    }

    public function ajaxGetAllRequestsData()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => false, 'message' => 'Invalid request']);
        }

        $model = new ApprovalRequestModel();
        $data = $model->getAllRequests();

        return $this->response->setJSON(['status' => true, 'data' => $data]);
    }

    public function ajaxApproveRequest()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => false, 'message' => 'Invalid request']);
        }

        $id = (int) $this->request->getPost('id');
        if (!$id) {
            return $this->response->setJSON(['status' => false, 'message' => 'ID tidak valid']);
        }

        $model = new ApprovalRequestModel();
        $adminId = (int) (session('profile_id') ?? 0);
        $saved = $model->approveRequest($id, $adminId);

        if ($saved) {
            return $this->response->setJSON(['status' => true, 'message' => 'Permintaan disetujui']);
        }

        return $this->response->setJSON(['status' => false, 'message' => 'Gagal menyetujui permintaan']);
    }

    public function ajaxRejectRequest()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => false, 'message' => 'Invalid request']);
        }

        $id = (int) $this->request->getPost('id');
        $notes = trim($this->request->getPost('notes') ?? '');

        if (!$id) {
            return $this->response->setJSON(['status' => false, 'message' => 'ID tidak valid']);
        }

        $model = new ApprovalRequestModel();
        $adminId = (int) (session('profile_id') ?? 0);
        $saved = $model->rejectRequest($id, $adminId, $notes);

        if ($saved) {
            return $this->response->setJSON(['status' => true, 'message' => 'Permintaan ditolak']);
        }

        return $this->response->setJSON(['status' => false, 'message' => 'Gagal menolak permintaan']);
    }
}
