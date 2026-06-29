-- ============================================
-- 1. Tambah grup Validasi (group_id = 16)
-- ============================================
INSERT IGNORE INTO user_group (group_id, group_name, group_health_care, group_application_type, group_record_status)
VALUES (16, 'Validasi', '', 0, 'A');

-- ============================================
-- 2. Buat tabel pivot grup-departemen
-- ============================================
CREATE TABLE IF NOT EXISTS user_group_department (
    id INT AUTO_INCREMENT PRIMARY KEY,
    group_id INT(11) NOT NULL,
    department_id INT(11) NOT NULL,
    created_at DATETIME NULL,
    updated_at DATETIME NULL,
    UNIQUE KEY uq_group_department (group_id, department_id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
