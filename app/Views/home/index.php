         <!--begin::Container-->
         <div class="container-fluid">
             <!--begin::Row - Welcome Card-->
             <div class="row mb-4">
                 <div class="col-12">
                     <div class="card border-0 shadow-sm">
                         <div class="card-body p-4">
                             <div class="d-flex align-items-center">
                                 <div class="me-3">
                                     <i class="fas fa-hospital fa-3x text-primary"></i>
                                 </div>
                                 <div>
                                     <h4 class="mb-1 fw-bold">Selamat Datang di PMKPV4</h4>
                                     <p class="text-muted mb-0">Sistem Pengelolaan Indikator Mutu Nasional (INM) - Versi 4</p>
                                 </div>
                             </div>
                         </div>
                     </div>
                 </div>
             </div>
             <!--end::Row-->

             <!--begin::Row - Quick Access-->
             <div class="row mb-4">
                 <div class="col-12">
                     <h5 class="mb-3 fw-bold">Menu Cepat</h5>
                 </div>
                 <div class="col-lg-3 col-md-6 col-6">
                     <a href="<?= site_url('siimut/rekap-laporan-inm') ?>" class="text-decoration-none">
                         <div class="card border-0 shadow-sm h-100 hover-card">
                             <div class="card-body text-center p-4">
                                 <i class="fas fa-chart-bar fa-2x text-primary mb-3"></i>
                                 <h6 class="mb-0 fw-bold">Rekap INM</h6>
                                 <small class="text-muted">Laporan Indikator Mutu</small>
                             </div>
                         </div>
                     </a>
                 </div>
                 <div class="col-lg-3 col-md-6 col-6">
                     <a href="<?= site_url('siimut/dashboard') ?>" class="text-decoration-none">
                         <div class="card border-0 shadow-sm h-100 hover-card">
                             <div class="card-body text-center p-4">
                                 <i class="fas fa-tachometer-alt fa-2x text-success mb-3"></i>
                                 <h6 class="mb-0 fw-bold">Dashboard</h6>
                                 <small class="text-muted">Ringkasan Data</small>
                             </div>
                         </div>
                     </a>
                 </div>
                 <div class="col-lg-3 col-md-6 col-6">
                     <a href="<?= site_url('ikprs') ?>" class="text-decoration-none">
                         <div class="card border-0 shadow-sm h-100 hover-card">
                             <div class="card-body text-center p-4">
                                 <i class="fas fa-clipboard-list fa-2x text-warning mb-3"></i>
                                 <h6 class="mb-0 fw-bold">Input IKP</h6>
                                 <small class="text-muted">Input Data Indikator</small>
                             </div>
                         </div>
                     </a>
                 </div>
                 <div class="col-lg-3 col-md-6 col-6">
                     <a href="<?= site_url('siimut/rekap-laporan-inm') ?>" class="text-decoration-none">
                         <div class="card border-0 shadow-sm h-100 hover-card">
                             <div class="card-body text-center p-4">
                                 <i class="fas fa-file-alt fa-2x text-info mb-3"></i>
                                 <h6 class="mb-0 fw-bold">Laporan</h6>
                                 <small class="text-muted">Cetak & Export</small>
                             </div>
                         </div>
                     </a>
                 </div>
             </div>
             <!--end::Row-->

             <!--begin::Row - Info Cards-->
             <div class="row mb-4">
                 <div class="col-12">
                     <h5 class="mb-3 fw-bold">Informasi Sistem</h5>
                 </div>
                 <div class="col-lg-4 col-md-6">
                     <div class="card border-0 shadow-sm h-100">
                         <div class="card-body">
                             <div class="d-flex align-items-center mb-3">
                                 <div class="bg-primary bg-opacity-10 rounded-circle p-3 me-3">
                                     <i class="fas fa-info-circle text-primary"></i>
                                 </div>
                                 <h6 class="mb-0 fw-bold">Tentang Aplikasi</h6>
                             </div>
                             <p class="text-muted mb-0 small">
                                 PMKPV4 adalah sistem pengelolaan Indikator Mutu Nasional (INM) 
                                 untuk rumah sakit. Aplikasi ini membantu dalam pemantauan dan 
                                 evaluasi indikator mutu pelayanan kesehatan.
                             </p>
                         </div>
                     </div>
                 </div>
                 <div class="col-lg-4 col-md-6">
                     <div class="card border-0 shadow-sm h-100">
                         <div class="card-body">
                             <div class="d-flex align-items-center mb-3">
                                 <div class="bg-success bg-opacity-10 rounded-circle p-3 me-3">
                                     <i class="fas fa-cogs text-success"></i>
                                 </div>
                                 <h6 class="mb-0 fw-bold">Fitur Utama</h6>
                             </div>
                             <ul class="text-muted mb-0 small ps-3">
                                 <li>Input data indikator mutu per ruangan</li>
                                 <li>Rekapitulasi data bulanan & tahunan</li>
                                 <li>Monitoring pencapaian target</li>
                                 <li>Laporan dan export data</li>
                             </ul>
                         </div>
                     </div>
                 </div>
                 <div class="col-lg-4 col-md-6">
                     <div class="card border-0 shadow-sm h-100">
                         <div class="card-body">
                             <div class="d-flex align-items-center mb-3">
                                 <div class="bg-warning bg-opacity-10 rounded-circle p-3 me-3">
                                     <i class="fas fa-lightbulb text-warning"></i>
                                 </div>
                                 <h6 class="mb-0 fw-bold">Petunjuk Penggunaan</h6>
                             </div>
                             <ul class="text-muted mb-0 small ps-3">
                                 <li>Pilih tahun pada dropdown untuk filter data</li>
                                 <li>Klik indikator untuk melihat detail per ruangan</li>
                                 <li>Gunakan tombol fullscreen untuk tampilan penuh</li>
                                 <li>Warna hijau = target tercapai, merah = tidak tercapai</li>
                             </ul>
                         </div>
                     </div>
                 </div>
             </div>
             <!--end::Row-->

             <!--begin::Row - Footer Info-->
             <div class="row">
                 <div class="col-12">
                     <div class="card border-0 shadow-sm">
                         <div class="card-body p-3">
                             <div class="d-flex flex-wrap justify-content-between align-items-center">
                                 <div>
                                     <small class="text-muted">
                                         <i class="fas fa-code-branch me-1"></i> PMKPV4 v4.0 | 
                                         <i class="fas fa-calendar me-1"></i> <?= date('Y') ?>
                                     </small>
                                 </div>
                                 <div>
                                     <small class="text-muted">
                                         <i class="fas fa-server me-1"></i> Server: <?= $_SERVER['SERVER_NAME'] ?? 'localhost' ?>
                                     </small>
                                 </div>
                             </div>
                         </div>
                     </div>
                 </div>
             </div>
             <!--end::Row-->
         </div>
         <!--end::Container-->

         <style>
             .hover-card {
                 transition: transform 0.2s ease, box-shadow 0.2s ease;
             }
             .hover-card:hover {
                 transform: translateY(-5px);
                 box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
             }
             .bg-opacity-10 {
                 background-opacity: 0.1;
             }
         </style>
