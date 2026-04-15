<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AuthGoogleOAuthTestMigration extends Migration
{
    public function up(): void
    {
        // Create user_profile table if it doesn't exist
        $this->forge->addField([
            'profile_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'profile_fullname' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'profile_email' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'unique' => true,
            ],
            'profile_password' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'profile_record_status' => [
                'type' => 'VARCHAR',
                'constraint' => 1,
                'default' => 'A',
            ],
            'profile_insert_by' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'profile_insert_date' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'profile_is_verified' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'profile_verification_token' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'profile_verification_sent_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'profile_online_status' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'profile_disable' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'profile_department_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'profile_group_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'profile_photo' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'profile_gender' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
                'null' => true,
            ],
            'profile_birth_place' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'profile_dob' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'profile_handphone1' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
            ],
            'profile_handphone2' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
            ],
            'profile_employee_id' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
        ]);
        
        $this->forge->addKey('profile_id', true);
        $this->forge->createTable('user_profile', true);

        // Create user_group table
        $this->forge->addField([
            'group_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'group_name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'group_record_status' => [
                'type' => 'VARCHAR',
                'constraint' => 1,
                'default' => 'A',
            ],
        ]);
        
        $this->forge->addKey('group_id', true);
        $this->forge->createTable('user_group', true);

        // Insert default groups
        $this->db->table('user_group')->insertBatch([
            ['group_id' => 1, 'group_name' => 'Kendali Mutu dan Tim Pokja', 'group_record_status' => 'A'],
            ['group_id' => 2, 'group_name' => 'Komite', 'group_record_status' => 'A'],
            ['group_id' => 3, 'group_name' => 'Administrator', 'group_record_status' => 'A'],
        ]);

        // Create master_institution_department table
        $this->forge->addField([
            'department_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'department_name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'department_record_status' => [
                'type' => 'VARCHAR',
                'constraint' => 1,
                'default' => 'A',
            ],
        ]);
        
        $this->forge->addKey('department_id', true);
        $this->forge->createTable('master_institution_department', true);

        // Insert default department
        $this->db->table('master_institution_department')->insert([
            'department_name' => 'Test Department',
            'department_record_status' => 'A',
        ]);
    }

    public function down(): void
    {
        $this->forge->dropTable('user_profile', true);
        $this->forge->dropTable('user_group', true);
        $this->forge->dropTable('master_institution_department', true);
    }
}
