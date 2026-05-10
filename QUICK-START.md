# ⚡ Quick Start - Deploy dalam 5 Menit!

## 📦 Yang Harus Di-Upload

```
✅ WAJIB upload:
- vendor/ (folder paling penting!)
- app/
- bootstrap/
- config/
- database/
- public/
- resources/
- routes/
- storage/
- .env.example
- .htaccess
- artisan
- composer.json

❌ TIDAK perlu upload:
- node_modules/
- .git/
- tests/
```

## 🚀 Langkah Deploy

### 1. Di Komputer Lokal
```bash
composer install
npm install && npm run build
```

### 2. Upload ke Server
Upload semua file (termasuk `vendor/`) ke folder `public_html/`

### 3. Buka Browser
```
https://wanseven.com/check.php
```
Pastikan semua ✅ (terutama vendor/)

### 4. Jalankan Installer
```
https://wanseven.com/install.php
```
- Pilih SQLite (paling mudah)
- Isi data admin
- Klik Install

### 5. Selesai! 🎉
```
https://wanseven.com
```

---

## ❌ Kalau Error 500?

### Cek 1: Vendor Ada?
```
https://wanseven.com/check.php
```
Kalau vendor ❌, upload folder vendor/ dari lokal!

### Cek 2: Permission
Via File Manager atau SSH:
```bash
chmod -R 755 storage
chmod -R 755 bootstrap/cache
```

### Cek 3: .env Ada?
Jalankan installer dulu: `/install.php`

---

## 💡 Tips Upload Vendor

**Opsi 1: Upload Langsung** (Recommended)
- Upload folder `vendor/` via FTP/File Manager
- Bisa lama tapi pasti berhasil

**Opsi 2: Upload ZIP**
```bash
# Di lokal
zip -r vendor.zip vendor/

# Upload vendor.zip ke server
# Extract via File Manager
```

**Opsi 3: Auto-Install** (Bisa timeout!)
```
https://wanseven.com/composer-setup.php
```
⚠️ Hanya untuk server yang cepat!

---

## 🆘 Masih Error?

1. Buka `/check.php` - lihat apa yang ❌
2. Baca `DEPLOYMENT.md` untuk troubleshooting lengkap
3. Pastikan PHP >= 8.1

---

**Selamat mencoba! 🚀**
