<?php
$f = 'C:/xampp/htdocs/pmkpv4/app/Controllers/RekapPeriodeInm.php';
$c = file_get_contents($f);
$c = str_replace('\/PhpOffice/PhpSpreadsheet/Style/Alignment', '\/phpOffice/PhpSpreadsheet/Style/Alignment', $c);
$c = str_replace('\/PhpOffice\/PhpSpreadsheet\/Style\/Alignment', '\\PhpOffice\\PhpSpreadsheet\\Style\\Alignment', $c);
file_put_contents($f, $c);
echo 'Done';