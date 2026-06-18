<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\SiimutMenuModel;

class MenuManager extends AppController
{
    protected SiimutMenuModel $menuModel;

    public function __construct()
    {
        parent::__construct();
        $this->menuModel = new SiimutMenuModel();
    }

    public function index()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/auth');
        }
        if (session()->get('user_role') !== 'ADMINISTRATOR') {
            return redirect()->to('/siimut/dashboard')->with('error', 'Hanya untuk Administrator');
        }

        // Auto-create default menus if empty
        $count = $this->menuModel->countAll();
        if ($count === 0) {
            $this->_seedDefaultMenus();
        }

        // Ambil semua menu untuk dropdown parent
        $allMenus = $this->menuModel->orderBy('urutan', 'ASC')->findAll();

        // Daftar role yang tersedia
        $roles = ['ADMINISTRATOR', 'KOMITE', 'KENDALI_MUTU', 'VALIDATOR', 'APP', 'KARU', 'KEPALA_KEPERAWATAN', 'PELAPOR'];

        return $this->render('siimut/menu_list', [
            'judul'   => 'Pengaturan Menu Sidebar',
            'icon'    => '<i class="bi bi-list-ul"></i>',
            'allMenus' => $allMenus,
            'roles'    => $roles,
        ]);
    }

    public function ajaxGetData()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        }

        $post = $this->request->getPost();
        $draw  = (int) ($post['draw'] ?? 1);
        $start = (int) ($post['start'] ?? 0);
        $length = (int) ($post['length'] ?? 10);
        $search = $post['search']['value'] ?? '';

        $builder = $this->menuModel->builder();
        $builder->select('siimut_menus.*, parent.nama_menu as parent_nama')
            ->join('siimut_menus as parent', 'parent.id_menu = siimut_menus.parent_id', 'left');

        $total = $builder->countAllResults(false);

        if ($search) {
            $builder->like('siimut_menus.nama_menu', $search)
                ->orLike('siimut_menus.url', $search)
                ->orLike('parent.nama_menu', $search);
        }

        $filtered = $builder->countAllResults(false);

        $builder->orderBy('siimut_menus.urutan', 'ASC')
            ->limit($length, $start);

        $data = $builder->get()->getResultArray();

        $rows = [];
        $no = $start + 1;
        foreach ($data as $row) {
            $rows[] = [
                'no'          => $no++,
                'id_menu'     => $row['id_menu'],
                'nama_menu'   => $row['nama_menu'],
                'url'         => $row['url'],
                'icon'        => $row['icon'],
                'parent_nama' => $row['parent_nama'] ?? '<em class="text-muted">Root</em>',
                'urutan'      => $row['urutan'],
                'role_access' => $row['role_access'],
                'parent_id'   => $row['parent_id'],
            ];
        }

        return $this->response->setJSON([
            'draw'            => $draw,
            'recordsTotal'    => $total,
            'recordsFiltered' => $filtered,
            'data'            => $rows,
        ]);
    }

    public function store()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setStatusCode(401)->setJSON(['status' => false, 'message' => 'Unauthorized']);
        }
        if (session()->get('user_role') !== 'ADMINISTRATOR') {
            return $this->response->setJSON(['status' => false, 'message' => 'Hanya untuk Administrator']);
        }

        $namaMenu = trim($this->request->getPost('nama_menu'));
        if (!$namaMenu) {
            return $this->response->setJSON(['status' => false, 'message' => 'Nama menu wajib diisi']);
        }

        $roleAccess = $this->request->getPost('role_access');
        $roleAccessStr = is_array($roleAccess) ? implode(',', $roleAccess) : '';

        $data = [
            'nama_menu'   => $namaMenu,
            'url'         => trim($this->request->getPost('url') ?? ''),
            'icon'        => trim($this->request->getPost('icon') ?? ''),
            'parent_id'   => $this->request->getPost('parent_id') ?: null,
            'role_access' => $roleAccessStr,
            'urutan'      => (int) ($this->request->getPost('urutan') ?? 0),
        ];

        $id = $this->request->getPost('id_menu');
        if ($id) {
            $this->menuModel->update($id, $data);
            $msg = 'Menu berhasil diperbarui';
        } else {
            $this->menuModel->insert($data);
            $msg = 'Menu berhasil ditambahkan';
        }

        return $this->response->setJSON(['status' => true, 'message' => $msg]);
    }

    public function delete(int $id)
    {
        if (!session()->get('logged_in')) {
            return $this->response->setStatusCode(401)->setJSON(['status' => false, 'message' => 'Unauthorized']);
        }
        if (session()->get('user_role') !== 'ADMINISTRATOR') {
            return $this->response->setJSON(['status' => false, 'message' => 'Hanya untuk Administrator']);
        }

        // Cek apakah memiliki child
        $childCount = $this->menuModel->where('parent_id', $id)->countAllResults();
        if ($childCount > 0) {
            return $this->response->setJSON(['status' => false, 'message' => 'Tidak bisa dihapus — menu ini memiliki ' . $childCount . ' sub-menu. Hapus sub-menu terlebih dahulu.']);
        }

        $this->menuModel->delete($id);
        return $this->response->setJSON(['status' => true, 'message' => 'Menu berhasil dihapus']);
    }

    public function getMenu(int $id)
    {
        if (!session()->get('logged_in')) {
            return $this->response->setStatusCode(401)->setJSON(['status' => false, 'message' => 'Unauthorized']);
        }

        $menu = $this->menuModel->find($id);
        if (!$menu) {
            return $this->response->setJSON(['status' => false, 'message' => 'Menu tidak ditemukan']);
        }

        return $this->response->setJSON(['status' => true, 'data' => $menu]);
    }

    private function _seedDefaultMenus()
    {
        $this->menuModel->insertBatch([
            [
                'nama_menu'   => 'Dashboard',
                'url'         => 'siimut/dashboard',
                'icon'        => 'bi bi-speedometer2',
                'parent_id'   => null,
                'role_access' => 'ADMINISTRATOR,KOMITE,KENDALI_MUTU',
                'urutan'      => 1,
            ],
            [
                'nama_menu'   => 'Pengaturan',
                'url'         => '',
                'icon'        => 'bi bi-gear',
                'parent_id'   => null,
                'role_access' => 'ADMINISTRATOR',
                'urutan'      => 99,
            ],
            [
                'nama_menu'   => 'Menu Sidebar',
                'url'         => 'siimut/menu-manager',
                'icon'        => 'bi bi-list-ul',
                'parent_id'   => null,
                'role_access' => 'ADMINISTRATOR',
                'urutan'      => 1,
            ],
        ]);

        // Set parent for Menu Sidebar
        $pengaturan = $this->menuModel->where('nama_menu', 'Pengaturan')->first();
        $menuSidebar = $this->menuModel->where('nama_menu', 'Menu Sidebar')->first();
        if ($pengaturan && $menuSidebar) {
            $this->menuModel->update($menuSidebar['id_menu'], ['parent_id' => $pengaturan['id_menu'], 'urutan' => 1]);
        }
    }
}
