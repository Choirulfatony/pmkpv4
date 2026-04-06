<?php

// app/Controllers/Auth.php
namespace App\Controllers;

use App\Models\SessionAppsModel;
use App\Libraries\Captcha;
use App\Libraries\GoogleLogin;
use CodeIgniter\Controller;

class Auth extends BaseController
{
    protected $session;
    protected $sessionApps;
    protected Captcha $captcha;

    public function __construct()
    {
        $this->session = session();
        $this->sessionApps = new SessionAppsModel();
        $this->captcha = new Captcha();
        helper(['url']);
    }

    public function index()
    {
        if ($this->session->get('logged_in')) {
            return redirect()->to('/ikprs');
        }

        if ($this->request->getGet('timeout')) {
            $this->session->setFlashdata('error', 'Session Anda berakhir karena tidak aktif.');
        }

        $captcha = $this->captcha->generate([
            'min' => 1,
            'max' => 20
        ]);

        $this->session->set([
            'captcha_word'  => $captcha['word'],
            'captcha_html' => $captcha['html']
        ]);

        $contentData = [
            'captcha_html' => $this->session->get('captcha_html')
        ];

        $data = [
            'login_title' => 'PMKP v2.0 RSSM ',
            '_content'   => view('auth/login', $contentData),
            '_login_css' => view('_layout/_login_css'),
            '_login_js'  => view('_layout/_login_js'),
        ];

        return view('_layout/login_template', $data);
    }

    public function refresh_captcha()
    {
        $captcha = $this->captcha->generate([
            'min' => 1,
            'max' => 20
        ]);

        $this->session->set([
            'captcha_word'  => $captcha['word'],
            'captcha_html' => $captcha['html']
        ]);

        return $this->response->setJSON([
            'captcha_html' => $captcha['html']
        ]);
    }

    public function process()
    {
        $identity = trim($this->request->getPost('identity'));
        $password = $this->request->getPost('password');
        $captcha  = strtoupper($this->request->getPost('captcha'));

        // CAPTCHA
        if (!Captcha::validate($captcha, session()->get('captcha_word'))) {
            // Regenerate CAPTCHA
            $newCaptcha = $this->captcha->generate(['min' => 1, 'max' => 20]);
            $this->session->set([
                'captcha_word'  => $newCaptcha['word'],
                'captcha_html' => $newCaptcha['html']
            ]);
            return redirect()->back()->with('error', 'Jawaban perhitungan salah');
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

    // private function loginAplikasi(string $email, string $password)
    // {
    //     $user = $this->sessionApps->checkLogin([
    //         'user_profile.profile_email'    => $email,
    //         'user_profile.profile_password' => md5($password),
    //         'user_profile.profile_record_status' => 'A'
    //     ]);

    //     if (!$user) {
    //         return redirect()->back()->with('error', 'Email atau password salah');
    //     }

    //     $roleMap = [
    //         'Kendali Mutu dan Tim Pokja' => 'KENDALI_MUTU',
    //         'Komite' => 'KOMITE',
    //         'Administrator' => 'ADMINISTRATOR'
    //     ];
    //     session()->set([
    //         'logged_in'       => true,
    //         'login_source'    => 'APP',

    //         'profile_id'      => $user->profile_id,
    //         'nama_lengkap'    => $user->profile_fullname,
    //         'profile_email'   => $user->profile_email,

    //         'department_id'   => $user->department_id,
    //         'department_name' => $user->department_name,
    //         'user_role'            => $user->group_name,

    //         'login_time'      => date('Y-m-d H:i:s'),
    //     ]);

    //     log_message('error', 'DEBUG LOGIN:APP - session set, redirecting to dashboard');
    //     return redirect()->to('dashboard');
    // }

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

        // ✅ Mapping role dari database ke sistem menu
        $roleMap = [
            'Kendali Mutu dan Tim Pokja' => 'KENDALI_MUTU',
            'Komite'                    => 'KOMITE',
            'Administrator'             => 'ADMINISTRATOR'
        ];

        // ✅ Tentukan role final
        $userRole = $roleMap[$user->hak_akses] ?? 'APP';

        // ✅ Set session (SATU KALI SAJA)
        session()->set([
            'logged_in'       => true,
            'login_source'    => 'APP',

            'profile_id'      => $user->profile_id,
            'nama_lengkap'    => $user->profile_fullname,
            'profile_email'   => $user->profile_email,
            'profile_picture' => $user->profile_photo ?: null,

            'department_id'   => $user->department_id,
            'department_name' => $user->lokasi,

            // ✅ INI YANG DIPAKAI MENU
            'user_role'       => $userRole,

            // (opsional kalau mau simpan asli)
            'role_asli'       => $user->hak_akses,

            'login_time'      => date('Y-m-d H:i:s'),
        ]);

        log_message('error', 'DEBUG LOGIN:APP - role: ' . $userRole);

        return redirect()->to('/siimut/dashboard');
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

    public function showRegister()
    {
        $email = session('register_email');
        $name = session('register_name');

        if (!$email) {
            return redirect()->to(site_url('auth'));
        }

        $data = [
            'email' => $email,
            'name' => $name,
        ];

        $contentData = [
            'email' => $email,
            'name' => $name,
        ];

        $data = [
            'login_title' => 'Registrasi - PMKP v2.0 RSSM',
            '_content'   => view('auth/register', $contentData),
            '_login_css' => view('_layout/_login_css'),
            '_login_js'  => view('_layout/_login_js'),
        ];

        return view('_layout/login_template', $data);
    }

    public function processRegister()
    {
        log_message('error', 'GOOGLE REGISTER: Session register_picture = ' . (session('register_picture') ?: 'NULL'));

        $email = $this->request->getPost('profile_email');
        $fullname = $this->request->getPost('profile_fullname');
        $gender = $this->request->getPost('profile_gender');
        $birthPlace = $this->request->getPost('profile_birth_place');
        $dob = $this->request->getPost('profile_dob');
        $handphone1 = $this->request->getPost('profile_handphone1');
        $handphone2 = $this->request->getPost('profile_handphone2');

        if (!$email || !$fullname) {
            return redirect()->back()->with('error', 'Nama dan email wajib diisi');
        }

        $db = db_connect();

        $existing = $db->table('user_profile')
            ->where('profile_email', $email)
            ->where('profile_record_status', 'A')
            ->get()
            ->getRow();

        if ($existing) {
            return redirect()->back()->with('error', 'Email sudah terdaftar');
        }

        $data = [
            'profile_fullname'      => $fullname,
            'profile_email'         => $email,
            'profile_gender'        => $gender ?: null,
            'profile_birth_place'   => $birthPlace ?: null,
            'profile_dob'           => $dob ?: null,
            'profile_password'      => $dob ? md5($dob) : md5('123456'),
            'profile_handphone1'    => $handphone1 ?: null,
            'profile_handphone2'    => $handphone2 ?: null,
            'profile_photo'         => session('register_picture') ?: null,
            'profile_record_status' => 'A',
            'profile_insert_by'     => 'GOOGLE',
            'profile_insert_date'   => date('Y-m-d H:i:s'),
            'profile_online_status' => 0,
            'profile_disable'       => 0,
        ];

        log_message('error', 'GOOGLE REGISTER: DOB = ' . ($dob ?: 'NULL') . ', Password = ' . ($dob ? md5($dob) : md5('123456')));

        $insert = $db->table('user_profile')->insert($data);

        if (!$insert) {
            return redirect()->back()->with('error', 'Gagal membuat akun');
        }

        $profileId = $db->insertID();

        log_message('error', 'GOOGLE REGISTER: User registered - ' . $email . ' profile_id: ' . $profileId);

        $photoUrl = session('register_picture') ?: null;

        log_message('error', 'GOOGLE REGISTER: Photo URL from session = ' . ($photoUrl ?: 'NULL'));
        log_message('error', 'GOOGLE REGISTER: All session data = ' . json_encode(session()->get()));

        session()->remove(['register_email', 'register_name', 'register_picture']);

        log_message('error', 'GOOGLE REGISTER: Photo URL = ' . ($photoUrl ?: 'NULL'));

        session()->set([
            'logged_in'       => true,
            'login_source'    => 'APP',
            'auth_method'     => 'GOOGLE',
            'profile_id'      => $profileId,
            'nama_lengkap'    => $fullname,
            'profile_email'   => $email,
            'profile_picture' => $photoUrl,
            'user_role'       => 'APP',
            'login_time'      => date('Y-m-d H:i:s'),
        ]);

        log_message('error', 'GOOGLE REGISTER: Session profile_picture = ' . (session('profile_picture') ?: 'NULL'));

        return redirect()->to('/siimut/dashboard');
    }

    public function cek_session()
    {
        $session = session();
        
        if (!$session->get('logged_in')) {
            return $this->response->setJSON([
                'logged_in' => false
            ])->setStatusCode(401);
        }

        return $this->response->setJSON([
            'logged_in'    => true,
            'login_source' => $session->get('login_source')
        ]);
    }

    /**
     * Redirect to Google OAuth
     */
    public function googleLogin()
    {
        $googleLogin = new GoogleLogin();
        $authUrl = $googleLogin->getAuthUrl();
        return redirect()->to($authUrl);
    }

    /**
     * Google OAuth callback
     */
    public function googleCallback()
    {
        $code = $this->request->getGet('code');
        
        if (!$code) {
            log_message('error', 'GOOGLE CALLBACK: No code received');
            return redirect()->to(site_url('auth'))->with('error', 'Login Google gagal');
        }

        try {
            log_message('error', 'GOOGLE CALLBACK: Code received, fetching token');
            $googleLogin = new GoogleLogin();
            $token = $googleLogin->getAccessToken($code);
            
            if (isset($token['error'])) {
                log_message('error', 'GOOGLE CALLBACK: Token error - ' . $token['error']);
                return redirect()->to(site_url('auth'))->with('error', 'Gagal mendapatkan akses Google');
            }

            log_message('error', 'GOOGLE CALLBACK: Token received, fetching user info');
            $userInfo = $googleLogin->getUserInfo($token);
            
            $email = $userInfo->getEmail();
            $name = $userInfo->getName();
            $picture = $userInfo->getPicture();

            log_message('error', 'GOOGLE CALLBACK: User info - Email: ' . $email . ', Name: ' . $name);

            // Cek apakah email terdaftar di database aplikasi
            $user = $this->sessionApps->select('*, user_group.group_name as hak_akses, master_institution_department.department_name as lokasi')
                ->join('master_institution_department', 'master_institution_department.department_id = user_profile.profile_department_id', 'left')
                ->join('user_group', 'user_group.group_id = user_profile.profile_group_id', 'left')
                ->where('user_profile.profile_email', $email)
                ->where('user_profile.profile_record_status', 'A')
                ->first();

            if ($user) {
                // Login berhasil - user terdaftar
                $roleMap = [
                    'Kendali Mutu dan Tim Pokja' => 'KENDALI_MUTU',
                    'Komite' => 'KOMITE',
                    'Administrator' => 'ADMINISTRATOR'
                ];

                $userRole = $roleMap[$user->hak_akses] ?? 'APP';

                session()->set([
                    'logged_in'       => true,
                    'login_source'    => 'APP',
                    'auth_method'     => 'GOOGLE',

                    'profile_id'      => $user->profile_id,
                    'nama_lengkap'    => $user->profile_fullname,
                    'profile_email'   => $user->profile_email,
                    'profile_picture' => $picture,

                    'department_id'   => $user->department_id,
                    'department_name' => $user->lokasi,
                    'user_role'       => $userRole,
                    'role_asli'       => $user->hak_akses,

                    'login_time'      => date('Y-m-d H:i:s'),
                ]);

                log_message('error', 'GOOGLE CALLBACK: Login success - ' . $email . ' role: ' . $userRole);

                return redirect()->to('/siimut/dashboard');
            } else {
                // Email tidak terdaftar - redirect ke halaman register
                session()->set([
                    'register_email'    => $email,
                    'register_name'     => $name,
                    'register_picture'  => $picture,
                ]);

                log_message('error', 'GOOGLE CALLBACK: Email not registered - ' . $email . ' - redirect to register');

                return redirect()->to(site_url('auth/register'));
            }

        } catch (\Exception $e) {
            log_message('error', 'Google Login Error: ' . $e->getMessage());
            return redirect()->to(site_url('auth'))->with('error', 'Terjadi kesalahan saat login Google');
        }
    }
}
