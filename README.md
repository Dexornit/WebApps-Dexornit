# Wanseven - Laravel E-commerce Application

A modern e-commerce application built with Laravel 13.7, featuring an intuitive admin panel for managing products and categories.

## Features

- 🛍️ Product catalog with categories
- 🖼️ Product image management
- 📦 Product variants support
- 👨‍💼 Admin dashboard
- 🔐 User authentication (Laravel Breeze)
- 📱 Responsive design with Tailwind CSS
- 🚀 Easy web-based installation

## Requirements

- PHP >= 8.3
- Composer
- Node.js & NPM
- MySQL or SQLite database
- Required PHP extensions: PDO, Mbstring, OpenSSL, Tokenizer, XML, Ctype, JSON, BCMath, Fileinfo

## Installation

### Quick Installation (Web Installer)

1. Clone the repository and install dependencies:
```bash
git clone <repository-url>
cd wanseven
composer install
npm install && npm run build
```

2. Set proper permissions:
```bash
chmod -R 775 storage bootstrap/cache
```

3. Access your application URL in a browser (e.g., `http://localhost:8000` or `http://wanseven.com`)

4. You will be automatically redirected to the web installer at `/install`

5. Follow the installation wizard:
   - Environment requirements will be checked automatically
   - Configure your database (SQLite or MySQL)
   - Create your admin account
   - Set application settings

6. After installation completes, you'll be redirected to the login page

7. Login with your admin credentials and start managing your store!

### Manual Installation (Alternative)

If you prefer manual installation:

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Configure database in .env file
# Then run migrations
php artisan migrate

# Create admin user
php artisan tinker
>>> User::create(['name' => 'Admin', 'email' => 'admin@example.com', 'password' => Hash::make('password')]);

# Create installation marker to skip installer
mkdir -p storage/app
echo '{"installed_at":"'$(date)'","version":"1.0.0"}' > storage/app/.installed
```

## Development

Start the development server:

```bash
php artisan serve
npm run dev
```

Access the application at `http://localhost:8000`

## Admin Panel

Access the admin panel at `/admin/dashboard` after logging in.

Features:
- Product management (CRUD operations)
- Category management
- Product image uploads
- Product variants
- Dashboard statistics

## Reinstallation

To reinstall the application:

1. Delete the installation marker:
```bash
rm storage/app/.installed
```

2. (Optional) Reset the database:
```bash
php artisan migrate:fresh
```

3. Access your application URL - you'll be redirected to the installer

## Security

- All passwords are hashed using bcrypt
- CSRF protection on all forms
- SQL injection prevention via Eloquent ORM
- XSS protection via Blade templating

## License

This application is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
