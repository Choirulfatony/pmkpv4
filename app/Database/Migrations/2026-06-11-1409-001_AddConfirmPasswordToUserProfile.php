<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddConfirmPasswordToUserProfile extends Migration
{
    public function up()
    {
        $fields = [
            'profile_confirm_password' => [
                'type'       => 'VARCHAR',
                'constraint' => 32,
                'null'       => true,
                'after'      => 'profile_password',
            ],
        ];
        $this->forge->addColumn('user_profile', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('user_profile', 'profile_confirm_password');
    }
}
