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

    public function getDatatable(array $post, int $indicatorId): array
    {
        $db = db_connect();
        $builder = $db->table($this->table);
        $builder->select('
            quality_indicator_variable.*,
            quality_indicator.indicator_element,
            quality_indicator.indicator_units
        ');
        $builder->join('quality_indicator', 'quality_indicator_variable.variable_indicator_id = quality_indicator.indicator_id', 'left');

        $builder->where('quality_indicator_variable.variable_indicator_id', $indicatorId);
        $builder->whereIn('quality_indicator_variable.variable_record_status', ['A', 'D']);

        $searchValue = $post['search']['value'] ?? '';
        if (!empty($searchValue)) {
            $builder->groupStart();
            $builder->like('variable_type', $searchValue);
            $builder->orLike('variable_name', $searchValue);
            $builder->orLike('variable_unit_name', $searchValue);
            $builder->orLike('quality_indicator.indicator_element', $searchValue);
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

        $totalAll = $db->table($this->table)
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
        return $db->table($this->table)
            ->select('
                quality_indicator_variable.*,
                quality_indicator.indicator_element,
                quality_indicator.indicator_units
            ')
            ->join('quality_indicator', 'quality_indicator_variable.variable_indicator_id = quality_indicator.indicator_id', 'left')
            ->where('quality_indicator_variable.variable_id', $id)
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
