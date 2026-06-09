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
        
        // Form Input INM
        $routes->get('form-inm', 'LoadModuleForminput::index');
        $routes->post('load-module-forminput/get-indicators', 'LoadModuleForminput::get_indicators');
        $routes->post('load-module-forminput/get-indicator-detail', 'LoadModuleForminput::get_indicator_detail');
        $routes->post('load-module-forminput/save', 'LoadModuleForminput::save');
        $routes->post('load-module-forminput/delete', 'LoadModuleForminput::delete');
        $routes->post('load-module-forminput/validasi', 'LoadModuleForminput::validasi');
        $routes->post('load-module-forminput/get-riwayat', 'LoadModuleForminput::get_riwayat');
        $routes->post('load-module-forminput/get-daily-detail', 'LoadModuleForminput::get_daily_detail');
        $routes->post('load-module-forminput/check-input-allowed', 'LoadModuleForminput::check_input_allowed');
        $routes->post('load-module-forminput/request-approval', 'LoadModuleForminput::request_approval');
        $routes->post('load-module-forminput/check-request-status', 'LoadModuleForminput::check_request_status');

        // Form Input IMPRS
        $routes->get('imprs', 'LoadModuleForminputImprs::index');
        $routes->post('imprs/get-indicators', 'LoadModuleForminputImprs::get_indicators');
        $routes->post('imprs/get-indicator-detail', 'LoadModuleForminputImprs::get_indicator_detail');
        $routes->post('imprs/save', 'LoadModuleForminputImprs::save');
        $routes->post('imprs/delete', 'LoadModuleForminputImprs::delete');
        $routes->post('imprs/validasi', 'LoadModuleForminputImprs::validasi');
        $routes->post('imprs/get-riwayat', 'LoadModuleForminputImprs::get_riwayat');
        $routes->post('imprs/get-daily-detail', 'LoadModuleForminputImprs::get_daily_detail');
        $routes->post('imprs/check-input-allowed', 'LoadModuleForminputImprs::check_input_allowed');
        $routes->post('imprs/request-approval', 'LoadModuleForminputImprs::request_approval');
        $routes->post('imprs/check-request-status', 'LoadModuleForminputImprs::check_request_status');

        // Form Input IMPUNIT
        $routes->get('impunit', 'LoadModuleForminputImpunit::index');
        $routes->post('impunit/get-indicators', 'LoadModuleForminputImpunit::get_indicators');
        $routes->post('impunit/get-indicator-detail', 'LoadModuleForminputImpunit::get_indicator_detail');
        $routes->post('impunit/save', 'LoadModuleForminputImpunit::save');
        $routes->post('impunit/delete', 'LoadModuleForminputImpunit::delete');
        $routes->post('impunit/validasi', 'LoadModuleForminputImpunit::validasi');
        $routes->post('impunit/get-riwayat', 'LoadModuleForminputImpunit::get_riwayat');
        $routes->post('impunit/get-daily-detail', 'LoadModuleForminputImpunit::get_daily_detail');
        $routes->post('impunit/check-input-allowed', 'LoadModuleForminputImpunit::check_input_allowed');
        $routes->post('impunit/request-approval', 'LoadModuleForminputImpunit::request_approval');
        $routes->post('impunit/check-request-status', 'LoadModuleForminputImpunit::check_request_status');

        // Form Input IKP
        $routes->get('ikp', 'LoadModuleForminputIkp::index');
        $routes->post('ikp/get-indicators', 'LoadModuleForminputIkp::get_indicators');
        $routes->post('ikp/get-indicator-detail', 'LoadModuleForminputIkp::get_indicator_detail');
        $routes->post('ikp/save', 'LoadModuleForminputIkp::save');
        $routes->post('ikp/delete', 'LoadModuleForminputIkp::delete');
        $routes->post('ikp/validasi', 'LoadModuleForminputIkp::validasi');
        $routes->post('ikp/get-riwayat', 'LoadModuleForminputIkp::get_riwayat');
        $routes->post('ikp/get-daily-detail', 'LoadModuleForminputIkp::get_daily_detail');
        $routes->post('ikp/check-input-allowed', 'LoadModuleForminputIkp::check_input_allowed');
        $routes->post('ikp/request-approval', 'LoadModuleForminputIkp::request_approval');
        $routes->post('ikp/check-request-status', 'LoadModuleForminputIkp::check_request_status');

        // Approval
        $routes->get('approval/requests-list', 'Approval::requests_list');
        $routes->post('approval/ajax-get-requests-data', 'Approval::ajaxGetRequestsData');
        $routes->post('approval/ajax-get-all-requests-data', 'Approval::ajaxGetAllRequestsData');
        $routes->post('approval/ajax-approve-request', 'Approval::ajaxApproveRequest');
        $routes->post('approval/ajax-reject-request', 'Approval::ajaxRejectRequest');
        $routes->get('approval/(:any)', 'Approval::index/$1');
        $routes->post('approval/ajax-get-data', 'Approval::ajaxGetData');
        $routes->post('approval/ajax-get-departments', 'Approval::ajaxGetDepartments');
        $routes->post('approval/ajax-get-recap', 'Approval::ajaxGetRecap');
        $routes->post('approval/ajax-approve', 'Approval::ajaxApprove');

        // Validation
        $routes->get('validation/(:any)/form', 'Validation::form/$1');
        $routes->post('validation/(:any)/save', 'Validation::save/$1');
        $routes->get('validation/(:any)', 'Validation::index/$1');

        // Trash
        $routes->get('trash/(:any)', 'Trash::index/$1');
        $routes->post('trash/ajax-get-data', 'Trash::ajaxGetData');
        $routes->post('trash/ajax-permanent-delete', 'Trash::ajaxPermanentDelete');

    // Rekap Laporan INM
    $routes->get('rekap-laporan-inm', 'RekapLaporanInm::index');
    $routes->post('rekap-laporan-inm/ajax_rekap_inm', 'RekapLaporanInm::getAjaxDataRekapInm');
    $routes->post('rekap-laporan-inm/ajax-detail-inm', 'RekapLaporanInm::getAjaxDataRekapInmDetail');
    $routes->get('rekap-laporan-inm/export', 'RekapLaporanInm::exportExcel');
    $routes->get('rekap-laporan-inm/export-indicator/(:num)', 'RekapLaporanInm::exportExcelIndicator/$1');
    $routes->get('rekap-laporan-inm/detail/(:num)', 'RekapLaporanInm::viewDetailInm/$1');
    $routes->post('rekap-laporan-inm/ajax-daily-detail', 'RekapLaporanInm::getAjaxDailyDetail');

    // Rekap Periode INM (Triwulan/Semester/Tahun)
    $routes->get('rekap-periode-inm', 'RekapPeriodeInm::index');
    $routes->post('rekap-periode-inm/ajax_inm-(:num)', 'RekapPeriodeInm::getAjaxRekapPeriode/$1');
    $routes->get('rekap-periode-inm/export', 'RekapPeriodeInm::exportExcel');

    // Grafik INM
    $routes->get('grafik-inm', 'GrafikInm::index');
    $routes->post('grafik-inm/data', 'GrafikInm::getDataGrafik');
    $routes->post('grafik-inm/indicators', 'GrafikInm::getIndicatorsByYear');

    // Grafik IMPRS
    $routes->get('grafik-imprs', 'GrafikImprs::index');
    $routes->post('grafik-imprs/indicators', 'GrafikImprs::getIndicatorsByYear');
    $routes->post('grafik-imprs/data', 'GrafikImprs::getDataGrafik');

    // Grafik IMPUnit
    $routes->get('grafik-impunit', 'GrafikImpunit::index');
    $routes->post('grafik-impunit/indicators', 'GrafikImpunit::getIndicatorsByYear');
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
    $routes->post('rekap-laporan-imprs/ajax-daily-detail-imprs', 'RekapLaporanImprs::getAjaxDailyDetail');

    // Rekap Laporan IKP
    $routes->get('rekap-laporan-ikp', 'RekapLaporanIkp::index');
    $routes->post('rekap-laporan-ikp/ajax_rekap_ikp', 'RekapLaporanIkp::getAjaxDataRekapIkp');
    $routes->post('rekap-laporan-ikp/ajax-detail-ikp', 'RekapLaporanIkp::getAjaxDataRekapIkpDetail');
    $routes->get('rekap-laporan-ikp/export', 'RekapLaporanIkp::exportExcel');
    $routes->get('rekap-laporan-ikp/export-indicator/(:num)', 'RekapLaporanIkp::exportExcelIndicator/$1');
    $routes->get('rekap-laporan-ikp/detail/(:num)', 'RekapLaporanIkp::viewDetailIkp/$1');
    $routes->post('rekap-laporan-ikp/ajax-daily-detail-ikp', 'RekapLaporanIkp::getAjaxDailyDetail');

        // Rekap Laporan IMPUnit
        $routes->get('rekap-laporan-impunit', 'RekapLaporanImpunit::index');
        $routes->post('rekap-laporan-impunit/ajax_rekap_impunit', 'RekapLaporanImpunit::getAjaxDataRekapImpunit');
        $routes->post('rekap-laporan-impunit/ajax-detail-impunit', 'RekapLaporanImpunit::getAjaxDataRekapImpunitDetail');
        $routes->post('rekap-laporan-impunit/ajax-daily-detail-impunit', 'RekapLaporanImpunit::getAjaxDailyDetail');
        $routes->get('rekap-laporan-impunit/export', 'RekapLaporanImpunit::exportExcel');
        $routes->get('rekap-laporan-impunit/export-indicator/(:num)', 'RekapLaporanImpunit::exportExcelIndicator/$1');
        $routes->get('rekap-laporan-impunit/detail/(:num)', 'RekapLaporanImpunit::viewDetailImpunit/$1');

        // Data Indikator Management (CRUD for quality_indicator table)
        $routes->get('data-indikator/(:any)', 'DataIndikator::index/$1');
        $routes->post('data-indikator/ajax-get-data', 'DataIndikator::ajaxGetData');
        $routes->post('data-indikator/get-detail', 'DataIndikator::getDetail');
        $routes->post('data-indikator/save', 'DataIndikator::save');
        $routes->post('data-indikator/delete', 'DataIndikator::delete');
        $routes->post('data-indikator/restore', 'DataIndikator::restore');
        $routes->post('data-indikator/ajax-get-numdenum', 'DataIndikator::ajaxGetNumDenum');
        $routes->post('data-indikator/get-numdenum-detail', 'DataIndikator::getNumDenumDetail');
        $routes->post('data-indikator/save-numdenum', 'DataIndikator::saveNumDenum');
        $routes->post('data-indikator/delete-numdenum', 'DataIndikator::deleteNumDenum');

        // Manajemen Staf
        $routes->get('staf', 'Staff::index');
        $routes->post('staf/ajax-get-data', 'Staff::ajaxGetData');
        $routes->get('staf/create', 'Staff::create');
        $routes->post('staf/store', 'Staff::store');
        $routes->get('staf/edit/(:num)', 'Staff::edit/$1');
        $routes->post('staf/update/(:num)', 'Staff::update/$1');
        $routes->post('staf/delete/(:num)', 'Staff::delete/$1');
        $routes->post('staf/toggle-disable/(:num)', 'Staff::toggleDisable/$1');
        $routes->post('staf/toggle-online/(:num)', 'Staff::toggleOnline/$1');

        // Manajemen Unit / Bagian
        $routes->get('unit', 'Department::index');
        $routes->post('unit/ajax-get-data', 'Department::ajaxGetData');
        $routes->get('unit/create', 'Department::create');
        $routes->post('unit/store', 'Department::store');
        $routes->get('unit/edit/(:num)', 'Department::edit/$1');
        $routes->post('unit/update/(:num)', 'Department::update/$1');
        $routes->post('unit/delete/(:num)', 'Department::delete/$1');
        $routes->post('unit/toggle-disable/(:num)', 'Department::toggleDisable/$1');
    });

    // ========== IKPRS ==========
    $routes->group('ikprs', ['filter' => 'auth'], function ($routes) {
    // Dashboard
    $routes->get('/', 'Ikprs::index');
    $routes->get('menu', 'Ikprs::ikprs');

    // Form Input
    $routes->get('_form_add_ikp', 'Ikprs::formAddIkp');
    $routes->match(['GET', 'POST'], 'form_pending', 'Ikprs::formPending');
    $routes->match(['GET', 'POST'], 'form_send', 'Ikprs::formSend');
    $routes->match(['GET', 'POST'], 'form_inbox_karu', 'Ikprs::formInbox_karu');
    $routes->match(['get', 'post'], 'form_info', 'Ikprs::formInfo');
    $routes->get('hapus_test_notif', 'Ikprs::hapusTestNotif');
    $routes->get('test_wa', 'Ikprs::testWhatsApp');
    $routes->get('test_wa_template', 'Ikprs::testWATemplate');
    $routes->get('waMonitoring', 'Ikprs::waMonitoring');
    $routes->get('wa-monitoring', 'Ikprs::waMonitoring');
    $routes->post('waRetry', 'Ikprs::waRetry');
    $routes->post('wa-retry', 'Ikprs::waRetry');
    
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