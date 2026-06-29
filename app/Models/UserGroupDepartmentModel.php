<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class UserGroupDepartmentModel extends Model
{
    protected $table = 'user_group_department';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'group_id',
        'department_id',
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getDepartmentsByGroup(int $groupId): array
    {
        $db = db_connect();
        return $db->table('user_group_department ugd')
            ->select('ugd.*, mid.department_name')
            ->join('master_institution_department mid', 'mid.department_id = ugd.department_id')
            ->where('ugd.group_id', $groupId)
            ->orderBy('mid.department_name', 'ASC')
            ->get()
            ->getResult();
    }

    public function getGroupIdsByDepartment(int $departmentId): array
    {
        $db = db_connect();
        $result = $db->table('user_group_department')
            ->select('group_id')
            ->where('department_id', $departmentId)
            ->get()
            ->getResult();
        return array_column($result, 'group_id');
    }

    public function getAssignedDepartmentIds(int $groupId): array
    {
        $db = db_connect();
        $result = $db->table('user_group_department')
            ->select('department_id')
            ->where('group_id', $groupId)
            ->get()
            ->getResult();
        return array_column($result, 'department_id');
    }

    public function saveDepartments(int $groupId, array $departmentIds): void
    {
        $db = db_connect();
        $db->table('user_group_department')
            ->where('group_id', $groupId)
            ->delete();

        $data = [];
        foreach ($departmentIds as $deptId) {
            if (!empty($deptId)) {
                $data[] = [
                    'group_id' => $groupId,
                    'department_id' => $deptId,
                ];
            }
        }

        if (!empty($data)) {
            $db->table('user_group_department')->insertBatch($data);
        }
    }

    public function getUserAccessibleDepartmentIds(?int $profileGroupId = null): array
    {
        $db = db_connect();
        $groupId = $profileGroupId ?? session('profile_group_id');

        if (!$groupId) {
            $profileId = session('profile_id');
            if ($profileId) {
                $profile = $db->table('user_profile')
                    ->select('profile_group_id')
                    ->where('profile_id', $profileId)
                    ->where('profile_record_status', 'A')
                    ->get()
                    ->getRow();
                $groupId = $profile->profile_group_id ?? null;
            }
        }

        if (!$groupId) {
            return [];
        }

        $result = $db->table('user_group_department')
            ->select('department_id')
            ->where('group_id', $groupId)
            ->get()
            ->getResult();

        return array_column($result, 'department_id');
    }
}
