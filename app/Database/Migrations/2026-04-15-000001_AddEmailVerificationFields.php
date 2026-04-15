<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddEmailVerificationFields extends Migration
{
    public function up(): void
    {
        $this->forge = \Config\Database::forge();
        
        // Cek apakah kolom profile_is_verified sudah ada
        $fields = $this->db->getFieldData('user_profile');
        $hasProfileIsVerified = false;
        $hasProfileVerificationToken = false;
        $hasProfileVerificationSentAt = false;
        
        foreach ($fields as $field) {
            if ($field->name === 'profile_is_verified') {
                $hasProfileIsVerified = true;
            }
            if ($field->name === 'profile_verification_token') {
                $hasProfileVerificationToken = true;
            }
            if ($field->name === 'profile_verification_sent_at') {
                $hasProfileVerificationSentAt = true;
            }
        }
        
        // Tambahkan kolom jika belum ada
        if (!$hasProfileIsVerified) {
            $this->forge->addColumn('user_profile', [
                'profile_is_verified' => [
                    'type' => 'TINYINT',
                    'constraint' => 1,
                    'default' => 0,
                    'after' => 'profile_insert_date',
                ],
            ]);
            echo "Added column: profile_is_verified\n";
        }
        
        if (!$hasProfileVerificationToken) {
            $this->forge->addColumn('user_profile', [
                'profile_verification_token' => [
                    'type' => 'VARCHAR',
                    'constraint' => 255,
                    'null' => true,
                    'after' => 'profile_is_verified',
                ],
            ]);
            echo "Added column: profile_verification_token\n";
        }
        
        if (!$hasProfileVerificationSentAt) {
            $this->forge->addColumn('user_profile', [
                'profile_verification_sent_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                    'after' => 'profile_verification_token',
                ],
            ]);
            echo "Added column: profile_verification_sent_at\n";
        }
        
        if ($hasProfileIsVerified && $hasProfileVerificationToken && $hasProfileVerificationSentAt) {
            echo "All email verification columns already exist.\n";
        } else {
            echo "\nEmail verification fields added successfully!\n";
        }
    }

    public function down(): void
    {
        $this->forge = \Config\Database::forge();
        
        $fields = $this->db->getFieldData('user_profile');
        
        foreach ($fields as $field) {
            if ($field->name === 'profile_is_verified') {
                $this->forge->dropColumn('user_profile', 'profile_is_verified');
                echo "Dropped column: profile_is_verified\n";
            }
            if ($field->name === 'profile_verification_token') {
                $this->forge->dropColumn('user_profile', 'profile_verification_token');
                echo "Dropped column: profile_verification_token\n";
            }
            if ($field->name === 'profile_verification_sent_at') {
                $this->forge->dropColumn('user_profile', 'profile_verification_sent_at');
                echo "Dropped column: profile_verification_sent_at\n";
            }
        }
    }
}
