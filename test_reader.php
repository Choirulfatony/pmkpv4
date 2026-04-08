<?php
require 'vendor/autoload.php';

use Tests\Support\Libraries\ConfigReader;

$reader = new ConfigReader();
var_dump($reader->baseURL);
?>