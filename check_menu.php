<?php
$mysqli = new mysqli('localhost', 'root', '', 'pmkpv4');
if ($mysqli->connect_error) {
    die('Connection failed: ' . $mysqli->connect_error);
}
$result = $mysqli->query('SELECT * FROM siimut_menus ORDER BY urutan');
echo "<pre>";
while ($row = $result->fetch_assoc()) {
    print_r($row);
    echo str_repeat('-', 50) . "\n";
}
echo "</pre>";
$mysqli->close();
?>