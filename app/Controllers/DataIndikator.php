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

        $recordStatus = $this->request->getPost('indicator_record_status');
        $recordStatus = ($recordStatus === 'A') ? 'A' : 'D';

        $data = [
            'indicator_element'                    => $this->request->getPost('indicator_element'),
            'indicator_name_id'                    => $this->request->getPost('indicator_name_id') ?? '',
            'indicator_institution_code'           => $this->request->getPost('indicator_institution_code') ?? '',
            'indicator_target'                     => $this->request->getPost('indicator_target') ?? '',
            'indicator_target_calculation'         => $this->request->getPost('indicator_target_calculation') ?? '',
            'indicator_factors'                    => $this->request->getPost('indicator_factors') ?? '',
            'indicator_units'                      => $this->request->getPost('indicator_units') ?? '',
            'indicator_target_unit'                => $this->request->getPost('indicator_target_unit') ?? '',
            'indicator_frequency'                  => $this->request->getPost('metode_pengumpulan_data') ?? 'D',
            'indicator_type'                       => $this->request->getPost('jenis_indikator') ?? '',
            'indicator_source_of_data'             => $this->request->getPost('sumber_data') ?? '',
            'indicator_definition'                 => $this->request->getPost('definisi_operasional') ?? '',
            'indicator_criteria_inclusive'         => $this->request->getPost('indicator_inclusive') ?? '',
            'indicator_criteria_exclusive'         => $this->request->getPost('indicator_exclusive') ?? '',
            'indicator_monitoring_area'            => $this->request->getPost('indicator_monitoring_area') ?? '',
            'indicator_valid_date'                 => $this->request->getPost('indicator_valid_date') ?: null,
            'indicator_record_status'              => $recordStatus,
            'indicator_dasar_pemikiran'            => $this->request->getPost('dasar_pemikiran') ?? '',
            'indicator_dimensi_mutu'               => $this->request->getPost('dimensi_mutu') ? implode(',', $this->request->getPost('dimensi_mutu')) : '',
            'indicator_tujuan'                     => $this->request->getPost('tujuan') ?? '',
            'indicator_periode_pengumpulan_data'   => $this->request->getPost('periode_pengumpulan_data') ?? '',
            'indicator_instrumen_pengambilan_data' => $this->request->getPost('instrumen_pengambilan_data') ?? '',
            'indicator_besar_sampel'               => $this->request->getPost('besar_sampel') ?? '',
            'indicator_cara_pengambilan_sampel'    => $this->request->getPost('cara_pengambilan_sampel') ?? '',
            'indicator_periode_analisis_pelaporan' => $this->request->getPost('periode_analisis_dan_pelaporan_data') ?? '',
            'indicator_penyajian_data'             => $this->request->getPost('penyajian_data') ?? '',
            'indicator_penanggung_jawab'           => $this->request->getPost('penanggung_jawab') ?? '',
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

        $db = db_connect();
        $varTable = $mod['prefix'] . 'quality_indicator_variable';
        $numDenumCount = $db->table($varTable)
            ->where('variable_indicator_id', $id)
            ->whereIn('variable_record_status', ['A', 'D'])
            ->countAllResults();

        if ($numDenumCount > 0) {
            return $this->response->setJSON(['status' => false, 'message' => 'Tidak bisa hapus indikator karena masih ada data Numerator/Denominator (' . $numDenumCount . ' data). Hapus data Numerator/Denominator terlebih dahulu.']);
        }

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

    public function ajaxGetNumDenum()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setJSON(['error' => 'Unauthorized']);
        }

        $indicatorId = (int) $this->request->getPost('indicator_id');
        if (!$indicatorId) {
            return $this->response->setJSON(['draw' => 1, 'recordsTotal' => 0, 'recordsFiltered' => 0, 'data' => []]);
        }

        $module = $this->request->getPost('module') ?? 'inm';
        $prefix = $this->modules[$module]['prefix'] ?? '';

        $model = new \App\Models\IndicatorGroupModel($prefix);
        $result = $model->getDatatable($this->request->getPost(), $indicatorId);

        return $this->response->setJSON($result);
    }

    public function getNumDenumDetail()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setJSON(['status' => false, 'message' => 'Unauthorized']);
        }

        $id = (int) $this->request->getPost('id');
        if (!$id) {
            return $this->response->setJSON(['status' => false, 'message' => 'ID tidak valid']);
        }

        $module = $this->request->getPost('module') ?? 'inm';
        $prefix = $this->modules[$module]['prefix'] ?? '';

        $model = new \App\Models\IndicatorGroupModel($prefix);
        $data = $model->getDetail($id);

        if (!$data) {
            return $this->response->setJSON(['status' => false, 'message' => 'Data tidak ditemukan']);
        }

        return $this->response->setJSON(['status' => true, 'data' => $data]);
    }

    public function saveNumDenum()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setJSON(['status' => false, 'message' => 'Unauthorized']);
        }

        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => false, 'message' => 'Invalid request']);
        }

        $module = $this->request->getPost('module') ?? 'inm';
        $prefix = $this->modules[$module]['prefix'] ?? '';

        $model = new \App\Models\IndicatorGroupModel($prefix);
        $id = (int) $this->request->getPost('variable_id');

        $data = [
            'variable_indicator_id'   => $this->request->getPost('variable_indicator_id'),
            'variable_institution_code' => $this->request->getPost('variable_institution_code') ?? '',
            'variable_name'           => $this->request->getPost('variable_name') ?? '',
            'variable_type'           => $this->request->getPost('variable_type') ?? '',
            'variable_unit_name'      => $this->request->getPost('variable_unit_name') ?? '',
        ];

        if (empty($data['variable_type'])) {
            return $this->response->setJSON(['status' => false, 'message' => 'Tipe variabel harus diisi']);
        }

        try {
            if ($id > 0) {
                $model->updateGroup($id, $data);
                $message = 'Data berhasil diupdate';
            } else {
                $id = $model->insertGroup($data);
                $message = 'Data berhasil ditambahkan';
            }

            return $this->response->setJSON(['status' => true, 'message' => $message, 'id' => $id]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => false, 'message' => 'Gagal menyimpan: ' . $e->getMessage()]);
        }
    }

    public function deleteNumDenum()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setJSON(['status' => false, 'message' => 'Unauthorized']);
        }

        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => false, 'message' => 'Invalid request']);
        }

        $id = (int) $this->request->getPost('id');
        if (!$id) {
            return $this->response->setJSON(['status' => false, 'message' => 'ID tidak valid']);
        }

        $module = $this->request->getPost('module') ?? 'inm';
        $prefix = $this->modules[$module]['prefix'] ?? '';

        $model = new \App\Models\IndicatorGroupModel($prefix);

        try {
            $model->softDelete($id);
            return $this->response->setJSON(['status' => true, 'message' => 'Data berhasil dihapus']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => false, 'message' => 'Gagal menghapus: ' . $e->getMessage()]);
        }
    }
}
