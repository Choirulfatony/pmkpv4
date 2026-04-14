# PMKPV4 - Project Context

## Project Overview

**PMKPV4** (PMKP v4) is a CodeIgniter 4 PHP web application developed for **RS Dr. Soedono Madiun** (RSSM), a hospital in Indonesia. The application manages hospital quality management (PMKP = *Penilaian Mutu Keselamatan Pasien* - Patient Safety and Quality Assessment).

The application features two main modules:
1. **SIIMUT** - Quality assessment reports, recapitulation, and charting (INM reports)
2. **IKPRS** - Incident reporting and patient safety incident management system

### Architecture
- **Framework**: CodeIgniter 4
- **PHP Version**: 8.1+
- **Database**: MySQL (primary) + PostgreSQL (SIMRS integration)
- **Authentication**: Session-based with dual login sources (Application DB and HRIS)
- **Dependencies**: Google API Client, PHPSpreadsheet (Excel export)

## Project Structure

```
pmkpv4/
├── app/
│   ├── Config/          # Configuration (App, Database, Routes, Filters, etc.)
│   ├── Controllers/     # HTTP controllers (Auth, Dashboard, Ikprs, RekapLaporanInm, etc.)
│   ├── Filters/         # Request filters (AuthFilter, HrisFilter)
│   ├── Helpers/         # Helper functions (captcha, notifikasi)
│   ├── Language/        # Internationalization
│   ├── Libraries/       # Custom libraries (Captcha, GoogleLogin, Template)
│   ├── Models/          # Data models (HrisUserModel, IkpInsidenModel, etc.)
│   ├── Views/           # PHP templates (auth, dashboard, ikprs, siimut, _layout)
│   ├── Database/        # Migrations and seeds
│   └── ThirdParty/      # Third-party code
├── public/              # Web root (index.php, assets)
├── tests/               # PHPUnit tests (unit, database, session)
├── writable/            # Cache, logs, sessions, uploads
└── vendor/              # Composer dependencies
```

## Key Modules

### Authentication (`Auth` controller)
- Dual login: Application email/password + HRIS NIP/password
- CAPTCHA protection
- Google OAuth login support
- Email verification for new registrations
- Session timeout after 30 minutes of inactivity
- Role-based access: `KENDALI_MUTU`, `KOMITE`, `ADMINISTRATOR`, `KARU`, `PELAPOR`

### SIIMUT Module (`/siimut/*`)
- Quality report recapitulation (`RekapLaporanInm`)
- Period recapitulation (`RekapPeriodeInm`)
- Charts/graphs (`GrafikInm`)
- Excel export functionality using PHPSpreadsheet

### IKPRS Module (`/ikprs/*`)
- Incident reporting forms (drafts, send, inbox)
- Patient lookup
- Verification and validation workflow (Karu/Komite)
- Notification system

## Building and Running

### Prerequisites
- PHP 8.1+ with extensions: `intl`, `mbstring`, `mysqlnd`, `libcurl`
- Composer
- MySQL database server

### Setup
```bash
# Install dependencies
composer install

# Copy and configure environment
cp env .env
# Edit .env with your database settings and baseURL
```

### Development Server
```bash
# Start CodeIgniter development server
php spark serve
```

The application is configured to run at `http://localhost/pmkpv4/` by default.

### Testing
```bash
# Run all tests
composer test
# or
phpunit

# Run a specific test file
phpunit tests/unit/HealthTest.php

# Run tests with coverage
phpunit --coverage-html build/logs/html
```

## Database Configuration

The application connects to **multiple databases**:

| Connection | Purpose | Type |
|-----------|---------|------|
| `default` | Application data (sidokar_db) | MySQL @ 192.168.1.68 |
| `db2` | HRIS (db_hris_rssm) | MySQL @ 10.10.103.105 |
| `simrs_db` | SIMRS (medismart) | PostgreSQL @ 192.168.1.74 |

> **Note:** Database credentials are stored in `app/Config/Database.php`. For production, move sensitive settings to `.env`.

## Development Conventions

### PHP Standards
- PSR-4 autoloading with `App\` namespace
- Strict typing: `declare(strict_types=1);`
- Typed properties and return type declarations
- 4-space indentation, max 120 characters per line

### Naming Conventions
- **Classes**: PascalCase (`HomeController`, `Template`)
- **Methods**: camelCase (`index()`, `getData()`)
- **Properties**: camelCase (`$request`, `$helpers`)
- **Constants**: UPPER_CASE (`BASE_URL`)
- **Views**: snake_case with underscore prefix for partials (`_form_drafts.php`)

### Controllers
- Extend `BaseController` (which extends `CodeIgniter\Controller`)
- Cache-control headers set to prevent browser back-button access
- Use `$this->request` for input, return via `return` (view, redirect, JSON)

### Views
- Layout pattern: `_template.php` composes `_header.php`, `_sidebar.php`, `_content.php`, `_footer.php`
- Custom `Template` library used for consistent rendering: `$template->render('view_name', $data)`
- Indonesian-language UI text throughout

### Routes
- Defined in `app/Config/Routes.php`
- Auth filter applied to protected routes: `['filter' => 'auth']`
- Routes grouped by module using `$routes->group()`

### Security
- Session timeout: 1800 seconds (30 minutes)
- CAPTCHA on login page
- Cache-control headers prevent browser caching of authenticated pages
- CSRF protection (CodeIgniter built-in)
- Secrets should be in `.env`, never committed

## Filters

| Filter | Purpose |
|--------|---------|
| `AuthFilter` | Authentication check, session timeout, login-source routing restriction |
| `HrisFilter` | HRIS-specific access control |

## Key Files

| File | Description |
|------|-------------|
| `app/Config/Routes.php` | URL routing definitions |
| `app/Config/Database.php` | Database connection settings (3 databases) |
| `app/Config/App.php` | Base URL, timezone (Asia/Jakarta), session config |
| `app/Controllers/BaseController.php` | Base controller with cache-control headers |
| `app/Controllers/Auth.php` | Authentication logic (login, logout, registration) |
| `app/Filters/AuthFilter.php` | Session validation and timeout logic |
| `app/Libraries/Template.php` | View rendering with layout composition |
| `app/Libraries/Captcha.php` | CAPTCHA generation and validation |

## Notable Patterns

1. **Dual Database Login**: Auto-detects login type by email (APP) vs NIP (HRIS)
2. **Role-Based Menu**: `user_role` session variable controls menu visibility
3. **Session Management**: `last_activity` tracking with 30-minute timeout
4. **AJAX Session Handling**: Returns 401 JSON for expired AJAX sessions instead of redirect
5. **Anti-Back Button**: Cache-control headers on all authenticated pages
6. **Email Verification**: New registrations require email confirmation before login
