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

        // Hitung jumlah hari dalam bulan
        $day = $this->getDaysInMonth($bulan, $tahun);

        // Format bulan jadi 2 digit
        $bulanx = str_pad($bulan, 2, '0', STR_PAD_LEFT);

        // Tanggal awal & akhir
        $startDate = "{$tahun}-{$bulanx}-01";
        $endDate   = "{$tahun}-{$bulanx}-{$day}";

        $builder = $db->table('quality_indicator_result qir');

        $builder->select("
                    qi.indicator_category_id,
                    qi.indicator_id,
                    qi.indicator_element,

                    SUM(qir.result_numerator_value) AS num,
                    SUM(qir.result_denumerator_value) AS denum,

                    CASE 
                        WHEN SUM(qir.result_denumerator_value) = 0 THEN NULL
                        ELSE ROUND(
                            (SUM(qir.result_numerator_value) / SUM(qir.result_denumerator_value)) 
                            * qi.indicator_factors
                        , 4)
                    END AS total_value,

                    CASE 
                        WHEN SUM(qir.result_denumerator_value) = 0 THEN 'TIDAK ADA DATA'
                        ELSE CONCAT(
                            ROUND(
                                (SUM(qir.result_numerator_value) / SUM(qir.result_denumerator_value)) 
                                * qi.indicator_factors
                            , 2),
                            ' ',
                            qi.indicator_units
                        )
                    END AS total
                ");
        $builder->join(
            'quality_indicator qi',
            'qir.result_indicator_id = qi.indicator_id',
            'LEFT'
        );

        // ✅ gunakan where terpisah (lebih aman & terbaca)
        $builder->where('qir.result_period >=', $startDate);
        $builder->where('qir.result_period <=', $endDate);

        $builder->where('qi.indicator_category_id', '4');
        // [CHANGED] Biar indikator non-aktif (status 'D') tetap muncul hasil rekapan historisnya
        $builder->whereIn('qi.indicator_record_status', ['A']);
        $builder->where('qi.indicator_id', $indicator);
        $builder->where('qir.result_record_status', 'A');
        // Untuk M/Y: pilih record terawal per bulan (input user), bukan kumulatif akhir bulan
        $builder->where("(qi.indicator_frequency NOT IN ('M', 'Y') OR qir.result_period = (
            SELECT MIN(lqir2.result_period)
            FROM quality_indicator_result lqir2
            WHERE lqir2.result_indicator_id = qir.result_indicator_id
            AND lqir2.result_department_id = qir.result_department_id
            AND YEAR(lqir2.result_period) = YEAR(qir.result_period)
            AND MONTH(lqir2.result_period) = MONTH(qir.result_period)
            AND lqir2.result_record_status = 'A'
        ))", null, false);

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

    public function getAjaxDataRekapInm(int $indicator, int $tahun, int $bulan)
    {
        $builder = $this->_getAjaxDataRekapInm($indicator, $tahun, $bulan);

        // gunakan getRowArray biar lebih aman dipakai di view/json
        return $builder->get()->getRowArray();
    }

    // ==================== PUBLIC METHODS ====================


    /**
     * Ambil SEMUA data bulanan dalam 1 query (OPTIMIZED + CACHE)
     */
    public function getAllMonthlyData(array $indicatorIds, int $tahun, ?int $departmentId = null)
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

            CASE 
                WHEN SUM(qir.result_denumerator_value) = 0 THEN NULL
                ELSE ROUND(
                    SUM(qir.result_numerator_value) /
                    SUM(qir.result_denumerator_value) *
                    qi.indicator_factors
                , 4)
            END AS total_value,

            CASE 
                WHEN SUM(qir.result_denumerator_value) = 0 THEN 'TIDAK ADA DATA'
                ELSE CONCAT(
                    ROUND(
                        SUM(qir.result_numerator_value) /
                        SUM(qir.result_denumerator_value) *
                        qi.indicator_factors
                    , 2),
                    ' ',
                    qi.indicator_units
                )
            END AS total,

            qi.indicator_units AS units
        ");

        $builder->join('quality_indicator qi', 'qir.result_indicator_id = qi.indicator_id', 'LEFT');

        $builder->where("YEAR(qir.result_period)", $tahun);
        $builder->where("qi.indicator_category_id", '4');
        // [CHANGED] Biar indikator non-aktif tetap ikut diambil data bulanannya
        $builder->whereIn("qi.indicator_record_status", ['A']);
        $builder->whereIn('qi.indicator_id', $indicatorIds);
        $builder->where('qir.result_record_status', 'A');
        // Untuk M/Y: pilih record terawal per bulan (input user), bukan kumulatif akhir bulan
        $builder->where("(qi.indicator_frequency NOT IN ('M', 'Y') OR qir.result_period = (
            SELECT MIN(lqir2.result_period)
            FROM quality_indicator_result lqir2
            WHERE lqir2.result_indicator_id = qir.result_indicator_id
            AND lqir2.result_department_id = qir.result_department_id
            AND YEAR(lqir2.result_period) = YEAR(qir.result_period)
            AND MONTH(lqir2.result_period) = MONTH(qir.result_period)
            AND lqir2.result_record_status = 'A'
        ))", null, false);
    
        if ($departmentId !== null && $departmentId > 0) {
            $builder->where('qir.result_department_id', $departmentId);
        }

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
    public function getIndicatorInm($post, ?int $departmentId = null)
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
            quality_indicator.indicator_factors AS factors,
            quality_indicator.indicator_record_status
        ");

        $builder->join('quality_indicator', 'quality_indicator.indicator_id = quality_indicator_group.group_indicator_id');
        $builder->join('master_institution_department', 'master_institution_department.department_id = quality_indicator_group.group_department_id');

        $builder->where("quality_indicator.indicator_category_id", '4');
        // [CHANGED] Biar indikator non-aktif (status 'D') tetap muncul di daftar rekap
        $builder->whereIn("quality_indicator.indicator_record_status", ['A']);

        $vtahun = isset($post['vtahun']) ? (int) $post['vtahun'] : (int) date('Y');

        // Filter by user role / department override
        $userRole = session('user_role') ?? '';
        $userDepartmentId = session('department_id') ?? 0;
        $effectiveDeptId = $departmentId !== null && $departmentId > 0
            ? $departmentId
            : ((!in_array($userRole, ['ADMINISTRATOR', 'KOMITE']) && $userDepartmentId > 0) ? $userDepartmentId : null);

        if ($effectiveDeptId !== null) {
            $builder->where('master_institution_department.department_id', $effectiveDeptId);
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

        // Order default
        $builder->orderBy('quality_indicator.indicator_record_status', 'ASC');
        if (isset($post['order'])) {
            $col = $this->column_order[$post['order'][0]['column']] ?? 'indicator_element';
            $dir = $post['order'][0]['dir'] ?? 'ASC';
            if ($col) {
                $builder->orderBy($col, $dir);
            }
        } else {
            $builder->orderBy('indicator_element', 'ASC');
        }

        // === Ambil SEMUA data dulu (tanpa limit) agar bisa diurutkan dengan benar ===
        $allBuilder = clone $builder;
        $allBuilder->limit(10000, 0);
        $allResults = $allBuilder->get()->getResult();

        // === Ambil semua indicator yang punya data (sekali query saja) ===
        $indicatorsWithData = $this->getIndicatorsWithData($vtahun);

        // === Urutin manual: yang punya data di atas, yang tidak di bawah ===
        $withData = [];
        $withoutData = [];

        foreach ($allResults as $row) {
            // Cast ke int untuk确保 perbandingan benar
            $rowId = (int) $row->indicator_id;
            if (in_array($rowId, array_map('intval', $indicatorsWithData))) {
                $withData[] = $row;
            } else {
                $withoutData[] = $row;
            }
        }

        // === Gabungkan: yang punya data di atas ===
        $sortedResults = array_merge($withData, $withoutData);

        // === Apply pagination manual ===
        $start = (int) ($post['start'] ?? 0);
        $length = (int) ($post['length'] ?? -1);
        
        if ($length > 0) {
            $sortedResults = array_slice($sortedResults, $start, $length);
        }

        return $sortedResults;

        // === OLD CODE (unused - replaced by sorting logic above) ===
        /*
        // Pagination
        if (isset($post['length']) && $post['length'] != -1) {
            $builder->limit($post['length'], $post['start'] ?? 0);
        }

        return $builder->get()->getResult();
        */
    }

    /**
     * Ambil semua indicator yang punya data di tahun tertentu (sekali query)
     */
    private function getIndicatorsWithData(int $tahun): array
    {
        $db = db_connect();
        $query = $db->query("
            SELECT DISTINCT CAST(result_indicator_id AS UNSIGNED) AS result_indicator_id
            FROM quality_indicator_result 
            WHERE YEAR(result_period) = ?
        ", [$tahun]);

        $result = $query->getResult();
        return array_map('intval', array_column($result, 'result_indicator_id'));
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
                AND quality_indicator.indicator_record_status IN ('A')
                " . ((!in_array($userRole, ['ADMINISTRATOR', 'KOMITE']) && $userDepartmentId > 0) ? "AND master_institution_department.department_id = " . $userDepartmentId : "") . "
                GROUP BY quality_indicator.indicator_id
            ) as counted
        ");

        $count = $query->getRow()->total ?? 0;

        // Cache 10 menit
        $cache->save($cacheKey, $count, 600);

        return $count;
    }

    /**
     * Ambil semua ruangan untuk indicator tertentu
     */
    public function getDepartmentsByIndicator(int $indicatorId, int $tahun, $post = [], ?int $departmentId = null)
    {
        $db = db_connect();

        $searchCondition = '';
        if (isset($post['search']['value']) && !empty($post['search']['value'])) {
            $searchValue = addslashes($post['search']['value']);
            $searchCondition = "AND master_institution_department.department_name LIKE '%{$searchValue}%'";
        }

        $deptCondition = '';
        if ($departmentId !== null) {
            $deptCondition = "AND master_institution_department.department_id = " . (int) $departmentId;
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
            -- [CHANGED] Pake IN ('A') biar departemen indikator non-aktif tetap tampil di detail
            AND quality_indicator.indicator_record_status IN ('A') 
            AND quality_indicator_group.group_record_status = 'A'
            AND quality_indicator_group.group_indicator_id = ?
            {$searchCondition}
            {$deptCondition}
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
                quality_indicator.indicator_units,
                -- [CHANGED] Ambil indicator_record_status biar tau indikator ini non-aktif atau tidak
                quality_indicator.indicator_record_status
            FROM quality_indicator
            WHERE quality_indicator.indicator_id = ?
            AND quality_indicator.indicator_category_id = '4' 
            -- [CHANGED] Pake IN ('A') biar detail indikator non-aktif tetap bisa dibuka
            AND quality_indicator.indicator_record_status IN ('A')
        ", [$indicatorId]);

        return $query->getRow();
    }

    /**
     * Ambil semua data detail per ruangan dalam 1 query
     */
    public function getAllDetailData(int $indicatorId, int $tahun, ?int $departmentId = null)
    {
        $cache = \Config\Services::cache();
        $cacheKey = 'detail_data_' . $indicatorId . '_' . $tahun . '_dept_' . ($departmentId ?? 'all');

        $db = db_connect();
        $builder = $db->table('quality_indicator_result qir');

        $builder->select("
        qir.result_department_id,
        MONTH(qir.result_period) AS bulan,

        SUM(qir.result_numerator_value) AS num,
        SUM(qir.result_denumerator_value) AS denum,

       CASE 
        WHEN SUM(qir.result_denumerator_value) = 0 THEN NULL
        ELSE ROUND(
            SUM(qir.result_numerator_value) /
            SUM(qir.result_denumerator_value) *
            qi.indicator_factors
        , 4)
        END AS total_value,

        CASE 
            WHEN SUM(qir.result_denumerator_value) = 0 THEN 'TIDAK ADA DATA'
            ELSE CONCAT(
                ROUND(
                    SUM(qir.result_numerator_value) /
                    SUM(qir.result_denumerator_value) *
                    qi.indicator_factors
                , 2),
                ' ',
                qi.indicator_units
            )
        END AS total
    ");

        $builder->join('quality_indicator qi', 'qir.result_indicator_id = qi.indicator_id', 'LEFT');

        $builder->where('YEAR(qir.result_period)', $tahun);
        $builder->where('qir.result_indicator_id', $indicatorId);
        $builder->where('qir.result_record_status', 'A');
        if ($departmentId !== null) {
            $builder->where('qir.result_department_id', $departmentId);
        }

        $builder->groupBy([
            'qir.result_department_id',
            'MONTH(qir.result_period)'
        ]);

        $results = $builder->get()->getResult();

        // DEBUG
        log_message('error', 'getAllDetailData: indicatorId=' . $indicatorId . ', tahun=' . $tahun . ', count=' . count($results));

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
    public function countDepartmentsByIndicator(int $indicatorId, int $tahun, $post = [], ?int $departmentId = null)
    {
        $db = db_connect();

        $searchCondition = '';
        if (isset($post['search']['value']) && !empty($post['search']['value'])) {
            $searchValue = addslashes($post['search']['value']);
            $searchCondition = "AND master_institution_department.department_name LIKE '%{$searchValue}%'";
        }

        $deptCondition = '';
        if ($departmentId !== null) {
            $deptCondition = "AND master_institution_department.department_id = " . (int) $departmentId;
        }

        $query = $db->query("
            SELECT COUNT(DISTINCT master_institution_department.department_id) as total
            FROM quality_indicator_group
            JOIN quality_indicator ON quality_indicator.indicator_id = quality_indicator_group.group_indicator_id
            JOIN master_institution_department ON master_institution_department.department_id = quality_indicator_group.group_department_id
            WHERE quality_indicator_group.group_indicator_id = ?
            AND quality_indicator.indicator_category_id = '4' 
            -- [CHANGED] Pake IN ('A') biar hitungan departemen indikator non-aktif tetap akurat
            AND quality_indicator.indicator_record_status IN ('A')
            {$deptCondition}
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
                AND quality_indicator.indicator_record_status IN ('A')
                " . ((!in_array($userRole, ['ADMINISTRATOR', 'KOMITE']) && $userDepartmentId > 0) ? "AND master_institution_department.department_id = " . $userDepartmentId : "") . "
                {$searchCondition}
                GROUP BY quality_indicator.indicator_id
            ) as counted
        ");

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
    public function getRekapPeriode(int $tahun, ?int $departmentId = null)
    {
        $db = db_connect();

        $builder = $db->table('quality_indicator');
        $builder->distinct();
        // [CHANGED] Tambah indicator_record_status biar bisa nampilin badge Non-Aktif di view
        $builder->select('quality_indicator.indicator_id, quality_indicator.indicator_element, quality_indicator.indicator_target, quality_indicator.indicator_factors, quality_indicator.indicator_units, quality_indicator.indicator_target_calculation, quality_indicator.indicator_record_status');
        $builder->join('quality_indicator_group', 'quality_indicator.indicator_id = quality_indicator_group.group_indicator_id');
        $builder->where('quality_indicator.indicator_category_id', '4');
        $builder->whereIn('quality_indicator.indicator_record_status', ['A']);

        // Filter by user role / department override
        $userRole = session('user_role') ?? '';
        $userDepartmentId = session('department_id') ?? 0;

        $filterDepartmentId = null;
        if ($departmentId !== null && $departmentId > 0) {
            $builder->where('quality_indicator_group.group_department_id', $departmentId);
            $filterDepartmentId = $departmentId;
        } elseif (!in_array($userRole, ['ADMINISTRATOR', 'KOMITE']) && $userDepartmentId > 0) {
            $builder->where('quality_indicator_group.group_department_id', $userDepartmentId);
            $filterDepartmentId = $userDepartmentId;
        }

        $indicators = $builder->get()->getResult();

        if (empty($indicators)) return [];

        $indicatorIds = array_column($indicators, 'indicator_id');
        $allMonthlyData = $this->getAllMonthlyData($indicatorIds, $tahun, $filterDepartmentId);

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
                // [CHANGED] Biar view tau indikator ini non-aktif atau tidak
                'indicator_record_status' => $indicator->indicator_record_status ?? 'A',
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
            AND qir.result_period BETWEEN '{$tahun}-{$bulanMulaiStr}-01' 
                AND '{$tahun}-{$bulanAkhirStr}-{$dayAkhir}'
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
     * Ambil data harian untuk satu indikator, bulan, dan tahun (semua departemen)
     */
    public function getDailyDataAllDepartments(int $indicatorId, int $tahun, int $bulan, ?int $departmentId = null)
    {
        $db = db_connect();
        $builder = $db->table('quality_indicator_result qir');

        $builder->select("
            qir.result_department_id,
            DAY(qir.result_period) AS tanggal,
            SUM(qir.result_numerator_value) AS num,
            SUM(qir.result_denumerator_value) AS denum
        ");

        $builder->where('qir.result_indicator_id', $indicatorId);
        $builder->where('YEAR(qir.result_period)', $tahun);
        $builder->where('MONTH(qir.result_period)', $bulan);

        if ($departmentId !== null) {
            $builder->where('qir.result_department_id', $departmentId);
        }

        $builder->groupBy(['qir.result_department_id', 'qir.result_period']);
        $builder->orderBy('qir.result_department_id');
        $builder->orderBy('qir.result_period', 'ASC');

        return $builder->get()->getResult();
    }

    /**
     * Ambil kendala & perbaikan dari local_rencana_perbaikan
     */
    public function getKendalaPerbaikan(int $indicatorId, int $tahun, int $bulan, ?int $departmentId = null): array
    {
        $db = db_connect();
        $builder = $db->table('local_rencana_perbaikan');

        $builder->select("
            result_department_id,
            DAY(result_period) AS tanggal,
            kendala,
            perbaikan
        ");

        $builder->where('result_indicator_id', $indicatorId);
        $builder->where('indicator_category_id', '4');
        $builder->where('YEAR(result_period)', $tahun);
        $builder->where('MONTH(result_period)', $bulan);

        if ($departmentId !== null) {
            $builder->where('result_department_id', $departmentId);
        }

        $results = $builder->get()->getResult();

        $map = [];
        foreach ($results as $row) {
            $did = (int) $row->result_department_id;
            $tgl = (int) $row->tanggal;
            $map[$did . '_' . $tgl] = [
                'kendala'   => $row->kendala,
                'perbaikan' => $row->perbaikan,
            ];
        }
        return $map;
    }

    /**
     * Ambil data harian per departemen untuk satu indikator, bulan, dan tahun
     */
    public function getDailyDataByDepartment(int $indicatorId, int $departmentId, int $tahun, int $bulan)
    {
        $db = db_connect();
        $builder = $db->table('quality_indicator_result qir');

        $builder->select("
            DAY(qir.result_period) AS tanggal,
            qir.result_period,
            SUM(qir.result_numerator_value) AS num,
            SUM(qir.result_denumerator_value) AS denum
        ");

        $builder->where('qir.result_indicator_id', $indicatorId);
        $builder->where('qir.result_department_id', $departmentId);
        $builder->where('YEAR(qir.result_period)', $tahun);
        $builder->where('MONTH(qir.result_period)', $bulan);

        $builder->groupBy('qir.result_period');
        $builder->orderBy('qir.result_period', 'ASC');

        return $builder->get()->getResult();
    }

    /**
     * Ambil data bulanan untuk satu indikator
     */
    public function getMonthlyDataByIndicator(int $indicatorId, int $tahun, ?int $departmentId = null): array
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
        $builder->where('qir.result_record_status', 'A');
        $builder->where("YEAR(qir.result_period)", $tahun);
        // Untuk M/Y: pilih record terawal per bulan (input user), bukan kumulatif akhir bulan
        $builder->where("(qi.indicator_frequency NOT IN ('M', 'Y') OR qir.result_period = (
            SELECT MIN(lqir2.result_period)
            FROM quality_indicator_result lqir2
            WHERE lqir2.result_indicator_id = qir.result_indicator_id
            AND lqir2.result_department_id = qir.result_department_id
            AND YEAR(lqir2.result_period) = YEAR(qir.result_period)
            AND MONTH(lqir2.result_period) = MONTH(qir.result_period)
            AND lqir2.result_record_status = 'A'
        ))", null, false);
        if ($departmentId !== null) {
            $builder->where('qir.result_department_id', $departmentId);
        }
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

            $nilai = $denum > 0 ? round(($num / $denum) * $factors, 2) : (($num == 0 && $denum == 0) ? 0 : null);

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
    public function getNilaiTriwulan(int $indicatorId, int $tahun, ?int $departmentId = null): array
    {
        $monthly = $this->getMonthlyDataByIndicator($indicatorId, $tahun, $departmentId);

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
    public function getNilaiSemester(int $indicatorId, int $tahun, ?int $departmentId = null): array
    {
        $monthly = $this->getMonthlyDataByIndicator($indicatorId, $tahun, $departmentId);

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
    public function getNilaiTahun(int $indicatorId, int $tahun, ?int $departmentId = null): array
    {
        $monthly = $this->getMonthlyDataByIndicator($indicatorId, $tahun, $departmentId);

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
    public function getNilaiPerTahun(int $indicatorId, int $tahun, ?int $departmentId = null): array
    {
        $indicator = $this->getDetailByIdInm($indicatorId);
        $target = (float) ($indicator->indicator_target ?? 0);
        $factors = (float) ($indicator->indicator_factors ?? 1);
        $operator = $indicator->indicator_target_calculation ?? '>=';

        $perTahun = [];
        $tahunMulai = $tahun - 4;

        for ($th = $tahunMulai; $th <= $tahun; $th++) {
            $monthly = $this->getMonthlyDataByIndicator($indicatorId, $th, $departmentId);

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

    /**
     * Ambil daftar departemen yang punya data approved di tahun tertentu
     */
    public function getActiveDepartmentsForYear(int $tahun)
    {
        $db = db_connect();
        return $db->table('master_institution_department mid')
            ->distinct()
            ->select('mid.department_id, mid.department_name')
            ->join('quality_indicator_result qir', 'qir.result_department_id = mid.department_id', 'inner')
            ->join('quality_indicator qi', 'qi.indicator_id = qir.result_indicator_id', 'inner')
            ->where('qi.indicator_category_id', '4')
            ->where('qir.result_record_status', 'A')
            ->where('YEAR(qir.result_period)', $tahun)
            ->orderBy('mid.department_name', 'ASC')
            ->get()
            ->getResult();
    }

    /**
     * Hitung jumlah draft (belum disetujui) per departemen untuk INM
     * Menghitung kombinasi unik (indikator + bulan + departemen) — setiap baris draft dihitung 1x
     */
    public function getDraftCountByDepartment(int $tahun): array
    {
        $db = db_connect();
        $rows = $db->table('quality_indicator_result qir')
            ->select("
                qir.result_department_id AS department_id,
                mid.department_name,
                COUNT(*) AS total_draft
            ")
            ->join('quality_indicator qi', 'qir.result_indicator_id = qi.indicator_id', 'inner')
            ->join('master_institution_department mid', 'mid.department_id = qir.result_department_id', 'left')
            ->where('qi.indicator_category_id', '4')
            ->where('qir.result_record_status', 'D')
            ->where('YEAR(qir.result_period)', $tahun)
            ->groupBy('qir.result_department_id, mid.department_name')
            ->orderBy('mid.department_name', 'ASC')
            ->get()
            ->getResult();

        return array_map(function ($r) {
            return [
                'department_id'   => (int) $r->department_id,
                'department_name' => $r->department_name ?? '(Tanpa Departemen)',
                'total_draft'     => (int) $r->total_draft,
            ];
        }, $rows);
    }

    /**
     * Hitung jumlah draft (belum disetujui) per bulan untuk INM
     */
    public function getDraftCountByMonth(int $tahun): array
    {
        $db = db_connect();
        $rows = $db->table('quality_indicator_result qir')
            ->select("
                MONTH(qir.result_period) AS bulan,
                COUNT(*) AS total_draft
            ")
            ->join('quality_indicator qi', 'qir.result_indicator_id = qi.indicator_id', 'inner')
            ->where('qi.indicator_category_id', '4')
            ->where('qir.result_record_status', 'D')
            ->where('YEAR(qir.result_period)', $tahun)
            ->groupBy('MONTH(qir.result_period)')
            ->orderBy('MONTH(qir.result_period)', 'ASC')
            ->get()
            ->getResult();

        $result = array_fill(1, 12, 0);
        foreach ($rows as $r) {
            $result[(int) $r->bulan] = (int) $r->total_draft;
        }
        return $result;
    }
}
