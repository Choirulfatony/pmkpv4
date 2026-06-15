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

        $tableQig = $this->tablePrefix . 'quality_indicator_group';
        $tableQi  = $this->tablePrefix . 'quality_indicator';

        // Subquery: get latest period's group_days per indicator+dept
        $sub = $db->table($tableQig . ' qig2');
        $sub->select('MAX(qig2.group_period) as max_period, qig2.group_indicator_id, qig2.group_department_id');
        $sub->where('qig2.group_record_status', 'A');
        $sub->groupStart();
        $sub->where('qig2.group_period', $tahun);
        $sub->orWhere('qig2.group_period', $tahun - 1);
        $sub->orWhere('qig2.group_period', $tahun - 2);
        $sub->groupEnd();
        if ($departmentId !== null && $departmentId > 0) {
            $sub->where('qig2.group_department_id', $departmentId);
        }
        $userRole = session()->get('user_role') ?? '';
        $userDepartmentId = session()->get('department_id') ?? 0;
        if (!in_array($userRole, ['ADMINISTRATOR', 'KOMITE']) && $userDepartmentId > 0) {
            $sub->where('qig2.group_department_id', $userDepartmentId);
        }
        $sub->groupBy('qig2.group_indicator_id, qig2.group_department_id');
        $subSql = $sub->getCompiledSelect();

        $builder = $db->table($tableQig . ' qig');
        $builder->select('
            qig.group_indicator_id,
            qig.group_department_id AS department_id,
            qig.group_days,
            qi.indicator_id,
            qi.indicator_category_id,
            qi.indicator_element,
            qi.indicator_target,
            qi.indicator_units,
            qi.indicator_target_unit,
            qi.indicator_target_calculation,
            qi.indicator_factors,
            qi.indicator_frequency,
            qi.indicator_record_status,
            mid.department_name
        ');
        $builder->join('(' . $subSql . ') latest', 'latest.group_indicator_id = qig.group_indicator_id AND latest.group_department_id = qig.group_department_id AND latest.max_period = qig.group_period', 'inner');
        $builder->join($tableQi . ' qi', 'qi.indicator_id = qig.group_indicator_id', 'left');
        $builder->join('master_institution_department mid', 'mid.department_id = qig.group_department_id', 'left');
        $builder->where('qi.indicator_category_id', $this->categoryId);
        $builder->where('qig.group_record_status', 'A');

        // Re-apply department filter on main query too (in addition to subquery)
        if ($departmentId !== null && $departmentId > 0) {
            $builder->where('qig.group_department_id', $departmentId);
        }
        if (!in_array($userRole, ['ADMINISTRATOR', 'KOMITE']) && $userDepartmentId > 0) {
            $builder->where('qig.group_department_id', $userDepartmentId);
        }

        $builder->orderBy('qi.indicator_id ASC');

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

        $frequency = $indicator->indicator_frequency ?? 'D';

        $builder = $db->table($this->tablePrefix . 'quality_indicator_result qir')
            ->select('qir.*, up.profile_fullname')
            ->join('user_profile up', 'up.profile_id = qir.result_insert_by', 'left')
            ->where('qir.result_indicator_id', $indicatorId)
            ->where('qir.result_department_id', $departmentId)
            ->whereIn('qir.result_record_status', ['D', 'A'])
            ->where('YEAR(qir.result_period)', $tahun)
            ->where('MONTH(qir.result_period)', $bulan);

        if ($frequency !== 'M' && $frequency !== 'Y') {
            $builder->where('DAY(qir.result_period)', $hari);
        }

        $existingData = $builder->get()->getResult();

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

        $units = $this->getNumDenUnits([$indicatorId]);
        $numUnit = $units[$indicatorId]['num_unit'] ?? '';
        $denUnit = $units[$indicatorId]['den_unit'] ?? '';

        return [
            'indicator' => $indicator,
            'existing_data' => $existingData,
            'monthly_total' => $monthlyTotal,
            'rencana_perbaikan' => $rencanaPerbaikan,
            'num_unit' => $numUnit,
            'den_unit' => $denUnit
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

        if ($this->tablePrefix === 'local_') {
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
        } else {
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
        }

        return $query->getResult();
    }

    public function getCategoryDepartments()
    {
        $db = db_connect();

        if ($this->tablePrefix === 'local_') {
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

    public function getPendingApproval(int $tahun, int $bulan, ?int $departmentId = null, ?int $indicatorId = null)
    {
        $db = db_connect();
        $bulanStr = str_pad((string) $bulan, 2, '0', STR_PAD_LEFT);

        $sql = "
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
              AND qi.indicator_category_id = ?
              AND YEAR(qir.result_period) = ?
              AND MONTH(qir.result_period) = ?
        ";

        $params = [$this->categoryId, $tahun, $bulanStr];

        if (!empty($departmentId)) {
            $sql .= " AND qir.result_department_id = ?";
            $params[] = $departmentId;
        }

        if (!empty($indicatorId)) {
            $sql .= " AND qir.result_indicator_id = ?";
            $params[] = $indicatorId;
        }

        $sql .= " ORDER BY qir.result_indicator_id, qir.result_department_id, qir.result_period ASC";

        return $db->query($sql, $params)->getResult();
    }

    /**
     * Ambil daftar departemen yang memiliki data draft (result_record_status='D')
     * untuk kombinasi tahun+bulan+category tertentu, diurutkan alfabetis.
     *
     * @return array<int, array{department_id:int, department_name:string}>
     */
    public function getActiveDepartmentsWithDraft(int $tahun, int $bulan): array
    {
        $db = db_connect();
        $bulanStr = str_pad((string) $bulan, 2, '0', STR_PAD_LEFT);

        $rows = $db->query("
            SELECT DISTINCT
                qir.result_department_id AS department_id,
                mid.department_name
            FROM {$this->tablePrefix}quality_indicator_result qir
            INNER JOIN {$this->tablePrefix}quality_indicator qi ON qi.indicator_id = qir.result_indicator_id
            JOIN master_institution_department mid ON mid.department_id = qir.result_department_id
            WHERE qir.result_record_status = 'D'
              AND qi.indicator_category_id = ?
              AND YEAR(qir.result_period) = ?
              AND MONTH(qir.result_period) = ?
            ORDER BY mid.department_name ASC
        ", [$this->categoryId, $tahun, $bulanStr])->getResult();

        return array_map(function ($r) {
            return [
                'department_id'   => (int) $r->department_id,
                'department_name' => $r->department_name ?? '(Tanpa Nama)',
            ];
        }, $rows);
    }

    /**
     * Ambil daftar indikator yang memiliki data draft (result_record_status='D')
     * untuk kombinasi tahun+bulan+category tertentu.
     *
     * @return array<int, array{indicator_id:int, indicator_element:string}>
     */
    public function getActiveIndicatorsWithDraft(int $tahun, int $bulan, ?int $departmentId = null): array
    {
        $db = db_connect();
        $bulanStr = str_pad((string) $bulan, 2, '0', STR_PAD_LEFT);

        $sql = "
            SELECT DISTINCT
                qi.indicator_id,
                qi.indicator_element
            FROM {$this->tablePrefix}quality_indicator_result qir
            INNER JOIN {$this->tablePrefix}quality_indicator qi ON qi.indicator_id = qir.result_indicator_id
            WHERE qir.result_record_status = 'D'
              AND qi.indicator_category_id = ?
              AND YEAR(qir.result_period) = ?
              AND MONTH(qir.result_period) = ?
        ";
        $params = [$this->categoryId, $tahun, $bulanStr];

        if (!empty($departmentId)) {
            $sql .= " AND qir.result_department_id = ?";
            $params[] = $departmentId;
        }

        $sql .= " ORDER BY qi.indicator_element ASC";

        $rows = $db->query($sql, $params)->getResult();

        return array_map(function ($r) {
            return [
                'indicator_id'      => (int) $r->indicator_id,
                'indicator_element' => $r->indicator_element ?? '(Tanpa Nama)',
            ];
        }, $rows);
    }

    /**
     * Rekap bulanan per indikator, di-filter ke indikator yang memiliki data draft (status D)
     * pada bulan terpilih. Untuk setiap indikator yang lolos filter, hitung SUM(num)/SUM(denum)
     * per bulan (Jan-Des) pada tahun terpilih, kemudian nilai = (sum_num / sum_denum) * factor * 100.
     *
     * @return array<int, array{
     *     indicator_id:int,
     *     indicator_element:string,
     *     indicator_units:string,
     *     indicator_target:?string,
     *     indicator_factors:?string,
     *     indicator_target_calculation:?string,
     *     months:array<int, array{num:float, denum:float, nilai:?float, has_draft:bool, has_approved:bool}>
     * }>
     */
    public function getRecapByIndicatorWithDraft(int $tahun, int $bulan, ?int $departmentId = null, ?int $indicatorId = null): array
    {
        $db = db_connect();

        $draftSql = "
            SELECT DISTINCT qir.result_indicator_id
            FROM {$this->tablePrefix}quality_indicator_result qir
            INNER JOIN {$this->tablePrefix}quality_indicator qi
                ON qi.indicator_id = qir.result_indicator_id
            WHERE qir.result_record_status = 'D'
              AND qi.indicator_category_id = ?
              AND YEAR(qir.result_period) = ?
              AND MONTH(qir.result_period) = ?
        ";
        $draftParams = [$this->categoryId, $tahun, $bulan];
        if ($departmentId !== null && $departmentId > 0) {
            $draftSql .= " AND qir.result_department_id = ?";
            $draftParams[] = $departmentId;
        }
        if (!empty($indicatorId)) {
            $draftSql .= " AND qir.result_indicator_id = ?";
            $draftParams[] = $indicatorId;
        }
        $draftRows = $db->query($draftSql, $draftParams)->getResultArray();
        $indicatorIds = array_map(static fn($r) => (int) $r['result_indicator_id'], $draftRows);
        if (empty($indicatorIds)) {
            return [];
        }

        $idList = implode(',', array_map('intval', $indicatorIds));
        $headerSql = "
            SELECT
                qi.indicator_id,
                qi.indicator_element,
                qi.indicator_units,
                qi.indicator_target,
                qi.indicator_factors,
                qi.indicator_target_calculation
            FROM {$this->tablePrefix}quality_indicator qi
            WHERE qi.indicator_id IN ($idList)
              AND qi.indicator_category_id = ?
            ORDER BY qi.indicator_element ASC
        ";
        $indicators = $db->query($headerSql, [$this->categoryId])->getResultArray();
        if (empty($indicators)) {
            return [];
        }

        $recapSql = "
            SELECT
                qir.result_indicator_id,
                MONTH(qir.result_period) AS bulan,
                qir.result_record_status,
                COALESCE(SUM(qir.result_numerator_value), 0)   AS sum_num,
                COALESCE(SUM(qir.result_denumerator_value), 0) AS sum_denum
            FROM {$this->tablePrefix}quality_indicator_result qir
            WHERE qir.result_indicator_id IN ($idList)
              AND YEAR(qir.result_period) = ?
              AND qir.result_record_status IN ('A', 'D')
        ";
        $recapParams = [$tahun];
        if ($departmentId !== null && $departmentId > 0) {
            $recapSql .= " AND qir.result_department_id = ?";
            $recapParams[] = $departmentId;
        }
        $recapSql .= "
            GROUP BY qir.result_indicator_id, MONTH(qir.result_period), qir.result_record_status
        ";
        $recapRows = $db->query($recapSql, $recapParams)->getResultArray();

        $bucket = [];
        foreach ($recapRows as $r) {
            $iid = (int) $r['result_indicator_id'];
            $m   = (int) $r['bulan'];
            $st  = $r['result_record_status'];
            if (!isset($bucket[$iid][$m])) {
                $bucket[$iid][$m] = ['num' => 0.0, 'denum' => 0.0, 'has_draft' => false, 'has_approved' => false];
            }
            $bucket[$iid][$m]['num']   += (float) $r['sum_num'];
            $bucket[$iid][$m]['denum'] += (float) $r['sum_denum'];
            if ($st === 'D') { $bucket[$iid][$m]['has_draft']    = true; }
            if ($st === 'A') { $bucket[$iid][$m]['has_approved'] = true; }
        }

        $out = [];
        foreach ($indicators as $ind) {
            $iid = (int) $ind['indicator_id'];
            $factor = (float) ($ind['indicator_factors'] ?? 1);
            $months = [];
            for ($m = 1; $m <= 12; $m++) {
                $cell = $bucket[$iid][$m] ?? ['num' => 0.0, 'denum' => 0.0, 'has_draft' => false, 'has_approved' => false];
                $nilai = ($cell['denum'] > 0) ? round(($cell['num'] / $cell['denum']) * $factor, 2) : null;
                $months[$m] = [
                    'num'          => $cell['num'],
                    'denum'        => $cell['denum'],
                    'nilai'        => $nilai,
                    'has_draft'    => $cell['has_draft'],
                    'has_approved' => $cell['has_approved'],
                ];
            }
            $out[] = [
                'indicator_id'                => $iid,
                'indicator_element'           => $ind['indicator_element'] ?? '',
                'indicator_units'             => $ind['indicator_units'] ?? '',
                'indicator_target'            => $ind['indicator_target'],
                'indicator_factors'           => $ind['indicator_factors'],
                'indicator_target_calculation'=> $ind['indicator_target_calculation'],
                'months'                      => $months,
            ];
        }

        return $out;
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

    private function applyGroupDaysAutoRevert(int $days, ?string $changedAt): int
    {
        if ($days <= 0 || $changedAt === null) {
            return $days;
        }
        $changed = strtotime($changedAt);
        if ((time() - $changed) > 172800) { // 48 jam
            return 0;
        }
        return $days;
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

        // Get indicator frequency
        $freqRow = $db->table($this->tablePrefix . 'quality_indicator')
            ->select('indicator_frequency')
            ->where('indicator_id', $indicatorId)
            ->where('indicator_category_id', $this->categoryId)
            ->get()
            ->getRow();
        $frequency = $freqRow ? $freqRow->indicator_frequency : 'D';

        // Get group_days (used both for open_period override and normal logic)
        $groupRow = $db->table($this->tablePrefix . 'quality_indicator_group')
            ->select('group_days, group_days_changed_at')
            ->where('group_indicator_id', $indicatorId)
            ->where('group_department_id', $departmentId)
            ->where('group_period', $tahun)
            ->where('group_record_status', 'A')
            ->get()
            ->getRow();
        $groupDays = $groupRow ? (int) $groupRow->group_days : 0;
        $groupDays = $this->applyGroupDaysAutoRevert($groupDays, $groupRow->group_days_changed_at ?? null);

        // Jika group_days mencukupi, izinkan langsung (override "tanggal 6" lock)
        if ($groupDays > 0 && $diffDays <= $groupDays) {
            $restricted = false;
            return [
                'allowed' => true,
                'restricted' => $restricted,
                'message' => $restricted ? 'Data hanya bisa dilihat, hubungi admin untuk edit/hapus' : '',
                'max_days' => $groupDays
            ];
        }

        // Lock pada tgl 6 bulan berikutnya (hanya jika group_days tidak override)
        $inputMonth = (int) $tgl->format('m');
        $inputYear = (int) $tgl->format('Y');
        $cutoffMonth = $inputMonth + 1;
        $cutoffYear = $inputYear;
        if ($cutoffMonth > 12) {
            $cutoffMonth = 1;
            $cutoffYear++;
        }
        $cutoffDate = new \DateTime(sprintf('%04d-%02d-07', $cutoffYear, $cutoffMonth));
        if ($today >= $cutoffDate) {
            return ['allowed' => false, 'restricted' => false, 'message' => 'Periode input ditutup setiap tanggal 6 bulan berikutnya', 'max_days' => 0];
        }

        if ($frequency === 'W' || $frequency === 'M' || $frequency === 'Y') {
            $maxDays = $groupDays > 0 ? $groupDays : 9999;
            if ($diffDays <= $maxDays) {
                return ['allowed' => true, 'restricted' => false, 'message' => '', 'max_days' => $maxDays];
            }
            return [
                'allowed' => false,
                'restricted' => false,
                'message' => "Input maksimal $maxDays hari ke belakang untuk indikator ini",
                'max_days' => $maxDays
            ];
        }

        // For D (daily): 30-day hard limit
        if ($diffDays <= 30) {
            return ['allowed' => true, 'restricted' => false, 'message' => '', 'max_days' => 30];
        }

        // Lebih dari 30 hari → cek group_days
        $maxDays = $groupDays > 0 ? $groupDays : 30;
        if ($diffDays <= $maxDays) {
            return [
                'allowed' => true,
                'restricted' => true,
                'message' => 'Data hanya bisa dilihat, hubungi admin untuk edit/hapus',
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

    public function getNumDenUnits(array $indicatorIds): array
    {
        if (empty($indicatorIds)) {
            return [];
        }

        $db = db_connect();
        $vt = $this->tablePrefix . 'quality_indicator_variable';

        $rows = $db->table($vt)
            ->select('variable_indicator_id, variable_type, variable_unit_name')
            ->whereIn('variable_indicator_id', $indicatorIds)
            ->whereIn('variable_type', ['N', 'D'])
            ->where('variable_record_status', 'A')
            ->get()
            ->getResult();

        $result = [];
        foreach ($rows as $r) {
            $id = (int) $r->variable_indicator_id;
            if (!isset($result[$id])) {
                $result[$id] = ['num_unit' => '', 'den_unit' => ''];
            }
            if ($r->variable_type === 'N') {
                $result[$id]['num_unit'] = $r->variable_unit_name ?? '';
            } elseif ($r->variable_type === 'D') {
                $result[$id]['den_unit'] = $r->variable_unit_name ?? '';
            }
        }

        return $result;
    }
}
