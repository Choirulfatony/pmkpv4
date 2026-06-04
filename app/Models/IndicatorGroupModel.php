<?php

namespace App\Models;

use CodeIgniter\Model;

class IndicatorGroupModel extends Model
{
    protected $table = 'quality_indicator_variable';
    protected $primaryKey = 'variable_id';
    protected $allowedFields = [
        'variable_uuid', 'variable_indicator_id', 'variable_name',
        'variable_type', 'variable_unit_name', 'variable_record_status'
    ];
    protected $useTimestamps = false;

    protected string $tablePrefix = '';

    public function __construct(string $tablePrefix = '')
    {
        parent::__construct();
        $this->tablePrefix = $tablePrefix;
        $this->table = $tablePrefix . 'quality_indicator_variable';
    }

    private function varTable(): string
    {
        return $this->tablePrefix . 'quality_indicator_variable';
    }

    private function indTable(): string
    {
        return $this->tablePrefix . 'quality_indicator';
    }

    public function getDatatable(array $post, int $indicatorId): array
    {
        $db = db_connect();
        $vt = $this->varTable();
        $it = $this->indTable();

        $builder = $db->table($vt);
        $builder->select("{$vt}.*, {$it}.indicator_element, {$it}.indicator_units");
        $builder->join($it, "{$vt}.variable_indicator_id = {$it}.indicator_id", 'left');

        $builder->where("{$vt}.variable_indicator_id", $indicatorId);
        $builder->whereIn("{$vt}.variable_record_status", ['A', 'D']);

        $searchValue = $post['search']['value'] ?? '';
        if (!empty($searchValue)) {
            $builder->groupStart();
            $builder->like('variable_type', $searchValue);
            $builder->orLike('variable_name', $searchValue);
            $builder->orLike('variable_unit_name', $searchValue);
            $builder->orLike("{$it}.indicator_element", $searchValue);
            $builder->groupEnd();
        }

        $totalFiltered = $builder->countAllResults(false);

        $orderColumn = $post['order'][0]['column'] ?? 0;
        $orderDir = $post['order'][0]['dir'] ?? 'ASC';
        $columns = ['variable_id', 'variable_type', 'variable_name', 'variable_unit_name'];
        $orderBy = $columns[$orderColumn] ?? 'variable_id';
        $builder->orderBy($orderBy, $orderDir);

        $start = (int) ($post['start'] ?? 0);
        $length = (int) ($post['length'] ?? 10);
        $builder->limit($length, $start);

        $data = $builder->get()->getResultArray();

        $totalAll = $db->table($vt)
            ->where('variable_indicator_id', $indicatorId)
            ->whereIn('variable_record_status', ['A', 'D'])
            ->countAllResults();

        return [
            'draw' => (int) ($post['draw'] ?? 1),
            'recordsTotal' => $totalAll,
            'recordsFiltered' => $totalFiltered,
            'data' => $data,
        ];
    }

    public function getDetail(int $id): ?array
    {
        $db = db_connect();
        $vt = $this->varTable();
        $it = $this->indTable();

        return $db->table($vt)
            ->select("{$vt}.*, {$it}.indicator_element, {$it}.indicator_units")
            ->join($it, "{$vt}.variable_indicator_id = {$it}.indicator_id", 'left')
            ->where("{$vt}.variable_id", $id)
            ->get()
            ->getRowArray();
    }

    public function insertGroup(array $data): int
    {
        $db = db_connect();
        $data['variable_record_status'] = 'A';
        $data['variable_uuid'] = uniqid('var_', true);
        $db->table($this->table)->insert($data);
        return $db->insertID();
    }

    public function updateGroup(int $id, array $data): bool
    {
        $db = db_connect();
        $data['variable_last_updated'] = date('Y-m-d H:i:s');
        $db->table($this->table)->where('variable_id', $id)->update($data);
        return $db->affectedRows() > 0;
    }

    public function softDelete(int $id): bool
    {
        $db = db_connect();
        $db->table($this->table)->where('variable_id', $id)
            ->update(['variable_record_status' => 'X']);
        return $db->affectedRows() > 0;
    }
}
