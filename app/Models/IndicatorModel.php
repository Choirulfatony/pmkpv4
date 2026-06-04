<?php

namespace App\Models;

use CodeIgniter\Model;

class IndicatorModel extends Model
{
    protected $table = 'quality_indicator';
    protected $primaryKey = 'indicator_id';
    protected $allowedFields = [
        'indicator_uuid', 'indicator_name_id', 'indicator_definition',
        'indicator_criteria_inclusive', 'indicator_criteria_exclusive',
        'indicator_element', 'indicator_source_of_data', 'indicator_type',
        'indicator_value_standard', 'indicator_monitoring_area',
        'indicator_frequency', 'indicator_target', 'indicator_category_id',
        'indicator_iscomplete', 'indicator_factors', 'indicator_target_calculation',
        'indicator_units', 'indicator_target_unit', 'indicator_lcl', 'indicator_ucl',
        'indicator_valid_date', 'indicator_last_updated', 'indicator_order_number',
        'indicator_record_status'
    ];
    protected $useTimestamps = false;

    protected string $tablePrefix = '';
    protected string $categoryId = '4';

    public function __construct(string $tablePrefix = '', string $categoryId = '4')
    {
        parent::__construct();
        $this->tablePrefix = $tablePrefix;
        $this->categoryId = $categoryId;
        $this->table = $tablePrefix . 'quality_indicator';
    }

    public function getTableName(): string
    {
        return $this->table;
    }

    public function getCategoryId(): string
    {
        return $this->categoryId;
    }

    public function getDatatable(array $post): array
    {
        $db = db_connect();

        $builder = $db->table($this->table . ' qi');

        $builder->select('
            qi.indicator_id,
            qi.indicator_element,
            qi.indicator_target,
            qi.indicator_target_calculation,
            qi.indicator_factors,
            qi.indicator_units,
            qi.indicator_target_unit,
            qi.indicator_frequency,
            qi.indicator_record_status,
            qi.indicator_order_number,
            qi.indicator_name_id,
            qi.indicator_type,
            qi.indicator_monitoring_area,
            qi.indicator_source_of_data,
            qi.indicator_definition,
            qi.indicator_criteria_inclusive,
            qi.indicator_criteria_exclusive,
            qi.indicator_value_standard,
            qi.indicator_lcl,
            qi.indicator_ucl,
            qi.indicator_category_id,
            qi.indicator_valid_date,
            qi.indicator_last_updated
        ');

        $builder->where('qi.indicator_category_id', $this->categoryId);

        if (isset($post['status']) && $post['status'] !== '') {
            $builder->where('qi.indicator_record_status', $post['status']);
        } else {
            $builder->whereIn('qi.indicator_record_status', ['A', 'D']);
        }

        $searchValue = $post['search']['value'] ?? '';
        $cariInput = $post['cari_input'] ?? '';
        $searchTerm = !empty($cariInput) ? $cariInput : $searchValue;
        if (!empty($searchTerm)) {
            $builder->groupStart();
            $builder->like('qi.indicator_element', $searchTerm);
            $builder->orLike('qi.indicator_name_id', $searchTerm);
            $builder->orLike('qi.indicator_type', $searchTerm);
            $builder->groupEnd();
        }

        $totalFiltered = $builder->countAllResults(false);

        $orderColumn = $post['order'][0]['column'] ?? 0;
        $orderDir = $post['order'][0]['dir'] ?? 'ASC';
        $columns = ['indicator_id', 'indicator_element', 'indicator_target', 'indicator_frequency', 'indicator_record_status', 'indicator_order_number'];
        $orderBy = $columns[$orderColumn] ?? 'indicator_id';
        $builder->orderBy($orderBy, $orderDir);

        $start = (int) ($post['start'] ?? 0);
        $length = (int) ($post['length'] ?? 10);
        $builder->limit($length, $start);

        $data = $builder->get()->getResultArray();

        $totalAll = $db->table($this->table)
            ->where('indicator_category_id', $this->categoryId)
            ->whereIn('indicator_record_status', ['A', 'D'])
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
        $row = $db->table($this->table)
            ->where('indicator_id', $id)
            ->where('indicator_category_id', $this->categoryId)
            ->get()
            ->getRowArray();
        return $row ?: null;
    }

    public function insertIndicator(array $data): int
    {
        $db = db_connect();

        $data['indicator_category_id'] = $this->categoryId;
        $data['indicator_record_status'] = 'A';
        $data['indicator_last_updated'] = date('Y-m-d H:i:s');

        if (empty($data['indicator_uuid'])) {
            $data['indicator_uuid'] = uniqid('ind_', true);
        }

        $db->table($this->table)->insert($data);
        return $db->insertID();
    }

    public function updateIndicator(int $id, array $data): bool
    {
        $db = db_connect();

        $data['indicator_last_updated'] = date('Y-m-d H:i:s');

        $db->table($this->table)
            ->where('indicator_id', $id)
            ->where('indicator_category_id', $this->categoryId)
            ->update($data);

        return $db->affectedRows() > 0;
    }

    public function softDelete(int $id): bool
    {
        $db = db_connect();

        $db->table($this->table)
            ->where('indicator_id', $id)
            ->where('indicator_category_id', $this->categoryId)
            ->update(['indicator_record_status' => 'X', 'indicator_last_updated' => date('Y-m-d H:i:s')]);

        return $db->affectedRows() > 0;
    }

    public function restore(int $id): bool
    {
        $db = db_connect();

        $db->table($this->table)
            ->where('indicator_id', $id)
            ->where('indicator_category_id', $this->categoryId)
            ->update(['indicator_record_status' => 'A', 'indicator_last_updated' => date('Y-m-d H:i:s')]);

        return $db->affectedRows() > 0;
    }
}
