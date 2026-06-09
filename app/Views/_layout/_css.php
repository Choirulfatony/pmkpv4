<?php
/**
 * SIIMUT _css.php
 *
 * Toggle SIIMUT_OFFLINE_MODE in app/Config/Constants.php (or via .env):
 *   - true  = load assets from assets/adminlte/* (offline)
 *   - false = load from CDN (online)
 *
 * Default: true (offline).
 */
$offline = defined('SIIMUT_OFFLINE_MODE') ? SIIMUT_OFFLINE_MODE : true;

$asset = function (string $cdnUrl, string $localPath) use ($offline): string {
    return $offline ? base_url($localPath) : $cdnUrl;
};

// CSS
$BOOTSTRAP_CSS    = $asset('https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css',                                 'assets/adminlte/css/bootstrap.min.css');
$FONTAWESOME_CSS  = $asset('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css',                              'assets/adminlte/css/fontawesome.all.min.css');
$BOOTSTRAP_ICONS  = $asset('https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css',                            'assets/adminlte/css/bootstrap-icons.min.css');
$TEMPUS_V6_CSS    = $asset('https://cdn.jsdelivr.net/npm/@eonasdan/tempus-dominus@6.9.4/dist/css/tempus-dominus.min.css',            'assets/adminlte/plugins/tempus-dominus/tempus-dominus.min.css');
$TEMPUS_V4_CSS    = $asset('https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/css/tempusdominus-bootstrap-4.min.css', 'assets/adminlte/plugins/tempusdominus-bootstrap-4/tempusdominus-bootstrap-4.min.css');
$SELECT2_CSS      = $asset('https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css',                               'assets/adminlte/css/select2.min.css');
$SELECT2_THEME    = $asset('https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css',    'assets/adminlte/css/select2-bootstrap-5-theme.min.css');
$FONTSOURCE_CSS   = $asset('https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css',                                'assets/adminlte/plugins/fontsource/source-sans-3.css');
$FLATPICKR_CSS    = $asset('https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css',                                          'assets/adminlte/plugins/flatpickr/flatpickr.min.css');
$BS_STEPPER_CSS   = $asset('https://cdn.jsdelivr.net/npm/bs-stepper/dist/css/bs-stepper.min.css',                                    'assets/adminlte/plugins/bs-stepper/bs-stepper.min.css');
$TOASTR_CSS       = $asset('https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css',                                 'assets/adminlte/css/toastr.min.css');
$OVERLAY_CSS      = $asset('https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/styles/overlayscrollbars.min.css',                 'assets/adminlte/css/overlayscrollbars.min.css');
$DATATABLES_CSS   = $asset('https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css',                                    'assets/adminlte/css/dataTables.bootstrap5.min.css');
$ADMINLTE_CSS     = $asset('https://cdn.jsdelivr.net/npm/admin-lte@4.0.0-beta3/dist/css/adminlte.min.css',                            'assets/adminlte/css/adminlte.min.css');
?>
<!-- Bootstrap 5 -->
<link rel="stylesheet" href="<?= $BOOTSTRAP_CSS ?>">

<!-- Font Awesome -->
<link rel="stylesheet" href="<?= $FONTAWESOME_CSS ?>">

<!-- Bootstrap Icons -->
<link href="<?= $BOOTSTRAP_ICONS ?>" rel="stylesheet">

<!-- Tempus Dominus v6 (current) -->
<link rel="stylesheet" href="<?= $TEMPUS_V6_CSS ?>">

<!-- Tempus Dominus v4 (legacy fallback) -->
<link rel="stylesheet" href="<?= $TEMPUS_V4_CSS ?>">

<!-- Select2 -->
<link href="<?= $SELECT2_CSS ?>" rel="stylesheet">

<!-- Select2 Bootstrap 5 Theme -->
<link href="<?= $SELECT2_THEME ?>" rel="stylesheet">

<!-- Fonts -->
<link rel="stylesheet" href="<?= $FONTSOURCE_CSS ?>">

<!-- Flatpickr -->
<link rel="stylesheet" href="<?= $FLATPICKR_CSS ?>">

<!-- bs-Stepper -->
<link rel="stylesheet" href="<?= $BS_STEPPER_CSS ?>">

<!-- Toastr -->
<link rel="stylesheet" href="<?= $TOASTR_CSS ?>">

<!-- OverlayScrollbars -->
<link rel="stylesheet" href="<?= $OVERLAY_CSS ?>" crossorigin="anonymous">

<!-- DataTables -->
<link rel="stylesheet" href="<?= $DATATABLES_CSS ?>">

<!-- AdminLTE 4 -->
<link rel="stylesheet" href="<?= $ADMINLTE_CSS ?>">

<!-- ============================================================
     SIIMUT MODULE STYLES (centralized)
     - Form variants use CSS variables: --siimut-color-1/2/3
     - INM (green) by default; .form-imprs-header / .form-impunit-header override
     ============================================================ -->
<style>
:root {
    --siimut-color-1: #28a745;
    --siimut-color-2: #1e7e34;
    --siimut-color-3: #145523;
    --siimut-color-rgb: 40, 167, 69;
    --siimut-cell-target-bg: rgba(40, 167, 69, 0.9);
    --siimut-cell-empty-bg: rgba(255, 193, 7, 0.9);
    --siimut-cell-fail-bg: rgba(220, 53, 69, 0.9);
}
.form-imprs-header,
.page-imprs {
    --siimut-color-1: #007bff;
    --siimut-color-2: #0056b3;
    --siimut-color-3: #004494;
    --siimut-color-rgb: 0, 123, 255;
}
.form-impunit-header,
.page-impunit {
    --siimut-color-1: #17a2b8;
    --siimut-color-2: #0f7c8f;
    --siimut-color-3: #0a5d6b;
    --siimut-color-rgb: 23, 162, 184;
}
.header-row {
    background: linear-gradient(50deg, red, orange, yellow, green, blue, indigo, violet);
    background-size: 400% 400%;
    animation: rgbAnimation 500s ease infinite;
    color: white;
    padding: 10px 20px;
    border-radius: 1px;
    text-decoration: none;
}
@keyframes rgbAnimation {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}
#table-indikator_wrapper .btn-group { gap: 2px; }
#table-indikator_wrapper .btn-group .btn { padding: 0.2rem 0.4rem; font-size: 0.75rem; line-height: 1.2; }
#table-indikator_wrapper td { vertical-align: middle; }
#table-indikator_wrapper th { white-space: nowrap; }
@media (max-width: 768px) {
    #table-indikator_wrapper .btn-group .btn { padding: 0.15rem 0.3rem; font-size: 0.7rem; }
    #table-indikator_wrapper td,
    #table-indikator_wrapper th { font-size: 0.8rem; padding: 0.3rem 0.4rem; }
}
.btn-xs { padding: 0.1rem 0.3rem; font-size: 0.7rem; line-height: 1.2; }
.is-invalid { border-color: #dc3545 !important; box-shadow: 0 0 0 0.15rem rgba(220, 53, 69, 0.25) !important; }
.btn-group-xs > .btn { padding: 0.1rem 0.3rem; font-size: 0.7rem; line-height: 1.2; }
.table-sm > :not(caption) > * > * { padding: 0.2rem 0.3rem; }
.form-inm-header,
.form-imprs-header,
.form-impunit-header {
    color: white;
    padding: 20px;
    border-radius: 10px;
    margin-bottom: 20px;
}
.form-inm-header { background: linear-gradient(135deg, var(--siimut-color-1) 0%, var(--siimut-color-2) 100%); }
.form-imprs-header { background: linear-gradient(135deg, var(--siimut-color-1) 0%, var(--siimut-color-2) 100%); }
.form-impunit-header { background: linear-gradient(135deg, var(--siimut-color-1) 0%, var(--siimut-color-2) 100%); }
.card-form-inm { border: none; border-radius: 10px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1); }
.card-form-inm .card-header {
    background: var(--bs-tertiary-bg);
    border-bottom: 2px solid var(--siimut-color-1);
    font-weight: bold;
}
.btn-inm-primary,
.btn-imprs-primary,
.btn-impunit-primary {
    background: linear-gradient(135deg, var(--siimut-color-1) 0%, var(--siimut-color-2) 100%);
    border: none;
    color: white;
}
.btn-inm-primary:hover,
.btn-imprs-primary:hover,
.btn-impunit-primary:hover {
    background: linear-gradient(135deg, var(--siimut-color-2) 0%, var(--siimut-color-3) 100%);
    color: white;
}
.form-control:focus, .form-select:focus {
    border-color: var(--siimut-color-1);
    box-shadow: 0 0 0 0.2rem rgba(var(--siimut-color-rgb), 0.25);
}
.modal-header.modal-inm,
.modal-header.modal-imprs,
.modal-header.modal-impunit {
    background: linear-gradient(135deg, var(--siimut-color-1) 0%, var(--siimut-color-2) 100%);
    color: white;
}
.modal-inm .btn-close,
.modal-imprs .btn-close,
.modal-impunit .btn-close { filter: brightness(0) invert(1); }
.table-wrap { overflow-x: auto; max-width: 100%; border: 1px solid #dee2e6; border-radius: 8px; }
.table-inm-inm, .table-inm-imprs, .table-inm-impunit {
    margin-bottom: 0;
    border-collapse: separate;
    border-spacing: 0;
}
.table-inm-inm > thead, .table-inm-imprs > thead, .table-inm-impunit > thead {
    background: linear-gradient(135deg, var(--siimut-color-1) 0%, var(--siimut-color-2) 100%);
    color: white;
}
.table-inm-inm > thead th, .table-inm-imprs > thead th, .table-inm-impunit > thead th {
    position: sticky;
    top: 0;
    z-index: 2;
    background: inherit;
    border: 1px solid rgba(255,255,255,0.2);
    font-size: 13px;
    padding: 8px 6px;
    text-align: center;
    white-space: nowrap;
}
.table-inm-inm > thead th.fixed-col,
.table-inm-imprs > thead th.fixed-col,
.table-inm-impunit > thead th.fixed-col { position: sticky; left: 0; z-index: 3; background: var(--siimut-color-2); }
.table-inm-inm > thead th.fixed-col2,
.table-inm-imprs > thead th.fixed-col2,
.table-inm-impunit > thead th.fixed-col2 { position: sticky; z-index: 3; background: var(--siimut-color-2); }
.table-inm-inm tbody td, .table-inm-imprs tbody td, .table-inm-impunit tbody td {
    padding: 8px 6px;
    border: 1px solid #dee2e6;
    text-align: center;
    vertical-align: middle;
    font-size: 13px;
}
.table-inm-inm tbody td.fixed-col,
.table-inm-imprs tbody td.fixed-col,
.table-inm-impunit tbody td.fixed-col { position: sticky; left: 0; z-index: 1; background: white; }
.table-inm-inm tbody td.fixed-col2,
.table-inm-imprs tbody td.fixed-col2,
.table-inm-impunit tbody td.fixed-col2 { position: sticky; z-index: 1; background: white; }
.day-cell { cursor: pointer; min-width: 70px; transition: all 0.15s ease; }
.day-cell:hover { transform: scale(1.05); box-shadow: 0 2px 8px rgba(0,0,0,0.15); z-index: 1; position: relative; }
.legend-box { display: inline-block; width: 16px; height: 16px; border-radius: 3px; border: 1px solid #dee2e6; vertical-align: middle; margin-right: 4px; }
.cell-target { background-color: #c3e6cb !important; }
.cell-fail { background-color: #f8d7da !important; }
.cell-fail .fw-bold, .cell-fail .num-denum { color: #fff !important; }
.cell-empty { background-color: #e2e3e5 !important; }
td.day-cell.cell-inputable { cursor: pointer; }
td.day-cell.cell-inputable .fw-bold { color: #0d6efd !important; }
td.day-cell.cell-inputable .num-denum { color: #0d6efd !important; }
.cell-has-data { font-weight: 600; }
.cell-draft { background-color: #fff3cd !important; }
.cell-approved { background-color: #d4edda !important; }
.day-cell .num-denum { font-size: 11px; color: #6c757d; margin-top: 2px; }
.day-header { font-weight: 600; font-size: 13px; }
.table-inm-inm tbody tr.indicator-row:hover td,
.table-inm-imprs tbody tr.indicator-row:hover td,
.table-inm-impunit tbody tr.indicator-row:hover td { background-color: #e8f4fd; }
.table-inm-inm tbody tr.indicator-row:hover td.fixed-col,
.table-inm-inm tbody tr.indicator-row:hover td.fixed-col2,
.table-inm-imprs tbody tr.indicator-row:hover td.fixed-col,
.table-inm-imprs tbody tr.indicator-row:hover td.fixed-col2,
.table-inm-impunit tbody tr.indicator-row:hover td.fixed-col,
.table-inm-impunit tbody tr.indicator-row:hover td.fixed-col2 { background-color: #e8f4fd; }
.dataTables_length label, .dataTables_filter label { font-weight: normal; font-size: 13px; display: inline-flex; align-items: center; gap: 6px; }
.dataTables_length select { width: auto; display: inline-block; }
.dataTables_filter input { width: auto; display: inline-block; margin-left: 4px; }
.dataTables_info { font-size: 13px; padding-top: 4px; }
input[type="month"].form-control-sm { min-height: 31px; }
.chart-container { position: relative; height: 350px; background: var(--bs-body-bg); border-radius: 8px; padding: 15px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); }
.indicator-info { background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
.target-badge { background: rgba(255, 255, 255, 0.2); padding: 5px 12px; border-radius: 20px; font-size: 14px; }
.status-badge { padding: 5px 15px; border-radius: 20px; font-weight: bold; }
.status-tercap { background: #28a745; color: white; }
.status-tidak { background: #dc3545; color: white; }
.card-grafik { border: none; border-radius: 10px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1); margin-bottom: 20px; }
.card-grafik .card-header { background: var(--bs-tertiary-bg); border-bottom: 2px solid #28a745; font-weight: bold; color: var(--bs-body-color); }
[data-bs-theme="dark"] .select2-selection,
[data-bs-theme="dark"] .select2-container--bootstrap-5 .select2-selection,
[data-bs-theme="dark"] .select2-container--open .select2-selection { background-color: #2b3035 !important; border-color: #495057 !important; }
[data-bs-theme="dark"] .select2-selection__rendered,
[data-bs-theme="dark"] .select2-container--bootstrap-5 .select2-selection__rendered,
[data-bs-theme="dark"] #select2-indicator_id-container,
[data-bs-theme="dark"] #select2-tahun-container { color: #dee2e6 !important; background-color: transparent !important; }
[data-bs-theme="dark"] .select2-dropdown,
[data-bs-theme="dark"] .select2-container--bootstrap-5 .select2-dropdown { background-color: #2b3035 !important; border-color: #495057 !important; }
[data-bs-theme="dark"] .select2-results__option,
[data-bs-theme="dark"] .select2-container--bootstrap-5 .select2-results__option { color: #dee2e6 !important; }
[data-bs-theme="dark"] .select2-results__option--highlighted,
[data-bs-theme="dark"] .select2-results__option--highlighted[aria-selected] { background-color: #0d6efd !important; color: white !important; }
[data-bs-theme="dark"] .select2-selection__arrow b,
[data-bs-theme="dark"] .select2-selection--single .select2-selection__arrow::after { border-color: #dee2e6 transparent transparent transparent !important; }
.cell-target { background-color: rgba(41, 185, 92) !important; font-weight: bold; }
.cell-empty { background-color: rgba(255, 222, 60) !important; font-weight: bold; }
.cell-fail { background-color: rgba(220, 57, 57) !important; color: #fff !important; font-weight: bold; }
.cell-target *, .cell-fail * { color: #fff !important; }
.cell-empty * { color: #000 !important; }
.cell-clickable { cursor: pointer !important; position: relative; }
.cell-clickable:hover::after {
    content: "\f133";
    font-family: "Font Awesome 6 Free", "Font Awesome 5 Free", "Font Awesome 6 Pro";
    font-weight: 900;
    position: absolute;
    top: 2px;
    right: 4px;
    font-size: 10px;
    color: rgba(0, 0, 0, 0.3);
    opacity: 0.6;
}
.legend-dot { width: 12px; height: 12px; border-radius: 3px; display: inline-block; }
.badge-non-aktif { font-size: 10px; vertical-align: middle; background-color: #6c757d; color: #fff; padding: 2px 6px; border-radius: 4px; margin-left: 4px; }
[data-bs-theme="dark"] .badge-non-aktif { background-color: #495057; color: #dee2e6; }
#ajax_data_rekap td, #ajax_data_rekap th,
#ajax_detail td, #ajax_detail th,
#ajax_detail_imprs td, #ajax_detail_imprs th,
#ajax_detail_impunit td, #ajax_detail_impunit th,
#ajax_data_periode td, #ajax_data_periode th,
#ajax_data_periode_inm td, #ajax_data_periode_inm th,
#ajax_data_periode_imprs td, #ajax_data_periode_imprs th,
#ajax_data_periode_impunit td, #ajax_data_periode_impunit th {
    vertical-align: middle;
}
#ajax_data_rekap td, #ajax_data_rekap th,
#ajax_detail td, #ajax_detail th,
#ajax_detail_imprs td, #ajax_detail_imprs th,
#ajax_detail_impunit td, #ajax_detail_impunit th { font-size: 12px; white-space: nowrap; padding: 6px 4px !important; }
#ajax_data_rekap td, #ajax_data_rekap th { font-size: 12px; padding: 6px 4px !important; }
#ajax_data_periode td, #ajax_data_periode th,
#ajax_data_periode_inm td, #ajax_data_periode_inm th,
#ajax_data_periode_imprs td, #ajax_data_periode_imprs th,
#ajax_data_periode_impunit td, #ajax_data_periode_impunit th { font-size: 12px; text-align: center; padding: 12px 8px !important; white-space: nowrap; }
#ajax_detail th, #ajax_detail_imprs th { background-color: #28a745 !important; color: #fff; text-align: center; font-weight: 600; }
#ajax_detail_impunit th { background-color: #363636 !important; color: #fff; text-align: center; font-weight: 600; }
#ajax_data_periode_inm th, #ajax_data_periode th { background-color: #198754 !important; color: #fff; white-space: nowrap; }
#ajax_data_periode_imprs th, #ajax_data_periode_impunit th { background-color: #6C757D !important; color: #fff; white-space: nowrap; }
#ajax_data_rekap th { color: #fff; text-align: center; font-weight: 600; }
#ajax_data_rekap td a, #ajax_detail td a { color: #000; text-decoration: none; font-weight: 600; }
#ajax_data_rekap td a:hover, #ajax_detail td a:hover { text-decoration: underline; }
[data-bs-theme="dark"] #ajax_data_rekap td,
[data-bs-theme="dark"] #ajax_data_rekap th,
[data-bs-theme="dark"] #ajax_detail td,
[data-bs-theme="dark"] #ajax_detail th { color: #fff !important; }
[data-bs-theme="dark"] #ajax_data_rekap td a,
[data-bs-theme="dark"] #ajax_detail td a { color: #fff !important; }
[data-bs-theme="dark"] #ajax_data_rekap td a:hover { color: #80bdff !important; }
[data-bs-theme="dark"] #ajax_data_rekap td .text-muted { color: #adb5bd !important; }
[data-bs-theme="dark"] #ajax_data_rekap td .small { color: #ced4da !important; }
[data-bs-theme="dark"] #ajax_data_rekap td span#total { color: #fff !important; }
[data-bs-theme="dark"] #ajax_data_rekap td span#num,
[data-bs-theme="dark"] #ajax_data_rekap td span#denum { color: #ced4da !important; }
#ajax_data_rekap_inm td, #ajax_data_rekap_inm th { font-size: 12px; white-space: nowrap; padding: 6px 4px !important; }
#ajax_data_rekap_inm th { background-color: #28a745 !important; color: #fff; text-align: center; font-weight: 600; }
#ajax_data_rekap_inm td a { color: #000; text-decoration: none; font-weight: 600; }
#ajax_data_rekap_inm td a:hover { color: #007bff; text-decoration: underline; }
#ajax_data_rekap_imprs td, #ajax_data_rekap_imprs th { font-size: 12px; white-space: nowrap; padding: 6px 4px !important; }
#ajax_data_rekap_imprs th { background-color: #6C757D !important; color: #fff; text-align: center; font-weight: 600; }
#ajax_data_rekap_imprs td a { color: #000; text-decoration: none; font-weight: 600; }
#ajax_data_rekap_imprs td a:hover { color: #6C757D; text-decoration: underline; }
#ajax_data_rekap_impunit td, #ajax_data_rekap_impunit th { font-size: 12px; white-space: nowrap; padding: 6px 4px !important; }
#ajax_data_rekap_impunit th { background-color: #363636 !important; color: #fff; text-align: center; font-weight: 600; }
#ajax_data_rekap_impunit td a { color: #000; text-decoration: none; font-weight: 600; }
#ajax_data_rekap_impunit td a:hover { color: #363636; text-decoration: underline; }
#ajax_data_periode td:first-child,
#ajax_data_periode_inm td:first-child,
#ajax_data_periode_imprs td:first-child,
#ajax_data_periode_impunit td:first-child { text-align: left; white-space: nowrap; }
#daily-table td, #daily-table th { font-size: 12px; vertical-align: middle; text-align: center; padding: 6px 4px !important; }
.daily-tercapai { background-color: rgba(41, 185, 92) !important; font-weight: bold; }
.daily-tidak-tercapai { background-color: rgba(220, 57, 57) !important; color: #fff !important; font-weight: bold; }
.daily-tanpa-data { background-color: rgba(255, 222, 60) !important; font-weight: bold; }
.approve-header { background: linear-gradient(135deg, #6f42c1 0%, #5533a3 100%); color: white; padding: 20px; border-radius: 10px; margin-bottom: 20px; }
.card-approve { border: none; border-radius: 10px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1); }
.card-approve .card-header { background: var(--bs-tertiary-bg); border-bottom: 2px solid #6f42c1; font-weight: bold; }
.table-approve th { background: #f8f9fa; white-space: nowrap; font-size: 0.85rem; }
.table-approve td { font-size: 0.85rem; vertical-align: middle; }
.badge-draft { background-color: #fff3cd; color: #856404; }
.badge-approved { background-color: #d4edda; color: #155724; }
.recap-header { background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%); color: white; padding: 14px 18px; border-radius: 10px; margin-top: 20px; margin-bottom: 14px; }
.card-recap { border: none; border-radius: 10px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1); }
.card-recap .card-header { background: var(--bs-tertiary-bg); border-bottom: 2px solid #0d6efd; font-weight: bold; }
.table-recap th { background: #f8f9fa; white-space: nowrap; font-size: 0.78rem; text-align: center; padding: 6px 4px; position: sticky; top: 0; z-index: 5; }
.table-recap th:first-child, .table-recap th:nth-child(2) { text-align: left; }
.table-recap td { font-size: 0.8rem; vertical-align: middle; text-align: center; padding: 6px 4px; }
.table-recap td:first-child, .table-recap td:nth-child(2) { text-align: left; white-space: nowrap; }
.table-recap .month-cell { min-width: 62px; }
.table-recap .cell-has-draft { background-color: #fff8e1; font-weight: 600; }
.table-recap .cell-num-denum { font-size: 0.7rem; color: #6c757d; line-height: 1.1; margin-top: 2px; }
.table-recap .nilai-text { font-weight: 600; }
.table-recap .nilai-na { color: #adb5bd; }
#loadingOverlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(255,255,255,0.8); display: none; justify-content: center; align-items: center; z-index: 9999; }
#loadingOverlay.show { display: flex; }
.approve-stats { font-size: 0.9rem; }
.trash-header { background: linear-gradient(135deg, #dc3545 0%, #a71d2a 100%); color: white; padding: 20px; border-radius: 10px; margin-bottom: 20px; }
.card-trash { border: none; border-radius: 10px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1); }
.card-trash .card-header { background: var(--bs-tertiary-bg); border-bottom: 2px solid #dc3545; font-weight: bold; }
.table-trash th { background: #f8f9fa; white-space: nowrap; font-size: 0.85rem; }
.table-trash td { font-size: 0.85rem; vertical-align: middle; }
#loadingIndicator { display: none; justify-content: center; align-items: center; min-height: 300px; flex-direction: column; }
.badge-deleted { background-color: #f8d7da; color: #721c24; }
.dataTables_wrapper .dataTables_processing { display: none !important; }
table.dataTable { opacity: 1; transition: opacity 0.1s ease; }
table.dataTable.loading { opacity: 0.2; }
.dataTables_scrollBody { background-color: transparent !important; }
[data-bs-theme="dark"] .dataTables_scrollBody { background-color: transparent !important; }
.table-responsive { position: relative; }
.overlay-wrapper { position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: transparent; z-index: 9999; }
.overlay { position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: transparent; display: flex; justify-content: center; align-items: center; z-index: 9999; }
.loader { width: 3em; height: 3em; transform: rotate(165deg); }
.loader:before, .loader:after { content: ""; position: absolute; top: 50%; left: 50%; display: block; width: 1em; height: 1em; border-radius: 0.5em; transform: translate(-50%, -50%); }
.loader:before { animation: before8 2s infinite; }
.loader:after { animation: after6 2s infinite; }
@keyframes before8 {
    0% { width: 1em; box-shadow: 2em -1em rgba(225, 20, 98, 0.75), -2em 1em rgba(111, 202, 220, 0.75); }
    35% { width: 4em; box-shadow: 0 -1em rgba(225, 20, 98, 0.75), 0 1em rgba(111, 202, 220, 0.75); }
    70% { width: 1em; box-shadow: -2em -1em rgba(225, 20, 98, 0.75), 2em 1em rgba(111, 202, 220, 0.75); }
    100% { box-shadow: 2em -1em rgba(225, 20, 98, 0.75), -2em 1em rgba(111, 202, 220, 0.75); }
}
@keyframes after6 {
    0% { height: 1em; box-shadow: 1em 2em rgba(61, 184, 143, 0.75), -1em -2em rgba(233, 169, 32, 0.75); }
    35% { height: 4em; box-shadow: 1em 0 rgba(61, 184, 143, 0.75), -1em 0 rgba(233, 169, 32, 0.75); }
    70% { height: 1em; box-shadow: 1em -2em rgba(61, 184, 143, 0.75), -1em 2em rgba(233, 169, 32, 0.75); }
    100% { box-shadow: 1em 2em rgba(61, 184, 143, 0.75), -1em -2em rgba(233, 169, 32, 0.75); }
}
</style>
