<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class HrisFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (session('hris_logged_in') !== true) {

            if ($request->isAJAX()) {
                return service('response')
                    ->setJSON([
                        'status'  => 'hris_required',
                        'message' => 'Silakan login HRIS'
                    ])
                    ->setStatusCode(401); // 🔥 WAJIB
            }

            return redirect()->to(site_url('ikprs'));
        }
    }

    public function after(
        RequestInterface $request,
        ResponseInterface $response,
        $arguments = null
    ) {
        // kosong
    }
}
