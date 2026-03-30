<?php

namespace App\Controllers;

use App\Models\MloadModuleIkp;
use App\Models\IkpInsidenModel;
use App\Models\IkpNotifikasiModel;


// use App\Models\HrisUserModel;

class Ikprs extends AppController
{
    protected $ikpModel;

    public function __construct()
    {
        parent::__construct();
        $this->ikpModel = new MloadModuleIkp();
    }

    // ================= HALAMAN UTAMA =================
    // public function index()
    // {
    //     $this->disableCache();

    //     return $this->render('dashboard/index', [
    //         'judul'    => 'IKPRS',
    //         'icon'     => '<i class="bi bi-clipboard-check"></i>',
    //         '_content' => view('ikprs/ikp_content'),
    //     ]);
    // }

    public function index()
    {
        $this->disableCache();

        $db = db_connect();

        $tahunIni = date('Y');
        
        $tahunMin = $db->table('ikprssm_insiden')
            ->select('MIN(YEAR(selesai_at)) as min_tahun')
            ->where('status_laporan', 'SELESAI')
            ->where('selesai_at IS NOT NULL')
            ->get()
            ->getRow();
        
        $tahunMulai = $tahunMin && $tahunMin->min_tahun ? (int) $tahunMin->min_tahun : ($tahunIni - 4);

        $labels = [];
        for ($t = $tahunMulai; $t <= $tahunIni; $t++) {
            $labels[] = (string) $t;
        }

        $jenisInsiden = ['KNC', 'KTD', 'KTC', 'KPC', 'Sentinel'];
        $datasets = [];

        foreach ($jenisInsiden as $jenis) {
            $dataPerTahun = [];
            for ($t = $tahunMulai; $t <= $tahunIni; $t++) {
                $count = $db->table('ikprssm_insiden')
                    ->where('jenis_insiden', $jenis)
                    ->where('status_laporan', 'SELESAI')
                    ->where("selesai_at >= '{$t}-01-01' AND selesai_at <= '{$t}-12-31'")
                    ->countAllResults();
                $dataPerTahun[] = $count;
            }
            $datasets[] = [
                'jenis' => $jenis,
                'data' => $dataPerTahun
            ];
        }

        $chartData = [
            'labels' => $labels,
            'datasets' => $datasets,
        ];

        return $this->render('dashboard/index', [
            'judul'    => 'Dashboard IKPRS',
            'icon'     => '<i class="bi bi-clipboard-check"></i>',
            'chartData' => $chartData,
            '_content'  => view('ikprs/dashboard', ['chartData' => $chartData]),
        ]);
    }


    public function ikprs()
    {
        $this->disableCache();

        return $this->render('dashboard/index', [
            'judul'    => 'IKPRS',
            'icon'     => '<i class="bi bi-clipboard-check"></i>',
            '_content' => view('ikprs/ikp_content'),
        ]);
    }

    // ================= CARI PASIEN =================
    public function cari_pasien()
    {
        $kd_pasien   = $this->request->getPost('kd_pasien');
        $tgl_masuk   = $this->request->getPost('tgl_masuk');
        $asal_pasien = $this->request->getPost('asal_pasien');

        if (!$kd_pasien || !$tgl_masuk || !$asal_pasien) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Parameter tidak lengkap'
            ]);
        }

        // 🔥 INI WAJIB
        $model = new MloadModuleIkp();

        $data = $model->cari_pasien($kd_pasien, $tgl_masuk, $asal_pasien);

        if ($data) {
            return $this->response->setJSON([
                'status' => 'success',
                'data'   => $data
            ]);
        }

        return $this->response->setJSON([
            'status'  => 'error',
            'message' => 'Data pasien tidak ditemukan'
        ]);
    }

    // form add ikp
    public function formAddIkp()
    {
        if (!session()->has('hris_nip') || !session()->has('hris_full_name')) {
            return $this->response
                ->setStatusCode(403)
                ->setBody('Unauthorized');
        }

        return view('ikprs/_form_add_ikp');
    }

    // inbox
    // public function formInbox()
    // {
    //     return view('ikprs/_form_inbox');
    // }

    // get departments untuk select2
    public function get_departments()
    {
        $keyword = $this->request->getGet('q');

        $model = new MloadModuleIkp();

        return $this->response->setJSON(
            $model->get_departments($keyword)
        );
    }

    //refreshBadgeCounter
    // public function counterAjax()
    // {
    //     helper('notifikasi');

    //     $user_id = session()->get('hris_user_id');
    //     $role    = session()->get('user_role');

    //     if (!$user_id) {
    //         return $this->response->setJSON([
    //             'total_notif' => 0,
    //             'total_inbox' => 0,
    //             'total_send'  => 0,
    //             'total_draft' => 0,
    //             'data'        => []
    //         ]);
    //     }

    //     $model = new IkpInsidenModel();
    //     $db    = db_connect();

    //     // jumlah notif belum dibaca
    //     $total_notif = $db->table('ikprssm_notifikasi')
    //         ->where('hris_user_id', $user_id)
    //         ->where('is_read', 0)
    //         ->countAllResults();


    //     // ambil data notif
    //     $rows = $db->table('ikprssm_notifikasi n')
    //         ->select('
    //     n.id as notif_id,
    //     n.insiden_id,
    //     n.pesan,
    //     n.is_read,
    //     n.created_at as notif_time,
    //     i.jenis_insiden,
    //     i.current_receiver_id,
    //     d.department_name as unit_ruangan
    //      ')
    //         ->join('ikprssm_insiden i', 'i.id=n.insiden_id', 'left')
    //         ->join('master_institution_department d', 'd.department_id=i.tempat_insiden', 'left')
    //         ->where('n.hris_user_id', $user_id)
    //         ->orderBy('n.created_at', 'DESC')
    //         ->limit(5)
    //         ->get()
    //         ->getResultArray();

    //     $data = [];

    // foreach ($rows as $row) {

    //     // status dibaca
    //     $status_read = ($row['is_read'] == 0) ? 'Baru' : 'Sudah dibaca';

    //     $data[] = [
    //         'notif_id' => $row['notif_id'],
    //         'id' => $row['insiden_id'],
    //         'jenis' => $row['jenis_insiden'],
    //         'unit' => $row['unit_ruangan'],
    //         'current_receiver_id' => $row['current_receiver_id'],
    //         'waktu_lalu' => waktu_lalu($row['notif_time']),
    //         'status_text' => $row['pesan'], // langsung dari database
    //         'status_read' => $status_read,
    //         'is_read' => $row['is_read']
    //     ];
    // }

    //     return $this->response->setJSON([
    //         'total_notif' => (int)$total_notif,
    //         'total_inbox' => (int)$model->countInboxByUser($user_id, $role),
    //         'total_send'  => (int)$model->countSendByUser($user_id),
    //         'total_draft' => (int)$model->countDraftByUser($user_id),
    //         'data'        => $data
    //     ]);
    // }

    // public function counterAjax()
    // {
    //     helper('notifikasi');

    //     $user_id = session('hris_user_id');
    //     $role    = session('user_role');

    //     $db = db_connect();

    //     $total_notif = $db->table('ikprssm_notifikasi')
    //         ->where('hris_user_id', $user_id)
    //         ->where('is_read', 0)
    //         ->countAllResults();

    //     if ($role == 'KARU') {

    //         $total_inbox = $db->table('ikprssm_insiden')
    //             ->groupStart()
    //             ->where('status_laporan', 'TERKIRIM')
    //             ->orWhere('penerima_laporan', session('hris_full_name'))
    //             ->groupEnd()
    //             ->countAllResults();

    //     } elseif ($role == 'KOMITE') {

    //         $total_inbox = $db->table('ikprssm_insiden')
    //             ->where('status_laporan', 'INSTALASI')
    //             ->countAllResults();

    //     } else {

    //         $total_inbox = 0;
    //     }

    //     $total_draft = $db->table('ikprssm_insiden')
    //         ->where('user_id', $user_id)
    //         ->where('status_laporan', 'DRAFT')
    //         ->countAllResults();


    //     if ($role == 'KARU') {

    //         $total_send = $db->table('ikprssm_insiden')
    //             ->where('karu_id', $user_id)
    //             ->whereIn('status_laporan', ['INSTALASI', 'SELESAI'])
    //             ->countAllResults();
    //     } elseif ($role == 'KOMITE') {

    //         $total_send = $db->table('ikprssm_insiden')
    //             ->where('komite_id', $user_id)
    //             ->whereIn('status_laporan', ['INSTALASI', 'SELESAI'])
    //             ->countAllResults();
    //     } else {

    //         $total_send = $db->table('ikprssm_insiden')
    //             ->where('user_id', $user_id)
    //             ->where('status_laporan !=', 'DRAFT')
    //             ->countAllResults();
    //     }

    //     $notif = $this->getNotifList($user_id);

    //     return $this->response->setJSON([
    //         'total_notif' => $total_notif,
    //         'total_inbox' => $total_inbox,
    //         'total_draft' => $total_draft,
    //         'total_send'  => $total_send,
    //         'data'        => $notif
    //     ]);
    // }

    // public function counterAjax()
    // {
    //     helper('notifikasi');

    //     $user_id = session('hris_user_id');
    //     $role    = session('user_role');

    //     $db = db_connect();

    //     $total_notif = $db->table('ikprssm_notifikasi')
    //         ->where('hris_user_id', $user_id)
    //         ->where('is_read', 0)
    //         ->countAllResults();

    //     // INBOX
    //     // INBOX
    //     if ($role == 'KARU') {

    //         $total_inbox = $db->table('ikprssm_insiden')
    //             ->groupStart()
    //             ->groupStart()
    //             ->where('current_receiver_id', $user_id)
    //             ->where('current_receiver_role', 'KARU')
    //             ->groupEnd()
    //             ->orGroupStart()
    //             ->where('karu_id', $user_id)
    //             ->where('status_laporan', 'INSTALASI')
    //             ->groupEnd()
    //             ->groupEnd()
    //             ->countAllResults();
    //     } elseif ($role == 'KOMITE') {

    //         $total_inbox = $db->table('ikprssm_notifikasi n')
    //             ->join('ikprssm_insiden i', 'i.id = n.insiden_id')
    //             ->where('n.hris_user_id', $user_id)
    //             ->where('i.status_laporan', 'INSTALASI')
    //             ->where('n.is_read', 0)
    //             ->countAllResults();
    //     } else {

    //         $total_inbox = 0;
    //     }

    //     // DRAFT
    //     $total_draft = $db->table('ikprssm_insiden')
    //         ->where('user_id', $user_id)
    //         ->where('status_laporan', 'DRAFT')
    //         ->countAllResults();

    //     // SEND
    //     if ($role == 'KARU') {

    //         $total_send = $db->table('ikprssm_insiden')
    //             ->where('karu_id', $user_id)
    //             ->whereIn('status_laporan', ['INSTALASI', 'SELESAI'])
    //             ->countAllResults();
    //     } elseif ($role == 'KOMITE') {

    //         $total_send = $db->table('ikprssm_insiden')
    //             ->where('komite_id', $user_id)
    //             ->whereIn('status_laporan', ['INSTALASI', 'SELESAI'])
    //             ->countAllResults();
    //     } else {

    //         $total_send = $db->table('ikprssm_insiden')
    //             ->where('user_id', $user_id)
    //             ->where('status_laporan !=', 'DRAFT')
    //             ->countAllResults();
    //     }

    //     $notif = $this->getNotifList($user_id);

    //     return $this->response->setJSON([
    //         'total_notif' => $total_notif,
    //         'total_inbox' => $total_inbox,
    //         'total_draft' => $total_draft,
    //         'total_send'  => $total_send,
    //         'data'        => $notif
    //     ]);
    // }

    // public function counterAjax()
    // {
    //     helper('notifikasi');

    //     $user_id = session('hris_user_id');
    //     // $role    = session('user_role');
    //     $role = session()->get('user_role');

    //     $db = db_connect();

    //     // NOTIFIKASI
    //     $total_notif = $db->table('ikprssm_notifikasi')
    //         ->where('hris_user_id', $user_id)
    //         ->where('is_read', 0)
    //         ->countAllResults();

    //     /*
    //     ==========================
    //     INBOX
    //     ==========================
    //     */

    //     if ($role == 'KARU') {

    //         $total_inbox = $db->table('ikprssm_insiden')
    //             ->groupStart()

    //             ->groupStart()
    //             ->where('current_receiver_id', $user_id)
    //             ->where('current_receiver_role', 'KARU')
    //             ->groupEnd()

    //             ->orGroupStart()
    //             ->where('karu_id', $user_id)
    //             ->where('status_laporan', 'INSTALASI')
    //             ->groupEnd()

    //             ->groupEnd()
    //             ->countAllResults();
    //     } elseif ($role == 'KOMITE') {

    //         $total_inbox = $db->table('ikprssm_notifikasi n')
    //             ->join('ikprssm_insiden i', 'i.id = n.insiden_id', 'inner')
    //             ->where('n.hris_user_id', $user_id)
    //             ->where('i.status_laporan', 'INSTALASI')
    //             ->countAllResults();
    //     } else {

    //         $total_inbox = 0;
    //     }

    //     /*
    //     ==========================
    //     DRAFT
    //     ==========================
    //     */

    //     $total_draft = $db->table('ikprssm_insiden')
    //         ->where('user_id', $user_id)
    //         ->where('status_laporan', 'DRAFT')
    //         ->countAllResults();

    //     /*
    //     ==========================
    //     SENT
    //     ==========================
    //     */

    //     if ($role == 'KARU') {

    //         $total_send = $db->table('ikprssm_insiden')
    //             ->where('karu_id', $user_id)
    //             ->whereIn('status_laporan', ['INSTALASI', 'SELESAI'])
    //             ->countAllResults();
    //     } elseif ($role == 'KOMITE') {

    //         $total_send = $db->table('ikprssm_insiden')
    //             ->where('komite_id', $user_id)
    //             ->whereIn('status_laporan', ['INSTALASI', 'SELESAI'])
    //             ->countAllResults();
    //     } else {

    //         $total_send = $db->table('ikprssm_insiden')
    //             ->where('user_id', $user_id)
    //             ->where('status_laporan !=', 'DRAFT')
    //             ->countAllResults();
    //     }

    //     $notif = $this->getNotifList($user_id);

    //     return $this->response->setJSON([
    //         'total_notif' => $total_notif,
    //         'total_inbox' => $total_inbox,
    //         'total_draft' => $total_draft,
    //         'total_send'  => $total_send,
    //         'data'        => $notif
    //     ]);
    // }

    public function counterAjax()
    {
        helper('notifikasi');

        $user_id = session()->get('hris_user_id');
        $role    = session()->get('user_role');

        if (!$user_id) {
            return $this->response->setJSON([
                'error' => 'User belum login'
            ]);
        }

        $db = db_connect();

        $typeFilter = [];

        if ($role == 'PELAPOR') {
            $typeFilter = ['type' => 'to_pelapor'];
        } elseif ($role == 'KARU') {
            $typeFilter = ['type' => 'to_karu'];
        } elseif ($role == 'KOMITE') {
            $typeFilter = ['type' => 'to_komite'];
        }

        // NOTIF
        $total_notif = $db->table('ikprssm_notifikasi')
            ->where('hris_user_id', $user_id)
            ->where('is_read', 0)
            ->where($typeFilter) // 🔥 INI
            ->countAllResults();

        // ==========================
        // INBOX
        // ==========================

        if ($role == 'KARU') {

            // Untuk KARU: hitung semua insiden yang pernah masuk ke KARU
            // termasuk yang sudah selesai
            $total_inbox = $db->table('ikprssm_insiden')
                ->select('id')
                ->whereIn('status_laporan', ['DRAFT', 'KARU', 'INSTALASI', 'SELESAI'])
                ->groupStart()
                ->where('karu_id', $user_id)
                ->orWhere('current_receiver_id', $user_id)
                ->groupEnd()
                ->groupBy('id')
                ->countAllResults();
        } elseif ($role == 'KOMITE') {

            $total_inbox = $db->table('ikprssm_insiden i')
                ->select('i.id')
                ->join('ikprssm_notifikasi n', 'n.insiden_id = i.id', 'left')
                ->groupStart()
                ->where('n.hris_user_id', $user_id)
                ->orWhere('i.komite_id', $user_id)
                ->groupEnd()
                ->whereIn('i.status_laporan', ['INSTALASI', 'SELESAI'])
                ->groupBy('i.id')
                ->countAllResults();
        } else {

            // PELAPOR - bisa lihat semua status laporan miliknya
            $total_inbox = $db->table('ikprssm_insiden')
                ->select('id')
                ->where('user_id', $user_id)
                ->whereIn('status_laporan', ['DRAFT', 'KARU', 'INSTALASI', 'SELESAI'])
                ->countAllResults();
        }

        // ==========================
        // DRAFT
        // ==========================

        $total_draft = $db->table('ikprssm_insiden')
            ->where('user_id', $user_id)
            ->where('status_laporan', 'DRAFT')
            ->countAllResults();

        // ==========================
        // SENT
        // ==========================

        if ($role == 'KARU') {

            $total_send = $db->table('ikprssm_insiden')
                ->where('karu_id', $user_id)
                ->whereIn('status_laporan', ['INSTALASI', 'SELESAI'])
                ->countAllResults();
        } elseif ($role == 'KOMITE') {

            // KOMITE: hitung yang sudah di proses oleh komite ini
            // bisa dari komite_id ATAU komite_opened_by
            $total_send = $db->table('ikprssm_insiden')
                ->where('komite_id', intval($user_id))
                ->whereIn('status_laporan', ['INSTALASI', 'SELESAI'])
                ->countAllResults();
        } else {

            $total_send = $db->table('ikprssm_insiden')
                ->where('user_id', $user_id)
                ->where('status_laporan !=', 'DRAFT')
                ->countAllResults();
        }

        $notif = $this->getNotifList($user_id, $role);

        return $this->response->setJSON([
            'total_notif' => $total_notif,
            'total_inbox' => $total_inbox,
            'total_draft' => $total_draft,
            'total_send'  => $total_send,
            'data'        => $notif
        ]);
    }

    // private function getNotifList($user_id)
    // {

    //     $db = db_connect();

    //     $rows = $db->table('ikprssm_notifikasi n')
    //         ->select('
    //     n.id as notif_id,
    //     n.insiden_id,
    //     n.pesan,
    //     n.is_read,
    //     n.created_at as notif_time,
    //     i.jenis_insiden,
    //     i.status_laporan,
    //     COALESCE(i.current_receiver_id,0) as current_receiver_id,
    //     d.department_name as unit_ruangan
    //      ')
    //         ->join('ikprssm_insiden i', 'i.id = n.insiden_id', 'left')
    //         ->join('master_institution_department d', 'd.department_id=i.tempat_insiden', 'left')
    //         ->where('n.hris_user_id', $user_id)
    //         ->orderBy('n.created_at', 'DESC')
    //         ->limit(10)
    //         ->get()
    //         ->getResultArray();

    //     $data = [];

    //     foreach ($rows as $row) {

    //         $status_read = ($row['is_read'] == 0) ? 'Baru' : 'Sudah dibaca';

    //         $data[] = [
    //             'notif_id' => $row['notif_id'],
    //             'insiden_id' => $row['insiden_id'],
    //             'jenis' => $row['jenis_insiden'],
    //             'unit' => $row['unit_ruangan'],
    //             'current_receiver_id' => $row['current_receiver_id'],
    //             'waktu_lalu' => waktu_lalu($row['notif_time']),

    //             // langsung dari database
    //             'status_text' => $row['pesan'],

    //             'status_read' => $status_read,
    //             'is_read' => $row['is_read']
    //         ];
    //     }

    //     return $data;
    // }


    //lama
    // private function getNotifList($user_id)
    // {

    //     $db = db_connect();

    //     $rows = $db->table('ikprssm_notifikasi n')
    //         ->select('
    //         n.id as notif_id,
    //         n.insiden_id,
    //         n.pesan,
    //         n.is_read,
    //         n.created_at as notif_time,
    //         i.jenis_insiden,
    //         i.status_laporan,
    //         COALESCE(i.current_receiver_id,0) as current_receiver_id,
    //         d.department_name as unit_ruangan
    //     ')
    //         ->join('ikprssm_insiden i', 'i.id = n.insiden_id', 'left')
    //         ->join('master_institution_department d', 'd.department_id=i.tempat_insiden', 'left')
    //         ->where('n.hris_user_id', $user_id)
    //         ->orderBy('n.created_at', 'DESC')
    //         ->limit(10)
    //         ->get()
    //         ->getResultArray();

    //     $data = [];

    //     foreach ($rows as $row) {

    //         $status_read = ($row['is_read'] == 0) ? 'Baru' : 'Sudah dibaca';

    //         // $data[] = [
    //         //     'notif_id' => $row['notif_id'],
    //         //     'insiden_id' => $row['insiden_id'],
    //         //     'jenis' => $row['jenis_insiden'],
    //         //     'unit' => $row['unit_ruangan'],
    //         //     'current_receiver_id' => $row['current_receiver_id'],
    //         //     'waktu_lalu' => waktu_lalu($row['notif_time']),
    //         //     'status_text' => $row['pesan'],
    //         //     'status_read' => $status_read,
    //         //     'is_read' => $row['is_read']
    //         // ];
    //         $data[] = [
    //             'notif_id' => $row['notif_id'],
    //             'insiden_id' => $row['insiden_id'],
    //             'jenis' => $row['jenis_insiden'],
    //             'unit' => $row['unit_ruangan'],
    //             'current_receiver_id' => $row['current_receiver_id'],
    //             'status_laporan' => $row['status_laporan'], // TAMBAHKAN
    //             'waktu_lalu' => waktu_lalu($row['notif_time']),
    //             'status_text' => $row['pesan'],
    //             'status_read' => $status_read,
    //             'is_read' => $row['is_read']
    //         ];
    //     }

    //     return $data;
    // }

    //20.03.2026
    private function getNotifList($user_id, $role)
    {
        $db = db_connect();

        $typeFilter = "";

        if ($role == 'PELAPOR') {
            $typeFilter = "AND n.type = 'to_pelapor'";
        } elseif ($role == 'KARU') {
            $typeFilter = "AND n.type = 'to_karu'";
        } elseif ($role == 'KOMITE') {
            $typeFilter = "AND n.type = 'to_komite'";
        }

        // Debug: log jika role kosong
        if (empty($role)) {
            log_message('error', 'getNotifList called with empty role, user_id: ' . $user_id);
        }

        $query = "
            SELECT 
                n.id as notif_id,
                n.insiden_id,
                n.pesan,
                n.is_read,
                n.created_at as notif_time,
                n.hris_user_id as receiver_id,
                i.jenis_insiden,
                i.status_laporan,
                i.karu_read_at,
                i.komite_read_at,
                COALESCE(i.current_receiver_id,0) as current_receiver_id,
                d.department_name as unit_ruangan
            FROM ikprssm_notifikasi n
            LEFT JOIN ikprssm_insiden i ON i.id = n.insiden_id
            LEFT JOIN master_institution_department d ON d.department_id = i.tempat_insiden
            WHERE n.hris_user_id = ?";
        
        // Hanya tambahkan typeFilter jika tidak kosong
        if (!empty($typeFilter)) {
            $query .= " " . $typeFilter;
        }
        
        $query .= " ORDER BY n.id DESC";

        $rows = $db->query($query, [$user_id])->getResultArray();

        // Hapus filter unique - tampilkan SEMUA notifikasi
        $data = [];

        foreach ($rows as $row) {

            $status_read = ($row['is_read'] == 0) ? 'Baru' : 'Sudah dibaca';

            $data[] = [
                'notif_id' => $row['notif_id'],
                'insiden_id' => $row['insiden_id'],
                'jenis' => $row['jenis_insiden'] ?? '-',
                'unit' => $row['unit_ruangan'] ?? '-',
                'receiver_id' => $row['receiver_id'],
                'current_receiver_id' => $row['current_receiver_id'],
                'status_laporan' => $row['status_laporan'],
                'waktu_lalu' => waktu_lalu($row['notif_time']),
                'status_text' => $row['pesan'],
                'status_read' => $status_read,
                'karu_read_at'   => $row['karu_read_at'],
                'komite_read_at' => $row['komite_read_at'],
                'is_read' => $row['is_read']
            ];

            if (count($data) >= 20) break;
        }

        return $data;
    }

    //simpan ikp
    public function simpanikp()
    {
        $ikpModel   = new IkpInsidenModel();
        $notifModel = new IkpNotifikasiModel();
        $simrs      = new MloadModuleIkp();
        $db         = db_connect();

        // 1️⃣ VALIDASI INPUT UTAMA
        $tempat_insiden = $this->request->getPost('tempat_insiden');

        if (!$tempat_insiden) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Tempat insiden wajib dipilih'
            ]);
        }

        // 2️⃣ AMBIL DATA PASIEN
        $pasien = $simrs->cari_pasien(
            $this->request->getPost('kd_pasien'),
            $this->request->getPost('tgl_masuk'),
            $this->request->getPost('asal_pasien')
        );

        if (!$pasien) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Data pasien tidak ditemukan'
            ]);
        }

        // 3️⃣ AMBIL KARU DULU (WAJIB SEBELUM INSERT)
        $karu = $db->table('unit_karu')
            ->where('department_id', $tempat_insiden)
            ->where('aktif', 1)
            ->get()
            ->getRow();

        if (!$karu) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'KARU tidak ditemukan untuk unit ini'
            ]);
        }

        // 4️⃣ INSERT KE ikprssm_insiden
        $dataInsiden = [
            'user_id'        => session('hris_user_id'),
            'nip'            => session('hris_nip'),
            'nama_petugas'   => session('hris_full_name'),

            'asal_pasien'    => $this->request->getPost('asal_pasien'),
            'tgl_masuk'      => $this->request->getPost('tgl_masuk'),
            'jam_masuk'      => $pasien->jam_masuk,
            'kd_pasien'      => $pasien->kd_pasien,
            'nama_pasien'    => $pasien->nama,
            'umur_tahun'     => $pasien->umur_tahun,
            'kelompok_umur'  => $pasien->kelompok_umur,
            'nama_unit'      => $pasien->nama_unit,
            'nama_kamar'     => $pasien->nama_kamar,
            'kelamin'        => $pasien->kelamin,
            'penjamin'       => $pasien->penjamin,

            'insiden'        => $this->request->getPost('insiden'),
            'kronologis_insiden' => $this->request->getPost('kronologis_insiden'),
            'tempat_insiden' => $tempat_insiden,

            'jenis_insiden'  => $this->request->getPost('jenis_insiden'),
            'pelapor_insiden' => $this->request->getPost('pelapor_insiden'),
            'pelapor_lain_text' => $this->request->getPost('pelapor_lain_text'),
            'insiden_pada' => $this->request->getPost('insiden_pada'),
            'insiden_pada_lain' => $this->request->getPost('insiden_pada_lain'),
            'spesialisasi_pasien' => $this->request->getPost('spesialisasi_pasien'),
            'spesialisasi_lain' => $this->request->getPost('spesialisasi_lain'),
            'akibat_insiden' => $this->request->getPost('akibat_insiden'),

            'tindakan_segera' => $this->request->getPost('tindakan_segera'),
            'tindakan_oleh' => $this->request->getPost('tindakan_oleh'),
            'tindakan_tim' => $this->request->getPost('tindakan_tim'),
            'tindakan_petugas_lain' => $this->request->getPost('tindakan_petugas_lain'),
            'pernah_terjadi' => $this->request->getPost('pernah_terjadi'),
            'tindakan_lanjutan' => $this->request->getPost('tindakan_lanjutan'),

            'tgl_insiden'    => $this->request->getPost('tgl_insiden'),
            'jam_insiden'    => $this->request->getPost('jam_insiden'),

            // 🔥 PENTING SESUAI KONSEP BARU
            'status_laporan'      => 'DRAFT', // Draft di KARU
            'karu_id'               => $karu->hris_user_id, // simpan karu penanggung jawab
            'current_receiver_id' => $karu->hris_user_id,
            'current_receiver_role' => 'KARU'
        ];

        $ikpModel->insert($dataInsiden);
        $insiden_id = $ikpModel->getInsertID();

        if (!$insiden_id) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Gagal menyimpan data IKP'
            ]);
        }

        // 5️⃣ INSERT NOTIF KE KARU
        $pelapor_id = session('hris_user_id');


        // notif ke KARU
        $notifModel->insert([
            'sender_id'    => $pelapor_id,
            'hris_user_id' => $karu->hris_user_id,
            'insiden_id'   => $insiden_id,
            'pesan'        => 'Ada laporan IKP baru',
            'status'       => 'NEW',
            'type'          => 'to_karu',
            'is_read'      => 0,
            'created_at'   => date('Y-m-d H:i:s')
        ]);


        // notif ke pelapor (jika bukan KARU)
        if ($pelapor_id != $karu->hris_user_id) {

            $notifModel->insert([
                'sender_id'    => $pelapor_id,
                'hris_user_id' => $pelapor_id,
                'insiden_id'   => $insiden_id,
                'pesan'        => 'Laporan sedang diverifikasi KARU',
                'status'       => 'INFO',
                'type' => 'to_pelapor',
                'is_read'      => 0,
                'created_at'   => date('Y-m-d H:i:s')
            ]);
        }

        return $this->response->setJSON([
            'status'  => true,
            'message' => 'Data IKP berhasil disimpan dan masuk ke Inbox KARU'
        ]);
    }

    //draft ikp
    public function formDrafts()
    {
        $request = service('request');

        $user_id = session()->get('hris_user_id');
        if (!$user_id) {
            return 'SESSION USER BELUM ADA';
        }

        // 🔑 FIX UTAMA DI SINI
        $page = (int) (
            $request->getPost('page')
            ?? $request->getGet('page')
            ?? 1
        );

        $keyword = trim($request->getGet('keyword') ?? '');
        $limit   = 10;

        $filters = [
            'tahun'     => $request->getGet('tahun') ?? null,
            'semester'  => $request->getGet('semester') ?? null,
            'triwulan'  => $request->getGet('triwulan') ?? null,
            'status'    => $request->getGet('status') ?? null
        ];

        $model = new IkpInsidenModel();

        // 🔹 Hitung total
        $total = $model->countDraftFiltered($user_id, $keyword, $filters);

        // 🔹 Hitung total halaman TANPA dipaksa minimal 1
        $total_pages = $total > 0 ? (int) ceil($total / $limit) : 0;

        // 🔹 Jika kosong
        if ($total_pages === 0) {
            $page   = 0;
            $offset = 0;
        } else {
            $page   = max(1, min($page, $total_pages));
            $offset = ($page - 1) * $limit;
        }

        $data = [
            'list'        => $model->getDraftPaginated($user_id, $limit, $offset, $keyword, $filters),
            'total'       => $total,
            'total_pages' => $total_pages,
            'page'        => $page,
            'keyword'     => $keyword,
            'filters'    => $filters
        ];

        if ($request->isAJAX()) {
            return view('ikprs/_form_drafts', $data);
        }

        return view('ikprs/_form_drafts', $data);
    }

    //send ikp
    public function formSend()
    {
        $request = service('request');

        $user_id = session()->get('hris_user_id');
        if (!$user_id) {
            return 'SESSION USER BELUM ADA';
        }

        // 🔑 FIX UTAMA DI SINI
        $page = (int) (
            $request->getPost('page')
            ?? $request->getGet('page')
            ?? 1
        );

        $keyword = trim($request->getGet('keyword') ?? '');
        $limit   = 10;

        $filters = [
            'tahun'     => $request->getGet('tahun') ?? null,
            'semester'  => $request->getGet('semester') ?? null,
            'triwulan'  => $request->getGet('triwulan') ?? null,
            'status'    => $request->getGet('status') ?? null
        ];

        $model = new IkpInsidenModel();


        // 🔹 Hitung total
        $total = $model->countSendFiltered($user_id, $keyword, $filters);

        // 🔹 Hitung total halaman TANPA dipaksa minimal 1
        $total_pages = $total > 0 ? (int) ceil($total / $limit) : 0;

        // 🔹 Jika kosong
        if ($total_pages === 0) {
            $page   = 0;
            $offset = 0;
        } else {
            $page   = max(1, min($page, $total_pages));
            $offset = ($page - 1) * $limit;
        }

        $data = [
            'list'        => $model->getSendPaginated($user_id, $limit, $offset, $keyword, $filters),
            'total'       => $total,
            'total_pages' => $total_pages,
            'page'        => $page,
            'keyword'     => $keyword,
            'filters'    => $filters
        ];

        if ($request->isAJAX()) {
            return view('ikprs/_form_send', $data);
        }

        return view('ikprs/_form_send', $data);
    }

    // INBOX IKP
    public function formInbox_karu()
    {
        $request = service('request');

        $user_id = session()->get('hris_user_id');
        $role    = session()->get('user_role');
        // dd([
        // 'user_id' => session('hris_user_id'),
        // 'role'    => session('user_role')
        // ]);
        if (!$user_id) {
            return 'SESSION USER BELUM ADA';
        }

        $page = (int) (
            $request->getPost('page')
            ?? $request->getGet('page')
            ?? 1
        );

        $keyword = trim($request->getGet('keyword') ?? '');
        $limit   = 10;

        $filters = [
            'tahun'     => $request->getGet('tahun') ?? null,
            'semester'  => $request->getGet('semester') ?? null,
            'triwulan'  => $request->getGet('triwulan') ?? null,
            'status'    => $request->getGet('status') ?? null
        ];

        $model = new IkpInsidenModel();

        // 🔹 HITUNG TOTAL BERDASARKAN ROLE
        $total = $model->countInboxFiltered($user_id, $keyword, 'inbox', $filters);

        $total_pages = $total > 0 ? (int) ceil($total / $limit) : 0;

        if ($total_pages === 0) {
            $page   = 0;
            $offset = 0;
        } else {
            $page   = max(1, min($page, $total_pages));
            $offset = ($page - 1) * $limit;
        }

        $data = [
            'inbox'       => $model->getInboxPaginated($user_id, $limit, $offset, $keyword, 'inbox', $filters),
            'total'       => $total,
            'total_pages' => $total_pages,
            'page'        => $page,
            'keyword'     => $keyword,
            'filters'    => $filters
        ];

        return view('ikprs/_form_inbox_karu', $data);
    }

    // Verifikasi Karu
    public function verifikasi_karu()
    {
        $db = db_connect();
        $notifModel = new IkpNotifikasiModel();

        $insiden_id = $this->request->getPost('insiden_id');
        $grading    = $this->request->getPost('grading');
        $catatan    = $this->request->getPost('catatan_karu');

        // ======================
        // VALIDASI SERVER
        // ======================
        if (!$insiden_id) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'ID insiden tidak ditemukan'
            ]);
        }

        if (!$grading) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Grading risiko wajib dipilih'
            ]);
        }

        if (!$catatan || trim($catatan) == '') {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Catatan KARU tidak boleh kosong'
            ]);
        }

        $insiden = $db->table('ikprssm_insiden i')
            ->select('i.*, d.department_name')
            ->join('master_institution_department d', 'd.department_id = i.tempat_insiden', 'left')
            ->where('i.id', $insiden_id)
            ->get()
            ->getRow();

        if (!$insiden) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Data insiden tidak ditemukan'
            ]);
        }

        if (
            $insiden->current_receiver_id != session('hris_user_id')
            || $insiden->current_receiver_role != 'KARU'
        ) {

            return $this->response->setJSON([
                'status' => false,
                'message' => 'Anda tidak berhak memverifikasi laporan ini'
            ]);
        }

        if ($insiden->status_laporan != 'DRAFT') {

            return $this->response->setJSON([
                'status' => false,
                'message' => 'Laporan sudah diverifikasi sebelumnya'
            ]);
        }


        // ambil komite
        $komite = $db->table('unit_karu')
            ->where('role_id', 2)
            ->where('aktif', 1)
            ->orderBy('RAND()')
            ->get()
            ->getResult();

        if (!$komite) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'User komite tidak ditemukan'
            ]);
        }

        $db->transStart();

        $db->table('ikprssm_insiden')
            ->where('id', $insiden_id)
            ->update([
                'grading_risiko'   => $grading,
                'catatan_atasan'   => $catatan,
                'penerima_laporan' => session('hris_full_name'),
                'karu_id'          => session('hris_user_id'),
                'tgl_terima'       => date('Y-m-d'),
                'status_laporan'   => 'INSTALASI',
                // 🔥 TAMBAHAN WAJIB
                'karu_read_at'     => date('Y-m-d H:i:s'),
                'current_receiver_role' => 'KOMITE',
                'current_receiver_id'   => NULL, // belum ada komite yang menangani
                'updated_at'       => date('Y-m-d H:i:s')
            ]);
        /*
        ==========================
        NOTIF KE KOMITE
        ==========================
        */

        $dataNotif = [];

        foreach ($komite as $k) {

            $dataNotif[] = [
                'sender_id'    => session('hris_user_id'),
                'hris_user_id' => $k->hris_user_id,
                'insiden_id'   => $insiden_id,
                'pesan'        => $insiden->jenis_insiden . ' dari ' . $insiden->department_name . ' menunggu analisa PMKP',
                'status'       => 'NEW',
                'type'         => 'to_komite',
                'is_read'      => 0,
                'created_at'   => date('Y-m-d H:i:s')
            ];
        }

        if (!empty($dataNotif)) {
            $notifModel->insertBatch($dataNotif);
        }

        /*
        ==========================
        NOTIF KE KARU (STATUS PROSES)
        ==========================
        */

        $notifModel->insert([
            'sender_id'    => session('hris_user_id'),
            'hris_user_id' => session('hris_user_id'),
            'insiden_id'   => $insiden_id,
            'pesan'        => 'Laporan telah dikirim ke Komite untuk analisa',
            'status'       => 'INFO',
            'type'         => 'to_karu', // 🔥 TAMBAHAN
            'is_read'      => 0, // 🔥 Baru verifikasi = belum dibaca
            'created_at'   => date('Y-m-d H:i:s')
        ]);

        /*
        ==========================
        NOTIF KE PELAPOR
        ==========================
        */

        // Update notifikasi yang ada dengan status terbaru
        if ($insiden->user_id != session('hris_user_id')) {
            
            // Update SEMUA notifikasi pelapor untuk insiden ini jadi read
            $db->table('ikprssm_notifikasi')
                ->where('insiden_id', $insiden_id)
                ->where('hris_user_id', $insiden->user_id)
                ->update([
                    'is_read' => 1
                ]);

            // Insert notifikasi baru dengan status terbaru
            $notifModel->insert([
                'sender_id'    => session('hris_user_id'),
                'hris_user_id' => $insiden->user_id,
                'insiden_id'   => $insiden_id,
                'pesan'        => 'Laporan Anda telah diverifikasi oleh KARU',
                'status'       => 'INFO',
                'type'         => 'to_pelapor',
                'is_read'      => 0,
                'created_at'   => date('Y-m-d H:i:s')
            ]);
        }


        $db->transComplete();

        if ($db->transStatus() === FALSE) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Gagal memproses verifikasi'
            ]);
        }


        return $this->response->setJSON([
            'status' => true,
            'message' => 'Verifikasi KARU berhasil',
            'insiden_id' => $insiden_id
        ]);
    }

    //detailInboxKaru
    public function detailInboxKaru($id)
    {
        $db = db_connect();
        $user_id = session()->get('hris_user_id');

        $insiden = $db->table('ikprssm_insiden')
            ->where('id', $id)
            ->get()
            ->getRowArray();

        if (!$insiden) {
            return "<div class='p-3 text-danger'>Data tidak ditemukan</div>";
        }

        // ubah status dari DRAFT menjadi INBOX jika dibuka KARU
        if ($insiden['status_laporan'] == 'DRAFT') {
            $insiden['status_laporan'] = 'INBOX'; // hanya tampilan
        }

        /* NEXT (id lebih kecil karena inbox DESC) */
        $next = $db->table('ikprssm_insiden')
            ->select('id')
            ->where('current_receiver_id', $user_id)
            ->where('status_laporan', 'DRAFT')
            ->where('id <', $id)
            ->orderBy('id', 'DESC')
            ->get(1)
            ->getRow();

        /* PREV (id lebih besar) */
        $prev = $db->table('ikprssm_insiden')
            ->select('id')
            ->where('current_receiver_id', $user_id)
            ->where('status_laporan', 'DRAFT')
            ->where('id >', $id)
            ->orderBy('id', 'ASC')
            ->get(1)
            ->getRow();

        return view('ikprs/_detail_view_karu', [
            'insiden' => $insiden,
            'next_id' => $next->id ?? '',
            'prev_id' => $prev->id ?? ''
        ]);
    }

    public function detailInsiden($id)
    {
        $db = db_connect();
        $user_id = session()->get('hris_user_id');

        $insiden = $db->table('ikprssm_insiden')
            ->where('id', $id)
            ->get()
            ->getRowArray();

        if (!$insiden) {
            return "<div class='p-3 text-danger'>Data tidak ditemukan</div>";
        }

        $status = $insiden['status_laporan'];

        $karuUser = null;
        $komiteUser = null;

        // Ambil nama KARU dari tabel ikprssm_insiden (field karu_id)
        if (!empty($insiden['karu_id'])) {
            try {
                $db2 = db_connect('db2');
                $karu = $db2->table('tb_users')
                    ->select('nama_lengkap, nip')
                    ->where('id', $insiden['karu_id'])
                    ->get()
                    ->getRow();
                if ($karu) {
                    $karuUser = (object)['full_name' => $karu->nama_lengkap, 'nip' => $karu->nip];
                }
            } catch (\Exception $e) {
                log_message('error', 'Error get karu: ' . $e->getMessage());
            }
        }
        
        // Ambil nama KOMITE dari tabel ikprssm_insiden (field komite_id)
        if (!empty($insiden['komite_id'])) {
            try {
                $db2 = db_connect('db2');
                $komite = $db2->table('tb_users')
                    ->select('nama_lengkap, nip')
                    ->where('id', $insiden['komite_id'])
                    ->get()
                    ->getRow();
                if ($komite) {
                    $komiteUser = (object)['full_name' => $komite->nama_lengkap, 'nip' => $komite->nip];
                }
            } catch (\Exception $e) {
                log_message('error', 'Error get komite: ' . $e->getMessage());
            }
        }

        /* =============================
       NEXT & PREV berdasarkan tipe
         ============================== */

        if ($status == 'DRAFT') {

            // INBOX KARU
            $next = $db->table('ikprssm_insiden')
                ->select('id')
                ->where('current_receiver_id', $user_id)
                ->where('status_laporan', 'DRAFT')
                ->where('id <', $id)
                ->orderBy('id', 'DESC')
                ->get(1)
                ->getRow();

            $prev = $db->table('ikprssm_insiden')
                ->select('id')
                ->where('current_receiver_id', $user_id)
                ->where('status_laporan', 'DRAFT')
                ->where('id >', $id)
                ->orderBy('id', 'ASC')
                ->get(1)
                ->getRow();
        } else {

            // SEND (pelapor)
            $next = $db->table('ikprssm_insiden')
                ->select('id')
                ->where('user_id', $insiden['user_id'])
                ->whereIn('status_laporan', ['TERKIRIM', 'KARU', 'INSTALASI', 'SELESAI'])
                ->where('id <', $id)
                ->orderBy('id', 'DESC')
                ->get(1)
                ->getRow();

            $prev = $db->table('ikprssm_insiden')
                ->select('id')
                ->where('user_id', $insiden['user_id'])
                ->whereIn('status_laporan', ['TERKIRIM', 'KARU', 'INSTALASI', 'SELESAI'])
                ->where('id >', $id)
                ->orderBy('id', 'ASC')
                ->get(1)
                ->getRow();
        }

        $tipe = $this->request->getGet('tipe') ?? 'inbox';

        $user_role = session()->get('user_role');

        return view('ikprs/_detail_view_karu', [
            'insiden' => $insiden,
            'next_id' => $next->id ?? '',
            'prev_id' => $prev->id ?? '',
            'tipe' => $tipe,
            'user_role' => $user_role,
            'karu_user' => $karuUser,
            'komite_user' => $komiteUser

        ]);
    }



    //20.03.2026
    // public function tandaiDibaca()
    // {
    //     $insiden_id = $this->request->getPost('insiden_id');
    //     $user_id    = session()->get('hris_user_id');

    //     $db = db_connect();

    //     // ambil notif milik user login SAJA
    //     $notif = $db->table('ikprssm_notifikasi')
    //         ->where('insiden_id', $insiden_id)
    //         ->where('hris_user_id', $user_id)
    //         ->where('sender_id !=', $user_id) // 🔥 WAJIB BANGET
    //         ->orderBy('id', 'DESC') // ambil terbaru
    //         ->get()
    //         ->getRow();

    //     // ❌ kalau tidak ada → STOP (ini kunci biar KARU tidak update notif KOMITE)
    //     if (!$notif) {
    //         return $this->response->setJSON([
    //             'status' => 'skip'
    //         ]);
    //     }

    //     // ✅ update notif sendiri saja
    //     $db->table('ikprssm_notifikasi')
    //         ->where('id', $notif->id)
    //         ->where('is_read', 0)
    //         ->update([
    //             'is_read' => 1
    //         ]);

    //     // ==========================
    //     // 🔒 UPDATE BALIK TERBATAS
    //     // ==========================

    //     // hanya update sender JIKA DIA MEMANG PASANGAN LANGSUNG
    //     $db->table('ikprssm_notifikasi')
    //         ->where('insiden_id', $insiden_id)
    //         ->where('sender_id', $user_id) // 🔥 penting
    //         ->where('hris_user_id', $notif->sender_id) // pasangan
    //         ->where('sender_id != hris_user_id') // 🔥 INI KUNCI
    //         ->where('is_read', 0)
    //         ->update([
    //             'is_read' => 1
    //         ]);

    //     return $this->response->setJSON([
    //         'status' => 'ok'
    //     ]);
    // }


    // public function tandaiDibaca()
    // {
    //     $insiden_id = $this->request->getPost('insiden_id');
    //     $user_id    = session()->get('hris_user_id');

    //     $db = db_connect();

    //     // ✅ HANYA update milik sendiri
    //     $db->table('ikprssm_notifikasi')
    //         ->where('insiden_id', $insiden_id)
    //         ->where('hris_user_id', $user_id)
    //         ->where('is_read', 0)
    //         ->update([
    //             'is_read' => 1
    //         ]);

    //     return $this->response->setJSON(['status' => 'ok']);
    // }

    // public function tandaiDibaca()
    // {
    //     $insiden_id = $this->request->getPost('insiden_id');
    //     $user_id    = session()->get('hris_user_id');
    //     $role       = session()->get('user_role'); // 🔥 TAMBAHAN

    //     $db = db_connect();

    //     // ==========================
    //     // ✅ 1. UPDATE MILIK SENDIRI
    //     // ==========================
    //     $db->table('ikprssm_notifikasi')
    //         ->where('insiden_id', $insiden_id)
    //         ->where('hris_user_id', $user_id)
    //         ->where('is_read', 0)
    //         ->update([
    //             'is_read' => 1
    //         ]);

    //     // ==========================
    //     // 🔥 2. AMBIL PELAPOR
    //     // ==========================
    //     $insiden = $db->table('ikprssm_insiden')
    //         ->select('user_id, karu_id')
    //         ->where('id', $insiden_id)
    //         ->get()
    //         ->getRow();

    //     // ==========================
    //     // 🔥 3. UPDATE NOTIF PELAPOR
    //     // ==========================
    //     if ($insiden) {
    //         $db->table('ikprssm_notifikasi')
    //             ->where('insiden_id', $insiden_id)
    //             ->where('hris_user_id', $insiden->user_id) // 🔥 pelapor
    //             ->where('is_read', 0)
    //             ->update([
    //                 'is_read' => 1
    //             ]);
    //     }
    //     // ==========================
    //     // 🚀 4. KHUSUS KOMITE BACA
    //     // ==========================
    //     if ($role == 'KOMITE' && $insiden) {

    //         // update tracking
    //         $db->table('ikprssm_insiden')
    //             ->where('id', $insiden_id)
    //             ->update([
    //                 'komite_read_at' => date('Y-m-d H:i:s')
    //             ]);

    //         // 🔥 kirim notif ke KARU (sekali saja)
    //         $cek = $db->table('ikprssm_notifikasi')
    //             ->where('insiden_id', $insiden_id)
    //             ->where('hris_user_id', $insiden->karu_id)
    //             ->where('pesan', 'Komite telah membaca laporan')
    //             ->get()
    //             ->getRow();

    //         if (!$cek) {
    //             $db->table('ikprssm_notifikasi')->insert([
    //                 'sender_id'    => $user_id,
    //                 'hris_user_id' => $insiden->karu_id,
    //                 'insiden_id'   => $insiden_id,
    //                 'pesan'        => 'Komite telah membaca laporan',
    //                 'type'         => 'to_karu',
    //                 'is_read'      => 0,
    //                 'created_at'   => date('Y-m-d H:i:s')
    //             ]);
    //         }
    //     }

    //     return $this->response->setJSON(['status' => 'ok']);
    // }

    // baru di matikan lumayan 20.03.2026
    // public function tandaiDibaca()
    // {
    //     $insiden_id = $this->request->getPost('insiden_id');
    //     $user_id    = session()->get('hris_user_id');
    //     $role       = session()->get('user_role');

    //     $db = db_connect();

    //     // ==========================
    //     // ✅ 1. UPDATE NOTIF SENDIRI
    //     // ==========================
    //     $db->table('ikprssm_notifikasi')
    //         ->where('insiden_id', $insiden_id)
    //         ->where('hris_user_id', $user_id)
    //         ->where('is_read', 0)
    //         ->update([
    //             'is_read' => 1
    //         ]);

    //     // ==========================
    //     // 🔥 2. AMBIL DATA INSIDEN
    //     // ==========================
    //     $insiden = $db->table('ikprssm_insiden')
    //         ->select('user_id, karu_id')
    //         ->where('id', $insiden_id)
    //         ->get()
    //         ->getRow();

    //     // ==========================
    //     // 🔥 3. UPDATE NOTIF PELAPOR
    //     // ==========================
    //     if ($insiden) {
    //         $db->table('ikprssm_notifikasi')
    //             ->where('insiden_id', $insiden_id)
    //             ->where('hris_user_id', $insiden->user_id)
    //             ->where('is_read', 0)
    //             ->update([
    //                 'is_read' => 1
    //             ]);
    //     }

    //     // ==========================
    //     // 🔥 4. KHUSUS KARU BACA
    //     // ==========================
    //     if ($role == 'KARU') {
    //         $db->table('ikprssm_insiden')
    //             ->where('id', $insiden_id)
    //             ->update([
    //                 'karu_read_at' => date('Y-m-d H:i:s')
    //             ]);
    //     }

    //     // ==========================
    //     // 🔥 5. KHUSUS KOMITE BACA
    //     // ==========================
    //     if ($role == 'KOMITE' && $insiden) {

    //         // update tracking
    //         $db->table('ikprssm_insiden')
    //             ->where('id', $insiden_id)
    //             ->update([
    //                 'komite_read_at' => date('Y-m-d H:i:s')
    //             ]);

    //         $cek = $db->table('ikprssm_notifikasi')
    //             ->where('insiden_id', $insiden_id)
    //             ->where('hris_user_id', $insiden->karu_id)
    //             ->where('pesan', 'Komite telah membaca laporan')
    //             ->get()
    //             ->getRow();

    //         if ($cek) {

    //             // 🔥 UPDATE notif lama → jadi "baru lagi"
    //             $db->table('ikprssm_notifikasi')
    //                 ->where('id', $cek->id)
    //                 ->update([
    //                     'is_read'    => 0, // munculin lagi
    //                     'created_at' => date('Y-m-d H:i:s'),
    //                     'pesan'      => 'Komite telah membaca laporan'
    //                 ]);
    //         } else {

    //             // 🔥 kalau belum ada → insert baru
    //             $db->table('ikprssm_notifikasi')->insert([
    //                 'sender_id'    => $user_id,
    //                 'hris_user_id' => $insiden->karu_id,
    //                 'insiden_id'   => $insiden_id,
    //                 'pesan'        => 'Komite telah membaca laporan',
    //                 'status'       => 'INFO',
    //                 'type'         => 'to_karu',
    //                 'is_read'      => 1,
    //                 'created_at'   => date('Y-m-d H:i:s')
    //             ]);
    //         }
    //     }

    //     return $this->response->setJSON(['status' => 'ok']);
    // }

    public function tandaiDibaca()
    {
        $insiden_id = $this->request->getPost('insiden_id');
        $user_id    = session()->get('hris_user_id');
        $role       = session()->get('user_role');

        $db = db_connect();

        // ==========================
        // 🔥 1. AMBIL DATA INSIDEN
        // ==========================
        $insiden = $db->table('ikprssm_insiden')
            ->select('user_id, karu_id')
            ->where('id', $insiden_id)
            ->get()
            ->getRow();

        // ==========================
        // ✅ 2. UPDATE is_read di notifikasi (KARU & KOMITE)
        // ==========================
        if ($role == 'KARU') {
            
            // Update notifikasi untuk KARU sendiri
            $db->table('ikprssm_notifikasi')
                ->where('insiden_id', $insiden_id)
                ->where('hris_user_id', $user_id)
                ->where('is_read', 0)
                ->update([
                    'is_read' => 1
                ]);

            // Update notifikasi untuk PELAPOR juga (karu sudah membaca)
            if ($insiden && $insiden->user_id) {
                $db->table('ikprssm_notifikasi')
                    ->where('insiden_id', $insiden_id)
                    ->where('hris_user_id', $insiden->user_id)
                    ->where('is_read', 0)
                    ->update([
                        'is_read' => 1
                    ]);
            }

            // Track KARU baca di insiden
            $db->table('ikprssm_insiden')
                ->where('id', $insiden_id)
                ->update([
                    'karu_read_at' => date('Y-m-d H:i:s')
                ]);

        } elseif ($role == 'KOMITE') {

            // update notif milik sendiri
            $db->table('ikprssm_notifikasi')
                ->where('insiden_id', $insiden_id)
                ->where('hris_user_id', $user_id)
                ->where('is_read', 0)
                ->update([
                    'is_read' => 1
                ]);

            // update notif pelapor
            if ($insiden) {
                $db->table('ikprssm_notifikasi')
                    ->where('insiden_id', $insiden_id)
                    ->where('hris_user_id', $insiden->user_id)
                    ->where('is_read', 0)
                    ->update([
                        'is_read' => 1
                    ]);
            }
        }

        // ==========================
        // 🔥 3. TRACKING KOMITE BACA + SIMPAN KOMITE YANG MEMBUKA PERTAMA
        // ==========================
        if ($role == 'KOMITE' && $insiden) {

            // Cek apakah sudah ada komite_id (sudah ada yang membuka dulu)
            $insidenData = $db->table('ikprssm_insiden')
                ->select('komite_id, komite_opened_at')
                ->where('id', $insiden_id)
                ->get()
                ->getRow();

            // Jika belum ada komite_id, simpan komite pertama yang membuka
            if (empty($insidenData->komite_id)) {
                $db->table('ikprssm_insiden')
                    ->where('id', $insiden_id)
                    ->update([
                        'komite_id'        => $user_id,
                        'komite_opened_at' => date('Y-m-d H:i:s')
                    ]);
            }

            // Selalu update komite_read_at untuk tracking
            $db->table('ikprssm_insiden')
                ->where('id', $insiden_id)
                ->update([
                    'komite_read_at' => date('Y-m-d H:i:s')
                ]);

            $cek = $db->table('ikprssm_notifikasi')
                ->where('insiden_id', $insiden_id)
                ->where('hris_user_id', $insiden->karu_id)
                ->where('pesan', 'Komite telah membaca laporan')
                ->get()
                ->getRow();

            if ($cek) {

                // munculin lagi notif ke KARU
                $db->table('ikprssm_notifikasi')
                    ->where('id', $cek->id)
                    ->update([
                        'is_read'    => 0,
                        'created_at' => date('Y-m-d H:i:s'),
                        'pesan'      => 'Komite telah membaca laporan'
                    ]);
            } else {

                // insert notif baru ke KARU
                $db->table('ikprssm_notifikasi')->insert([
                    'sender_id'    => $user_id,
                    'hris_user_id' => $insiden->karu_id,
                    'insiden_id'   => $insiden_id,
                    'pesan'        => 'Komite telah membaca laporan',
                    'status'       => 'INFO',
                    'type'         => 'to_karu',
                    'is_read'      => 0, // 🔥 harus 0 biar muncul
                    'created_at'   => date('Y-m-d H:i:s')
                ]);
            }
        }

        return $this->response->setJSON(['status' => 'ok']);
    }

    // Validasi komite
    // public function validasi_komite()
    // {
    //     $db = db_connect();

    //     $id      = $this->request->getPost('id');
    //     $aksi    = $this->request->getPost('aksi');
    //     $catatan = $this->request->getPost('catatan');
    //     $grading = $this->request->getPost('grading');

    //     $user_id = session()->get('hris_user_id'); // 🔥 ambil user komite


    //     if (!$grading) {
    //         return $this->response->setJSON([
    //             'status' => 'error',
    //             'message' => 'Grading wajib diisi'
    //         ]);
    //     }

    //     if ($aksi == 'setujui') {

    //         $data = [
    //             'status_laporan' => 'SELESAI',
    //             'grading_final'  => $grading,
    //             'catatan_komite' => $catatan,
    //             'validated_at'   => date('Y-m-d H:i:s'),
    //             'selesai_at'     => date('Y-m-d H:i:s'),

    //             'komite_id'        => $user_id,
    //             'komite_opened_by' => $user_id,
    //             'updated_at'       => date('Y-m-d H:i:s')
    //         ];
    //     } else {

    //         $data = [
    //             'status_laporan' => 'INSTALASI',
    //             'komite_id'        => $user_id,
    //             'grading_final'  => $grading,
    //             'komite_opened_by' => $user_id,
    //             'validated_at'   => date('Y-m-d H:i:s'),
    //             'catatan_komite' => $catatan
    //         ];
    //     }

    //     $db->table('ikprssm_insiden')
    //         ->where('id', $id)
    //         ->update($data);

    //     return $this->response->setJSON(['status' => 'success']);
    // }

    public function validasi_komite()
    {
        $db = db_connect();
        $notifModel = new IkpNotifikasiModel();

        $id      = $this->request->getPost('id');
        $catatan = $this->request->getPost('catatan');
        $grading = $this->request->getPost('grading');

        $user_id   = session()->get('hris_user_id');
        $user_name = session()->get('hris_full_name');

        // ======================
        // VALIDASI
        // ======================
        if (!$id) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'ID tidak ditemukan'
            ]);
        }

        if (!$grading) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Grading wajib diisi'
            ]);
        }

        if (!$catatan || trim($catatan) == '') {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Catatan komite wajib diisi'
            ]);
        }

        // ambil data insiden
        $insiden = $db->table('ikprssm_insiden')
            ->where('id', $id)
            ->get()
            ->getRow();

        if (!$insiden) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Data tidak ditemukan'
            ]);
        }

        $db->transStart();

        // ======================
        // UPDATE DATA
        // ======================
        $db->table('ikprssm_insiden')
            ->where('id', $id)
            ->update([
                'status_laporan' => 'SELESAI',
                'grading_final'  => $grading,
                'catatan_komite' => $catatan,
                'validated_at'   => date('Y-m-d H:i:s'),
                'selesai_at'     => date('Y-m-d H:i:s'),

                'komite_id'        => $user_id,
                'komite_opened_by' => $user_id,

                'current_receiver_id'   => null,
                'current_receiver_role' => null,

                'updated_at' => date('Y-m-d H:i:s')
            ]);

        /*
        ==========================
        NOTIF KE KARU
        ==========================
        */

        if (!empty($insiden->karu_id)) {
            
            // Update semua notifikasi lama KARU untuk insiden ini jadi read
            $db->table('ikprssm_notifikasi')
                ->where('insiden_id', $id)
                ->where('hris_user_id', $insiden->karu_id)
                ->update(['is_read' => 1]);

            // Insert notifikasi baru
            $notifModel->insert([
                'sender_id'    => $user_id,
                'hris_user_id' => $insiden->karu_id,
                'insiden_id'   => $id,
                'pesan'        => 'Laporan telah divalidasi dan diselesaikan oleh Komite PMKP',
                'status'       => 'INFO',
                'type'         => 'to_karu',
                'is_read'      => 0,
                'created_at'   => date('Y-m-d H:i:s')
            ]);
        }

        /*
        ==========================
        NOTIF KE PELAPOR
        ==========================
        */

        if (!empty($insiden->user_id)) {
            
            // Update semua notifikasi lama pelapor untuk insiden ini jadi read
            $db->table('ikprssm_notifikasi')
                ->where('insiden_id', $id)
                ->where('hris_user_id', $insiden->user_id)
                ->update(['is_read' => 1]);

            // Insert notifikasi baru
            $notifModel->insert([
                'sender_id'    => $user_id,
                'hris_user_id' => $insiden->user_id,
                'insiden_id'   => $id,
                'pesan'        => 'Laporan Anda telah divalidasi oleh Komite PMKP dan dinyatakan selesai',
                'status'       => 'INFO',
                'type'         => 'to_pelapor',
                'is_read'      => 0,
                'created_at'   => date('Y-m-d H:i:s')
            ]);
        }

        $db->transComplete();

        if ($db->transStatus() === FALSE) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Gagal menyimpan data'
            ]);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Validasi komite berhasil'
        ]);
    }

    public function kirimEmailTest()
    {
        $email = \Config\Services::email();

        $email->setTo('bimahayunugraha@gmail.com');
        $email->setSubject('Notifikasi IKPRS');

        $email->setMessage('
        <h3>Laporan Insiden Baru</h3>
        <p>Silahkan login ke sistem untuk melihat laporan.</p>
        ');

        if ($email->send()) {
            echo "Email berhasil dikirim";
        } else {
            echo $email->printDebugger(['headers']);
        }
    }



    // ================= END OF CONTROLLER =================
}
