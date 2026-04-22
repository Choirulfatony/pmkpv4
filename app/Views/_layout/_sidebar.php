<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="">
    <div class="sidebar-brand sticky-top bg-body shadow-sm">
        <a href="<?= site_url('dashboard') ?>" class="brand-link">
            <img src="<?= base_url('assets/login/img/rssm_icon.ico') ?>" class="brand-image rounded-circle shadow" alt="Logo" style="width:40px; height:40px; object-fit:cover;">
            <span class="brand-text fw-light">SIIMUT</span>
        </a>
    </div>

    <?php $login_source = session('login_source'); ?>
    <?php if (isset($login_source)): ?>
        <div class="sidebar-wrapper">
            <nav class="mt-2">
                <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="navigation" data-accordion="true">
                    <?php if ($login_source == 'APP'): ?>
                        <li class="nav-item">
                            <div class="px-3 py-2 mb-2 bg-warning text-dark rounded">
                                <small class="d-block"><strong>Hak Akses:</strong> <?= session('role_asli') ?? '-' ?></small>
                                <small class="d-block"><strong>User Role:</strong> <?= session('user_role') ?? '-' ?></small>
                            </div>
                        </li>
                        <?php
                        function renderMenu($menus)
                        {
                            foreach ($menus as $menu):
                                $hasChild = !empty($menu['children']);
                                if ($hasChild): ?>
                                    <li class="nav-item has-treeview">
                                        <a href="#" class="nav-link d-flex align-items-center">
                                            <i class="nav-icon <?= esc($menu['icon']) ?>"></i>
                                            <p class="mb-0 ms-2 flex-grow-1"><?= esc($menu['nama_menu']) ?></p>
                                        </a>
                                        <ul class="nav nav-treeview ms-3"><?php renderMenu($menu['children']); ?></ul>
                                    </li>
                                <?php else: ?>
                                    <li class="nav-item">
                                        <a href="<?= (!empty($menu['url']) && $menu['url'] != '#') ? site_url($menu['url']) : 'javascript:void(0)' ?>" class="nav-link d-flex align-items-center">
                                            <i class="nav-icon <?= esc($menu['icon']) ?>"></i>
                                            <p class="mb-0 ms-2"><?= esc($menu['nama_menu']) ?></p>
                                        </a>
                                    </li>
                                <?php endif;
                            endforeach;
                        }
                        renderMenu($menus);
                        ?>
                    <?php endif; ?>

                    <?php if ($login_source == 'HRIS' && in_array(session('user_role'), ['KOMITE', 'KARU'])): ?>
                        <li class="nav-item">
                            <a href="<?= site_url('ikprs') ?>" class="nav-link">
                                <i class="nav-icon bi bi-speedometer"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if ($login_source == 'HRIS'): ?>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon bi bi-pencil-square"></i>
                                <p>Forms Input</p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="<?= site_url('ikprs/menu') ?>" class="nav-link">
                                        <i class="nav-icon bi bi-circle"></i>
                                        <p>IKPRS RSSM</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    <?php endif; ?>
</aside>

<style>
    .sidebar-wrapper { overflow-y: auto; overflow-x: visible; }
    .sidebar-menu { overflow-x: visible; white-space: nowrap; }
    .sidebar-menu .nav-link { display: flex; align-items: center; }
    .sidebar-menu .nav-icon { margin-right: 8px; flex-shrink: 0; }
    .sidebar-menu .nav-link p { flex-grow: 1; }
    .nav-treeview .nav-link { padding-left: 1.5rem; }
    .nav-treeview .nav-link p { margin: 0; word-wrap: break-word; white-space: normal; }
    .nav-treeview .nav-item::before { display: none; }
    .has-treeview:not(.menu-open)>.nav-treeview { display: none; }
</style>