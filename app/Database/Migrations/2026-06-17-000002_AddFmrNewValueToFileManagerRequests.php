<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFmrNewValueToFileManagerRequests extends Migration
{
    public function up()
    {
        $this->forge->addColumn('file_manager_requests', [
            'fmr_new_value' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'fmr_reason',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('file_manager_requests', 'fmr_new_value');
    }
}
