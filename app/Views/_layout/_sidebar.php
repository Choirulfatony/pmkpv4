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

            <span class="brand-text fw-light">PMKP</span>
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

                            <?php foreach ($menus as $menu): ?>

                                <?php if (empty($menu['children'])): ?>

                                    <!-- MENU TANPA SUB -->
                                    <li class="nav-item">
                                        <a href="<?= (!empty($menu['url']) && $menu['url'] != '#')
                                                        ? site_url($menu['url'])
                                                        : 'javascript:void(0)' ?>" class="nav-link">
                                            <i class="nav-icon <?= esc($menu['icon']) ?>"></i>
                                            <p><?= esc($menu['nama_menu']) ?></p>
                                        </a>
                                    </li>

                                <?php else: ?>
                                    <!-- MENU DENGAN SUB -->
                                    <li class="nav-item has-treeview">

                                        <a href="#" class="nav-link">
                                            <i class="nav-icon <?= esc($menu['icon']) ?>"></i>

                                            <p class="d-flex justify-content-between align-items-center mb-0 w-100">
                                                <span><?= esc($menu['nama_menu']) ?></span>
                                                <!-- <i class="nav-arrow bi bi-chevron-right"></i> -->
                                            </p>
                                        </a>

                                   

                                        <ul class="nav nav-treeview">
                                            <?php foreach ($menu['children'] as $child): ?>
                                                <li class="nav-item">
                                                    <a href="<?= (!empty($child['url']) && $child['url'] != '#')
                                                                    ? site_url($child['url'])
                                                                    : 'javascript:void(0)' ?>"
                                                        class="nav-link sub-menu">

                                                        <i class="nav-icon <?= esc($child['icon']) ?>"></i>
                                                        <p><?= esc($child['nama_menu']) ?></p>

                                                    </a>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>

                                    </li>

                                <?php endif; ?>



                            <?php endforeach; ?>
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
    /* 🔥 RAPATKAN SUBMENU */
    .nav-treeview .nav-link {
        display: flex;
        align-items: center;
        padding-left: 1.5rem !important;
        /* atur jarak kiri */
    }

    /* ICON SUBMENU */
    .nav-treeview .nav-icon {
        width: 20px;
        text-align: center;
        margin-right: 10px;
        font-size: 14px;
    }

    /* TEXT SUBMENU */
    .nav-treeview .nav-link p {
        margin: 0;
    }

    /* HILANGKAN BULLET / CIRCLE ANEH */
    .nav-treeview .nav-item::before {
        display: none !important;
    }
</style>