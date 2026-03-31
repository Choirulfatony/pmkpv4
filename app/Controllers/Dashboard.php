<?php

namespace App\Controllers;


class Dashboard extends AppController
{
    public function index()
    {
        if (! session()->get('logged_in')) {
            return redirect()->to('/auth');
        }

        $role = session()->get('user_role');
        if (!in_array($role, ['KOMITE', 'KARU'])) {
            return redirect()->to(site_url('ikprs/menu'))->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
        }

        $this->disableCache();

        $request = service('request');
        $db = db_connect();

        $tahunIni = date('Y');

        $tahunMin = $db->table('ikprssm_insiden')
            ->select('MIN(YEAR(selesai_at)) as min_tahun')
            ->where('status_laporan', 'SELESAI')
            ->where('selesai_at IS NOT NULL')
            ->get()
            ->getRow();

        $tahunMulai = $tahunMin && $tahunMin->min_tahun ? (int) $tahunMin->min_tahun : ($tahunIni - 4);

        $tahunFilter = $request->getGet('tahun') ?? $tahunIni;

        $filters = [
            'tahun'     => $tahunFilter,
            'triwulan'  => $request->getGet('triwulan') ?? null,
            'semester'  => $request->getGet('semester') ?? null
        ];

        $labels = [];
        $displayStart = (int) $tahunFilter;
        $displayEnd = (int) $tahunFilter;
        $xAxisType = 'tahun';

        if ($filters['tahun'] == '' || $filters['tahun'] == null) {
            $displayStart = $tahunMulai;
            $displayEnd = $tahunIni;
            $xAxisType = 'tahun';
        }

        if ($filters['triwulan']) {
            $xAxisType = 'bulan';
            $triwulan = (int) $filters['triwulan'];
            $startMonth = ($triwulan - 1) * 3 + 1;
            $endMonth = $triwulan * 3;
            $labels = [];
            for ($m = $startMonth; $m <= $endMonth; $m++) {
                $labels[] = date('M', mktime(0, 0, 0, $m, 1));
            }
        } elseif ($filters['semester']) {
            $xAxisType = 'bulan';
            $semester = (int) $filters['semester'];
            $startMonth = $semester == 1 ? 1 : 7;
            $endMonth = $semester == 1 ? 6 : 12;
            $labels = [];
            for ($m = $startMonth; $m <= $endMonth; $m++) {
                $labels[] = date('M', mktime(0, 0, 0, $m, 1));
            }
        } elseif ($filters['tahun'] && $filters['tahun'] != '') {
            $xAxisType = 'bulan';
            for ($m = 1; $m <= 12; $m++) {
                $labels[] = date('M', mktime(0, 0, 0, $m, 1));
            }
        } else {
            for ($t = $displayStart; $t <= $displayEnd; $t++) {
                $labels[] = (string) $t;
            }
        }

        $jenisInsiden = ['KNC', 'KTD', 'KTC', 'KPC', 'Sentinel'];
        $datasets = [];

        foreach ($jenisInsiden as $jenis) {
            $dataPerPeriode = [];

            if ($filters['triwulan']) {
                $triwulan = (int) $filters['triwulan'];
                $startMonth = ($triwulan - 1) * 3 + 1;
                $endMonth = $triwulan * 3;
                for ($m = $startMonth; $m <= $endMonth; $m++) {
                    $count = $db->table('ikprssm_insiden')
                        ->where('jenis_insiden', $jenis)
                        ->where('status_laporan', 'SELESAI')
                        ->where('selesai_at IS NOT NULL')
                        ->where("MONTH(selesai_at) = {$m}")
                        ->where("YEAR(selesai_at) = {$displayStart}")
                        ->countAllResults();
                    $dataPerPeriode[] = $count;
                }
            } elseif ($filters['semester']) {
                $semester = (int) $filters['semester'];
                $startMonth = $semester == 1 ? 1 : 7;
                $endMonth = $semester == 1 ? 6 : 12;
                for ($m = $startMonth; $m <= $endMonth; $m++) {
                    $count = $db->table('ikprssm_insiden')
                        ->where('jenis_insiden', $jenis)
                        ->where('status_laporan', 'SELESAI')
                        ->where('selesai_at IS NOT NULL')
                        ->where("MONTH(selesai_at) = {$m}")
                        ->where("YEAR(selesai_at) = {$displayStart}")
                        ->countAllResults();
                    $dataPerPeriode[] = $count;
                }
            } elseif ($filters['tahun'] && $filters['tahun'] != '') {
                for ($m = 1; $m <= 12; $m++) {
                    $count = $db->table('ikprssm_insiden')
                        ->where('jenis_insiden', $jenis)
                        ->where('status_laporan', 'SELESAI')
                        ->where('selesai_at IS NOT NULL')
                        ->where("MONTH(selesai_at) = {$m}")
                        ->where("YEAR(selesai_at) = {$displayStart}")
                        ->countAllResults();
                    $dataPerPeriode[] = $count;
                }
            } else {
                for ($t = $displayStart; $t <= $displayEnd; $t++) {
                    $count = $db->table('ikprssm_insiden')
                        ->where('jenis_insiden', $jenis)
                        ->where('status_laporan', 'SELESAI')
                        ->where("selesai_at >= '{$t}-01-01' AND selesai_at <= '{$t}-12-31'")
                        ->countAllResults();
                    $dataPerPeriode[] = $count;
                }
            }

            $datasets[] = [
                'jenis' => $jenis,
                'data' => $dataPerPeriode
            ];
        }

        $chartData = [
            'labels' => $labels,
            'datasets' => $datasets,
        ];

        $gradingLabels = [];
        $gradingTypes = ['HIJAU', 'BIRU', 'KUNING', 'MERAH'];

        $getGradingData = function ($startMonth, $endMonth, $isYearly = false) use ($db, $displayStart, $gradingTypes) {
            $result = [];
            foreach ($gradingTypes as $g) {
                $dataArr = [];
                if ($isYearly) {
                    for ($t = $startMonth; $t <= $endMonth; $t++) {
                        $dataArr[] = (int) $db->table('ikprssm_insiden')
                            ->where('grading_final', $g)
                            ->where('status_laporan', 'SELESAI')
                            ->where("selesai_at >= '{$t}-01-01' AND selesai_at <= '{$t}-12-31'")
                            ->countAllResults();
                    }
                } else {
                    for ($m = $startMonth; $m <= $endMonth; $m++) {
                        $dataArr[] = (int) $db->table('ikprssm_insiden')
                            ->where('grading_final', $g)
                            ->where('status_laporan', 'SELESAI')
                            ->where('selesai_at IS NOT NULL')
                            ->where("MONTH(selesai_at) = {$m}")
                            ->where("YEAR(selesai_at) = {$displayStart}")
                            ->countAllResults();
                    }
                }
                $result[] = ['grading' => $g, 'data' => $dataArr];
            }
            return $result;
        };

        if ($filters['triwulan']) {
            $triwulan = (int) $filters['triwulan'];
            $startMonth = ($triwulan - 1) * 3 + 1;
            $endMonth = $triwulan * 3;
            $gradingLabels = [];
            for ($m = $startMonth; $m <= $endMonth; $m++) {
                $gradingLabels[] = date('M', mktime(0, 0, 0, $m, 1));
            }
            $gradingDatasets = $getGradingData($startMonth, $endMonth, false);
        } elseif ($filters['semester']) {
            $semester = (int) $filters['semester'];
            $startMonth = $semester == 1 ? 1 : 7;
            $endMonth = $semester == 1 ? 6 : 12;
            $gradingLabels = [];
            for ($m = $startMonth; $m <= $endMonth; $m++) {
                $gradingLabels[] = date('M', mktime(0, 0, 0, $m, 1));
            }
            $gradingDatasets = $getGradingData($startMonth, $endMonth, false);
        } elseif ($filters['tahun'] && $filters['tahun'] != '') {
            $gradingLabels = [];
            for ($m = 1; $m <= 12; $m++) {
                $gradingLabels[] = date('M', mktime(0, 0, 0, $m, 1));
            }
            $gradingDatasets = $getGradingData(1, 12, false);
        } else {
            $gradingLabels = [];
            for ($t = $displayStart; $t <= $displayEnd; $t++) {
                $gradingLabels[] = (string) $t;
            }
            $gradingDatasets = $getGradingData($displayStart, $displayEnd, true);
        }

        $gradingChartData = [
            'labels' => $gradingLabels,
            'datasets' => $gradingDatasets
        ];

        return $this->render('dashboard/index', [
            'judul'    => 'Dashboard IKPRS',
            'icon'     => '<i class="bi bi-clipboard-check"></i>',
            'chartData' => $chartData,
            'gradingChartData' => $gradingChartData,
            'filters' => $filters,
            'tahunMulai' => $tahunMulai,
            'tahunIni' => $tahunIni,
            'xAxisType' => $xAxisType,
            '_content'  => view('ikprs/dashboard', [
                'chartData' => $chartData,
                'gradingChartData' => $gradingChartData,
                'filters' => $filters,
                'tahunMulai' => $tahunMulai,
                'tahunIni' => $tahunIni,
                'xAxisType' => $xAxisType
            ]),
        ]);
    }
}
