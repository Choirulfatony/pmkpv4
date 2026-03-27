<?php

namespace App\Controllers;

use Config\Database;

class TestDb extends BaseController
{
    public function index()
    {
        $db = Database::connect();

        if ($db->connect()) {
            return "Database CONNECTED";
        } else {
            return "Database FAILED";
        }
    }
}
