<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRencanaPerbaikanToAnalisis extends Migration
{
    public function up()
    {
        $this->forge->addColumn('triasmutu_analisis', [
            'rencana_perbaikan' => [
                'type'       => 'TEXT',
                'null'       => true,
                'after'      => 'penyebab',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('triasmutu_analisis', 'rencana_perbaikan');
    }
}
