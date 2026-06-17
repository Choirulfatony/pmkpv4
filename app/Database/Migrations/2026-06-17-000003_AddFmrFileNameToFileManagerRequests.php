<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFmrFileNameToFileManagerRequests extends Migration
{
    public function up()
    {
        $this->forge->addColumn('file_manager_requests', [
            'fmr_file_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'fmr_file_id',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('file_manager_requests', 'fmr_file_name');
    }
}
