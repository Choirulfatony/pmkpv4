<?php

namespace App\Controllers;

use App\Models\UserGroupDepartmentModel;
use App\Models\StaffModel;

class GroupAccess extends AppController
{
    public function index()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/auth');
        }

        $role = session()->get('user_role');
        if (!in_array($role, ['ADMINISTRATOR'])) {
            return redirect()->to('/siimut/dashboard')->with('error', 'Hanya untuk Administrator');
        }

        $staffModel = new StaffModel();
        $groups = $staffModel->getAllGroups();

        return $this->render('siimut/group_access_list', [
            'judul'  => 'Akses Departemen per Grup',
            'icon'   => '<i class="bi bi-diagram-3"></i>',
            'groups' => $groups,
        ]);
    }

    public function ajaxGetDepartments(int $groupId)
    {
        if (!session()->get('logged_in')) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        }

        $model = new UserGroupDepartmentModel();
        $departments = $model->getDepartmentsByGroup($groupId);

        return $this->response->setJSON([
            'status' => true,
            'data'   => $departments,
        ]);
    }

    public function edit(int $groupId)
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/auth');
        }

        $role = session()->get('user_role');
        if (!in_array($role, ['ADMINISTRATOR'])) {
            return redirect()->to('/siimut/dashboard')->with('error', 'Hanya untuk Administrator');
        }

        $staffModel = new StaffModel();
        $group = null;
        foreach ($staffModel->getAllGroups() as $g) {
            if ((int) $g->group_id === $groupId) {
                $group = $g;
                break;
            }
        }

        if (!$group) {
            return redirect()->to('siimut/group-access')->with('error', 'Grup tidak ditemukan');
        }

        $model = new UserGroupDepartmentModel();
        $assignedDeptIds = $model->getAssignedDepartmentIds($groupId);
        $departments = $staffModel->getAllDepartments();

        return $this->render('siimut/group_access_edit', [
            'judul'          => 'Atur Departemen - ' . esc($group->group_name),
            'icon'           => '<i class="bi bi-diagram-3"></i>',
            'group'          => $group,
            'departments'    => $departments,
            'assignedDeptIds' => $assignedDeptIds,
        ]);
    }

    public function update(int $groupId)
    {
        if (!session()->get('logged_in')) {
            return $this->response->setStatusCode(401)->setJSON(['status' => false, 'message' => 'Unauthorized']);
        }

        $role = session()->get('user_role');
        if (!in_array($role, ['ADMINISTRATOR'])) {
            return $this->response->setStatusCode(403)->setJSON(['status' => false, 'message' => 'Akses ditolak']);
        }

        $departmentIds = $this->request->getPost('department_ids') ?? [];

        $model = new UserGroupDepartmentModel();
        $model->saveDepartments($groupId, $departmentIds);

        return $this->response->setJSON([
            'status'  => true,
            'message' => 'Departemen berhasil diperbarui untuk grup ini',
        ]);
    }
}
