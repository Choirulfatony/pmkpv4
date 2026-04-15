-- ============================================================
-- SQL Script: Fix User Google OAuth dengan profile_insert_by Salah
-- ============================================================
-- 
-- Masalah: User Google memiliki profile_insert_by = 'GOOGL' 
--          (seharusnya 'GOOGLE') dan token masih ada
--
-- Jalankan script ini untuk memperbaiki data user
-- ============================================================

-- 1. Lihat user yang memiliki profile_insert_by tidak standar
SELECT 
    profile_id,
    profile_email,
    profile_fullname,
    profile_insert_by,
    profile_is_verified,
    profile_verification_token
FROM user_profile
WHERE profile_insert_by IN ('GOOGL', 'GOOG', 'G', 'Google', 'google')
   OR profile_insert_by LIKE '%oogle%'
ORDER BY profile_insert_date DESC;

-- 2. FIX: Update profile_insert_by dari 'GOOGL' ke 'GOOGLE'
UPDATE user_profile
SET profile_insert_by = 'GOOGLE'
WHERE profile_insert_by = 'GOOGL';

-- 3. FIX: Hapus verification_token untuk user yang sudah verified
UPDATE user_profile
SET 
    profile_verification_token = NULL,
    profile_verification_sent_at = NULL
WHERE profile_insert_by = 'GOOGLE'
  AND profile_is_verified = 1
  AND profile_verification_token IS NOT NULL;

-- 4. Verifikasi: Cek hasil fix
SELECT 
    profile_id,
    profile_email,
    profile_fullname,
    profile_insert_by,
    profile_is_verified,
    profile_verification_token
FROM user_profile
WHERE profile_email = 'choirulfatoni@gmail.com';

-- ============================================================
-- SELESAI
-- ============================================================
