<?php

namespace App\Models;

use CodeIgniter\Model;

class TriasMutuDokumenModel extends Model
{
    protected $table = 'triasmutu_dokumen';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'unit_id', 'indicator_category_id', 'indicator_id',
        'triwulan', 'tahun', 'status', 'final_by', 'final_at', 'created_by'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getOrCreateDokumen(array $data): int
    {
        $existing = $this->where([
            'unit_id'                => $data['unit_id'],
            'indicator_category_id'  => $data['indicator_category_id'],
            'indicator_id'           => $data['indicator_id'],
            'triwulan'               => $data['triwulan'],
            'tahun'                  => $data['tahun'],
        ])->first();

        if ($existing) {
            return (int) $existing['id'];
        }

        $this->save([
            'unit_id'               => $data['unit_id'],
            'indicator_category_id' => $data['indicator_category_id'],
            'indicator_id'          => $data['indicator_id'],
            'triwulan'              => $data['triwulan'],
            'tahun'                 => $data['tahun'],
            'status'                => 'draft',
            'created_by'            => session()->get('user_id') ?? 0,
        ]);

        return (int) $this->insertID();
    }

    public function getDokumenWithRelations(int $id): ?array
    {
        $db = db_connect();
        $dokumen = $this->find($id);
        if (!$dokumen) {
            return null;
        }

        $analisis = $db->table('triasmutu_analisis')
            ->where('dokumen_id', $id)
            ->orderBy('kategori', 'ASC')
            ->get()
            ->getResultArray();

        $pdsa = $db->table('triasmutu_pdsa')
            ->where('dokumen_id', $id)
            ->get()
            ->getRowArray();

        $ttd = $db->table('triasmutu_ttd')
            ->where('dokumen_id', $id)
            ->get()
            ->getRowArray();

        $indicator = $this->getIndicatorInfo(
            $dokumen['indicator_category_id'],
            $dokumen['indicator_id']
        );

        $unit = $db->table('master_institution_department')
            ->select('department_name')
            ->where('department_id', $dokumen['unit_id'])
            ->get()
            ->getRowArray();

        $finalBy = null;
        if ($dokumen['final_by']) {
            $finalBy = $db->table('users')
                ->select('nama')
                ->where('id_user', $dokumen['final_by'])
                ->get()
                ->getRowArray();
        }

        $dokumen['indicator'] = $indicator;
        $dokumen['unit_name'] = $unit['department_name'] ?? '-';
        $dokumen['final_by_name'] = $finalBy['nama'] ?? '-';
        $dokumen['analisis'] = $analisis;
        $dokumen['pdsa'] = $pdsa;
        $dokumen['ttd'] = $ttd;

        return $dokumen;
    }

    public function getIndicatorInfo(int $categoryId, int $indicatorId): ?object
    {
        $db = db_connect();
        $table = $categoryId == 4 ? 'quality_indicator' : 'local_quality_indicator';

        return $db->table($table)
            ->select("indicator_id, indicator_element, indicator_target, 
                      indicator_factors, indicator_target_calculation, 
                      indicator_units, indicator_target_unit")
            ->where('indicator_id', $indicatorId)
            ->get()
            ->getRow();
    }

    public function getMeasurementData(int $categoryId, int $indicatorId, int $tahun, int $triwulan, ?int $unitId = null): array
    {
        $db = db_connect();
        $tableResult = ($categoryId == 4 ? 'quality_indicator_result' : 'local_quality_indicator_result');
        $tableIndicator = ($categoryId == 4 ? 'quality_indicator' : 'local_quality_indicator');

        $bulanMulai = ($triwulan - 1) * 3 + 1;
        $bulanAkhir = $triwulan * 3;

        $rows = $db->table("$tableResult qir")
            ->select("
                MONTH(qir.result_period) AS bulan,
                SUM(qir.result_numerator_value) AS num,
                SUM(qir.result_denumerator_value) AS denum
            ")
            ->join("$tableIndicator qi", 'qi.indicator_id = qir.result_indicator_id', 'LEFT')
            ->where('qir.result_indicator_id', $indicatorId)
            ->where('qir.result_record_status', 'A')
            ->where('YEAR(qir.result_period)', $tahun)
            ->where('MONTH(qir.result_period) >=', $bulanMulai)
            ->where('MONTH(qir.result_period) <=', $bulanAkhir)
            ->groupBy('MONTH(qir.result_period)')
            ->get()
            ->getResult();

        $monthly = array_fill(1, 12, ['num' => 0, 'denum' => 0]);

        foreach ($rows as $r) {
            $monthly[(int) $r->bulan] = [
                'num'   => (float) $r->num,
                'denum' => (float) $r->denum,
            ];
        }

        $indicator = $this->getIndicatorInfo($categoryId, $indicatorId);
        $factors = (float) ($indicator->indicator_factors ?? 1);

        $result = [];
        $totalNum = 0;
        $totalDenum = 0;

        for ($b = $bulanMulai; $b <= $bulanAkhir; $b++) {
            $num = $monthly[$b]['num'];
            $denum = $monthly[$b]['denum'];
            $totalNum += $num;
            $totalDenum += $denum;

            $nilai = $denum > 0 ? round(($num / $denum) * $factors, 2) : null;

            $result['bulanan'][] = [
                'bulan' => $b,
                'num'   => $num,
                'denum' => $denum,
                'nilai' => $nilai,
            ];
        }

        $result['total_num'] = $totalNum;
        $result['total_denum'] = $totalDenum;
        $result['nilai_triwulan'] = $totalDenum > 0
            ? round(($totalNum / $totalDenum) * $factors, 2)
            : null;
        $result['target'] = (float) ($indicator->indicator_target ?? 0);
        $result['indicator'] = $indicator;

        return $result;
    }

    public function getDepartmentsByIndicator(int $categoryId, int $indicatorId, int $tahun): array
    {
        $db = db_connect();
        $tableResult = ($categoryId == 4 ? 'quality_indicator_result' : 'local_quality_indicator_result');
        $tableGroup = ($categoryId == 4 ? 'quality_indicator_group' : 'local_quality_indicator_group');

        return $db->table('master_institution_department mid')
            ->select('mid.department_id, mid.department_name')
            ->distinct()
            ->join($tableResult . ' qir', 'qir.result_department_id = mid.department_id', 'inner')
            ->join($tableGroup . ' qig', function ($join) use ($indicatorId) {
                $join->on('qig.group_department_id = mid.department_id')
                    ->on('qig.group_indicator_id', "$indicatorId", false);
            })
            ->where('YEAR(qir.result_period)', $tahun)
            ->where('qir.result_record_status', 'A')
            ->where('qir.result_indicator_id', $indicatorId)
            ->orderBy('mid.department_name', 'ASC')
            ->get()
            ->getResultArray();
    }
}
