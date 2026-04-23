<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// ========== HALAMAN UTAMA ==========
$routes->get('/', 'Auth::index');


// ========== AUTH ==========
$routes->get('auth', 'Auth::index');
$routes->post('auth/process', 'Auth::process');
$routes->get('auth/logout', 'Auth::logout');
$routes->get('auth/refresh-captcha', 'Auth::refresh_captcha');
$routes->post('auth/clear_register_session', 'Auth::clear_register_session');
$routes->post('auth/ping', 'Auth::ping');
$routes->get('auth/resend_verification', 'Auth::resend_verification');
$routes->get('auth/cek_session', 'Auth::cek_session');

// Google Login
$routes->get('auth/google-login', 'Auth::googleLogin');
$routes->get('auth/google-callback', 'Auth::googleCallback');

// Registrasi
$routes->get('auth/register', 'Auth::showRegister');
$routes->post('auth/register/process', 'Auth::processRegister');
$routes->get('auth/verify_email', 'Auth::verify_email');
$routes->get('auth/verify_email_notice', 'Auth::verify_email_notice');


// ========== DASHBOARD ==========
$routes->get('dashboard', 'Dashboard::index', ['filter' => 'auth']);


// ========== SIIMUT ==========
$routes->group('siimut', ['filter' => 'auth'], function ($routes) {
    // Dashboard
    $routes->get('', 'Dashboard::index');
    $routes->get('dashboard', 'Dashboard::index');

    // Rekap Laporan INM
    $routes->get('rekap-laporan-inm', 'RekapLaporanInm::index');
    $routes->post('rekap-laporan-inm/ajax_rekap_inm', 'RekapLaporanInm::getAjaxDataRekapInm');
    $routes->post('rekap-laporan-inm/ajax-detail-inm', 'RekapLaporanInm::getAjaxDataRekapInmDetail');
    $routes->get('rekap-laporan-inm/export', 'RekapLaporanInm::exportExcel');
    $routes->get('rekap-laporan-inm/export-indicator/(:num)', 'RekapLaporanInm::exportExcelIndicator/$1');
    $routes->get('rekap-laporan-inm/detail/(:num)', 'RekapLaporanInm::viewDetailInm/$1');

    // Rekap Periode INM (Triwulan/Semester/Tahun)
    $routes->get('rekap-periode-inm', 'RekapPeriodeInm::index');
    $routes->post('rekap-periode-inm/ajax_inm-(:num)', 'RekapPeriodeInm::getAjaxRekapPeriode/$1');
    $routes->get('rekap-periode-inm/export', 'RekapPeriodeInm::exportExcel');

    // Grafik INM
    $routes->get('grafik-inm', 'GrafikInm::index');
    $routes->post('grafik-inm/data', 'GrafikInm::getDataGrafik');

    // Grafik IMPRS
    $routes->get('grafik-imprs', 'GrafikImprs::index');
    $routes->post('grafik-imprs/data', 'GrafikImprs::getDataGrafik');

    // Grafik IMPUnit
    $routes->get('grafik-impunit', 'GrafikImpunit::index');
    $routes->post('grafik-impunit/data', 'GrafikImpunit::getDataGrafik');

    // Rekap Periode IMPRS
    $routes->get('rekap-periode-imprs', 'RekapPeriodeImprs::index');
    $routes->post('rekap-periode-imprs/ajax_imprs-(:num)', 'RekapPeriodeImprs::getAjaxRekapPeriode/$1');
    $routes->get('rekap-periode-imprs/export', 'RekapPeriodeImprs::exportExcel');

    // Rekap Periode IMPUnit
    $routes->get('rekap-periode-impunit', 'RekapPeriodeImpunit::index');
    $routes->post('rekap-periode-impunit/ajax_impunit-(:num)', 'RekapPeriodeImpunit::getAjaxRekapPeriode/$1');
    $routes->get('rekap-periode-impunit/export', 'RekapPeriodeImpunit::exportExcel');

    // Rekap Laporan IMPRS
    $routes->get('rekap-laporan-imprs', 'RekapLaporanImprs::index');
    $routes->post('rekap-laporan-imprs/ajax_rekap_imprs', 'RekapLaporanImprs::getAjaxDataRekapImprs');
    $routes->post('rekap-laporan-imprs/ajax-detail-imprs', 'RekapLaporanImprs::getAjaxDataRekapImprsDetail');
    $routes->get('rekap-laporan-imprs/export', 'RekapLaporanImprs::exportExcel');
    $routes->get('rekap-laporan-imprs/export-indicator/(:num)', 'RekapLaporanImprs::exportExcelIndicator/$1');
    $routes->get('rekap-laporan-imprs/detail/(:num)', 'RekapLaporanImprs::viewDetailImprs/$1');

    // Rekap Laporan IMPUnit
    $routes->get('rekap-laporan-impunit', 'RekapLaporanImpunit::index');
    $routes->post('rekap-laporan-impunit/ajax_rekap_impunit', 'RekapLaporanImpunit::getAjaxDataRekapImpunit');
    $routes->post('rekap-laporan-impunit/ajax-detail-impunit', 'RekapLaporanImpunit::getAjaxDataRekapImpunitDetail');
    $routes->get('rekap-laporan-impunit/export', 'RekapLaporanImpunit::exportExcel');
    $routes->get('rekap-laporan-impunit/export-indicator/(:num)', 'RekapLaporanImpunit::exportExcelIndicator/$1');
    $routes->get('rekap-laporan-impunit/detail/(:num)', 'RekapLaporanImpunit::viewDetailImpunit/$1');
});


// ========== IKPRS ==========
$routes->group('ikprs', ['filter' => 'auth'], function ($routes) {
    // Dashboard
    $routes->get('/', 'Ikprs::index');
    $routes->get('menu', 'Ikprs::ikprs');

    // Form Input
    $routes->get('_form_add_ikp', 'Ikprs::formAddIkp');
    $routes->match(['get', 'post'], 'form_drafts', 'Ikprs::formDrafts');
    $routes->match(['get', 'post'], 'form_send', 'Ikprs::formSend');
    $routes->match(['get', 'post'], 'form_inbox_karu', 'Ikprs::formInbox_karu');
    $routes->match(['get', 'post'], 'form_info', 'Ikprs::formInfo');

    // Aksi
    $routes->match(['get', 'post'], 'tandaiDibaca', 'Ikprs::tandaiDibaca');
    $routes->post('simpanikp', 'Ikprs::simpanikp');
    $routes->post('simpan_verifikasi', 'Ikprs::verifikasi_karu');
    $routes->post('validasi_komite', 'Ikprs::validasi_komite');
    $routes->post('cari_pasien', 'Ikprs::cari_pasien');

    // AJAX & Detail
    $routes->get('counter-ajax', 'Ikprs::counterAjax');
    $routes->get('get_departments', 'Ikprs::get_departments');
    $routes->get('detailInboxKaru/(:num)', 'Ikprs::detailInboxKaru/$1');
    $routes->get('detail-insiden/(:num)', 'Ikprs::detailInsiden/$1');
});