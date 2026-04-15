<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRememberTokenToUserProfile extends Migration
{
    public function up()
    {
        $fields = [
            'profile_remember_token' => [
                'type'       => 'VARCHAR',
                'constraint' => 64,
                'null'       => true,
                'after'      => 'profile_online_status'
            ],
        ];
        $this->forge->addColumn('user_profile', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('user_profile', 'profile_remember_token');
    }
}
