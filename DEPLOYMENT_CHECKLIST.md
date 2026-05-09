# ✅ Deployment Checklist - Dexornit Store

**Quick Reference untuk Deploy ke Shared Hosting**

---

## 📋 Pre-Deployment (Di Local)

### 1. Build Assets
```bash
cd DexornitStore
npm run build
```
✅ Pastikan folder `public/build/` ada

### 2. Test di Local
```bash
php artisan serve
```
✅ Aplikasi berjalan tanpa error

### 3. Compress Project
```bash
zip -r dexornit-store.zip . -x "node_modules/*" -x ".git/*"
```
✅ File zip siap upload

---

## 🗄️ Setup Database (cPanel)

1. ✅ Login ke cPanel
2. ✅ Buka **MySQL Databases**
3. ✅ Buat database: `dexornit_store`
4. ✅ Buat user: `dexornit_user`
5. ✅ Set password (strong password)
6. ✅ Add user to database (ALL PRIVILEGES)
7. ✅ Catat credentials

---

## 📤 Upload Files

1. ✅ Login cPanel → File Manager
2. ✅ Buat folder: `/home/username/dexornit-app`
3. ✅ Upload `dexornit-store.zip` ke folder tersebut
4. ✅ Extract zip file
5. ✅ Hapus zip file

---

## 📁 Setup Public Folder

1. ✅ Buka `/home/username/dexornit-app/public/`
2. ✅ Select All files
3. ✅ Move ke `/home/username/public_html/`
4. ✅ Confirm move

---

## ⚙️ Edit index.php

Edit `/home/username/public_html/index.php`:

**Baris 17-18**, ganti:
```php
require __DIR__.'/../vendor/autoload.php';
```
Menjadi:
```php
require __DIR__.'/../dexornit-app/vendor/autoload.php';
```

**Baris 31**, ganti:
```php
$app = require_once __DIR__.'/../bootstrap/app.php';
```
Menjadi:
```php
$app = require_once __DIR__.'/../dexornit-app/bootstrap/app.php';
```

✅ Save file

---

## 🔧 Konfigurasi .env

Edit `/home/username/dexornit-app/.env`:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_DATABASE=dexornit_store
DB_USERNAME=dexornit_user
DB_PASSWORD=your_password_here
```

✅ Save file

---

## 🔐 Set Permissions

Via File Manager:
- ✅ `storage/` → 755
- ✅ `bootstrap/cache/` → 755
- ✅ `.env` → 644

---

## 🚀 Deploy Helper (Recommended)

1. ✅ Upload `deploy-helper.php` ke `public_html/`
2. ✅ Akses: `https://yourdomain.com/deploy-helper.php`
3. ✅ Klik tombol sesuai urutan:
   - System Check
   - Run Migrations
   - Run Seeders
   - Create Storage Link
   - Optimize Application
4. ✅ **HAPUS** `deploy-helper.php` setelah selesai

---

## 🔒 Enable SSL

1. ✅ cPanel → SSL/TLS Status
2. ✅ Run AutoSSL untuk domain
3. ✅ Tunggu proses selesai
4. ✅ Test: `https://yourdomain.com`

---

## ✅ Verifikasi

### Landing Page
- ✅ Akses: `https://yourdomain.com`
- ✅ Design muncul dengan benar
- ✅ Images loading
- ✅ Product cards muncul
- ✅ Filter berfungsi

### Admin Panel
- ✅ Akses: `https://yourdomain.com/login`
- ✅ Login: `admin@dexornit.store` / `password`
- ✅ Dashboard muncul
- ✅ Statistics benar
- ✅ Products list muncul

### CRUD Operations
- ✅ Create product
- ✅ Upload logo
- ✅ Upload images
- ✅ Add variants
- ✅ Edit product
- ✅ Delete product
- ✅ Restore product
- ✅ Toggle status

### Categories
- ✅ Create category
- ✅ Edit category
- ✅ Category muncul di landing page

---

## 🐛 Common Issues

### 500 Error
```bash
chmod -R 755 storage
chmod -R 755 bootstrap/cache
```

### Images tidak muncul
```bash
# Buat storage link via deploy-helper.php
# Atau manual via SSH:
php artisan storage:link
```

### CSS/JS tidak loading
- ✅ Check folder `public/build/` exists
- ✅ Clear browser cache
- ✅ Check `.env` APP_URL

---

## 📞 Need Help?

1. Check error logs: cPanel → Error Log
2. Check Laravel logs: `storage/logs/laravel.log`
3. Re-run deploy-helper.php
4. Contact hosting support

---

## 🎉 Deployment Complete!

**Landing Page**: https://yourdomain.com  
**Admin Panel**: https://yourdomain.com/admin/dashboard  
**Login**: admin@dexornit.store / password

**Next Steps**:
1. ✅ Change admin password
2. ✅ Add real products
3. ✅ Update contact information
4. ✅ Setup regular backups
5. ✅ Monitor error logs

---

**Status**: 🚀 LIVE IN PRODUCTION!
