<?php

namespace App\Controllers;

use App\Libraries\Template;

class Home extends BaseController
{
    public function index()
    {
        $template = new Template();

        $data = [
            'title' => 'Dashboard',
            'contentTitle' => 'Selamat Datang di Dashboard'
        ];

        return $template->render('home/index', $data);
    }
}