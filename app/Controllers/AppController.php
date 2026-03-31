<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class AppController extends Controller
{
    protected $session;

    public function __construct()
    {
        $this->session = session();
    }

    protected function disableCache()
    {
        $this->response
            ->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->setHeader('Cache-Control', 'post-check=0, pre-check=0', false)
            ->setHeader('Pragma', 'no-cache')
            ->setHeader('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
    }



    //     protected function render(string $view, array $data = [])
    //     {
    //         $this->disableCache(); // ⬅️ WAJIB

    //         $data = array_merge([
    //             '_meta'          => view('_layout/_meta'),
    //             '_css'           => view('_layout/_css'),
    //             '_header'        => view('_layout/_header'),
    //             '_sidebar'       => view('_layout/_sidebar'),
    //             '_footer'        => view('_layout/_footer'),
    //             '_js'            => view('_layout/_js'),
    //             '_headerContent' => view('_layout/_headerContent'),
    //             '_content'       => view($view),
    //         ], $data);

    //         return view('_layout/_template', $data);
    //     }


    // protected function render(string $view, array $data = [])
    // {
    //     $this->disableCache(); // ⬅️ WAJIB

    //     // 1️⃣ SET DEFAULT DATA DULU (INI KUNCI)
    //     $data['judul'] = $data['judul'] ?? 'Dashboard';
    //     $data['icon']  = $data['icon']  ?? '<i class="bi bi-speedometer2"></i>';

    //     // 2️⃣ BARU RENDER PARTIAL YANG BUTUH DATA
    //     $data = array_merge([
    //         '_meta'          => view('_layout/_meta'),
    //         '_css'           => view('_layout/_css'),
    //         '_header'        => view('_layout/_header'),
    //         '_sidebar'       => view('_layout/_sidebar'),
    //         '_headerContent' => view('_layout/_headerContent', $data), // ⬅️ PENTING
    //         '_footer'        => view('_layout/_footer'),
    //         '_js'            => view('_layout/_js'),
    //         '_content'       => view($view),
    //     ], $data);

    //     return view('_layout/_template', $data);
    // }

    protected function render(string $view, array $data = [])
    {
        $this->disableCache();

        $data['judul'] = $data['judul'] ?? 'Dashboard';
        $data['icon']  = $data['icon']  ?? '<i class="bi bi-speedometer2"></i>';

        // ✅ TAMBAHKAN MENU DI SINI
        $menuModel = new \App\Models\SiimutMenuModel();
        $role = session()->get('user_role');
        $data['menus'] = $menuModel->getMenuByRole($role);

        $data = array_merge([
            '_meta'          => view('_layout/_meta'),
            '_css'           => view('_layout/_css'),
            '_header'        => view('_layout/_header'),

            // ✅ FIX DI SINI
            '_sidebar'       => view('_layout/_sidebar', $data),

            '_headerContent' => view('_layout/_headerContent', $data),
            '_footer'        => view('_layout/_footer'),
            '_js'            => view('_layout/_js'),
            '_content'       => view($view, $data),
        ], $data);

        return view('_layout/_template', $data);
    }

    // protected function render(string $view, array $data = [])
    // {
    //     // 🔐 pastikan login
    //     $user_id = session('hris_user_id');

    //     if ($user_id) {
    //         $ikpModel = new \App\Models\IkpInsidenModel();

    //         // 🔥 HITUNG BADGE GLOBAL DI SINI
    //         $data['total_draft'] = $ikpModel->countDraftByUser($user_id);
    //         // kalau belum ada inbox, set 0 dulu
    //         $data['total_inbox'] = $data['total_inbox'] ?? 0;
    //     } else {
    //         $data['total_draft'] = 0;
    //         $data['total_inbox'] = 0;
    //     }

    //     echo view('layout/header', $data);
    //     echo view('layout/sidebar', $data); // 🔑 INI KUNCI
    //     echo view($view, $data);
    //     echo view('layout/footer', $data);
    // }
}
