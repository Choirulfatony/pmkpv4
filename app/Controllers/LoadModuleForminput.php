<?php

namespace App\Controllers;

use App\Models\SiimutMenuModel;
use App\Models\LoadModuleForminputModel;

class LoadModuleForminput extends AppController
{
    protected $model;

    public function __construct()
    {
        parent::__construct();
        $this->model = new LoadModuleForminputModel();
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

        $departments = $this->model->getFormIndicators($tahun, $department_id);
        // Extract unique departments from the indicators list
        $deptMap = [];
        foreach ($departments as $dept) {
            $deptMap[$dept->department_id] = [
                'department_id' => $dept->department_id,
                'department_name' => $dept->department_name
            ];
        }
        $departments = array_values($deptMap);

        // Determine if we should show the "all departments" option
        $role = session()->get('user_role');
        $userDepartmentId = session()->get('department_id') ?? 0;
        $showAllOption = in_array($role, ['ADMINISTRATOR', 'KOMITE']) || empty($userDepartmentId);

        return $this->render('siimut/form_inm', [
            'judul'          => 'Form Input INM',
            'icon'           => '<i class="bi bi-pencil-square"></i>',
            '_content'       => view('siimut/form_inm', [
                'tahun'            => $tahun,
                'bulan'            => $bulan,
                'departments'      => $departments,
                'showAllOption'    => $showAllOption,
                'userDepartmentId' => $userDepartmentId
            ]),
            'menus'        => $menus
        ]);
    }

    public function get_indicators()
    {
        $tahun = $this->request->getPost('tahun') ?? date('Y');
        $bulan = $this->request->getPost('bulan') ?? date('m');
        $department_id = $this->request->getPost('department_id') ?? 0;

        $indicators = $this->model->getFormIndicators($tahun, $department_id);

        // Get fill status for each indicator
        $statusList = $this->model->getFillStatus($tahun, $bulan);

        // Map status by indicator_id + department_id
        $statusMap = [];
        foreach ($statusList as $s) {
            $key = $s->indicator_id . '_' . $s->department_id;
            $statusMap[$key] = $s;
        }

        // Attach status to each indicator
        foreach ($indicators as &$ind) {
            $key = $ind->indicator_id . '_' . $ind->department_id;
            if (isset($statusMap[$key])) {
                $ind->last_fill_date = $statusMap[$key]->last_fill_date;
                $ind->fill_count = $statusMap[$key]->fill_count;
                $ind->monthly_num = $statusMap[$key]->monthly_num;
                $ind->monthly_den = $statusMap[$key]->monthly_den;
            } else {
                $ind->last_fill_date = null;
                $ind->fill_count = 0;
                $ind->monthly_num = 0;
                $ind->monthly_den = 0;
            }
        }

        return $this->response->setJSON($indicators);
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

    public function save()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => false, 'message' => 'Invalid request']);
        }

        $indicator_id = $this->request->getPost('indicator_id');
        $department_id = $this->request->getPost('department_id');
        $tanggal = $this->request->getPost('tanggal');
        $numerator = $this->request->getPost('numerator');
        $denumerator = $this->request->getPost('denumerator');

        if (!$indicator_id || !$department_id || !$tanggal) {
            return $this->response->setJSON(['status' => false, 'message' => 'Data tidak lengkap']);
        }

        $success = $this->model->saveResult(
            $indicator_id,
            $department_id,
            $tanggal,
            $numerator,
            $denumerator
        );

        if ($success) {
            return $this->response->setJSON(['status' => true, 'message' => 'Data berhasil disimpan']);
        } else {
            return $this->response->setJSON(['status' => false, 'message' => 'Gagal menyimpan data']);
        }
    }
}
