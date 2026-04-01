<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    // public function before(RequestInterface $request, $arguments = null)
    // {
    //     $session = session();

    //     if (! $session->get('logged_in')) {
    //         return redirect()->to('/auth')->with('error', 'Silakan login dulu');
    //     }

    //     $timeout = 3600;  // 1 JAM (3600 detik)
    //     $last = $session->get('last_activity');

    //     if ($last && (time() - $last) > $timeout) {
    //         $session->destroy();
    //         return redirect()->to('/auth?timeout=1');
    //     }

    //     $session->set('last_activity', time());
    // }


    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        $uri = ltrim($request->getUri()->getPath(), '/');

        // ✅ Lewati /auth
        if ($uri === 'auth' || strpos($uri, 'auth/') === 0) {
            return;
        }

        // 🔥 JIKA AJAX → JANGAN SENTUH SESSION
        if ($request->isAJAX()) {

            if (! $session->get('logged_in')) {
                return service('response')
                    ->setStatusCode(401)
                    ->setJSON([
                        'status'  => 'unauthorized',
                        'message' => 'Session habis, silakan login ulang'
                    ]);
            }

            return;
        }

        // ===== NON-AJAX REQUEST =====

        if (! $session->get('logged_in')) {
            return redirect()->to('/auth')->with('error', 'Silakan login dulu');
        }

        $timeout = 3600;
        $last = $session->get('last_activity');

        if ($last && (time() - $last) > $timeout) {
            $session->destroy();
            return redirect()->to('/auth?timeout=1');
        }

        $session->set('last_activity', time());

        // 🔥 CEK LOGIN SOURCE - APP tidak boleh akses IKPRS
        $loginSource = $session->get('login_source');

        if ($loginSource === 'APP' && strpos($uri, 'ikprs') === 0) {
            return redirect()->to('/siimut/dashboard');
        }
    }

    // public function before(RequestInterface $request, $arguments = null)
    // {
    //     $session = session();

    //     // ================= AJAX REQUEST =================
    //     if ($request->isAJAX()) {

    //         if (
    //             ! $session->get('logged_in') ||
    //             ! $session->get('hris_user_id')
    //         ) {
    //             return service('response')
    //                 ->setStatusCode(401)
    //                 ->setJSON([
    //                     'status'  => 'unauthorized',
    //                     'message' => 'Session habis, silakan login ulang'
    //                 ]);
    //         }

    //         return; // 🔥 STOP — JANGAN update last_activity
    //     }

    //     // ================= PAGE LOAD =================

    //     if (
    //         ! $session->get('logged_in') ||
    //         ! $session->get('hris_user_id')
    //     ) {
    //         return redirect()->to('/auth')->with('error', 'Silakan login dulu');
    //     }

    //     $timeout = 3600; // 1 jam
    //     $last = $session->get('last_activity');

    //     if ($last && (time() - $last) > $timeout) {
    //         $session->destroy();
    //         return redirect()->to('/auth?timeout=1');
    //     }

    //     // ✅ update activity HANYA untuk page load
    //     $session->set('last_activity', time());
    // }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // 🔥 INI KUNCI ANTI BACK
        $response->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
        $response->setHeader('Cache-Control', 'post-check=0, pre-check=0', false);
        $response->setHeader('Pragma', 'no-cache');
        $response->setHeader('Expires', '0');
    }
}
