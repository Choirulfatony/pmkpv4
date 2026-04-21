<?php

namespace App\Models;

use CodeIgniter\Model;

class RekapLaporanImprsModel extends Model
{
    protected $table = 'local_quality_indicator';
    protected $primaryKey = 'indicator_id';
    protected $allowedFields = [];

    protected $column_search = [
        'indicator_element',
        'indicator_name_id',
    ];

    protected $column_order = [
        null,
        'indicator_element',
        null,
        null,
        null,
        null,
        null,
        null,
        null,
        null,
        null,
        null,
        null,
        null,
    ];

    private function _getAjaxDataRekapImprs(int $indicator, int $tahun, int $bulan)
    {
        $db = db_connect();
        $day = $this->getDaysInMonth($bulan, $tahun);
        $bulanx = str_pad($bulan, 2, '0', STR_PAD_LEFT);
        $builder = $db->table('local_quality_indicator_result lqir');

        $builder->select("
            lqi.indicator_category_id,
            lqi.indicator_id,
            lqi.indicator_element,

            SUM(lqir.result_numerator_value) AS num,
            SUM(lqir.result_denumerator_value) AS denum,

            ROUND(
                SUM(lqir.result_numerator_value) 
                / NULLIF(SUM(lqir.result_denumerator_value), 0)
                * lqi.indicator_factors, 
            4) AS total_value,

            CONCAT(
                ROUND(
                    SUM(lqir.result_numerator_value) 
                    / NULLIF(SUM(lqir.result_denumerator_value), 0)
                    * lqi.indicator_factors, 
                2),
                lqi.indicator_units
            ) AS total
        ");

        $builder->join('local_quality_indicator lqi', 'lqir.result_indicator_id = lqi.indicator_id', 'LEFT');

        $builder->where("lqir.result_period BETWEEN '{$tahun}-{$bulanx}-01' AND '{$tahun}-{$bulanx}-{$day}'");
        $builder->where('lqi.indicator_category_id', '5');
        $builder->where('lqi.indicator_record_status', 'A');
        $builder->where('lqi.indicator_id', $indicator);

        $builder->groupBy([
            'lqi.indicator_category_id',
            'lqi.indicator_id',
            'lqi.indicator_element',
            'lqi.indicator_factors',
            'lqi.indicator_units'
        ]);

        return $builder;
    }

    public function getAjaxDataRekapImprs(int $indicator, int $tahun, int $bulan)
    {
        $builder = $this->_getAjaxDataRekapImprs($indicator, $tahun, $bulan);
        return $builder->get()->getRow();
    }

    public function getAllMonthlyData(array $indicatorIds, int $tahun)
    {
        if (empty($indicatorIds)) {
            return [];
        }

        $db = db_connect();
        $builder = $db->table('local_quality_indicator_result lqir');

        $builder->select("
        lqi.indicator_id,
        MONTH(lqir.result_period) AS bulan,

        SUM(lqir.result_numerator_value) AS num,
        SUM(lqir.result_denumerator_value) AS denum,

        ROUND(
            SUM(lqir.result_numerator_value) 
            / NULLIF(SUM(lqir.result_denumerator_value), 0)
            * lqi.indicator_factors, 
        4) AS total_value,

        CONCAT(
            ROUND(
                SUM(lqir.result_numerator_value) 
                / NULLIF(SUM(lqir.result_denumerator_value), 0)
                * lqi.indicator_factors, 
            2),
            lqi.indicator_units
        ) AS total,

        lqi.indicator_units AS units
        ");

        $builder->join('local_quality_indicator lqi', 'lqir.result_indicator_id = lqi.indicator_id', 'LEFT');

        $builder->where("YEAR(lqir.result_period)", $tahun);
        $builder->where("lqi.indicator_category_id", '5');
        $builder->where("lqi.indicator_record_status", 'A');
        $builder->whereIn('lqi.indicator_id', $indicatorIds);

        $builder->groupBy([
            'lqi.indicator_id',
            'MONTH(lqir.result_period)',
            'lqi.indicator_factors',
            'lqi.indicator_units'
        ]);

        $results = $builder->get()->getResult();

        $data = [];
        foreach ($results as $row) {
            $key = $row->indicator_id . '_' . $row->bulan;
            $data[$key] = $row;
        }

        return $data;
    }

    public function getIndicatorImprs($post)
    {
        $db = db_connect();
        $builder = $db->table('local_quality_indicator_group');

        $builder->select("
            local_quality_indicator_group.group_indicator_id,
            local_quality_indicator_group.group_department_id,
            local_quality_indicator_group.group_period,
            local_quality_indicator.indicator_id,
            local_quality_indicator.indicator_element,
            local_quality_indicator.indicator_target,
            master_institution_department.department_id,
            master_institution_department.department_name,
            local_quality_indicator.indicator_units,
            local_quality_indicator.indicator_target_unit,
            local_quality_indicator.indicator_target_calculation AS operator,
            local_quality_indicator.indicator_factors AS factors
        ");

        $builder->join('local_quality_indicator', 'local_quality_indicator.indicator_id = local_quality_indicator_group.group_indicator_id');
        $builder->join('master_institution_department', 'master_institution_department.department_id = local_quality_indicator_group.group_department_id');

        $builder->where("local_quality_indicator.indicator_category_id", '5');
        $builder->where("local_quality_indicator.indicator_record_status", 'A');

        $vtahun = isset($post['vtahun']) ? (int) $post['vtahun'] : (int) date('Y');

        $availablePeriods = $this->getAvailableGroupPeriods();
        $usePeriod = in_array($vtahun, $availablePeriods) ? $vtahun : min($availablePeriods);

        $builder->groupStart();
        $builder->where("local_quality_indicator_group.group_period", $usePeriod);
        $builder->orWhere('local_quality_indicator_group.group_period', $usePeriod - 1);
        $builder->orWhere('local_quality_indicator_group.group_period', $usePeriod - 2);
        $builder->groupEnd();

        $userRole = session('user_role') ?? '';
        $userDepartmentId = session('department_id') ?? 0;

        if (!in_array($userRole, ['ADMINISTRATOR', 'KOMITE']) && $userDepartmentId > 0) {
            $builder->where('master_institution_department.department_id', $userDepartmentId);
        }

        $builder->groupBy('local_quality_indicator.indicator_id');

        if (isset($post['search']['value']) && !empty($post['search']['value'])) {
            $builder->groupStart();
            foreach ($this->column_search as $i => $item) {
                if ($i === 0) {
                    $builder->like($item, $post['search']['value']);
                } else {
                    $builder->orLike($item, $post['search']['value']);
                }
            }
            $builder->groupEnd();
        }

        if (isset($post['order'])) {
            $col = $this->column_order[$post['order'][0]['column']] ?? 'indicator_element';
            $dir = $post['order'][0]['dir'] ?? 'ASC';
            if ($col) {
                $builder->orderBy($col, $dir);
            }
        } else {
            $builder->orderBy('indicator_element', 'ASC');
        }

        if (isset($post['length']) && $post['length'] != -1) {
            $builder->limit($post['length'], $post['start'] ?? 0);
        }

        return $builder->get()->getResult();
    }

    public function getDetailByIdImprs(int $indicatorId)
    {
        $db = db_connect();

        $query = $db->query("
            SELECT 
                local_quality_indicator.indicator_id,
                local_quality_indicator.indicator_element,
                local_quality_indicator.indicator_target,
                local_quality_indicator.indicator_factors,
                local_quality_indicator.indicator_target_calculation,
                local_quality_indicator.indicator_units
            FROM local_quality_indicator
            WHERE local_quality_indicator.indicator_id = ?
            AND local_quality_indicator.indicator_category_id = '5' 
            AND local_quality_indicator.indicator_record_status = 'A'
        ", [$indicatorId]);

        return $query->getRow();
    }

    public function getAllDetailData(int $indicatorId, int $tahun)
    {
        $db = db_connect();
        $builder = $db->table('local_quality_indicator_result lqir');

        $builder->select("
            lqir.result_department_id,
            MONTH(lqir.result_period) AS bulan,

            SUM(lqir.result_numerator_value) AS num,
            SUM(lqir.result_denumerator_value) AS denum,

            ROUND(
                SUM(lqir.result_numerator_value) 
                / NULLIF(SUM(lqir.result_denumerator_value), 0)
                * 100,
            4) AS total_value,

            CONCAT(
                ROUND(
                    SUM(lqir.result_numerator_value) 
                    / NULLIF(SUM(lqir.result_denumerator_value), 0)
                    * 100,
                2),
            '%') AS total
        ");

        $builder->where('YEAR(lqir.result_period)', $tahun);
        $builder->where('lqir.result_indicator_id', $indicatorId);

        $builder->groupBy([
            'lqir.result_department_id',
            'MONTH(lqir.result_period)'
        ]);

        $results = $builder->get()->getResult();

        $data = [];
        foreach ($results as $row) {
            $key = $row->result_department_id . '_' . $row->bulan;
            $data[$key] = $row;
        }

        return $data;
    }

    private function getAvailableGroupPeriods(): array
    {
        $db = db_connect();
        $query = $db->query("SELECT DISTINCT group_period FROM local_quality_indicator_group WHERE group_record_status = 'A' ORDER BY group_period DESC");
        return array_column($query->getResult(), 'group_period');
    }

    private function getDaysInMonth(int $bulan, int $tahun): int
    {
        switch ($bulan) {
            case 1:
            case 3:
            case 5:
            case 7:
            case 8:
            case 10:
            case 12:
                return 31;
            case 4:
            case 6:
            case 9:
            case 11:
                return 30;
            case 2:
                return ($tahun % 4 == 0) ? 29 : 28;
            default:
                return 30;
        }
    }

    public function getMonthlyDataByIndicator(int $indicatorId, int $tahun): array
    {
        $db = db_connect();
        $builder = $db->table('local_quality_indicator_result lqir');

        $builder->select("
            MONTH(lqir.result_period) AS bulan,
            SUM(lqir.result_numerator_value) AS num,
            SUM(lqir.result_denumerator_value) AS denum
        ");

        $builder->join('local_quality_indicator lqi', 'lqir.result_indicator_id = lqi.indicator_id', 'LEFT');
        $builder->where('lqir.result_indicator_id', $indicatorId);
        $builder->where("YEAR(lqir.result_period)", $tahun);
        $builder->groupBy('MONTH(lqir.result_period)');

        $results = $builder->get()->getResult();

        $data = array_fill(1, 12, ['num' => 0, 'denum' => 0, 'nilai' => null]);

        $indicator = $this->getDetailByIdImprs($indicatorId);
        $target = (float) ($indicator->indicator_target ?? 0);
        $factors = (float) ($indicator->indicator_factors ?? 1);

        foreach ($results as $row) {
            $bulan = (int) $row->bulan;
            $num = (float) $row->num;
            $denum = (float) $row->denum;

            $nilai = $denum > 0 ? round(($num / $denum) * $factors, 2) : null;

            $data[$bulan] = [
                'num'    => $num,
                'denum'  => $denum,
                'nilai'  => $nilai
            ];
        }

        return $data;
    }

    public function getNilaiTriwulan(int $indicatorId, int $tahun): array
    {
        $monthly = $this->getMonthlyDataByIndicator($indicatorId, $tahun);

        $indicator = $this->getDetailByIdImprs($indicatorId);
        $target = (float) ($indicator->indicator_target ?? 0);
        $factors = (float) ($indicator->indicator_factors ?? 1);
        $operator = $indicator->indicator_target_calculation ?? '>=';

        $triwulan = [];
        for ($tw = 1; $tw <= 4; $tw++) {
            $bulanMulai = ($tw - 1) * 3 + 1;
            $bulanAkhir = $tw * 3;

            $totalNum = 0;
            $totalDenum = 0;
            for ($i = $bulanMulai; $i <= $bulanAkhir; $i++) {
                $totalNum += $monthly[$i]['num'];
                $totalDenum += $monthly[$i]['denum'];
            }

            $nilai = $totalDenum > 0 ? round(($totalNum / $totalDenum) * $factors, 2) : null;
            $tercap = $this->cekTercapai($nilai, $target, $operator);

            $triwulan[$tw] = ['nilai' => $nilai, 'num' => $totalNum, 'denum' => $totalDenum, 'tercap' => $tercap];
        }

        return $triwulan;
    }

    public function getNilaiSemester(int $indicatorId, int $tahun): array
    {
        $monthly = $this->getMonthlyDataByIndicator($indicatorId, $tahun);

        $indicator = $this->getDetailByIdImprs($indicatorId);
        $target = (float) ($indicator->indicator_target ?? 0);
        $factors = (float) ($indicator->indicator_factors ?? 1);
        $operator = $indicator->indicator_target_calculation ?? '>=';

        $semester = [];
        for ($sm = 1; $sm <= 2; $sm++) {
            $bulanMulai = ($sm - 1) * 6 + 1;
            $bulanAkhir = $sm * 6;

            $totalNum = 0;
            $totalDenum = 0;
            for ($i = $bulanMulai; $i <= $bulanAkhir; $i++) {
                $totalNum += $monthly[$i]['num'];
                $totalDenum += $monthly[$i]['denum'];
            }

            $nilai = $totalDenum > 0 ? round(($totalNum / $totalDenum) * $factors, 2) : null;
            $tercap = $this->cekTercapai($nilai, $target, $operator);

            $semester[$sm] = ['nilai' => $nilai, 'num' => $totalNum, 'denum' => $totalDenum, 'tercap' => $tercap];
        }

        return $semester;
    }

    public function getNilaiTahun(int $indicatorId, int $tahun): array
    {
        $monthly = $this->getMonthlyDataByIndicator($indicatorId, $tahun);

        $indicator = $this->getDetailByIdImprs($indicatorId);
        $target = (float) ($indicator->indicator_target ?? 0);
        $factors = (float) ($indicator->indicator_factors ?? 1);
        $operator = $indicator->indicator_target_calculation ?? '>=';

        $totalNum = 0;
        $totalDenum = 0;
        for ($i = 1; $i <= 12; $i++) {
            $totalNum += $monthly[$i]['num'];
            $totalDenum += $monthly[$i]['denum'];
        }

        $nilai = $totalDenum > 0 ? round(($totalNum / $totalDenum) * $factors, 2) : null;
        $tercap = $this->cekTercapai($nilai, $target, $operator);

        return ['nilai' => $nilai, 'num' => $totalNum, 'denum' => $totalDenum, 'tercap' => $tercap, 'target' => $target];
    }

    public function getNilaiPerTahun(int $indicatorId, int $tahun): array
    {
        $indicator = $this->getDetailByIdImprs($indicatorId);
        $target = (float) ($indicator->indicator_target ?? 0);
        $factors = (float) ($indicator->indicator_factors ?? 1);
        $operator = $indicator->indicator_target_calculation ?? '>=';

        $perTahun = [];
        $tahunMulai = $tahun - 4;

        for ($th = $tahunMulai; $th <= $tahun; $th++) {
            $monthly = $this->getMonthlyDataByIndicator($indicatorId, $th);

            $totalNum = 0;
            $totalDenum = 0;
            for ($i = 1; $i <= 12; $i++) {
                $totalNum += $monthly[$i]['num'];
                $totalDenum += $monthly[$i]['denum'];
            }

            $nilai = $totalDenum > 0 ? round(($totalNum / $totalDenum) * $factors, 2) : null;
            $tercap = $this->cekTercapai($nilai, $target, $operator);

            $perTahun[$th] = ['nilai' => $nilai, 'num' => $totalNum, 'denum' => $totalDenum, 'tercap' => $tercap];
        }

        return $perTahun;
    }

    private function cekTercapai($nilai, float $target, string $operator): bool
    {
        if ($nilai === null) {
            return false;
        }
        $angka = (float) preg_replace('/[^0-9.]/', '', $nilai);
        switch ($operator) {
            case '>=':
                return $angka >= $target;
            case '>':
                return $angka > $target;
            case '<=':
                return $angka <= $target;
            case '<':
                return $angka < $target;
            case '=':
                return $angka == $target;
            default:
                return $angka >= $target;
        }
    }

    private function getStatusPMKP($nilai, float $target, string $operator): string
    {
        if ($nilai === null) {
            return 'TIDAK ADA DATA';
        }

        $angka = (float) preg_replace('/[^0-9.]/', '', $nilai);
        switch ($operator) {
            case '>=':
                return $angka >= $target ? 'TERCAPAI' : 'TIDAK TERCAPAI';
            case '>':
                return $angka > $target ? 'TERCAPAI' : 'TIDAK TERCAPAI';
            case '<=':
                return $angka <= $target ? 'TERCAPAI' : 'TIDAK TERCAPAI';
            case '<':
                return $angka < $target ? 'TERCAPAI' : 'TIDAK TERCAPAI';
            case '=':
                return $angka == $target ? 'TERCAPAI' : 'TIDAK TERCAPAI';
            default:
                return $angka >= $target ? 'TERCAPAI' : 'TIDAK TERCAPAI';
        }
    }

    public function getRekapPeriode(int $tahun): array
    {
        $db = db_connect();

        $indicators = $db->table('local_quality_indicator')
            ->select('indicator_id, indicator_element, indicator_target, indicator_factors, indicator_units, indicator_target_calculation')
            ->where('indicator_category_id', '5')
            ->where('indicator_record_status', 'A')
            ->get()
            ->getResult();

        if (empty($indicators)) return [];

        $indicatorIds = array_column($indicators, 'indicator_id');
        $allMonthlyData = $this->getAllMonthlyData($indicatorIds, $tahun);

        $monthlyByIndicator = [];
        foreach ($allMonthlyData as $key => $data) {
            [$id, $bulan] = explode('_', $key);
            $monthlyByIndicator[$id][$bulan] = $data;
        }

        $results = [];

        foreach ($indicators as $indicator) {
            $id       = $indicator->indicator_id;
            $target   = (float) $indicator->indicator_target;
            $factor   = (float) $indicator->indicator_factors;
            $operator = $indicator->indicator_target_calculation ?? '>=';

            $monthly = $monthlyByIndicator[$id] ?? [];

            $num = array_fill(1, 12, 0);
            $den = array_fill(1, 12, 0);

            foreach ($monthly as $b => $val) {
                $num[$b] = (float) ($val->num ?? 0);
                $den[$b] = (float) ($val->denum ?? 0);
            }

            $hitung = function ($start, $end) use ($num, $den, $factor) {
                $totalNum = 0;
                $totalDen = 0;

                for ($i = $start; $i <= $end; $i++) {
                    $totalNum += $num[$i];
                    $totalDen += $den[$i];
                }

                if ($totalDen == 0) {
                    return [
                        'nilai' => null,
                        'num' => $totalNum,
                        'denum' => $totalDen
                    ];
                }

                return [
                    'nilai' => round(($totalNum / $totalDen) * $factor, 2),
                    'num' => $totalNum,
                    'denum' => $totalDen
                ];
            };

            $triwulan = [];
            for ($i = 1; $i <= 4; $i++) {
                $start = ($i - 1) * 3 + 1;
                $end   = $i * 3;
                $r = $hitung($start, $end);
                $triwulan[$i] = [
                    ...$r,
                    'status' => $this->getStatusPMKP($r['nilai'], $target, $operator)
                ];
            }

            $semester = [];
            for ($i = 1; $i <= 2; $i++) {
                $start = ($i - 1) * 6 + 1;
                $end   = $i * 6;
                $r = $hitung($start, $end);
                $semester[$i] = [
                    ...$r,
                    'status' => $this->getStatusPMKP($r['nilai'], $target, $operator)
                ];
            }

            $tahunR = $hitung(1, 12);

            $tercapTw = count(array_filter($triwulan, fn($t) => $t['status'] === 'TERCAPAI'));
            $tercapSm = count(array_filter($semester, fn($s) => $s['status'] === 'TERCAPAI'));

            $results[] = [
                'indicator_id' => $id,
                'indicator_element' => $indicator->indicator_element,
                'target' => $target,
                'satuan' => $indicator->indicator_units,
                'triwulan' => $triwulan,
                'semester' => $semester,
                'tahun' => [
                    ...$tahunR,
                    'status' => $this->getStatusPMKP($tahunR['nilai'], $target, $operator)
                ],
                'summary' => [
                    'tw_tercapai' => $tercapTw,
                    'sm_tercapai' => $tercapSm
                ]
            ];
        }

        return $results;
    }

    public function countAllRekapImprs($post = [])
    {
        $db = db_connect();
        $builder = $db->table('local_quality_indicator_group');

        $builder->join('local_quality_indicator', 'local_quality_indicator.indicator_id = local_quality_indicator_group.group_indicator_id');
        $builder->join('master_institution_department', 'master_institution_department.department_id = local_quality_indicator_group.group_department_id');

        $builder->where("local_quality_indicator.indicator_category_id", '5');
        $builder->where("local_quality_indicator.indicator_record_status", 'A');

        $vtahun = $post['vtahun'] ?? date('Y');
        $availablePeriods = $this->getAvailableGroupPeriods();
        $usePeriod = in_array($vtahun, $availablePeriods) ? $vtahun : min($availablePeriods);

        $builder->groupStart();
        $builder->where("local_quality_indicator_group.group_period", $usePeriod);
        $builder->orWhere('local_quality_indicator_group.group_period', $usePeriod - 1);
        $builder->orWhere('local_quality_indicator_group.group_period', $usePeriod - 2);
        $builder->groupEnd();

        $userRole = session('user_role') ?? '';
        $userDepartmentId = session('department_id') ?? 0;
        if (!in_array($userRole, ['ADMINISTRATOR', 'KOMITE']) && $userDepartmentId > 0) {
            $builder->where('master_institution_department.department_id', $userDepartmentId);
        }

        $builder->groupBy('local_quality_indicator.indicator_id');

        return $builder->countAllResults();
    }

    public function countFilteredRekapImprs($post = [])
    {
        $db = db_connect();
        $builder = $db->table('local_quality_indicator_group');

        $builder->join('local_quality_indicator', 'local_quality_indicator.indicator_id = local_quality_indicator_group.group_indicator_id');
        $builder->join('master_institution_department', 'master_institution_department.department_id = local_quality_indicator_group.group_department_id');

        $builder->where("local_quality_indicator.indicator_category_id", '5');
        $builder->where("local_quality_indicator.indicator_record_status", 'A');

        $vtahun = $post['vtahun'] ?? date('Y');
        $availablePeriods = $this->getAvailableGroupPeriods();
        $usePeriod = in_array($vtahun, $availablePeriods) ? $vtahun : min($availablePeriods);

        $builder->groupStart();
        $builder->where("local_quality_indicator_group.group_period", $usePeriod);
        $builder->orWhere('local_quality_indicator_group.group_period', $usePeriod - 1);
        $builder->orWhere('local_quality_indicator_group.group_period', $usePeriod - 2);
        $builder->groupEnd();

        $userRole = session('user_role') ?? '';
        $userDepartmentId = session('department_id') ?? 0;
        if (!in_array($userRole, ['ADMINISTRATOR', 'KOMITE']) && $userDepartmentId > 0) {
            $builder->where('master_institution_department.department_id', $userDepartmentId);
        }

        $builder->groupBy('local_quality_indicator.indicator_id');

        if (isset($post['search']['value']) && !empty($post['search']['value'])) {
            $builder->groupStart();
            foreach ($this->column_search as $i => $item) {
                if ($i === 0) {
                    $builder->like($item, $post['search']['value']);
                } else {
                    $builder->orLike($item, $post['search']['value']);
                }
            }
            $builder->groupEnd();
        }

        return $builder->get()->getNumRows();
    }

    public function getDepartmentsByIndicatorImprs(int $indicatorId, int $tahun, $post = [])
    {
        $db = db_connect();
        $builder = $db->table('local_quality_indicator_group');

        $builder->select('
            local_quality_indicator_group.group_department_id,
            master_institution_department.department_id,
            master_institution_department.department_name
        ');

        $builder->join('local_quality_indicator', 'local_quality_indicator.indicator_id = local_quality_indicator_group.group_indicator_id', 'left');
        $builder->join('master_institution_department', 'master_institution_department.department_id = local_quality_indicator_group.group_department_id', 'left');

        $builder->where("local_quality_indicator.indicator_category_id", '5');
        $builder->where("local_quality_indicator.indicator_record_status", 'A');
        $builder->where('local_quality_indicator.indicator_id', $indicatorId);

        $builder->groupStart();
        $builder->where("local_quality_indicator_group.group_period", $tahun);
        $builder->orWhere('local_quality_indicator_group.group_period', $tahun - 1);
        $builder->orWhere('local_quality_indicator_group.group_period', $tahun - 2);
        $builder->groupEnd();

        $userRole = session('user_role') ?? '';
        $userDepartmentId = session('department_id') ?? 0;
        if (!in_array($userRole, ['ADMINISTRATOR', 'KOMITE']) && $userDepartmentId > 0) {
            $builder->where('master_institution_department.department_id', $userDepartmentId);
        }

        $builder->groupBy('local_quality_indicator_group.group_department_id');

        return $builder->get()->getResult();
    }
}
