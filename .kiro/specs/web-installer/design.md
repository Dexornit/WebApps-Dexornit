# Design Document: Web Installer

## Overview

Web Installer adalah sistem wizard multi-step yang memandu pengguna melalui proses instalasi awal aplikasi Laravel. Sistem ini menggunakan middleware untuk deteksi status instalasi, session untuk menyimpan progress, dan Alpine.js untuk interaktivitas frontend. Setelah instalasi selesai, installer otomatis terkunci dengan marker file.

**Teknologi Stack:**
- Backend: Laravel 13.7 (PHP 8.3+)
- Frontend: Blade templates + Alpine.js + Tailwind CSS
- Database: SQLite atau MySQL (user pilih saat instalasi)
- Session: File-based untuk menyimpan progress wizard

## Architecture

### High-Level Flow

```
User Request → Installer Middleware → Check .installed marker
                                    ↓
                        Marker exists? → Yes → Redirect to main app
                                    ↓
                                   No → Show installer wizard
                                    ↓
                        Step 1: Environment Check
                        Step 2: Database Setup
                        Step 3: Admin Account
                        Step 4: App Settings
                        Step 5: Complete → Create marker → Redirect to login
```

### Folder Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   └── Installer/
│   │       └── InstallerController.php (single controller, handles all steps)
│   └── Middleware/
│       └── CheckInstallation.php (detects installation status)
├── Services/
│   └── Installer/
│       ├── EnvironmentChecker.php
│       ├── DatabaseConfigurator.php
│       ├── MigrationRunner.php
│       ├── AdminSeeder.php
│       └── CleanupService.php
└── Models/
    └── User.php (existing)

resources/views/
└── installer/
    ├── layout.blade.php (wizard layout with step indicator)
    ├── step1-environment.blade.php
    ├── step2-database.blade.php
    ├── step3-admin.blade.php
    ├── step4-settings.blade.php
    └── step5-complete.blade.php

storage/app/
└── .installed (marker file, created after completion)

routes/
└── web.php (installer routes group)
```

## Components and Interfaces

### 1. Middleware: CheckInstallation

**Responsibility:** Deteksi status instalasi dan routing logic

**Logic:**
- Check apakah file `storage/app/.installed` exists
- Jika tidak ada: redirect semua request (kecuali `/install/*`) ke installer
- Jika ada: block akses ke `/install/*`, redirect ke home

**Applied to:** Global middleware (semua routes)

### 2. Controller: InstallerController

**Responsibility:** Handle semua step wizard dan form submission

**Methods:**
- `index()` - Show current step (dari session)
- `step1()` - Environment check
- `step2()` - Database configuration form
- `step2Store()` - Test connection, save to .env
- `step3()` - Admin account form
- `step3Store()` - Create admin user
- `step4()` - App settings form
- `step4Store()` - Save settings to .env
- `step5()` - Complete installation, create marker
- `previous()` - Navigate back to previous step

**Session Keys:**
- `installer.current_step` (1-5)
- `installer.step1_passed`
- `installer.step2_data`
- `installer.step3_data`
- `installer.step4_data`

### 3. Service: EnvironmentChecker

**Responsibility:** Validate server requirements

**Checks:**
- PHP version >= 8.3
- Required extensions: PDO, Mbstring, OpenSSL, Tokenizer, XML, Ctype, JSON, BCMath, Fileinfo
- Write permissions: storage/, bootstrap/cache/, database/

**Returns:** Array of checks with status (pass/fail) dan messages

### 4. Service: DatabaseConfigurator

**Responsibility:** Configure dan test database connection

**Methods:**
- `testConnection($config)` - Test DB connection dengan credentials
- `writeToEnv($config)` - Write DB config ke .env file
- `generateAppKey()` - Generate APP_KEY menggunakan `Str::random(32)`

**Supported DB Types:** SQLite, MySQL

### 5. Service: MigrationRunner

**Responsibility:** Execute database migrations

**Methods:**
- `runMigrations()` - Execute `php artisan migrate --force`
- `rollbackOnError()` - Rollback jika ada error

**Error Handling:** Catch exceptions, rollback, return error message

### 6. Service: AdminSeeder

**Responsibility:** Create first admin user

**Methods:**
- `createAdmin($name, $email, $password)` - Insert user ke database
- Password di-hash dengan bcrypt (cost 12)
- Validate email uniqueness sebelum insert

### 7. Service: CleanupService

**Responsibility:** Delete unnecessary files after installation

**Files to Delete:**
- BUGFIX_REPORT.md
- CHANGES_SUMMARY.md
- DEPLOYMENT_CHECKLIST.md
- DEPLOYMENT_GUIDE.md
- IMPLEMENTATION_COMPLETE.md
- PROJECT_COMPLETE.md
- TESTING_REPORT.md
- SECURITY.md

**Files to Keep:** README.md (update dengan installation info)

## Data Models

### Installation Marker File

**Path:** `storage/app/.installed`

**Format:** JSON
```json
{
  "installed_at": "2025-01-15 10:30:00",
  "version": "1.0.0",
  "php_version": "8.3.0",
  "database_type": "mysql"
}
```

### Session Data Structure

**Step 2 Data (Database):**
```php
[
  'db_type' => 'mysql|sqlite',
  'db_host' => 'localhost',
  'db_port' => '3306',
  'db_name' => 'wanseven',
  'db_user' => 'root',
  'db_pass' => 'secret'
]
```

**Step 3 Data (Admin):**
```php
[
  'name' => 'Admin',
  'email' => 'admin@example.com',
  'password' => 'hashed_password'
]
```

**Step 4 Data (Settings):**
```php
[
  'app_name' => 'Wanseven',
  'app_url' => 'http://localhost',
  'timezone' => 'Asia/Jakarta'
]
```

## Routing

**Route Group:** `/install`

**Middleware:** `web` (session, CSRF)

**Routes:**
```
GET  /install           → InstallerController@index (redirect to current step)
GET  /install/step1     → InstallerController@step1
GET  /install/step2     → InstallerController@step2
POST /install/step2     → InstallerController@step2Store
GET  /install/step3     → InstallerController@step3
POST /install/step3     → InstallerController@step3Store
GET  /install/step4     → InstallerController@step4
POST /install/step4     → InstallerController@step4Store
GET  /install/step5     → InstallerController@step5
POST /install/previous  → InstallerController@previous
```

## Error Handling

**Strategy:** User-friendly error messages dengan recovery options

**Error Types:**

1. **Environment Errors**
   - Missing PHP extensions → Show which extensions needed
   - Permission errors → Show which directories need chmod 775

2. **Database Errors**
   - Connection failed → Show DB error message, allow retry
   - Migration failed → Rollback, show error, allow retry

3. **Validation Errors**
   - Invalid email → Show inline error
   - Password mismatch → Show inline error
   - Invalid URL → Show inline error

**Error Display:**
- Inline errors: Below input fields (red text)
- Step errors: Alert box at top of form
- Critical errors: Full-page error with instructions

**Logging:** All errors logged to `storage/logs/laravel.log`

## Testing Strategy

### Assessment: Property-Based Testing Applicability

This feature is **NOT suitable for property-based testing** because:
- It's primarily an **installation workflow** with side effects (file writes, DB operations, user creation)
- Most operations are **one-time setup tasks** with external dependencies
- Testing involves **infrastructure configuration** (environment, database, file system)
- Behavior is **deterministic** and doesn't vary meaningfully with input ranges

**Appropriate Testing Approach:** Unit tests + Integration tests + Manual testing

### Unit Tests

**Focus:** Individual service methods with mocked dependencies

**Test Cases:**

1. **EnvironmentChecker**
   - Test PHP version validation (pass/fail scenarios)
   - Test extension checking (all present, some missing)
   - Test permission checking (writable, not writable)

2. **DatabaseConfigurator**
   - Test SQLite configuration writing
   - Test MySQL configuration writing
   - Test connection validation (mock PDO)
   - Test APP_KEY generation (length, randomness)

3. **AdminSeeder**
   - Test user creation with valid data
   - Test duplicate email rejection
   - Test password hashing (bcrypt verification)

4. **CleanupService**
   - Test file deletion (mock filesystem)
   - Test README.md preservation
   - Test error handling when file deletion fails

**Example Test Structure:**
```php
// Test environment checker detects missing extensions
public function test_environment_checker_detects_missing_extensions()
{
    // Mock extension_loaded() to return false for 'mbstring'
    // Assert that check fails with correct error message
}

// Test database configurator writes correct .env values
public function test_database_configurator_writes_mysql_config()
{
    // Provide MySQL config array
    // Mock file writing
    // Assert .env contains correct DB_CONNECTION, DB_HOST, etc.
}

// Test admin seeder rejects duplicate email
public function test_admin_seeder_rejects_duplicate_email()
{
    // Create user with email
    // Attempt to create another user with same email
    // Assert validation error returned
}
```

### Integration Tests

**Focus:** End-to-end workflow with real database

**Test Cases:**

1. **Full Installation Flow**
   - Start with clean state (no .installed marker)
   - Navigate through all steps
   - Verify .env file updated correctly
   - Verify database tables created
   - Verify admin user exists
   - Verify .installed marker created

2. **Installation Locking**
   - Complete installation
   - Attempt to access /install routes
   - Verify redirect to main app

3. **Progress Persistence**
   - Complete step 2
   - Simulate browser refresh
   - Verify step 2 data restored from session

4. **Error Recovery**
   - Provide invalid database credentials
   - Verify error message displayed
   - Correct credentials and retry
   - Verify installation continues

### Manual Testing Checklist

- [ ] Test on fresh Laravel installation
- [ ] Test with SQLite database
- [ ] Test with MySQL database
- [ ] Test with missing PHP extensions (simulate)
- [ ] Test with insufficient permissions (simulate)
- [ ] Test form validation (all fields)
- [ ] Test responsive design (mobile, tablet, desktop)
- [ ] Test browser back button behavior
- [ ] Test session timeout handling
- [ ] Test reinstallation after deleting .installed marker

### Security Testing

- [ ] Verify CSRF protection on all forms
- [ ] Verify SQL injection prevention (parameterized queries)
- [ ] Verify XSS prevention (Blade escaping)
- [ ] Verify rate limiting on installation attempts
- [ ] Verify password hashing strength (bcrypt cost >= 12)
- [ ] Verify APP_KEY cryptographic strength

## UI/UX Design

### Step Indicator

**Visual:** Horizontal progress bar dengan 5 steps

```
[1] Environment → [2] Database → [3] Admin → [4] Settings → [5] Complete
 ✓                  ●              ○            ○             ○
```

- Completed steps: Green checkmark
- Current step: Blue dot
- Future steps: Gray circle

### Form Layout

**Structure:**
- Step title (h1)
- Step description (p)
- Form fields (vertical stack)
- Validation errors (inline, red)
- Action buttons (bottom right)

**Buttons:**
- Previous (secondary, left)
- Next/Submit (primary, right)
- Loading spinner during async operations

### Responsive Breakpoints

- Mobile: 375px - 640px (single column)
- Tablet: 641px - 1024px (single column, larger inputs)
- Desktop: 1025px+ (centered card, max-width 600px)

### Alpine.js Interactivity

**Features:**
- Real-time validation (x-model, @input)
- Password strength indicator
- Database connection test (AJAX)
- Loading states (x-show)
- Step navigation (x-data)

**Example Alpine Component:**
```html
<div x-data="{ testing: false, result: null }">
  <button @click="testConnection()">Test Connection</button>
  <div x-show="testing">Testing...</div>
  <div x-show="result === 'success'">✓ Connected</div>
  <div x-show="result === 'error'">✗ Failed</div>
</div>
```

## Security Considerations

1. **CSRF Protection:** All forms include `@csrf` token
2. **Input Sanitization:** Use Laravel validation rules
3. **SQL Injection Prevention:** Use Eloquent ORM and parameterized queries
4. **XSS Prevention:** Blade auto-escapes output
5. **Rate Limiting:** Max 5 installation attempts per 15 minutes (throttle middleware)
6. **Password Security:** Bcrypt with cost 12
7. **APP_KEY Security:** Cryptographically secure random generator
8. **File Path Validation:** Prevent directory traversal attacks

## Performance Considerations

**Optimization Strategies:**

1. **Lazy Loading:** Load Alpine.js and Tailwind CSS only on installer pages
2. **Session Cleanup:** Clear installer session after 24 hours
3. **Database Connection Pooling:** Reuse connection during migration
4. **Async Operations:** Use AJAX for database connection test (avoid page reload)
5. **Minimal Dependencies:** No additional packages required

**Expected Performance:**
- Environment check: < 1 second
- Database connection test: < 2 seconds
- Migration execution: 5-10 seconds (depends on migration count)
- Total installation time: < 30 seconds

## Deployment Notes

**Pre-Installation Requirements:**
- Web server (Apache/Nginx) configured
- PHP 8.3+ installed with required extensions
- Composer installed
- Node.js and npm installed (for asset compilation)

**Installation Trigger:**
- User accesses application URL for first time
- No .installed marker exists
- Automatically redirected to /install

**Post-Installation:**
- .installed marker prevents re-access
- To reinstall: manually delete `storage/app/.installed`
- Backup database before reinstallation

## Future Enhancements

**Potential Improvements (out of scope for v1):**

1. **Multi-language Support:** Translate installer to Indonesian
2. **Database Backup:** Automatic backup before reinstallation
3. **Email Configuration:** Add SMTP setup step
4. **Advanced Settings:** Cache driver, queue driver selection
5. **Installation Log:** Detailed log of installation steps
6. **Rollback Feature:** Undo installation if user cancels mid-process
7. **CLI Installer:** Command-line alternative to web installer
