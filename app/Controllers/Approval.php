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
        'ikp'     => ['prefix' => 'local_', 'categoryId' => '7', 'title' => 'IKP'],
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

        $model = new ApprovalRequestModel();
        $model->rejectExpiredApprovals();

        $cfg = $this->modules[$module];
        $tahun = $this->request->getGet('tahun') ?? date('Y');
        $bulan = $this->request->getGet('bulan') ?? date('m');
        $tanggal = $this->request->getGet('tanggal') ?? '';
        $departmentId = (int) ($this->request->getGet('department') ?? 0);

        $backUrls = [
            'inm'    => 'siimut/form-inm',
            'imprs'  => 'siimut/imprs',
            'impunit' => 'siimut/impunit',
            'ikp'     => 'siimut/ikp',
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
        if (!session()->get('logged_in')) {
            return $this->response->setJSON(['status' => false, 'message' => 'Session expired, silakan login ulang']);
        }
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => false, 'message' => 'Invalid request']);
        }

        $module = $this->request->getPost('module') ?? 'inm';
        $tahun = (int) ($this->request->getPost('tahun') ?? date('Y'));
        $bulan = (int) ($this->request->getPost('bulan') ?? date('m'));
        $tanggal = $this->request->getPost('tanggal') ?? '';
        $departmentId = (int) ($this->request->getPost('department') ?? 0);
        $indicatorId = (int) ($this->request->getPost('indicator_id') ?? 0);

        if (!isset($this->modules[$module])) {
            return $this->response->setJSON(['status' => false, 'message' => 'Modul tidak valid']);
        }

        $cfg = $this->modules[$module];
        $model = new LoadModuleForminputModel($cfg['prefix'], $cfg['categoryId']);
        $data = $model->getPendingApproval($tahun, $bulan, $departmentId > 0 ? $departmentId : null, $indicatorId > 0 ? $indicatorId : null);

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
        if (!session()->get('logged_in')) {
            return $this->response->setJSON(['status' => false, 'message' => 'Session expired, silakan login ulang']);
        }
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

    /**
     * AJAX: Ambil daftar indikator yang punya data draft untuk periode/modul tertentu.
     */
    public function ajaxGetIndicators()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setJSON(['status' => false, 'message' => 'Session expired, silakan login ulang']);
        }
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => false, 'message' => 'Invalid request']);
        }

        $module = $this->request->getPost('module') ?? 'inm';
        $tahun = (int) ($this->request->getPost('tahun') ?? date('Y'));
        $bulan = (int) ($this->request->getPost('bulan') ?? date('m'));
        $departmentId = (int) ($this->request->getPost('department') ?? 0);

        if (!isset($this->modules[$module])) {
            return $this->response->setJSON(['status' => false, 'message' => 'Modul tidak valid']);
        }

        $cfg = $this->modules[$module];
        $model = new LoadModuleForminputModel($cfg['prefix'], $cfg['categoryId']);
        $indicators = $model->getActiveIndicatorsWithDraft($tahun, $bulan, $departmentId > 0 ? $departmentId : null);

        return $this->response->setJSON(['status' => true, 'data' => $indicators]);
    }

    /**
     * Rekap bulanan per indikator, di-filter ke indikator yang memiliki data draft (status D)
     * pada bulan yang dipilih. Untuk modul approval (siimut/approval/{module}).
     */
    public function ajaxGetRecap()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setJSON(['status' => false, 'message' => 'Session expired, silakan login ulang']);
        }
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => false, 'message' => 'Invalid request']);
        }

        $module       = $this->request->getPost('module') ?? 'inm';
        $tahun        = (int) ($this->request->getPost('tahun') ?? date('Y'));
        $bulan        = (int) ($this->request->getPost('bulan') ?? date('m'));
        $departmentId = (int) ($this->request->getPost('department') ?? 0);
        $indicatorId  = (int) ($this->request->getPost('indicator_id') ?? 0);

        if (!isset($this->modules[$module])) {
            return $this->response->setJSON(['status' => false, 'message' => 'Modul tidak valid']);
        }

        $cfg   = $this->modules[$module];
        $model = new LoadModuleForminputModel($cfg['prefix'], $cfg['categoryId']);
        $data  = $model->getRecapByIndicatorWithDraft($tahun, $bulan, $departmentId > 0 ? $departmentId : null, $indicatorId > 0 ? $indicatorId : null);

        return $this->response->setJSON(['status' => true, 'data' => $data]);
    }

    public function ajaxApprove()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setJSON(['status' => false, 'message' => 'Session expired, silakan login ulang']);
        }
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
            'judul'    => 'Backdate Request',
            'icon'     => '<i class="bi bi-envelope-open"></i>',
            'menus'    => $menus,
            '_content' => view('siimut/approval_requests', [
                'profileId' => session('profile_id') ?? 0,
                'groupType' => null
            ])
        ]);
    }

    public function requestsListByType(string $type)
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/auth');
        }

        $typeMap = [
            'inm'    => ['id' => '1', 'title' => 'INM'],
            'imprs'  => ['id' => '5', 'title' => 'IMPRS'],
            'impunit' => ['id' => '6', 'title' => 'IMPUNIT'],
            'ikp'    => ['id' => '7', 'title' => 'IKP'],
        ];

        if (!isset($typeMap[$type])) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $this->disableCache();

        $model = new ApprovalRequestModel();
        $model->rejectExpiredApprovals();

        $role = session()->get('user_role');
        $menuModel = new SiimutMenuModel();
        $menus = $menuModel->getMenuByRole($role);
        $cfg = $typeMap[$type];

        return $this->render('siimut/approval_requests', [
            'judul'    => 'Backdate Request - ' . $cfg['title'],
            'icon'     => '<i class="bi bi-envelope-open"></i>',
            'menus'    => $menus,
            '_content' => view('siimut/approval_requests', [
                'profileId' => session('profile_id') ?? 0,
                'groupType' => $cfg['id']
            ])
        ]);
    }

    public function ajaxGetRequestsData()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => false, 'message' => 'Invalid request']);
        }

        $model = new ApprovalRequestModel();
        $model->rejectExpiredApprovals();
        $groupType = $this->request->getPost('group_type');
        $data = $model->getPendingRequests($groupType);

        return $this->response->setJSON(['status' => true, 'data' => $data]);
    }

    public function ajaxGetAllRequestsData()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => false, 'message' => 'Invalid request']);
        }

        $model = new ApprovalRequestModel();
        $model->rejectExpiredApprovals();
        $groupType = $this->request->getPost('group_type');
        $data = $model->getAllRequests($groupType);

        return $this->response->setJSON(['status' => true, 'data' => $data]);
    }

    public function ajaxBackdateNotification()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => false, 'message' => 'Invalid request']);
        }

        $model = new ApprovalRequestModel();
        $model->rejectExpiredApprovals();
        $data = $model->getPendingRequests(null, 5);
        $count = $model->getPendingCount();

        $profileId = (int) session('profile_id');
        $myRequests = [];
        if ($profileId > 0) {
            $myRequests = $model->getMyRecentRequests($profileId, 5);
        }

        return $this->response->setJSON([
            'status'      => true,
            'total'       => $count,
            'data'        => $data,
            'my_requests' => $myRequests
        ]);
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
        $request = $model->getById($id);

        if (!$request) {
            return $this->response->setJSON(['status' => false, 'message' => 'Data request tidak ditemukan']);
        }

        $adminId = (int) (session('profile_id') ?? 0);
        $saved = $model->approveRequest($id, $adminId);

        if (!$saved) {
            return $this->response->setJSON(['status' => false, 'message' => 'Gagal menyetujui permintaan']);
        }

        // ===== Jika open_period → update group_days otomatis =====
        if ($request->ar_action_type === 'open_period') {
            $indicatorId  = $request->ar_indicator_id;
            $departmentId = $request->ar_department_id;
            $periodStart  = $request->ar_period;
            $periodEnd    = $request->ar_period_end;

            if ($indicatorId && $departmentId && $periodStart && $periodEnd) {
                $groupType = $request->ar_group_type;
                if (!$groupType) {
                    $db2 = db_connect();
                    $catRow = $db2->table('quality_indicator')
                        ->select('indicator_category_id')
                        ->where('indicator_id', $indicatorId)
                        ->get()
                        ->getRow();
                    $catId = $catRow ? $catRow->indicator_category_id : null;
                    if (!$catId) {
                        // Fallback: check local_quality_indicator
                        $catRow = $db2->table('local_quality_indicator')
                            ->select('indicator_category_id')
                            ->where('indicator_id', $indicatorId)
                            ->get()
                            ->getRow();
                        $catId = $catRow ? $catRow->indicator_category_id : null;
                    }
                    $catMap = ['4' => '1', '5' => '5', '6' => '6', '7' => '7'];
                    $groupType = $catMap[$catId] ?? null;
                }

                $startDate = new \DateTime($periodStart);
                $endDate   = new \DateTime($periodEnd);
                $todayDt   = new \DateTime();
                $groupDays = (int) $todayDt->diff($startDate)->days;
                if ($groupDays < 0) $groupDays = 0;

                $db     = db_connect();
                $period = substr($periodStart, 0, 4);
                $where  = [
                    'group_department_id' => (string) $departmentId,
                    'group_indicator_id'  => (string) $indicatorId,
                    'group_period'        => $period,
                    'group_record_status' => 'A',
                ];
                if ($groupType) {
                    $where['group_type'] = (int) $groupType;
                }

                $tableMap = [1 => 'quality_indicator_group', 5 => 'local_quality_indicator_group', 6 => 'local_quality_indicator_group', 7 => 'local_quality_indicator_group'];
                $tbl = $groupType ? ($tableMap[(int) $groupType] ?? 'quality_indicator_group') : 'quality_indicator_group';

                $existing = $db->table($tbl)->where($where)->get()->getRow();
                if ($existing) {
                    $db->table($tbl)->where($where)->update([
                        'group_days'             => $groupDays,
                        'group_days_changed_at'  => date('Y-m-d H:i:s'),
                    ]);
                } else {
                    // Get institution_code from another existing record for same dept
                    $refRow = $db->table($tbl)
                        ->select('group_institution_code')
                        ->where('group_department_id', (string) $departmentId)
                        ->where('group_record_status', 'A')
                        ->get()
                        ->getRow();
                    $insCode = $refRow ? $refRow->group_institution_code : 'RSSM';
                    $db->table($tbl)->insert([
                        'group_indicator_id'     => (string) $indicatorId,
                        'group_department_id'    => (string) $departmentId,
                        'group_institution_code' => $insCode,
                        'group_period'           => $period,
                        'group_type'             => (int) $groupType,
                        'group_days'             => $groupDays,
                        'group_days_changed_at'  => date('Y-m-d H:i:s'),
                        'group_record_status'    => 'A',
                    ]);
                }
            }
        }

        return $this->response->setJSON(['status' => true, 'message' => 'Permintaan disetujui']);
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
