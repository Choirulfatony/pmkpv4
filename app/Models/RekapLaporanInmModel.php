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

        $builder = $db->table('quality_indicator_result');
        $builder->select("
            quality_indicator.indicator_category_id,
            quality_indicator.indicator_id,
            quality_indicator.indicator_element,
            SUM(quality_indicator_result.result_numerator_value) AS num,
            SUM(quality_indicator_result.result_denumerator_value) AS denum,
            CONCAT(
                ROUND(
                    SUM(quality_indicator_result.result_numerator_value) 
                    / SUM(quality_indicator_result.result_denumerator_value) 
                    * quality_indicator.indicator_factors, 
                    2
                ), 
                quality_indicator.indicator_units
            ) AS total
        ");
        $builder->join('quality_indicator', 'quality_indicator_result.result_indicator_id = quality_indicator.indicator_id', 'LEFT');
        $builder->where("quality_indicator_result.result_period BETWEEN '{$tahun}-{$bulanx}-01' AND '{$tahun}-{$bulanx}-{$day}' AND quality_indicator.indicator_category_id = '4' AND quality_indicator.indicator_record_status = 'A'");
        $builder->where('quality_indicator.indicator_id', $indicator);
        $builder->groupBy('quality_indicator.indicator_id');

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

        // Cek cache
        $cache = \Config\Services::cache();
        $cacheKey = 'rekap_monthly_' . $tahun . '_' . md5(implode(',', $indicatorIds));
        
        if ($cached = $cache->get($cacheKey)) {
            return $cached;
        }

        // Query database
        $db = db_connect();
        $builder = $db->table('quality_indicator_result');
        $builder->select("
            quality_indicator.indicator_id,
            MONTH(quality_indicator_result.result_period) AS bulan,
            SUM(quality_indicator_result.result_numerator_value) AS num,
            SUM(quality_indicator_result.result_denumerator_value) AS denum,
            ROUND(
                SUM(quality_indicator_result.result_numerator_value) 
                / NULLIF(SUM(quality_indicator_result.result_denumerator_value), 0) 
                * quality_indicator.indicator_factors, 
                2
            ) AS total,
            quality_indicator.indicator_units AS units
        ");
        $builder->join('quality_indicator', 'quality_indicator_result.result_indicator_id = quality_indicator.indicator_id', 'LEFT');
        $builder->where("YEAR(quality_indicator_result.result_period)", $tahun);
        $builder->where("quality_indicator.indicator_category_id", '4');
        $builder->where("quality_indicator.indicator_record_status", 'A');
        $builder->whereIn('quality_indicator.indicator_id', $indicatorIds);
        $builder->groupBy('quality_indicator.indicator_id, MONTH(quality_indicator_result.result_period)');

        $results = $builder->get()->getResult();

        // Convert ke array associative: indicator_id_bulan => data
        $data = [];
        foreach ($results as $row) {
            $key = $row->indicator_id . '_' . $row->bulan;
            $data[$key] = $row;
        }

        // Simpan ke cache selama 10 menit
        $cache->save($cacheKey, $data, 600);

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
        $builder->groupStart();
        $builder->where("quality_indicator_group.group_period", $vtahun);
        $builder->orWhere('quality_indicator_group.group_period', $vtahun - 1);
        $builder->orWhere('quality_indicator_group.group_period', $vtahun - 2);
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
     * Ambil detail INM by ID
     */
    public function getDetailByIdInm(int $indicatorId)
    {
        $db = db_connect();
        $builder = $db->table('quality_indicator');
        $builder->select("indicator_id, indicator_element, indicator_target, indicator_factors, indicator_target_calculation, indicator_units");
        $builder->where('indicator_id', $indicatorId);
        return $builder->get()->getRow();
    }

    /**
     * Ambil semua ruangan untuk indicator tertentu
     */
    public function getDepartmentsByIndicator(int $indicatorId, int $tahun, $post = [])
    {
        $db = db_connect();
        $builder = $db->table('quality_indicator_group');
        
        $builder->select("
            DISTINCT master_institution_department.department_id,
            master_institution_department.department_name
        ");
        
        $builder->join('master_institution_department', 'master_institution_department.department_id = quality_indicator_group.group_department_id');
        
        $builder->where('quality_indicator_group.group_indicator_id', $indicatorId);
        $builder->groupStart();
        $builder->where('quality_indicator_group.group_period', $tahun);
        $builder->orWhere('quality_indicator_group.group_period', $tahun - 1);
        $builder->orWhere('quality_indicator_group.group_period', $tahun - 2);
        $builder->groupEnd();

        // Search filter
        if (isset($post['search']['value']) && !empty($post['search']['value'])) {
            $builder->like('master_institution_department.department_name', $post['search']['value']);
        }

        $builder->orderBy('master_institution_department.department_name', 'ASC');

        if (isset($post['length']) && $post['length'] != -1) {
            $builder->limit($post['length'], $post['start'] ?? 0);
        }

        return $builder->get()->getResult();
    }

    /**
     * Ambil semua data detail per ruangan dalam 1 query
     */
    public function getAllDetailData(int $indicatorId, int $tahun)
    {
        $cache = \Config\Services::cache();
        $cacheKey = 'detail_data_' . $indicatorId . '_' . $tahun;
        
        if ($cached = $cache->get($cacheKey)) {
            return $cached;
        }

        $db = db_connect();
        $builder = $db->table('quality_indicator_result');
        $builder->select("
            quality_indicator_result.result_department_id,
            MONTH(quality_indicator_result.result_period) AS bulan,
            SUM(quality_indicator_result.result_numerator_value) AS num,
            SUM(quality_indicator_result.result_denumerator_value) AS denum
        ");
        $builder->where('YEAR(quality_indicator_result.result_period)', $tahun);
        $builder->where('quality_indicator_result.result_indicator_id', $indicatorId);
        $builder->groupBy('quality_indicator_result.result_department_id, MONTH(quality_indicator_result.result_period)');

        $results = $builder->get()->getResult();

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
        $builder = $db->table('quality_indicator_group');
        
        $builder->select("COUNT(DISTINCT master_institution_department.department_id) as total");
        
        $builder->join('master_institution_department', 'master_institution_department.department_id = quality_indicator_group.group_department_id');
        
        $builder->where('quality_indicator_group.group_indicator_id', $indicatorId);
        $builder->groupStart();
        $builder->where('quality_indicator_group.group_period', $tahun);
        $builder->orWhere('quality_indicator_group.group_period', $tahun - 1);
        $builder->orWhere('quality_indicator_group.group_period', $tahun - 2);
        $builder->groupEnd();

        // Search filter
        if (isset($post['search']['value']) && !empty($post['search']['value'])) {
            $builder->like('master_institution_department.department_name', $post['search']['value']);
        }

        return $builder->get()->getRow()->total ?? 0;
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
        // Cache dihapus otomatis saat timeout
        // Atau bisa manual: $cache->delete('cache_key');
        return true;
    }

    // ==================== HELPER ====================

    /**
     * Hitung hari dalam bulan
     */
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
}
