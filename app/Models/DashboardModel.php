<?php

namespace App\Models;

use CodeIgniter\Model;

class DashboardModel extends Model
{
    protected $table = 'quality_indicator';
    protected $primaryKey = 'indicator_id';

    private array $typeMapping = [
        'inm'    => 1,
        'imprs'  => 5,
        'impunit'=> 6,
        'ikp'    => 7,
    ];

    private array $labelMapping = [
        1 => 'INM',
        5 => 'IMPRS',
        6 => 'IMPUNIT',
        7 => 'IKP',
    ];

    public function getSummary(int $tahun, ?int $departmentId = null): array
    {
        $db = db_connect();
        $result = [];

        foreach ([1, 5, 6, 7] as $type) {
            $table = $type === 1 ? 'quality_indicator_group' : 'local_quality_indicator_group';

            $sql = "SELECT COUNT(DISTINCT group_indicator_id) AS cnt
                    FROM {$table}
                    WHERE group_type = ?
                      AND group_record_status = 'A'";
            $params = [$type];

            if ($departmentId !== null) {
                $sql .= " AND group_department_id = ?";
                $params[] = (string) $departmentId;
            }

            $row = $db->query($sql, $params)->getRow();
            $result[$type] = [
                'label' => $this->labelMapping[$type],
                'count' => (int) ($row->cnt ?? 0),
            ];
        }

        return $result;
    }

    public function getInputProgress(int $tahun, int $bulan, ?int $departmentId = null): array
    {
        $db = db_connect();
        $result = [];

        foreach ([1, 5, 6, 7] as $type) {
            $groupTable = $type === 1 ? 'quality_indicator_group' : 'local_quality_indicator_group';
            $resultTable = $type === 1 ? 'quality_indicator_result' : 'local_quality_indicator_result';

            $periodStart = sprintf('%s-%02s-01', $tahun, $bulan);

            $groupBuilder = $db->table($groupTable)
                ->where('group_type', $type)
                ->where('group_record_status', 'A');
            if ($departmentId !== null) {
                $groupBuilder->where('group_department_id', (string) $departmentId);
            }
            $total = (int) $groupBuilder->countAllResults();

            $filledSql = "SELECT COUNT(DISTINCT CONCAT(r.result_indicator_id, '-', r.result_department_id)) as cnt
                FROM {$resultTable} r
                INNER JOIN {$groupTable} g
                    ON r.result_indicator_id = g.group_indicator_id
                    AND r.result_department_id = g.group_department_id
                    AND g.group_type = ?
                    AND g.group_record_status = 'A'
                WHERE r.result_period = ?
                    AND r.result_record_status IN ('D', 'A')";
            $params = [$type, $periodStart];
            if ($departmentId !== null) {
                $filledSql .= " AND r.result_department_id = ?";
                $params[] = (string) $departmentId;
            }

            $filled = $db->query($filledSql, $params)->getRowArray();
            $filledCount = (int) ($filled['cnt'] ?? 0);
            $pct = $total > 0 ? round(($filledCount / $total) * 100, 1) : 0;

            $result[$type] = [
                'label'  => $this->labelMapping[$type],
                'total'  => $total,
                'filled' => $filledCount,
                'pct'    => $pct,
            ];
        }

        return $result;
    }

    public function getTargetStatus(int $tahun, int $bulan, ?int $departmentId = null): array
    {
        $db = db_connect();
        $result = [];

        foreach ([1, 5, 6, 7] as $type) {
            $resultTable = $type === 1 ? 'quality_indicator_result' : 'local_quality_indicator_result';
            $indicatorTable = $type === 1 ? 'quality_indicator' : 'local_quality_indicator';
            $groupTable = $type === 1 ? 'quality_indicator_group' : 'local_quality_indicator_group';

            $periodStart = sprintf('%s-%02s-01', $tahun, $bulan);

            $builder = $db->table($resultTable . ' r')
                ->select("
                    r.result_indicator_id,
                    r.result_department_id,
                    SUM(r.result_numerator_value) AS num,
                    SUM(r.result_denumerator_value) AS denum,
                    i.indicator_target,
                    i.indicator_factors,
                    i.indicator_target_calculation
                ")
                ->join("{$indicatorTable} i", 'r.result_indicator_id = i.indicator_id', 'LEFT')
                ->where('r.result_period', $periodStart)
                ->whereIn('r.result_record_status', ['D', 'A'])
                ->groupBy('r.result_indicator_id, r.result_department_id');

            if ($departmentId !== null) {
                $builder->where('r.result_department_id', (string) $departmentId);
            }

            $rows = $builder->get()->getResult();

            $tercapai = 0;
            $tidakTercapai = 0;
            $tidakAdaData = 0;

            foreach ($rows as $row) {
                $num = (float) $row->num;
                $denum = (float) $row->denum;
                $target = (float) ($row->indicator_target ?? 0);
                $factors = (float) ($row->indicator_factors ?? 1);
                $operator = $row->indicator_target_calculation ?? '>=';

                $nilai = $denum > 0 ? round(($num / $denum) * $factors, 2) : null;

                if ($nilai === null) {
                    $tidakAdaData++;
                    continue;
                }

                if ($this->cekTercapai($nilai, $target, $operator)) {
                    $tercapai++;
                } else {
                    $tidakTercapai++;
                }
            }

            $groupBuilder = $db->table($groupTable)
                ->where('group_type', $type)
                ->where('group_record_status', 'A');
            if ($departmentId !== null) {
                $groupBuilder->where('group_department_id', (string) $departmentId);
            }
            $totalGroups = (int) $groupBuilder->countAllResults();

            $belumInput = $totalGroups - ($tercapai + $tidakTercapai + $tidakAdaData);

            $result[$type] = [
                'label'         => $this->labelMapping[$type],
                'tercapai'      => $tercapai,
                'tidak_tercapai'=> $tidakTercapai,
                'tidak_ada_data'=> $tidakAdaData,
                'belum_input'   => max(0, $belumInput),
                'total'         => $totalGroups,
            ];
        }

        return $result;
    }

    public function getDepartmentsWithoutInput(int $tahun, int $bulan): array
    {
        $db = db_connect();
        $periodStart = sprintf('%s-%02s-01', $tahun, $bulan);

        // Semua departemen yg punya grup aktif (tipe apa pun)
        $deptSql = "
            SELECT DISTINCT mid.department_id, mid.department_name
            FROM master_institution_department mid
            WHERE mid.department_record_status = 'A'
            AND EXISTS (
                SELECT 1 FROM quality_indicator_group qig
                WHERE qig.group_department_id = mid.department_id AND qig.group_record_status = 'A'
                UNION
                SELECT 1 FROM local_quality_indicator_group lig
                WHERE lig.group_department_id = mid.department_id AND lig.group_record_status = 'A'
            )
            ORDER BY mid.department_name
        ";
        $departments = $db->query($deptSql)->getResult();

        // Per tipe, kumpulkan ID departemen yg punya grup dan yg sudah input
        $deptWithGroup = [];
        $deptWithInput = [];

        foreach ([1, 5, 6, 7] as $type) {
            $groupTable = $type === 1 ? 'quality_indicator_group' : 'local_quality_indicator_group';
            $resultTable = $type === 1 ? 'quality_indicator_result' : 'local_quality_indicator_result';

            $rows = $db->query(
                "SELECT DISTINCT group_department_id FROM {$groupTable} WHERE group_type = ? AND group_record_status = 'A'",
                [$type]
            )->getResult();
            $deptWithGroup[$type] = array_map(fn($r) => $r->group_department_id, $rows);

            $rows = $db->query(
                "SELECT DISTINCT r.result_department_id
                 FROM {$resultTable} r
                 JOIN {$groupTable} g ON g.group_indicator_id = r.result_indicator_id
                     AND g.group_department_id = r.result_department_id
                     AND g.group_type = ?
                     AND g.group_record_status = 'A'
                 WHERE r.result_period = ? AND r.result_record_status IN ('D','A')",
                [$type, $periodStart]
            )->getResult();
            $deptWithInput[$type] = array_map(fn($r) => $r->result_department_id, $rows);
        }

        // Cari selisih: punya grup tapi belum input per tipe
        $result = [];
        foreach ($departments as $dept) {
            $missingTypes = [];
            foreach ([1, 5, 6, 7] as $type) {
                if (in_array($dept->department_id, $deptWithGroup[$type])
                    && !in_array($dept->department_id, $deptWithInput[$type])) {
                    $missingTypes[] = $this->labelMapping[$type];
                }
            }
            if (count($missingTypes) > 0) {
                $result[] = (object) [
                    'department_id'   => $dept->department_id,
                    'department_name' => $dept->department_name,
                    'missing_types'   => $missingTypes,
                ];
            }
        }

        return $result;
    }

    public function getMonthlyTrend(int $tahun, ?int $departmentId = null): array
    {
        $db = db_connect();
        $result = [];

        foreach ([1, 5, 6, 7] as $type) {
            $resultTable = $type === 1 ? 'quality_indicator_result' : 'local_quality_indicator_result';
            $indicatorTable = $type === 1 ? 'quality_indicator' : 'local_quality_indicator';

            $builder = $db->table($resultTable . ' r')
                ->select("
                    MONTH(r.result_period) AS bulan,
                    r.result_indicator_id,
                    SUM(r.result_numerator_value) AS num,
                    SUM(r.result_denumerator_value) AS denum,
                    i.indicator_factors
                ")
                ->join("{$indicatorTable} i", 'r.result_indicator_id = i.indicator_id', 'LEFT')
                ->where('YEAR(r.result_period)', $tahun)
                ->whereIn('r.result_record_status', ['D', 'A']);

            if ($departmentId !== null) {
                $builder->where('r.result_department_id', (string) $departmentId);
            }

            $rows = $builder->groupBy('MONTH(r.result_period), r.result_indicator_id')
                ->orderBy('MONTH(r.result_period)', 'ASC')
                ->get()
                ->getResult();

            $monthly = array_fill(1, 12, ['values' => []]);
            foreach ($rows as $row) {
                $num = (float) $row->num;
                $denum = (float) $row->denum;
                $factors = (float) ($row->indicator_factors ?? 1);
                $nilai = $denum > 0 ? round(($num / $denum) * $factors, 2) : 0;
                $monthly[(int) $row->bulan]['values'][] = $nilai;
            }

            // Rata-rata nilai per bulan
            foreach ($monthly as $bulan => &$data) {
                $vals = $data['values'];
                $data['avg'] = count($vals) > 0 ? round(array_sum($vals) / count($vals), 2) : 0;
                unset($data['values']);
            }

            $result[$type] = [
                'label'   => $this->labelMapping[$type],
                'monthly' => $monthly,
            ];
        }

        return $result;
    }

    public function getDraftCounts(int $tahun, int $bulan, ?int $departmentId = null): array
    {
        $db = db_connect();
        $periodStart = sprintf('%s-%02s-01', $tahun, $bulan);
        $result = [];

        foreach ([1, 5, 6, 7] as $type) {
            $groupTable = $type === 1 ? 'quality_indicator_group' : 'local_quality_indicator_group';
            $resultTable = $type === 1 ? 'quality_indicator_result' : 'local_quality_indicator_result';

            $builder = $db->table("{$resultTable} r")
                ->select("COUNT(DISTINCT CONCAT(r.result_indicator_id, '-', r.result_department_id)) AS cnt")
                ->join("{$groupTable} g", 'g.group_indicator_id = r.result_indicator_id AND g.group_department_id = r.result_department_id')
                ->where('g.group_type', $type)
                ->where('g.group_record_status', 'A')
                ->where('r.result_period', $periodStart)
                ->where('r.result_record_status', 'D');

            if ($departmentId !== null) {
                $builder->where('r.result_department_id', (string) $departmentId);
            }

            $row = $builder->get()->getRow();
            $result[$type] = [
                'label' => $this->labelMapping[$type],
                'draft' => (int) ($row->cnt ?? 0),
            ];
        }

        return $result;
    }

    public function getTopBottomIndicators(int $tahun, int $bulan, ?int $departmentId = null, string $type = 'inm', int $limit = 5): array
    {
        $groupType = $this->typeMapping[$type] ?? 1;
        $resultTable = $groupType === 1 ? 'quality_indicator_result' : 'local_quality_indicator_result';
        $indicatorTable = $groupType === 1 ? 'quality_indicator' : 'local_quality_indicator';
        $groupTable = $groupType === 1 ? 'quality_indicator_group' : 'local_quality_indicator_group';

        $db = db_connect();
        $periodStart = sprintf('%s-%02s-01', $tahun, $bulan);

        $builder = $db->table($resultTable . ' r')
            ->select("
                r.result_indicator_id,
                r.result_department_id,
                mid.department_name,
                i.indicator_element,
                i.indicator_target,
                i.indicator_factors,
                i.indicator_target_calculation,
                SUM(r.result_numerator_value) AS num,
                SUM(r.result_denumerator_value) AS denum
            ")
            ->join("{$indicatorTable} i", 'r.result_indicator_id = i.indicator_id', 'LEFT')
            ->join('master_institution_department mid', 'r.result_department_id = mid.department_id', 'LEFT')
            ->where('r.result_period', $periodStart)
            ->whereIn('r.result_record_status', ['D', 'A'])
            ->groupBy('r.result_indicator_id, r.result_department_id');

        if ($departmentId !== null) {
            $builder->where('r.result_department_id', (string) $departmentId);
        }

        $rows = $builder->get()->getResult();

        $indicators = [];
        foreach ($rows as $row) {
            $num = (float) $row->num;
            $denum = (float) $row->denum;
            $target = (float) ($row->indicator_target ?? 0);
            $factors = (float) ($row->indicator_factors ?? 1);
            $operator = $row->indicator_target_calculation ?? '>=';

            $nilai = $denum > 0 ? round(($num / $denum) * $factors, 2) : null;
            if ($nilai === null) continue;

            $tercapai = $this->cekTercapai($nilai, $target, $operator);

            $indicators[] = [
                'indicator_id'     => $row->result_indicator_id,
                'department_id'    => $row->result_department_id,
                'department_name'  => $row->department_name ?? '-',
                'indicator_element'=> substr($row->indicator_element ?? '-', 0, 80),
                'nilai'            => $nilai,
                'target'           => $target,
                'tercapai'         => $tercapai,
                'selisih'          => abs($nilai - $target),
            ];
        }

        usort($indicators, fn($a, $b) => $b['nilai'] <=> $a['nilai']);

        $top = array_slice($indicators, 0, $limit);

        $bottom = [];
        if (count($indicators) > $limit) {
            $bottom = array_reverse(array_slice($indicators, -$limit));
        }

        return [
            'top'         => $top,
            'bottom'      => $bottom,
            'total_count' => count($indicators),
        ];
    }

    private function cekTercapai($nilai, float $target, string $operator): bool
    {
        if ($nilai === null) return false;
        $angka = (float) preg_replace('/[^0-9.]/', '', (string) $nilai);
        return match ($operator) {
            '>=' => $angka >= $target,
            '>'  => $angka > $target,
            '<=' => $angka <= $target,
            '<'  => $angka < $target,
            '='  => $angka == $target,
            default => $angka >= $target,
        };
    }
}
