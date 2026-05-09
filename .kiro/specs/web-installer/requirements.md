# Requirements Document

## Introduction

Sistem Web Installer adalah fitur yang memandu pengguna melalui proses instalasi awal aplikasi Laravel wanseven.com. Installer ini menyediakan wizard step-by-step untuk konfigurasi environment, database, pembuatan akun admin, dan pengaturan aplikasi. Setelah instalasi selesai, sistem akan mencegah akses ulang ke installer untuk keamanan.

## Glossary

- **Installer**: Sistem wizard yang memandu proses instalasi awal aplikasi
- **Installation_Marker**: File atau record database yang menandai aplikasi sudah terinstall
- **Environment_Checker**: Komponen yang memvalidasi persyaratan sistem (PHP version, extensions, permissions)
- **Database_Configurator**: Komponen yang menangani konfigurasi koneksi database
- **Admin_Account_Creator**: Komponen yang membuat akun administrator pertama
- **Application_Settings_Manager**: Komponen yang mengelola pengaturan aplikasi dasar
- **Installer_Middleware**: Middleware yang mengontrol akses ke installer dan redirect logic
- **Installation_Wizard**: Interface multi-step untuk proses instalasi
- **APP_KEY**: Kunci enkripsi aplikasi Laravel yang di-generate secara otomatis
- **Migration_Runner**: Komponen yang menjalankan database migrations
- **Admin_Seeder**: Komponen yang membuat user admin pertama di database
- **Validation_Engine**: Sistem yang memvalidasi input pengguna secara real-time
- **Cleanup_Service**: Komponen yang menghapus file-file yang tidak diperlukan setelah instalasi

## Requirements

### Requirement 1: Installation Detection and Routing

**User Story:** Sebagai pengguna yang mengakses aplikasi, saya ingin sistem otomatis mendeteksi status instalasi, sehingga saya diarahkan ke halaman yang tepat (installer atau aplikasi utama)

#### Acceptance Criteria

1. WHEN a user accesses the application root URL, THE Installer_Middleware SHALL check for the existence of Installation_Marker
2. IF Installation_Marker does not exist, THEN THE Installer_Middleware SHALL redirect the user to the installer wizard
3. IF Installation_Marker exists, THEN THE Installer_Middleware SHALL allow access to the main application
4. WHEN a user attempts to access installer routes directly, IF Installation_Marker exists, THEN THE Installer_Middleware SHALL redirect to the main application with an error message
5. THE Installation_Marker SHALL be stored as a file named ".installed" in the storage/app directory

### Requirement 2: Environment Requirements Validation

**User Story:** Sebagai pengguna yang menjalankan installer, saya ingin sistem memeriksa persyaratan environment, sehingga saya tahu apakah server memenuhi syarat untuk menjalankan aplikasi

#### Acceptance Criteria

1. WHEN the installer wizard starts, THE Environment_Checker SHALL validate PHP version is 8.3 or higher
2. THE Environment_Checker SHALL verify the following PHP extensions are enabled: PDO, Mbstring, OpenSSL, Tokenizer, XML, Ctype, JSON, BCMath, Fileinfo
3. THE Environment_Checker SHALL check write permissions for the following directories: storage, bootstrap/cache, database
4. WHEN any requirement fails validation, THE Environment_Checker SHALL display a specific error message indicating which requirement failed
5. WHEN all requirements pass validation, THE Environment_Checker SHALL enable the "Next" button to proceed to database configuration
6. THE Environment_Checker SHALL display validation results with visual indicators (checkmark for pass, X for fail)

### Requirement 3: Database Configuration

**User Story:** Sebagai pengguna yang menjalankan installer, saya ingin mengkonfigurasi koneksi database, sehingga aplikasi dapat menyimpan data

#### Acceptance Criteria

1. THE Database_Configurator SHALL provide options to select between SQLite and MySQL database types
2. WHERE SQLite is selected, THE Database_Configurator SHALL validate or create the database file path
3. WHERE MySQL is selected, THE Database_Configurator SHALL collect host, port, database name, username, and password
4. WHEN database credentials are provided, THE Database_Configurator SHALL test the connection before proceeding
5. IF the database connection test fails, THEN THE Database_Configurator SHALL display a descriptive error message
6. WHEN the database connection test succeeds, THE Database_Configurator SHALL write the configuration to the .env file
7. THE Database_Configurator SHALL set DB_CONNECTION to "sqlite" or "mysql" based on user selection

### Requirement 4: Application Key Generation

**User Story:** Sebagai sistem installer, saya ingin generate APP_KEY secara otomatis, sehingga aplikasi memiliki kunci enkripsi yang aman

#### Acceptance Criteria

1. WHEN database configuration is completed, THE Installer SHALL generate a new APP_KEY using Laravel's key generation algorithm
2. THE Installer SHALL write the generated APP_KEY to the .env file
3. THE Installer SHALL use a cryptographically secure random generator for key generation
4. THE generated APP_KEY SHALL be 32 characters in base64 encoding

### Requirement 5: Database Migration Execution

**User Story:** Sebagai sistem installer, saya ingin menjalankan database migrations secara otomatis, sehingga struktur database terbentuk dengan benar

#### Acceptance Criteria

1. WHEN APP_KEY generation is completed, THE Migration_Runner SHALL execute all pending database migrations
2. IF any migration fails, THEN THE Migration_Runner SHALL rollback all migrations and display an error message
3. WHEN all migrations succeed, THE Migration_Runner SHALL log the successful migration execution
4. THE Migration_Runner SHALL display migration progress to the user during execution

### Requirement 6: Admin Account Creation

**User Story:** Sebagai pengguna yang menjalankan installer, saya ingin membuat akun administrator pertama, sehingga saya dapat mengelola aplikasi

#### Acceptance Criteria

1. THE Admin_Account_Creator SHALL collect name, email, and password from the user
2. THE Admin_Account_Creator SHALL validate email format using standard email validation rules
3. THE Admin_Account_Creator SHALL require password to be at least 8 characters long
4. THE Admin_Account_Creator SHALL require password confirmation to match the password
5. WHEN admin account data is submitted, THE Admin_Account_Creator SHALL hash the password using bcrypt
6. THE Admin_Seeder SHALL create a user record in the users table with the provided credentials
7. THE Admin_Seeder SHALL set the created user as an administrator (if role system exists)
8. IF email already exists in database, THEN THE Admin_Account_Creator SHALL display an error message

### Requirement 7: Application Settings Configuration

**User Story:** Sebagai pengguna yang menjalankan installer, saya ingin mengkonfigurasi pengaturan aplikasi dasar, sehingga aplikasi berjalan sesuai kebutuhan saya

#### Acceptance Criteria

1. THE Application_Settings_Manager SHALL collect application name from the user
2. THE Application_Settings_Manager SHALL collect application URL from the user
3. THE Application_Settings_Manager SHALL provide a dropdown to select timezone from available PHP timezones
4. THE Application_Settings_Manager SHALL validate URL format using standard URL validation rules
5. WHEN settings are submitted, THE Application_Settings_Manager SHALL write APP_NAME to the .env file
6. WHEN settings are submitted, THE Application_Settings_Manager SHALL write APP_URL to the .env file
7. WHEN settings are submitted, THE Application_Settings_Manager SHALL write APP_TIMEZONE to the .env file (if supported)

### Requirement 8: Installation Completion and Marker Creation

**User Story:** Sebagai sistem installer, saya ingin menandai instalasi sebagai selesai, sehingga installer tidak dapat diakses lagi

#### Acceptance Criteria

1. WHEN all installation steps are completed successfully, THE Installer SHALL create the Installation_Marker file
2. THE Installation_Marker file SHALL contain installation metadata including installation date and time
3. THE Installation_Marker file SHALL contain the installed version number
4. WHEN Installation_Marker is created, THE Installer SHALL display a success message
5. WHEN Installation_Marker is created, THE Installer SHALL redirect the user to the login page after 3 seconds
6. THE Installer SHALL clear any cached configuration after installation completion

### Requirement 9: Installation Wizard User Interface

**User Story:** Sebagai pengguna yang menjalankan installer, saya ingin interface yang modern dan mudah digunakan, sehingga proses instalasi tidak membingungkan

#### Acceptance Criteria

1. THE Installation_Wizard SHALL display a step indicator showing current step and total steps
2. THE Installation_Wizard SHALL display step numbers and titles: (1) Environment Check, (2) Database Setup, (3) Admin Account, (4) Application Settings, (5) Complete
3. THE Installation_Wizard SHALL use Tailwind CSS for styling with a clean, modern design
4. THE Installation_Wizard SHALL use Alpine.js for interactive elements and form validation
5. THE Installation_Wizard SHALL provide "Next" and "Previous" buttons for navigation between steps
6. THE Installation_Wizard SHALL disable "Next" button until current step validation passes
7. THE Installation_Wizard SHALL display a loading spinner during long-running operations (database test, migrations)
8. THE Installation_Wizard SHALL be responsive and work on mobile devices with screen width down to 375px

### Requirement 10: Real-time Input Validation

**User Story:** Sebagai pengguna yang mengisi form installer, saya ingin validasi input secara real-time, sehingga saya dapat memperbaiki kesalahan sebelum submit

#### Acceptance Criteria

1. WHEN a user types in an email field, THE Validation_Engine SHALL validate email format and display error if invalid
2. WHEN a user types in a password field, THE Validation_Engine SHALL display password strength indicator
3. WHEN a user types in password confirmation field, THE Validation_Engine SHALL validate it matches the password field
4. WHEN a user types in URL field, THE Validation_Engine SHALL validate URL format and display error if invalid
5. THE Validation_Engine SHALL display validation errors below the respective input field in red text
6. THE Validation_Engine SHALL display validation success with a green checkmark icon
7. THE Validation_Engine SHALL perform validation on blur event and on input event with 300ms debounce

### Requirement 11: Error Handling and User Feedback

**User Story:** Sebagai pengguna yang mengalami error saat instalasi, saya ingin pesan error yang jelas, sehingga saya tahu cara memperbaikinya

#### Acceptance Criteria

1. WHEN any installation step fails, THE Installer SHALL display an error message in a prominent alert box
2. THE Installer SHALL provide specific error messages for common failures: database connection, permission issues, migration errors
3. WHEN a database connection fails, THE Installer SHALL display the specific connection error from the database driver
4. WHEN a file permission error occurs, THE Installer SHALL display which directory lacks write permission
5. THE Installer SHALL provide a "Retry" button for recoverable errors
6. THE Installer SHALL log all errors to the Laravel log file for debugging purposes
7. IF a critical error occurs, THE Installer SHALL provide instructions to manually resolve the issue

### Requirement 12: Installation Security

**User Story:** Sebagai administrator sistem, saya ingin installer yang aman, sehingga aplikasi tidak dapat dieksploitasi selama proses instalasi

#### Acceptance Criteria

1. THE Installer SHALL validate and sanitize all user inputs before processing
2. THE Installer SHALL use CSRF protection for all form submissions
3. THE Installer SHALL prevent SQL injection by using parameterized queries for all database operations
4. THE Installer SHALL prevent directory traversal attacks when handling file paths
5. THE Installer SHALL rate-limit installation attempts to prevent brute force attacks (maximum 5 attempts per 15 minutes)
6. THE Installer SHALL generate APP_KEY using a cryptographically secure random number generator
7. THE Admin_Account_Creator SHALL hash passwords using bcrypt with a cost factor of 12 or higher

### Requirement 13: Post-Installation File Cleanup

**User Story:** Sebagai sistem installer, saya ingin menghapus file-file yang tidak diperlukan setelah instalasi, sehingga aplikasi lebih bersih dan aman

#### Acceptance Criteria

1. WHEN installation is completed, THE Cleanup_Service SHALL delete the following files: BUGFIX_REPORT.md, CHANGES_SUMMARY.md, DEPLOYMENT_CHECKLIST.md, DEPLOYMENT_GUIDE.md, IMPLEMENTATION_COMPLETE.md, PROJECT_COMPLETE.md, TESTING_REPORT.md, SECURITY.md
2. THE Cleanup_Service SHALL preserve README.md file
3. THE Cleanup_Service SHALL update README.md with installation instructions and basic usage information
4. IF any file deletion fails, THE Cleanup_Service SHALL log the error but continue with installation completion
5. THE Cleanup_Service SHALL execute after Installation_Marker creation

### Requirement 14: Reinstallation Support

**User Story:** Sebagai administrator sistem, saya ingin dapat mengulang instalasi jika diperlukan, sehingga saya dapat reset aplikasi ke kondisi awal

#### Acceptance Criteria

1. THE Installer SHALL provide documentation on how to trigger reinstallation
2. WHEN Installation_Marker file is manually deleted, THE Installer_Middleware SHALL allow access to the installer again
3. WHEN reinstallation is triggered, THE Installer SHALL warn that existing data will be lost
4. THE Installer SHALL provide an option to backup existing database before reinstallation
5. WHEN reinstallation proceeds, THE Installer SHALL drop all existing tables before running migrations

### Requirement 15: Installation Progress Persistence

**User Story:** Sebagai pengguna yang mengalami gangguan saat instalasi, saya ingin progress saya tersimpan, sehingga saya tidak perlu mengulang dari awal

#### Acceptance Criteria

1. WHEN a user completes a step, THE Installer SHALL save the step data to a temporary session
2. WHEN a user returns to the installer, THE Installer SHALL restore the last completed step
3. THE Installer SHALL allow users to navigate back to previous steps to modify data
4. WHEN a user modifies data in a previous step, THE Installer SHALL invalidate subsequent steps that depend on the modified data
5. WHEN installation is completed or abandoned for more than 24 hours, THE Installer SHALL clear the temporary session data
