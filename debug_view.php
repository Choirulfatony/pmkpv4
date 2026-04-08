<?php
// This is just to check what would be output in the view
require 'vendor/autoload.php';

use App\Models\RekapLaporanInmModel;

$model = new RekapLaporanInmModel();
$indicators = $model->getIndicatorInm([]);

echo "<select class='form-select' id='indicator_id' onchange='loadGrafik()'>";
echo "<option value=''>-- Pilih Indikator --</option>";

if (count($indicators) > 0) {
    foreach ($indicators as $ind) {
        $selected = ($ind->indicator_id == 1) ? 'selected' : ''; // Just for testing
        echo "<option value='{$ind->indicator_id}' {$selected}>" . esc($ind->indicator_element) . "</option>";
    }
} else {
    echo "<option value='' disabled>Tidak ada indikator tersedia</option>";
}
echo "</select>";

echo "<p>Jumlah indikator: " . count($indicators) . "</p>";
?>