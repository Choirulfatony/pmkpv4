<?php

namespace App\Controllers;

use App\Models\StaffModel;

class Staff extends AppController
{
    protected StaffModel $staffModel;

    public function __construct()
    {
        parent::__construct();
        $this->staffModel = new StaffModel();
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

        return $this->render('siimut/staff_list', [
            'judul'     => 'Daftar Staf',
            'icon'      => '<i class="bi bi-people"></i>',
            'total'     => $this->staffModel->getTotalStaff(),
            'active'    => $this->staffModel->getActiveStaff(),
        ]);
    }

    public function ajaxGetData()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        }

        $post = $this->request->getPost();
        $result = $this->staffModel->getDatatable($post);

        $rows = [];
        $no = ($post['start'] ?? 0) + 1;

        foreach ($result['data'] as $row) {
            $onlineBadge = $row->profile_online_status == 1
                ? '<span class="badge bg-success"><i class="bi bi-circle-fill"></i> Online</span>'
                : '<span class="badge bg-secondary">Offline</span>';

            $disableChecked = $row->profile_disable ? 'checked' : '';
            $toggleLabel = $row->profile_disable ? 'Nonaktif' : 'Aktif';
            $disableToggle = '<div class="form-check form-switch d-inline-block">'
                . '<input class="form-check-input btn-toggle-disable" type="checkbox" data-id="' . $row->profile_id . '" data-status="' . $row->profile_disable . '" ' . $disableChecked . '>'
                . '<label class="form-check-label small">' . $toggleLabel . '</label>'
                . '</div>';

            $lastLogin = $row->profile_last_login
                ? date('d M Y H:i', strtotime($row->profile_last_login))
                : '-';

            $actions = '<div class="btn-group btn-group-sm">';
            $actions .= '<a href="' . site_url('siimut/staf/edit/' . $row->profile_id) . '" class="btn btn-outline-primary" title="Edit"><i class="bi bi-pencil-square"></i></a>';
            $actions .= '<button type="button" class="btn btn-outline-danger btn-delete" data-id="' . $row->profile_id . '" data-name="' . esc($row->profile_fullname) . '" title="Hapus"><i class="bi bi-trash"></i></button>';
            $actions .= '</div>';

            $rows[] = [
                'no'         => $no++,
                'nama'       => esc($row->profile_fullname),
                'grup'       => esc($row->group_name ?? '-'),
                'email'      => esc($row->profile_email ?? '-'),
                'unit_kerja' => esc($row->department_name ?? '-'),
                'akun'       => $disableToggle,
                'online'     => $onlineBadge,
                'last_login' => $lastLogin,
                'actions'    => $actions,
            ];
        }

        return $this->response->setJSON([
            'draw'            => (int) ($post['draw'] ?? 1),
            'recordsTotal'    => $result['recordsTotal'],
            'recordsFiltered' => $result['recordsFiltered'],
            'data'            => $rows,
        ]);
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

        $staff = $this->staffModel->getStaffById($id);
        if (!$staff) {
            return redirect()->to('siimut/staf')->with('error', 'Staf tidak ditemukan');
        }

        return $this->render('siimut/staff_edit', [
            'judul'      => 'Edit Staf',
            'icon'       => '<i class="bi bi-pencil-square"></i>',
            'staff'      => $staff,
            'groups'     => $this->staffModel->getAllGroups(),
            'departments' => $this->staffModel->getAllDepartments(),
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

        return $this->render('siimut/staff_add', [
            'judul'      => 'Tambah Staf Baru',
            'icon'       => '<i class="bi bi-person-plus"></i>',
            'groups'     => $this->staffModel->getAllGroups(),
            'departments' => $this->staffModel->getAllDepartments(),
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

        $fullname     = trim($this->request->getPost('profile_fullname'));
        $email        = trim($this->request->getPost('profile_email'));
        $password     = $this->request->getPost('profile_password');
        $groupId      = $this->request->getPost('profile_group_id');
        $departmentId = $this->request->getPost('profile_department_id');
        $employeeId   = $this->request->getPost('profile_employee_id');
        $gender       = $this->request->getPost('profile_gender');
        $handphone1   = $this->request->getPost('profile_handphone1');
        $dob          = $this->request->getPost('profile_dob');

        if (!$fullname || !$email || !$password) {
            return $this->response->setJSON(['status' => false, 'message' => 'Nama, email, dan password wajib diisi']);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->response->setJSON(['status' => false, 'message' => 'Format email tidak valid']);
        }

        if (strlen($password) < 6) {
            return $this->response->setJSON(['status' => false, 'message' => 'Password minimal 6 karakter']);
        }

        if ($this->staffModel->emailExists($email)) {
            return $this->response->setJSON(['status' => false, 'message' => 'Email sudah terdaftar']);
        }

        $insertData = [
            'profile_fullname'      => $fullname,
            'profile_email'         => $email,
            'profile_password'      => md5($password),
            'profile_group_id'      => $groupId ?: null,
            'profile_department_id' => $departmentId ?: null,
            'profile_employee_id'   => $employeeId ?: null,
            'profile_gender'        => $gender ?: null,
            'profile_handphone1'    => $handphone1 ?: null,
            'profile_dob'           => $dob ?: null,
            'profile_record_status' => 'A',
            'profile_insert_by'     => session('profile_id'),
            'profile_insert_date'   => date('Y-m-d H:i:s'),
            'profile_online_status' => 0,
            'profile_disable'       => 0,
            'profile_is_verified'   => 1,
        ];

        $newId = $this->staffModel->createStaff($insertData);

        if ($newId) {
            return $this->response->setJSON(['status' => true, 'message' => 'Staf baru berhasil ditambahkan']);
        }

        return $this->response->setJSON(['status' => false, 'message' => 'Gagal menambahkan staf baru']);
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

        $fullname     = $this->request->getPost('profile_fullname');
        $email        = $this->request->getPost('profile_email');
        $groupId      = $this->request->getPost('profile_group_id');
        $departmentId = $this->request->getPost('profile_department_id');
        $employeeId   = $this->request->getPost('profile_employee_id');
        $gender       = $this->request->getPost('profile_gender');
        $handphone1   = $this->request->getPost('profile_handphone1');
        $dob          = $this->request->getPost('profile_dob');
        $note         = $this->request->getPost('profile_note');

        if (!$fullname || !$email) {
            return $this->response->setJSON(['status' => false, 'message' => 'Nama dan email wajib diisi']);
        }

        $updateData = [
            'profile_fullname'      => $fullname,
            'profile_email'         => $email,
            'profile_group_id'      => $groupId ?: null,
            'profile_department_id' => $departmentId ?: null,
            'profile_employee_id'   => $employeeId ?: null,
            'profile_gender'        => $gender ?: null,
            'profile_handphone1'    => $handphone1 ?: null,
            'profile_dob'           => $dob ?: null,
            'profile_note'          => $note ?: null,
            'profile_update_by'     => session('profile_id'),
            'profile_update_date'   => date('Y-m-d H:i:s'),
        ];

        $result = $this->staffModel->updateStaff($id, $updateData);

        if ($result) {
            return $this->response->setJSON(['status' => true, 'message' => 'Data staf berhasil diperbarui']);
        }

        return $this->response->setJSON(['status' => false, 'message' => 'Gagal memperbarui data staf']);
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

        $staff = $this->staffModel->getStaffById($id);
        if (!$staff) {
            return $this->response->setJSON(['status' => false, 'message' => 'Staf tidak ditemukan']);
        }

        $result = $this->staffModel->softDelete($id);

        if ($result) {
            return $this->response->setJSON(['status' => true, 'message' => 'Staf berhasil dihapus']);
        }

        return $this->response->setJSON(['status' => false, 'message' => 'Gagal menghapus staf']);
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

        $staff = $this->staffModel->getStaffById($id);
        if (!$staff) {
            return $this->response->setJSON(['status' => false, 'message' => 'Staf tidak ditemukan']);
        }

        $newStatus = $staff->profile_disable ? 0 : 1;
        $result = $this->staffModel->toggleDisable($id, $newStatus);

        if ($result) {
            $label = $newStatus ? 'dinonaktifkan' : 'diaktifkan';
            return $this->response->setJSON(['status' => true, 'message' => 'Akun berhasil ' . $label, 'new_status' => $newStatus]);
        }

        return $this->response->setJSON(['status' => false, 'message' => 'Gagal mengubah status akun']);
    }

    public function toggleOnline(int $id)
    {
        if (!session()->get('logged_in')) {
            return $this->response->setStatusCode(401)->setJSON(['status' => false, 'message' => 'Unauthorized']);
        }

        $role = session()->get('user_role');
        if (!in_array($role, ['ADMINISTRATOR'])) {
            return $this->response->setStatusCode(403)->setJSON(['status' => false, 'message' => 'Akses ditolak']);
        }

        $staff = $this->staffModel->getStaffById($id);
        if (!$staff) {
            return $this->response->setJSON(['status' => false, 'message' => 'Staf tidak ditemukan']);
        }

        $newStatus = $staff->profile_online_status ? 0 : 1;
        $db = db_connect();
        $result = $db->table('user_profile')
            ->where('profile_id', $id)
            ->update([
                'profile_online_status' => $newStatus,
                'profile_update_by'     => session('profile_id'),
                'profile_update_date'   => date('Y-m-d H:i:s'),
            ]);

        if ($result) {
            $label = $newStatus ? 'Online' : 'Offline';
            return $this->response->setJSON(['status' => true, 'message' => 'Status akun: ' . $label, 'new_status' => $newStatus]);
        }

        return $this->response->setJSON(['status' => false, 'message' => 'Gagal mengubah status online']);
    }
}
