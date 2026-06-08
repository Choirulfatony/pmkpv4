<?php

namespace App\Controllers;

use App\Models\IndicatorValidationModel;
use App\Models\LoadModuleForminputModel;
use App\Models\SiimutMenuModel;

class Validation extends AppController
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

        $cfg = $this->modules[$module];
        $tahun = $this->request->getGet('tahun') ?? date('Y');
        $bulan = $this->request->getGet('bulan') ?? date('m');

        $role = session()->get('user_role');
        $menuModel = new SiimutMenuModel();
        $menus = $menuModel->getMenuByRole($role);

        $validationModel = new IndicatorValidationModel();
        $data = $validationModel->getPendingIndicators($cfg['categoryId'], (int)$tahun, (int)$bulan);

        $namaBulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        return $this->render('siimut/validation_list', [
            'judul'    => 'Validasi ' . $cfg['title'],
            'icon'     => '<i class="bi bi-check2-square"></i>',
            'menus'    => $menus,
            '_content' => view('siimut/validation_list', [
                'module'       => $module,
                'moduleTitle'  => $cfg['title'],
                'tahun'        => $tahun,
                'bulan'        => $bulan,
                'data'         => $data,
                'namaBulan'    => $namaBulan,
                'profileId'    => session('profile_id') ?? 0
            ])
        ]);
    }

    public function form(string $module = 'inm')
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/auth');
        }

        if (!isset($this->modules[$module])) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $this->disableCache();

        $cfg = $this->modules[$module];
        $indicatorId = (int) $this->request->getGet('indicator_id');
        $departmentId = $this->request->getGet('department_id');
        $tahun = (int) ($this->request->getGet('tahun') ?? date('Y'));
        $bulan = (int) ($this->request->getGet('bulan') ?? date('m'));

        if (!$indicatorId || !$departmentId) {
            return redirect()->to('siimut/validation/' . $module);
        }

        $role = session()->get('user_role');
        $menuModel = new SiimutMenuModel();
        $menus = $menuModel->getMenuByRole($role);

        $model = new LoadModuleForminputModel($cfg['prefix'], $cfg['categoryId']);
        $validationModel = new IndicatorValidationModel();

        $records = $validationModel->getRecordsForValidation($cfg['prefix'], $indicatorId, $departmentId, $tahun, $bulan);
        $info = $model->getIndicatorInfo($indicatorId);
        $deptInfo = $model->getDepartmentInfo($departmentId);
        $existingValidation = $validationModel->getExistingValidation($indicatorId, $departmentId, sprintf('%04d-%02d-01', $tahun, $bulan), $cfg['categoryId']);

        $namaBulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        return $this->render('siimut/validation_form', [
            'judul'    => 'Form Validasi ' . $cfg['title'],
            'icon'     => '<i class="bi bi-check2-square"></i>',
            'menus'    => $menus,
            '_content' => view('siimut/validation_form', [
                'module'       => $module,
                'moduleTitle'  => $cfg['title'],
                'indicatorId'  => $indicatorId,
                'departmentId' => $departmentId,
                'tahun'        => $tahun,
                'bulan'        => $bulan,
                'records'      => $records,
                'info'         => $info,
                'deptInfo'     => $deptInfo,
                'existing'     => $existingValidation,
                'namaBulan'    => $namaBulan,
                'profileId'    => session('profile_id') ?? 0
            ])
        ]);
    }

    public function save()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => false, 'message' => 'Invalid request']);
        }

        $module = $this->request->getPost('module');
        if (!isset($this->modules[$module])) {
            return $this->response->setJSON(['status' => false, 'message' => 'Modul tidak valid']);
        }

        $cfg = $this->modules[$module];
        $indicatorId = (int) $this->request->getPost('indicator_id');
        $departmentId = $this->request->getPost('department_id');
        $tahun = (int) ($this->request->getPost('tahun') ?? date('Y'));
        $bulan = (int) ($this->request->getPost('bulan') ?? date('m'));
        $period = sprintf('%04d-%02d-01', $tahun, $bulan);
        $validatorId = (int) (session('profile_id') ?? 0);

        $validationModel = new IndicatorValidationModel();

        $validationId = $validationModel->saveValidation([
            'validation_indicator_id'       => $indicatorId,
            'validation_department_id'      => $departmentId,
            'validation_period'             => $period,
            'validation_validator_id'       => $validatorId,
            'validation_data_lengkap'       => $this->request->getPost('data_lengkap'),
            'validation_sumber_data_sesuai' => $this->request->getPost('sumber_data_sesuai'),
            'validation_numerator_sesuai'   => $this->request->getPost('numerator_sesuai'),
            'validation_denominator_sesuai' => $this->request->getPost('denominator_sesuai'),
            'validation_perhitungan_benar'  => $this->request->getPost('perhitungan_benar'),
            'validation_sample_count'       => (int) ($this->request->getPost('sample_count') ?? 0),
            'validation_note'               => $this->request->getPost('note'),
            'validation_category_id'        => $cfg['categoryId'],
        ]);

        if (!$validationId) {
            return $this->response->setJSON(['status' => false, 'message' => 'Gagal menyimpan validasi']);
        }

        $result = $validationModel->find($validationId);

        // Jika valid → approve semua draft record bulan itu
        if ($result->validation_result === 'valid') {
            $model = new LoadModuleForminputModel($cfg['prefix'], $cfg['categoryId']);
            $affected = $validationModel->approveMonthRecords($cfg['prefix'], $indicatorId, $departmentId, $tahun, $bulan, $validatorId);
            return $this->response->setJSON([
                'status'  => true,
                'message' => 'Validasi berhasil, ' . $affected . ' data disetujui',
                'score'   => $result->validation_score,
                'result'  => $result->validation_result
            ]);
        }

        return $this->response->setJSON([
            'status'  => true,
            'message' => 'Validasi berhasil, data dikembalikan ke unit untuk perbaikan',
            'score'   => $result->validation_score,
            'result'  => $result->validation_result
        ]);
    }
}
