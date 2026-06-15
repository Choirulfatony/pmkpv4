<?php

namespace App\Controllers;

use App\Models\DashboardModel;
use App\Models\SiimutMenuModel;

class Dashboard extends AppController
{
    public function index()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/auth');
        }

        $this->disableCache();

        $role = session()->get('user_role');
        $departmentId = session()->get('department_id');

        $menuModel = new SiimutMenuModel();
        $menus = $menuModel->getMenuByRole($role);

        $tahun = (int) date('Y');
        $bulan = (int) date('m');

        $dashboardModel = new DashboardModel();

        $filterDept = $role === 'ADMINISTRATOR' ? null : $departmentId;

        $summary = $dashboardModel->getSummary($tahun, $filterDept);
        $progress = $dashboardModel->getInputProgress($tahun, $bulan, $filterDept);
        $targetStatus = $dashboardModel->getTargetStatus($tahun, $bulan, $filterDept);
        $deptWithoutInput = $dashboardModel->getDepartmentsWithoutInput($tahun, $bulan);
        $trend = $dashboardModel->getMonthlyTrend($tahun, $filterDept);

        $topInm = $dashboardModel->getTopBottomIndicators($tahun, $bulan, $filterDept, 'inm', 5);
        $topImprs = $dashboardModel->getTopBottomIndicators($tahun, $bulan, $filterDept, 'imprs', 5);
        $topImpunit = $dashboardModel->getTopBottomIndicators($tahun, $bulan, $filterDept, 'impunit', 5);

        $draftCounts = $dashboardModel->getDraftCounts($tahun, $bulan, $filterDept);

        return $this->render('dashboard/index', [
            'judul'    => 'Dashboard SIIMUT',
            'icon'     => '<i class="bi bi-speedometer"></i>',
            '_content' => view('siimut/dashboard_home', [
                'summary'            => $summary,
                'progress'           => $progress,
                'targetStatus'       => $targetStatus,
                'deptWithoutInput'   => $deptWithoutInput,
                'trend'              => $trend,
                'topInm'             => $topInm,
                'topImprs'           => $topImprs,
                'topImpunit'         => $topImpunit,
                'draftCounts'        => $draftCounts,
                'tahun'              => $tahun,
                'bulan'              => $bulan,
            ]),
            'menus'    => $menus,
        ]);
    }
}
