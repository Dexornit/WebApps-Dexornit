# 🚀 Panduan Deploy Dexornit Store — RumahWeb Shared Hosting

> **Last updated:** 2026-05-10  
> **Stack:** Laravel 12 + MySQL + PHP 8.1/8.2

---

## ⚡ Perubahan Penting (Dari Versi Sebelumnya)

| Bug Lama | Status | Keterangan |
|----------|--------|------------|
| Root `.htaccess` infinite redirect loop | ✅ Fixed | Kondisi `!-f !-d` + flag `[QSA]` ditambahkan |
| Dua installer konflik (`install.php` + `/install` route) | ✅ Fixed | `public/install.php` dihapus, pakai Laravel route saja |
| `public/index.php` redirect ke path yang salah | ✅ Fixed | Disederhanakan, middleware yang handle redirect |
| `DB_CONNECTION=sqlite` di shared hosting | ✅ Fixed | Diganti ke `mysql` |
| Middleware panggil `Artisan::call('key:generate')` tanpa APP_KEY | ✅ Fixed | Dihapus dari middleware |
| `composer.json` require PHP ^8.3 | ✅ Fixed | Diturunkan ke ^8.1 |
| Bootstrap cache stale | ✅ Fixed | Semua file cache dihapus |

---

## 📋 Prasyarat Sebelum Upload

- [ ] Folder `vendor/` ada (hasil `composer install --no-dev` di lokal)
- [ ] Folder `public/build/` ada (hasil `npm run build` di lokal)
- [ ] Database MySQL sudah dibuat di cPanel

---

## 📁 File yang Diupload ke Server

Upload **semua ini** ke `public_html/` (document root):

```
public_html/
├── .htaccess                  ← root htaccess (WAJIB)
├── .env                       ← isi DB credentials dulu!
├── app/
├── bootstrap/
│   ├── app.php
│   ├── cache/                 ← upload KOSONG (jangan ada .php di dalamnya)
│   └── providers.php
├── config/
├── database/
│   └── migrations/
├── public/                    ← upload ISINYA, bukan foldernya!
│   ├── .htaccess              ← public htaccess (WAJIB)
│   ├── index.php              ← entry point Laravel (WAJIB)
│   ├── assets/
│   └── build/
├── resources/
├── routes/
├── storage/                   ← harus writable (chmod 755)
└── vendor/                    ← WAJIB ADA
```

> ❌ **JANGAN upload:** `node_modules/`, `node_modules.zip`, `.git/`, `.kiro/`

---

## 🛠️ Langkah Deploy Step-by-Step

### Step 1 — Buat Database di cPanel

1. Login **cPanel → MySQL Databases**
2. Buat database baru (contoh: `wanseven_db`)
3. Buat user MySQL baru
4. Assign user ke database dengan **ALL PRIVILEGES**
5. Catat: hostname, nama DB, username, password

### Step 2 — Edit `.env` Sebelum Upload

```env
APP_NAME="Dexornit Store"
APP_ENV=production
APP_KEY=base64:aJM7kVSKKMbs6HwS1cJJbYifHn+8CAyhpDc4DmGUSOo=
APP_DEBUG=false
APP_URL=http://wanseven.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=nama_database_kamu
DB_USERNAME=user_database_kamu
DB_PASSWORD=password_database_kamu
```

> ⚠️ Ganti `nama_database_kamu`, `user_database_kamu`, `password_database_kamu` sesuai Step 1.

### Step 3 — Upload via cPanel File Manager

1. Compress project jadi `.zip` (kecuali yang dilarang di atas)
2. Upload ke `public_html/` via File Manager
3. Extract di sana

### Step 4 — Set File Permissions

Di cPanel → File Manager, klik kanan folder → **Change Permissions:**

| Path | Permission |
|------|------------|
| `storage/` (recursive) | `755` |
| `bootstrap/cache/` | `755` |
| `.env` | `644` |

### Step 5 — Set PHP Version di cPanel

1. cPanel → **Select PHP Version**
2. Pilih **PHP 8.1** atau **PHP 8.2**
3. Pastikan extension berikut **aktif (centang):**
   - `pdo_mysql`
   - `mbstring`
   - `openssl`
   - `fileinfo`
   - `xml`
   - `bcmath`
   - `ctype`
   - `json`

### Step 6 — Jalankan Installer

Buka browser: **`http://wanseven.com/install`**

Ikuti wizard 4 langkah:
1. ✅ **Check** — verifikasi requirements
2. 🗄️ **Database** — masukkan credentials MySQL dari Step 1
3. 👤 **Admin** — buat akun admin
4. 🚀 **Install** — proses migrasi & setup otomatis

### Step 7 — Selesai!

Setelah installer berhasil:
- Website: `http://wanseven.com`
- Admin login: `http://wanseven.com/login`
- Admin panel: `http://wanseven.com/admin/dashboard`

---

## ⚠️ Troubleshooting

### Lihat Error Log

```
cPanel → Error Logs
```
atau buka file: `storage/logs/laravel.log`

### Aktifkan Debug Sementara

Edit `.env` di server:
```env
APP_DEBUG=true
```
Reload halaman → error detail akan muncul. **Matikan lagi setelah selesai debug!**

### Tabel Masalah Umum

| Error | Penyebab | Solusi |
|-------|----------|--------|
| "Vendor folder missing" | `vendor/` tidak terupload | Upload folder `vendor/` dari lokal |
| `SQLSTATE[HY000]` | Credentials DB salah | Cek `.env` DB_HOST/USER/PASS |
| "Permission denied" | Permission storage salah | chmod 755 storage/ dan bootstrap/cache/ |
| "No application encryption key" | APP_KEY kosong | Generate: `php artisan key:generate --show` di lokal, copy ke `.env` |
| Blank white page (no error) | Error tersembunyi | Set `APP_DEBUG=true` sementara |
| 404 on all routes | `.htaccess` tidak terupload | Pastikan kedua `.htaccess` ada di root dan di `public/` |
| Installer redirect loop | `.installed` marker ada tapi DB kosong | Hapus `storage/app/.installed`, buka `/install` lagi |

---

## 🔐 Keamanan Post-Deploy

- [ ] `APP_DEBUG=false` di `.env`
- [ ] `APP_ENV=production` di `.env`
- [ ] File `storage/app/.installed` sudah ada
- [ ] Route `/install` otomatis diblock setelah terinstall

---

## 📌 Info RumahWeb Small Plan

| Fitur | Ketersediaan |
|-------|-------------|
| PHP 8.1/8.2 | ✅ Tersedia (pilih di cPanel) |
| MySQL | ✅ Tersedia (buat via cPanel) |
| SQLite | ⚠️ Tidak disarankan |
| SSH / Terminal | ❌ Tidak tersedia di Small plan |
| Composer di server | ❌ Tidak tersedia → upload `vendor/` dari lokal |
| Node.js / npm | ❌ Tidak tersedia → build `public/build/` dari lokal |
