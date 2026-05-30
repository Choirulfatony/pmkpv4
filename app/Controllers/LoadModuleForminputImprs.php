<?php

namespace App\Controllers;

use App\Models\SiimutMenuModel;
use App\Models\LoadModuleForminputModel;

class LoadModuleForminputImprs extends AppController
{
    protected $model;

    public function __construct()
    {
        parent::__construct();
        $this->model = new LoadModuleForminputModel('local_', '5');
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
        $bulan = $this->request->getGet('bulan') ?? date('m');
        $department_id = session()->get('department_id') ?? 0;

        $indicators = $this->model->getIndicators($tahun, $department_id);
        $deptMap = [];
        foreach ($indicators as $dept) {
            $deptMap[$dept->department_id] = [
                'department_id' => $dept->department_id,
                'department_name' => $dept->department_name
            ];
        }
        $departments = array_values($deptMap);

        $role = session()->get('user_role');
        $userDepartmentId = session()->get('department_id') ?? 0;
        $showAllOption = in_array($role, ['ADMINISTRATOR', 'KOMITE']) || empty($userDepartmentId);

        return $this->render('siimut/form_imprs', [
            'judul'          => 'Form Input IMPRS',
            'icon'           => '<i class="bi bi-pencil-square"></i>',
            '_content'       => view('siimut/form_imprs', [
                'tahun'            => $tahun,
                'bulan'            => $bulan,
                'departments'      => $departments,
                'showAllOption'    => $showAllOption,
                'userDepartmentId' => $userDepartmentId,
                'profileId'        => session('profile_id') ?? 0
            ]),
            'menus'        => $menus
        ]);
    }

    public function get_indicators()
    {
        $tahun = (int) ($this->request->getPost('tahun') ?? date('Y'));
        $bulan = (int) ($this->request->getPost('bulan') ?? date('m'));
        $department_id = (int) ($this->request->getPost('department_id') ?? 0);

        $indicators = $this->model->getIndicators($tahun, $department_id);

        $statusList = $this->model->getFillStatus($tahun, str_pad((string) $bulan, 2, '0', STR_PAD_LEFT));

        $statusMap = [];
        foreach ($statusList as $s) {
            $key = $s->indicator_id . '_' . $s->department_id;
            $statusMap[$key] = $s;
        }

        $indicatorIds = array_map(fn($i) => (int) $i->indicator_id, $indicators);
        $rawDaily = $this->model->getDailyDataForMultipleIndicators($indicatorIds, $tahun, $bulan);

        $dailyMap = [];
        foreach ($rawDaily as $d) {
            $key = $d->result_indicator_id . '_' . $d->result_department_id;
            $day = (int) $d->tanggal;
            $dailyMap[$key][$day] = $d;
        }

        $daysInMonth = $this->model->getDaysInMonth($bulan, $tahun);

        foreach ($indicators as &$ind) {
            $key = $ind->indicator_id . '_' . $ind->department_id;

            if (isset($statusMap[$key])) {
                $ind->last_fill_date = $statusMap[$key]->last_fill_date;
                $ind->fill_count = (int) $statusMap[$key]->fill_count;
                $ind->monthly_num = (float) $statusMap[$key]->monthly_num;
                $ind->monthly_den = (float) $statusMap[$key]->monthly_den;
            } else {
                $ind->last_fill_date = null;
                $ind->fill_count = 0;
                $ind->monthly_num = 0;
                $ind->monthly_den = 0;
            }

            $target  = (float) ($ind->indicator_target ?? 0);
            $factors = (float) ($ind->indicator_factors ?? 1);
            $operator = $ind->indicator_target_calculation ?? '>=';

            $daily = [];
            for ($d = 1; $d <= $daysInMonth; $d++) {
                if (isset($dailyMap[$key][$d])) {
                    $r      = $dailyMap[$key][$d];
                    $num    = (float) $r->num;
                    $denum  = (float) $r->denum;
                    $nilai  = $denum > 0 ? round(($num / $denum) * $factors, 2) : null;
                    $status = $r->result_record_status ?? '';
                } else {
                    $num    = 0;
                    $denum  = 0;
                    $nilai  = null;
                    $status = '';
                }
                $daily[] = [
                    'hari'     => $d,
                    'num'      => $num,
                    'denum'    => $denum,
                    'nilai'    => $nilai,
                    'tercapai' => $nilai !== null ? $this->model->hitungTercapai($nilai, $target, $operator) : null,
                    'status'   => $status,
                ];
            }
            $ind->daily = $daily;
        }

        return $this->response->setJSON([
            'indicators' => $indicators,
            'days'       => $daysInMonth,
        ]);
    }

    public function get_indicator_detail()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Invalid request']);
        }

        $indicator_id = $this->request->getPost('indicator_id') ?? 0;
        $tanggal = $this->request->getPost('tanggal') ?? date('Y-m-d');
        $department_id = $this->request->getPost('department_id') ?? 0;

        $data = $this->model->getIndicatorDetail($indicator_id, $department_id, $tanggal);

        return $this->response->setJSON($data);
    }

    public function get_riwayat()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Invalid request']);
        }

        $tahun = $this->request->getPost('tahun') ?? date('Y');
        $bulan = $this->request->getPost('bulan') ?? date('m');
        $department_id = $this->request->getPost('department_id') ?? 0;

        $data = $this->model->getRiwayat($tahun, $bulan, $department_id);

        return $this->response->setJSON($data);
    }

    public function get_daily_detail()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Invalid request']);
        }

        $indicatorId  = (int) $this->request->getPost('indicator_id');
        $tahun        = (int) $this->request->getPost('tahun');
        $bulan        = (int) $this->request->getPost('bulan');

        $role = session()->get('user_role') ?? '';
        $userDeptId = null;
        if (!in_array($role, ['ADMINISTRATOR', 'KOMITE'])) {
            $userDeptId = session()->get('department_id') ?? null;
        }

        $info = $this->model->getIndicatorById($indicatorId);

        $target  = $info ? (float) ($info->indicator_target ?? 0) : 0;
        $factors = $info ? (float) ($info->indicator_factors ?? 1) : 1;
        $operator = $info ? ($info->indicator_target_calculation ?? '>=') : '>=';
        $units   = $info ? ($info->indicator_units ?? '%') : '%';

        $daysInMonth = $this->model->getDaysInMonth($bulan, $tahun);

        $departments = $this->model->getDepartmentsByIndicator($indicatorId, $tahun, $userDeptId);
        $rawData     = $this->model->getDailyDataAllDepartments($indicatorId, $tahun, $bulan);

        $byDept = [];
        foreach ($rawData as $row) {
            $deptId = (int) $row->result_department_id;
            $day    = (int) $row->tanggal;
            $byDept[$deptId][$day] = $row;
        }

        $deptDaily = [];
        foreach ($departments as $dept) {
            $did = (int) $dept->department_id;
            $daily = [];
            for ($d = 1; $d <= $daysInMonth; $d++) {
                if (isset($byDept[$did][$d])) {
                    $r      = $byDept[$did][$d];
                    $num    = (float) $r->num;
                    $denum  = (float) $r->denum;
                    $nilai  = $denum > 0 ? round(($num / $denum) * $factors, 2) : null;
                    $status = $r->result_record_status ?? '';
                } else {
                    $num    = 0;
                    $denum  = 0;
                    $nilai  = null;
                    $status = '';
                }

                $daily[] = [
                    'hari'     => $d,
                    'num'      => $num,
                    'denum'    => $denum,
                    'nilai'    => $nilai,
                    'tercapai' => $nilai !== null ? $this->model->hitungTercapai($nilai, $target, $operator) : null,
                    'status'   => $status,
                ];
            }
            $deptDaily[] = [
                'department_id'   => $did,
                'department_name' => $dept->department_name,
                'daily'           => $daily,
            ];
        }

        $namaBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        return $this->response->setJSON([
            'dept_data'   => $deptDaily,
            'days'        => $daysInMonth,
            'target'      => $target,
            'units'       => $units,
            'operator'    => $operator,
            'bulan'       => $namaBulan[$bulan] ?? $bulan,
            'bulan_angka' => $bulan,
            'tahun'       => $tahun,
            'indicator'   => $info ? $info->indicator_element : '',
        ]);
    }

    public function save()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => false, 'message' => 'Invalid request']);
        }

        $indicator_id = $this->request->getPost('indicator_id');
        $department_id = $this->request->getPost('department_id');
        $tanggal = $this->request->getPost('tanggal');
        $numerator = (float) ($this->request->getPost('numerator') ?? 0);
        $denumerator = (float) ($this->request->getPost('denumerator') ?? 0);
        $kendala = $this->request->getPost('kendala') ?? '';
        $perbaikan = $this->request->getPost('perbaikan') ?? '';

        if (!$indicator_id || !$department_id || !$tanggal) {
            return $this->response->setJSON(['status' => false, 'message' => 'Data tidak lengkap']);
        }

        $db = db_connect();
        $db->transStart();

        $this->model->saveResult(
            $indicator_id,
            $department_id,
            $tanggal,
            $numerator,
            $denumerator
        );

        $this->model->saveRencanaPerbaikan(
            $indicator_id,
            $department_id,
            $tanggal,
            $numerator,
            $denumerator,
            $kendala,
            $perbaikan
        );

        $db->transComplete();

        if ($db->transStatus()) {
            return $this->response->setJSON(['status' => true, 'message' => 'Data berhasil disimpan']);
        } else {
            return $this->response->setJSON(['status' => false, 'message' => 'Gagal menyimpan data']);
        }
    }

    public function delete()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => false, 'message' => 'Invalid request']);
        }

        $indicator_id = $this->request->getPost('indicator_id');
        $department_id = $this->request->getPost('department_id');
        $tanggal = $this->request->getPost('tanggal');

        if (!$indicator_id || !$department_id || !$tanggal) {
            return $this->response->setJSON(['status' => false, 'message' => 'Data tidak lengkap']);
        }

        $success = $this->model->deleteResult($indicator_id, $department_id, $tanggal);

        if ($success) {
            return $this->response->setJSON(['status' => true, 'message' => 'Data berhasil dihapus']);
        } else {
            return $this->response->setJSON(['status' => false, 'message' => 'Gagal menghapus data']);
        }
    }
}
