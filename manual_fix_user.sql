-- ============================================================
-- MANUAL FIX: User Google OAuth yang Tidak Bisa Login
-- ============================================================

-- 1. Lihat data user saat ini
SELECT 
    profile_id,
    profile_email,
    profile_fullname,
    profile_insert_by,
    profile_is_verified,
    profile_verification_token,
    profile_verification_sent_at,
    profile_record_status
FROM user_profile
WHERE profile_email = 'choirulfatoni@gmail.com';

-- 2. FIX: Update semua data yang salah
UPDATE user_profile 
SET profile_insert_by = 'GOOGLE', 
    profile_is_verified = 1, 
    profile_verification_token = NULL, 
    profile_verification_sent_at = NULL 
WHERE profile_email = 'choirulfatoni@gmail.com';

-- 3. Verifikasi hasil update
SELECT 
    profile_id,
    profile_email,
    profile_fullname,
    profile_insert_by,
    profile_is_verified,
    profile_verification_token,
    profile_record_status
FROM user_profile
WHERE profile_email = 'choirulfatoni@gmail.com';

-- ============================================================
-- EXPECTED RESULT:
-- profile_insert_by = 'GOOGLE' (bukan 'GOOGL')
-- profile_is_verified = 1
-- profile_verification_token = NULL
-- ============================================================
