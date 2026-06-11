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
            'judul'       => 'Daftar Unit / Bagian',
            'icon'        => '<i class="bi bi-building"></i>',
            'total'       => $this->departmentModel->getTotalDepartments(),
            'totalInactive' => $this->departmentModel->getInactiveDepartmentsCount(),
            'totalWithIndicators' => $this->departmentModel->getDepartmentsWithIndicatorCount(),
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
                1 => ['label' => 'INM',     'icon' => 'bi-activity',       'color' => 'primary', 'module' => 'inm'],
                5 => ['label' => 'IMPRS',   'icon' => 'bi-building',       'color' => 'success', 'module' => 'imprs'],
                6 => ['label' => 'IMPUNIT', 'icon' => 'bi-diagram-3',      'color' => 'warning', 'module' => 'impunit'],
                7 => ['label' => 'IKP',     'icon' => 'bi-heart-pulse',    'color' => 'info',    'module' => 'ikp'],
            ];

            $badges = '<div class="d-flex flex-wrap gap-1 justify-content-center">';
            foreach ($indicatorTypes as $type => $cfg) {
                $count = $this->departmentModel->getIndicatorCount($row->department_id, $type);
                $active = $count > 0;
                $cls = $active ? 'btn-outline-' . $cfg['color'] : 'btn-outline-secondary';
                $title = $cfg['label'] . ($active ? " ({$count} indikator)" : ' (belum ada data)');
                $badges .= '<button type="button" class="btn btn-sm btn-icon ' . $cls . ' btn-show-indicators" '
                    . 'data-dept-id="' . $row->department_id . '" '
                    . 'data-dept-name="' . esc($row->department_name) . '" '
                    . 'data-type="' . $type . '" '
                    . 'data-label="' . $cfg['label'] . '" '
                    . 'title="' . $title . '">'
                    . '<i class="bi ' . $cfg['icon'] . '"></i>'
                    . '</button>';
            }
            $badges .= '</div>';

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
                LEFT JOIN {$indTable} qi ON qi.indicator_id = qig.group_indicator_id
                WHERE qig.group_department_id = ?
                  AND qig.group_type = ?
                  AND qig.group_record_status = 'A'
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
                'indicator_element'   => $row->indicator_element ?? '(indikator tidak ditemukan)',
                'indicator_target'    => $row->indicator_target,
                'indicator_units'     => $row->indicator_units,
                'indicator_frequency' => ($row->indicator_frequency && isset($freqMap[$row->indicator_frequency])) ? $freqMap[$row->indicator_frequency] : ($row->indicator_frequency ?? '-'),
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
            ->update(['group_record_status' => 'X']);

        return $this->response->setJSON(['status' => true, 'message' => 'Indikator berhasil dihapus']);
    }

    public function trash()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/auth');
        }

        $role = session()->get('user_role');
        if (!in_array($role, ['ADMINISTRATOR'])) {
            return redirect()->to('/siimut/dashboard')->with('error', 'Hanya untuk Administrator');
        }

        return $this->render('siimut/department_trash', [
            'judul' => 'Tempat Sampah Indikator Unit',
            'icon'  => '<i class="bi bi-trash"></i>',
        ]);
    }

    public function ajaxGetTrash()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        }

        $tableMap = [
            1 => ['table' => 'quality_indicator_group', 'indicator' => 'quality_indicator', 'label' => 'INM'],
            5 => ['table' => 'local_quality_indicator_group', 'indicator' => 'local_quality_indicator', 'label' => 'IMPRS'],
            6 => ['table' => 'local_quality_indicator_group', 'indicator' => 'local_quality_indicator', 'label' => 'IMPUNIT'],
            7 => ['table' => 'local_quality_indicator_group', 'indicator' => 'local_quality_indicator', 'label' => 'IKP'],
        ];

        $db = db_connect();
        $rows = [];
        $no = 1;

        $post = $this->request->getPost();
        $draw  = (int) ($post['draw'] ?? 1);
        $start = (int) ($post['start'] ?? 0);
        $length = (int) ($post['length'] ?? 10);
        $searchValue = $post['search']['value'] ?? '';

        // Collect all X records across all types
        $allData = [];
        $allFiltered = [];
        foreach ($tableMap as $type => $cfg) {
            $builder = $db->table($cfg['table'] . ' qig');
            $builder->select("
                qig.group_id,
                qig.group_type,
                qig.group_period,
                qig.group_days,
                qig.group_department_id,
                qig.group_institution_code,
                qig.group_indicator_id,
                qig.group_record_status,
                {$type} as group_type_num,
                '" . $cfg['label'] . "' as type_label
            ");
            $builder->where('qig.group_record_status', 'X');
            $builder->where('qig.group_type', $type);

            if ($searchValue) {
                $builder->groupStart();
                $builder->like('qig.group_period', $searchValue);
                $builder->orLike('qig.group_institution_code', $searchValue);
                $builder->groupEnd();
            }

            $data = $builder->get()->getResult();
            $allData = array_merge($allData, $data);
        }

        // Enrich with department & indicator names
        foreach ($allData as &$row) {
            $dept = $db->table('master_institution_department')
                ->select('department_name')
                ->where('department_id', $row->group_department_id)
                ->get()->getRow();
            $row->department_name = $dept ? $dept->department_name : '(dihapus)';

            $cfg = $tableMap[$row->group_type_num] ?? $tableMap[1];
            $ind = $db->table($cfg['indicator'])
                ->select('indicator_element')
                ->where('indicator_id', $row->group_indicator_id)
                ->get()->getRow();
            $row->indicator_element = $ind ? $ind->indicator_element : '(dihapus)';
        }
        unset($row);

        $recordsTotal = count($allData);
        $recordsFiltered = count($allData);

        // Manual slice for pagination
        $pageData = array_slice($allData, $start, $length);

        foreach ($pageData as $row) {
            $rows[] = [
                'no'        => $no++,
                'unit'      => esc($row->department_name),
                'tipe'      => $row->type_label,
                'indikator' => esc($row->indicator_element),
                'periode'   => $row->group_period,
                'days'      => $row->group_days,
                'aksi'      => '<button type="button" class="btn btn-sm btn-success btn-restore-trash me-1" title="Pulihkan" data-id="' . $row->group_id . '" data-type="' . $row->group_type_num . '"><i class="bi bi-arrow-counterclockwise"></i></button>'
                           . '<button type="button" class="btn btn-sm btn-danger btn-delete-trash" title="Hapus Permanen" data-id="' . $row->group_id . '" data-type="' . $row->group_type_num . '" data-name="' . esc($row->indicator_element) . '"><i class="bi bi-trash3"></i></button>',
            ];
        }

        return $this->response->setJSON([
            'draw'            => $draw,
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $rows,
        ]);
    }

    public function ajaxPermanentDelete()
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
            ->delete();

        return $this->response->setJSON(['status' => true, 'message' => 'Data berhasil dihapus permanen']);
    }

    public function ajaxRestoreGroup()
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
            ->update(['group_record_status' => 'A']);

        return $this->response->setJSON(['status' => true, 'message' => 'Indikator berhasil dipulihkan']);
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

    public function ajaxGetRequestStatus()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setStatusCode(401)->setJSON(['status' => false]);
        }

        $departmentId = $this->request->getPost('department_id');
        $groupType    = $this->request->getPost('group_type');
        $userId = (int) (session('profile_id') ?? 0);

        if (!$departmentId || !$userId) {
            return $this->response->setJSON(['status' => false, 'data' => []]);
        }

        $model = new \App\Models\ApprovalRequestModel();
        $data = $model->getUserRequests($userId, (string) $departmentId, 'open_period', $groupType);

        return $this->response->setJSON(['status' => true, 'data' => $data]);
    }

    public function ajaxRequestOpenPeriod()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setStatusCode(401)->setJSON(['status' => false, 'message' => 'Unauthorized']);
        }

        $indicatorId   = (int) $this->request->getPost('indicator_id');
        $departmentId  = $this->request->getPost('department_id');
        $periodStart   = $this->request->getPost('period_start');
        $periodEnd     = $this->request->getPost('period_end');
        $reason        = trim($this->request->getPost('reason') ?? '');
        $groupType     = $this->request->getPost('group_type');
        $userId        = (int) (session('profile_id') ?? 0);

        if (!$indicatorId || !$departmentId || !$periodStart || !$periodEnd || !$reason) {
            return $this->response->setJSON(['status' => false, 'message' => 'Data tidak lengkap']);
        }

        if ($periodStart > $periodEnd) {
            return $this->response->setJSON(['status' => false, 'message' => 'Tanggal mulai tidak boleh melebihi tanggal selesai']);
        }

        $approvalModel = new \App\Models\ApprovalRequestModel();
        $saved = $approvalModel->saveOpenPeriodRequest(
            $indicatorId,
            (string) $departmentId,
            $periodStart,
            $periodEnd,
            $reason,
            $userId,
            'open_period',
            $groupType
        );

        if ($saved) {
            $this->sendWaOpenPeriodNotification($indicatorId, $departmentId, $periodStart, $periodEnd, $reason, $groupType);
            return $this->response->setJSON(['status' => true, 'message' => 'Permohonan buka periode berhasil dikirim']);
        }

        return $this->response->setJSON(['status' => false, 'message' => 'Gagal mengirim permohonan']);
    }

    private function sendWaOpenPeriodNotification(int $indicatorId, string $departmentId, string $periodStart, string $periodEnd, string $reason, ?string $groupType): void
    {
        $db = db_connect();

        // Nama indikator
        if ($groupType === '1') {
            $ind = $db->table('quality_indicator')->select('indicator_element')->where('indicator_id', $indicatorId)->get()->getRow();
        } else {
            $ind = $db->table('local_quality_indicator')->select('indicator_element')->where('indicator_id', $indicatorId)->get()->getRow();
        }
        $indicatorName = $ind->indicator_element ?? '-';

        // Nama unit
        $dept = $db->table('master_institution_department')->select('department_name')->where('department_id', $departmentId)->get()->getRow();
        $departmentName = $dept->department_name ?? '-';

        // Nama type
        $typeNames = ['1' => 'INM', '5' => 'IMPRS', '6' => 'IMP Unit', '7' => 'IKP'];
        $typeName = $typeNames[$groupType] ?? 'INDIKATOR';

        $phone = '6285859410265';
        $token = 'EAAOPZAk50d4QBRWgRZBlswqPFxIjTIWToyWsrS5Hj0ZCw7fVjSydW3sRqiUM6dgZCITNOK3MK7bDdl7Qbmt9LBMcbnhwXrZC9xoiNcS8Y4tjbj1kB0VgwI8ZBBhITGyzAeuFy2EXXzIeM3z6VDsw9NZCXlZAvku93DZAS2jiVBZCTBSf3nZCoBxGZBP0x7DopUOsDgZD';
        $url = "https://graph.facebook.com/v19.0/1128976353628313/messages";

        $params = [
            ['type' => 'text', 'text' => $typeName],
            ['type' => 'text', 'text' => $departmentName],
            ['type' => 'text', 'text' => $indicatorName],
            ['type' => 'text', 'text' => $periodStart],
            ['type' => 'text', 'text' => $periodEnd],
            ['type' => 'text', 'text' => $reason],
        ];

        $data = [
            'messaging_product' => 'whatsapp',
            'to' => $phone,
            'type' => 'template',
            'template' => [
                'name' => 'to_admin_pengajuan',
                'language' => ['code' => 'id'],
                'components' => [
                    ['type' => 'body', 'parameters' => $params],
                ],
            ],
        ];

        $headers = [
            'Authorization: Bearer ' . $token,
            'Content-Type: application/json',
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        log_message('error', 'WA open_period: phone=' . $phone . ', group_type=' . ($groupType ?? '') . ', response=' . ($response ?: 'none') . ', error=' . ($err ?: 'none'));
    }
}
