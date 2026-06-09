<?php

namespace App\Controllers;

use App\Models\DepartmentModel;

class Department extends AppController
{
    protected DepartmentModel $departmentModel;

    public function __construct()
    {
        parent::__construct();
        $this->departmentModel = new DepartmentModel();
    }

    public function index()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/auth');
        }

        $role = session()->get('user_role');
        if (!in_array($role, ['ADMINISTRATOR'])) {
            return redirect()->to('/siimut/dashboard')->with('error', 'Hanya untuk Administrator');
        }

        return $this->render('siimut/department_list', [
            'judul' => 'Daftar Unit / Bagian',
            'icon'  => '<i class="bi bi-building"></i>',
            'total' => $this->departmentModel->getTotalDepartments(),
        ]);
    }

    public function ajaxGetData()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        }

        $post = $this->request->getPost();
        $result = $this->departmentModel->getDatatable($post);

        $rows = [];
        $no = ($post['start'] ?? 0) + 1;

        foreach ($result['data'] as $row) {
            $indicatorTypes = [
                1 => ['label' => 'INM',     'color' => 'primary', 'module' => 'inm'],
                5 => ['label' => 'IMPRS',   'color' => 'success', 'module' => 'imprs'],
                6 => ['label' => 'IMPUNIT', 'color' => 'warning', 'module' => 'impunit'],
                7 => ['label' => 'IKP',     'color' => 'info',    'module' => 'ikp'],
            ];

            $badges = '';
            foreach ($indicatorTypes as $type => $cfg) {
                $count = $this->departmentModel->getIndicatorCount($row->department_id, $type);
                $active = $count > 0;
                $cls = $active ? 'btn-success' : 'btn-outline-secondary';
                $title = $cfg['label'] . ($active ? " ({$count} indikator)" : ' (belum ada data)');
                $badges .= '<button type="button" class="btn btn-sm btn-indicator ' . $cls . ' btn-show-indicators" '
                    . 'data-dept-id="' . $row->department_id . '" '
                    . 'data-dept-name="' . esc($row->department_name) . '" '
                    . 'data-type="' . $type . '" '
                    . 'data-label="' . $cfg['label'] . '" '
                    . 'title="' . $title . '">'
                    . $cfg['label']
                    . '</button> ';
            }

            $isAktif = $row->department_record_status === 'A';
            $disableChecked = $isAktif ? 'checked' : '';
            $toggleLabel = $isAktif ? 'Aktif' : 'Nonaktif';
            $disableToggle = '<div class="form-check form-switch d-inline-block">'
                . '<input class="form-check-input btn-toggle-disable" type="checkbox" data-id="' . $row->department_id . '" data-status="' . $row->department_record_status . '" ' . $disableChecked . '>'
                . '<label class="form-check-label small">' . $toggleLabel . '</label>'
                . '</div>';

            $actions = '<div class="btn-group btn-group-sm">';
            $actions .= '<a href="' . site_url('siimut/unit/edit/' . $row->department_id) . '" class="btn btn-outline-primary" title="Edit"><i class="bi bi-pencil-square"></i></a>';
            $actions .= '<button type="button" class="btn btn-outline-danger btn-delete" data-id="' . $row->department_id . '" data-name="' . esc($row->department_name) . '" title="Hapus"><i class="bi bi-trash"></i></button>';
            $actions .= '</div>';

            $rows[] = [
                'no'          => $no++,
                'nama'        => esc($row->department_name),
                'keterangan'  => esc($row->department_description ?? '-'),
                'akses'       => $badges,
                'status'      => $disableToggle,
                'actions'     => $actions,
            ];
        }

        return $this->response->setJSON([
            'draw'            => (int) ($post['draw'] ?? 1),
            'recordsTotal'    => $result['recordsTotal'],
            'recordsFiltered' => $result['recordsFiltered'],
            'data'            => $rows,
        ]);
    }

    public function create()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/auth');
        }

        $role = session()->get('user_role');
        if (!in_array($role, ['ADMINISTRATOR'])) {
            return redirect()->to('/siimut/dashboard')->with('error', 'Hanya untuk Administrator');
        }

        return $this->render('siimut/department_add', [
            'judul' => 'Tambah Unit / Bagian',
            'icon'  => '<i class="bi bi-building-add"></i>',
        ]);
    }

    public function store()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setStatusCode(401)->setJSON(['status' => false, 'message' => 'Unauthorized']);
        }

        $role = session()->get('user_role');
        if (!in_array($role, ['ADMINISTRATOR'])) {
            return $this->response->setStatusCode(403)->setJSON(['status' => false, 'message' => 'Akses ditolak']);
        }

        $name = trim($this->request->getPost('department_name'));
        $desc = trim($this->request->getPost('department_description'));
        $instCode = trim($this->request->getPost('department_institution_code'));

        if (!$name) {
            return $this->response->setJSON(['status' => false, 'message' => 'Nama unit/bagian wajib diisi']);
        }

        if ($this->departmentModel->nameExists($name)) {
            return $this->response->setJSON(['status' => false, 'message' => 'Nama unit/bagian sudah ada']);
        }

        $newId = $this->departmentModel->createDepartment([
            'department_name'             => $name,
            'department_description'      => $desc,
            'department_institution_code' => $instCode,
            'department_record_status'    => 'A',
        ]);

        if ($newId) {
            return $this->response->setJSON(['status' => true, 'message' => 'Unit/bagian berhasil ditambahkan']);
        }

        return $this->response->setJSON(['status' => false, 'message' => 'Gagal menambahkan unit/bagian']);
    }

    public function edit(int $id)
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/auth');
        }

        $role = session()->get('user_role');
        if (!in_array($role, ['ADMINISTRATOR'])) {
            return redirect()->to('/siimut/dashboard')->with('error', 'Hanya untuk Administrator');
        }

        $dept = $this->departmentModel->getDepartmentById($id);
        if (!$dept) {
            return redirect()->to('siimut/unit')->with('error', 'Unit/bagian tidak ditemukan');
        }

        $indicatorAccess = $this->departmentModel->getIndicatorAccess($id);

        return $this->render('siimut/department_edit', [
            'judul'            => 'Edit Unit / Bagian',
            'icon'             => '<i class="bi bi-building-gear"></i>',
            'department'       => $dept,
            'indicatorAccess'  => $indicatorAccess,
        ]);
    }

    public function update(int $id)
    {
        if (!session()->get('logged_in')) {
            return $this->response->setStatusCode(401)->setJSON(['status' => false, 'message' => 'Unauthorized']);
        }

        $role = session()->get('user_role');
        if (!in_array($role, ['ADMINISTRATOR'])) {
            return $this->response->setStatusCode(403)->setJSON(['status' => false, 'message' => 'Akses ditolak']);
        }

        $name = trim($this->request->getPost('department_name'));
        $desc = trim($this->request->getPost('department_description'));
        $instCode = trim($this->request->getPost('department_institution_code'));

        if (!$name) {
            return $this->response->setJSON(['status' => false, 'message' => 'Nama unit/bagian wajib diisi']);
        }

        if ($this->departmentModel->nameExists($name, $id)) {
            return $this->response->setJSON(['status' => false, 'message' => 'Nama unit/bagian sudah ada']);
        }

        $result = $this->departmentModel->updateDepartment($id, [
            'department_name'             => $name,
            'department_description'      => $desc,
            'department_institution_code' => $instCode,
        ]);

        if ($result) {
            return $this->response->setJSON(['status' => true, 'message' => 'Data unit/bagian berhasil diperbarui']);
        }

        return $this->response->setJSON(['status' => false, 'message' => 'Gagal memperbarui data']);
    }

    public function delete(int $id)
    {
        if (!session()->get('logged_in')) {
            return $this->response->setStatusCode(401)->setJSON(['status' => false, 'message' => 'Unauthorized']);
        }

        $role = session()->get('user_role');
        if (!in_array($role, ['ADMINISTRATOR'])) {
            return $this->response->setStatusCode(403)->setJSON(['status' => false, 'message' => 'Akses ditolak']);
        }

        $dept = $this->departmentModel->getDepartmentById($id);
        if (!$dept) {
            return $this->response->setJSON(['status' => false, 'message' => 'Unit/bagian tidak ditemukan']);
        }

        $result = $this->departmentModel->softDelete($id);

        if ($result) {
            return $this->response->setJSON(['status' => true, 'message' => 'Unit/bagian berhasil dihapus']);
        }

        return $this->response->setJSON(['status' => false, 'message' => 'Gagal menghapus unit/bagian']);
    }

    public function ajaxGetIndicators()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        }

        $deptId = (int) $this->request->getPost('department_id');
        $type   = (int) $this->request->getPost('group_type');

        if (!$deptId || !$type) {
            return $this->response->setJSON(['data' => []]);
        }

        $db = db_connect();
        $tableMap = [1 => 'quality_indicator_group', 5 => 'local_quality_indicator_group', 6 => 'local_quality_indicator_group', 7 => 'local_quality_indicator_group'];
        $prefixMap = [1 => '', 5 => 'local_', 6 => 'local_', 7 => 'local_'];
        $groupTable = $tableMap[$type] ?? 'quality_indicator_group';
        $prefix = $prefixMap[$type] ?? '';
        $indTable = $prefix . 'quality_indicator';
        $categoryMap = [1 => '4', 5 => '5', 6 => '6', 7 => '7'];

        $sql = "SELECT 
                    qig.group_id,
                    qig.group_period,
                    qig.group_days,
                    qig.group_indicator_id,
                    qig.group_record_status,
                    qi.indicator_id,
                    qi.indicator_element,
                    qi.indicator_target,
                    qi.indicator_units,
                    qi.indicator_frequency
                FROM {$groupTable} qig
                JOIN {$indTable} qi ON qi.indicator_id = qig.group_indicator_id
                WHERE qig.group_department_id = ?
                  AND qig.group_type = ?
                  AND qig.group_record_status IN ('A', 'D')
                  AND qi.indicator_record_status = 'A'
                ORDER BY qig.group_period DESC, qi.indicator_order_number ASC, qi.indicator_id ASC";

        $data = $db->query($sql, [(string) $deptId, $type])->getResult();

        $freqMap = ['D' => 'Harian', 'M' => 'Bulanan', 'W' => 'Mingguan', 'Y' => 'Tahunan'];
        $rows = [];
        foreach ($data as $row) {
            $rows[] = [
                'group_id'            => (int) $row->group_id,
                'group_period'        => $row->group_period,
                'group_days'          => (int) ($row->group_days ?? 0),
                'group_record_status' => $row->group_record_status,
                'indicator_id'        => (int) $row->indicator_id,
                'indicator_element'   => $row->indicator_element,
                'indicator_target'    => $row->indicator_target,
                'indicator_units'     => $row->indicator_units,
                'indicator_frequency' => $freqMap[$row->indicator_frequency] ?? $row->indicator_frequency,
            ];
        }

        return $this->response->setJSON(['data' => $rows]);
    }

    public function ajaxGetAvailableIndicators()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        }

        $type = (int) $this->request->getPost('group_type');
        $this->indicatorTables = [1 => 'quality_indicator_group', 5 => 'local_quality_indicator_group', 6 => 'local_quality_indicator_group', 7 => 'local_quality_indicator_group'];
        $this->indicatorPrefixes = [1 => '', 5 => 'local_', 6 => 'local_', 7 => 'local_'];
        $this->categoryIds = [1 => '4', 5 => '5', 6 => '6', 7 => '7'];

        $prefix = $this->indicatorPrefixes[$type] ?? '';
        $catId = $this->categoryIds[$type] ?? '4';
        $table = $prefix . 'quality_indicator';

        $db = db_connect();
        $data = $db->table($table . ' qi')
            ->select('qi.indicator_id, qi.indicator_element, qi.indicator_type, qi.indicator_monitoring_area')
            ->where('qi.indicator_category_id', $catId)
            ->where('qi.indicator_record_status', 'A')
            ->orderBy('qi.indicator_order_number ASC, qi.indicator_id ASC')
            ->get()
            ->getResult();

        return $this->response->setJSON(['data' => $data]);
    }

    public function ajaxAddGroup()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setStatusCode(401)->setJSON(['status' => false, 'message' => 'Unauthorized']);
        }
        $role = session()->get('user_role');
        if (!in_array($role, ['ADMINISTRATOR'])) {
            return $this->response->setStatusCode(403)->setJSON(['status' => false, 'message' => 'Akses ditolak']);
        }

        $deptId         = (int) $this->request->getPost('department_id');
        $type           = (int) $this->request->getPost('group_type');
        $indicatorId    = (string) $this->request->getPost('indicator_id');
        $period         = trim($this->request->getPost('group_period'));
        $days           = (int) $this->request->getPost('group_days');
        $institutionCode = trim($this->request->getPost('institution_code') ?? 'RSSM');

        if (!$deptId || !$type || !$indicatorId || !$period) {
            return $this->response->setJSON(['status' => false, 'message' => 'Data tidak lengkap']);
        }

        $tableMap = [1 => 'quality_indicator_group', 5 => 'local_quality_indicator_group', 6 => 'local_quality_indicator_group', 7 => 'local_quality_indicator_group'];
        $groupTable = $tableMap[$type] ?? 'quality_indicator_group';

        $db = db_connect();

        $exists = $db->table($groupTable)
            ->where('group_department_id', (string) $deptId)
            ->where('group_indicator_id', $indicatorId)
            ->where('group_type', $type)
            ->where('group_period', $period)
            ->where('group_record_status', 'A')
            ->countAllResults();

        if ($exists > 0) {
            return $this->response->setJSON(['status' => false, 'message' => 'Indikator sudah terdaftar untuk periode ini']);
        }

        $db->table($groupTable)->insert([
            'group_indicator_id'     => $indicatorId,
            'group_department_id'    => (string) $deptId,
            'group_institution_code' => $institutionCode,
            'group_period'           => $period,
            'group_type'             => (string) $type,
            'group_days'             => $days,
            'group_record_status'    => 'A',
        ]);

        return $this->response->setJSON(['status' => true, 'message' => 'Indikator berhasil ditambahkan']);
    }

    public function ajaxUpdateGroup()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setStatusCode(401)->setJSON(['status' => false, 'message' => 'Unauthorized']);
        }
        $role = session()->get('user_role');
        if (!in_array($role, ['ADMINISTRATOR'])) {
            return $this->response->setStatusCode(403)->setJSON(['status' => false, 'message' => 'Akses ditolak']);
        }

        $groupId  = (int) $this->request->getPost('group_id');
        $type     = (int) $this->request->getPost('group_type');
        $period   = trim($this->request->getPost('group_period'));
        $days     = (int) $this->request->getPost('group_days');

        if (!$groupId || !$period) {
            return $this->response->setJSON(['status' => false, 'message' => 'Data tidak lengkap']);
        }

        $tableMap = [1 => 'quality_indicator_group', 5 => 'local_quality_indicator_group', 6 => 'local_quality_indicator_group', 7 => 'local_quality_indicator_group'];
        $groupTable = $tableMap[$type] ?? 'quality_indicator_group';

        $db = db_connect();
        $db->table($groupTable)
            ->where('group_id', $groupId)
            ->update([
                'group_period' => $period,
                'group_days'   => $days,
            ]);

        return $this->response->setJSON(['status' => true, 'message' => 'Data berhasil diperbarui']);
    }

    public function ajaxDeleteGroup()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setStatusCode(401)->setJSON(['status' => false, 'message' => 'Unauthorized']);
        }
        $role = session()->get('user_role');
        if (!in_array($role, ['ADMINISTRATOR'])) {
            return $this->response->setStatusCode(403)->setJSON(['status' => false, 'message' => 'Akses ditolak']);
        }

        $groupId = (int) $this->request->getPost('group_id');
        $type    = (int) $this->request->getPost('group_type');

        if (!$groupId) {
            return $this->response->setJSON(['status' => false, 'message' => 'ID tidak valid']);
        }

        $tableMap = [1 => 'quality_indicator_group', 5 => 'local_quality_indicator_group', 6 => 'local_quality_indicator_group', 7 => 'local_quality_indicator_group'];
        $groupTable = $tableMap[$type] ?? 'quality_indicator_group';

        $db = db_connect();
        $db->table($groupTable)
            ->where('group_id', $groupId)
            ->update(['group_record_status' => 'D']);

        return $this->response->setJSON(['status' => true, 'message' => 'Indikator berhasil dihapus']);
    }

    public function toggleDisable(int $id)
    {
        if (!session()->get('logged_in')) {
            return $this->response->setStatusCode(401)->setJSON(['status' => false, 'message' => 'Unauthorized']);
        }

        $role = session()->get('user_role');
        if (!in_array($role, ['ADMINISTRATOR'])) {
            return $this->response->setStatusCode(403)->setJSON(['status' => false, 'message' => 'Akses ditolak']);
        }

        $dept = $this->departmentModel->getDepartmentById($id);
        if (!$dept) {
            return $this->response->setJSON(['status' => false, 'message' => 'Unit/bagian tidak ditemukan']);
        }

        $newStatus = $dept->department_record_status === 'A' ? 'D' : 'A';
        $result = $this->departmentModel->toggleRecordStatus($id, $newStatus);

        if ($result) {
            $label = $newStatus === 'D' ? 'dinonaktifkan' : 'diaktifkan';
            return $this->response->setJSON(['status' => true, 'message' => 'Unit/bagian berhasil ' . $label, 'new_status' => $newStatus]);
        }

        return $this->response->setJSON(['status' => false, 'message' => 'Gagal mengubah status']);
    }
}
