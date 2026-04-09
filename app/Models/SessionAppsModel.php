<?php

// app/Models/SessionAppsModel.php
namespace App\Models;

use CodeIgniter\Model;

class SessionAppsModel extends Model
{
    protected $table = 'user_profile';
    protected $primaryKey = 'profile_id';
    protected $returnType = 'object';

    public function checkLogin(array $where)
    {
        return $this->db->table('user_profile')
            ->select('user_profile.*, user_group.group_name as hak_akses, master_institution_department.department_name as lokasi, user_profile.profile_photo as profile_photo')
            ->join('user_group', 'user_group.group_id = user_profile.profile_group_id', 'left')
            ->join('master_institution_department', 'master_institution_department.department_id = user_profile.profile_department_id', 'left')
            ->where($where)
            ->get()
            ->getRow();
    }
}
