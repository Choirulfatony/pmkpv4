<?php

namespace App\Controllers;

use App\Models\IndicatorModel;

class DataIndikator extends AppController
{
    protected $modules = [
        'inm'    => ['prefix' => '',       'categoryId' => '4', 'title' => 'INM', 'icon' => '<i class="bi bi-bar-chart"></i>'],
        'imprs'  => ['prefix' => 'local_', 'categoryId' => '5', 'title' => 'IMPRS', 'icon' => '<i class="bi bi-hospital"></i>'],
        'impunit' => ['prefix' => 'local_', 'categoryId' => '6', 'title' => 'IMPUnit', 'icon' => '<i class="bi bi-building"></i>'],
    ];

    public function index(string $module = 'inm')
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/auth');
        }

        $this->disableCache();

        $role = session()->get('user_role');
        if ($role !== 'ADMINISTRATOR') {
            return redirect()->to('/siimut/dashboard')->with('error', 'Hanya untuk Administrator');
        }

        if (!isset($this->modules[$module])) {
            $module = 'inm';
        }

        $mod = $this->modules[$module];

        return $this->render('siimut/data_indikator', [
            'judul' => 'Data Indikator ' . $mod['title'],
            'icon'  => $mod['icon'],
            'module' => $module,
            'modTitle' => $mod['title'],
        ]);
    }

    public function ajaxGetData()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setJSON(['error' => 'Unauthorized']);
        }

        $module = $this->request->getPost('module') ?? 'inm';
        if (!isset($this->modules[$module])) {
            $module = 'inm';
        }
        $mod = $this->modules[$module];

        $model = new IndicatorModel($mod['prefix'], $mod['categoryId']);
        $result = $model->getDatatable($this->request->getPost());

        return $this->response->setJSON($result);
    }

    public function getDetail()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setJSON(['status' => false, 'message' => 'Unauthorized']);
        }

        $module = $this->request->getPost('module') ?? 'inm';
        $id = (int) $this->request->getPost('id');

        if (!$id) {
            return $this->response->setJSON(['status' => false, 'message' => 'ID tidak valid']);
        }

        if (!isset($this->modules[$module])) {
            $module = 'inm';
        }
        $mod = $this->modules[$module];

        $model = new IndicatorModel($mod['prefix'], $mod['categoryId']);
        $data = $model->getDetail($id);

        if (!$data) {
            return $this->response->setJSON(['status' => false, 'message' => 'Data tidak ditemukan']);
        }

        return $this->response->setJSON(['status' => true, 'data' => $data]);
    }

    public function save()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setJSON(['status' => false, 'message' => 'Unauthorized']);
        }

        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => false, 'message' => 'Invalid request']);
        }

        $module = $this->request->getPost('module') ?? 'inm';
        if (!isset($this->modules[$module])) {
            $module = 'inm';
        }
        $mod = $this->modules[$module];

        $model = new IndicatorModel($mod['prefix'], $mod['categoryId']);

        $id = (int) $this->request->getPost('indicator_id');

        $data = [
            'indicator_element'         => $this->request->getPost('indicator_element'),
            'indicator_name_id'         => $this->request->getPost('indicator_name_id') ?? '',
            'indicator_target'          => $this->request->getPost('indicator_target') ?? '',
            'indicator_target_calculation' => $this->request->getPost('indicator_target_calculation') ?? '',
            'indicator_factors'         => $this->request->getPost('indicator_factors') ?? '',
            'indicator_units'           => $this->request->getPost('indicator_units') ?? '',
            'indicator_target_unit'     => $this->request->getPost('indicator_target_unit') ?? '',
            'indicator_frequency'       => $this->request->getPost('indicator_frequency') ?? 'D',
            'indicator_value_standard'  => $this->request->getPost('indicator_value_standard') ?? 0,
            'indicator_order_number'    => $this->request->getPost('indicator_order_number') ?? 0,
            'indicator_type'            => $this->request->getPost('indicator_type') ?? '',
            'indicator_monitoring_area' => $this->request->getPost('indicator_monitoring_area') ?? '',
            'indicator_source_of_data'  => $this->request->getPost('indicator_source_of_data') ?? '',
            'indicator_definition'      => $this->request->getPost('indicator_definition') ?? '',
            'indicator_criteria_inclusive' => $this->request->getPost('indicator_criteria_inclusive') ?? '',
            'indicator_criteria_exclusive' => $this->request->getPost('indicator_criteria_exclusive') ?? '',
            'indicator_lcl'             => $this->request->getPost('indicator_lcl') ?? '',
            'indicator_ucl'             => $this->request->getPost('indicator_ucl') ?? '',
            'indicator_valid_date'      => $this->request->getPost('indicator_valid_date') ?? null,
        ];

        if (empty($data['indicator_element'])) {
            return $this->response->setJSON(['status' => false, 'message' => 'Nama indikator harus diisi']);
        }

        try {
            if ($id > 0) {
                $model->updateIndicator($id, $data);
                $message = 'Indikator berhasil diupdate';
            } else {
                $id = $model->insertIndicator($data);
                $message = 'Indikator berhasil ditambahkan';
            }

            return $this->response->setJSON(['status' => true, 'message' => $message, 'id' => $id]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => false, 'message' => 'Gagal menyimpan: ' . $e->getMessage()]);
        }
    }

    public function delete()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setJSON(['status' => false, 'message' => 'Unauthorized']);
        }

        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => false, 'message' => 'Invalid request']);
        }

        $module = $this->request->getPost('module') ?? 'inm';
        $id = (int) $this->request->getPost('id');

        if (!$id) {
            return $this->response->setJSON(['status' => false, 'message' => 'ID tidak valid']);
        }

        if (!isset($this->modules[$module])) {
            $module = 'inm';
        }
        $mod = $this->modules[$module];

        $model = new IndicatorModel($mod['prefix'], $mod['categoryId']);

        try {
            $model->softDelete($id);
            return $this->response->setJSON(['status' => true, 'message' => 'Indikator berhasil dihapus']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => false, 'message' => 'Gagal menghapus: ' . $e->getMessage()]);
        }
    }

    public function restore()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setJSON(['status' => false, 'message' => 'Unauthorized']);
        }

        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => false, 'message' => 'Invalid request']);
        }

        $module = $this->request->getPost('module') ?? 'inm';
        $id = (int) $this->request->getPost('id');

        if (!$id) {
            return $this->response->setJSON(['status' => false, 'message' => 'ID tidak valid']);
        }

        if (!isset($this->modules[$module])) {
            $module = 'inm';
        }
        $mod = $this->modules[$module];

        $model = new IndicatorModel($mod['prefix'], $mod['categoryId']);

        try {
            $model->restore($id);
            return $this->response->setJSON(['status' => true, 'message' => 'Indikator berhasil dipulihkan']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => false, 'message' => 'Gagal memulihkan: ' . $e->getMessage()]);
        }
    }
}
