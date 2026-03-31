<?php

namespace App\Controllers;

use App\Models\SiimutMenuModel;

class Dashboard extends AppController
{
    public function index()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/auth');
        }

        $this->disableCache();

        // ✅ ambil role user
        $role = session()->get('user_role');
        // contoh: APP / KOMITE / KENDALI_MUTU / ADMINISTRATOR


        // ✅ load model
        $menuModel = new SiimutMenuModel();

        // ✅ ambil menu berdasarkan role
        $menus = $menuModel->getMenuByRole($role);

        // 🔥 DEBUG DI SINI
        // dd($menus, $role);

        return $this->render('dashboard/index', [
            'judul'    => 'Dashboard SIIMUT',
            'icon'     => '<i class="bi bi-speedometer"></i>',
            '_content' => view('siimut/dashboard_home'),

            // ✅ kirim ke view (WAJIB)
            'menus'    => $menus
        ]);
    }
}
