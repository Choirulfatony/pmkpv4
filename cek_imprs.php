<?php
$db = \Config\Database::connect();
$type = 5;
$sql = "SELECT g.group_indicator_id, i.indicator_record_status
FROM local_quality_indicator_group g
JOIN local_quality_indicator i ON i.indicator_id = g.group_indicator_id AND i.indicator_record_status = 'A'
WHERE g.group_type = ?
  AND g.group_record_status = 'A'
GROUP BY g.group_indicator_id
ORDER BY g.group_indicator_id";
$rows = $db->query($sql, [$type])->getResult();
echo "Unique IMPRS indicators (active only): " . count($rows) . "\n";
foreach ($rows as $r) {
    echo "ID:{$r->group_indicator_id}\n";
}
echo "\n--- Without indicator status filter ---\n";
$sql2 = "SELECT g.group_indicator_id, i.indicator_record_status
FROM local_quality_indicator_group g
JOIN local_quality_indicator i ON i.indicator_id = g.group_indicator_id
WHERE g.group_type = ?
  AND g.group_record_status = 'A'
GROUP BY g.group_indicator_id
ORDER BY g.group_indicator_id";
$rows2 = $db->query($sql2, [$type])->getResult();
echo "Unique IMPRS indicators (all): " . count($rows2) . "\n";
foreach ($rows2 as $r) {
    echo "ID:{$r->group_indicator_id} status:[{$r->indicator_record_status}]\n";
}
echo "DONE\n";
