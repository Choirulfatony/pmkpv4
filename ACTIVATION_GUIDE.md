# Cara Aktivasi User Google OAuth yang Sudah Ada

## Masalah

User yang sudah terdaftar di database `user_profile` tidak bisa login dengan Google OAuth karena belum terverifikasi.

## Solusi

Ada 3 cara untuk mengaktifkan user:

---

## Cara 1: Web UI (REKOMENDASI - Paling Mudah) ✅

### Langkah:

1. **Buka browser dan akses:**
   ```
   http://localhost/pmkpv4/activate_google_users.php
   ```

2. **Lihat statistik user:**
   - Total Users
   - Google OAuth Users
   - Verified Users
   - Need Activation

3. **Pilih salah satu:**
   
   **Opsi A: Aktivasi Satu Per Satu**
   - Klik tombol "Activate" di samping user yang ingin diaktivasi
   - User akan langsung terverifikasi
   
   **Opsi B: Aktivasi Semua Sekaligus**
   - Klik tombol "🚀 Activate All X Users"
   - Konfirmasi di modal yang muncul
   - Semua user Google yang belum terverifikasi akan diaktivasi

4. **Cek hasil aktivasi:**
   - Statistics akan update
   - Activity log menunjukkan user yang sudah diaktivasi

### Keuntungan:
- ✅ Visual dan mudah digunakan
- ✅ Bisa pilih user mana yang mau diaktivasi
- ✅ Ada konfirmasi sebelum aktivasi massal
- ✅ Langsung bisa lihat hasilnya

---

## Cara 2: SQL Script (Untuk Developer/DBA)

### Langkah:

1. **Buka MySQL Client** (phpMyAdmin, MySQL Workbench, dll)

2. **Buka file SQL:**
   ```
   activate_all_google_users.sql
   ```

3. **Jalankan query pertama** untuk lihat user yang belum terverifikasi:
   ```sql
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
   ```

4. **Jalankan query UPDATE** untuk aktivasi semua user Google:
   ```sql
   UPDATE user_profile
   SET 
       profile_is_verified = 1,
       profile_verification_token = NULL,
       profile_verification_sent_at = NULL
   WHERE profile_insert_by = 'GOOGLE'
     AND (profile_is_verified = 0 OR profile_is_verified IS NULL);
   ```

5. **Verifikasi hasil:**
   ```sql
   SELECT 
       COUNT(*) as total_google_users,
       SUM(CASE WHEN profile_is_verified = 1 THEN 1 ELSE 0 END) as verified_count,
       SUM(CASE WHEN profile_is_verified = 0 THEN 1 ELSE 0 END) as unverified_count
   FROM user_profile
   WHERE profile_insert_by = 'GOOGLE';
   ```

### Opsi Query Lainnya:

**Aktivasi user tertentu berdasarkan email:**
```sql
UPDATE user_profile
SET 
    profile_is_verified = 1,
    profile_verification_token = NULL
WHERE profile_email = 'choirulfatoni@gmail.com';
```

**Aktivasi semua user Gmail:**
```sql
UPDATE user_profile
SET 
    profile_is_verified = 1,
    profile_verification_token = NULL
WHERE profile_email LIKE '%@gmail.com'
  AND (profile_is_verified = 0 OR profile_is_verified IS NULL);
```

---

## Cara 3: Command Line (Via Terminal)

### Langkah:

1. **Buka terminal/command prompt**

2. **Masuk ke direktori project:**
   ```bash
   cd C:\xampp\htdocs\pmkpv4
   ```

3. **Jalankan SQL via MySQL CLI:**
   ```bash
   mysql -h 192.168.1.68 -u choirul -p sidokar_db < activate_all_google_users.sql
   ```
   
   Atau gunakan PHP:
   ```bash
   php -r "
   require 'vendor/autoload.php';
   require 'app/Config/Paths.php';
   \$paths = new Config\Paths();
   require SYSTEMPATH . 'bootstrap.php';
   \$db = Config\Database::connect();
   \$db->table('user_profile')
       ->where('profile_insert_by', 'GOOGLE')
       ->where('profile_is_verified', 0)
       ->update(['profile_is_verified' => 1, 'profile_verification_token' => null]);
   echo 'All Google users activated!\n';
   "
   ```

---

## Verifikasi Setelah Aktivasi

### 1. Cek via Web UI:
```
http://localhost/pmkpv4/verify_columns.php
```

Akan menampilkan:
- ✅ Status kolom database
- ✅ User statistics
- ✅ Informasi lengkap

### 2. Test Login Google OAuth:

1. Buka: `http://localhost/pmkpv4/index.php/auth`
2. Klik "Login with Google"
3. Pilih akun Google Anda
4. **Expected:** Login berhasil dan redirect ke `/siimut/dashboard`

### 3. Cek Log:
```
writable/logs/log-2026-04-15.log
```

Cari entry:
```
GOOGLE CALLBACK: Login success - your.email@gmail.com
```

---

## Penjelasan Database Fields

Setelah aktivasi, field ini akan di-update:

| Field | Sebelum | Sesudah | Keterangan |
|-------|---------|---------|------------|
| `profile_is_verified` | `0` | `1` | Status verifikasi |
| `profile_verification_token` | `'abc123...'` | `NULL` | Token tidak diperlukan lagi |
| `profile_verification_sent_at` | `'2024-01-01...'` | `NULL` | Timestamp tidak diperlukan |

---

## Security Notes

### ✅ Aman Karena:
- Hanya user Google OAuth yang diaktivasi
- User dengan `profile_insert_by = 'GOOGLE'` saja
- User yang register manual tetap harus verifikasi email
- Auto-verify tetap berlaku untuk login Google berikutnya

### ⚠️ Penting:
- Jangan aktivasi user yang tidak dikenal
- Backup database sebelum aktivasi massal
- Monitor log setelah aktivasi
- Hanya jalankan sekali untuk user yang sudah ada

---

## Troubleshooting

### Problem: User masih tidak bisa login Google OAuth

**Check:**
1. Pastikan email sama persis di Google dan database
2. Cek apakah `profile_is_verified = 1` setelah aktivasi
3. Cek log error di `writable/logs/log-*.log`

**Solution:**
```sql
-- Cek user tertentu
SELECT * FROM user_profile WHERE profile_email = 'your.email@gmail.com';

-- Aktivasi manual
UPDATE user_profile
SET profile_is_verified = 1, profile_verification_token = NULL
WHERE profile_email = 'your.email@gmail.com';
```

### Problem: Error saat akses activate_google_users.php

**Check:**
1. Pastikan Apache/PHP running
2. Cek error di browser console
3. Pastikan CodeIgniter bisa connect database

**Solution:**
- Restart Apache
- Clear browser cache
- Cek `.env` untuk database config

---

## Files

| File | Purpose |
|------|---------|
| `public/activate_google_users.php` | Web UI untuk aktivasi user |
| `public/verify_columns.php` | Web UI untuk cek kolom database |
| `activate_all_google_users.sql` | SQL script untuk aktivasi |
| `GOOGLE_OAUTH_FIX.md` | Dokumentasi lengkap fix |

---

## Recommended Steps

Urutan yang disarankan:

1. ✅ **Jalankan Migration** (sudah dilakukan)
   ```bash
   php spark migrate
   ```

2. ✅ **Aktivasi User** (pilih salah satu cara di atas)
   - Via Web UI (paling mudah)
   - Via SQL Script
   - Via Command Line

3. ✅ **Test Login**
   - Coba login dengan Google OAuth
   - Cek apakah berhasil

4. ✅ **Monitor Log**
   - Cek `writable/logs/log-*.log`
   - Pastikan tidak ada error

5. ✅ **Selesai!** 🎉

---

## Contact/Support

Jika masih ada masalah setelah aktivasi:
1. Cek log error di `writable/logs/`
2. Pastikan semua kolom database sudah ada
3. Test dengan user yang berbeda
4. Periksa konfigurasi Google OAuth di `.env`

**Good luck!** 🚀
