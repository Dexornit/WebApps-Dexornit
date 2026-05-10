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

### 3. Jalankan Installer
```
https://wanseven.com/install.php
```

Installer akan memandu kamu melalui 5 langkah:
1. ✅ Cek Requirements
2. 🗄️ Setup Database (pilih SQLite - paling mudah)
3. 👤 Buat Admin Account
4. ⚙️ Instalasi Otomatis (termasuk migrasi!)
5. 🎉 Selesai!

### 4. Selesai! 🎉
```
https://wanseven.com
```

---

## ❌ Kalau Error 500?

### Cek 1: Vendor Ada?
Pastikan folder `vendor/` sudah di-upload ke server!

### Cek 2: Permission
Via File Manager atau SSH:
```bash
chmod -R 755 storage
chmod -R 755 bootstrap/cache
```

### Cek 3: Jalankan Installer
Buka `/install.php` dan ikuti langkah-langkahnya.

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

**Opsi 3: Auto-Install** (Tidak tersedia - exec() disabled di shared hosting)
Gunakan opsi 1 atau 2 saja.

---

## 🆘 Masih Error?

1. Pastikan folder `vendor/` sudah di-upload
2. Pastikan permission `storage/` dan `bootstrap/cache/` = 755
3. Baca `DEPLOYMENT.md` untuk troubleshooting lengkap

---

**Selamat mencoba! 🚀**
