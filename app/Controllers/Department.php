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
                $url = site_url('siimut/data-indikator/' . $cfg['module'] . '?department_id=' . $row->department_id);
                $title = $cfg['label'] . ($active ? " ({$count} indikator)" : ' (belum ada data)');
                $badges .= '<a href="' . $url . '" class="btn btn-sm btn-indicator ' . $cls . '" '
                    . 'title="' . $title . '">'
                    . $cfg['label']
                    . '</a> ';
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
