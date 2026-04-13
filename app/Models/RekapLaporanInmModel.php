<?php

namespace App\Models;

use CodeIgniter\Model;

class RekapLaporanInmModel extends Model
{
    protected $table = 'quality_indicator';
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

    // ==================== PRIVATE QUERY BUILDERS ====================

    private function _getAjaxDataRekapInm(int $indicator, int $tahun, int $bulan)
    {
        $db = db_connect();
        $day = $this->getDaysInMonth($bulan, $tahun);
        $bulanx = str_pad($bulan, 2, '0', STR_PAD_LEFT);
        $builder = $db->table('quality_indicator_result qir');

        $builder->select("
            qi.indicator_category_id,
            qi.indicator_id,
            qi.indicator_element,

            SUM(qir.result_numerator_value) AS num,
            SUM(qir.result_denumerator_value) AS denum,

            ROUND(
                SUM(qir.result_numerator_value) 
                / NULLIF(SUM(qir.result_denumerator_value), 0)
                * qi.indicator_factors, 
            4) AS total_value,

            CONCAT(
                ROUND(
                    SUM(qir.result_numerator_value) 
                    / NULLIF(SUM(qir.result_denumerator_value), 0)
                    * qi.indicator_factors, 
                2),
                qi.indicator_units
            ) AS total
        ");

        $builder->join('quality_indicator qi', 'qir.result_indicator_id = qi.indicator_id', 'LEFT');

        $builder->where("qir.result_period BETWEEN '{$tahun}-{$bulanx}-01' AND '{$tahun}-{$bulanx}-{$day}'");
        $builder->where('qi.indicator_category_id', '4');
        $builder->where('qi.indicator_record_status', 'A');
        $builder->where('qi.indicator_id', $indicator);

        $builder->groupBy([
            'qi.indicator_category_id',
            'qi.indicator_id',
            'qi.indicator_element',
            'qi.indicator_factors',
            'qi.indicator_units'
        ]);

        return $builder;
    }

    // ==================== PUBLIC METHODS ====================

    /**
     * Ambil data rekap INM per bulan
     */
    public function getAjaxDataRekapInmm(int $indicator, int $tahun, int $bulan)
    {
        $builder = $this->_getAjaxDataRekapInm($indicator, $tahun, $bulan);
        return $builder->get()->getRow();
    }

    /**
     * Ambil SEMUA data bulanan dalam 1 query (OPTIMIZED + CACHE)
     */
    public function getAllMonthlyData(array $indicatorIds, int $tahun)
    {
        if (empty($indicatorIds)) {
            return [];
        }

        // Query database
        $db = db_connect();
        $builder = $db->table('quality_indicator_result qir');

        $builder->select("
        qi.indicator_id,
        MONTH(qir.result_period) AS bulan,

        SUM(qir.result_numerator_value) AS num,
        SUM(qir.result_denumerator_value) AS denum,

        ROUND(
            SUM(qir.result_numerator_value) 
            / NULLIF(SUM(qir.result_denumerator_value), 0)
            * qi.indicator_factors, 
        4) AS total_value,

        CONCAT(
            ROUND(
                SUM(qir.result_numerator_value) 
                / NULLIF(SUM(qir.result_denumerator_value), 0)
                * qi.indicator_factors, 
            2),
            qi.indicator_units
        ) AS total,

        qi.indicator_units AS units
        ");

        $builder->join('quality_indicator qi', 'qir.result_indicator_id = qi.indicator_id', 'LEFT');

        $builder->where("YEAR(qir.result_period)", $tahun);
        $builder->where("qi.indicator_category_id", '4');
        $builder->where("qi.indicator_record_status", 'A');
        $builder->whereIn('qi.indicator_id', $indicatorIds);

        $builder->groupBy([
            'qi.indicator_id',
            'MONTH(qir.result_period)',
            'qi.indicator_factors',
            'qi.indicator_units'
        ]);

        $results = $builder->get()->getResult();

        log_message('error', 'DEBUG getAllMonthlyData: tahun=' . $tahun . ', indicators=' . json_encode($indicatorIds) . ', result_count=' . count($results));

        // Convert ke array associative: indicator_id_bulan => data
        $data = [];
        foreach ($results as $row) {
            $key = $row->indicator_id . '_' . $row->bulan;
            $data[$key] = $row;
        }

        return $data;
    }

    /**
     * Ambil indikator dengan pagination
     */
    public function getIndicatorInm($post)
    {
        $db = db_connect();
        $builder = $db->table('quality_indicator_group');

        $builder->select("
            quality_indicator_group.group_indicator_id,
            quality_indicator_group.group_department_id,
            quality_indicator_group.group_period,
            quality_indicator.indicator_id,
            quality_indicator.indicator_element,
            quality_indicator.indicator_target,
            master_institution_department.department_id,
            master_institution_department.department_name,
            quality_indicator.indicator_units,
            quality_indicator.indicator_target_unit,
            quality_indicator.indicator_target_calculation AS operator,
            quality_indicator.indicator_factors AS factors
        ");

        $builder->join('quality_indicator', 'quality_indicator.indicator_id = quality_indicator_group.group_indicator_id');
        $builder->join('master_institution_department', 'master_institution_department.department_id = quality_indicator_group.group_department_id');

        $builder->where("quality_indicator.indicator_category_id", '4');
        $builder->where("quality_indicator.indicator_record_status", 'A');

        $vtahun = isset($post['vtahun']) ? (int) $post['vtahun'] : (int) date('Y');

        // Cek available group_period - gunakan tahun dipilih jika ada, kalau tidak gunakan yang tersedia
        $availablePeriods = $this->getAvailableGroupPeriods();
        $usePeriod = in_array($vtahun, $availablePeriods) ? $vtahun : min($availablePeriods);

        $builder->groupStart();
        $builder->where("quality_indicator_group.group_period", $usePeriod);
        $builder->orWhere('quality_indicator_group.group_period', $usePeriod - 1);
        $builder->orWhere('quality_indicator_group.group_period', $usePeriod - 2);
        $builder->groupEnd();

        // Filter by user role
        $userRole = session('user_role') ?? '';
        $userDepartmentId = session('department_id') ?? 0;

        // ADMINISTRATOR & KOMITE → Buka semua
        // KENDALI_MUTU → Filter by department_id
        if (!in_array($userRole, ['ADMINISTRATOR', 'KOMITE']) && $userDepartmentId > 0) {
            $builder->where('master_institution_department.department_id', $userDepartmentId);
        }

        // GROUP BY indicator_id
        $builder->groupBy('quality_indicator.indicator_id');

        // Search filter
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

        // Order
        if (isset($post['order'])) {
            $col = $this->column_order[$post['order'][0]['column']] ?? 'indicator_element';
            $dir = $post['order'][0]['dir'] ?? 'ASC';
            if ($col) {
                $builder->orderBy($col, $dir);
            }
        } else {
            $builder->orderBy('indicator_element', 'ASC');
        }

        // Pagination
        if (isset($post['length']) && $post['length'] != -1) {
            $builder->limit($post['length'], $post['start'] ?? 0);
        }

        return $builder->get()->getResult();
    }

    /**
     * Hitung semua rekap INM (CACHE)
     */
    public function countAllRekapInm($post = [])
    {
        $vtahun = isset($post['vtahun']) ? (int) $post['vtahun'] : (int) date('Y');
        $userRole = session('user_role') ?? '';
        $userDepartmentId = session('department_id') ?? 0;

        // Cek cache
        $cache = \Config\Services::cache();
        $cacheKey = 'count_all_' . $vtahun . '_' . $userRole . '_' . $userDepartmentId;

        if ($cached = $cache->get($cacheKey)) {
            return $cached;
        }

        $db = db_connect();

        // Query sama dengan getIndicatorInm tapi hanya COUNT
        $query = $db->query("
            SELECT COUNT(*) as total FROM (
                SELECT DISTINCT quality_indicator.indicator_id
                FROM quality_indicator_group
                JOIN quality_indicator ON quality_indicator.indicator_id = quality_indicator_group.group_indicator_id
                JOIN master_institution_department ON master_institution_department.department_id = quality_indicator_group.group_department_id
                WHERE quality_indicator.indicator_category_id = '4'
                AND quality_indicator.indicator_record_status = 'A'
                AND (quality_indicator_group.group_period = ? 
                     OR quality_indicator_group.group_period = ? 
                     OR quality_indicator_group.group_period = ?)
                " . ((!in_array($userRole, ['ADMINISTRATOR', 'KOMITE']) && $userDepartmentId > 0) ? "AND master_institution_department.department_id = " . $userDepartmentId : "") . "
                GROUP BY quality_indicator.indicator_id
            ) as counted
        ", [$vtahun, $vtahun - 1, $vtahun - 2]);

        $count = $query->getRow()->total ?? 0;

        // Cache 10 menit
        $cache->save($cacheKey, $count, 600);

        return $count;
    }

    /**
     * Ambil semua ruangan untuk indicator tertentu
     */
    public function getDepartmentsByIndicator(int $indicatorId, int $tahun, $post = [])
    {
        $db = db_connect();

        $searchCondition = '';
        if (isset($post['search']['value']) && !empty($post['search']['value'])) {
            $searchValue = addslashes($post['search']['value']);
            $searchCondition = "AND master_institution_department.department_name LIKE '%{$searchValue}%'";
        }

        $limit = '';
        if (isset($post['length']) && $post['length'] != -1) {
            $start = isset($post['start']) ? (int) $post['start'] : 0;
            $length = (int) $post['length'];
            $limit = "LIMIT {$length} OFFSET {$start}";
        }

        $query = $db->query("
            SELECT DISTINCT 
                quality_indicator.indicator_id,
                quality_indicator.indicator_element,
                master_institution_department.department_id,
                master_institution_department.department_name,
                quality_indicator_group.group_indicator_id
            FROM quality_indicator_group
            JOIN quality_indicator ON quality_indicator.indicator_id = quality_indicator_group.group_indicator_id
            JOIN master_institution_department ON master_institution_department.department_id = quality_indicator_group.group_department_id
            WHERE quality_indicator.indicator_category_id = '4' 
            AND quality_indicator.indicator_record_status = 'A' 
            AND quality_indicator_group.group_record_status = 'A'
            AND quality_indicator_group.group_indicator_id = ?
            {$searchCondition}
            GROUP BY master_institution_department.department_id
            ORDER BY master_institution_department.department_name ASC
            {$limit}
        ", [$indicatorId]);

        return $query->getResult();
    }

    /**
     * Ambil detail indicator by ID (CI4 version of get_detail_byid_inm)
     */
    public function getDetailByIdInm(int $indicatorId)
    {
        $db = db_connect();

        $query = $db->query("
            SELECT 
                quality_indicator.indicator_id,
                quality_indicator.indicator_element,
                quality_indicator.indicator_target,
                quality_indicator.indicator_factors,
                quality_indicator.indicator_target_calculation,
                quality_indicator.indicator_units
            FROM quality_indicator
            WHERE quality_indicator.indicator_id = ?
            AND quality_indicator.indicator_category_id = '4' 
            AND quality_indicator.indicator_record_status = 'A'
        ", [$indicatorId]);

        return $query->getRow();
    }

    /**
     * Ambil semua data detail per ruangan dalam 1 query
     */
    public function getAllDetailData(int $indicatorId, int $tahun)
    {
        $cache = \Config\Services::cache();
        $cacheKey = 'detail_data_' . $indicatorId . '_' . $tahun;

        // Skip cache for debugging
        // if ($cached = $cache->get($cacheKey)) {
        //     return $cached;
        // }

        $db = db_connect();
        $builder = $db->table('quality_indicator_result qir');

        $builder->select("
            qir.result_department_id,
            MONTH(qir.result_period) AS bulan,

            SUM(qir.result_numerator_value) AS num,
            SUM(qir.result_denumerator_value) AS denum,

            ROUND(
                SUM(qir.result_numerator_value) 
                / NULLIF(SUM(qir.result_denumerator_value), 0)
                * 100,
            4) AS total_value,

            CONCAT(
                ROUND(
                    SUM(qir.result_numerator_value) 
                    / NULLIF(SUM(qir.result_denumerator_value), 0)
                    * 100,
                2),
            '%') AS total
        ");

        $builder->where('YEAR(qir.result_period)', $tahun);
        $builder->where('qir.result_indicator_id', $indicatorId);

        $builder->groupBy([
            'qir.result_department_id',
            'MONTH(qir.result_period)'
        ]);

        $results = $builder->get()->getResult();

        log_message('error', 'getAllDetailData: indicatorId=' . $indicatorId . ', tahun=' . $tahun . ', count=' . count($results));
        if (count($results) > 0) {
            log_message('error', 'getAllDetailData sample: dept=' . $results[0]->result_department_id . ', bulan=' . $results[0]->bulan . ', total=' . ($results[0]->total ?? 'NULL') . ', num=' . $results[0]->num);
        }

        $data = [];
        foreach ($results as $row) {
            $key = $row->result_department_id . '_' . $row->bulan;
            $data[$key] = $row;
        }

        $cache->save($cacheKey, $data, 600);
        return $data;
    }

    /**
     * Hitung jumlah ruangan untuk indicator tertentu
     */
    public function countDepartmentsByIndicator(int $indicatorId, int $tahun, $post = [])
    {
        $db = db_connect();

        $searchCondition = '';
        if (isset($post['search']['value']) && !empty($post['search']['value'])) {
            $searchValue = addslashes($post['search']['value']);
            $searchCondition = "AND master_institution_department.department_name LIKE '%{$searchValue}%'";
        }

        $query = $db->query("
            SELECT COUNT(DISTINCT master_institution_department.department_id) as total
            FROM quality_indicator_group
            JOIN quality_indicator ON quality_indicator.indicator_id = quality_indicator_group.group_indicator_id
            JOIN master_institution_department ON master_institution_department.department_id = quality_indicator_group.group_department_id
            WHERE quality_indicator_group.group_indicator_id = ?
            AND quality_indicator.indicator_category_id = '4' 
            AND quality_indicator.indicator_record_status = 'A'
            {$searchCondition}
        ", [$indicatorId]);

        return $query->getRow()->total ?? 0;
    }

    /**
     * Hitung rekap INM dengan filter
     */
    public function countFilteredRekapInm($post = [])
    {
        $vtahun = isset($post['vtahun']) ? (int) $post['vtahun'] : (int) date('Y');
        $userRole = session('user_role') ?? '';
        $userDepartmentId = session('department_id') ?? 0;

        $db = db_connect();

        // Search condition
        $searchCondition = '';
        if (isset($post['search']['value']) && !empty($post['search']['value'])) {
            $searchValue = $post['search']['value'];
            $searchCondition = "AND (quality_indicator.indicator_element LIKE '%{$searchValue}%' 
                               OR quality_indicator.indicator_name_id LIKE '%{$searchValue}%')";
        }

        // Query sama dengan getIndicatorInm tapi hanya COUNT
        $query = $db->query("
            SELECT COUNT(*) as total FROM (
                SELECT DISTINCT quality_indicator.indicator_id
                FROM quality_indicator_group
                JOIN quality_indicator ON quality_indicator.indicator_id = quality_indicator_group.group_indicator_id
                JOIN master_institution_department ON master_institution_department.department_id = quality_indicator_group.group_department_id
                WHERE quality_indicator.indicator_category_id = '4'
                AND quality_indicator.indicator_record_status = 'A'
                AND (quality_indicator_group.group_period = ? 
                     OR quality_indicator_group.group_period = ? 
                     OR quality_indicator_group.group_period = ?)
                " . ((!in_array($userRole, ['ADMINISTRATOR', 'KOMITE']) && $userDepartmentId > 0) ? "AND master_institution_department.department_id = " . $userDepartmentId : "") . "
                {$searchCondition}
                GROUP BY quality_indicator.indicator_id
            ) as counted
        ", [$vtahun, $vtahun - 1, $vtahun - 2]);

        return $query->getRow()->total ?? 0;
    }

    /**
     * Clear cache untuk refresh data
     */
    public function clearCache()
    {
        $cache = \Config\Services::cache();
        return true;
    }

    /**
     * Ambil available group_period dari database
     */
    private function getAvailableGroupPeriods(): array
    {
        $cache = \Config\Services::cache();
        $cacheKey = 'available_group_periods';

        if ($cached = $cache->get($cacheKey)) {
            return $cached;
        }

        $db = db_connect();
        $query = $db->query("SELECT DISTINCT group_period FROM quality_indicator_group WHERE group_record_status = 'A' ORDER BY group_period DESC");
        $periods = array_column($query->getResult(), 'group_period');

        $cache->save($cacheKey, $periods, 3600);
        return $periods;
    }

    /**
     * Ambil data rekap per Triwulan, Semester, dan Tahun
     */
    public function getRekapPeriode(int $tahun)
    {
        $db = db_connect();

        $indicators = $db->table('quality_indicator')
            ->select('indicator_id, indicator_element, indicator_target, indicator_factors, indicator_units, indicator_target_calculation')
            ->where('indicator_category_id', '4')
            ->where('indicator_record_status', 'A')
            ->get()
            ->getResult();

        if (empty($indicators)) return [];

        $indicatorIds = array_column($indicators, 'indicator_id');
        $allMonthlyData = $this->getAllMonthlyData($indicatorIds, $tahun);

        // mapping
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

            // init
            $num = array_fill(1, 12, 0);
            $den = array_fill(1, 12, 0);

            foreach ($monthly as $b => $val) {
                $num[$b] = (float) ($val->num ?? 0);
                $den[$b] = (float) ($val->denum ?? 0);
            }

            // ================= CORE PMKP =================
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

            // ================= TRI WULAN =================
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

            // ================= SEMESTER =================
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

            // ================= TAHUN =================
            $tahunR = $hitung(1, 12);

            // ================= REKAP CAPAIAN =================
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

    /**
     * Ambil nilai rata-rata untuk periode tertentu
     */
    private function getNilaiPeriode(int $indicatorId, int $tahun, int $bulanMulai, int $bulanAkhir, float $factors)
    {
        $db = db_connect();
        $bulanMulaiStr = str_pad($bulanMulai, 2, '0', STR_PAD_LEFT);
        $bulanAkhirStr = str_pad($bulanAkhir, 2, '0', STR_PAD_LEFT);
        $dayAkhir = $this->getDaysInMonth($bulanAkhir, $tahun);

        $query = $db->query("
            SELECT 
                SUM(qir.result_numerator_value) AS num,
                SUM(qir.result_denumerator_value) AS denum,
                qi.indicator_units
            FROM quality_indicator_result qir
            JOIN quality_indicator qi ON qir.result_indicator_id = qi.indicator_id
            WHERE qir.result_indicator_id = ?
            AND qir.result_period BETWEEN '{$tahun}-{$bulanMulaiStr}-01' AND '{$tahun}-{$bulanAkhirStr}-{$dayAkhir}'
            GROUP BY qi.indicator_units
        ", [$indicatorId]);

        $row = $query->getRow();
        if (!$row || $row->denum == 0) {
            return null;
        }

        $nilai = round(($row->num / $row->denum) * $factors, 2);
        return $nilai . $row->indicator_units;
    }

    /**
     * Cek apakah tercapai berdasarkan operator (boolean)
     */
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

    /**
     * Get status PMKP (TERCAPAI/TIDAK TERCAPAI/TIDAK ADA DATA)
     */
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

    // ==================== HELPER ====================

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

    /**
     * Ambil data bulanan untuk satu indikator
     */
    public function getMonthlyDataByIndicator(int $indicatorId, int $tahun): array
    {
        $db = db_connect();
        $builder = $db->table('quality_indicator_result qir');

        $builder->select("
            MONTH(qir.result_period) AS bulan,
            SUM(qir.result_numerator_value) AS num,
            SUM(qir.result_denumerator_value) AS denum
        ");

        $builder->join('quality_indicator qi', 'qir.result_indicator_id = qi.indicator_id', 'LEFT');
        $builder->where('qir.result_indicator_id', $indicatorId);
        $builder->where("YEAR(qir.result_period)", $tahun);
        $builder->groupBy('MONTH(qir.result_period)');

        $results = $builder->get()->getResult();

        $data = array_fill(1, 12, ['num' => 0, 'denum' => 0, 'nilai' => null]);

        $indicator = $this->getDetailByIdInm($indicatorId);
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

    /**
     * Ambil nilai triwulan
     */
    public function getNilaiTriwulan(int $indicatorId, int $tahun): array
    {
        $monthly = $this->getMonthlyDataByIndicator($indicatorId, $tahun);

        $indicator = $this->getDetailByIdInm($indicatorId);
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

    /**
     * Ambil nilai semester
     */
    public function getNilaiSemester(int $indicatorId, int $tahun): array
    {
        $monthly = $this->getMonthlyDataByIndicator($indicatorId, $tahun);

        $indicator = $this->getDetailByIdInm($indicatorId);
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

    /**
     * Ambil nilai tahunan
     */
    public function getNilaiTahun(int $indicatorId, int $tahun): array
    {
        $monthly = $this->getMonthlyDataByIndicator($indicatorId, $tahun);

        $indicator = $this->getDetailByIdInm($indicatorId);
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

    /**
     * Ambil nilai per tahun (5 tahun terakhir)
     */
    public function getNilaiPerTahun(int $indicatorId, int $tahun): array
    {
        $indicator = $this->getDetailByIdInm($indicatorId);
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
}
