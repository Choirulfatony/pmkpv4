<?php

namespace App\Controllers;

use App\Models\SiimutMenuModel;
use App\Models\RekapLaporanImprsModel;

class FormImprs extends AppController
{
    protected $model;

    public function __construct()
    {
        $this->model = new RekapLaporanImprsModel();
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
        $department_id = session()->get('department_id') ?? 0;

        $db = db_connect();

        $departments = $db->table('local_quality_indicator_group lqig')
            ->select('DISTINCT master_institution_department.department_id, master_institution_department.department_name')
            ->join('local_quality_indicator', 'local_quality_indicator.indicator_id = lqig.group_indicator_id', 'left')
            ->join('master_institution_department', 'master_institution_department.department_id = lqig.group_department_id', 'left')
            ->where('local_quality_indicator.indicator_category_id', '5')
            ->where('local_quality_indicator.indicator_record_status', 'A')
            ->groupStart()
            ->where('lqig.group_period', $tahun)
            ->orWhere('lqig.group_period', $tahun - 1)
            ->orWhere('lqig.group_period', $tahun - 2)
            ->groupEnd();

        if (!in_array($role, ['ADMINISTRATOR', 'KOMITE']) && $department_id > 0) {
            $departments->where('master_institution_department.department_id', $department_id);
        }

        $departments = $departments->get()->getResult();

        return $this->render('siimut/form_imprs', [
            'judul'         => 'Form Input IMPRS',
            'icon'          => '<i class="bi bi-pencil-square"></i>',
            '_content'      => view('siimut/form_imprs', [
                'tahun'         => $tahun,
                'departments'  => $departments,
            ]),
            'menus'         => $menus
        ]);
    }

    public function ajax_list()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Invalid request']);
        }

        $post = $this->request->getPost();
        $vtahun = $post['vtahun'] ?? date('Y');

        $indicators = $this->model->get_form_indicator_imprs(['vtahun' => $vtahun, ...$post]);
        $countAll = $this->model->count_all_form_imprs(['vtahun' => $vtahun]);
        $countFiltered = $this->model->count_filtered_form_imprs(['vtahun' => $vtahun, ...$post]);

        $data = [];
        $no = isset($post['start']) ? (int) $post['start'] : 0;

        foreach ($indicators as $row) {
            $no++;

            $selectDept = '<select class="form-select form-select-sm department-select" data-indicator="' . esc($row->indicator_id) . '">';
            $selectDept .= '<option value="">-- Pilih Ruangan --</option>';
            $selectDept .= '<option value="' . esc($row->department_id) . '" selected>' . esc($row->department_name) . '</option>';
            $selectDept .= '</select>';

            $btnInput = '<button type="button" class="btn btn-primary btn-sm" onclick="showInputForm(' . esc($row->indicator_id) . ', ' . esc($row->department_id) . ')"><i class="bi bi-pencil"></i> Input</button>';

            $data[] = [
                $no,
                esc($row->indicator_element),
                esc($row->department_name),
                esc($row->indicator_target) . ' ' . esc($row->indicator_units),
                $selectDept,
                $btnInput
            ];
        }

        return $this->response->setJSON([
            'draw'            => $post['draw'] ?? 1,
            'recordsTotal'    => $countAll,
            'recordsFiltered' => $countFiltered,
            'data'            => $data
        ]);
    }

    public function get_indicators()
    {
        $tahun = $this->request->getGet('tahun') ?? date('Y');
        $department_id = $this->request->getGet('department_id') ?? 0;

        $db = db_connect();
        
        $builder = $db->table('local_quality_indicator_group lqig');
        $builder->select('
            lqig.group_indicator_id,
            lqig.group_department_id,
            lqig.group_days,
            lqi.indicator_id,
            lqi.indicator_element,
            lqi.indicator_target,
            lqi.indicator_units,
            lqi.indicator_target_unit,
            lqi.indicator_target_calculation,
            lqi.indicator_factors,
            mid.department_id,
            mid.department_name
        ');
        $builder->join('local_quality_indicator lqi', 'lqi.indicator_id = lqig.group_indicator_id', 'left');
        $builder->join('master_institution_department mid', 'mid.department_id = lqig.group_department_id', 'left');
        $builder->where('lqi.indicator_category_id', '5');
        $builder->where('lqi.indicator_record_status', 'A');
        $builder->groupStart();
        $builder->where('lqig.group_period', $tahun);
        $builder->orWhere('lqig.group_period', $tahun - 1);
        $builder->orWhere('lqig.group_period', $tahun - 2);
        $builder->groupEnd();
        if ($department_id > 0) {
            $builder->where('lqig.group_department_id', $department_id);
        }
        $role = session()->get('user_role');
        $userDepartmentId = session()->get('department_id') ?? 0;
        if (!in_array($role, ['ADMINISTRATOR', 'KOMITE']) && $userDepartmentId > 0) {
            $builder->where('lqig.group_department_id', $userDepartmentId);
        }
        $builder->groupBy('lqig.group_indicator_id, lqig.group_department_id');
        
        $indicators = $builder->get()->getResult();
        
        return $this->response->setJSON($indicators);
    }

    public function get_indicator_detail()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Invalid request']);
        }

        $indicator_id = $this->request->getGet('indicator_id') ?? 0;
        $tanggal = $this->request->getGet('tanggal') ?? date('Y-m-d');
        $department_id = $this->request->getGet('department_id') ?? 0;

        $detail = $this->model->getDetailByIdImprs((int) $indicator_id);
        $variables = $this->model->get_by_id_ipmrs((int) $indicator_id);

        $tahun = date('Y', strtotime($tanggal));
        $bulan = date('m', strtotime($tanggal));
        $hari = date('d', strtotime($tanggal));

        $existingData = $this->model->get_ajax_data_form_imprs($indicator_id, $department_id, $tahun, $bulan, $hari);
        $monthlyTotal = $this->model->get_ajax_data_total_imprs($indicator_id, $department_id, $tahun, $bulan, $hari);

        return $this->response->setJSON([
            'indicator'      => $detail,
            'variables'      => $variables,
            'existing_data'  => $existingData,
            'monthly_total'  => $monthlyTotal
        ]);
    }

    public function save()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => false, 'message' => 'Invalid request']);
        }

        $db = db_connect();

        $indicator_id = $this->request->getPost('indicator_id');
        $department_id = $this->request->getPost('department_id');
        $tanggal = $this->request->getPost('tanggal');
        $numerator = $this->request->getPost('numerator');
        $denumerator = $this->request->getPost('denumerator');

        log_message('error', 'FormImprs save() - indicator_id: ' . $indicator_id . ', department_id: ' . $department_id . ', tanggal: ' . $tanggal . ', num: ' . $numerator . ', denum: ' . $denumerator);

        if (!$indicator_id || !$department_id || !$tanggal) {
            return $this->response->setJSON(['status' => false, 'message' => 'Data tidak lengkap: indicator=' . $indicator_id . ', dept=' . $department_id . ', tanggal=' . $tanggal]);
        }

        $num = is_numeric($numerator) ? (float) $numerator : 0;
        $den = is_numeric($denumerator) ? (float) $denumerator : 0;
        $user_id = session()->get('hris_user_id') ?? session()->get('user_id') ?? 0;

        log_message('error', 'FormImprs save() - num: ' . $num . ', den: ' . $den . ', user_id: ' . $user_id);

        $checkQuery = $db->table('local_quality_indicator_result')
            ->where('result_indicator_id', $indicator_id)
            ->where('result_department_id', $department_id)
            ->where('result_period', $tanggal)
            ->get()
            ->getRow();

        $user_id = session()->get('hris_user_id') ?? session()->get('user_id') ?? 0;

        if ($checkQuery) {
            log_message('error', 'FormImprs save() - UPDATE existing record id: ' . $checkQuery->result_id);
            $result = $db->table('local_quality_indicator_result')
                ->where('result_id', $checkQuery->result_id)
                ->update([
                    'result_numerator_value'   => $num,
                    'result_denumerator_value' => $den,
                    'result_update_by'         => $user_id,
                    'result_update_at'         => date('Y-m-d H:i:s')
                ]);
            log_message('error', 'FormImprs save() - UPDATE result: ' . ($result ? 'SUCCESS' : 'FAILED'));
        } else {
            log_message('error', 'FormImprs save() - INSERT new record');
            $result = $db->table('local_quality_indicator_result')
                ->insert([
                    'result_indicator_id'       => $indicator_id,
                    'result_department_id'     => $department_id,
                    'result_period'            => $tanggal,
                    'result_numerator_value'   => $num,
                    'result_denumerator_value'  => $den,
                    'result_create_by'         => $user_id,
                    'result_create_at'         => date('Y-m-d H:i:s')
                ]);
            log_message('error', 'FormImprs save() - INSERT result: ' . ($result ? 'SUCCESS' : 'FAILED') . ', insertID: ' . $db->insertID());
        }

        $lastError = $db->error();
        if (!empty($lastError['message'])) {
            log_message('error', 'FormImprs save() - DB Error: ' . $lastError['message']);
        }

        if ($db->affectedRows() > 0 || $checkQuery) {
            return $this->response->setJSON(['status' => true, 'message' => 'Data berhasil disimpan']);
        } else {
            return $this->response->setJSON(['status' => false, 'message' => 'Gagal menyimpan data: ' . $lastError['message']]);
        }
    }

    public function save_perbaikan()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => false, 'message' => 'Invalid request']);
        }

        $db = db_connect();

        $indicator_id = $this->request->getPost('indicator_id');
        $department_id = $this->request->getPost('department_id');
        $tanggal = $this->request->getPost('tanggal');
        $rencana = $this->request->getPost('rencana_perbaikan');

        if (!$indicator_id || !$department_id || !$tanggal) {
            return $this->response->setJSON(['status' => false, 'message' => 'Data tidak lengkap']);
        }

        $checkQuery = $db->table('local_rencana_perbaikan')
            ->where('result_indicator_id', $indicator_id)
            ->where('result_department_id', $department_id)
            ->where('result_period', $tanggal)
            ->get()
            ->getRow();

        $db->transStart();

        if ($checkQuery) {
            $db->table('local_rencana_perbaikan')
                ->where('rencana_id', $checkQuery->rencana_id)
                ->update([
                    'rencana_perbaikan'  => $rencana,
                    'rencana_update_by'  => session()->get('hris_user_id') ?? session()->get('user_id'),
                    'rencana_update_at'  => date('Y-m-d H:i:s')
                ]);
        } else {
            $db->table('local_rencana_perbaikan')
                ->insert([
                    'result_indicator_id'   => $indicator_id,
                    'result_department_id'  => $department_id,
                    'result_period'         => $tanggal,
                    'rencana_perbaikan'     => $rencana,
                    'rencana_create_by'     => session()->get('hris_user_id') ?? session()->get('user_id'),
                    'rencana_create_at'     => date('Y-m-d H:i:s')
                ]);
        }

        $db->transComplete();

        if ($db->transStatus()) {
            return $this->response->setJSON(['status' => true, 'message' => 'Rencana perbaikan berhasil disimpan']);
        } else {
            return $this->response->setJSON(['status' => false, 'message' => 'Gagal menyimpan rencana perbaikan']);
        }
    }
}
