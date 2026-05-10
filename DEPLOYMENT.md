# 🚀 Panduan Deploy Wanseven ke Shared Hosting

## Langkah-Langkah Deploy

### 1️⃣ Persiapan di Komputer Lokal

```bash
# Install dependencies
composer install

# Build assets (jika ada)
npm install && npm run build
```

### 2️⃣ Upload ke Server

Upload semua file KECUALI:
- ❌ `node_modules/` (tidak perlu)
- ❌ `.git/` (tidak perlu)
- ✅ **WAJIB upload `vendor/`** (ini yang paling penting!)

**Struktur di server harus seperti ini:**
```
/home/wanj3194/public_html/
├── app/
├── bootstrap/
├── config/
├── database/
├── public/
│   ├── index.php
│   ├── install.php
│   └── check.php
├── resources/
├── routes/
├── storage/
├── vendor/          ← WAJIB ADA!
├── .env.example
├── .htaccess
├── artisan
└── composer.json
```

### 3️⃣ Set Permissions

Via File Manager atau SSH:
```bash
chmod -R 755 storage
chmod -R 755 bootstrap/cache
```

### 4️⃣ Jalankan Installer

1. Buka browser: `https://wanseven.com/check.php`
   - Cek apakah semua requirement terpenuhi
   - Pastikan vendor/ ada (✅)

2. Jika semua OK, buka: `https://wanseven.com/install.php`
   - Pilih database (SQLite recommended untuk shared hosting)
   - Isi data admin
   - Klik Install

3. Setelah instalasi selesai, jalankan via SSH (jika ada akses):
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

### 5️⃣ Selesai!

Buka `https://wanseven.com` - website sudah bisa diakses!

---

## ⚠️ Troubleshooting

### Error 500 - Internal Server Error

**Penyebab paling umum:**
1. ❌ Folder `vendor/` tidak ada
   - **Solusi:** Upload folder vendor/ dari lokal
   
2. ❌ File `.env` tidak ada
   - **Solusi:** Jalankan installer dulu

3. ❌ Permission storage/ salah
   - **Solusi:** `chmod -R 755 storage bootstrap/cache`

4. ❌ PHP version < 8.1
   - **Solusi:** Update PHP di cPanel/Hosting Panel

### Error: Too Many Redirects

**Sudah diperbaiki!** File `.htaccess` sudah dikonfigurasi dengan benar.

### Vendor Folder Terlalu Besar untuk Upload

**Opsi 1: Upload via ZIP**
```bash
# Di lokal
zip -r vendor.zip vendor/

# Upload vendor.zip ke server
# Extract via File Manager
```

**Opsi 2: Composer via SSH** (jika ada akses)
```bash
ssh user@wanseven.com
cd public_html
composer install --no-dev --optimize-autoloader
```

**Opsi 3: Upload Bertahap**
Upload folder vendor/ secara bertahap (per subfolder) via FTP.

---

## 📋 Checklist Deploy

- [ ] Upload semua file termasuk `vendor/`
- [ ] Set permission `storage/` dan `bootstrap/cache/` ke 755
- [ ] Buka `/check.php` untuk verifikasi
- [ ] Jalankan `/install.php`
- [ ] Jalankan `php artisan migrate` (via SSH atau manual)
- [ ] Test website di browser
- [ ] Login sebagai admin
- [ ] Hapus file `install.php` setelah selesai (opsional, untuk keamanan)

---

## 🆘 Butuh Bantuan?

1. Cek `/check.php` untuk diagnosis otomatis
2. Cek error log di `storage/logs/laravel.log`
3. Aktifkan debug mode sementara di `.env`:
   ```
   APP_DEBUG=true
   ```
   (Jangan lupa matikan lagi setelah selesai!)

---

**Good luck! 🎉**
