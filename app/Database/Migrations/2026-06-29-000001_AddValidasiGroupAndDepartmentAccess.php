<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddValidasiGroupAndDepartmentAccess extends Migration
{
    public function up()
    {
        // Insert group_id = 16 (Validasi)
        $this->db->query("INSERT IGNORE INTO user_group (group_id, group_name, group_health_care, group_application_type, group_record_status)
                          VALUES (16, 'Validasi', '', 0, 'A')");

        // Create pivot table for group-department access
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => true,
            ],
            'group_id' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'department_id' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['group_id', 'department_id']);
        $this->forge->createTable('user_group_department', true);
    }

    public function down()
    {
        $this->forge->dropTable('user_group_department', true);
        $this->db->query("DELETE FROM user_group WHERE group_id = 16");
    }
}
