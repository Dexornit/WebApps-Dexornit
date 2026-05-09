# Implementation Plan: Web Installer

## Overview

This implementation plan breaks down the Web Installer feature into discrete coding tasks. The installer is a multi-step wizard that guides users through initial application setup including environment validation, database configuration, admin account creation, and application settings. The implementation uses Laravel 13.7 with Blade templates, Alpine.js for interactivity, and Tailwind CSS for styling.

## Tasks

- [ ] 1. Set up installer folder structure and routing
  - Create `app/Http/Controllers/Installer/InstallerController.php`
  - Create `app/Http/Middleware/CheckInstallation.php`
  - Create `app/Services/Installer/` directory with service classes
  - Create `resources/views/installer/` directory for Blade templates
  - Add installer routes to `routes/web.php` with `/install` prefix
  - Register `CheckInstallation` middleware in `bootstrap/app.php`
  - _Requirements: 1.1, 1.2, 1.3, 9.1, 9.2_

- [ ] 2. Implement CheckInstallation middleware
  - [ ] 2.1 Create middleware class with installation detection logic
    - Implement `handle()` method to check for `.installed` marker file in `storage/app/`
    - If marker doesn't exist: redirect all non-installer routes to `/install`
    - If marker exists: block access to `/install/*` routes and redirect to home
    - Add exception for installer routes when marker doesn't exist
    - _Requirements: 1.1, 1.2, 1.3, 1.4_
  
  - [ ]* 2.2 Write unit tests for CheckInstallation middleware
    - Test redirect to installer when marker doesn't exist
    - Test redirect to home when marker exists and user accesses installer
    - Test normal access when marker exists and user accesses main app
    - _Requirements: 1.1, 1.2, 1.3, 1.4_

- [ ] 3. Implement EnvironmentChecker service
  - [ ] 3.1 Create EnvironmentChecker service class
    - Implement `checkPhpVersion()` method to validate PHP >= 8.3
    - Implement `checkExtensions()` method to verify required extensions (PDO, Mbstring, OpenSSL, Tokenizer, XML, Ctype, JSON, BCMath, Fileinfo)
    - Implement `checkPermissions()` method to verify write access to storage/, bootstrap/cache/, database/
    - Implement `runAllChecks()` method that returns array of check results with status and messages
    - _Requirements: 2.1, 2.2, 2.3, 2.4, 2.5, 2.6_
  
  - [ ]* 3.2 Write unit tests for EnvironmentChecker
    - Test PHP version validation (pass and fail scenarios)
    - Test extension checking with all present and some missing
    - Test permission checking with writable and non-writable directories
    - Mock `phpversion()`, `extension_loaded()`, and `is_writable()` functions
    - _Requirements: 2.1, 2.2, 2.3, 2.4, 2.6_

- [ ] 4. Implement DatabaseConfigurator service
  - [ ] 4.1 Create DatabaseConfigurator service class
    - Implement `testConnection($config)` method to test database connection using PDO
    - Implement `writeToEnv($config)` method to update .env file with DB credentials
    - Implement `generateAppKey()` method using `Str::random(32)` and base64 encoding
    - Support both SQLite and MySQL database types
    - Handle connection errors and return descriptive error messages
    - _Requirements: 3.1, 3.2, 3.3, 3.4, 3.5, 3.6, 3.7, 4.1, 4.2, 4.3, 4.4_
  
  - [ ]* 4.2 Write unit tests for DatabaseConfigurator
    - Test SQLite configuration writing to .env
    - Test MySQL configuration writing to .env
    - Test connection validation with mocked PDO
    - Test APP_KEY generation (length and format)
    - Test error handling for invalid credentials
    - _Requirements: 3.1, 3.2, 3.3, 3.4, 3.5, 3.6, 3.7, 4.1, 4.2, 4.3, 4.4_

- [ ] 5. Implement MigrationRunner service
  - [ ] 5.1 Create MigrationRunner service class
    - Implement `runMigrations()` method using `Artisan::call('migrate', ['--force' => true])`
    - Implement `rollbackOnError()` method to rollback migrations on failure
    - Add error handling with try-catch blocks
    - Return success/failure status with error messages
    - Log migration progress and errors
    - _Requirements: 5.1, 5.2, 5.3, 5.4_
  
  - [ ]* 5.2 Write unit tests for MigrationRunner
    - Test successful migration execution
    - Test rollback on migration failure
    - Test error message handling
    - Mock `Artisan::call()` to simulate success and failure
    - _Requirements: 5.1, 5.2, 5.3_

- [ ] 6. Implement AdminSeeder service
  - [ ] 6.1 Create AdminSeeder service class
    - Implement `createAdmin($name, $email, $password)` method
    - Validate email uniqueness before insertion
    - Hash password using `Hash::make()` with bcrypt cost 12
    - Create user record in users table using Eloquent
    - Return success/failure status with error messages
    - _Requirements: 6.1, 6.2, 6.3, 6.4, 6.5, 6.6, 6.7, 6.8, 12.7_
  
  - [ ]* 6.2 Write unit tests for AdminSeeder
    - Test user creation with valid data
    - Test duplicate email rejection
    - Test password hashing verification (bcrypt)
    - Mock User model for database operations
    - _Requirements: 6.1, 6.2, 6.3, 6.5, 6.6, 6.8, 12.7_

- [ ] 7. Implement CleanupService
  - [ ] 7.1 Create CleanupService class
    - Implement `deleteUnnecessaryFiles()` method
    - Delete files: BUGFIX_REPORT.md, CHANGES_SUMMARY.md, DEPLOYMENT_CHECKLIST.md, DEPLOYMENT_GUIDE.md, IMPLEMENTATION_COMPLETE.md, PROJECT_COMPLETE.md, TESTING_REPORT.md, SECURITY.md
    - Preserve README.md file
    - Implement `updateReadme()` method to add installation info
    - Handle file deletion errors gracefully (log but continue)
    - _Requirements: 13.1, 13.2, 13.3, 13.4, 13.5_
  
  - [ ]* 7.2 Write unit tests for CleanupService
    - Test file deletion with mocked filesystem
    - Test README.md preservation
    - Test error handling when file deletion fails
    - Verify correct files are targeted for deletion
    - _Requirements: 13.1, 13.2, 13.3, 13.4_

- [ ] 8. Checkpoint - Ensure all services are implemented
  - Ensure all tests pass, ask the user if questions arise.

- [ ] 9. Implement InstallerController - Part 1 (Index and Step 1)
  - [ ] 9.1 Create InstallerController with session management
    - Create controller class in `app/Http/Controllers/Installer/`
    - Implement `index()` method to redirect to current step from session
    - Initialize session keys: `installer.current_step`, `installer.step1_passed`, etc.
    - _Requirements: 1.1, 15.1, 15.2_
  
  - [ ] 9.2 Implement step1() method for environment check
    - Inject EnvironmentChecker service
    - Call `runAllChecks()` and pass results to view
    - Store validation results in session
    - Enable "Next" button only if all checks pass
    - _Requirements: 2.1, 2.2, 2.3, 2.4, 2.5, 2.6, 9.5_

- [ ] 10. Implement InstallerController - Part 2 (Step 2 Database)
  - [ ] 10.1 Implement step2() method for database configuration form
    - Display database type selection (SQLite/MySQL)
    - Show appropriate form fields based on database type
    - Restore previous data from session if exists
    - _Requirements: 3.1, 3.2, 3.3, 15.2, 15.3_
  
  - [ ] 10.2 Implement step2Store() method for database configuration
    - Validate form inputs (required fields, format validation)
    - Inject DatabaseConfigurator service
    - Test database connection using `testConnection()`
    - If connection fails: return error message and allow retry
    - If connection succeeds: write to .env using `writeToEnv()`
    - Generate APP_KEY using `generateAppKey()`
    - Run migrations using MigrationRunner service
    - Store step2 data in session
    - Update current_step to 3
    - _Requirements: 3.4, 3.5, 3.6, 3.7, 4.1, 4.2, 4.3, 4.4, 5.1, 5.2, 5.3, 5.4, 11.3, 15.1_

- [ ] 11. Implement InstallerController - Part 3 (Step 3 Admin)
  - [ ] 11.1 Implement step3() method for admin account form
    - Display form fields: name, email, password, password_confirmation
    - Restore previous data from session if exists (except password)
    - _Requirements: 6.1, 15.2, 15.3_
  
  - [ ] 11.2 Implement step3Store() method for admin account creation
    - Validate inputs: email format, password length >= 8, password confirmation match
    - Inject AdminSeeder service
    - Create admin user using `createAdmin()`
    - Handle duplicate email error
    - Store step3 data in session (without password)
    - Update current_step to 4
    - _Requirements: 6.1, 6.2, 6.3, 6.4, 6.5, 6.6, 6.7, 6.8, 12.7, 15.1_

- [ ] 12. Implement InstallerController - Part 4 (Step 4 Settings)
  - [ ] 12.1 Implement step4() method for application settings form
    - Display form fields: app_name, app_url, timezone
    - Provide timezone dropdown with PHP timezone list
    - Restore previous data from session if exists
    - _Requirements: 7.1, 7.2, 7.3, 15.2, 15.3_
  
  - [ ] 12.2 Implement step4Store() method for settings configuration
    - Validate inputs: URL format validation
    - Write APP_NAME, APP_URL, APP_TIMEZONE to .env file
    - Store step4 data in session
    - Update current_step to 5
    - _Requirements: 7.4, 7.5, 7.6, 7.7, 15.1_

- [ ] 13. Implement InstallerController - Part 5 (Step 5 Complete)
  - [ ] 13.1 Implement step5() method for installation completion
    - Create `.installed` marker file in `storage/app/`
    - Write installation metadata to marker file (JSON format): installed_at, version, php_version, database_type
    - Clear Laravel config cache using `Artisan::call('config:clear')`
    - Inject CleanupService and call `deleteUnnecessaryFiles()`
    - Clear installer session data
    - Display success message with redirect countdown
    - Redirect to login page after 3 seconds
    - _Requirements: 8.1, 8.2, 8.3, 8.4, 8.5, 8.6, 13.1, 13.2, 13.3, 13.4, 13.5_
  
  - [ ] 13.2 Implement previous() method for backward navigation
    - Decrement current_step in session
    - Redirect to previous step route
    - Preserve step data in session
    - _Requirements: 9.5, 15.3, 15.4_

- [ ] 14. Checkpoint - Ensure controller is complete
  - Ensure all tests pass, ask the user if questions arise.

- [ ]* 15. Write integration tests for InstallerController
  - Test full installation flow from step 1 to step 5
  - Test installation locking after completion
  - Test progress persistence across requests
  - Test error recovery (invalid DB credentials, retry)
  - Test backward navigation and data preservation
  - Use RefreshDatabase trait for clean test state
  - _Requirements: 1.1, 1.2, 1.3, 1.4, 8.1, 8.2, 8.3, 15.1, 15.2, 15.3, 15.4_

- [ ] 16. Create installer layout Blade template
  - [ ] 16.1 Create layout.blade.php in resources/views/installer/
    - Include HTML5 boilerplate with proper meta tags
    - Load Tailwind CSS from CDN or compiled assets
    - Load Alpine.js from CDN (version 3.x)
    - Create responsive container (max-width 600px, centered)
    - Add step indicator component (horizontal progress bar)
    - Display step numbers and titles: (1) Environment Check, (2) Database Setup, (3) Admin Account, (4) Application Settings, (5) Complete
    - Style completed steps with green checkmark, current step with blue dot, future steps with gray circle
    - Add @yield('content') for step-specific content
    - Add @stack('scripts') for step-specific JavaScript
    - _Requirements: 9.1, 9.2, 9.3, 9.4, 9.8_

- [ ] 17. Create Step 1 view (Environment Check)
  - [ ] 17.1 Create step1-environment.blade.php
    - Extend installer.layout
    - Display step title and description
    - Loop through environment check results from controller
    - Display each check with visual indicator (✓ for pass, ✗ for fail)
    - Show specific error messages for failed checks
    - Disable "Next" button if any check fails
    - Add "Next" button to proceed to step 2
    - Style with Tailwind CSS (card layout, spacing, colors)
    - _Requirements: 2.4, 2.5, 2.6, 9.3, 9.5, 9.6, 9.8_

- [ ] 18. Create Step 2 view (Database Setup)
  - [ ] 18.1 Create step2-database.blade.php
    - Extend installer.layout
    - Display step title and description
    - Add database type selection (radio buttons: SQLite, MySQL)
    - Show MySQL fields conditionally using Alpine.js (x-show)
    - Fields: host, port, database name, username, password
    - Add "Test Connection" button with Alpine.js click handler
    - Display connection test result (success/error message)
    - Add loading spinner during connection test (x-show with loading state)
    - Add CSRF token to form
    - Add "Previous" and "Next" buttons
    - Style with Tailwind CSS
    - _Requirements: 3.1, 3.2, 3.3, 3.4, 3.5, 9.3, 9.4, 9.5, 9.7, 9.8, 12.2_

- [ ] 19. Create Step 3 view (Admin Account)
  - [ ] 19.1 Create step3-admin.blade.php
    - Extend installer.layout
    - Display step title and description
    - Add form fields: name, email, password, password_confirmation
    - Add password strength indicator using Alpine.js
    - Add real-time validation for email format (x-model, @input)
    - Add real-time validation for password match (x-model, @input)
    - Display validation errors inline below fields (red text)
    - Add CSRF token to form
    - Add "Previous" and "Submit" buttons
    - Style with Tailwind CSS
    - _Requirements: 6.1, 6.2, 6.3, 6.4, 9.3, 9.4, 9.5, 9.8, 10.1, 10.2, 10.3, 10.5, 10.6, 10.7_

- [ ] 20. Create Step 4 view (Application Settings)
  - [ ] 20.1 Create step4-settings.blade.php
    - Extend installer.layout
    - Display step title and description
    - Add form fields: app_name, app_url, timezone (dropdown)
    - Populate timezone dropdown with PHP timezone list
    - Add real-time URL validation using Alpine.js
    - Display validation errors inline below fields
    - Add CSRF token to form
    - Add "Previous" and "Submit" buttons
    - Style with Tailwind CSS
    - _Requirements: 7.1, 7.2, 7.3, 7.4, 9.3, 9.4, 9.5, 9.8, 10.4, 10.5_

- [ ] 21. Create Step 5 view (Complete)
  - [ ] 21.1 Create step5-complete.blade.php
    - Extend installer.layout
    - Display success message with checkmark icon
    - Display installation summary (database type, admin email, app name)
    - Add countdown timer (3 seconds) using Alpine.js
    - Auto-redirect to login page after countdown
    - Add manual "Go to Login" button
    - Style with Tailwind CSS (success colors, centered content)
    - _Requirements: 8.4, 8.5, 9.3, 9.8_

- [ ] 22. Implement Alpine.js interactivity
  - [ ] 22.1 Add Alpine.js components for database connection test
    - Create x-data component with testing state and result
    - Implement testConnection() method using fetch API
    - Send AJAX POST request to test connection endpoint
    - Display loading spinner during request
    - Display success/error message based on response
    - _Requirements: 3.4, 9.4, 9.7_
  
  - [ ] 22.2 Add Alpine.js components for real-time validation
    - Implement email validation on input event with 300ms debounce
    - Implement password strength indicator (weak/medium/strong)
    - Implement password confirmation match validation
    - Implement URL format validation
    - Display validation errors and success indicators
    - _Requirements: 9.4, 10.1, 10.2, 10.3, 10.4, 10.5, 10.6, 10.7_
  
  - [ ] 22.3 Add Alpine.js components for step navigation
    - Implement step indicator state management
    - Implement "Previous" button handler
    - Implement form submission with loading states
    - Disable buttons during async operations
    - _Requirements: 9.4, 9.5, 9.7_

- [ ] 23. Add installer routes to web.php
  - [ ] 23.1 Create installer route group
    - Add route group with `/install` prefix
    - Apply `web` middleware (session, CSRF)
    - Add GET route for `/install` → InstallerController@index
    - Add GET route for `/install/step1` → InstallerController@step1
    - Add GET route for `/install/step2` → InstallerController@step2
    - Add POST route for `/install/step2` → InstallerController@step2Store
    - Add GET route for `/install/step3` → InstallerController@step3
    - Add POST route for `/install/step3` → InstallerController@step3Store
    - Add GET route for `/install/step4` → InstallerController@step4
    - Add POST route for `/install/step4` → InstallerController@step4Store
    - Add GET route for `/install/step5` → InstallerController@step5
    - Add POST route for `/install/previous` → InstallerController@previous
    - _Requirements: 1.1, 1.2, 9.1, 9.2_

- [ ] 24. Register CheckInstallation middleware globally
  - [ ] 24.1 Update bootstrap/app.php to register middleware
    - Add CheckInstallation to global middleware stack
    - Ensure middleware runs before route resolution
    - Test middleware execution order
    - _Requirements: 1.1, 1.2, 1.3, 1.4_

- [ ] 25. Implement security measures
  - [ ] 25.1 Add CSRF protection to all forms
    - Verify @csrf directive in all form views
    - Test CSRF token validation on form submissions
    - _Requirements: 12.2_
  
  - [ ] 25.2 Add input validation and sanitization
    - Use Laravel validation rules in all store methods
    - Sanitize user inputs before processing
    - Prevent SQL injection using Eloquent ORM
    - Prevent XSS using Blade auto-escaping
    - _Requirements: 12.1, 12.2, 12.3, 12.4_
  
  - [ ] 25.3 Add rate limiting to installer routes
    - Apply throttle middleware to installer routes (5 attempts per 15 minutes)
    - Display rate limit error message
    - _Requirements: 12.5_
  
  - [ ] 25.4 Implement secure password hashing
    - Verify bcrypt cost factor is 12 or higher in AdminSeeder
    - Test password hashing in unit tests
    - _Requirements: 12.7_
  
  - [ ] 25.5 Implement secure APP_KEY generation
    - Verify cryptographically secure random generator in DatabaseConfigurator
    - Test APP_KEY format and length
    - _Requirements: 12.6_

- [ ] 26. Checkpoint - Ensure security measures are in place
  - Ensure all tests pass, ask the user if questions arise.

- [ ] 27. Add error handling and logging
  - [ ] 27.1 Implement error handling in all controller methods
    - Wrap database operations in try-catch blocks
    - Display user-friendly error messages
    - Log errors to Laravel log file
    - _Requirements: 11.1, 11.2, 11.6_
  
  - [ ] 27.2 Add specific error messages for common failures
    - Database connection errors with specific driver messages
    - File permission errors with directory names
    - Migration errors with rollback instructions
    - _Requirements: 11.2, 11.3, 11.4_
  
  - [ ] 27.3 Add retry functionality for recoverable errors
    - Add "Retry" button for database connection failures
    - Preserve form data on error
    - _Requirements: 11.5_

- [ ] 28. Implement responsive design
  - [ ] 28.1 Test and adjust layout for mobile devices (375px - 640px)
    - Single column layout
    - Touch-friendly button sizes
    - Readable font sizes
    - _Requirements: 9.8_
  
  - [ ] 28.2 Test and adjust layout for tablet devices (641px - 1024px)
    - Single column layout with larger inputs
    - Optimized spacing
    - _Requirements: 9.8_
  
  - [ ] 28.3 Test and adjust layout for desktop devices (1025px+)
    - Centered card with max-width 600px
    - Optimal spacing and typography
    - _Requirements: 9.8_

- [ ]* 29. Write end-to-end integration tests
  - Test complete installation flow with SQLite database
  - Test complete installation flow with MySQL database
  - Test installation locking mechanism
  - Test reinstallation after deleting marker file
  - Test session persistence across page refreshes
  - Test error recovery scenarios
  - Test responsive design on different screen sizes
  - _Requirements: 1.1, 1.2, 1.3, 1.4, 8.1, 8.2, 8.3, 14.1, 14.2, 15.1, 15.2, 15.3_

- [ ] 30. Final integration and testing
  - [ ] 30.1 Test full installation flow manually
    - Start with fresh Laravel installation
    - Complete all wizard steps
    - Verify .installed marker created
    - Verify .env file updated correctly
    - Verify database tables created
    - Verify admin user exists and can login
    - Verify installer routes blocked after completion
    - _Requirements: 1.1, 1.2, 1.3, 1.4, 8.1, 8.2, 8.3, 8.4, 8.5, 8.6_
  
  - [ ] 30.2 Test error scenarios
    - Test with invalid database credentials
    - Test with missing PHP extensions (simulate)
    - Test with insufficient file permissions (simulate)
    - Test form validation errors
    - Verify error messages are clear and helpful
    - _Requirements: 11.1, 11.2, 11.3, 11.4, 11.5_
  
  - [ ] 30.3 Test security measures
    - Verify CSRF protection works
    - Verify rate limiting works
    - Verify password hashing strength
    - Verify APP_KEY security
    - _Requirements: 12.1, 12.2, 12.3, 12.4, 12.5, 12.6, 12.7_

- [ ] 31. Cleanup and documentation
  - [ ] 31.1 Verify CleanupService deletes correct files
    - Manually verify files are deleted after installation
    - Verify README.md is preserved and updated
    - _Requirements: 13.1, 13.2, 13.3, 13.4, 13.5_
  
  - [ ] 31.2 Update README.md with installation instructions
    - Add section on how to trigger installer
    - Add section on reinstallation process
    - Add troubleshooting guide
    - _Requirements: 13.3, 14.1_

- [ ] 32. Final checkpoint - Complete installation
  - Ensure all tests pass, ask the user if questions arise.

## Notes

- Tasks marked with `*` are optional testing tasks and can be skipped for faster MVP
- Each task references specific requirements for traceability
- Checkpoints ensure incremental validation at key milestones
- The implementation uses Laravel 13.7 with PHP 8.3+
- Alpine.js and Tailwind CSS are used for frontend interactivity and styling
- All forms include CSRF protection and input validation
- Security measures include rate limiting, password hashing, and secure key generation
- Error handling provides user-friendly messages with recovery options
- The installer automatically locks after completion using a marker file
