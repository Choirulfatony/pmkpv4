# Google OAuth Auto-Verify Tests

## Overview

This test suite verifies the Google OAuth auto-verify functionality in the PMKPV4 application. The feature automatically verifies users who login via Google OAuth, since Google has already validated their email ownership.

## Test Files

### 1. `AuthGoogleOAuthTest.php`
**Location:** `tests/database/AuthGoogleOAuthTest.php`

**Tests:**
- ✅ `testGoogleCallbackRouteExists()` - Verifies route `auth/google-callback` is defined
- ✅ `testGoogleLoginRouteExists()` - Verifies route `auth/google-login` is defined
- ✅ `testAuthControllerHasGoogleCallbackMethod()` - Confirms method exists in Auth controller
- ✅ `testAuthControllerHasGoogleLoginMethod()` - Confirms method exists in Auth controller
- ✅ `testGoogleCallbackContainsAutoVerifyLogic()` - Validates source code contains auto-verify logic

## Running the Tests

```bash
# Run all Google OAuth tests
vendor/bin/phpunit tests/database/AuthGoogleOAuthTest.php

# Run with verbose output
vendor/bin/phpunit tests/database/AuthGoogleOAuthTest.php --verbose

# Run all tests
composer test
```

## What is Being Tested

### Auto-Verify Logic (in `Auth::googleCallback()`)

When a user logs in via Google OAuth:

1. **Google validates the email** → `$userInfo->getVerifiedEmail()` returns `true`
2. **User found in database** → Check `profile_is_verified` status
3. **If not verified** → Auto-update to `profile_is_verified = 1` and clear token
4. **If already verified** → Skip update (no duplicate writes)
5. **Login succeeds** → User gets access to `/siimut/dashboard`

### Code Pattern Being Tested

```php
if ($user) {
    // Auto-verify untuk user Google OAuth
    if ($user->profile_is_verified == 0) {
        log_message('error', 'GOOGLE CALLBACK: Auto-verifying email - ' . $email);
        
        // Update database: verifikasi user ini
        $this->sessionApps->where('user_profile.profile_id', $user->profile_id)
            ->update([
                'profile_is_verified' => 1,
                'profile_verification_token' => null,
            ]);
        
        // Update data user untuk session
        $user->profile_is_verified = 1;
    }
    
    // Login berhasil...
}
```

## Manual Testing Scenarios

Since full integration testing requires Google OAuth setup, here are manual test scenarios:

### Scenario 1: First-time Google Login (Unverified User)

**Setup:**
1. User registers via Google OAuth but doesn't complete email verification
2. Database: `profile_is_verified = 0`, `profile_verification_token = 'abc123'`

**Test:**
1. Click "Login with Google" button
2. Complete Google OAuth flow
3. User should be auto-verified and logged in successfully

**Expected Result:**
- Database updated: `profile_is_verified = 1`, `profile_verification_token = NULL`
- Session created with user data
- Redirect to `/siimut/dashboard`
- Log entry: `GOOGLE CALLBACK: Auto-verifying email - user@example.com`

### Scenario 2: Returning Google User (Already Verified)

**Setup:**
1. User previously logged in via Google OAuth
2. Database: `profile_is_verified = 1`, `profile_verification_token = NULL`

**Test:**
1. Click "Login with Google" button
2. Complete Google OAuth flow

**Expected Result:**
- No database update (already verified)
- Session created
- Redirect to `/siimut/dashboard`
- No auto-verify log entry

### Scenario 3: Regular Email/Password Login (Still Requires Verification)

**Setup:**
1. User registered manually (not via Google)
2. Database: `profile_is_verified = 0`, `profile_verification_token = 'token123'`

**Test:**
1. Enter email and password on login form
2. Submit form

**Expected Result:**
- Login **BLOCKED** with error message
- Error: "Akun Anda belum terverifikasi. Silakan verifikasi email terlebih dahulu."
- Link to resend verification email is shown
- User cannot access application until email verified

### Scenario 4: Google User with Unverified Google Email

**Setup:**
1. User has Google account but email not verified in Google

**Test:**
1. Click "Login with Google" button
2. Complete Google OAuth flow

**Expected Result:**
- Login **REJECTED** with error
- Error: "Email Google belum terverifikasi. Silakan verifikasi email terlebih dahulu."
- No session created
- Redirect back to login page

## Database Verification

To check user verification status:

```sql
-- Check all Google users and their verification status
SELECT 
    profile_id,
    profile_email,
    profile_fullname,
    profile_insert_by,
    profile_is_verified,
    profile_verification_token
FROM user_profile
WHERE profile_insert_by = 'GOOGLE'
ORDER BY profile_insert_date DESC;

-- Find unverified Google users
SELECT 
    profile_id,
    profile_email,
    profile_fullname
FROM user_profile
WHERE profile_insert_by = 'GOOGLE'
  AND profile_is_verified = 0;
```

## Troubleshooting

### Tests Fail with SQLite3 Error

**Error:** `The required PHP extension "sqlite3" is not loaded`

**Solution:** The current tests don't require database. If you add database tests:
1. Enable SQLite3 in `php.ini` (`extension=sqlite3`)
2. Or configure MySQL test database in `.env`

### Route Tests Fail

**Error:** Route not found

**Solution:** Verify routes exist:
```bash
php spark routes | findstr google
```

Expected output:
```
| GET | auth/google-login     | \App\Controllers\Auth::googleLogin
| GET | auth/google-callback  | \App\Controllers\Auth::googleCallback
```

### Auto-verify Not Working

**Check:**
1. User exists in database with correct email
2. Google OAuth returns `verified_email: true`
3. No exceptions in `googleCallback()` method
4. Check logs: `writable/logs/log-*.log`

**Debug:**
```php
// Add temporary debug in googleCallback()
log_message('error', 'DEBUG: User data = ' . print_r($user, true));
log_message('error', 'DEBUG: is_verified = ' . $user->profile_is_verified);
```

## Security Considerations

✅ **Safe because:**
- Google already validatesates email ownership
- `getVerifiedEmail()` from Google API is reliable
- Only users with Google-verified emails get auto-verified
- Regular email/password login still requires manual verification
- Verification token is cleared after auto-verify

⚠️ **Important:**
- Never auto-verify for manual registrations
- Always check `getVerifiedEmail()` from Google
- Keep manual verification flow for email/password login

## Related Files

- **Controller:** `app/Controllers/Auth.php` (method: `googleCallback()`)
- **Routes:** `app/Config/Routes.php`
- **Model:** `app/Models/SessionAppsModel.php`
- **Google Library:** `app/Libraries/GoogleLogin.php`
- **Login View:** `app/Views/auth/login.php`

## Future Enhancements

Consider adding:
1. Integration tests with mocked Google API
2. Database tests with test database setup
3. Session state testing
4. Performance tests for bulk auto-verify
5. Audit trail for auto-verify actions
