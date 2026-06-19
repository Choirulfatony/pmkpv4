<?php
$db = new mysqli('localhost', 'root', '', 'sidokar_db');
if ($db->connect_error) { die("Connection failed: " . $db->connect_error); }

echo "=== DEPT dgn GRUP IMPRS (type=5) ===\n";
$r = $db->query("SELECT DISTINCT g.group_department_id, d.department_name, d.department_record_status
    FROM local_quality_indicator_group g
    LEFT JOIN master_institution_department d ON d.department_id = g.group_department_id
    WHERE g.group_type = 5 AND g.group_record_status = 'A'");
while ($row = $r->fetch_object()) {
    echo "dept_id={$row->group_department_id}, name={$row->department_name}, record_status={$row->department_record_status}\n";
}

echo "\n=== DEPT AKTIF ===\n";
$a = $db->query("SELECT department_id, department_name FROM master_institution_department WHERE department_record_status = 'A' ORDER BY department_name");
while ($row = $a->fetch_object()) {
    echo "id={$row->department_id}, name={$row->department_name}\n";
}

$db->close();
