<?php

// app/Controllers/Auth.php
namespace App\Controllers;

use App\Models\SessionAppsModel;
use CodeIgniter\Controller;

class Auth extends BaseController
{
    protected $session;
    protected $sessionApps;

    public function __construct()
    {
        $this->session = session();
        $this->sessionApps = new SessionAppsModel();
        helper(['url', 'captcha']);
    }

    public function index()
    {
        // jika sudah login → dashboard
        if ($this->session->get('logged_in')) {
            return redirect()->to('dashboard');
        }

        // jika redirect karena timeout
        if ($this->request->getGet('timeout')) {
            $this->session->setFlashdata('error', 'Session Anda berakhir karena tidak aktif.');
        }

        // ================= CAPTCHA =================
        $config = [
            "img_width"  => 120,
            "img_height" => 40,
        ];

        $captcha = create_captcha($config);

        $this->session->set([
            'captcha_word'  => strtoupper($captcha['word']),
            'captcha_image' => $captcha['image']
        ]);

        // 🔥 INI YANG WAJIB
        $contentData = [
            'captcha_image' => $this->session->get('captcha_image')
        ];

        $data = [
            'login_title' => 'PMKP v2.0 RSSM ',
            '_content'   => view('auth/login', $contentData),
            '_login_css' => view('_layout/_login_css'),
            '_login_js'  => view('_layout/_login_js'),
        ];

        return view('_layout/login_template', $data);
    }

    // ================= REFRESH CAPTCHA =================
    public function refresh_captcha()
    {
        $captcha = create_captcha([
            'img_width'  => 120,
            'img_height' => 40,
        ]);

        $this->session->set([
            'captcha_word'  => strtoupper($captcha['word']),
            'captcha_image' => $captcha['image']
        ]);

        return $this->response->setJSON([
            'captcha_image' => $captcha['image']
        ]);
    }

    // ================= PROCESS LOGIN =================
    public function process()
    {
        $identity = trim($this->request->getPost('identity'));
        $password = $this->request->getPost('password');
        $captcha  = strtoupper($this->request->getPost('captcha'));

        // CAPTCHA
        if ($captcha !== session()->get('captcha_word')) {
            return redirect()->back()->with('error', 'Captcha salah');
        }

        if (!$identity || !$password) {
            return redirect()->back()->with('error', 'Data login tidak lengkap');
        }

        /**
         * ============================================
         * AUTO DETECT LOGIN TYPE
         * ============================================
         */
        if (filter_var($identity, FILTER_VALIDATE_EMAIL)) {

            // 🔐 LOGIN APLIKASI (DB 1)
            return $this->loginAplikasi($identity, $password);
        } else {

            // 🔐 LOGIN HRIS (DB 2)
            return $this->loginHris($identity, $password);
        }
    }

    private function loginAplikasi(string $email, string $password)
    {
        $user = $this->sessionApps->checkLogin([
            'user_profile.profile_email'    => $email,
            'user_profile.profile_password' => md5($password),
            'user_profile.profile_record_status' => 'A'
        ]);

        if (!$user) {
            return redirect()->back()->with('error', 'Email atau password salah');
        }

        session()->set([
            'logged_in'       => true,
            'login_source'    => 'APP',

            'profile_id'      => $user->profile_id,
            'nama_lengkap'    => $user->profile_fullname,
            'profile_email'   => $user->profile_email,

            'department_id'   => $user->department_id,
            'department_name' => $user->department_name,
            'role'            => $user->group_name,

            'login_time'      => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('dashboard');
    }

    private function loginHris(string $nip, string $password)
    {
        $hrisModel = new \App\Models\HrisUserModel();
        $user = $hrisModel->checkLoginHris($nip, $password);

        if (!$user) {
            return redirect()->back()->with('error', 'NIP atau password salah');
        }

        // 🔹 DETEKSI ROLE BERDASARKAN unit_karu
        $role = $this->detectRoleByHrisId($user->id);

        // 🔹 SET SESSION LOGIN
        session()->set([
            'logged_in'       => true,
            'login_source'    => 'HRIS',
            'hris_user_id'    => $user->id,
            'hris_nip'        => $user->nip,
            'hris_full_name'  => $user->nama_lengkap ?? $user->nama,
            'department_name' => $user->department_name ?? '',
            'user_role'       => $role,
            'login_time'      => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/ikprs');
    }

    private function detectRoleByHrisId($hris_user_id)
    {
        $db = db_connect();

        $user = $db->table('unit_karu uk')
            ->select('
            uk.role_id,
            uk.department_id,
            mid.department_name,
            up.profile_fullname
        ')
            ->join('user_profile up', 'up.profile_id = uk.profile_id', 'left')
            ->join(
                'master_institution_department mid',
                'mid.department_id = uk.department_id',
                'left'
            )
            ->where('uk.hris_user_id', $hris_user_id)
            ->where('uk.aktif', 1)
            ->get()
            ->getRow();

        if ($user) {

            session()->set([
                'karu_fullname'  => $user->profile_fullname,
                'karu_room_name' => $user->department_name
            ]);

            // 🔹 DETEKSI ROLE
            if ($user->role_id == 1) {
                return 'KARU';
            }

            if ($user->role_id == 2) {
                return 'KOMITE';
            }
        }

        return 'PELAPOR';
    }
    // private function loginHris(string $nip, string $password)
    // {
    //     $hrisModel = new \App\Models\HrisUserModel();
    //     $user = $hrisModel->checkLoginHris($nip, $password);

    //     if (!$user) {
    //         return redirect()->back()->with('error', 'NIP atau password salah');
    //     }

    //     // 🔥 1. DETEKSI ROLE
    //     $role = $this->detectRoleByNip($user->nip);

    //     // 🔥 2. SET SESSION DASAR
    //     session()->set([
    //         'logged_in'       => true,
    //         'login_source'    => 'HRIS',
    //         'hris_user_id'    => $user->id,
    //         'hris_nip'        => $user->nip,
    //         'hris_full_name'  => $user->nama_lengkap ?? $user->nama,
    //         'department_name' => $user->department_name ?? '',
    //         'user_role'       => $role,
    //         'login_time'      => date('Y-m-d H:i:s'),
    //     ]);

    //     // 🔥 3. JIKA PELAPOR → AMBIL KARU UNITNYA
    //     if ($role === 'PELAPOR') {

    //         $db = db_connect();

    //         // ambil profile dulu berdasarkan NIP
    //         $profile = $db->table('user_profile')
    //             ->where('profile_employee_id', $user->nip)
    //             ->where('profile_record_status', 'A')
    //             ->get()
    //             ->getRow();

    //         if ($profile) {

    //             $karuUnit = $db->table('unit_karu uk')
    //                 ->select('
    //                     up.profile_fullname,
    //                     up.profile_employee_id,
    //                     mid.department_name
    //                 ')
    //                 ->join('user_profile up', 'up.profile_id = uk.profile_id')
    //                 ->join(
    //                     'master_institution_department mid',
    //                     'mid.department_id = uk.department_id',
    //                     'left'
    //                 )
    //                 ->where('uk.department_id', $profile->profile_department_id)
    //                 ->where('uk.aktif', 1)
    //                 ->get()
    //                 ->getRow();


    //             if ($karuUnit) {
    //                 session()->set([
    //                     'karu_fullname' => $karuUnit->profile_fullname,
    //                     'karu_nip'      => $karuUnit->profile_employee_id,
    //                     'karu_room_name' => $karuUnit->department_name
    //                 ]);
    //             }
    //         }
    //     }

    //     return redirect()->to('/ikprs');
    // }


    // private function detectRoleByNip($nip)
    // {
    //     $db = db_connect();

    //     $karu = $db->table('user_profile up')
    //         ->select('up.profile_fullname,
    //               up.profile_employee_id,
    //               uk.department_id,
    //               mid.department_name')
    //         ->join('unit_karu uk', 'uk.profile_id = up.profile_id AND uk.aktif = 1')
    //         ->join(
    //             'master_institution_department mid',
    //             'mid.department_id = uk.department_id',
    //             'left'
    //         )
    //         ->where('up.profile_employee_id', $nip)
    //         ->where('up.profile_record_status', 'A')
    //         ->get()
    //         ->getRow();

    //     if ($karu) {
    //         session()->set([
    //             'karu_fullname' => $karu->profile_fullname,
    //             'karu_nip'      => $karu->profile_employee_id,
    //             'karu_room_name' => $karu->department_name
    //         ]);

    //         return 'KARU';
    //     }

    //     return 'PELAPOR';
    // }



    // ================= REGENERATE CAPTCHA =================
    private function regenerateCaptcha()
    {
        helper('captcha');

        $captcha = create_captcha([
            'img_width'  => 120,
            'img_height' => 40,
        ]);

        $this->session->set([
            'captcha_word'  => strtoupper($captcha['word']),
            'captcha_image' => $captcha['image'],
        ]);
    }


    public function check_session()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setStatusCode(401)->setJSON(['status' => 'unauthorized']);
        }
        return $this->response->setJSON(['status' => 'ok']);
    }


    public function logout()
    {
        session()->destroy();

        $this->response
            ->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->setHeader('Pragma', 'no-cache')
            ->setHeader('Expires', '0');

        return redirect()->to(site_url('auth'));
    }
}
