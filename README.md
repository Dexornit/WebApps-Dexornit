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

### 🚀 Quick Deploy to Shared Hosting

**Step 1: Prepare Locally**
```bash
composer install
npm install && npm run build
```

**Step 2: Upload to Server**
Upload ALL files including the `vendor/` folder to your hosting.

⚠️ **IMPORTANT:** Make sure `vendor/` folder is uploaded! This is the most common cause of errors.

**Step 3: Check Status**
Visit: `https://yourdomain.com/check.php` to verify all requirements are met.

**Step 4: Run Installer**
Visit: `https://yourdomain.com/install.php` and follow the 3-step wizard:
- Configure database (SQLite recommended for shared hosting)
- Create admin account
- Complete installation

**Step 5: Done!**
Your website is ready at `https://yourdomain.com`

📖 **Need help?** See [DEPLOYMENT.md](DEPLOYMENT.md) for detailed guide and troubleshooting.

---

### 🛠️ Local Development Installation

### 🛠️ Local Development Installation

For local development:

```bash
# Clone and install
git clone <repository-url>
cd wanseven
composer install
npm install && npm run build
# Set permissions
chmod -R 775 storage bootstrap/cache

# Copy environment and generate key
cp .env.example .env
php artisan key:generate

# Setup database
php artisan migrate
php artisan db:seed

# Start dev server
php artisan serve
npm run dev
```

Access at `http://localhost:8000` - installer will run automatically.

---

### ⚙️ Manual Installation (Alternative)

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
