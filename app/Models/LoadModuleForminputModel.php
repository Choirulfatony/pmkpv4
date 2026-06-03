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

    protected string $tablePrefix = '';
    protected string $categoryId = '4';

    public function __construct(string $tablePrefix = '', string $categoryId = '4')
    {
        parent::__construct();
        $this->tablePrefix = $tablePrefix;
        $this->categoryId = $categoryId;
        $this->table = $tablePrefix . 'quality_indicator_result';
    }

    public function getIndicators(int $tahun, ?int $departmentId = null, ?string $bulan = null)
    {
        if ($tahun === null) {
            $tahun = (int) date('Y');
        }

        $db = db_connect();

        if ($this->tablePrefix === 'local_') {
            // Local modules (IMPRS/IMPUNIT) - no quality_indicator_group table
            $builder = $db->table($this->tablePrefix . 'quality_indicator qi');
            $builder->select('
                qi.indicator_id,
                qi.indicator_element,
                qi.indicator_target,
                qi.indicator_units,
                qi.indicator_target_unit,
                qi.indicator_target_calculation,
                qi.indicator_factors,
                qi.indicator_frequency,
                qir.result_department_id AS department_id,
                mid.department_name
            ');
            $builder->join($this->tablePrefix . 'quality_indicator_result qir', 'qir.result_indicator_id = qi.indicator_id', 'left');
            $builder->join('master_institution_department mid', 'mid.department_id = qir.result_department_id', 'left');
            $builder->where('qi.indicator_category_id', $this->categoryId);

            if ($bulan !== null) {
                $tableResult = $this->tablePrefix . 'quality_indicator_result';
                $existsSql = "EXISTS (
                    SELECT 1 FROM {$tableResult} qir2
                    WHERE qir2.result_indicator_id = qi.indicator_id
                    AND qir2.result_record_status IN ('D', 'A')
                    AND YEAR(qir2.result_period) = {$tahun}
                    AND MONTH(qir2.result_period) = {$bulan}
                )";
                $selectedPeriod = $tahun . '-' . str_pad($bulan, 2, '0', STR_PAD_LEFT);
                if ($selectedPeriod == date('Y-m')) {
                    $builder->where('qi.indicator_record_status', 'A');
                    $builder->where($existsSql, null, false);
                } else {
                    $builder->where($existsSql, null, false);
                }
            }

            if ($departmentId !== null && $departmentId > 0) {
                $builder->where('qir.result_department_id', $departmentId);
            }

            $userRole = session()->get('user_role') ?? '';
            $userDepartmentId = session()->get('department_id') ?? 0;
            if (!in_array($userRole, ['ADMINISTRATOR', 'KOMITE']) && $userDepartmentId > 0) {
                $builder->where('qir.result_department_id', $userDepartmentId);
            }

            $builder->groupBy('qi.indicator_id, qir.result_department_id');
            $builder->orderBy('qi.indicator_id ASC');
        } else {
            // Regular modules (INM) - uses quality_indicator_group
            $builder = $db->table($this->tablePrefix . 'quality_indicator_group qig');
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
                qi.indicator_frequency,
                mid.department_id,
                mid.department_name
            ');
            $builder->join($this->tablePrefix . 'quality_indicator qi', 'qi.indicator_id = qig.group_indicator_id', 'left');
            $builder->join('master_institution_department mid', 'mid.department_id = qig.group_department_id', 'left');
            $builder->where('qi.indicator_category_id', $this->categoryId);

            if ($bulan !== null) {
                $selectedPeriod = $tahun . '-' . str_pad($bulan, 2, '0', STR_PAD_LEFT);
                $currentPeriod = date('Y-m');
                $tableResult = $this->tablePrefix . 'quality_indicator_result';

                $deptCondition = ($departmentId !== null && $departmentId > 0)
                    ? "qir.result_department_id = {$departmentId}"
                    : "qir.result_department_id = qig.group_department_id";

                $existsSql = "EXISTS (
                    SELECT 1 FROM {$tableResult} qir
                    WHERE qir.result_indicator_id = qi.indicator_id
                    AND {$deptCondition}
                    AND qir.result_record_status IN ('D', 'A')
                    AND YEAR(qir.result_period) = {$tahun}
                    AND MONTH(qir.result_period) = {$bulan}
                )";

                if ($selectedPeriod == $currentPeriod) {
                    $builder->where('qi.indicator_record_status', 'A');
                    $builder->where($existsSql, null, false);
                } else {
                    $builder->where($existsSql, null, false);
                }
            } else {
                $tableResult = $this->tablePrefix . 'quality_indicator_result';
                $builder->where("EXISTS (
                    SELECT 1 FROM {$tableResult} qir
                    WHERE qir.result_indicator_id = qi.indicator_id
                    AND qir.result_record_status IN ('D', 'A')
                )", null, false);
            }

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
        }

        return $builder->get()->getResult();
    }

    public function getIndicatorDetail(int $indicatorId, int $departmentId, string $tanggal)
    {
        $db = db_connect();

        $indicator = $db->table($this->tablePrefix . 'quality_indicator')
            ->where('indicator_id', $indicatorId)
            ->where('indicator_category_id', $this->categoryId)
            // Show indicator regardless of record status to allow viewing historical data
            // whereIn('indicator_record_status', ['A', 'D']) // currently allows A and D
            ->get()
            ->getRow();

        $tahun = (int) date('Y', strtotime($tanggal));
        $bulan = date('m', strtotime($tanggal));
        $hari = date('d', strtotime($tanggal));

        $existingData = $db->table($this->tablePrefix . 'quality_indicator_result qir')
            ->select('qir.*, up.profile_fullname')
            ->join('user_profile up', 'up.profile_id = qir.result_insert_by', 'left')
            ->where('qir.result_indicator_id', $indicatorId)
            ->where('qir.result_department_id', $departmentId)
            ->whereIn('qir.result_record_status', ['D', 'A'])
            ->where('YEAR(qir.result_period)', $tahun)
            ->where('MONTH(qir.result_period)', $bulan)
            ->where('DAY(qir.result_period)', $hari)
            ->get()
            ->getResult();

        $monthlyTotal = $db->table($this->tablePrefix . 'quality_indicator_result')
            ->select('
                SUM(result_numerator_value) AS num,
                SUM(result_denumerator_value) AS denum
            ')
            ->where('result_indicator_id', $indicatorId)
            ->where('result_department_id', $departmentId)
            ->whereIn('result_record_status', ['D', 'A'])
            ->where('YEAR(result_period)', $tahun)
            ->where('MONTH(result_period)', $bulan)
            ->get()
            ->getRow();

        $rencanaPerbaikan = $db->table('local_rencana_perbaikan')
            ->where('result_indicator_id', $indicatorId)
            ->where('result_department_id', $departmentId)
            ->where('result_period', $tanggal)
            ->where('indicator_category_id', $this->categoryId)
            ->get()
            ->getRow();

        return [
            'indicator' => $indicator,
            'existing_data' => $existingData,
            'monthly_total' => $monthlyTotal,
            'rencana_perbaikan' => $rencanaPerbaikan
        ];
    }

    public function saveResult(int $indicatorId, int $departmentId, string $tanggal, float $numerator, float $denumerator): bool
    {
        $db = db_connect();

        $num = $numerator;
        $den = $denumerator;
        $user_id = session('profile_id') ?? 0;
        $now = date('Y-m-d H:i:s');

        $existing = $db->table($this->tablePrefix . 'quality_indicator_result')
            ->where('result_indicator_id', $indicatorId)
            ->where('result_department_id', $departmentId)
            ->where('result_period', $tanggal)
            ->where('result_record_status', 'D')
            ->get()
            ->getRow();

        if ($existing) {
            $db->table($this->tablePrefix . 'quality_indicator_result')
                ->where('result_id', $existing->result_id)
                ->update([
                    'result_numerator_value'   => $num,
                    'result_denumerator_value' => $den,
                    'result_update_by'         => $user_id,
                    'result_update_date'       => $now,
                    'result_record_status'     => 'D'
                ]);
        } else {
            $db->table($this->tablePrefix . 'quality_indicator_result')
                ->insert([
                    'result_indicator_id'       => $indicatorId,
                    'result_department_id'      => (string) $departmentId,
                    'result_period'             => $tanggal,
                    'result_numerator_value'    => (string) $num,
                    'result_denumerator_value'  => (string) $den,
                    'result_record_status'      => 'D',
                    'result_insert_by'          => (string) $user_id,
                    'result_insert_date'        => $now
                ]);
        }

        return $db->affectedRows() > 0;
    }

    public function saveRencanaPerbaikan(int $indicatorId, int $departmentId, string $tanggal, float $numerator, float $denumerator, string $kendala = '', string $perbaikan = ''): bool
    {
        $db = db_connect();
        $user_id = (int) (session('profile_id') ?? 0);

        $existing = $db->table('local_rencana_perbaikan')
            ->where('result_indicator_id', $indicatorId)
            ->where('result_department_id', $departmentId)
            ->where('result_period', $tanggal)
            ->where('indicator_category_id', $this->categoryId)
            ->get()
            ->getRow();

        $data = [
            'result_numerator_value'   => (string) $numerator,
            'result_denumerator_value' => (string) $denumerator,
            'kendala'                  => $kendala,
            'perbaikan'                => $perbaikan
        ];

        if ($existing) {
            $db->table('local_rencana_perbaikan')
                ->where('rencana_id', $existing->rencana_id)
                ->update($data);
        } else {
            $data['result_indicator_id'] = $indicatorId;
            $data['result_department_id'] = $departmentId;
            $data['result_period'] = $tanggal;
            $data['indicator_category_id'] = $this->categoryId;
            $data['result_insert_date'] = date('Y-m-d');
            $data['result_insert_by'] = $user_id;

            $db->table('local_rencana_perbaikan')
                ->insert($data);
        }

        return $db->affectedRows() > 0;
    }

    public function deleteResult(int $indicatorId, int $departmentId, string $tanggal): bool
    {
        $db = db_connect();
        $user_id = session('profile_id') ?? 0;
        $now = date('Y-m-d H:i:s');

        $db->table($this->tablePrefix . 'quality_indicator_result')
            ->where('result_indicator_id', $indicatorId)
            ->where('result_department_id', $departmentId)
            ->where('result_period', $tanggal)
            ->where('result_record_status', 'D')
            ->update([
                'result_record_status'  => 'X',
                'result_delete_by'      => $user_id,
                'result_delete_date'    => $now
            ]);

        $db->table('local_rencana_perbaikan')
            ->where('result_indicator_id', $indicatorId)
            ->where('result_department_id', $departmentId)
            ->where('result_period', $tanggal)
            ->where('indicator_category_id', $this->categoryId)
            ->delete();

        return $db->affectedRows() > 0;
    }

    public function validateResult(int $indicatorId, int $departmentId, string $tanggal): bool
    {
        $db = db_connect();
        $user_id = session('profile_id') ?? 0;
        $now = date('Y-m-d H:i:s');

        $db->table($this->tablePrefix . 'quality_indicator_result')
            ->where('result_indicator_id', $indicatorId)
            ->where('result_department_id', $departmentId)
            ->where('result_period', $tanggal)
            ->where('result_record_status', 'D')
            ->update([
                'result_record_status' => 'A',
                'result_update_by'     => $user_id,
                'result_update_date'   => $now
            ]);

        return $db->affectedRows() > 0;
    }

    public function getRiwayat(string $tahun, string $bulan, ?int $departmentId = null)
    {
        $db = db_connect();

        $builder = $db->table($this->tablePrefix . 'quality_indicator_result qir');
        $builder->select("
            qir.result_id,
            qir.result_indicator_id,
            qir.result_department_id,
            qir.result_period,
            qir.result_numerator_value,
            qir.result_denumerator_value,
            qir.result_insert_by,
            qir.result_insert_date,
            qi.indicator_element,
            lrp.kendala,
            lrp.perbaikan,
            up.profile_fullname
        ");
        $builder->join($this->tablePrefix . 'quality_indicator qi', 'qi.indicator_id = qir.result_indicator_id', 'left');
        $builder->join('local_rencana_perbaikan lrp', "lrp.result_indicator_id = qir.result_indicator_id AND lrp.result_department_id = qir.result_department_id AND lrp.result_period = qir.result_period AND lrp.indicator_category_id = {$this->categoryId}", 'left');
        $builder->join('user_profile up', 'up.profile_id = qir.result_insert_by', 'left');
        $builder->where('YEAR(qir.result_period)', $tahun);
        $builder->where('MONTH(qir.result_period)', $bulan);
        $builder->whereIn('qir.result_record_status', ['D', 'A']);

        if ($departmentId !== null && $departmentId > 0) {
            $builder->where('qir.result_department_id', $departmentId);
        }

        $userRole = session()->get('user_role') ?? '';
        $userDepartmentId = session()->get('department_id') ?? 0;
        if (!in_array($userRole, ['ADMINISTRATOR', 'KOMITE']) && $userDepartmentId > 0) {
            $builder->where('qir.result_department_id', $userDepartmentId);
        }

        $builder->orderBy('qir.result_period', 'DESC');

        return $builder->get()->getResult();
    }

    public function getFillStatus(int $tahun, string $bulan)
    {
        $db = db_connect();

        $userRole = session()->get('user_role') ?? '';
        $userDepartmentId = session()->get('department_id') ?? 0;

        $builder = $db->table($this->tablePrefix . 'quality_indicator_result qir');
        $builder->select('
            qir.result_indicator_id AS indicator_id,
            qir.result_department_id AS department_id,
            MAX(qir.result_period) AS last_fill_date,
            COUNT(qir.result_id) AS fill_count,
            SUM(qir.result_numerator_value) AS monthly_num,
            SUM(qir.result_denumerator_value) AS monthly_den
        ');
        $builder->join($this->tablePrefix . 'quality_indicator qi', 'qi.indicator_id = qir.result_indicator_id', 'left');
        $builder->where('qi.indicator_category_id', $this->categoryId);
        $builder->where('qir.result_record_status', 'A');
        $builder->where('YEAR(qir.result_period)', $tahun);
        $builder->where('MONTH(qir.result_period)', $bulan);

        if (!in_array($userRole, ['ADMINISTRATOR', 'KOMITE']) && $userDepartmentId > 0) {
            $builder->where('qir.result_department_id', $userDepartmentId);
        }

        $builder->groupBy('qir.result_indicator_id, qir.result_department_id');

        return $builder->get()->getResult();
    }

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
                return ($tahun % 4 == 0 && ($tahun % 100 != 0 || $tahun % 400 == 0)) ? 29 : 28;
            default:
                return 30;
        }
    }

    public function getDailyDataAllDepartments(int $indicatorId, int $tahun, int $bulan)
    {
        $db = db_connect();
        $builder = $db->table($this->tablePrefix . 'quality_indicator_result qir');

        $builder->select("
            qir.result_department_id,
            DAY(qir.result_period) AS tanggal,
            qir.result_record_status,
            SUM(qir.result_numerator_value) AS num,
            SUM(qir.result_denumerator_value) AS denum
        ");

        $builder->where('qir.result_indicator_id', $indicatorId);
        $builder->whereIn('qir.result_record_status', ['D', 'A']);
        $builder->where('YEAR(qir.result_period)', $tahun);
        $builder->where('MONTH(qir.result_period)', $bulan);

        $builder->groupBy(['qir.result_department_id', 'qir.result_period', 'qir.result_record_status']);
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
        $builder = $db->table($this->tablePrefix . 'quality_indicator_result qir');

        $builder->select("
            qir.result_indicator_id,
            qir.result_department_id,
            DAY(qir.result_period) AS tanggal,
            qir.result_record_status,
            SUM(qir.result_numerator_value) AS num,
            SUM(qir.result_denumerator_value) AS denum
        ");

        $builder->whereIn('qir.result_indicator_id', $indicatorIds);
        $builder->whereIn('qir.result_record_status', ['D', 'A']);
        $builder->where('YEAR(qir.result_period)', $tahun);
        $builder->where('MONTH(qir.result_period)', $bulan);

        $builder->groupBy(['qir.result_indicator_id', 'qir.result_department_id', 'qir.result_period', 'qir.result_record_status']);
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
            FROM {$this->tablePrefix}quality_indicator_group qig
            JOIN {$this->tablePrefix}quality_indicator qi ON qi.indicator_id = qig.group_indicator_id
            JOIN master_institution_department mid ON mid.department_id = qig.group_department_id
            WHERE qi.indicator_category_id = ?
            AND qi.indicator_record_status IN ('A', 'D')
            AND qig.group_record_status = 'A'
            AND qig.group_indicator_id = ?
            {$deptCondition}
            GROUP BY mid.department_id
            ORDER BY mid.department_name ASC
        ", [$this->categoryId, $indicatorId]);

        return $query->getResult();
    }

    public function getCategoryDepartments()
    {
        $db = db_connect();

        if ($this->tablePrefix === 'local_') {
            $query = $db->query("
                SELECT DISTINCT
                    qir.result_department_id AS department_id,
                    mid.department_name
                FROM {$this->tablePrefix}quality_indicator_result qir
                JOIN {$this->tablePrefix}quality_indicator qi ON qi.indicator_id = qir.result_indicator_id
                JOIN master_institution_department mid ON mid.department_id = qir.result_department_id
                WHERE qi.indicator_category_id = ?
                AND qi.indicator_record_status = 'A'
                ORDER BY mid.department_name ASC
            ", [$this->categoryId]);
        } else {
            $query = $db->query("
                SELECT DISTINCT
                    qig.group_department_id AS department_id,
                    mid.department_name
                FROM {$this->tablePrefix}quality_indicator_group qig
                JOIN {$this->tablePrefix}quality_indicator qi ON qi.indicator_id = qig.group_indicator_id
                JOIN master_institution_department mid ON mid.department_id = qig.group_department_id
                WHERE qi.indicator_category_id = ?
                AND qi.indicator_record_status = 'A'
                AND qig.group_record_status = 'A'
                ORDER BY mid.department_name ASC
            ", [$this->categoryId]);
        }

        return $query->getResult();
    }

    public function getIndicatorById(int $indicatorId)
    {
        $db = db_connect();
        return $db->table($this->tablePrefix . 'quality_indicator')
            ->where('indicator_id', $indicatorId)
            ->where('indicator_category_id', $this->categoryId)
            ->whereIn('indicator_record_status', ['A', 'D'])
            ->get()
            ->getRow();
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

    public function getPendingApproval(int $tahun, int $bulan)
    {
        $db = db_connect();
        $bulanStr = str_pad((string) $bulan, 2, '0', STR_PAD_LEFT);

        return $db->query("
            SELECT
                qir.result_id,
                qir.result_indicator_id,
                qir.result_department_id,
                qir.result_period,
                qir.result_numerator_value,
                qir.result_denumerator_value,
                qir.result_record_status,
                qir.result_insert_by,
                qi.indicator_element,
                qi.indicator_target,
                qi.indicator_units,
                qi.indicator_target_calculation,
                qi.indicator_factors,
                mid.department_name,
                up.profile_fullname
            FROM {$this->tablePrefix}quality_indicator_result qir
            LEFT JOIN {$this->tablePrefix}quality_indicator qi ON qi.indicator_id = qir.result_indicator_id
            JOIN master_institution_department mid ON mid.department_id = qir.result_department_id
            LEFT JOIN user_profile up ON up.profile_id = qir.result_insert_by
            WHERE qir.result_record_status = 'D'
              AND YEAR(qir.result_period) = ?
              AND MONTH(qir.result_period) = ?
            ORDER BY qir.result_indicator_id, qir.result_department_id, qir.result_period ASC
        ", [$tahun, $bulanStr])->getResult();
    }

    public function approveBatch(array $resultIds, int $userId): int
    {
        if (empty($resultIds)) {
            return 0;
        }
        $db = db_connect();
        $now = date('Y-m-d H:i:s');
        $ids = implode(',', array_map('intval', $resultIds));

        $db->query("
            UPDATE {$this->tablePrefix}quality_indicator_result
            SET result_record_status = 'A',
                result_update_by = ?,
                result_update_date = ?
            WHERE result_id IN ($ids)
              AND result_record_status = 'D'
        ", [$userId, $now]);

        return $db->affectedRows();
    }

    public function getDeletedData(int $tahun, int $bulan)
    {
        $db = db_connect();
        $bulanStr = str_pad((string) $bulan, 2, '0', STR_PAD_LEFT);

        return $db->query("
            SELECT
                qir.result_id,
                qir.result_indicator_id,
                qir.result_department_id,
                qir.result_period,
                qir.result_numerator_value,
                qir.result_denumerator_value,
                qir.result_record_status,
                qir.result_insert_by,
                qir.result_delete_by,
                qir.result_delete_date,
                qi.indicator_element,
                qi.indicator_target,
                qi.indicator_units,
                qi.indicator_factors,
                mid.department_name,
                up.profile_fullname
            FROM {$this->tablePrefix}quality_indicator_result qir
            LEFT JOIN {$this->tablePrefix}quality_indicator qi ON qi.indicator_id = qir.result_indicator_id
            JOIN master_institution_department mid ON mid.department_id = qir.result_department_id
            LEFT JOIN user_profile up ON up.profile_id = qir.result_insert_by
            WHERE qir.result_record_status = 'X'
              AND YEAR(qir.result_period) = ?
              AND MONTH(qir.result_period) = ?
            ORDER BY qir.result_delete_date DESC
        ", [$tahun, $bulanStr])->getResult();
    }

    public function canInputDate(int $indicatorId, int $departmentId, string $tanggal): array
    {
        $db = db_connect();
        $tahun = date('Y', strtotime($tanggal));
        $today = new \DateTime();
        $tgl = new \DateTime($tanggal);
        $diffDays = (int) $today->diff($tgl)->days;

        // Masa depan
        if ($tgl > $today) {
            return ['allowed' => false, 'restricted' => false, 'message' => 'Tidak bisa input untuk tanggal yang akan datang', 'max_days' => 0];
        }

        // Dalam 30 hari → allowed (full access)
        if ($diffDays <= 30) {
            return ['allowed' => true, 'restricted' => false, 'message' => '', 'max_days' => 30];
        }

        // Cek group_days
        $row = $db->table($this->tablePrefix . 'quality_indicator_group')
            ->select('group_days')
            ->where('group_indicator_id', $indicatorId)
            ->where('group_department_id', $departmentId)
            ->where('group_period', $tahun)
            ->where('group_record_status', 'A')
            ->get()
            ->getRow();

        $maxDays = $row ? (int) $row->group_days : 30;

        if ($diffDays <= $maxDays) {
            $restricted = $diffDays > 30;
            return [
                'allowed' => true,
                'restricted' => $restricted,
                'message' => $restricted ? 'Data hanya bisa dilihat, hubungi admin untuk edit/hapus' : '',
                'max_days' => $maxDays
            ];
        }

        return [
            'allowed' => false,
            'restricted' => false,
            'message' => "Input maksimal $maxDays hari ke belakang untuk indikator ini",
            'max_days' => $maxDays
        ];
    }

    public function hasExistingData(int $indicatorId, int $departmentId, string $tanggal): bool
    {
        $db = db_connect();
        $row = $db->table($this->tablePrefix . 'quality_indicator_result')
            ->where('result_indicator_id', $indicatorId)
            ->where('result_department_id', $departmentId)
            ->where('result_period', $tanggal)
            ->whereIn('result_record_status', ['D', 'A'])
            ->get()
            ->getRow();
        return $row !== null;
    }

    public function getIndicatorInfo(int $indicatorId): ?object
    {
        return $this->getIndicatorById($indicatorId);
    }

    public function getDepartmentInfo(int $departmentId): ?object
    {
        $db = db_connect();
        return $db->table('master_institution_department')
            ->where('department_id', $departmentId)
            ->get()
            ->getRow();
    }

    public function permanentDelete(array $resultIds): int
    {
        if (empty($resultIds)) {
            return 0;
        }
        $db = db_connect();
        $ids = implode(',', array_map('intval', $resultIds));

        $db->query("
            DELETE FROM {$this->tablePrefix}quality_indicator_result
            WHERE result_id IN ($ids)
              AND result_record_status = 'X'
        ");

        return $db->affectedRows();
    }
}
