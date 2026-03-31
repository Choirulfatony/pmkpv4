<?php

namespace App\Models;

use CodeIgniter\Model;

class SiimutMenuModel extends Model
{
    protected $table = 'siimut_menus';
    protected $primaryKey = 'id_menu';
    protected $allowedFields = [
        'nama_menu',
        'url',
        'icon',
        'parent_id',
        'role_access',
        'urutan'
    ];

    public function getMenuByRole($role)
    {
        $menus = $this->orderBy('urutan', 'ASC')->findAll();

        // Filter berdasarkan role
        $filtered = array_filter($menus, function ($menu) use ($role) {

            $roles = !empty($menu['role_access'])
                ? array_map('trim', explode(',', $menu['role_access']))
                : [];

            return in_array($role, $roles);
        });

        // Susun parent-child
        $menuTree = [];
        foreach ($filtered as $menu) {
            if ($menu['parent_id'] == NULL) {
                $menu['children'] = [];

                foreach ($filtered as $child) {
                    if ($child['parent_id'] == $menu['id_menu']) {
                        $menu['children'][] = $child;
                    }
                }

                $menuTree[] = $menu;
            }
        }

        return $menuTree;
    }
}
