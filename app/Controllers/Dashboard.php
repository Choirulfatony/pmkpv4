<?php

namespace App\Controllers;



class Dashboard extends AppController
{
    public function index()
    {
        if (! session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $this->disableCache();

        return $this->render('dashboard/index', [
            'judul'   => 'Dashboard Siimut',
            '_content' => view('dashboard/index'),
        ]);
    }
}
