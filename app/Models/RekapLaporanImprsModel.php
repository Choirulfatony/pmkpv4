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

    // ==================== HELPER ====================

    private function filterActiveOrHasData($builder, $startDate, $endDate)
    {
        $builder->groupStart();

        $builder->groupStart()
            ->where('lqi.indicator_active_to IS NULL', null, false)
            ->orWhere('lqi.indicator_active_to >=', $startDate)
        ->groupEnd();

        $builder->orWhere("
            EXISTS (
                SELECT 1 FROM local_quality_indicator_result lqir
                WHERE lqir.result_indicator_id = lqi.indicator_id
                AND lqir.result_period BETWEEN '{$startDate}' AND '{$endDate}'
            )
        ", null, false)

        ->groupEnd();
    }

    // ==================== PRIVATE QUERY BUILDERS ====================

    private function _getAjaxDataRekapImprs(int $indicator, int $tahun, int $bulan)
    {
        $db = db_connect();

        // Hitung jumlah hari dalam bulan
        $day = $this->getDaysInMonth($bulan, $tahun);

        // Format bulan jadi 2 digit
        $bulanx = str_pad($bulan, 2, '0', STR_PAD_LEFT);

        // Tanggal awal & akhir
        $startDate = "{$tahun}-{$bulanx}-01";
        $endDate   = "{$tahun}-{$bulanx}-{$day}";

        $builder = $db->table('local_quality_indicator_result lqir');

        $builder->select("
                    lqi.indicator_category_id,
                    lqi.indicator_id,
                    lqi.indicator_element,

                    SUM(lqir.result_numerator_value) AS num,
                    SUM(lqir.result_denumerator_value) AS denum,

                    CASE 
                        WHEN SUM(lqir.result_denumerator_value) = 0 THEN NULL
                        ELSE ROUND(
                            (SUM(lqir.result_numerator_value) / SUM(lqir.result_denumerator_value)) 
                            * lqi.indicator_factors
                        , 4)
                    END AS total_value,

                    CASE 
                        WHEN SUM(lqir.result_denumerator_value) = 0 THEN 'TIDAK ADA DATA'
                        ELSE CONCAT(
                            ROUND(
                                (SUM(lqir.result_numerator_value) / SUM(lqir.result_denumerator_value)) 
                                * lqi.indicator_factors
                            , 2),
                            ' ',
                            lqi.indicator_units
                        )
                    END AS total
                ");
        $builder->join(
            'local_quality_indicator lqi',
            'lqir.result_indicator_id = lqi.indicator_id',
            'LEFT'
        );

        // ✅ gunakan where terpisah (lebih aman & terbaca)
        $builder->where('lqir.result_period >=', $startDate);
        $builder->where('lqir.result_period <=', $endDate);

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

    // ==================== PUBLIC METHODS ====================

    /**
     * Ambil data rekap IMPRS per bulan
     */

    public function getAjaxDataRekapImprs(int $indicator, int $tahun, int $bulan)
    {
        $builder = $this->_getAjaxDataRekapImprs($indicator, $tahun, $bulan);

        // gunakan getRowArray biar lebih aman dipakai di view/json
        return $builder->get()->getRowArray();
    }

    // ==================== PUBLIC METHODS ====================


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
        $builder = $db->table('local_quality_indicator_result lqir');

        $builder->select("
            lqi.indicator_id,
            MONTH(lqir.result_period) AS bulan,

            SUM(lqir.result_numerator_value) AS num,
            SUM(lqir.result_denumerator_value) AS denum,

            CASE 
                WHEN SUM(lqir.result_denumerator_value) = 0 THEN NULL
                ELSE ROUND(
                    SUM(lqir.result_numerator_value) /
                    SUM(lqir.result_denumerator_value) *
                    lqi.indicator_factors
                , 4)
            END AS total_value,

            CASE 
                WHEN SUM(lqir.result_denumerator_value) = 0 THEN 'TIDAK ADA DATA'
                ELSE CONCAT(
                    ROUND(
                        SUM(lqir.result_numerator_value) /
                        SUM(lqir.result_denumerator_value) *
                        lqi.indicator_factors
                    , 2),
                    ' ',
                    lqi.indicator_units
                )
            END AS total,

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
public function getIndicatorImprs($post)
    {
        $db = db_connect();
        $builder = $db->table('local_quality_indicator_group');

        $vtahun = isset($post['vtahun']) ? (int) $post['vtahun'] : (int) date('Y');

        // Cek available group_period
        $availablePeriods = $this->getAvailableGroupPeriods();
        $usePeriod = in_array($vtahun, $availablePeriods) ? $vtahun : min($availablePeriods);

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
        $builder->join('local_quality_indicator lqi', 'lqi.indicator_id = local_quality_indicator_group.group_indicator_id', 'left');

        $builder->where("local_quality_indicator.indicator_category_id", '5');
        $builder->where("local_quality_indicator.indicator_record_status", 'A');
        $builder->where("local_quality_indicator_group.group_record_status", 'A');

        $builder->groupStart();
        $builder->where("local_quality_indicator_group.group_period", $usePeriod);
        $builder->orWhere('local_quality_indicator_group.group_period', $usePeriod - 1);
        $builder->orWhere('local_quality_indicator_group.group_period', $usePeriod - 2);
        $builder->groupEnd();

        $this->filterActiveOrHasData($builder, $vtahun . '-01-01', $vtahun . '-12-31');

        $builder->groupStart();
        $builder->where('lqi.indicator_active_from IS NULL', null, false)
            ->orWhere("YEAR(lqi.indicator_active_from) <= {$vtahun}", null, false);
        $builder->groupEnd();
        $builder->groupStart();
        $builder->where('lqi.indicator_active_to IS NULL', null, false)
            ->orWhere("YEAR(lqi.indicator_active_to) >= {$vtahun}", null, false);
        $builder->groupEnd();

        // Filter by user role
        $userRole = session('user_role') ?? '';
        $userDepartmentId = session('department_id') ?? 0;

        if (!in_array($userRole, ['ADMINISTRATOR', 'KOMITE']) && $userDepartmentId > 0) {
            $builder->where('local_quality_indicator_group.group_department_id', $userDepartmentId);
        }

        $builder->groupBy('local_quality_indicator.indicator_id');

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
        if (isset($post['order'])) {
            $col = $this->column_order[$post['order'][0]['column']] ?? 'indicator_element';
            $dir = $post['order'][0]['dir'] ?? 'ASC';
            if ($col) {
                $builder->orderBy($col, $dir);
            }
        } else {
            $builder->orderBy('indicator_element', 'ASC');
        }

// Ambil SEMUA data dulu (tanpa limit) agar bisa diurutkan dengan benar
        $allBuilder = clone $builder;
        $allBuilder->limit(10000, 0);
        $allResults = $allBuilder->get()->getResult();

        // Ambil semua indicator yang punya data (sekali query saja)
        $indicatorsWithData = $this->getIndicatorsWithData($vtahun);

        // Debug: log jika perlu
        // log_message('error', 'vtahun: ' . $vtahun . ' - indicators: ' . json_encode($indicatorsWithData));

        // Urutin manual: yang punya data di atas, yang tidak di bawah
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

        // Gabungkan: yang punya data di atas
        $sortedResults = array_merge($withData, $withoutData);

        // Simpan total untuk pagination
        $this->_totalRecords = count($sortedResults);

        // Apply pagination manual - jika tidak ada length di post, ambil semua
        $start = (int) ($post['start'] ?? 0);
        if (isset($post['length']) && $post['length'] != -1) {
            $length = (int) $post['length'];
        } else {
            // Jika tidak ada pagination (export), ambil semua
            $length = count($sortedResults);
        }
        $results = array_slice($sortedResults, $start, $length);

        return $results;
    }

    private $_totalRecords = 0;

    // === UNUSED CODE (kept for reference) ===
    /*
    private function checkIndicatorHasData(int $indicatorId, int $tahun): bool
    {
        $db = db_connect();
        $query = $db->query("
            SELECT 1 FROM local_quality_indicator_result 
            WHERE result_indicator_id = ? 
            AND YEAR(result_period) = ? 
            LIMIT 1
        ", [$indicatorId, $tahun]);
        return $query->getNumRows() > 0;
    }
    */

    /**
     * Ambil semua indicator yang punya data di tahun tertentu (sekali query)
     */
    private function getIndicatorsWithData(int $tahun): array
    {
        $db = db_connect();
        $query = $db->query("
            SELECT DISTINCT CAST(result_indicator_id AS UNSIGNED) AS result_indicator_id
            FROM local_quality_indicator_result 
            WHERE YEAR(result_period) = ?
        ", [$tahun]);

        $result = $query->getResult();
        return array_map('intval', array_column($result, 'result_indicator_id'));
    }

    public function getTotalRecords(): int
    {
        return $this->_totalRecords;
    }

/**
     * Hitung semua rekap IMPRS (CACHE)
     */
    public function countAllRekapImprs($post = [])
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

        // Query sama dengan getIndicatorImprs tapi hanya COUNT
        $query = $db->query("
            SELECT COUNT(*) as total FROM (
                SELECT DISTINCT local_quality_indicator.indicator_id
                FROM local_quality_indicator_group
                JOIN local_quality_indicator ON local_quality_indicator.indicator_id = local_quality_indicator_group.group_indicator_id
                JOIN master_institution_department ON master_institution_department.department_id = local_quality_indicator_group.group_department_id
                WHERE local_quality_indicator.indicator_category_id = '5'
                AND local_quality_indicator.indicator_record_status = 'A'
                AND local_quality_indicator_group.group_record_status = 'A'
                AND (local_quality_indicator_group.group_period = ? 
                     OR local_quality_indicator_group.group_period = ? 
                     OR local_quality_indicator_group.group_period = ?)
                " . ((!in_array($userRole, ['ADMINISTRATOR', 'KOMITE']) && $userDepartmentId > 0) ? "AND local_quality_indicator_group.group_department_id = " . $userDepartmentId : "") . "
                GROUP BY local_quality_indicator.indicator_id
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
                local_quality_indicator.indicator_id,
                local_quality_indicator.indicator_element,
                master_institution_department.department_id,
                master_institution_department.department_name,
                local_quality_indicator_group.group_indicator_id
            FROM local_quality_indicator_group
            JOIN local_quality_indicator ON local_quality_indicator.indicator_id = local_quality_indicator_group.group_indicator_id
            JOIN master_institution_department ON master_institution_department.department_id = local_quality_indicator_group.group_department_id
            WHERE local_quality_indicator.indicator_category_id = '5' 
            AND local_quality_indicator.indicator_record_status = 'A' 
            AND local_quality_indicator_group.group_record_status = 'A'
            AND local_quality_indicator_group.group_indicator_id = ?
            {$searchCondition}
            GROUP BY master_institution_department.department_id
            ORDER BY master_institution_department.department_name ASC
            {$limit}
        ", [$indicatorId]);

        return $query->getResult();
    }

    /**
     * Ambil detail indicator by ID (CI4 version of get_detail_byid_imprs)
     */
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

    /**
     * Ambil semua data detail per ruangan dalam 1 query
     */
    public function getAllDetailData(int $indicatorId, int $tahun)
    {
        $cache = \Config\Services::cache();
        $cacheKey = 'detail_data_' . $indicatorId . '_' . $tahun;

        $db = db_connect();
        $builder = $db->table('local_quality_indicator_result lqir');

        $builder->select("
        lqir.result_department_id,
        MONTH(lqir.result_period) AS bulan,

        SUM(lqir.result_numerator_value) AS num,
        SUM(lqir.result_denumerator_value) AS denum,

       CASE 
        WHEN SUM(lqir.result_denumerator_value) = 0 THEN NULL
        ELSE ROUND(
            SUM(lqir.result_numerator_value) /
            SUM(lqir.result_denumerator_value) *
            lqi.indicator_factors
        , 4)
        END AS total_value,

        CASE 
            WHEN SUM(lqir.result_denumerator_value) = 0 THEN 'TIDAK ADA DATA'
            ELSE CONCAT(
                ROUND(
                    SUM(lqir.result_numerator_value) /
                    SUM(lqir.result_denumerator_value) *
                    lqi.indicator_factors
                , 2),
                ' ',
                lqi.indicator_units
            )
        END AS total
    ");

        $builder->join('local_quality_indicator lqi', 'lqir.result_indicator_id = lqi.indicator_id', 'LEFT');

        $builder->where('YEAR(lqir.result_period)', $tahun);
        $builder->where('lqir.result_indicator_id', $indicatorId);

        $builder->groupBy([
            'lqir.result_department_id',
            'MONTH(lqir.result_period)'
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
            FROM local_quality_indicator_group
            JOIN local_quality_indicator ON local_quality_indicator.indicator_id = local_quality_indicator_group.group_indicator_id
            JOIN master_institution_department ON master_institution_department.department_id = local_quality_indicator_group.group_department_id
            WHERE local_quality_indicator_group.group_indicator_id = ?
            AND local_quality_indicator.indicator_category_id = '5' 
            AND local_quality_indicator.indicator_record_status = 'A'
            {$searchCondition}
        ", [$indicatorId]);

        return $query->getRow()->total ?? 0;
    }

    /**
     * Hitung rekap IMPRS dengan filter
     */
    public function countFilteredRekapImprs($post = [])
    {
        $vtahun = isset($post['vtahun']) ? (int) $post['vtahun'] : (int) date('Y');
        $userRole = session('user_role') ?? '';
        $userDepartmentId = session('department_id') ?? 0;

        $db = db_connect();

        // Search condition
        $searchCondition = '';
        if (isset($post['search']['value']) && !empty($post['search']['value'])) {
            $searchValue = $post['search']['value'];
            $searchCondition = "AND (local_quality_indicator.indicator_element LIKE '%{$searchValue}%' 
                               OR local_quality_indicator.indicator_name_id LIKE '%{$searchValue}%')";
        }

        // Query sama dengan getIndicatorImprs tapi hanya COUNT
        $query = $db->query("
            SELECT COUNT(*) as total FROM (
                SELECT DISTINCT local_quality_indicator.indicator_id
                FROM local_quality_indicator_group
                JOIN local_quality_indicator ON local_quality_indicator.indicator_id = local_quality_indicator_group.group_indicator_id
                JOIN master_institution_department ON master_institution_department.department_id = local_quality_indicator_group.group_department_id
                WHERE local_quality_indicator.indicator_category_id = '5'
                AND local_quality_indicator.indicator_record_status = 'A'
                AND local_quality_indicator_group.group_record_status = 'A'
                AND (local_quality_indicator_group.group_period = ? 
                     OR local_quality_indicator_group.group_period = ? 
                     OR local_quality_indicator_group.group_period = ?)
                " . ((!in_array($userRole, ['ADMINISTRATOR', 'KOMITE']) && $userDepartmentId > 0) ? "AND local_quality_indicator_group.group_department_id = " . $userDepartmentId : "") . "
                {$searchCondition}
                GROUP BY local_quality_indicator.indicator_id
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
        // Clear cache keys yang relevan
        $cache->delete('count_all_');
        $cache->delete('available_group_periods');
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
        $query = $db->query("SELECT DISTINCT group_period FROM local_quality_indicator_group WHERE group_record_status = 'A' ORDER BY group_period DESC");
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

        // Samakan filter dengan getIndicatorImprs - pakai local_quality_indicator_group
        $availablePeriods = $this->getAvailableGroupPeriods();
        $usePeriod = in_array($tahun, $availablePeriods) ? $tahun : min($availablePeriods);

        $builder = $db->table('local_quality_indicator lqi');

        $builder->select("
            lqi.indicator_id,
            lqi.indicator_element,
            lqi.indicator_target,
            lqi.indicator_factors,
            lqi.indicator_units,
            lqi.indicator_target_calculation
        ");

        $builder->join('local_quality_indicator_group lqig', 'lqi.indicator_id = lqig.group_indicator_id', 'left');

        $builder->where("lqi.indicator_category_id", '5');
        $builder->where("lqi.indicator_record_status", 'A');
        $builder->where('lqig.group_record_status', 'A');

        // Filter group_period untuk IMPRS
        $availablePeriods = $this->getAvailableGroupPeriods();
        $usePeriod = in_array($tahun, $availablePeriods) ? $tahun : min($availablePeriods);
        $builder->groupStart()
            ->where('lqig.group_period', $usePeriod)
            ->orWhere('lqig.group_period', $usePeriod - 1)
            ->orWhere('lqig.group_period', $usePeriod - 2)
            ->orWhere('lqig.group_period IS NULL', null, false)
        ->groupEnd();

        $builder->groupStart()
            ->where('lqi.indicator_active_from IS NULL', null, false)
            ->orWhere("YEAR(lqi.indicator_active_from) <= {$tahun}", null, false);
        $builder->groupEnd();
        $builder->groupStart()
            ->where('lqi.indicator_active_to IS NULL', null, false)
            ->orWhere("YEAR(lqi.indicator_active_to) >= {$tahun}", null, false);
        $builder->groupEnd();

        $builder->groupBy('lqi.indicator_id');

        $indicators = $builder->get()->getResult();

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

        // Urutin: yang punya data di atas, yang tidak di bawah
        $withData = [];
        $withoutData = [];

        foreach ($results as $row) {
            // Cek apakah indicator punya data (ada entry di bulan manapun)
            $hasData = false;
            for ($b = 1; $b <= 12; $b++) {
                if (isset($monthlyByIndicator[$row['indicator_id']][$b])) {
                    $hasData = true;
                    break;
                }
            }

            if ($hasData) {
                $withData[] = $row;
            } else {
                $withoutData[] = $row;
            }
        }

        // Gabungkan: yang punya data di atas
        $results = array_merge($withData, $withoutData);

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
                SUM(lqir.result_numerator_value) AS num,
                SUM(lqir.result_denumerator_value) AS denum,
                lqi.indicator_units
            FROM local_quality_indicator_result lqir
            JOIN local_quality_indicator lqi ON lqir.result_indicator_id = lqi.indicator_id
            WHERE lqir.result_indicator_id = ?
            AND lqir.result_period BETWEEN '{$tahun}-{$bulanMulaiStr}-01' 
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
     * Ambil data bulanan untuk satu indikator
     */
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

    /**
     * Ambil nilai triwulan
     */
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

    /**
     * Ambil nilai semester
     */
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

    /**
     * Ambil nilai tahunan
     */
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

    /**
     * Ambil nilai per tahun (5 tahun terakhir)
     */
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
}
