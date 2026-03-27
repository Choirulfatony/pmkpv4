<?php

namespace App\Models;

use CodeIgniter\Model;

class IkpNotifikasiModel extends Model
{
    protected $table         = 'ikprssm_notifikasi';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'hris_user_id',
        'insiden_id',
        'sender_id',
        'pesan',
        'status',
        'type',
        'is_read',
        'created_at'
    ];

    protected $useTimestamps = false;

    // public function countNotif($user_id)
    // {
    //     return $this->db->table($this->table)
    //         ->where('current_receiver_id', $user_id) // ✅ BENAR
    //         ->where('status_laporan', 'DRAFT')
    //         ->countAllResults();
    // }
}
