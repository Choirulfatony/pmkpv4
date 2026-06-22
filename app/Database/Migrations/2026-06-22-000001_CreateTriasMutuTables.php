<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTriasMutuTables extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => true,
            ],
            'unit_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
            ],
            'indicator_category_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
                'comment' => '4=INM, 5=IMPRS, 6=IMPUNIT',
            ],
            'indicator_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
            ],
            'triwulan' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'null' => false,
                'comment' => '1-4',
            ],
            'tahun' => [
                'type' => 'YEAR',
                'constraint' => 4,
                'null' => false,
            ],
            'status' => [
                'type' => "ENUM('draft','final')",
                'default' => 'draft',
            ],
            'final_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'final_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'default' => null,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'default' => null,
            ],
            'created_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['unit_id', 'indicator_category_id', 'indicator_id', 'triwulan', 'tahun']);
        $this->forge->createTable('triasmutu_dokumen');

        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => true,
            ],
            'dokumen_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
            ],
            'permasalahan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'kategori' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => false,
                'comment' => 'Man, Method, Machine, Material, Environment, Measurement',
            ],
            'penyebab' => [
                'type' => 'TEXT',
                'null' => false,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'default' => null,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('dokumen_id');
        $this->forge->createTable('triasmutu_analisis');

        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => true,
            ],
            'dokumen_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
            ],
            'plan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'do' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'study' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'act' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'default' => null,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'default' => null,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('dokumen_id');
        $this->forge->createTable('triasmutu_pdsa');

        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => true,
            ],
            'dokumen_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
            ],
            'disusun_oleh' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'mengetahui' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'kepala_unit' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'default' => null,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'default' => null,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('dokumen_id');
        $this->forge->createTable('triasmutu_ttd');
    }

    public function down()
    {
        $this->forge->dropTable('triasmutu_ttd');
        $this->forge->dropTable('triasmutu_pdsa');
        $this->forge->dropTable('triasmutu_analisis');
        $this->forge->dropTable('triasmutu_dokumen');
    }
}
