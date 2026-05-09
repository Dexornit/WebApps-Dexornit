# 🚀 Panduan Deploy Laravel ke Shared Hosting

**Project**: Dexornit Store  
**Target**: Shared Hosting (cPanel/Plesk)  
**Requirements**: PHP 8.2+, MySQL, SSL Support

---

## 📋 Persiapan Sebelum Deploy

### 1. Build Assets di Local (PENTING!)

Karena shared hosting tidak support Node.js, kita harus build assets di local terlebih dahulu:

```bash
# Di local computer
cd DexornitStore
npm run build
```

**Output yang dihasilkan**:
```
public/build/
├── manifest.json
└── assets/
    ├── app-DXVlPj7m.css
    └── app-CU21OIKH.js
```

✅ **Pastikan folder `public/build/` sudah ada sebelum upload!**

---

### 2. Konfigurasi Database

Karena shared hosting menggunakan MySQL (bukan SQLite), kita perlu update konfigurasi:

**Edit file `.env`**:
```env
APP_NAME="Dexornit Store"
APP_ENV=production
APP_KEY=base64:YOUR_APP_KEY_HERE
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password

SESSION_DRIVER=file
QUEUE_CONNECTION=sync
```

**PENTING**: 
- Ganti `APP_URL` dengan domain Anda
- Ganti database credentials dengan yang dari cPanel
- Set `APP_DEBUG=false` untuk production
- Jangan lupa generate `APP_KEY` baru

---

### 3. Optimize untuk Production

```bash
# Di local, jalankan commands ini:
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 📦 Struktur Folder di Shared Hosting

Shared hosting biasanya punya struktur seperti ini:

```
/home/username/
├── public_html/          ← Document root (public folder Laravel)
├── dexornit-app/         ← Folder aplikasi Laravel (di luar public_html)
│   ├── app/
│   ├── bootstrap/
│   ├── config/
│   ├── database/
│   ├── resources/
│   ├── routes/
│   ├── storage/
│   ├── vendor/
│   └── .env
└── ...
```

**Konsep**: 
- Semua file Laravel (kecuali `public/`) diletakkan di **luar** `public_html` untuk keamanan
- Hanya isi folder `public/` yang diletakkan di `public_html`

---

## 🔧 Langkah-Langkah Deploy

### Step 1: Buat Database di cPanel

1. Login ke **cPanel**
2. Buka **MySQL Databases**
3. Buat database baru:
   - Database name: `dexornit_store`
4. Buat user baru:
   - Username: `dexornit_user`
   - Password: (generate strong password)
5. **Add user to database** dengan privilege **ALL PRIVILEGES**
6. Catat credentials ini untuk `.env`

---

### Step 2: Upload Files

#### A. Compress Project di Local

```bash
# Di local
cd DexornitStore
zip -r dexornit-store.zip . -x "node_modules/*" -x ".git/*" -x "storage/logs/*"
```

**Atau manual**:
- Compress semua file KECUALI: `node_modules/`, `.git/`, `storage/logs/*`
- Pastikan `public/build/` termasuk dalam zip!

#### B. Upload via cPanel File Manager

1. Login ke **cPanel**
2. Buka **File Manager**
3. Navigate ke `/home/username/`
4. Buat folder baru: `dexornit-app`
5. Masuk ke folder `dexornit-app`
6. Upload `dexornit-store.zip`
7. Klik kanan → **Extract**
8. Hapus file zip setelah extract

---

### Step 3: Pindahkan Public Folder

1. Di File Manager, buka folder `dexornit-app/public/`
2. **Select All** files di dalam folder `public/`
3. **Move** semua files ke `/home/username/public_html/`
4. Confirm move

**Hasil**:
```
public_html/
├── index.php
├── .htaccess
├── assets/
├── build/
└── ...
```

---

### Step 4: Edit index.php

Edit file `/home/username/public_html/index.php`:

**Cari baris ini** (sekitar baris 17-18):
```php
require __DIR__.'/../vendor/autoload.php';
```

**Ganti menjadi**:
```php
require __DIR__.'/../dexornit-app/vendor/autoload.php';
```

**Cari baris ini** (sekitar baris 31):
```php
$app = require_once __DIR__.'/../bootstrap/app.php';
```

**Ganti menjadi**:
```php
$app = require_once __DIR__.'/../dexornit-app/bootstrap/app.php';
```

**Save file!**

---

### Step 5: Konfigurasi .env

1. Buka `/home/username/dexornit-app/.env`
2. Edit konfigurasi:

```env
APP_NAME="Dexornit Store"
APP_ENV=production
APP_KEY=base64:GENERATE_NEW_KEY_HERE
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=dexornit_store
DB_USERNAME=dexornit_user
DB_PASSWORD=your_password_here

SESSION_DRIVER=file
QUEUE_CONNECTION=sync

FILESYSTEM_DISK=public
```

**Generate APP_KEY baru**:
- Via SSH: `php artisan key:generate`
- Atau manual: Generate random base64 string

---

### Step 6: Set Permissions

Via **File Manager** atau **SSH**, set permissions:

```bash
# Via SSH
cd /home/username/dexornit-app

# Set permissions
chmod -R 755 storage
chmod -R 755 bootstrap/cache
chmod 644 .env
```

**Via File Manager**:
- Klik kanan folder `storage` → **Change Permissions** → `755`
- Klik kanan folder `bootstrap/cache` → **Change Permissions** → `755`
- Klik kanan file `.env` → **Change Permissions** → `644`

---

### Step 7: Install Composer Dependencies

**Opsi A: Via SSH (Recommended)**
```bash
cd /home/username/dexornit-app
composer install --optimize-autoloader --no-dev
```

**Opsi B: Upload vendor/ dari Local**
- Jika tidak ada SSH access
- Compress folder `vendor/` di local
- Upload dan extract ke `/home/username/dexornit-app/vendor/`

---

### Step 8: Run Migrations

**Via SSH**:
```bash
cd /home/username/dexornit-app
php artisan migrate --force
php artisan db:seed --force
```

**Via PHP Script** (jika tidak ada SSH):

Buat file `migrate.php` di `public_html/`:
```php
<?php
require __DIR__.'/../dexornit-app/vendor/autoload.php';
$app = require_once __DIR__.'/../dexornit-app/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->call('migrate', ['--force' => true]);
$kernel->call('db:seed', ['--force' => true]);

echo "Migration completed!";
```

Akses: `https://yourdomain.com/migrate.php`

**HAPUS file ini setelah selesai!**

---

### Step 9: Create Storage Link

**Via SSH**:
```bash
cd /home/username/dexornit-app
php artisan storage:link
```

**Via PHP Script** (jika tidak ada SSH):

Buat file `storage-link.php` di `public_html/`:
```php
<?php
$target = '/home/username/dexornit-app/storage/app/public';
$link = '/home/username/public_html/storage';

if (file_exists($link)) {
    unlink($link);
}

symlink($target, $link);
echo "Storage link created!";
```

Akses: `https://yourdomain.com/storage-link.php`

**HAPUS file ini setelah selesai!**

---

### Step 10: Optimize Application

**Via SSH**:
```bash
cd /home/username/dexornit-app
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

**Via PHP Script**:

Buat file `optimize.php` di `public_html/`:
```php
<?php
require __DIR__.'/../dexornit-app/vendor/autoload.php';
$app = require_once __DIR__.'/../dexornit-app/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->call('config:cache');
$kernel->call('route:cache');
$kernel->call('view:cache');

echo "Optimization completed!";
```

Akses: `https://yourdomain.com/optimize.php`

**HAPUS file ini setelah selesai!**

---

## 🔒 Konfigurasi SSL (HTTPS)

### Via cPanel (Let's Encrypt)

1. Login ke **cPanel**
2. Buka **SSL/TLS Status**
3. Pilih domain Anda
4. Klik **Run AutoSSL**
5. Tunggu proses selesai

### Force HTTPS

Edit file `public_html/.htaccess`, tambahkan di atas:

```apache
# Force HTTPS
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

---

## ✅ Verifikasi Deployment

### 1. Test Landing Page
- Akses: `https://yourdomain.com`
- ✅ Landing page muncul dengan benar
- ✅ Images dan CSS loading
- ✅ Product cards muncul

### 2. Test Admin Login
- Akses: `https://yourdomain.com/login`
- Login: `admin@dexornit.store` / `password`
- ✅ Login berhasil
- ✅ Redirect ke dashboard

### 3. Test CRUD Operations
- ✅ Create product
- ✅ Upload images
- ✅ Edit product
- ✅ Delete product

---

## 🐛 Troubleshooting

### Error: "500 Internal Server Error"

**Solusi**:
1. Check file permissions:
   ```bash
   chmod -R 755 storage
   chmod -R 755 bootstrap/cache
   ```

2. Check `.env` file exists dan readable:
   ```bash
   chmod 644 .env
   ```

3. Clear cache:
   ```bash
   php artisan cache:clear
   php artisan config:clear
   php artisan view:clear
   ```

4. Check error logs di cPanel → **Error Log**

---

### Error: "No application encryption key"

**Solusi**:
```bash
php artisan key:generate
```

Atau edit `.env` manual dan tambahkan:
```env
APP_KEY=base64:RANDOM_32_CHARACTER_STRING_HERE
```

---

### Error: "Class not found"

**Solusi**:
```bash
composer dump-autoload
php artisan optimize:clear
```

---

### Images tidak muncul

**Solusi**:
1. Pastikan storage link sudah dibuat
2. Check permissions folder `storage/`:
   ```bash
   chmod -R 755 storage
   ```
3. Verify symlink:
   ```bash
   ls -la public_html/storage
   ```

---

### CSS/JS tidak loading

**Solusi**:
1. Pastikan folder `public/build/` sudah di-upload
2. Check file `public/build/manifest.json` exists
3. Clear browser cache
4. Check `.env`:
   ```env
   APP_URL=https://yourdomain.com
   ```

---

## 📝 Maintenance Mode

### Enable Maintenance Mode
```bash
php artisan down --secret="your-secret-token"
```

Akses dengan: `https://yourdomain.com/your-secret-token`

### Disable Maintenance Mode
```bash
php artisan up
```

---

## 🔄 Update Application

Ketika ada update code:

1. **Build assets di local**:
   ```bash
   npm run build
   ```

2. **Upload files yang berubah**

3. **Clear cache**:
   ```bash
   php artisan cache:clear
   php artisan config:clear
   php artisan view:clear
   php artisan route:clear
   ```

4. **Re-optimize**:
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

---

## 📊 Performance Tips

### 1. Enable OPcache
Di cPanel → **Select PHP Version** → **Options**:
- Enable `opcache`

### 2. Use Redis (jika tersedia)
Edit `.env`:
```env
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
```

### 3. Optimize Images
- Compress images sebelum upload
- Use WebP format
- Enable lazy loading

---

## 🔐 Security Checklist

- ✅ `APP_DEBUG=false` di production
- ✅ `APP_ENV=production`
- ✅ Strong `APP_KEY`
- ✅ Strong database password
- ✅ SSL/HTTPS enabled
- ✅ `.env` file tidak accessible dari web
- ✅ `storage/` dan `bootstrap/cache/` writable
- ✅ Semua file Laravel di luar `public_html`
- ✅ Disable directory listing
- ✅ Regular backups

---

## 📞 Support

Jika ada masalah:
1. Check error logs di cPanel
2. Check Laravel logs: `storage/logs/laravel.log`
3. Contact hosting support untuk PHP/MySQL issues

---

## ✅ Deployment Checklist

Sebelum go-live:

- [ ] Build assets (`npm run build`)
- [ ] Upload semua files
- [ ] Pindahkan public folder
- [ ] Edit `index.php`
- [ ] Konfigurasi `.env`
- [ ] Set permissions
- [ ] Install composer dependencies
- [ ] Run migrations
- [ ] Create storage link
- [ ] Optimize application
- [ ] Enable SSL
- [ ] Test landing page
- [ ] Test admin login
- [ ] Test CRUD operations
- [ ] Test image uploads
- [ ] Check error logs
- [ ] Setup backups

---

**🎉 Selamat! Aplikasi Anda sudah live di shared hosting!**

**URL**: https://yourdomain.com  
**Admin**: https://yourdomain.com/admin/dashboard
