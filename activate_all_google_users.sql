-- ============================================================
-- SQL Script: Verifikasi Semua User Google OAuth yang Sudah Ada
-- ============================================================
-- 
-- Tujuan: Mengaktifkan semua user yang terdaftar via Google OAuth
--         yang belum terverifikasi agar bisa login
--
-- Jalankan script ini sekali saja di database Anda
-- ============================================================

-- 1. Lihat user Google yang belum terverifikasi
SELECT 
    profile_id,
    profile_email,
    profile_fullname,
    profile_insert_by,
    profile_is_verified,
    profile_verification_token,
    profile_insert_date
FROM user_profile
WHERE profile_insert_by = 'GOOGLE'
  AND (profile_is_verified = 0 OR profile_is_verified IS NULL)
ORDER BY profile_insert_date DESC;

-- 2. UPDATE: Verifikasi semua user Google yang belum terverifikasi
UPDATE user_profile
SET 
    profile_is_verified = 1,
    profile_verification_token = NULL,
    profile_verification_sent_at = NULL
WHERE profile_insert_by = 'GOOGLE'
  AND (profile_is_verified = 0 OR profile_is_verified IS NULL);

-- 3. Verifikasi: Cek hasil update
SELECT 
    COUNT(*) as total_google_users,
    SUM(CASE WHEN profile_is_verified = 1 THEN 1 ELSE 0 END) as verified_count,
    SUM(CASE WHEN profile_is_verified = 0 THEN 1 ELSE 0 END) as unverified_count
FROM user_profile
WHERE profile_insert_by = 'GOOGLE';

-- ============================================================
-- OPSI TAMBAHAN (Jalankan jika diperlukan):
-- ============================================================

-- OPSI A: Verifikasi SEMUA user yang punya email @gmail.com
-- (Hati-hati: ini akan verifikasi semua user Gmail)
/*
UPDATE user_profile
SET 
    profile_is_verified = 1,
    profile_verification_token = NULL
WHERE profile_email LIKE '%@gmail.com'
  AND (profile_is_verified = 0 OR profile_is_verified IS NULL);
*/

-- OPSI B: Verifikasi SEMUA user (tidak disarankan untuk production)
/*
UPDATE user_profile
SET 
    profile_is_verified = 1,
    profile_verification_token = NULL
WHERE (profile_is_verified = 0 OR profile_is_verified IS NULL);
*/

-- OPSI C: Verifikasi user tertentu berdasarkan email
/*
UPDATE user_profile
SET 
    profile_is_verified = 1,
    profile_verification_token = NULL
WHERE profile_email = 'your.email@gmail.com';
*/

-- ============================================================
-- SELESAI
-- ============================================================
