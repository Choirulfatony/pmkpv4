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
        // 🔒 APP user tidak boleh akses IKPRS
        if (session()->get('login_source') === 'APP') {
            return redirect()->to('/siimut/dashboard');
        }

        $this->disableCache();

        $role = session()->get('user_role');
        if (!in_array($role, ['KOMITE', 'KARU'])) {
            return redirect()->to(site_url('ikprs/menu'))->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
        }

        $request = service('request');
        $db = db_connect();

        $tahunIni = date('Y');
        
        $tahunMin = $db->table('ikprssm_insiden')
            ->select('MIN(YEAR(selesai_at)) as min_tahun')
            ->where('status_laporan', 'SELESAI')
            ->where('selesai_at IS NOT NULL')
            ->get()
            ->getRow();
        
        $tahunMulai = $tahunMin && $tahunMin->min_tahun ? (int) $tahunMin->min_tahun : ($tahunIni - 4);

        $tahunFilter = $request->getGet('tahun') ?? $tahunIni;
        
        $filters = [
            'tahun'     => $tahunFilter,
            'triwulan'  => $request->getGet('triwulan') ?? null,
            'semester'  => $request->getGet('semester') ?? null
        ];

        $labels = [];
        $displayStart = (int) $tahunFilter;
        $displayEnd = (int) $tahunFilter;
        $xAxisType = 'tahun';

        if ($filters['tahun'] == '' || $filters['tahun'] == null) {
            $displayStart = $tahunMulai;
            $displayEnd = $tahunIni;
            $xAxisType = 'tahun';
        }

        if ($filters['triwulan']) {
            $xAxisType = 'bulan';
            $triwulan = (int) $filters['triwulan'];
            $startMonth = ($triwulan - 1) * 3 + 1;
            $endMonth = $triwulan * 3;
            $labels = [];
            for ($m = $startMonth; $m <= $endMonth; $m++) {
                $labels[] = date('M', mktime(0, 0, 0, $m, 1));
            }
        } elseif ($filters['semester']) {
            $xAxisType = 'bulan';
            $semester = (int) $filters['semester'];
            $startMonth = $semester == 1 ? 1 : 7;
            $endMonth = $semester == 1 ? 6 : 12;
            $labels = [];
            for ($m = $startMonth; $m <= $endMonth; $m++) {
                $labels[] = date('M', mktime(0, 0, 0, $m, 1));
            }
        } elseif ($filters['tahun'] && $filters['tahun'] != '') {
            $xAxisType = 'bulan';
            for ($m = 1; $m <= 12; $m++) {
                $labels[] = date('M', mktime(0, 0, 0, $m, 1));
            }
        } else {
            for ($t = $displayStart; $t <= $displayEnd; $t++) {
                $labels[] = (string) $t;
            }
        }

        $jenisInsiden = ['KNC', 'KTD', 'KTC', 'KPC', 'Sentinel'];
        $datasets = [];

        foreach ($jenisInsiden as $jenis) {
            $dataPerPeriode = [];

            if ($filters['triwulan']) {
                $triwulan = (int) $filters['triwulan'];
                $startMonth = ($triwulan - 1) * 3 + 1;
                $endMonth = $triwulan * 3;
                for ($m = $startMonth; $m <= $endMonth; $m++) {
                    $count = $db->table('ikprssm_insiden')
                        ->where('jenis_insiden', $jenis)
                        ->where('status_laporan', 'SELESAI')
                        ->where('selesai_at IS NOT NULL')
                        ->where("MONTH(selesai_at) = {$m}")
                        ->where("YEAR(selesai_at) = {$displayStart}")
                        ->countAllResults();
                    $dataPerPeriode[] = $count;
                }
            } elseif ($filters['semester']) {
                $semester = (int) $filters['semester'];
                $startMonth = $semester == 1 ? 1 : 7;
                $endMonth = $semester == 1 ? 6 : 12;
                for ($m = $startMonth; $m <= $endMonth; $m++) {
                    $count = $db->table('ikprssm_insiden')
                        ->where('jenis_insiden', $jenis)
                        ->where('status_laporan', 'SELESAI')
                        ->where('selesai_at IS NOT NULL')
                        ->where("MONTH(selesai_at) = {$m}")
                        ->where("YEAR(selesai_at) = {$displayStart}")
                        ->countAllResults();
                    $dataPerPeriode[] = $count;
                }
            } elseif ($filters['tahun'] && $filters['tahun'] != '') {
                for ($m = 1; $m <= 12; $m++) {
                    $count = $db->table('ikprssm_insiden')
                        ->where('jenis_insiden', $jenis)
                        ->where('status_laporan', 'SELESAI')
                        ->where('selesai_at IS NOT NULL')
                        ->where("MONTH(selesai_at) = {$m}")
                        ->where("YEAR(selesai_at) = {$displayStart}")
                        ->countAllResults();
                    $dataPerPeriode[] = $count;
                }
            } else {
                for ($t = $displayStart; $t <= $displayEnd; $t++) {
                    $count = $db->table('ikprssm_insiden')
                        ->where('jenis_insiden', $jenis)
                        ->where('status_laporan', 'SELESAI')
                        ->where("selesai_at >= '{$t}-01-01' AND selesai_at <= '{$t}-12-31'")
                        ->countAllResults();
                    $dataPerPeriode[] = $count;
                }
            }

            $datasets[] = [
                'jenis' => $jenis,
                'data' => $dataPerPeriode
            ];
        }

        $chartData = [
            'labels' => $labels,
            'datasets' => $datasets,
        ];

        $gradingLabels = [];
        $gradingTypes = ['HIJAU', 'BIRU', 'KUNING', 'MERAH'];
        
        $getGradingData = function($startMonth, $endMonth, $isYearly = false) use ($db, $displayStart, $gradingTypes) {
            $result = [];
            foreach ($gradingTypes as $g) {
                $dataArr = [];
                if ($isYearly) {
                    for ($t = $startMonth; $t <= $endMonth; $t++) {
                        $dataArr[] = (int) $db->table('ikprssm_insiden')
                            ->where('grading_final', $g)
                            ->where('status_laporan', 'SELESAI')
                            ->where("selesai_at >= '{$t}-01-01' AND selesai_at <= '{$t}-12-31'")
                            ->countAllResults();
                    }
                } else {
                    for ($m = $startMonth; $m <= $endMonth; $m++) {
                        $dataArr[] = (int) $db->table('ikprssm_insiden')
                            ->where('grading_final', $g)
                            ->where('status_laporan', 'SELESAI')
                            ->where('selesai_at IS NOT NULL')
                            ->where("MONTH(selesai_at) = {$m}")
                            ->where("YEAR(selesai_at) = {$displayStart}")
                            ->countAllResults();
                    }
                }
                $result[] = ['grading' => $g, 'data' => $dataArr];
            }
            return $result;
        };
        
        if ($filters['triwulan']) {
            $triwulan = (int) $filters['triwulan'];
            $startMonth = ($triwulan - 1) * 3 + 1;
            $endMonth = $triwulan * 3;
            $gradingLabels = [];
            for ($m = $startMonth; $m <= $endMonth; $m++) {
                $gradingLabels[] = date('M', mktime(0, 0, 0, $m, 1));
            }
            $gradingDatasets = $getGradingData($startMonth, $endMonth, false);
        } elseif ($filters['semester']) {
            $semester = (int) $filters['semester'];
            $startMonth = $semester == 1 ? 1 : 7;
            $endMonth = $semester == 1 ? 6 : 12;
            $gradingLabels = [];
            for ($m = $startMonth; $m <= $endMonth; $m++) {
                $gradingLabels[] = date('M', mktime(0, 0, 0, $m, 1));
            }
            $gradingDatasets = $getGradingData($startMonth, $endMonth, false);
        } elseif ($filters['tahun'] && $filters['tahun'] != '') {
            $gradingLabels = [];
            for ($m = 1; $m <= 12; $m++) {
                $gradingLabels[] = date('M', mktime(0, 0, 0, $m, 1));
            }
            $gradingDatasets = $getGradingData(1, 12, false);
        } else {
            $gradingLabels = [];
            for ($t = $displayStart; $t <= $displayEnd; $t++) {
                $gradingLabels[] = (string) $t;
            }
            $gradingDatasets = $getGradingData($displayStart, $displayEnd, true);
        }

        $gradingChartData = [
            'labels' => $gradingLabels,
            'datasets' => $gradingDatasets
        ];

        return $this->render('dashboard/index', [
            'judul'    => 'Dashboard IKPRS',
            'icon'     => '<i class="bi bi-clipboard-check"></i>',
            'chartData' => $chartData,
            'gradingChartData' => $gradingChartData,
            'filters' => $filters,
            'tahunMulai' => $tahunMulai,
            'tahunIni' => $tahunIni,
            'xAxisType' => $xAxisType,
            '_content'  => view('ikprs/dashboard', [
                'chartData' => $chartData,
                'gradingChartData' => $gradingChartData,
                'filters' => $filters,
                'tahunMulai' => $tahunMulai,
                'tahunIni' => $tahunIni,
                'xAxisType' => $xAxisType
            ]),
        ]);
    }


    public function ikprs()
    {
        // 🔒 APP user tidak boleh akses IKPRS
        if (session()->get('login_source') === 'APP') {
            return redirect()->to('/siimut/dashboard');
        }

        $this->disableCache();

        $tab = $this->request->getGet('tab') ?? '';
        
        log_message('error', 'ikprs() called with tab: ' . $tab);

        $content = view('ikprs/ikp_content', ['initial_tab' => $tab]);
        return $this->render('dashboard/index', [
            'judul'    => 'IKPRS',
            'icon'     => '<i class="bi bi-clipboard-check"></i>',
            '_content' => $content,
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

        log_message('error', 'counterAjax called - user_id: ' . $user_id . ', role: ' . $role);

        if (!$user_id) {
            return $this->response->setJSON([
                'error' => 'User belum login'
            ]);
        }

$db = db_connect();

        // Notifikasi count berdasarkan role
        if ($role == 'PELAPOR') {
            $typeFilter = ['type' => 'to_pelapor'];
        } elseif ($role == 'KARU') {
            $typeFilter = ['type' => 'to_karu'];
        } elseif ($role == 'KOMITE') {
            $typeFilter = ['type' => 'to_komite'];
        }

        // Total notifikasi unread (hanya yang perlu aksi = status NEW)
        $notifQuery = $db->table('ikprssm_notifikasi')
            ->where('hris_user_id', $user_id)
            ->where('is_read', 0)
            ->where('status', 'NEW');
        
        if ($role == 'PELAPOR') {
            $notifQuery->where('type', 'to_pelapor');
        } elseif ($role == 'KARU') {
            $notifQuery->where('type', 'to_karu');
        } elseif ($role == 'KOMITE') {
            $notifQuery->where('type', 'to_komite');
        }
        
        $total_notif = $notifQuery->countAllResults();

// ==========================
        // INBOX
        // ==========================
        log_message('error', "counterAjax: user_id=$user_id, role=$role");
        
        if ($role == 'KARU') {
            // Inbox KARU: semua laporan untuk KARU ini (DRAFT, KARU, TERKIRIM)
            $total_inbox = $db->table('ikprssm_insiden')
                ->where('karu_id', $user_id)
                ->countAllResults();
            
            log_message('error', "counterAjax KARU: karu_id=$user_id, total_inbox=$total_inbox");
            
            $total_draft = 0;
            $total_send = 0;

        } elseif ($role == 'KOMITE') {
            // KOMITE inbox = semua status
            $total_inbox = $db->table('ikprssm_insiden i')
                ->select('i.id')
                ->join('ikprssm_notifikasi n', 'n.insiden_id = i.id', 'left')
                ->where('n.hris_user_id', $user_id)
                ->whereIn('i.status_laporan', ['DRAFT', 'KARU', 'TERKIRIM', 'INSTALASI', 'SELESAI'])
                ->groupBy('i.id')
                ->countAllResults();

            $total_draft = 0;
        } else {
            // PELAPOR inbox: hanya SELESAI (laporan yang sudah selesai)
            $total_inbox = $db->table('ikprssm_insiden')
                ->where('user_id', $user_id)
                ->where('status_laporan', 'SELESAI')
                ->countAllResults();

            // PELAPOR draft: hanya DRAFT
            $total_draft = $db->table('ikprssm_insiden')
                ->where('user_id', $user_id)
                ->where('status_laporan', 'DRAFT')
                ->countAllResults();
        }

        // ==========================
        // SENT
        // ==========================
        if ($role == 'KARU') {
            // KARU sent: hanya yang sudah diproses KOMITE (INSTALASI/SELESAI)
            $total_send = $db->table('ikprssm_insiden')
                ->where('karu_id', $user_id)
                ->whereIn('status_laporan', ['INSTALASI', 'SELESAI'])
                ->countAllResults();
        } elseif ($role == 'KOMITE') {
            // KOMITE sent: sudah diproses (INSTALASI/SELESAI)
            $total_send = $db->table('ikprssm_insiden')
                ->where('komite_id', intval($user_id))
                ->whereIn('status_laporan', ['INSTALASI', 'SELESAI'])
                ->countAllResults();
        } elseif ($role == 'PELAPOR') {
            // PELAPOR sent: KARU + TERKIRIM + INSTALASI + SELESAI (sudah diproses KARU)
            $total_send = $db->table('ikprssm_insiden')
                ->where('user_id', $user_id)
                ->whereIn('status_laporan', ['KARU', 'TERKIRIM', 'INSTALASI', 'SELESAI'])
                ->countAllResults();
        }

        $notif = $this->getNotifList($user_id, $role);

        // total_info: notif tipe 'INFO' untuk user ini - MATI untuk KARU
        $total_info = 0;
        if ($role != 'KARU') {
            $total_info = $db->table('ikprssm_notifikasi')
                ->where('hris_user_id', $user_id)
                ->where('status', 'INFO')
                ->countAllResults();
        }

        return $this->response->setJSON([
            'total_notif' => $total_notif,
            'total_inbox' => $total_inbox,
            'total_draft' => $total_draft,
            'total_send'  => $total_send,
            'total_info'  => $total_info,
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
        log_message('error', 'getNotifList called - user_id: ' . $user_id . ', role: ' . $role);
        
        $db = db_connect();

        $typeFilter = "";

        if ($role == 'PELAPOR') {
            $typeFilter = "AND n.type = 'to_pelapor'";
        } elseif ($role == 'KARU') {
            $typeFilter = "AND n.type = 'to_karu'";
        } elseif ($role == 'KOMITE') {
            $typeFilter = "AND n.type = 'to_komite'";
            // Filter: jangan tampilkan jika sudah dikunci oleh KOMITE lain
            $komiteFilter = "AND (i.komite_id IS NULL OR i.komite_id = " . intval($user_id) . ")";
        }

        // Debug: log jika role kosong
        if (empty($role)) {
            log_message('error', 'getNotifList called with empty role, user_id: ' . $user_id);
        }

        $builder = $db->table('ikprssm_notifikasi n')
            ->select('
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
            ')
            ->join('ikprssm_insiden i', 'i.id = n.insiden_id', 'left')
            ->join('master_institution_department d', 'd.department_id = i.tempat_insiden', 'left')
            ->where('n.hris_user_id', $user_id)
            ->where('n.status', 'NEW');

        if ($role == 'PELAPOR') {
            $builder->where('n.type', 'to_pelapor');
        } elseif ($role == 'KARU') {
            $builder->where('n.type', 'to_karu');
        } elseif ($role == 'KOMITE') {
            $builder->where('n.type', 'to_komite');
            // Filter: jangan tampilkan jika sudah dikunci oleh KOMITE lain
            $builder->groupStart()
                ->where('i.komite_id IS NULL')
                ->orWhere('i.komite_id', intval($user_id))
                ->groupEnd();
        }

        $rows = $builder->orderBy('n.id', 'DESC')->get()->getResultArray();

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

        // 3️⃣ AMBIL KARU DULU (WAJIB SEBELUM INSERT) - HANYA ROLE_ID = 1 (KARU)
        $karu = $db->table('unit_karu')
            ->where('department_id', $tempat_insiden)
            ->where('role_id', 1)  // ✅ PASTIKAN HANYA AMBIL KARU (BUKAN KOMITE)
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

            // 🔥 Simpan sebagai DRAFT dulu
            'status_laporan'      => 'DRAFT',
            'karu_id'           => $karu->hris_user_id,
            'current_receiver_id' => null,
            'current_receiver_role' => null
        ];

        log_message('error', 'simpanikp: inserting with status=DRAFT, karu_id=' . $karu->hris_user_id);

        $ikpModel->insert($dataInsiden);
        $insiden_id = $ikpModel->getInsertID();

        if (!$insiden_id) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Gagal menyimpan data IKP'
            ]);
        }

        return $this->response->setJSON([
            'status'  => true,
            'message' => 'Laporan berhasil disimpan sebagai DRAFT'
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

        $model = new IkpInsidenModel();

        // 🔹 Hitung total
        $total = $model->countDraftFiltered($user_id, $keyword, []);

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
            'list'        => $model->getDraftPaginated($user_id, $limit, $offset, $keyword, []),
            'total'       => $total,
            'total_pages' => $total_pages,
            'page'        => $page,
            'keyword'     => $keyword
        ];

        if ($request->isAJAX()) {
            return view('ikprs/_form_drafts', $data);
        }

        return view('ikprs/_form_drafts', $data);
    }

    // kirim draft ke KARU (PELAPOR)
    public function kirimDraft()
    {
        log_message('error', 'kirimDraft() called');
        
        $insiden_id = $this->request->getPost('insiden_id');
        $user_id = session()->get('hris_user_id');
        $role = session()->get('user_role');

        log_message('error', "kirimDraft: insiden_id=$insiden_id, user_id=$user_id, role=$role");

        if ($role !== 'PELAPOR') {
            log_message('error', 'kirimDraft: role not PELAPOR');
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Hanya PELAPOR yang dapat mengirim draft'
            ]);
        }

        $db = db_connect();
        $notifModel = new \App\Models\IkpNotifikasiModel();

        // Ambil data insiden
        $insiden = $db->table('ikprssm_insiden')
            ->where('id', $insiden_id)
            ->where('user_id', $user_id)
            ->get()
            ->getRow();

        if (!$insiden) {
            log_message('error', "kirimDraft: insiden not found for id=$insiden_id, user_id=$user_id");
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Data insiden tidak ditemukan'
            ]);
        }

        log_message('error', "kirimDraft: insiden status={$insiden->status_laporan}");

        if ($insiden->status_laporan != 'DRAFT') {
            log_message('error', "kirimDraft: status not DRAFT, current={$insiden->status_laporan}");
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Laporan sudah dikirim sebelumnya'
            ]);
        }

        // Ambil KARU berdasarkan department insiden
        $karu = $db->table('unit_karu')
            ->where('department_id', $insiden->tempat_insiden)
            ->where('role_id', 1) // KARU
            ->where('aktif', 1)
            ->get()
            ->getRow();

        if (!$karu) {
            log_message('error', "kirimDraft: KARU not found for department_id={$insiden->tempat_insiden}");
            return $this->response->setJSON([
                'status' => false,
                'message' => 'KARU tidak ditemukan untuk department ini'
            ]);
        }

        log_message('error', "kirimDraft: KARU found, karu_user_id={$karu->hris_user_id}");

        $db->transStart();

        // Update status ke KARU
        $updateResult = $db->table('ikprssm_insiden')
            ->where('id', $insiden_id)
            ->update([
                'status_laporan' => 'KARU',
                'karu_id' => $karu->hris_user_id,
                'current_receiver_id' => $karu->hris_user_id,
                'current_receiver_role' => 'KARU',
                'updated_at' => date('Y-m-d H:i:s')
            ]);

        log_message('error', "kirimDraft: update result=" . json_encode($updateResult));

        // Notifikasi ke KARU
        $notifData = [
            'sender_id' => $user_id,
            'hris_user_id' => $karu->hris_user_id,
            'insiden_id' => $insiden_id,
            'pesan' => 'Laporan IKP dikirim oleh PELAPOR',
            'status' => 'NEW',
            'type' => 'to_karu',
            'is_read' => 0,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        $notifModel->insert($notifData);
        $notif_id = $notifModel->getInsertID();
        
        log_message('error', "kirimDraft: notification inserted, id=$notif_id");

        $db->transComplete();

        if ($db->transStatus() === false) {
            log_message('error', 'kirimDraft: transaction failed');
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Gagal mengirim laporan'
            ]);
        }

        log_message('error', 'kirimDraft: SUCCESS');

        return $this->response->setJSON([
            'status' => true,
            'message' => 'Laporan berhasil dikirim ke KARU'
        ]);
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

        $model = new IkpInsidenModel();


        // 🔹 Hitung total
        $total = $model->countSendFiltered($user_id, $keyword, []);

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
            'list'        => $model->getSendPaginated($user_id, $limit, $offset, $keyword, []),
            'total'       => $total,
            'total_pages' => $total_pages,
            'page'        => $page,
            'keyword'     => $keyword
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

        $model = new IkpInsidenModel();

        // 🔹 HITUNG TOTAL BERDASARKAN ROLE
        $total = $model->countInboxFiltered($user_id, $keyword, 'inbox', []);

        $total_pages = $total > 0 ? (int) ceil($total / $limit) : 0;

        if ($total_pages === 0) {
            $page   = 0;
            $offset = 0;
        } else {
            $page   = max(1, min($page, $total_pages));
            $offset = ($page - 1) * $limit;
        }

        $data = [
            'inbox'       => $model->getInboxPaginated($user_id, $limit, $offset, $keyword, 'inbox', []),
            'total'       => $total,
            'total_pages' => $total_pages,
            'page'        => $page,
            'keyword'     => $keyword
        ];

        return view('ikprs/_form_inbox_karu', $data);
    }

    // Info / Notifikasi - Pakai LOGIC YANG SAMA dengan getNotifList di counterAjax
    public function formInfo()
    {
        helper('notifikasi');
        $request = service('request');

        $user_id = session()->get('hris_user_id');
        $role    = session()->get('user_role');

        log_message('error', 'formInfo called - user_id: ' . $user_id . ', role: ' . $role);

        if (!$user_id) {
            return 'SESSION USER BELUM ADA';
        }

        $page = (int) (
            $request->getPost('page')
            ?? $request->getGet('page')
            ?? 1
        );

        $keyword = trim($request->getGet('keyword') ?? '');
        $limit   = 20;

        $db = db_connect();

        // ===================== SAMA DENGAN getNotifList =====================
        $typeFilter = "";
        $komiteFilter = "";

        if ($role == 'PELAPOR') {
            $typeFilter = "AND n.type = 'to_pelapor'";
        } elseif ($role == 'KARU') {
            $typeFilter = "AND n.type = 'to_karu'";
        } elseif ($role == 'KOMITE') {
            $typeFilter = "AND n.type = 'to_komite'";
            // Filter: 不要tampilkan jika sudah dikunci oleh KOMITE lain
            $komiteFilter = "AND (i.komite_id IS NULL OR i.komite_id = " . intval($user_id) . ")";
        }

        // Query untuk hitung total - hanya INFO
        $countQuery = "
            SELECT COUNT(*) as total
            FROM ikprssm_notifikasi n
            LEFT JOIN ikprssm_insiden i ON i.id = n.insiden_id
            WHERE n.hris_user_id = ?
            AND n.status = 'INFO'
        ";
        if (!empty($typeFilter)) {
            $countQuery .= " AND n.type = '" . str_replace("'", '', explode("'", $typeFilter)[2]) . "'";
        }
        if (!empty($komiteFilter)) {
            $countQuery .= " " . $komiteFilter;
        }

        if (!empty($keyword)) {
            $countQuery .= " AND (n.pesan LIKE ? OR i.jenis_insiden LIKE ? OR i.nama_pasien LIKE ?)";
            $keywordLike = "%{$keyword}%";
            $total = (int) $db->query($countQuery, [$user_id, $keywordLike, $keywordLike, $keywordLike])->getRow()->total;
        } else {
            $total = (int) $db->query($countQuery, [$user_id])->getRow()->total;
        }

        $total_pages = $total > 0 ? (int) ceil($total / $limit) : 0;

        if ($total_pages === 0) {
            $page   = 0;
            $offset = 0;
        } else {
            $page   = max(1, min($page, $total_pages));
            $offset = ($page - 1) * $limit;
        }

        // Query untuk ambil data - hanya INFO
        $dataQuery = "
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
            WHERE n.hris_user_id = ?
            AND n.status = 'INFO'
        ";
        if (!empty($typeFilter)) {
            $dataQuery .= " AND n.type = '" . str_replace("'", '', explode("'", $typeFilter)[2]) . "'";
        }
        if (!empty($komiteFilter)) {
            $dataQuery .= " " . $komiteFilter;
        }
        if (!empty($keyword)) {
            $dataQuery .= " AND (n.pesan LIKE ? OR i.jenis_insiden LIKE ? OR i.nama_pasien LIKE ?)";
        }
        $dataQuery .= " ORDER BY n.id DESC LIMIT {$limit} OFFSET {$offset}";

        if (!empty($keyword)) {
            $rows = $db->query($dataQuery, [$user_id, $keywordLike, $keywordLike, $keywordLike])->getResultArray();
        } else {
            $rows = $db->query($dataQuery, [$user_id])->getResultArray();
        }

        // Proses data - SAMA DENGAN getNotifList
        $notif = [];
        foreach ($rows as $row) {
            $status_read = ($row['is_read'] == 0) ? 'Baru' : 'Sudah dibaca';

            $notif[] = [
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
        }

        $data = [
            'notif'       => $notif,
            'total'       => $total,
            'total_pages' => $total_pages,
            'page'        => $page,
            'keyword'     => $keyword
        ];

        return view('ikprs/_form_info', $data);
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

        if ($insiden->status_laporan != 'KARU') {

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
                'status_laporan'   => 'TERKIRIM', // ✅ Sudah diverifikasi KARU, kirim ke KOMITE
                'karu_read_at'     => date('Y-m-d H:i:s'),
                'current_receiver_role' => 'KOMITE',
                'current_receiver_id'   => NULL,
                'updated_at'       => date('Y-m-d H:i:s')
            ]);
/*
        ==========================
        NOTIF KE KARU (INFO - sudah diproses)
        ==========================
        */

        $notifModel->insert([
            'sender_id'    => session('hris_user_id'),
            'hris_user_id' => session('hris_user_id'),
            'insiden_id'   => $insiden_id,
            'pesan'        => 'Laporan telah diverifikasi dan menunggu analisa Komite',
            'status'       => 'INFO',
            'type'         => 'to_karu',
            'is_read'      => 0,
            'created_at'   => date('Y-m-d H:i:s')
        ]);

        /*
        ==========================
        NOTIF KE PELAPOR
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
        NOTIF KE KARU (STATUS PROSES) - MATIKAN (is_read = 1)
        ==========================
        */

        // MATIKAN NOTIFIKASI - Langsung jadi sudah dibaca
        // $notifModel->insert([
        //     'sender_id'    => session('hris_user_id'),
        //     'hris_user_id' => session('hris_user_id'),
        //     'insiden_id'   => $insiden_id,
        //     'pesan'        => 'Laporan telah dikirim ke Komite untuk analisa',
        //     'status'       => 'INFO',
        //     'type'         => 'to_karu',
        //     'is_read'      => 1, // 🔥 MATIKAN - sudah dibaca
        //     'created_at'   => date('Y-m-d H:i:s')
        // ]);

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
        $user_role = session()->get('user_role');

        $insiden = $db->table('ikprssm_insiden')
            ->where('id', $id)
            ->get()
            ->getRowArray();

        if (!$insiden) {
            return "<div class='p-3 text-danger'>Data tidak ditemukan</div>";
        }

        $status = $insiden['status_laporan'];
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

        log_message('error', "tandaiDibaca() called: insiden_id=$insiden_id, user_id=$user_id, role=$role");

        $db = db_connect();

        // ==========================
        // 🔥 1. AMBIL DATA INSIDEN
        // ==========================
        $insiden = $db->table('ikprssm_insiden')
            ->select('id, user_id, karu_id, status_laporan')
            ->where('id', $insiden_id)
            ->get()
            ->getRow();

        if (!$insiden) {
            log_message('error', "tandaiDibaca(): insiden not found, id=$insiden_id");
            return $this->response->setJSON(['status' => false, 'message' => 'Insiden not found']);
        }

        log_message('error', "tandaiDibaca(): insiden status=" . $insiden->status_laporan);

        // ==========================
        // ✅ 2. UPDATE is_read di notifikasi (KARU & KOMITE)
        // ==========================
        if ($role == 'KARU') {
            
            log_message('error', "tandaiDibaca(): updating KARU notifications");

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

            // Track KARU baca di insiden - update karu_read_at jika NULL
            if (empty($insiden->karu_read_at)) {
                $updateData = ['karu_read_at' => date('Y-m-d H:i:s')];
                
                // Ubah status ke KARU setelah KARU membaca (belum diverifikasi)
                $updateData['status_laporan'] = 'KARU';
                
                $db->table('ikprssm_insiden')
                    ->where('id', $insiden_id)
                    ->update($updateData);
                
                log_message('error', "tandaiDibaca: insiden_id=$insiden_id status => KARU");
            }

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
        // ✅ 4. UPDATE is_read untuk notif tipe 'info' (untuk user ini)
        // ==========================
        $db->table('ikprssm_notifikasi')
            ->where('hris_user_id', $user_id)
            ->where('type', 'info')
            ->where('is_read', 0)
            ->update([
                'is_read' => 1
            ]);

        // ==========================
        // 🔥 3. TRACKING KOMITE BACA + SIMPAN KOMITE YANG MEMBUKA PERTAMA + UBAH STATUS KE TERKIRIM
        // ==========================
        if ($role == 'KOMITE' && $insiden) {

            // Cek apakah sudah ada komite_id (sudah ada yang membuka dulu)
            $insidenData = $db->table('ikprssm_insiden')
                ->select('komite_id, komite_opened_at, status_laporan')
                ->where('id', $insiden_id)
                ->get()
                ->getRow();

            // Jika belum ada komite_id, simpan komite pertama yang membuka + ubah status ke TERKIRIM
            if (empty($insidenData->komite_id)) {
                $db->table('ikprssm_insiden')
                    ->where('id', $insiden_id)
                    ->update([
                        'komite_id'        => $user_id,
                        'komite_opened_at' => date('Y-m-d H:i:s'),
                        'komite_read_at'   => date('Y-m-d H:i:s'),
                        'status_laporan'   => 'TERKIRIM' // 🔥 Ubah dari KARU ke TERKIRIM (sudah terbaca komite)
                    ]);
            } else {
                // Jika sudah ada, tetap update tracking read
                $db->table('ikprssm_insiden')
                    ->where('id', $insiden_id)
                    ->update([
                        'komite_read_at' => date('Y-m-d H:i:s')
                    ]);
            }

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

        // CEK: Apakah sudah dikunci oleh KOMITE lain?
        if (!empty($insiden->komite_id) && $insiden->komite_id != $user_id) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Laporan ini sudah dikunci oleh Komite lain'
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

            // Insert notifikasi baru - langsung read
            $notifModel->insert([
                'sender_id'    => $user_id,
                'hris_user_id' => $insiden->karu_id,
                'insiden_id'   => $id,
                'pesan'        => 'Laporan telah divalidasi dan diselesaikan oleh Komite PMKP',
                'status'       => 'INFO',
                'type'         => 'to_karu',
                'is_read'      => 1,
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

            // Insert notifikasi baru - langsung read
            $notifModel->insert([
                'sender_id'    => $user_id,
                'hris_user_id' => $insiden->user_id,
                'insiden_id'   => $id,
                'pesan'        => 'Laporan Anda telah divalidasi oleh Komite PMKP dan dinyatakan selesai',
                'status'       => 'INFO',
                'type'         => 'to_pelapor',
                'is_read'      => 1,
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

        $email->setTo('choirulfatoni@gmail.com');
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
