<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddWaStatusToNotifikasi extends Migration
{
    public function up()
    {
        // Add columns for WhatsApp message tracking
        $fields = [
            'wa_status' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
                'default' => null,
                'comment' => 'Status WA: SENT, PENDING, FAILED, NO_PHONE'
            ],
            'wa_message_id' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'default' => null,
                'comment' => 'WhatsApp Message ID for tracking'
            ],
            'wa_error' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Error message if WA sending failed'
            ],
            'retry_count' => [
                'type' => 'INT',
                'constraint' => 3,
                'null' => false,
                'default' => 0,
                'comment' => 'Retry count for failed messages'
            ]
        ];

        $this->forge->addColumn('ikprssm_notifikasi', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('ikprssm_notifikasi', ['wa_status', 'wa_message_id', 'wa_error', 'retry_count']);
    }
}
