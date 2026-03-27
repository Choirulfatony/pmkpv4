<?php

namespace App\Controllers;

use App\Models\MloadModuleIkp;
use App\Models\HrisUserModel;

class Ikprs extends AppController
{
    protected $ikpModel;

    public function __construct()
    {
        parent::__construct();
        $this->ikpModel = new MloadModuleIkp();
    }

    // ================= HALAMAN UTAMA =================
    public function index()
    {
        $this->disableCache();

        return $this->render('dashboard/index', [
            'judul'    => 'IKPRS',
            'icon'     => '<i class="bi bi-clipboard-check"></i>',
            '_content' => view('ikprs/container'),
        ]);
    }

    // ================= PARTIAL LOGIN / IKP =================
    public function dataLogin()
    {
        $data = [];

        // 🔥 CEK LOGIN HRIS (BUKAN LOGIN APLIKASI)
        $data['sudah_login'] = session()->get('ikprs_logged_in') === true;

        if ($data['sudah_login']) {

            $data['nip']          = session('nip');
            $data['nama_lengkap'] = session('nama_lengkap');

            // STUB DULU (BELUM PAKAI INBOX)
            $data['list']           = [];
            $data['total_notif']    = 0;
            $data['total_sent']     = 0;
            $data['total_draft']    = 0;
            $data['total_approved'] = 0;
        } else {

            // BELUM LOGIN HRIS → TAMPIL LOGIN FORM
            $data['list']           = [];
            $data['total_notif']    = 0;
            $data['total_sent']     = 0;
            $data['total_draft']    = 0;
            $data['total_approved'] = 0;
        }

        return view('ikprs/partial_login', $data);
    }

    // ================= LOGIN HRIS (AJAX) =================
    // public function loginProcess()
    // {
    //     $nip      = trim($this->request->getPost('nip'));
    //     $password = $this->request->getPost('password');

    //     if (!$nip || !$password) {
    //         return $this->response->setJSON([
    //             'status'  => 'error',
    //             'message' => 'NIP dan password wajib diisi'
    //         ]);
    //     }

    //     $hrisModel = new HrisUserModel();

    //     $user = $hrisModel->checkLoginHris($nip, $password);

    //     if (!$user) {
    //         return $this->response->setJSON([
    //             'status'  => 'error',
    //             'message' => 'NIP atau password salah'
    //         ]);
    //     }

    //     // 🔥 SET SESSION KHUSUS HRIS
    //     session()->set([
    //         'ikprs_logged_in'  => true,
    //         'hris_login_time' => date('Y-m-d H:i:s'),

    //         // ⬇️ HRIS ONLY
    //         'hris_nip'        => $user->nip,
    //         'hris_user_id'    => $user->id,
    //         'hris_full_name'  => $user->nama_lengkap ?? $user->nama,
    //         'hris_department' => $user->department_name ?? '',
    //     ]);

    //     return $this->response->setJSON([
    //         'status' => 'success'
    //     ]);
    // }

    public function process()
    {
        $identity  = trim($this->request->getPost('identity'));
        $password  = $this->request->getPost('password');
        $captcha   = strtoupper($this->request->getPost('captcha'));

        if (!$identity || !$password) {
            return redirect()->back()->with('error', 'Identitas dan password wajib diisi');
        }

        // 🔐 Validasi captcha
        if ($captcha !== session()->get('captcha_word')) {
            return redirect()->back()->with('error', 'Captcha salah');
        }

        /* =====================================================
       🔎 AUTO DETECT LOGIN TYPE
    ===================================================== */

        if (filter_var($identity, FILTER_VALIDATE_EMAIL)) {

            /* ==========================
           LOGIN APLIKASI (DB 1)
        ========================== */

            $password_md5 = md5($password);

            $user = $this->sessionApps->checkLogin([
                'user_profile.profile_email'    => $identity,
                'user_profile.profile_password' => $password_md5,
                'user_profile.profile_record_status' => 'A'
            ]);

            if (!$user) {
                return redirect()->back()->with('error', 'Email / Password salah');
            }

            session()->set([
                'logged_in'       => true,
                'login_type'      => 'app',

                'profile_id'      => $user->profile_id,
                'profile_email'   => $user->profile_email,
                'nama_lengkap'    => $user->profile_fullname,
                'department_id'   => $user->department_id,
                'department_name' => $user->department_name,
                'role'            => $user->group_name,

                'login_time'      => date('Y-m-d H:i:s'),
            ]);

            return redirect()->to('dashboard');
        } else {

            /* ==========================
           LOGIN HRIS (DB 2)
        ========================== */

            $hrisModel = new \App\Models\HrisUserModel();
            $user = $hrisModel->checkLoginHris($identity, $password);

            if (!$user) {
                return redirect()->back()->with('error', 'NIP / Password salah');
            }

            session()->set([
                'ikprs_logged_in' => true,
                'login_type'      => 'hris',

                'hris_nip'        => $user->nip,
                'hris_user_id'    => $user->id,
                'hris_full_name'  => $user->nama_lengkap ?? $user->nama,
                'hris_department' => $user->department_name ?? '',

                'hris_login_time' => date('Y-m-d H:i:s'),
            ]);

            return redirect()->to('ikprs');
        }
    }


    // ================= LOGOUT HRIS =================
    public function logoutHris()
    {
        // 🔥 HAPUS SESSION HRIS SAJA
        session()->remove([
            'ikprs_logged_in',
            'nip',
            'full_name',
            'department',
            'hris_user_id'
        ]);

        return $this->response->setJSON([
            'status' => 'success'
        ]);
    }

    // app/Controllers/Ikprs.php
    public function formAddIkp()
    {
        // opsional: cek login IKPRS
        if (! session()->get('ikprs_logged_in')) {
            return view('ikprs/partial_login');
        }

        return view('ikprs/_form_add_ikp');
    }
}
