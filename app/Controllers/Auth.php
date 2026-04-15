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
        helper(['url', 'cookie']);
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
        $remember = $this->request->getPost('remember') === 'on' || $this->request->getPost('remember') === '1' || $this->request->getPost('remember') === 'true';

        // CAPTCHA
        if (!Captcha::validate($captcha, session()->get('captcha_word'))) {
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

        if (filter_var($identity, FILTER_VALIDATE_EMAIL)) {
            return $this->loginAplikasi($identity, $password, $remember);
        } else {
            return $this->loginHris($identity, $password, $remember);
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

    private function loginAplikasi(string $email, string $password, bool $remember = false)
    {
        $user = $this->sessionApps->checkLogin([
            'user_profile.profile_email'    => $email,
            'user_profile.profile_password' => md5($password),
            'user_profile.profile_record_status' => 'A'
        ]);

        if (!$user) {
            return redirect()->back()->with('error', 'Email atau password salah');
        }

        if (!empty($user->profile_verification_token) && $user->profile_is_verified == 0) {
            session()->set([
                'resend_verification_email' => $email,
                'resend_verification_name' => $user->profile_fullname,
            ]);
            return redirect()->back()->with('error', 'Akun Anda belum terverifikasi. Silakan verifikasi email terlebih dahulu. <a href="' . site_url('auth/resend_verification') . '" class="alert-link">Kirim Ulang Link Verifikasi</a>');
        }

        $roleMap = [
            'Kendali Mutu dan Tim Pokja' => 'KENDALI_MUTU',
            'Komite'                    => 'KOMITE',
            'Administrator'             => 'ADMINISTRATOR'
        ];

        $userRole = $roleMap[$user->hak_akses] ?? 'APP';

        $db = db_connect();
        $db->table('user_profile')
            ->where('profile_id', $user->profile_id)
            ->update([
                'profile_online_status' => 1,
                'profile_last_login' => date('Y-m-d H:i:s'),
            ]);

        if ($remember) {
            $rememberData = base64_encode($email . '|' . md5($password));
            set_cookie([
                'name'   => 'remember_credential',
                'value'  => $rememberData,
                'expire' => 86400 * 30,
                'path'   => '/',
                'secure' => false,
                'httponly' => false,
            ]);
        } else {
            delete_cookie('remember_credential');
        }

        session()->set([
            'logged_in'       => true,
            'login_source'    => 'APP',
            'last_activity'   => time(),

            'profile_id'      => $user->profile_id,
            'nama_lengkap'    => $user->profile_fullname,
            'profile_email'   => $user->profile_email,
            'profile_picture' => $user->profile_photo ?? null,

            'department_id'   => $user->department_id,
            'department_name' => $user->lokasi,

            'user_role'       => $userRole,

            'role_asli'       => $user->hak_akses,

            'login_time'      => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/siimut/dashboard');
    }

    private function loginHris(string $nip, string $password, bool $remember = false)
    {
        $hrisModel = new \App\Models\HrisUserModel();
        $user = $hrisModel->checkLoginHris($nip, $password);

        if (!$user) {
            return redirect()->back()->with('error', 'NIP atau password salah');
        }

        $role = $this->detectRoleByHrisId($user->id);

        $db = db_connect();
        $db->table('user_profile')
            ->where('profile_id', $user->profile_id)
            ->update([
                'profile_online_status' => 1,
                'profile_last_login' => date('Y-m-d H:i:s'),
            ]);

        if ($remember) {
            $rememberData = base64_encode($nip . '|' . md5($password) . '|HRIS');
            set_cookie([
                'name'   => 'remember_credential',
                'value'  => $rememberData,
                'expire' => 86400 * 30,
                'path'   => '/',
                'secure' => false,
                'httponly' => false,
            ]);
        } else {
            delete_cookie('remember_credential');
        }

        session()->set([
            'logged_in'       => true,
            'login_source'    => 'HRIS',
            'last_activity'   => time(),
            'hris_user_id'    => $user->id,
            'hris_nip'        => $user->nip,
            'hris_full_name'  => $user->nama_lengkap ?? $user->nama,
            'nama_lengkap'    => $user->nama_lengkap ?? $user->nama,
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
        $profileId = session('profile_id');

        if ($profileId) {
            $db = db_connect();
            $db->table('user_profile')
                ->where('profile_id', $profileId)
                ->update([
                    'profile_online_status' => 0,
                    'profile_remember_token' => null,
                ]);
        }

        delete_cookie('remember_credential');
        session()->destroy();

        $this->response
            ->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->setHeader('Pragma', 'no-cache')
            ->setHeader('Expires', '0');

        return redirect()->to(site_url('auth'));
    }

    public function clear_register_session()
    {
        session()->remove([
            'register_email',
            'register_name',
            'register_picture',
            'registered_email',
            'registered_name',
            'requires_verification'
        ]);

        return $this->response->setJSON(['status' => 'ok']);
    }

    public function ping()
    {
        header('Content-Type: application/json; charset=utf-8');
        
        $loggedIn = session('logged_in');
        $lastActivity = session('last_activity');
        $currentTime = time();
        $timeDiff = $lastActivity ? ($currentTime - $lastActivity) : null;

        if ($loggedIn) {
            $timeout = 1800; // 30 menit

            if ($lastActivity && $timeDiff > $timeout) {
                session()->destroy();
                http_response_code(401);
                echo json_encode([
                    'status' => 'timeout',
                    'message' => 'Session expired'
                ]);
                return;
            }

            session()->set('last_activity', $currentTime);
            echo json_encode(['status' => 'ok']);
            return;
        }

        http_response_code(401);
        echo json_encode([
            'status' => 'unauthorized'
        ]);
    }

    public function resend_verification()
    {
        $email = session('resend_verification_email');
        $name = session('resend_verification_name');

        if (!$email) {
            // Jika tidak ada session, coba ambil dari parameter GET
            $email = $this->request->getGet('email');
            $name = $this->request->getGet('name');
        }

        if (!$email) {
            return redirect()->to(site_url('auth'))->with('error', 'Sesi habis. Silakan login kembali.');
        }

        $db = db_connect();

        // Ambil data user dari database
        $user = $db->table('user_profile')
            ->where('profile_email', $email)
            ->where('profile_record_status', 'A')
            ->get()
            ->getRow();

        if (!$user) {
            return redirect()->to(site_url('auth'))->with('error', 'Akun tidak ditemukan.');
        }

        if ($user->profile_is_verified == 1) {
            return redirect()->to(site_url('auth'))->with('success', 'Email sudah terverifikasi. Silakan login.');
        }

        // Generate token baru
        $verificationToken = bin2hex(random_bytes(32));

        // Update token di database
        $db->table('user_profile')
            ->where('profile_id', $user->profile_id)
            ->update([
                'profile_verification_token' => $verificationToken,
                'profile_verification_sent_at' => date('Y-m-d H:i:s'),
            ]);

        // Kirim email verifikasi
        $this->sendVerificationEmail($email, $user->profile_fullname, $verificationToken);

        // Clear session
        session()->remove(['resend_verification_email', 'resend_verification_name']);

        return redirect()->to(site_url('auth'))->with('success', 'Link verifikasi telah dikirim ulang ke email Anda. Silakan cek inbox atau folder Spam.');
    }

    public function showRegister()
    {
        $email = session('register_email');
        $name = session('register_name');

        if (!$email) {
            return redirect()->to(site_url('auth'));
        }

        // Redirect to login page with modal
        return redirect()->to(site_url('auth?show_register=1'));
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

        // Generate email verification token
        $verificationToken = bin2hex(random_bytes(32));

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
            // Add email verification fields
            'profile_verification_token' => $verificationToken,
            'profile_is_verified'        => 0, // 0 = not verified, 1 = verified
            'profile_verification_sent_at' => date('Y-m-d H:i:s'),
        ];

        log_message('error', 'GOOGLE REGISTER: DOB = ' . ($dob ?: 'NULL') . ', Password = ' . ($dob ? md5($dob) : md5('123456')));

        $insert = $db->table('user_profile')->insert($data);

        if (!$insert) {
            return redirect()->back()->with('error', 'Gagal membuat akun');
        }

        $profileId = $db->insertID();

        log_message('error', 'GOOGLE REGISTER: User registered - ' . $email . ' profile_id: ' . $profileId);

        // Send verification email
        $this->sendVerificationEmail($email, $fullname, $verificationToken);

        $photoUrl = session('register_picture') ?: null;

        log_message('error', 'GOOGLE REGISTER: Photo URL from session = ' . ($photoUrl ?: 'NULL'));
        log_message('error', 'GOOGLE REGISTER: All session data = ' . json_encode(session()->get()));

        session()->remove(['register_email', 'register_name', 'register_picture']);

        log_message('error', 'GOOGLE REGISTER: Photo URL = ' . ($photoUrl ?: 'NULL'));

        // Don't auto-login - require email verification first
        session()->set([
            'registered_email'    => $email,
            'registered_name'     => $fullname,
            'requires_verification' => true,
            'login_time'          => date('Y-m-d H:i:s'),
        ]);

        log_message('error', 'GOOGLE REGISTER: Registration complete, awaiting email verification for ' . $email);

        return redirect()->to(site_url('auth/verify_email_notice'));
    }

    /**
     * Show email verification notice page
     */
    public function verify_email_notice()
    {
        $email = session('registered_email');
        $name = session('registered_name');

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
            'login_title' => 'Verifikasi Email - PMKP v2.0 RSSM',
            '_content'   => view('auth/verify_email_notice', $contentData),
            '_login_css' => view('_layout/_login_css'),
            '_login_js'  => view('_layout/_login_js'),
        ];

        return view('_layout/login_template', $data);
    }

    /**
     * Send verification email
     */
    private function sendVerificationEmail($emailTo, $fullname, $token)
    {
        // Load email library
        $email = \Config\Services::email();

        // Configure email (you may need to adjust these settings)
        $email->setFrom('noreply@pmkpv4.example.com', 'PMKP v4');
        $email->setTo($emailTo);
        $email->setSubject('Verifikasi Akun PMKP v4');

        $verificationUrl = site_url('auth/verify_email?token=' . $token . '&email=' . urlencode($emailTo));

        // $message = "
        // <html>
        // <head>
        //     <title>Verifikasi Akun PMKP v4</title>
        // </head>
        // <body>
        //     <h2>Halo " . $fullname . ",</h2>
        //     <p>Terima kasih telah mendaftar di PMKP v4. Untuk melengkapi pendaftaran Anda, silakan verifikasi alamat email Anda dengan mengikuti link di bawah ini:</p>
        //     <p><a href='" . $verificationUrl . "'>Verifikasi Email Saya</a></p>
        //     <p>Jika Anda tidak mendaftar di PMKP v4, silakan abaikan email ini.</p>
        //     <p>Link verifikasi akan kadaluarsa dalam 24 jam.</p>
        //     <hr>
        //     <p>Email ini dikirim secara otomatis, jangan balas ke email ini.</p>
        // </body>
        // </html>";
        $message = '
        <div style="font-family:Arial, sans-serif; background:#f4f6f9; padding:20px;">
            
            <div style="max-width:600px; margin:auto; background:#ffffff; padding:25px; border-radius:8px;">

                <h2 style="text-align:center; color:#0d6efd;">
                    Aktivasi Akun PMKP v4
                </h2>

                <p>Halo <b>' . $fullname . '</b>,</p>

                <p>
                    Terima kasih telah melakukan pendaftaran akun di 
                    <b>Sistem PMKP v4</b>.
                </p>

                <p>
                    Silakan klik tombol di bawah ini untuk mengaktifkan akun Anda:
                </p>

                <div style="text-align:center; margin:25px 0;">
                    <a href="' . $verificationUrl . '"
                    style="background:#0d6efd; color:#ffffff; padding:12px 20px; text-decoration:none; border-radius:6px; font-weight:bold;">
                    Aktivasi Akun
                    </a>
                </div>

                <p>
                    Link ini berlaku selama <b>24 jam</b>.
                </p>

                <p>
                    Jika Anda tidak merasa melakukan pendaftaran, silakan abaikan email ini.
                </p>

                <hr>

                <p style="text-align:center; font-size:12px; color:#888;">
                    Email ini dikirim otomatis oleh Sistem PMKP RS Dr. Soedono Madiun<br>
                    Mohon tidak membalas email ini
                </p>

            </div>
        </div>
        ';

        $email->setMessage($message);
        $email->setMailType('html');

        if (!$email->send()) {
            log_message('error', 'EMAIL VERIFICATION: Failed to send verification email to ' . $emailTo . '. Error: ' . $email->printDebugger(['headers']));
        } else {
            log_message('error', 'EMAIL VERIFICATION: Verification email sent successfully to ' . $emailTo);
        }
    }

    /**
     * Show email verification page
     */
    // public function verify_email()
    // {
    //     $token = $this->request->getGet('token');
    //     $email = $this->request->getGet('email');

    //     if (!$token || !$email) {
    //         return redirect()->to(site_url('auth'))->with('error', 'Link verifikasi tidak valid');
    //     }

    //     $db = db_connect();

    //     $user = $db->table('user_profile')
    //         ->where('profile_email', $email)
    //         ->where('profile_verification_token', $token)
    //         ->where('profile_record_status', 'A')
    //         ->get()
    //         ->getRow();

    //     if (!$user) {
    //         return redirect()->to(site_url('auth'))->with('error', 'Link verifikasi tidak valid atau telah kadaluarsa');
    //     }

    //     // Check if token is expired (24 hours)
    //     $sentAt = strtotime($user->profile_verification_sent_at);
    //     $expiresAt = $sentAt + (24 * 60 * 60); // 24 hours

    //     if (time() > $expiresAt) {
    //         return redirect()->to(site_url('auth'))->with('error', 'Link verifikasi telah kadaluarsa. Silakan daftar kembali.');
    //     }

    //     // Mark email as verified
    //     $db->table('user_profile')
    //         ->where('profile_id', $user->profile_id)
    //         ->update([
    //             'profile_is_verified' => 1,
    //             'profile_verified_at' => date('Y-m-d H:i:s'),
    //             'profile_verification_token' => null, // Clear token after use
    //         ]);

    //     log_message('error', 'EMAIL VERIFICATION: Email verified successfully for ' . $email);

    //     // Auto-login after verification
    //     session()->set([
    //         'logged_in'       => true,
    //         'login_source'    => 'APP',
    //         'auth_method'     => 'GOOGLE',
    //         'profile_id'      => $user->profile_id,
    //         'nama_lengkap'    => $user->profile_fullname,
    //         'profile_email'   => $user->profile_email,
    //         'profile_picture' => $user->profile_photo ?: null,
    //         'department_id'   => $user->profile_department_id ?: null,
    //         'department_name' => '', // Would need to join to get this
    //         'user_role'       => 'APP',
    //         'login_time'      => date('Y-m-d H:i:s'),
    //     ]);

    //     // Clear registration session data
    //     session()->remove(['registered_email', 'registered_name', 'requires_verification']);

    //     return redirect()->to('/siimut/dashboard')->with('success', 'Email berhasil diverifikasi! Anda telah masuk ke sistem.');
    // }
    public function verify_email()
    {
        $token = $this->request->getGet('token');

        // ❗ Validasi token
        if (!$token) {
            return redirect()->to(site_url('auth'))
                ->with('error', 'Link verifikasi tidak valid');
        }

        $db = db_connect();

        // 🔍 Ambil user + role + department
        $user = $db->table('user_profile')
            ->select('
            user_profile.*,
            user_group.group_name as hak_akses,
            master_institution_department.department_name as lokasi
        ')
            ->join('user_group', 'user_group.group_id = user_profile.profile_group_id', 'left')
            ->join('master_institution_department', 'master_institution_department.department_id = user_profile.profile_department_id', 'left')
            ->where('profile_verification_token', $token)
            ->where('profile_record_status', 'A')
            ->get()
            ->getRow();

        // ❌ Token tidak valid
        if (!$user) {
            return redirect()->to(site_url('auth'))
                ->with('error', 'Token tidak valid atau sudah digunakan');
        }

        // ⚠️ Sudah diverifikasi
        if ($user->profile_is_verified == 1) {
            return redirect()->to(site_url('auth'))
                ->with('info', 'Email sudah diverifikasi sebelumnya');
        }

        // ⚠️ Token kosong / tidak ada waktu kirim
        if (!$user->profile_verification_sent_at) {
            return redirect()->to(site_url('auth'))
                ->with('error', 'Token tidak valid');
        }

        // ⏱️ Cek expired (24 jam)
        if (strtotime($user->profile_verification_sent_at) < strtotime('-24 hours')) {
            return redirect()->to(site_url('auth'))
                ->with('error', 'Link verifikasi telah kadaluarsa');
        }

        $db->table('user_profile')
            ->where('profile_id', $user->profile_id)
            ->update([
                'profile_is_verified'        => 1,
                'profile_verified_at'        => date('Y-m-d H:i:s'),
                'profile_verification_token' => null,
                'profile_online_status'      => 1,
                'profile_last_login'         => date('Y-m-d H:i:s'),
            ]);

        log_message('info', 'EMAIL VERIFIED: ' . $user->profile_email);

        // 🎭 Set session (pakai data DB)
        session()->set([
            'logged_in'       => true,
            'login_source'    => 'APP',
            'auth_method'     => 'APP',

            'profile_id'      => $user->profile_id,
            'nama_lengkap'    => $user->profile_fullname,
            'profile_email'   => $user->profile_email,
            'profile_picture' => $user->profile_photo ?: null,

            'department_id'   => $user->profile_department_id,
            'department_name' => $user->lokasi,

            'user_role'       => $user->group_code ?? 'APP',
            'role_asli'       => $user->hak_akses,

            'login_time'      => date('Y-m-d H:i:s'),
        ]);

        // 🧹 Bersihkan session registrasi
        session()->remove([
            'registered_email',
            'registered_name',
            'requires_verification'
        ]);

        return redirect()->to('/siimut/dashboard')
            ->with('success', 'Email berhasil diverifikasi! Anda telah masuk ke sistem.');
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
        log_message('error', 'GOOGLE CALLBACK: Method called');
        
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

            log_message('error', 'GOOGLE CALLBACK: User info - Email: ' . $email . ', Name: ' . $name . ', Verified: ' . ($userInfo->getVerifiedEmail() ? 'Yes' : 'No'));

            // Validasi apakah email telah diverifikasi oleh Google
            if (!$userInfo->getVerifiedEmail()) {
                log_message('error', 'GOOGLE CALLBACK: Email not verified by Google - ' . $email);
                return redirect()->to(site_url('auth'))->with('error', 'Email Google belum terverifikasi. Silakan verifikasi email terlebih dahulu.');
            }

            // Cek apakah email terdaftar di database aplikasi
            $user = $this->sessionApps->select('user_profile.*, user_group.group_name as hak_akses, master_institution_department.department_name as lokasi')
                ->join('master_institution_department', 'master_institution_department.department_id = user_profile.profile_department_id', 'left')
                ->join('user_group', 'user_group.group_id = user_profile.profile_group_id', 'left')
                ->where('user_profile.profile_email', $email)
                ->where('user_profile.profile_record_status', 'A')
                ->first();

            if ($user) {
                // Auto-verify untuk user Google OAuth (karena Google sudah memvalidasi email)
                // Cek apakah property profile_is_verified ada dan bernilai 0
                // Juga cek profile_insert_by untuk memastikan ini user Google (bisa 'GOOGLE', 'GOOGL', dll)
                $isVerified = isset($user->profile_is_verified) ? $user->profile_is_verified : 1; // Default to 1 (verified) if column doesn't exist

                // Juga fix profile_insert_by jika tidak standar
                $insertBy = isset($user->profile_insert_by) ? strtoupper($user->profile_insert_by) : '';
                $isGoogleUser = ($insertBy === 'GOOGLE' || $insertBy === 'GOOGL' || $insertBy === 'GOOG' || strpos($insertBy, 'GOOGLE') !== false);
                
                // Cek apakah ada data yang perlu diperbaiki
                $needsFix = false;
                $fixData = [];
                
                // Fix 1: User belum verified
                if ($isVerified == 0 && $isGoogleUser) {
                    $needsFix = true;
                    $fixData['profile_is_verified'] = 1;
                    $fixData['profile_verification_token'] = null;
                    $fixData['profile_verification_sent_at'] = null;
                    log_message('error', 'GOOGLE CALLBACK: Auto-verifying email - ' . $email);
                }
                // Fix 2: User sudah verified tapi masih punya token
                elseif ($isVerified == 1 && !empty($user->profile_verification_token)) {
                    $needsFix = true;
                    $fixData['profile_verification_token'] = null;
                    $fixData['profile_verification_sent_at'] = null;
                    log_message('error', 'GOOGLE CALLBACK: Clearing verification token for verified user - ' . $email);
                }
                
                // Fix 3: profile_insert_by tidak standar
                if ($isGoogleUser && $insertBy !== 'GOOGLE') {
                    $needsFix = true;
                    $fixData['profile_insert_by'] = 'GOOGLE';
                    log_message('error', 'GOOGLE CALLBACK: Fixing profile_insert_by from ' . $insertBy . ' to GOOGLE');
                }
                
                // Update database jika ada yang perlu difix
                if ($needsFix && !empty($fixData)) {
                    log_message('error', 'GOOGLE CALLBACK: Updating user profile_id=' . $user->profile_id . ' with data: ' . json_encode($fixData));
                    
                    try {
                        $this->sessionApps->where('user_profile.profile_id', $user->profile_id)
                            ->update($fixData);

                        // Update data user untuk session
                        $user->profile_is_verified = 1;
                        $user->profile_insert_by = 'GOOGLE';
                        $user->profile_verification_token = null;
                        
                        log_message('error', 'GOOGLE CALLBACK: Update successful');
                    } catch (\Exception $e) {
                        log_message('error', 'GOOGLE CALLBACK: Update failed - ' . $e->getMessage());
                        // Continue anyway, user is already verified
                    }
                } else {
                    log_message('error', 'GOOGLE CALLBACK: No fix needed, user data is already correct');
                }
                
                $db = db_connect();
                $db->table('user_profile')
                    ->where('profile_id', $user->profile_id)
                    ->update([
                        'profile_photo' => $picture,
                        'profile_online_status' => 1,
                        'profile_last_login' => date('Y-m-d H:i:s'),
                    ]);
                
                $user->profile_photo = $picture;

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
                    'last_activity'   => time(),

                    'profile_id'      => $user->profile_id,
                    'nama_lengkap'    => $user->profile_fullname,
                    'profile_email'   => $user->profile_email,
                    'profile_picture' => $picture ?: ($user->profile_photo ?? null),

                    'department_id'   => $user->department_id ?? null,
                    'department_name' => $user->lokasi ?? '',
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
            log_message('error', 'Google Login Error Trace: ' . $e->getTraceAsString());
            log_message('error', 'Google Login Error File: ' . $e->getFile() . ' Line: ' . $e->getLine());
            return redirect()->to(site_url('auth'))->with('error', 'Terjadi kesalahan saat login Google - ' . $e->getMessage());
        }
    }
}
