<?php
require 'app/Config/Paths.php';
$paths = new \Config\Paths();
require rtrim($paths->systemDirectory, '/ ') . '/bootstrap.php';

$db = \Config\Database::connect();
$periodStart = date('Y-m-01');

echo "=== CEK DASHBOARD: Unit Belum Menginput ===\n";
echo "Periode: $periodStart\n\n";

// 1. Jumlah departemen dengan grup aktif
$deptSql = "
    SELECT COUNT(*) AS cnt FROM (
        SELECT DISTINCT mid.department_id
        FROM master_institution_department mid
        WHERE mid.department_record_status = 'A'
        AND EXISTS (
            SELECT 1 FROM quality_indicator_group qig
            WHERE qig.group_department_id = mid.department_id AND qig.group_record_status = 'A'
            UNION
            SELECT 1 FROM local_quality_indicator_group lig
            WHERE lig.group_department_id = mid.department_id AND lig.group_record_status = 'A'
        )
    ) depts
";
$total = $db->query($deptSql)->getRow()->cnt;
echo "(1) Departemen dengan grup aktif (INM/IMPRS/IMPUNIT/IKP): $total\n\n";

// 2. Per tipe
$types = [1 => 'INM', 5 => 'IMPRS', 6 => 'IMPUNIT', 7 => 'IKP'];
echo "(2) Breakdown per tipe:\n";
foreach ($types as $type => $label) {
    $groupTable = $type === 1 ? 'quality_indicator_group' : 'local_quality_indicator_group';
    $resultTable = $type === 1 ? 'quality_indicator_result' : 'local_quality_indicator_result';

    $g = $db->query(
        "SELECT COUNT(DISTINCT group_department_id) AS cnt FROM {$groupTable} WHERE group_type = ? AND group_record_status = 'A'",
        [$type]
    )->getRow()->cnt;

    $i = $db->query(
        "SELECT COUNT(DISTINCT r.result_department_id) AS cnt
         FROM {$resultTable} r
         JOIN {$groupTable} g ON g.group_indicator_id = r.result_indicator_id
             AND g.group_department_id = r.result_department_id
             AND g.group_type = ?
             AND g.group_record_status = 'A'
         WHERE r.result_period = ? AND r.result_record_status IN ('D','A')",
        [$type, $periodStart]
    )->getRow()->cnt;

    echo "   $label: punya grup = $g, sudah input = $i, belum = " . ($g - $i) . "\n";
}

echo "\n(3) Hitung persis yang tampil di Unit Belum Menginput:\n";
$deptListSql = "
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
$departments = $db->query($deptListSql)->getResult();
$showCount = 0;
$detailRows = [];
foreach ($departments as $dept) {
    $missingTypes = [];
    foreach ([1, 5, 6, 7] as $type) {
        $groupTable = $type === 1 ? 'quality_indicator_group' : 'local_quality_indicator_group';
        $resultTable = $type === 1 ? 'quality_indicator_result' : 'local_quality_indicator_result';
        
        $hasGroup = $db->table($groupTable)
            ->where('group_department_id', $dept->department_id)
            ->where('group_type', $type)
            ->where('group_record_status', 'A')
            ->countAllResults();
        
        if ($hasGroup > 0) {
            $hasInput = $db->table("{$resultTable} r")
                ->join("{$groupTable} g", "g.group_indicator_id = r.result_indicator_id AND g.group_department_id = r.result_department_id")
                ->where('g.group_department_id', $dept->department_id)
                ->where('g.group_type', $type)
                ->where('g.group_record_status', 'A')
                ->where('r.result_period', $periodStart)
                ->whereIn('r.result_record_status', ['D', 'A'])
                ->countAllResults();
            
            if ($hasInput == 0) {
                $missingTypes[] = $types[$type];
            }
        }
    }
    if (count($missingTypes) > 0) {
        $showCount++;
        $detailRows[] = $dept->department_name . ': ' . implode(', ', $missingTypes);
    }
}
echo "   Total tampil: $showCount\n";
echo "   Detail:\n";
foreach ($detailRows as $r) {
    echo "     - $r\n";
}
