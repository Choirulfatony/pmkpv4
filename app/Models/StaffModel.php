<?php

namespace App\Models;

use CodeIgniter\Model;

class StaffModel extends Model
{
    protected $table = 'user_profile';
    protected $primaryKey = 'profile_id';
    protected $returnType = 'object';

    protected $allowedFields = [
        'profile_group_id',
        'profile_subgroup_id',
        'profile_operator_id',
        'profile_fullname',
        'profile_employee_id',
        'profile_birth_place',
        'profile_dob',
        'profile_gender',
        'profile_religion',
        'profile_address',
        'profile_handphone1',
        'profile_handphone2',
        'profile_email',
        'profile_gmail',
        'profile_department_id',
        'profile_photo',
        'profile_disable',
        'profile_disable_reason',
        'profile_note',
        'profile_online_status',
        'profile_last_login',
        'profile_record_status',
        'profile_insert_by',
        'profile_insert_date',
        'profile_update_by',
        'profile_update_date',
        'profile_is_verified',
    ];

    public function getDatatable(array $post)
    {
        $db = db_connect();

        $columns = [
            0 => 'up.profile_fullname',
            1 => 'ug.group_name',
            2 => 'up.profile_email',
            3 => 'mid.department_name',
            4 => 'up.profile_disable',
            5 => 'up.profile_online_status',
            6 => 'up.profile_last_login',
        ];

        $searchValue = $post['search']['value'] ?? '';
        $orderColumn  = $columns[$post['order'][0]['column'] ?? 0] ?? 'up.profile_fullname';
        $orderDir     = $post['order'][0]['dir'] ?? 'ASC';
        $start        = (int) ($post['start'] ?? 0);
        $length       = (int) ($post['length'] ?? 10);

        $sql = "SELECT 
                    up.profile_id,
                    up.profile_fullname,
                    up.profile_email,
                    up.profile_employee_id,
                    up.profile_disable,
                    up.profile_online_status,
                    up.profile_last_login,
                    up.profile_record_status,
                    ug.group_name,
                    mid.department_name
                FROM user_profile up
                LEFT JOIN user_group ug ON ug.group_id = up.profile_group_id
                LEFT JOIN master_institution_department mid ON mid.department_id = up.profile_department_id
                WHERE up.profile_record_status = 'A'";

        $countSql = "SELECT COUNT(*) as total
                     FROM user_profile up
                     LEFT JOIN user_group ug ON ug.group_id = up.profile_group_id
                     LEFT JOIN master_institution_department mid ON mid.department_id = up.profile_department_id
                     WHERE up.profile_record_status = 'A'";

        if ($searchValue) {
            $searchCond = " AND (
                up.profile_fullname LIKE '%{$searchValue}%'
                OR up.profile_email LIKE '%{$searchValue}%'
                OR up.profile_employee_id LIKE '%{$searchValue}%'
                OR ug.group_name LIKE '%{$searchValue}%'
                OR mid.department_name LIKE '%{$searchValue}%'
            )";
            $sql .= $searchCond;
            $countSql .= $searchCond;
        }

        $totalResult = $db->query($countSql)->getRow()->total;

        $sql .= " ORDER BY {$orderColumn} {$orderDir}";
        $sql .= " LIMIT {$length} OFFSET {$start}";

        $data = $db->query($sql)->getResult();

        return [
            'draw'            => (int) ($post['draw'] ?? 1),
            'recordsTotal'    => (int) $totalResult,
            'recordsFiltered' => (int) $totalResult,
            'data'            => $data,
        ];
    }

    public function getStaffById(int $id)
    {
        $db = db_connect();
        return $db->table('user_profile up')
            ->select('
                up.*,
                ug.group_name,
                mid.department_name
            ')
            ->join('user_group ug', 'ug.group_id = up.profile_group_id', 'left')
            ->join('master_institution_department mid', 'mid.department_id = up.profile_department_id', 'left')
            ->where('up.profile_id', $id)
            ->where('up.profile_record_status', 'A')
            ->get()
            ->getRow();
    }

    public function getAllGroups()
    {
        $db = db_connect();
        return $db->table('user_group')
            ->where('group_record_status', 'A')
            ->orderBy('group_name', 'ASC')
            ->get()
            ->getResult();
    }

    public function getAllDepartments()
    {
        $db = db_connect();
        return $db->table('master_institution_department')
            ->orderBy('department_name', 'ASC')
            ->get()
            ->getResult();
    }

    public function updateStaff(int $id, array $data): bool
    {
        $db = db_connect();
        return $db->table('user_profile')
            ->where('profile_id', $id)
            ->update($data);
    }

    public function softDelete(int $id): bool
    {
        $db = db_connect();
        return $db->table('user_profile')
            ->where('profile_id', $id)
            ->update([
                'profile_record_status' => 'D',
                'profile_delete_date'   => date('Y-m-d H:i:s'),
                'profile_delete_by'     => session('profile_id'),
            ]);
    }

    public function toggleDisable(int $id, int $status): bool
    {
        $db = db_connect();
        return $db->table('user_profile')
            ->where('profile_id', $id)
            ->update([
                'profile_disable'   => $status,
                'profile_update_by' => session('profile_id'),
                'profile_update_date' => date('Y-m-d H:i:s'),
            ]);
    }

    public function getTotalStaff(): int
    {
        $db = db_connect();
        return (int) $db->table('user_profile')
            ->where('profile_record_status', 'A')
            ->countAllResults();
    }

    public function getActiveStaff(): int
    {
        $db = db_connect();
        return (int) $db->table('user_profile')
            ->where('profile_record_status', 'A')
            ->where('profile_online_status', 1)
            ->countAllResults();
    }
}
