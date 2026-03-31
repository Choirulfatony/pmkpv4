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

                        <li class="nav-item">
                            <a href="<?= site_url('dashboard') ?>" class="nav-link">
                                <i class="nav-icon bi bi-speedometer"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>

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