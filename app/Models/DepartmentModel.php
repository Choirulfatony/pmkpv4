<?php

namespace App\Models;

use CodeIgniter\Model;

class DepartmentModel extends Model
{
    protected $table = 'master_institution_department';
    protected $primaryKey = 'department_id';
    protected $returnType = 'object';

    protected $allowedFields = [
        'department_name',
        'department_description',
        'department_institution_code',
        'department_record_status',
    ];

    private array $indicatorTypes = [
        1 => 'INM',
        5 => 'IMPRS',
        6 => 'IMPUNIT',
        7 => 'IKP',
    ];

    private array $indicatorTables = [
        4 => 'quality_indicator_group',
        5 => 'local_quality_indicator_group',
        6 => 'local_quality_indicator_group',
        7 => 'local_quality_indicator_group',
    ];

    public function getDatatable(array $post)
    {
        $db = db_connect();

        $columns = [
            0 => 'mid.department_name',
            1 => 'mid.department_description',
        ];

        $searchValue = $post['search']['value'] ?? '';
        $orderColumn = $columns[$post['order'][0]['column'] ?? 0] ?? 'mid.department_name';
        $orderDir    = $post['order'][0]['dir'] ?? 'ASC';
        $start       = (int) ($post['start'] ?? 0);
        $length      = (int) ($post['length'] ?? 10);

        $sql = "SELECT 
                    mid.department_id,
                    mid.department_name,
                    mid.department_description,
                    mid.department_institution_code,
                    mid.department_record_status
                FROM master_institution_department mid
                WHERE mid.department_record_status IN ('A', 'D')";

        $countSql = "SELECT COUNT(*) as total
                     FROM master_institution_department mid
                     WHERE mid.department_record_status IN ('A', 'D')";

        if ($searchValue) {
            $searchCond = " AND (
                mid.department_name LIKE '%{$searchValue}%'
                OR mid.department_description LIKE '%{$searchValue}%'
                OR mid.department_institution_code LIKE '%{$searchValue}%'
            )";
            $sql .= $searchCond;
            $countSql .= $searchCond;
        }

        $totalResult = $db->query($countSql)->getRow()->total;

        $sql .= " ORDER BY mid.department_record_status ASC, {$orderColumn} {$orderDir}";
        $sql .= " LIMIT {$length} OFFSET {$start}";

        $data = $db->query($sql)->getResult();

        return [
            'draw'            => (int) ($post['draw'] ?? 1),
            'recordsTotal'    => (int) $totalResult,
            'recordsFiltered' => (int) $totalResult,
            'data'            => $data,
        ];
    }

    public function getDepartmentById(int $id)
    {
        $db = db_connect();
        return $db->table('master_institution_department')
            ->where('department_id', $id)
            ->whereIn('department_record_status', ['A', 'D'])
            ->get()
            ->getRow();
    }

    public function getIndicatorAccess(int $departmentId): array
    {
        $db = db_connect();
        $result = [];
        $deptStr = (string) $departmentId;

        foreach ($this->indicatorTypes as $type => $label) {
            $table = $this->indicatorTables[$type];
            $count = $db->table($table)
                ->where('group_department_id', $deptStr)
                ->where('group_type', $type)
                ->where('group_record_status', 'A')
                ->countAllResults();
            $result[$type] = $count > 0;
        }

        return $result;
    }

    public function getIndicatorCount(int $departmentId, int $type): int
    {
        $db = db_connect();
        $deptStr = (string) $departmentId;
        $table = $this->indicatorTables[$type] ?? 'quality_indicator_group';
        return (int) $db->table($table)
            ->where('group_department_id', $deptStr)
            ->where('group_type', $type)
            ->where('group_record_status', 'A')
            ->countAllResults();
    }

    public function getAllDepartments()
    {
        $db = db_connect();
        return $db->table('master_institution_department')
            ->where('department_record_status', 'A')
            ->orderBy('department_name', 'ASC')
            ->get()
            ->getResult();
    }

    public function createDepartment(array $data): int
    {
        $db = db_connect();
        if ($db->table('master_institution_department')->insert($data)) {
            return $db->insertID();
        }
        return 0;
    }

    public function updateDepartment(int $id, array $data): bool
    {
        $db = db_connect();
        return $db->table('master_institution_department')
            ->where('department_id', $id)
            ->update($data);
    }

    public function toggleRecordStatus(int $id, string $status): bool
    {
        $db = db_connect();
        return $db->table('master_institution_department')
            ->where('department_id', $id)
            ->update([
                'department_record_status' => $status,
            ]);
    }

    public function softDelete(int $id): bool
    {
        return $this->toggleRecordStatus($id, 'D');
    }

    public function getTotalDepartments(): int
    {
        $db = db_connect();
        return (int) $db->table('master_institution_department')
            ->where('department_record_status', 'A')
            ->countAllResults();
    }

    public function nameExists(string $name, int $excludeId = 0): bool
    {
        $db = db_connect();
        $builder = $db->table('master_institution_department')
            ->where('department_name', $name)
            ->where('department_record_status', 'A');
        if ($excludeId > 0) {
            $builder->where('department_id !=', $excludeId);
        }
        return $builder->countAllResults() > 0;
    }
}
