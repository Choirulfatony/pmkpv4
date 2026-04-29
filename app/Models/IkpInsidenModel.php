<?php

namespace App\Models;

use CodeIgniter\Model;

class IkpInsidenModel extends Model
{
    protected $table            = 'ikprssm_insiden';
    protected $primaryKey       = 'id';

    protected $allowedFields = [
        // Data Petugas
        'user_id', 'nip', 'nama_petugas',
        // Pasien
        'asal_pasien', 'tgl_masuk', 'jam_masuk', 'kd_pasien', 'nama_pasien', 'kelompok_umur', 'nama_unit', 'umur_tahun', 'nama_kamar', 'kelamin', 'penjamin',
        // Data insiden
        'tgl_insiden', 'jam_insiden', 'insiden', 'kronologis_insiden', 'tempat_insiden', 'jenis_insiden',
        // Pelapor
        'pelapor_insiden', 'pelapor_lain_text', 'insiden_pada', 'insiden_pada_lain', 'spesialisasi_pasien', 'spesialisasi_lain',
        // Dampak
        'akibat_insiden',
        // Tindakan
        'tindakan_segera', 'tindakan_oleh', 'tindakan_tim', 'tindakan_petugas_lain', 'pernah_terjadi', 'tindakan_lanjutan',
        // Instalasi
        'instalasi_id', 'karu_id', 'current_receiver_id', 'current_receiver_role',
        'status_laporan', 'created_at', 'karu_read_at', 'komite_read_at', 'validated_at', 'grading_final', 'selesai_at', 'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /* =====================
     * GET DRAFT PAGINATED
     * ===================== */
    public function getDraftPaginated($user_id, $limit, $offset, $search = '', $filters = [])
    {
        $role = session('user_role');
        $db = $this->db;
        $builder = $db->table($this->table);

        $builder->select('
        id, nama_pasien, kd_pasien, jenis_insiden, insiden, kronologis_insiden, created_at
        ');

        if ($role == 'KARU') {
            $builder->whereIn('status_laporan', ['DRAFT']);
            $builder->where('karu_id', $user_id);
        } elseif ($role == 'PELAPOR') {
            $builder->where('user_id', $user_id);
            $builder->where('status_laporan', 'DRAFT');
        } else {
            $builder->where('user_id', $user_id);
            $builder->where('status_laporan', 'DRAFT');
        }

        if ($search !== '') {
            $builder->groupStart()
                ->like('nama_pasien', $search)
                ->orLike('kd_pasien', $search)
                ->orLike('jenis_insiden', $search)
                ->groupEnd();
        }

        $this->applyFilters($builder, $filters);

        return $builder
            ->orderBy('created_at', 'DESC')
            ->limit($limit, $offset)
            ->get()
            ->getResultArray();
    }

    /* =====================
     * APPLY FILTERS (Triwulan, Semester, Tahun)
     * ===================== */
    private function applyFilters($builder, $filters = [])
    {
        if (empty($filters)) {
            return;
        }

        $tahun = $filters['tahun'] ?? null;
        $semester = $filters['semester'] ?? null;
        $triwulan = $filters['triwulan'] ?? null;

        if ($tahun) {
            $builder->where("YEAR(created_at)", $tahun);
        }

        if ($semester) {
            $startMonth = $semester == 1 ? 1 : 7;
            $endMonth = $semester == 1 ? 6 : 12;
            $builder->where("MONTH(created_at) >=", $startMonth);
            $builder->where("MONTH(created_at) <=", $endMonth);
        }

        if ($triwulan) {
            $startMonth = ($triwulan - 1) * 3 + 1;
            $endMonth = $triwulan * 3;
            $builder->where("MONTH(created_at) >=", $startMonth);
            $builder->where("MONTH(created_at) <=", $endMonth);
        }
    }

    /* =====================
     * COUNT DRAFT FILTERED
     * ===================== */
    public function countDraftFiltered($user_id, $search = '', $filters = [])
    {
        $role = session('user_role');
        $builder = $this->db->table($this->table);

        if ($role == 'KARU') {
            $builder->whereIn('status_laporan', ['DRAFT']);
            $builder->where('karu_id', $user_id);
        } elseif ($role == 'PELAPOR') {
            $builder->where('user_id', $user_id);
            $builder->where('status_laporan', 'DRAFT');
        } else {
            $builder->where('user_id', $user_id);
            $builder->where('status_laporan', 'DRAFT');
        }

        if ($search !== '') {
            $builder->groupStart()
                ->like('nama_pasien', $search)
                ->orLike('kd_pasien', $search)
                ->orLike('jenis_insiden', $search)
                ->groupEnd();
        }

        $this->applyFilters($builder, $filters);

        return $builder->countAllResults();
    }

    public function countDraftByUser($user_id)
    {
        return $this->db->table($this->table)
            ->where('user_id', $user_id)
            ->where('status_laporan', 'DRAFT')
            ->countAllResults();
    }

    /* =====================
     * COUNT SEND BY USER
     * ===================== */
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

    /* =====================
     * COUNT SEND FILTERED
     * ===================== */
    public function countSendFiltered($user_id, $search = '', $filters = [])
    {
        $role = session('user_role');
        $builder = $this->db->table($this->table);

        if ($role == 'KOMITE') {
            $builder->where('komite_id', $user_id);
            $builder->whereIn('status_laporan', ['INSTALASI', 'SELESAI']);
        } elseif ($role == 'PELAPOR') {
            $builder->where('user_id', $user_id);
            $builder->whereIn('status_laporan', ['KARU', 'TERKIRIM', 'INSTALASI', 'SELESAI']);
        } elseif ($role == 'KARU') {
            $builder->where('karu_id', $user_id);
            $builder->whereIn('status_laporan', ['INSTALASI', 'SELESAI']);
        } else {
            $builder->where('user_id', $user_id);
            $builder->whereIn('status_laporan', ['TERKIRIM', 'INSTALASI', 'SELESAI']);
        }

        if ($search !== '') {
            $builder->groupStart()
                ->like('nama_pasien', $search)
                ->orLike('kd_pasien', $search)
                ->orLike('jenis_insiden', $search)
                ->groupEnd();
        }

        $this->applyFilters($builder, $filters);

        return $builder->countAllResults();
    }

    /* =====================
     * GET SEND PAGINATED
     * ===================== */
    public function getSendPaginated($user_id, $limit, $offset, $search = '', $filters = [])
    {
        $role = session('user_role');
        $builder = $this->db->table($this->table);

        $builder->select('
        id, nama_pasien, kd_pasien, jenis_insiden, insiden, kronologis_insiden, grading_risiko, created_at
        ');

        if ($role == 'KOMITE') {
            $builder->where('komite_id', $user_id);
            $builder->whereIn('status_laporan', ['INSTALASI', 'SELESAI']);
        } elseif ($role == 'PELAPOR') {
            $builder->where('user_id', $user_id);
            $builder->whereIn('status_laporan', ['KARU', 'TERKIRIM', 'INSTALASI', 'SELESAI']);
        } elseif ($role == 'KARU') {
            // KARU Sent: hanya yang sudah diproses KOMITE (INSTALASI/SELESAI)
            $builder->where('karu_id', $user_id);
            $builder->whereIn('status_laporan', ['INSTALASI', 'SELESAI']);
        } else {
            $builder->where('user_id', $user_id);
            $builder->whereIn('status_laporan', ['TERKIRIM', 'INSTALASI', 'SELESAI']);
        }

        if ($search !== '') {
            $builder->groupStart()
                ->like('nama_pasien', $search)
                ->orLike('kd_pasien', $search)
                ->orLike('jenis_insiden', $search)
                ->groupEnd();
        }

        $this->applyFilters($builder, $filters);

        return $builder
            ->orderBy('created_at', 'DESC')
            ->limit($limit, $offset)
            ->get()
            ->getResultArray();
    }

    /* =====================
     * COUNT INBOX BY USER
     * ===================== */
    public function countInboxByUser($user_id)
    {
        $role = session('user_role');

        if ($role == 'KARU') {
            return $this->db->table($this->table)
                ->where('karu_id', $user_id)
                ->whereIn('status_laporan', ['KARU', 'TERKIRIM', 'SELESAI'])
                ->countAllResults();
        } elseif ($role == 'KOMITE') {
            return $this->db->table($this->table)
                ->whereIn('status_laporan', ['DRAFT', 'KARU', 'TERKIRIM', 'INSTALASI', 'SELESAI'])
                ->countAllResults();
        } elseif ($role == 'PELAPOR') {
            return $this->db->table($this->table)
                ->where('user_id', $user_id)
                ->where('status_laporan', 'SELESAI')
                ->countAllResults();
        }
        return 0;
    }

    /* =====================
     * COUNT INBOX FILTERED
     * ===================== */
    public function countInboxFiltered($user_id, $keyword = '', $tab = 'inbox', $filters = [])
    {
        $role = session('user_role');

        if ($role == 'KARU') {
            // KARU Inbox: semua laporan untuk KARU ini
            return $this->db->table($this->table)
                ->where('karu_id', $user_id)
                ->countAllResults();
        }

        $builder = $this->db->table('ikprssm_insiden i');

        if ($role == 'KOMITE') {
            $builder->join('ikprssm_notifikasi n', 'n.insiden_id = i.id', 'left');
            $builder->where('n.hris_user_id', $user_id);
            $builder->whereIn('i.status_laporan', ['DRAFT', 'KARU', 'TERKIRIM', 'INSTALASI', 'SELESAI']);
        } elseif ($role == 'PELAPOR') {
            $builder->where('i.user_id', $user_id);
            $builder->where('i.status_laporan', 'SELESAI');
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

        $this->applyFilters($builder, $filters);

        return $builder->countAllResults();
    }

    /* =====================
     * GET INBOX PAGINATED
     * ===================== */
    public function getInboxPaginated($user_id, $limit, $offset, $keyword = '', $tab = 'inbox', $filters = [])
    {
        $role = session('user_role');

        if ($role == 'KARU') {
            // KARU Inbox: semua laporan untuk KARU ini
            $sql = "SELECT i.*, 
                            d.department_name as unit_insiden,
                            CASE WHEN i.karu_read_at IS NULL THEN 0 ELSE 1 END as is_read
                        FROM ikprssm_insiden i
                        LEFT JOIN master_institution_department d ON d.department_id = i.tempat_insiden
                        WHERE i.karu_id = ?
                        ORDER BY i.created_at DESC
                        LIMIT ? OFFSET ?";
            
            return $this->db->query($sql, [$user_id, (int)$limit, (int)$offset])->getResultArray();
        }

        $builder = $this->db->table('ikprssm_insiden i');

        if ($role == 'KOMITE') {
            $builder->select('i.*, d.department_name as unit_insiden, n.is_read');
            $builder->join('ikprssm_notifikasi n', 'n.insiden_id = i.id AND n.hris_user_id = ' . $this->db->escape($user_id), 'left');
        } else {
            $builder->select('i.*, d.department_name as unit_insiden, 0 as is_read');
        }

        $builder->join('master_institution_department d', 'd.department_id = i.tempat_insiden', 'left');

        if ($role == 'KOMITE') {
            $builder->where('n.hris_user_id', $user_id);
            $builder->whereIn('i.status_laporan', ['DRAFT', 'KARU', 'TERKIRIM', 'INSTALASI', 'SELESAI']);
        } elseif ($role == 'PELAPOR') {
            $builder->where('i.user_id', $user_id);
            $builder->where('i.status_laporan', 'SELESAI');
        } else {
            return [];
        }

        if ($keyword) {
            $builder->groupStart()
                ->like('i.nama_pasien', $keyword)
                ->orLike('i.jenis_insiden', $keyword)
                ->orLike('i.nama_unit', $keyword)
                ->groupEnd();
        }

        $this->applyFilters($builder, $filters);

        return $builder
            ->orderBy('i.created_at', 'DESC')
            ->limit($limit, $offset)
            ->get()
            ->getResultArray();
    }
}
