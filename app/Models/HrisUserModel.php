<?php

namespace App\Models;

use CodeIgniter\Model;

class HrisUserModel extends Model
{
    protected $DBGroup    = 'db2';          // 🔥 database kedua
    protected $table      = 'tb_users';
    protected $primaryKey = 'id';            // sesuaikan jika beda
    protected $returnType = 'object';
    protected $allowedFields = ['nip', 'pass_login'];

    /**
     * SETARA check_login_hris CI3
     */
    public function checkLoginHris($nip, $password)
    {
        return $this->where([
            'nip'        => $nip,
            'pass_login' => md5($password) // ⚠️ sama persis CI3
        ])
            ->first(); // CI4: ambil 1 baris
    }
}
