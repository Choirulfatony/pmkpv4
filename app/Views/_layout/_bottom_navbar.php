<?php
// Deteksi URL aktif (opsional tapi disarankan)
$uri = service('uri')->getSegment(1);
?>

<!--begin::Bottom Navbar (Mobile Only)-->
<nav id="bottomNavbar"
    class="navbar navbar-dark bg-dark fixed-bottom d-md-none border-top">

    <div class="container-fluid px-0">
        <ul class="nav nav-pills nav-fill w-100">

            <!-- Dashboard -->
            <li class="nav-item">
                <a href="<?= site_url('dashboard') ?>"
                    class="nav-link text-center <?= $uri === 'dashboard' ? 'active' : '' ?>">
                    <i class="bi bi-speedometer fs-5"></i>
                    <small class="d-block">Home</small>
                </a>
            </li>

            <!-- Input / Form -->
            <!-- <li class="nav-item">
                <a href="<?= site_url('form') ?>"
                    class="nav-link text-center <?= $uri === 'form' ? 'active' : '' ?>">
                    <i class="bi bi-pencil-square fs-5"></i>
                    <small class="d-block">Input</small>
                </a>
            </li> -->

            <!-- Notification -->
            <!-- <li class="nav-item position-relative">
                <a href="<?= site_url('notifications') ?>"
                    class="nav-link text-center <?= $uri === 'notifications' ? 'active' : '' ?>">
                    <i class="bi bi-bell fs-5"></i>
                    <span class="badge bg-danger position-absolute top-0 start-50 translate-middle">
                        3
                    </span>
                    <small class="d-block">Notif</small>
                </a>
            </li> -->

            <!-- Profile -->
            <!-- <li class="nav-item">
                <a href="<?= site_url('profile') ?>"
                    class="nav-link text-center <?= $uri === 'profile' ? 'active' : '' ?>">
                    <i class="bi bi-person-circle fs-5"></i>
                    <small class="d-block">Profile</small>
                </a>
            </li> -->

            <!-- Logout -->
            <li class="nav-item">
                <a href="<?= site_url('auth/logout') ?>"
                    class="nav-link text-center text-danger">
                    <i class="bi bi-box-arrow-right fs-5"></i>
                    <small class="d-block">Logout</small>
                </a>
            </li>

        </ul>
    </div>
</nav>
<!--end::Bottom Navbar-->