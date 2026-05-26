<?php

namespace App\Models;

use CodeIgniter\Model;

class LoadModuleForminputModel extends Model
{
    protected $table = 'quality_indicator_result';
    protected $primaryKey = 'result_id';
    protected $allowedFields = [
        'result_indicator_id',
        'result_department_id',
        'result_period',
        'result_numerator_value',
        'result_denumerator_value',
        'result_create_by',
        'result_create_at',
        'result_update_by',
        'result_update_at'
    ];
    protected $useTimestamps = false;

    // Get INM indicators for the form datatable
    public function getFormIndicators($tahun = null, $departmentId = null)
    {
        if ($tahun === null) {
            $tahun = date('Y');
        }
        
        $db = db_connect();
        
        $builder = $db->table('quality_indicator_group qig');
        $builder->select('
            qig.group_indicator_id,
            qig.group_department_id,
            qig.group_days,
            qi.indicator_id,
            qi.indicator_element,
            qi.indicator_target,
            qi.indicator_units,
            qi.indicator_target_unit,
            qi.indicator_target_calculation,
            qi.indicator_factors,
            mid.department_id,
            mid.department_name
        ');
        $builder->join('quality_indicator qi', 'qi.indicator_id = qig.group_indicator_id', 'left');
        $builder->join('master_institution_department mid', 'mid.department_id = qig.group_department_id', 'left');
        $builder->where('qi.indicator_category_id', '4');
        $builder->where('qi.indicator_record_status', 'A');
        $builder->groupStart();
        $builder->where('qig.group_period', $tahun);
        $builder->orWhere('qig.group_period', $tahun - 1);
        $builder->orWhere('qig.group_period', $tahun - 2);
        $builder->groupEnd();

        if ($departmentId !== null && $departmentId > 0) {
            $builder->where('qig.group_department_id', $departmentId);
        }

        $userRole = session()->get('user_role') ?? '';
        $userDepartmentId = session()->get('department_id') ?? 0;
        if (!in_array($userRole, ['ADMINISTRATOR', 'KOMITE']) && $userDepartmentId > 0) {
            $builder->where('qig.group_department_id', $userDepartmentId);
        }

        $builder->groupBy('qig.group_indicator_id, qig.group_department_id');

        return $builder->get()->getResult();
    }

    // Get indicator detail with existing data for a specific date
    public function getIndicatorDetail($indicatorId, $departmentId, $tanggal)
    {
        $db = db_connect();
        
        // Get indicator info
        $indicator = $db->table('quality_indicator')
            ->where('indicator_id', $indicatorId)
            ->where('indicator_category_id', '4')
            ->whereIn('indicator_record_status', ['A', 'D'])
            ->get()
            ->getRow();

        // Get existing data for the date
        $tahun = date('Y', strtotime($tanggal));
        $bulan = date('m', strtotime($tanggal));
        $hari = date('d', strtotime($tanggal));
        
        $existingData = $db->table('quality_indicator_result')
            ->where('result_indicator_id', $indicatorId)
            ->where('result_department_id', $departmentId)
            ->where('YEAR(result_period)', $tahun)
            ->where('MONTH(result_period)', $bulan)
            ->where('DAY(result_period)', $hari)
            ->get()
            ->getResult();

        // Get monthly total
        $monthlyTotal = $db->table('quality_indicator_result')
            ->select('
                SUM(result_numerator_value) AS num,
                SUM(result_denumerator_value) AS denum
            ')
            ->where('result_indicator_id', $indicatorId)
            ->where('result_department_id', $departmentId)
            ->where('YEAR(result_period)', $tahun)
            ->where('MONTH(result_period)', $bulan)
            ->get()
            ->getRow();

        return [
            'indicator' => $indicator,
            'existing_data' => $existingData,
            'monthly_total' => $monthlyTotal
        ];
    }

    // Save INM result data (insert or update)
    public function saveResult($indicatorId, $departmentId, $tanggal, $numerator, $denumerator)
    {
        $db = db_connect();
        
        $num = is_numeric($numerator) ? (float) $numerator : 0;
        $den = is_numeric($denumerator) ? (float) $denumerator : 0;
        $user_id = session()->get('hris_user_id') ?? session()->get('user_id') ?? 0;

        // Check if record exists
        $existing = $db->table('quality_indicator_result')
            ->where('result_indicator_id', $indicatorId)
            ->where('result_department_id', $departmentId)
            ->where('result_period', $tanggal)
            ->get()
            ->getRow();

        if ($existing) {
            // Update existing
            $db->table('quality_indicator_result')
                ->where('result_id', $existing->result_id)
                ->update([
                    'result_numerator_value'   => $num,
                    'result_denumerator_value' => $den,
                    'result_update_by'         => $user_id,
                    'result_update_at'         => date('Y-m-d H:i:s')
                ]);
        } else {
            // Insert new
            $db->table('quality_indicator_result')
                ->insert([
                    'result_indicator_id'       => $indicatorId,
                    'result_department_id'      => $departmentId,
                    'result_period'             => $tanggal,
                    'result_numerator_value'    => $num,
                    'result_denumerator_value'  => $den,
                    'result_create_by'          => $user_id,
                    'result_create_at'          => date('Y-m-d H:i:s')
                ]);
        }
        
        return $db->affectedRows() > 0;
    }

    public function getFillStatus(int $tahun, string $bulan)
    {
        $db = db_connect();

        $userRole = session()->get('user_role') ?? '';
        $userDepartmentId = session()->get('department_id') ?? 0;

        $builder = $db->table('quality_indicator_result qir');
        $builder->select('
            qir.result_indicator_id AS indicator_id,
            qir.result_department_id AS department_id,
            MAX(qir.result_period) AS last_fill_date,
            COUNT(qir.result_id) AS fill_count,
            SUM(qir.result_numerator_value) AS monthly_num,
            SUM(qir.result_denumerator_value) AS monthly_den
        ');
        $builder->join('quality_indicator qi', 'qi.indicator_id = qir.result_indicator_id', 'left');
        $builder->where('qi.indicator_category_id', '4');
        $builder->where('YEAR(qir.result_period)', $tahun);
        $builder->where('MONTH(qir.result_period)', $bulan);

        if (!in_array($userRole, ['ADMINISTRATOR', 'KOMITE']) && $userDepartmentId > 0) {
            $builder->where('qir.result_department_id', $userDepartmentId);
        }

        $builder->groupBy('qir.result_indicator_id, qir.result_department_id');

        return $builder->get()->getResult();
    }

    // ==================== DAILY DETAIL TABLE ====================

    public function getDaysInMonth(int $bulan, int $tahun): int
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

    public function getDailyDataAllDepartments(int $indicatorId, int $tahun, int $bulan)
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

        $builder->groupBy(['qir.result_department_id', 'qir.result_period']);
        $builder->orderBy('qir.result_department_id');
        $builder->orderBy('qir.result_period', 'ASC');

        return $builder->get()->getResult();
    }

    public function getDailyDataForMultipleIndicators(array $indicatorIds, int $tahun, int $bulan)
    {
        if (empty($indicatorIds)) {
            return [];
        }

        $db = db_connect();
        $builder = $db->table('quality_indicator_result qir');

        $builder->select("
            qir.result_indicator_id,
            qir.result_department_id,
            DAY(qir.result_period) AS tanggal,
            SUM(qir.result_numerator_value) AS num,
            SUM(qir.result_denumerator_value) AS denum
        ");

        $builder->whereIn('qir.result_indicator_id', $indicatorIds);
        $builder->where('YEAR(qir.result_period)', $tahun);
        $builder->where('MONTH(qir.result_period)', $bulan);

        $builder->groupBy(['qir.result_indicator_id', 'qir.result_department_id', 'qir.result_period']);
        $builder->orderBy('qir.result_indicator_id');
        $builder->orderBy('qir.result_department_id');
        $builder->orderBy('qir.result_period', 'ASC');

        return $builder->get()->getResult();
    }

    public function getDepartmentsByIndicator(int $indicatorId, int $tahun, ?int $departmentId = null)
    {
        $db = db_connect();

        $deptCondition = '';
        if ($departmentId !== null) {
            $deptCondition = "AND qig.group_department_id = " . (int) $departmentId;
        }

        $query = $db->query("
            SELECT DISTINCT
                qig.group_indicator_id AS indicator_id,
                qig.group_department_id AS department_id,
                mid.department_name
            FROM quality_indicator_group qig
            JOIN quality_indicator qi ON qi.indicator_id = qig.group_indicator_id
            JOIN master_institution_department mid ON mid.department_id = qig.group_department_id
            WHERE qi.indicator_category_id = '4'
            AND qi.indicator_record_status IN ('A', 'D')
            AND qig.group_record_status = 'A'
            AND qig.group_indicator_id = ?
            {$deptCondition}
            GROUP BY mid.department_id
            ORDER BY mid.department_name ASC
        ", [$indicatorId]);

        return $query->getResult();
    }

    public function hitungTercapai(?float $nilai, float $target, string $operator): ?bool
    {
        if ($nilai === null) {
            return null;
        }
        $op = empty($operator) ? '>=' : $operator;
        return match ($op) {
            '>=' => $nilai >= $target,
            '<=' => $nilai <= $target,
            '>'  => $nilai > $target,
            '<'  => $nilai < $target,
            '='  => $nilai == $target,
            default => $nilai >= $target,
        };
    }
}
