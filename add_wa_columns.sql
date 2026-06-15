-- Add WhatsApp tracking columns to ikprssm_notifikasi table
ALTER TABLE `ikprssm_notifikasi` 
ADD COLUMN `wa_status` VARCHAR(20) NULL AFTER `type`,
ADD COLUMN `wa_message_id` VARCHAR(100) NULL AFTER `wa_status`,
ADD COLUMN `wa_error` TEXT NULL AFTER `wa_message_id`,
ADD COLUMN `retry_count` INT(3) NOT NULL DEFAULT 0 AFTER `wa_error`;

-- Add index for status
ALTER TABLE `ikprssm_notifikasi` 
ADD INDEX `idx_wa_status` (`wa_status`);

-- Update existing records
UPDATE `ikprssm_notifikasi` SET `wa_status` = 'SENT' WHERE `wa_status` IS NULL;
