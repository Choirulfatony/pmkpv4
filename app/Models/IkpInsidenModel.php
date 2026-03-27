<?php

namespace App\Models;

use CodeIgniter\Model;

class IkpInsidenModel extends Model
{
    protected $table            = 'ikprssm_insiden';
    protected $primaryKey       = 'id';

    protected $allowedFields = [
        // =====================
        // DATA PETUGAS
        // =====================
        'user_id',
        'nip',
        'nama_petugas',

        // =====================
        // Pasien
        // =====================
        'asal_pasien',
        'tgl_masuk',
        'jam_masuk',
        'kd_pasien',
        'nama_pasien',
        'kelompok_umur',
        'nama_unit',
        'umur_tahun',
        'nama_kamar',
        'kelamin',
        'penjamin',

        // =====================
        // Data insiden
        // =====================
        'tgl_insiden',
        'jam_insiden',
        'insiden',
        'kronologis_insiden',
        'tempat_insiden',
        'jenis_insiden',

        // =====================
        // PELAPOR & KORBAN
        // =====================
        'pelapor_insiden',
        'pelapor_lain_text',
        'insiden_pada',
        'insiden_pada_lain',
        'spesialisasi_pasien',
        'spesialisasi_lain',

        // =====================
        // DAMPAK 
        // =====================
        'akibat_insiden',

        // =====================
        // Tindakan
        // =====================
        'tindakan_segera',
        'tindakan_oleh',
        'tindakan_tim',
        'tindakan_petugas_lain',
        'pernah_terjadi',
        'tindakan_lanjutan',

        'instalasi_id',
        'karu_id',
        'current_receiver_id',
        'current_receiver_role',
        'status_laporan',
        'created_at',
        'karu_read_at',
        'komite_read_at',
        'validated_at',
        'grading_final',
        'selesai_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';


    /* =====================================================
     * GET DRAFT PAGINATED 
     * ===================================================== */
    public function getDraftPaginated($user_id, $limit, $offset, $search = '')
    {
        $builder = $this->builder();

        $builder->select(
            'id, nama_pasien, kd_pasien, jenis_insiden, insiden ,kronologis_insiden, created_at'
        )
            ->where('user_id', $user_id)
            ->where('status_laporan', 'DRAFT');

        if ($search !== '') {
            $builder->groupStart()
                ->like('nama_pasien', $search)
                ->orLike('kd_pasien', $search)
                ->orLike('jenis_insiden', $search)
                ->groupEnd();
        }

        return $builder
            ->orderBy('created_at', 'DESC')
            ->limit($limit, $offset)
            ->get()
            ->getResultArray();
    }

    /* =====================================================
     * COUNT DRAFT FILTERED
     * ===================================================== */
    public function countDraftFiltered($user_id, $search = '')
    {
        $builder = $this->builder();

        $builder
            ->where('user_id', $user_id)
            ->where('status_laporan', 'DRAFT');

        if ($search !== '') {
            $builder->groupStart()
                ->like('nama_pasien', $search)
                ->orLike('kd_pasien', $search)
                ->orLike('jenis_insiden', $search)
                ->groupEnd();
        }

        return $builder->countAllResults();
    }

    /**
     * Ambil draft laporan milik user (CI4 version of get_sent_draft)
     */
    public function countDraftByUser($user_id)
    {
        return $this->db->table($this->table)
            ->where('user_id', $user_id)
            ->where('status_laporan', 'DRAFT')
            ->countAllResults();
    }



    /* =====================================================
     * SEND
     * ===================================================== */

    // public function countSendByUser($user_id)
    // {
    //     return $this->db->table($this->table)
    //         ->where('user_id', $user_id)
    //         // ->where('status_laporan', 'TERKIRIM')
    //         ->whereIn('status_laporan', ['TERKIRIM', 'KARU', 'INSTALASI', 'SELESAI'])
    //         ->countAllResults();
    // }

    public function countSendByUser($user_id)
    {
        $builder = $this->db->table($this->table);

        $builder->groupStart()
            ->where('user_id', $user_id)
            ->orWhere('karu_id', $user_id)
            ->groupEnd()
            ->whereIn('status_laporan', ['TERKIRIM', 'INSTALASI', 'SELESAI']);

        return $builder->countAllResults();
    }


    /* =====================================================
     * COUNT SEND FILTERED
     * ===================================================== */
    public function countSendFiltered($user_id, $search = '')
    {
        $role = session('user_role');
        $builder = $this->db->table($this->table);

        if ($role == 'KOMITE') {
            // KOMITE: yang sudah diproses oleh komite
            $builder->where('komite_id', $user_id);
            $builder->whereIn('status_laporan', ['INSTALASI', 'SELESAI']);
        } else {
            $builder->groupStart()
                ->where('user_id', $user_id)
                ->orWhere('karu_id', $user_id)
                ->groupEnd()
                ->whereIn('status_laporan', ['TERKIRIM', 'INSTALASI', 'SELESAI']);
        }

        if ($search !== '') {
            $builder->groupStart()
                ->like('nama_pasien', $search)
                ->orLike('kd_pasien', $search)
                ->orLike('jenis_insiden', $search)
                ->groupEnd();
        }

        return $builder->countAllResults();
    }

    /* =====================================================
     *  * GET SEND PAGINATED
     * ===================================================== */
    public function getSendPaginated($user_id, $limit, $offset, $search = '')
    {
        $role = session('user_role');
        $builder = $this->db->table($this->table);

        $builder->select('
        id,
        nama_pasien,
        kd_pasien,
        jenis_insiden,
        kronologis_insiden,
        grading_risiko,
        created_at
     ');

        if ($role == 'KOMITE') {
            // KOMITE: yang sudah diproses oleh komite
            $builder->where('komite_id', $user_id);
            $builder->whereIn('status_laporan', ['INSTALASI', 'SELESAI']);
        } else {
            $builder->groupStart()
                ->where('user_id', $user_id)
                ->orWhere('karu_id', $user_id)
                ->groupEnd()
                ->whereIn('status_laporan', ['TERKIRIM', 'INSTALASI', 'SELESAI']);
        }

        if ($search !== '') {
            $builder->groupStart()
                ->like('nama_pasien', $search)
                ->orLike('kd_pasien', $search)
                ->orLike('jenis_insiden', $search)
                ->groupEnd();
        }

        return $builder
            ->orderBy('created_at', 'DESC')
            ->limit($limit, $offset)
            ->get()
            ->getResultArray();
    }


    /* =======================================================
    * COUNT INBOX
    * ===================================================== */


    // public function countInboxFiltered($user_id, $keyword = '')
    // {
    //     $role = session('user_role');

    //     $builder = $this->db->table('ikprssm_insiden i');

    //     if ($role == 'KARU') {

    //         $builder->groupStart()

    //             ->groupStart()
    //             ->where('i.current_receiver_id', $user_id)
    //             ->where('i.current_receiver_role', 'KARU')
    //             ->groupEnd()

    //             ->orGroupStart()
    //             ->where('i.karu_id', $user_id)
    //             ->where('i.status_laporan', 'INSTALASI')
    //             ->groupEnd()

    //             ->groupEnd();
    //     } elseif ($role == 'KOMITE') {

    //         $builder->join(
    //             'ikprssm_notifikasi n',
    //             'n.insiden_id = i.id',
    //             'inner'
    //         );

    //         $builder->where('n.hris_user_id', $user_id);
    //         $builder->where('i.status_laporan', 'INSTALASI');
    //     } else {

    //         return 0;
    //     }

    //     if ($keyword) {
    //         $builder->groupStart()
    //             ->like('i.nama_pasien', $keyword)
    //             ->orLike('i.jenis_insiden', $keyword)
    //             ->orLike('i.nama_unit', $keyword)
    //             ->groupEnd();
    //     }

    //     return $builder->countAllResults();
    // }

    public function countInboxFiltered($user_id, $keyword = '', $tab = 'inbox')
    {
        $role = session('user_role');

        $builder = $this->db->table('ikprssm_insiden i');

        // ======================
        // STATUS FILTER
        // ======================
        if ($tab == 'inbox') {
            // KARU & PELAPOR lihat semua status
            $status = ['DRAFT', 'KARU', 'INSTALASI', 'SELESAI'];
        } else {
            $status = ['SELESAI'];
        }

        if ($role == 'KARU') {
            
            // Pakai whereRaw untuk jelas
            $builder->whereIn('i.status_laporan', $status);
            $builder->where("(i.current_receiver_id = " . intval($user_id) . " OR i.karu_id = " . intval($user_id) . ")");
            
        } elseif ($role == 'KOMITE') {

            $builder->join('ikprssm_notifikasi n', 'n.insiden_id = i.id', 'left');
            $builder->where('n.hris_user_id', $user_id);
            $builder->whereIn('i.status_laporan', $status);
        } elseif ($role == 'PELAPOR') {

            // PELAPOR lihat semua status laporan miliknya
            $builder->where('i.user_id', $user_id);
            $builder->whereIn('i.status_laporan', $status);
        } else {
            return 0;
        }

        if ($keyword) {
            $builder->groupStart()
                ->like('i.nama_pasien', $keyword)
                ->orLike('i.jenis_insiden', $keyword)
                ->orLike('i.nama_unit', $keyword)
                ->groupEnd();
        }

        return $builder->countAllResults();
    }

    /* =======================================================
    * GET INBOX PAGINATED
    * ===================================================== */

    public function getInboxPaginated($user_id, $limit, $offset, $keyword = '', $tab = 'inbox')
    {
        $role = session('user_role');

        // DEBUG: if KARU, show more info
        if ($role == 'KARU') {
            // Langsung query untuk debug
            $check = $this->db->table('ikprssm_insiden')
                ->select('id, status_laporan, karu_id, current_receiver_id')
                ->where('status_laporan', 'DRAFT')
                ->get()
                ->getResultArray();
            
            log_message('error', 'DEBUG KARU - Semua DRAFT insiden: ' . json_encode($check));
            
            $check2 = $this->db->table('ikprssm_insiden')
                ->select('id, status_laporan, karu_id, current_receiver_id')
                ->where('karu_id', $user_id)
                ->get()
                ->getResultArray();
            
            log_message('error', 'DEBUG KARU - where karu_id=' . $user_id . ': ' . json_encode($check2));
        }

        $builder = $this->db->table('ikprssm_insiden i');

        $builder->select('
        i.*,
        d.department_name as unit_insiden,
        n.is_read
        ');

        $builder->join(
            'ikprssm_notifikasi n',
            'n.insiden_id = i.id AND n.hris_user_id = ' . $this->db->escape($user_id),
            'left'
        );

        $builder->join(
            'master_institution_department d',
            'd.department_id = i.tempat_insiden',
            'left'
        );

        $role = session('user_role');

        // DEBUG: log query conditions
        log_message('error', 'getInboxPaginated: user_id=' . $user_id . ', role=' . $role . ', tab=' . $tab);

        // ======================
        // STATUS FILTER
        // ======================
        if ($tab == 'inbox') {
            // KARU & PELAPOR lihat semua status
            $status = ['DRAFT', 'KARU', 'INSTALASI', 'SELESAI'];
        } else {
            $status = ['SELESAI'];
        }

        if ($role == 'KARU') {

            // Pakai whereRaw untuk jelas
            $builder->whereIn('i.status_laporan', $status);
            $builder->where("(i.current_receiver_id = " . intval($user_id) . " OR i.karu_id = " . intval($user_id) . ")");
            
        } elseif ($role == 'KOMITE') {

            $builder->where('n.hris_user_id', $user_id);

            $builder->whereIn('i.status_laporan', ['DRAFT', 'KARU', 'INSTALASI', 'SELESAI']);

        } elseif ($role == 'PELAPOR') {

            // PELAPOR lihat semua status laporan miliknya
            $builder->where('i.user_id', $user_id);
            $builder->whereIn('i.status_laporan', $status);
        } else {

            $builder->where('1=0');
        }

        if ($keyword) {
            $builder->groupStart()
                ->like('i.nama_pasien', $keyword)
                ->orLike('i.jenis_insiden', $keyword)
                ->orLike('d.department_name', $keyword)
                ->groupEnd();
        }

        return $builder
            ->groupBy('i.id')
            ->orderBy('i.created_at', 'DESC')
            ->limit($limit, $offset)
            ->get()
            ->getResultArray();
    }

    // public function getInboxPaginated($user_id, $limit, $offset, $keyword = '')
    // {
    //     $builder = $this->db->table('ikprssm_insiden i');

    //     $builder->select('
    //     i.*,
    //     d.department_name as unit_insiden,
    //     n.is_read
    //     ');

    //     $builder->join(
    //         'ikprssm_notifikasi n',
    //         'n.insiden_id = i.id AND n.hris_user_id = ' . $this->db->escape($user_id),
    //         'left'
    //     );

    //     $builder->join(
    //         'master_institution_department d',
    //         'd.department_id = i.tempat_insiden',
    //         'left'
    //     );

    //     $role = session('user_role');

    //     if ($role == 'KARU') {

    //         $builder->groupStart()

    //             ->groupStart()
    //             ->where('i.current_receiver_id', $user_id)
    //             ->where('i.current_receiver_role', 'KARU')
    //             ->groupEnd()

    //             ->orGroupStart()
    //             ->where('i.karu_id', $user_id)
    //             ->where('i.status_laporan', 'INSTALASI')
    //             ->groupEnd()

    //             ->groupEnd();
    //     } elseif ($role == 'KOMITE') {

    //         $builder->where('n.hris_user_id', $user_id);
    //         $builder->where('i.status_laporan', 'INSTALASI');
    //     } else {

    //         $builder->where('1=0');
    //     }

    //     if ($keyword) {
    //         $builder->groupStart()
    //             ->like('i.nama_pasien', $keyword)
    //             ->orLike('i.jenis_insiden', $keyword)
    //             ->orLike('d.department_name', $keyword)
    //             ->groupEnd();
    //     }

    //     return $builder
    //         ->groupBy('i.id')
    //         ->orderBy('i.created_at', 'DESC')
    //         ->limit($limit, $offset)
    //         ->get()
    //         ->getResultArray();
    // }

    /* =======================================================
    * COUNT INBOX BY USER
    //  * ===================================================== */


    // public function countInboxByUser($user_id)
    // {
    //     $role = session('user_role');

    //     $builder = $this->db->table('ikprssm_insiden i');

    //     if ($role == 'KARU') {

    //         $builder->groupStart()

    //             ->groupStart()
    //             ->where('i.current_receiver_id', $user_id)
    //             ->where('i.current_receiver_role', 'KARU')
    //             ->groupEnd()

    //             ->orGroupStart()
    //             ->where('i.karu_id', $user_id)
    //             ->where('i.status_laporan', 'INSTALASI')
    //             ->groupEnd()

    //             ->groupEnd();
    //     } elseif ($role == 'KOMITE') {

    //         $builder->join(
    //             'ikprssm_notifikasi n',
    //             'n.insiden_id = i.id',
    //             'inner'
    //         );

    //         $builder->where('n.hris_user_id', $user_id);
    //         $builder->where('i.status_laporan', 'INSTALASI');
    //     } else {

    //         return 0;
    //     }

    //     return $builder->countAllResults();
    // }

    // public function countInboxByUser($user_id, $tab = 'inbox')
    // {
    //     $role = session('user_role');

    //     $builder = $this->db->table('ikprssm_insiden i');

    //     if ($tab == 'inbox') {
    //         $status = ['DRAFT', 'KARU', 'INSTALASI'];
    //     } else {
    //         $status = ['SELESAI'];
    //     }

    //     if ($role == 'KARU') {

    //         $builder->whereIn('i.status_laporan', $status);

    //         $builder->groupStart()
    //             ->where('i.current_receiver_id', $user_id)
    //             ->orWhere('i.karu_id', $user_id)
    //             ->groupEnd();
    //     } elseif ($role == 'KOMITE') {

    //         $builder->join('ikprssm_notifikasi n', 'n.insiden_id = i.id', 'left');
    //         $builder->where('n.hris_user_id', $user_id);
    //         $builder->whereIn('i.status_laporan', $status);
    //     } elseif ($role == 'PELAPOR') {

    //         $builder->where('i.user_id', $user_id);
    //         $builder->whereIn('i.status_laporan', $status);
    //     } else {
    //         return 0;
    //     }

    //     return $builder->countAllResults();
    // }

    // public function countInboxByUser($user_id, $tab = 'inbox')
    // {
    //     $role = session('user_role');

    //     $builder = $this->db->table('ikprssm_insiden i');

    //     // ======================
    //     // STATUS
    //     // ======================
    //     if ($tab == 'inbox') {
    //         $status = ['DRAFT', 'KARU', 'INSTALASI'];
    //     } else {
    //         $status = ['SELESAI'];
    //     }

    //     // ======================
    //     // ROLE KARU
    //     // ======================
    //     if ($role == 'KARU') {

    //         $builder->whereIn('i.status_laporan', $status);

    //         $builder->groupStart()
    //             ->groupStart()
    //             ->where('i.current_receiver_id', $user_id)
    //             ->where('i.current_receiver_role', 'KARU')
    //             ->groupEnd()
    //             ->orGroupStart()
    //             ->where('i.karu_id', $user_id)
    //             ->groupEnd()
    //             ->groupEnd();
    //     }

    //     // ======================
    //     // ROLE KOMITE
    //     // ======================
    //     elseif ($role == 'KOMITE') {

    //         $builder->join('ikprssm_notifikasi n', 'n.insiden_id = i.id', 'left');

    //         $builder->groupStart()
    //             ->where('n.hris_user_id', $user_id)
    //             ->orWhere('i.komite_id', $user_id)
    //             ->groupEnd();

    //         $builder->whereIn('i.status_laporan', $status);
    //     }

    //     // ======================
    //     // ROLE PELAPOR (DEFAULT)
    //     // ======================
    //     else {

    //         $builder->where('i.user_id', $user_id);
    //         $builder->whereIn('i.status_laporan', $status);
    //     }

    //     return $builder->countAllResults();
    // }

    public function countInboxByUser($user_id)
    {
        $role = session('user_role');

        $builder = $this->db->table('ikprssm_insiden i');

        if ($role == 'KARU') {

            $builder->whereIn('i.status_laporan', ['DRAFT', 'KARU', 'INSTALASI', 'SELESAI']);

            $builder->groupStart()
                ->groupStart()
                ->where('i.current_receiver_id', $user_id)
                ->where('i.current_receiver_role', 'KARU')
                ->groupEnd()
                ->orGroupStart()
                ->where('i.karu_id', $user_id)
                ->groupEnd()
                ->groupEnd();
        } elseif ($role == 'KOMITE') {

            $builder->join('ikprssm_notifikasi n', 'n.insiden_id = i.id', 'left');

            $builder->groupStart()
                ->where('n.hris_user_id', $user_id)
                ->orWhere('i.komite_id', $user_id)
                ->groupEnd();

            $builder->whereIn('i.status_laporan', ['DRAFT', 'KARU', 'INSTALASI', 'SELESAI']);
        } else {

            // pelapor
            $builder->where('i.user_id', $user_id);
        }

        return $builder->countAllResults();
    }
}
