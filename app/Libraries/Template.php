<?php

namespace App\Libraries;

use CodeIgniter\View\View;

class Template
{
    protected $view;

    public function __construct()
    {
        $this->view = service('renderer'); // Service bawaan CI4 untuk render view
    }

    public function render(string $template, array $data = [])
    {
        // Partial Views
        $data['_meta']          = $this->view->setData($data)->render('_layout/_meta');
        $data['_css']           = $this->view->setData($data)->render('_layout/_css');
        $data['_js']            = $this->view->setData($data)->render('_layout/_js');
        $data['_nav']           = $this->view->setData($data)->render('_layout/_nav');
        $data['_header']        = $this->view->setData($data)->render('_layout/_header');
        $data['_sidebar']       = $this->view->setData($data)->render('_layout/_sidebar');
        $data['_headerContent'] = $this->view->setData($data)->render('_layout/_headerContent');
        $data['_content']       = $this->view->setData($data)->render($template);
        $data['_footer']        = $this->view->setData($data)->render('_layout/_footer');

        // Main Template
        return $this->view->setData($data)->render('_layout/_template');
    }
}
