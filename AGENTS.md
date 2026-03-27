# Agent Guidelines for PMKPV4

## Project Overview

This is a CodeIgniter 4 PHP application (PMK PV4). It uses:
- PHP 8.1+
- CodeIgniter 4 framework
- PHPUnit for testing
- Composer for dependency management

## Build/Test Commands

```bash
# Run all tests
composer test
phpunit

# Run a single test file
phpunit tests/unit/HealthTest.php

# Run a specific test class
phpunit --filter HealthTest

# Run tests with coverage
phpunit --coverage-html build/logs/html
```

## Code Style Guidelines

### PHP Standards
- Use PHP 8.1+ features (typed properties, named arguments)
- PSR-4 autoloading: `App\` namespace for application code
- All PHP files must use `<?php` tag (no short tags)
- Use strict typing: `declare(strict_types=1);`

### Naming Conventions
- **Classes**: PascalCase (e.g., `HomeController`, `Template`)
- **Methods**: camelCase (e.g., `index()`, `getData()`)
- **Properties**: camelCase (e.g., `$request`, `$helpers`)
- **Constants**: UPPER_CASE with underscores (e.g., `BASE_URL`)
- **Files**: Snake_case (e.g., `home_controller.php` - though CI uses PascalCase for classes)
- **Views**: snake_case.php (e.g., `index.php`, `_form_add_ikp.php`)

### Code Formatting
- Use 4 spaces for indentation (no tabs)
- Maximum line length: 120 characters
- Add space after commas in parameter lists
- Use braces for all control structures (even single-line)
- One blank line between method definitions

### Imports and Namespaces
- Group imports: `use` statements at top of file
- Order: PHP built-in, Composer packages, Application classes
- Use fully qualified class names or explicit imports

### Types and Type Hints
- Use return type declarations on all methods
- Use parameter type hints where applicable
- Use nullable types when appropriate: `?string`, `?array`
- Define property types explicitly

### Controllers (App\Controllers\)
- Extend `BaseController` (which extends `CodeIgniter\Controller`)
- Place business logic in model/services, not controllers
- Use `$this->request` for input data
- Return response via `return` (string, view, redirect, JSON)

### Views (app/Views\)
- Use `.php` extension
- Use snake_case naming: `index.php`, `_form_drafts.php`
- Prefix partials with underscore: `_header.php`, `_sidebar.php`
- Place in logical subdirectories: `ikprs/index.php`, `dashboard/index.php`

### Models
- Use CodeIgniter's `CodeIgniter\Model` or create custom model classes
- Follow CI4 conventions for table names (plural, snake_case)

### Config Files (app\Config\)
- Extend `CodeIgniter\Config\BaseConfig`
- Use public properties for configuration
- Keep environment-specific values in `.env`

### Error Handling
- Use CodeIgniter's exception handling
- Use `throw new \CodeIgniter\Exceptions\PageNotFoundException()` for 404s
- Log errors using `log_message('error', ...)`
- Return appropriate HTTP status codes in API responses

### Database
- Use CodeIgniter's Query Builder or raw queries appropriately
- Use migrations for schema changes
- Escape all user inputs to prevent SQL injection

### Security
- Never commit secrets, API keys, or credentials
- Use `.env` for sensitive configuration
- Sanitize all user inputs
- Use CSRF protection on forms (CodeIgniter's built-in)

### Testing
- Create test files in `tests/` directory
- Follow PHPUnit conventions
- Test one thing per test method
- Use descriptive test method names: `testUserCanLogin()`

### File Structure Reference
```
app/
├── Controllers/     # HTTP controllers (Home, Auth, Ikprs, Dashboard)
├── Models/          # Data models
├── Views/           # PHP templates
├── Config/          # Configuration classes
├── Filters/         # Request filters (AuthFilter, HrisFilter)
├── Helpers/         # Helper functions (notifikasi, captcha)
├── Libraries/       # Custom libraries
tests/
├── unit/            # Unit tests
├── database/        # Database tests
├── session/         # Session tests
```

### Common Patterns in This Project
- Custom `Template` library for view rendering
- Session-based authentication
- Multi-layer views: layout + partials + content
- Indonesian-language UI text in views

### Helpers and Libraries
- Custom helpers go in `app/Helpers/` (e.g., `notifikasi_helper.php`, `captcha_helper.php`)
- Custom libraries go in `app/Libraries/`
- Helpers are loaded via `$this->helpers = ['notifikasi'];` in controllers

### Routes Configuration
- Routes are defined in `app/Config/Routes.php`
- Use RESTful controller routing when possible
- Group routes by feature using `$routes->group()`

### Filters (app/Filters\)
- Used for request preprocessing (authentication, CSRF, etc.)
- Implement `FilterInterface`
- Register filters in `app/Config/Filters.php`
- Common filters: `AuthFilter`, `HrisFilter` for access control

### Services (app/Config/Services.php)
- Use CodeIgniter's service container for dependency injection
- Access services via `service('session')`, `service('email')`, etc.

### Working with Views
- Use the custom Template library for consistent page rendering
- Layout pattern: `_template.php` > `_header.php`, `_sidebar.php`, `_content.php`, `_footer.php`
- Partials prefixed with underscore: `_form_drafts.php`
- Pass data to views as associative arrays

### Development Workflow
1. Create/update controllers in `app/Controllers/`
2. Create/update views in `app/Views/`
3. Update routes in `app/Config/Routes.php`
4. Add filters if needed in `app/Filters/`
5. Run tests: `composer test`
6. Test locally using PHP built-in server: `php spark serve`

### Key Files to Know
- `app/Controllers/BaseController.php` - Base controller all controllers extend
- `app/Config/App.php` - Main app configuration (timezone, session, baseURL)
- `app/Config/Database.php` - Database connection settings
- `app/Config/Session.php` - Session configuration
- `app/Config/Routes.php` - URL routing definitions
- `.env` - Environment variables (database, API keys, etc.)