<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class IndicatorValidationModel extends Model
{
    protected $table = 'indicator_validation';
    protected $primaryKey = 'validation_id';
    protected $allowedFields = [
        'validation_indicator_id', 'validation_department_id', 'validation_period',
        'validation_validator_id', 'validation_date',
        'validation_data_lengkap', 'validation_sumber_data_sesuai',
        'validation_numerator_sesuai', 'validation_denominator_sesuai',
        'validation_perhitungan_benar',
        'validation_sample_count', 'validation_appropriate_count',
        'validation_score', 'validation_result', 'validation_note',
        'validation_category_id'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getPendingIndicators(string $categoryId, int $tahun, int $bulan): array
    {
        $db = db_connect();
        $prefixes = ['4' => '', '5' => 'local_', '6' => 'local_'];
        $prefix = $prefixes[$categoryId] ?? '';

        return $db->query("
            SELECT
                qir.result_indicator_id,
                qi.indicator_element,
                qir.result_department_id,
                mid.department_name,
                COUNT(DISTINCT qir.result_id) as total_records,
                MIN(qir.result_period) as period_start,
                MAX(qir.result_period) as period_end,
                iv.validation_id,
                iv.validation_result,
                iv.validation_score,
                iv.validation_date,
                iv.validation_note
            FROM {$prefix}quality_indicator_result qir
            JOIN {$prefix}quality_indicator qi ON qi.indicator_id = qir.result_indicator_id
            JOIN master_institution_department mid ON mid.department_id = qir.result_department_id
            LEFT JOIN indicator_validation iv ON iv.validation_indicator_id = qir.result_indicator_id
                AND iv.validation_department_id = qir.result_department_id
                AND YEAR(iv.validation_period) = ?
                AND MONTH(iv.validation_period) = ?
                AND iv.validation_category_id = ?
            WHERE qir.result_record_status = 'D'
                AND YEAR(qir.result_period) = ?
                AND MONTH(qir.result_period) = ?
            GROUP BY qir.result_indicator_id, qi.indicator_element, qir.result_department_id, mid.department_name,
                iv.validation_id, iv.validation_result, iv.validation_score, iv.validation_date, iv.validation_note
            ORDER BY qi.indicator_element, mid.department_name
        ", [$tahun, $bulan, $categoryId, $tahun, $bulan])->getResult();
    }

    public function getRecordsForValidation(string $prefix, int $indicatorId, string $departmentId, int $tahun, int $bulan): array
    {
        $db = db_connect();
        $bulanStr = str_pad((string) $bulan, 2, '0', STR_PAD_LEFT);

        return $db->query("
            SELECT result_id, result_period, result_numerator_value, result_denumerator_value,
                   result_record_status, result_insert_by, result_insert_date,
                   (result_numerator_value / NULLIF(result_denumerator_value, 0) * 100) as result_percentage
            FROM {$prefix}quality_indicator_result
            WHERE result_indicator_id = ?
              AND result_department_id = ?
              AND YEAR(result_period) = ?
              AND MONTH(result_period) = ?
              AND result_record_status = 'A'
            ORDER BY result_period ASC
        ", [$indicatorId, $departmentId, $tahun, $bulanStr])->getResult();
    }

    public function getExistingValidation(int $indicatorId, string $departmentId, string $period, string $categoryId)
    {
        return $this->where('validation_indicator_id', $indicatorId)
            ->where('validation_department_id', $departmentId)
            ->where('validation_period', $period)
            ->where('validation_category_id', $categoryId)
            ->orderBy('validation_id', 'DESC')
            ->first();
    }

    public function saveValidation(array $data): int
    {
        $score = 0;
        $criteriaCount = 5;
        $checks = ['validation_data_lengkap', 'validation_sumber_data_sesuai',
                    'validation_numerator_sesuai', 'validation_denominator_sesuai',
                    'validation_perhitungan_benar'];

        foreach ($checks as $c) {
            if (!empty($data[$c]) && strtolower($data[$c]) === 'ya') {
                $score++;
            }
        }

        $data['validation_score'] = ($score / $criteriaCount) * 100;
        $data['validation_appropriate_count'] = $score;

        if ($data['validation_score'] >= 90) {
            $data['validation_result'] = 'valid';
        } elseif ($data['validation_score'] >= 80) {
            $data['validation_result'] = 'invalid';
        } else {
            $data['validation_result'] = 'invalid';
        }

        $data['validation_date'] = date('Y-m-d H:i:s');

        return $this->insert($data);
    }

    public function approveMonthRecords(string $prefix, int $indicatorId, string $departmentId, int $tahun, int $bulan, int $userId): int
    {
        $db = db_connect();
        $bulanStr = str_pad((string) $bulan, 2, '0', STR_PAD_LEFT);
        $now = date('Y-m-d H:i:s');

        $db->query("
            UPDATE {$prefix}quality_indicator_result
            SET result_record_status = 'A',
                result_update_by = ?,
                result_update_date = ?
            WHERE result_indicator_id = ?
              AND result_department_id = ?
              AND YEAR(result_period) = ?
              AND MONTH(result_period) = ?
              AND result_record_status = 'D'
        ", [$userId, $now, $indicatorId, $departmentId, $tahun, $bulanStr]);

        return $db->affectedRows();
    }
}
