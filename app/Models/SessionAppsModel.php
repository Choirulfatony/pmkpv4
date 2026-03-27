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
            ->select('
                user_profile.profile_id,
                user_profile.profile_fullname,
                user_group.group_id,
                user_group.group_name,
                user_profile.profile_institution_code,
                user_profile.profile_email,
                user_profile.profile_password,
                user_profile.mod_id,
                user_profile.mod_id_grop AS mod_id_gropx,
                master_institution_department.department_id,
                master_institution_department.department_record_status,
                master_institution_department.department_name,
                user_profile.profile_record_status')
            ->join(
                'master_institution_department',
                'user_profile.profile_department_id = master_institution_department.department_id',
                'inner'
            )
            ->join(
                'user_group',
                'user_profile.profile_group_id = user_group.group_id',
                'inner'
            )
            ->where($where)
            ->get()
            ->getRow();
    }
}
