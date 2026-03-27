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
    // public function process()
    // {
    //     $profile_email = trim($this->request->getPost('profile_email'));
    //     $password_raw  = $this->request->getPost('password');
    //     $password_md5  = md5($password_raw);
    //     $captcha       = strtoupper($this->request->getPost('captcha'));
    //     $captcha_sess  = $this->session->get('captcha_word');

    //     // 🔍 LOG INPUT
    //     log_message('debug', 'LOGIN INPUT', [
    //         'email_post'    => $profile_email,
    //         'password_raw'  => $password_raw,
    //         'password_md5'  => $password_md5,
    //         'captcha_post'  => $captcha,
    //         'captcha_sess'  => $captcha_sess,
    //     ]);

    //     if ($captcha !== $captcha_sess) {
    //         log_message('debug', 'LOGIN GAGAL: CAPTCHA SALAH');
    //         return redirect()->to('auth')->with('error', 'Captcha salah');
    //     }

    //     $where = [
    //         'user_profile.profile_email'         => $profile_email,
    //         'user_profile.profile_password'      => $password_md5,
    //         'user_profile.profile_record_status' => 'A'
    //     ];

    //     log_message('debug', 'LOGIN WHERE', $where);

    //     $user = $this->sessionApps->checkLogin($where);

    //     // 🔍 LOG HASIL QUERY
    //     log_message('debug', 'LOGIN RESULT', [
    //         'user' => $user
    //     ]);

    //     if (!$user) {
    //         log_message('debug', 'LOGIN GAGAL: USER TIDAK DITEMUKAN');
    //         return redirect()->to('auth')->with('error', 'Email / Password salah');
    //     }

    //     $this->session->set([
    //         'profile_id'        => $user->profile_id,
    //         'department_id'     => $user->department_id,
    //         'nama_lengkap'      => $user->profile_fullname, // ⬅️ GANTI
    //         'department_name'   => $user->department_name,
    //         'profile_email'     => $user->profile_email,
    //         'role'              => $user->group_name,
    //         'logged_in'         => true,

    //         'login_time'        => date('Y-m-d H:i:s'),
    //     ]);

    //     log_message('debug', 'LOGIN SUKSES', [
    //         'profile_id' => $user->profile_id
    //     ]);

    //     return redirect()->to('dashboard');
    // }

    public function process()
    {
        $session = session();

        $profile_email = trim($this->request->getPost('profile_email'));
        $password_raw  = $this->request->getPost('password');
        $password_md5  = md5($password_raw);
        $captcha       = strtoupper($this->request->getPost('captcha'));
        $captcha_sess  = $session->get('captcha_word');

        // ❌ CAPTCHA SALAH
        if ($captcha !== $captcha_sess) {
            return redirect()->to('auth')->with('error', 'Captcha salah');
        }

        $where = [
            'user_profile.profile_email'         => $profile_email,
            'user_profile.profile_password'      => $password_md5,
            'user_profile.profile_record_status' => 'A'
        ];

        $user = $this->sessionApps->checkLogin($where);

        // ❌ USER TIDAK ADA
        if (!$user) {
            return redirect()->to('auth')->with('error', 'Email / Password salah');
        }

        // ✅ SET SESSION LOGIN UTAMA
        $session->set([
            // IDENTITAS
            'profile_id'        => $user->profile_id,
            'profile_email'     => $user->profile_email,
            'nama_lengkap'      => $user->profile_fullname,

            // DEPARTEMEN
            'department_id'   => $user->department_id,
            'department_name' => $user->department_name,

            // ROLE / AKSES
            'role'              => $user->group_name,

            // STATUS LOGIN
            'logged_in'         => true,

            // ⏰ WAKTU LOGIN PERTAMA
            'login_time'        => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('dashboard');
    }




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

    public function logout()
    {
        session()->regenerate(true); // 1️⃣ ganti session ID lama
        session()->destroy();        // 2️⃣ hancurkan session

        return redirect()->to(site_url('auth'));
    }
}
