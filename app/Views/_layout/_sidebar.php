<!--begin::Sidebar-->
<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="">
    <!--begin::Sidebar Brand-->
    <div class="sidebar-brand sticky-top bg-body shadow-sm">
        <a href="<?= site_url('dashboard') ?>" class="brand-link">
            <img
                src="<?= base_url('assets/login/img/rssm_icon.ico') ?>"
                class="brand-image rounded-circle shadow"
                alt="Logo"
                style="width:40px; height:40px; object-fit:cover;">

            <span class="brand-text fw-light">SIIMUT</span>
        </a>
    </div>
    <!--end::Sidebar Brand-->

    <!--begin::Sidebar Wrapper-->
    <?php $login_source = session('login_source'); ?>

    <?php if (isset($login_source)): ?>

        <div class="sidebar-wrapper">
            <nav class="mt-2">

                <ul class="nav sidebar-menu flex-column"
                    data-lte-toggle="treeview"
                    role="navigation"
                    data-accordion="false">

                    <!-- MENU SIIMUT -->
                    <?php if ($login_source == 'APP'): ?>

                        <!-- DEBUG HAK AKSES -->
                        <li class="nav-item">
                            <div class="px-3 py-2 mb-2 bg-warning text-dark rounded">
                                <small class="d-block"><strong>Hak Akses:</strong> <?= session('role_asli') ?? '-' ?></small>
                                <small class="d-block"><strong>User Role:</strong> <?= session('user_role') ?? '-' ?></small>
                            </div>
                        </li>

                        <!-- <li class="nav-item">
                            <a href="<?= site_url('dashboard') ?>" class="nav-link">
                                <i class="nav-icon bi bi-speedometer"></i>
                                <p>Dashboard</p>
                            </a>
                        </li> -->


                        <?php if ($login_source == 'APP'): ?>

                            <?php
                            function renderMenu($menus, $level = 0)
                            {
                                foreach ($menus as $menu):

                                    $hasChild = !empty($menu['children']);
                            ?>

                                    <?php if ($hasChild): ?>

                                        <li class="nav-item has-treeview">
                                            <a href="#" class="nav-link">

                                                <!-- ICON -->
                                                <i class="nav-icon <?= esc($menu['icon']) ?>"></i>

                                                <p class="mb-0 flex-grow-1">
                                                    <?= esc($menu['nama_menu']) ?>
                                                </p>

                                            </a>

                                            <ul class="nav nav-treeview">
                                                <?php renderMenu($menu['children'], $level + 1); ?>
                                            </ul>

                                        </li>

                                    <?php else: ?>

                                        <li class="nav-item">
                                            <a href="<?= (!empty($menu['url']) && $menu['url'] != '#')
                                                            ? site_url($menu['url'])
                                                            : 'javascript:void(0)' ?>"
                                                class="nav-link d-flex align-items-center">

                                                <!-- ICON -->
                                                <i class="nav-icon <?= esc($menu['icon']) ?>"></i>

                                                <!-- TEXT -->
                                                <p class="mb-0 ms-2 flex-grow-1">
                                                    <?= esc($menu['nama_menu']) ?>
                                                </p>

                                            </a>
                                        </li>

                                    <?php endif; ?>

                            <?php
                                endforeach;
                            }
                            ?>

                            <?php renderMenu($menus); ?>

                        <?php endif; ?>


                    <?php endif; ?>


                    <!-- MENU IKPRS -->
                    <?php if ($login_source == 'HRIS' && in_array(session('user_role'), ['KOMITE', 'KARU'])): ?>

                        <li class="nav-item">
                            <a href="<?= site_url('ikprs') ?>" class="nav-link">
                                <i class="nav-icon bi bi-speedometer"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>

                    <?php endif; ?>

                    <!-- MENU FORMS INPUT (untuk semua HRIS) -->
                    <?php if ($login_source == 'HRIS'): ?>

                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon bi bi-pencil-square"></i>
                                <p>
                                    Forms Input
                                </p>
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

    <!--end::Sidebar Wrapper-->
</aside>
<!--end::Sidebar-->


<style>
    .sidebar-menu .nav-link {
        padding-left: 1rem !important;
    }

    .sidebar-menu .nav-link p,
    .nav-treeview .nav-link p {
        white-space: normal;
    }

    .nav-treeview .nav-icon {
        width: 20px;
        text-align: center;
        margin-right: 8px;
        font-size: 14px;
    }

    .nav-treeview .nav-link p {
        margin: 0;
    }

    .nav-treeview .nav-item::before {
        display: none !important;
    }
</style>