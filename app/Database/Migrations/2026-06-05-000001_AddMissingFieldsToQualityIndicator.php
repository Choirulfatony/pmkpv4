<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddMissingFieldsToQualityIndicator extends Migration
{
    public function up()
    {
        $fields = [
            'indicator_dasar_pemikiran' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'indicator_dimensi_mutu' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'comment' => 'Comma-separated: 1=Safety,2=Effectiveness,3=Patient Centeredness,4=Timely,5=Efficiency,6=Equity,7=Terintegrasi',
            ],
            'indicator_tujuan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'indicator_periode_pengumpulan_data' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'indicator_instrumen_pengambilan_data' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'indicator_besar_sampel' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'indicator_cara_pengambilan_sampel' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'indicator_periode_analisis_pelaporan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'indicator_penyajian_data' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'indicator_penanggung_jawab' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
        ];

        $this->forge->addColumn('quality_indicator', $fields);
        $this->forge->addColumn('local_quality_indicator', $fields);
    }

    public function down()
    {
        $columns = [
            'indicator_dasar_pemikiran',
            'indicator_dimensi_mutu',
            'indicator_tujuan',
            'indicator_periode_pengumpulan_data',
            'indicator_instrumen_pengambilan_data',
            'indicator_besar_sampel',
            'indicator_cara_pengambilan_sampel',
            'indicator_periode_analisis_pelaporan',
            'indicator_penyajian_data',
            'indicator_penanggung_jawab',
        ];

        $this->forge->dropColumn('quality_indicator', $columns);
        $this->forge->dropColumn('local_quality_indicator', $columns);
    }
}
