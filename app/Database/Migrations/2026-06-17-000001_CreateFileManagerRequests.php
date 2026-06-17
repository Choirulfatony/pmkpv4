<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateFileManagerRequests extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => true,
            ],
            'fmr_file_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
            ],
            'fmr_action' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'default' => 'delete',
            ],
            'fmr_reason' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'fmr_status' => [
                'type' => "ENUM('pending','approved','rejected')",
                'default' => 'pending',
            ],
            'fmr_request_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
            ],
            'fmr_request_date' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'fmr_approve_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'fmr_approve_date' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'fmr_notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('file_manager_requests');
    }

    public function down()
    {
        $this->forge->dropTable('file_manager_requests');
    }
}
