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
     * Ambil SEMUA data bulanan dalam 1 query (OPTIMIZED)
     */
    public function getAllMonthlyData(array $indicatorIds, int $tahun)
    {
        if (empty($indicatorIds)) {
            return [];
        }

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

        return $data;
    }

    /**
     * Ambil indikator dengan pagination
     */
    public function getIndicatorInm($post)
    {
        $db = db_connect();
        $builder = $db->table('quality_indicator_group');
        $builder->distinct();

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

        $builder->where("quality_indicator.indicator_category_id = '4'");
        $builder->where("quality_indicator.indicator_record_status = 'A'");

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
     * Hitung semua rekap INM
     */
    public function countAllRekapInm($post = [])
    {
        $db = db_connect();
        $builder = $db->table('quality_indicator_group');
        $builder->distinct();

        $builder->select("quality_indicator.indicator_id");

        $builder->join('quality_indicator', 'quality_indicator.indicator_id = quality_indicator_group.group_indicator_id');
        $builder->join('master_institution_department', 'master_institution_department.department_id = quality_indicator_group.group_department_id');

        $builder->where("quality_indicator.indicator_category_id = '4'");
        $builder->where("quality_indicator.indicator_record_status = 'A'");

        $vtahun = isset($post['vtahun']) ? (int) $post['vtahun'] : (int) date('Y');
        $builder->groupStart();
        $builder->where("quality_indicator_group.group_period", $vtahun);
        $builder->orWhere('quality_indicator_group.group_period', $vtahun - 1);
        $builder->orWhere('quality_indicator_group.group_period', $vtahun - 2);
        $builder->groupEnd();

        // Filter by user group
        $userGroupId = session('group_id') ?? 0;
        $userDepartmentId = session('department_id') ?? 0;

        if (!in_array($userGroupId, [1, 15])) {
            $builder->where('master_institution_department.department_id', $userDepartmentId);
        }

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

        return $builder->countAllResults();
    }

    /**
     * Ambil detail INM by ID
     */
    public function getDetailByIdInm(int $indicatorId)
    {
        $db = db_connect();
        $builder = $db->table('quality_indicator');
        $builder->select("indicator_id, indicator_element, indicator_target");
        $builder->where('indicator_id', $indicatorId);
        return $builder->get()->getRow();
    }

    /**
     * Hitung rekap INM dengan filter
     */
    public function countFilteredRekapInm($post = [])
    {
        $db = db_connect();
        $builder = $db->table('quality_indicator_group');
        $builder->distinct();

        $builder->select("quality_indicator.indicator_id");

        $builder->join('quality_indicator', 'quality_indicator.indicator_id = quality_indicator_group.group_indicator_id');
        $builder->join('master_institution_department', 'master_institution_department.department_id = quality_indicator_group.group_department_id');

        $builder->where("quality_indicator.indicator_category_id = '4'");
        $builder->where("quality_indicator.indicator_record_status = 'A'");

        $vtahun = isset($post['vtahun']) ? (int) $post['vtahun'] : (int) date('Y');
        $builder->groupStart();
        $builder->where("quality_indicator_group.group_period", $vtahun);
        $builder->orWhere('quality_indicator_group.group_period', $vtahun - 1);
        $builder->orWhere('quality_indicator_group.group_period', $vtahun - 2);
        $builder->groupEnd();

        // Filter by user group
        $userGroupId = session('group_id') ?? 0;
        $userDepartmentId = session('department_id') ?? 0;

        if (!in_array($userGroupId, [1, 15])) {
            $builder->where('master_institution_department.department_id', $userDepartmentId);
        }

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

        return $builder->countAllResults();
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
