<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// halaman default = login
$routes->get('/', 'Auth::index');
// $routes->get('test-gd', 'Test::gd');
// $routes->get('test-db', 'TestDb::index');


$routes->get('auth', 'Auth::index');
$routes->post('auth/process', 'Auth::process');
$routes->get('auth/refresh-captcha', 'Auth::refresh_captcha');
$routes->get('auth/logout', 'Auth::logout');
$routes->post('auth/clear_register_session', 'Auth::clear_register_session');
$routes->post('auth/ping', 'Auth::ping');
$routes->get('auth/resend_verification', 'Auth::resend_verification');
$routes->get('auth/cek_session', 'Auth::cek_session');
$routes->get('auth/google-login', 'Auth::googleLogin');
$routes->get('auth/google-callback', 'Auth::googleCallback');
$routes->get('auth/register', 'Auth::showRegister');
$routes->post('auth/register/process', 'Auth::processRegister');
$routes->get('auth/verify_email', 'Auth::verify_email');
$routes->get('auth/verify_email_notice', 'Auth::verify_email_notice');

// Redirect /dashboard ke /siimut/dashboard
$routes->get('dashboard', 'Dashboard::index', ['filter' => 'auth']);


// ================= SIIMUT =================
$routes->group('siimut', ['filter' => 'auth'], function ($routes) {
    $routes->get('', 'Dashboard::index');        // /siimut
    $routes->get('dashboard', 'Dashboard::index'); // /siimut/dashboard
    $routes->get('rekap-laporan-inm', 'RekapLaporanInm::index'); // /siimut/rekap-laporan-inm
    $routes->post('rekap-laporan-inm/ajax_rekap_inm', 'RekapLaporanInm::getAjaxDataRekapInm'); // AJAX
    $routes->post('rekap-laporan-inm/ajax-detail-inm', 'RekapLaporanInm::getAjaxDataRekapInmDetail'); // AJAX Detail
    $routes->get('rekap-laporan-inm/export', 'RekapLaporanInm::exportExcel'); // Export Excel
    $routes->get('rekap-laporan-inm/export-indicator/(:num)', 'RekapLaporanInm::exportExcelIndicator/$1'); // Export Excel per indicator
    $routes->get('rekap-periode-inm', 'RekapPeriodeInm::index'); // Rekap Triwulan/Semester/Tahun
    $routes->post('rekap-periode-inm/ajax_inm-(:num)', 'RekapPeriodeInm::getAjaxRekapPeriode/$1'); // AJAX Rekap Periode INM
    $routes->get('rekap-periode-inm/export', 'RekapPeriodeInm::exportExcel'); // Export Excel
    $routes->get('rekap-laporan-inm/detail/(:num)', 'RekapLaporanInm::viewDetailInm/$1'); // Detail

    // Grafik INM
    $routes->get('grafik-inm', 'GrafikInm::index'); // Halaman Grafik
    $routes->post('grafik-inm/data', 'GrafikInm::getDataGrafik'); // AJAX Data Grafik

    // Grafik IMPRS
    $routes->get('grafik-imprs', 'GrafikImprs::index'); // Halaman Grafik
    $routes->post('grafik-imprs/data', 'GrafikImprs::getDataGrafik'); // AJAX Data Grafik

    // Rekap Periode IMPRS
    $routes->get('rekap-periode-imprs', 'RekapPeriodeImprs::index'); // Rekap Triwulan/Semester/Tahun
    $routes->post('rekap-periode-imprs/ajax_imprs-(:num)', 'RekapPeriodeImprs::getAjaxRekapPeriode/$1'); // AJAX Rekap Periode IMPRS
    $routes->get('rekap-periode-imprs/export', 'RekapPeriodeImprs::exportExcel'); // Export Excel

    // Form Input IMPRS
    $routes->get('form-imprs', 'FormImprs::index'); // Halaman Form Input
    $routes->post('form-imprs/ajax-list', 'FormImprs::ajax_list'); // AJAX DataTables
    $routes->get('form-imprs/get-indicators', 'FormImprs::get_indicators'); // AJAX Get Indicators
    $routes->get('form-imprs/get-indicator-detail', 'FormImprs::get_indicator_detail'); // AJAX Get Detail
    $routes->post('form-imprs/save', 'FormImprs::save'); // Save Data
    $routes->post('form-imprs/save-perbaikan', 'FormImprs::save_perbaikan'); // Save Perbaikan

    // Rekap Laporan IMPRS
    $routes->get('rekap-laporan-imprs', 'RekapLaporanImprs::index'); // Halaman Rekap
    $routes->post('rekap-laporan-imprs/ajax_rekap_imprs', 'RekapLaporanImprs::getAjaxDataRekapImprs'); // AJAX
    $routes->post('rekap-laporan-imprs/ajax-detail-imprs', 'RekapLaporanImprs::getAjaxDataRekapImprsDetail'); // AJAX Detail
    $routes->get('rekap-laporan-imprs/export', 'RekapLaporanImprs::exportExcel'); // Export Excel
    $routes->get('rekap-laporan-imprs/export-indicator/(:num)', 'RekapLaporanImprs::exportExcelIndicator/$1'); // Export Excel per indicator
    $routes->get('rekap-laporan-imprs/detail/(:num)', 'RekapLaporanImprs::viewDetailImprs/$1'); // Detail
});

$routes->get('test-email', 'Ikprs::kirimEmailTest');

$routes->group('ikprs', ['filter' => 'auth'], function ($routes) {


    $routes->get('/', 'Ikprs::index');      // Dashboard IKPRS
    $routes->get('menu', 'Ikprs::ikprs');   // Halaman IKPRS

    $routes->get('_form_add_ikp', 'Ikprs::formAddIkp');

    $routes->match(['get', 'post'], 'form_drafts', 'Ikprs::formDrafts');
    $routes->match(['get', 'post'], 'form_send', 'Ikprs::formSend');
    $routes->match(['get', 'post'], 'form_inbox_karu', 'Ikprs::formInbox_karu');
    $routes->match(['get', 'post'], 'form_info', 'Ikprs::formInfo');
    $routes->match(['get', 'post'], 'tandaiDibaca', 'Ikprs::tandaiDibaca');

    $routes->get('counter-ajax', 'Ikprs::counterAjax');
    $routes->get('detailInboxKaru/(:num)', 'Ikprs::detailInboxKaru/$1');
    $routes->get('detail-insiden/(:num)', 'Ikprs::detailInsiden/$1');
    $routes->post('cari_pasien', 'Ikprs::cari_pasien');
    $routes->get('get_departments', 'Ikprs::get_departments');
    $routes->post('simpanikp', 'Ikprs::simpanikp');
    $routes->post('simpan_verifikasi', 'Ikprs::verifikasi_karu');
    $routes->post('validasi_komite', 'Ikprs::validasi_komite');
});




// $routes->group('ikprs', ['filter' => 'auth'], function ($routes) {

//     $routes->get('/', 'Ikprs::index');

//     // FORM
//     $routes->match(['get', 'post'], 'form_inbox',  'Ikprs::formInbox');
//     $routes->match(['get', 'post'], 'form_draft',  'Ikprs::formDrafts');
//     $routes->match(['get', 'post'], 'form_send',   'Ikprs::formSend');
//     $routes->get('form_add', 'Ikprs::formAddIkp');

//     // AJAX
//     $routes->get('counter-ajax', 'Ikprs::counterAjax');
//     $routes->get('get_departments', 'Ikprs::get_departments');
//     $routes->post('cari_pasien', 'Ikprs::cari_pasien');
//     $routes->post('save', 'Ikprs::simpanikp');
// });














// $routes->get('ikprs', 'Ikprs::index', ['filter' => 'auth']);


// $routes->get('ikprs/data-login', 'Ikprs::dataLogin'); // tanpa filter
// $routes->post('ikprs/login-process', 'Ikprs::loginProcess');

// $routes->group('ikprs', ['filter' => 'auth'], function ($routes) {
//     $routes->get('/', 'Ikprs::index');
//     $routes->post('logout-hris', 'Ikprs::logoutHris');
// });



// ================= IKPRS =================

// // AJAX LOGIN HRIS (TANPA FILTER APAPUN)
// $routes->get('ikprs/data-login', 'Ikprs::dataLogin');
// $routes->post('ikprs/login-process', 'Ikprs::loginProcess');

// // HALAMAN IKPRS
// $routes->group('ikprs', ['filter' => 'auth'], function ($routes) {

//     $routes->get('/', 'Ikprs::index');
//     $routes->post('logout-hris', 'Ikprs::logoutHris');

//     // 🔥 ROUTE FORM ADD IKP (WAJIB)
//     $routes->get('_form_add_ikp', 'Ikprs::formAddIkp');

//     // 🔐 KHUSUS SETELAH LOGIN HRIS
//     $routes->group('', ['filter' => 'hris'], function ($routes) {
//         $routes->get('inbox', 'Ikprs::inbox');
//         $routes->get('detail/(:num)', 'Ikprs::detail/$1');
//     });
// });
