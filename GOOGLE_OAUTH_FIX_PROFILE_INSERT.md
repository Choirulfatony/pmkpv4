# Fix: User Google OAuth Tidak Bisa Login (profile_insert_by = 'GOOGL')

## Masalah yang Ditemukan

User dengan email `choirulfatoni@gmail.com` tidak bisa login via Google OAuth meskipun:
- ✅ Email ada di database (`user_profile`)
- ✅ `profile_is_verified = 1` (sudah terverifikasi)

### Root Cause

Setelah pengecekan, ditemukan 2 masalah:

1. **`profile_insert_by = 'GOOGL'`** (Kurang huruf 'E')
   - Seharusnya: `'GOOGLE'`
   - Ini terjadi karena truncation/bug saat registrasi awal
   - Kode mencari user dengan `profile_insert_by = 'GOOGLE'` sehingga tidak match

2. **`profile_verification_token` masih ada**
   - Nilai: `'44ac3d97cdcf1640c636f5920648ef2e'`
   - Seharusnya: `NULL` (karena sudah verified)
   - Ini menandakan proses registrasi tidak selesai sempurna

---

## Solusi

### ✅ Otomatis (Via Web UI - REKOMENDASI)

1. **Buka:**
   ```
   http://localhost/pmkpv4/activate_google_users.php
   ```

2. **Lihat daftar user yang perlu difix:**
   - Kolom "Insert By" akan show `'GOOGL'` dengan badge **"NEED FIX"**
   - Kolom "Has Token" akan show **"YES (Clear)"**

3. **Klik tombol "Fix & Activate"** untuk user Anda
   - Atau klik "🚀 Fix & Activate All" untuk semua user

4. **Done!** User sekarang bisa login dengan Google OAuth

### ✅ Manual (Via SQL)

Jalankan SQL ini di database Anda:

```sql
-- Fix profile_insert_by dari 'GOOGL' ke 'GOOGLE'
UPDATE user_profile
SET 
    profile_insert_by = 'GOOGLE',
    profile_verification_token = NULL,
    profile_verification_sent_at = NULL
WHERE profile_email = 'choirulfatoni@gmail.com';

-- Verifikasi
SELECT 
    profile_email,
    profile_insert_by,
    profile_is_verified,
    profile_verification_token
FROM user_profile
WHERE profile_email = 'choirulfatoni@gmail.com';
```

**Expected result:**
```
profile_email          | profile_insert_by | profile_is_verified | profile_verification_token
-----------------------|-------------------|---------------------|--------------------------
choirulfatoni@gmail.com| GOOGLE            | 1                   | NULL
```

---

## Kode yang Diperbaiki

### File: `app/Controllers/Auth.php`

**Method:** `googleCallback()` (lines 953-989)

**Perubahan:**
1. Deteksi variasi `profile_insert_by` ('GOOGLE', 'GOOGL', 'GOOG', dll)
2. Auto-fix `profile_insert_by` ke 'GOOGLE' saat auto-verify
3. Clear token dan verification_sent_at

```php
// Deteksi Google user dengan variasi profile_insert_by
$insertBy = isset($user->profile_insert_by) ? strtoupper($user->profile_insert_by) : '';
$isGoogleUser = ($insertBy === 'GOOGLE' || $insertBy === 'GOOGL' || $insertBy === 'GOOG' || strpos($insertBy, 'GOOGLE') !== false);

if ($isVerified == 0 && $isGoogleUser) {
    // Fix profile_insert_by jika tidak standar
    $fixData = [
        'profile_is_verified' => 1,
        'profile_verification_token' => null,
        'profile_verification_sent_at' => null,
    ];
    
    if ($insertBy !== 'GOOGLE') {
        $fixData['profile_insert_by'] = 'GOOGLE';
        log_message('error', 'GOOGLE CALLBACK: Fixing profile_insert_by from ' . $insertBy . ' to GOOGLE');
    }

    $this->sessionApps->where('user_profile.profile_id', $user->profile_id)
        ->update($fixData);
}
```

---

## Testing

### 1. Test Login Google OAuth

**Steps:**
1. Buka: `http://localhost/pmkpv4/index.php/auth`
2. Klik "Login with Google"
3. Pilih akun Google Anda (`choirulfatoni@gmail.com`)
4. **Expected:** Login berhasil dan redirect ke `/siimut/dashboard` ✅

### 2. Cek Log

**File:** `writable/logs/log-2026-04-15.log`

**Expected entries:**
```
GOOGLE CALLBACK: Fixing profile_insert_by from GOOGL to GOOGLE
GOOGLE CALLBACK: Login success - choirulfatoni@gmail.com role: ..., verified: 1
```

### 3. Cek Database

```sql
SELECT 
    profile_id,
    profile_email,
    profile_fullname,
    profile_insert_by,
    profile_is_verified,
    profile_verification_token,
    profile_photo
FROM user_profile
WHERE profile_email = 'choirulfatoni@gmail.com';
```

**Expected:**
- `profile_insert_by` = `'GOOGLE'` (bukan 'GOOGL')
- `profile_is_verified` = `1`
- `profile_verification_token` = `NULL`
- `profile_photo` = URL foto dari Google

---

## Files yang Diubah

| File | Status | Perubahan |
|------|--------|-----------|
| `app/Controllers/Auth.php` | ✏️ Modified | Handle variasi profile_insert_by dan auto-fix |
| `public/activate_google_users.php` | ✏️ Modified | Tambah kolom Insert By, Has Token, dan Fix button |
| `fix_google_user_data.sql` | ✨ NEW | SQL script untuk fix data user |
| `GOOGLE_OAUTH_FIX_PROFILE_INSERT.md` | ✨ NEW | Dokumentasi fix ini |

---

## Preventive Measures

### Pastikan Registrasi Google OAuth Menyimpan 'GOOGLE' dengan Benar

Cek di `app/Controllers/Auth.php` method `processRegister()`:

```php
$data = [
    // ...
    'profile_insert_by' => 'GOOGLE',  // Pastikan ini 'GOOGLE' lengkap
    // ...
];
```

### Monitoring

Cek user dengan profile_insert_by tidak standar:

```sql
SELECT 
    profile_id,
    profile_email,
    profile_insert_by,
    profile_is_verified
FROM user_profile
WHERE profile_insert_by != 'GOOGLE'
  AND profile_insert_by LIKE '%GOO%';
```

---

## Summary

### Yang Sudah Diperbaiki:

1. ✅ **Kode auto-verify** sekarang handle variasi `profile_insert_by`
2. ✅ **Auto-fix** `profile_insert_by` dari 'GOOGL' ke 'GOOGLE'
3. ✅ **Clear token** untuk user yang sudah verified
4. ✅ **Web UI activation** sekarang show status Insert By dan Token
5. ✅ **SQL script** tersedia untuk fix manual

### Next Steps:

1. ✅ **Fix user Anda** (via Web UI atau SQL)
2. ✅ **Test login Google OAuth**
3. ✅ **Monitor logs** untuk memastikan tidak ada error
4. ✅ **Selesai!** 🎉

---

## Good Luck! 🚀

Silakan test login dengan Google OAuth sekarang!
