# Security Implementation - Dexornit Store

## 🔒 Implementasi Keamanan

Dokumen ini menjelaskan semua langkah keamanan yang telah diimplementasikan untuk melindungi aplikasi dari serangan dan pembobolan.

---

## 1. **Authentication & Authorization**

### ✅ Laravel Breeze (Built-in Security)
- **Password Hashing**: Menggunakan bcrypt (cost factor 10) untuk hash password
- **Session Management**: Session token yang aman dengan regenerasi otomatis
- **CSRF Protection**: Token CSRF pada semua form untuk mencegah Cross-Site Request Forgery
- **Rate Limiting**: Pembatasan login attempts (5 attempts per menit)

### ✅ Middleware Protection
```php
// Semua route admin dilindungi dengan auth middleware
Route::middleware('auth')->prefix('admin')->group(function () {
    // Admin routes here
});
```

---

## 2. **Database Security**

### ✅ SQL Injection Prevention
- **Eloquent ORM**: Semua query menggunakan Eloquent ORM dengan prepared statements
- **Parameter Binding**: Tidak ada raw SQL query tanpa parameter binding
- **Input Sanitization**: Laravel otomatis escape input

### ✅ Mass Assignment Protection
```php
// Model Product
protected $fillable = ['name', 'logo_path', 'category_id', ...];
// Hanya field yang ada di $fillable yang bisa di-mass assign
```

### ✅ Soft Deletes
- Data tidak dihapus permanen, hanya di-mark sebagai deleted
- Dapat di-restore jika terjadi kesalahan

### ⚠️ Database Encryption (Optional - untuk data sensitif)
```php
// Jika ada data sensitif (credit card, password, dll), gunakan encryption
use Illuminate\Support\Facades\Crypt;

// Encrypt
$encrypted = Crypt::encryptString('sensitive data');

// Decrypt
$decrypted = Crypt::decryptString($encrypted);
```

**Note**: Untuk aplikasi ini, data produk tidak perlu dienkripsi karena bersifat publik. Enkripsi hanya diperlukan untuk data sensitif seperti:
- Password (sudah di-hash dengan bcrypt)
- Data pembayaran (jika ada)
- Data pribadi pelanggan (jika ada)

---

## 3. **File Upload Security**

### ✅ Validation
```php
'images.*' => 'image|mimes:jpeg,png,webp|max:2048', // Max 2MB
'logo' => 'image|mimes:jpeg,png,webp,svg|max:1024', // Max 1MB
```

### ✅ File Storage
- File disimpan di `storage/app/public/` (tidak di public root)
- Akses file melalui symlink `/storage/` yang aman
- Nama file di-sanitize dengan timestamp untuk mencegah collision

### ✅ File Type Validation
- Hanya menerima image dengan MIME type yang valid
- Laravel memvalidasi MIME type, bukan hanya extension

### ✅ Path Traversal Prevention
```php
// Laravel Storage otomatis mencegah path traversal
Storage::disk('public')->put($path, $file);
```

---

## 4. **XSS (Cross-Site Scripting) Prevention**

### ✅ Blade Template Engine
```blade
{{-- Otomatis escape output --}}
{{ $product->name }}

{{-- Jika perlu raw HTML (hati-hati!) --}}
{!! $trustedHtml !!}
```

### ✅ Input Sanitization
- Semua input dari user di-escape sebelum disimpan
- Blade template otomatis escape output

---

## 5. **CSRF (Cross-Site Request Forgery) Protection**

### ✅ CSRF Token
```blade
<form method="POST">
    @csrf
    <!-- Form fields -->
</form>
```
- Semua form POST/PUT/DELETE wajib memiliki @csrf token
- Laravel otomatis validasi token pada setiap request

---

## 6. **Input Validation**

### ✅ Server-Side Validation
```php
$validated = $request->validate([
    'name' => 'required|string|max:255',
    'category_id' => 'required|exists:categories,id',
    'short_description' => 'required|string|max:500',
    // ... validation rules
]);
```

### ✅ Sanitization
- `string`: Memastikan input adalah string
- `max:255`: Membatasi panjang input
- `exists:categories,id`: Memastikan foreign key valid
- `numeric|min:0`: Validasi angka positif

---

## 7. **Session Security**

### ✅ Configuration (config/session.php)
```php
'secure' => env('SESSION_SECURE_COOKIE', true), // HTTPS only
'http_only' => true, // Tidak bisa diakses via JavaScript
'same_site' => 'lax', // CSRF protection
```

---

## 8. **Environment Variables**

### ✅ .env File
```env
APP_KEY=base64:... # Encryption key (JANGAN SHARE!)
DB_PASSWORD=... # Database password (JANGAN SHARE!)
```

### ✅ .gitignore
```
.env
.env.backup
```
- File `.env` tidak di-commit ke Git
- Setiap environment punya `.env` sendiri

---

## 9. **Error Handling**

### ✅ Production Mode
```env
APP_DEBUG=false # Jangan tampilkan error details di production
APP_ENV=production
```

### ✅ Custom Error Pages
- 404 Page Not Found
- 500 Internal Server Error
- Tidak menampilkan stack trace ke user

---

## 10. **Rate Limiting**

### ✅ Login Throttling
```php
// LoginRequest.php
public function authenticate()
{
    $this->ensureIsNotRateLimited(); // Max 5 attempts per minute
    // ...
}
```

### ✅ API Rate Limiting (jika ada API)
```php
Route::middleware('throttle:60,1')->group(function () {
    // Max 60 requests per minute
});
```

---

## 11. **HTTPS & SSL**

### ⚠️ Production Checklist
- [ ] Install SSL Certificate (Let's Encrypt gratis)
- [ ] Force HTTPS di `.env`:
```env
APP_URL=https://yourdomain.com
SESSION_SECURE_COOKIE=true
```
- [ ] Redirect HTTP ke HTTPS di `.htaccess` atau Nginx config

---

## 12. **Database Backup**

### ⚠️ Backup Strategy
```bash
# Backup database setiap hari
php artisan backup:run

# Atau manual
mysqldump -u username -p database_name > backup.sql
```

---

## 13. **Dependency Security**

### ✅ Keep Dependencies Updated
```bash
# Check for security vulnerabilities
composer audit

# Update dependencies
composer update
```

---

## 14. **Additional Security Headers**

### ⚠️ Add to .htaccess or Nginx
```apache
# X-Frame-Options (prevent clickjacking)
Header always set X-Frame-Options "SAMEORIGIN"

# X-Content-Type-Options (prevent MIME sniffing)
Header always set X-Content-Type-Options "nosniff"

# X-XSS-Protection
Header always set X-XSS-Protection "1; mode=block"

# Content-Security-Policy
Header always set Content-Security-Policy "default-src 'self'"
```

---

## 15. **Logging & Monitoring**

### ✅ Laravel Logging
```php
// Log suspicious activities
Log::warning('Failed login attempt', ['email' => $email, 'ip' => $ip]);
```

### ⚠️ Monitor Logs
- Check `storage/logs/laravel.log` regularly
- Setup alerts for critical errors

---

## 🎯 Security Checklist untuk Production

- [ ] `APP_DEBUG=false` di `.env`
- [ ] `APP_ENV=production` di `.env`
- [ ] Install SSL Certificate
- [ ] Force HTTPS
- [ ] Setup database backup otomatis
- [ ] Setup monitoring & alerts
- [ ] Review file permissions (755 untuk folder, 644 untuk file)
- [ ] Disable directory listing
- [ ] Setup firewall (UFW/iptables)
- [ ] Regular security updates (`composer update`)
- [ ] Setup fail2ban untuk brute force protection

---

## 📚 Resources

- [Laravel Security Best Practices](https://laravel.com/docs/security)
- [OWASP Top 10](https://owasp.org/www-project-top-ten/)
- [PHP Security Cheat Sheet](https://cheatsheetseries.owasp.org/cheatsheets/PHP_Configuration_Cheat_Sheet.html)

---

## 🔐 Kesimpulan

**Keamanan yang sudah diimplementasikan:**
1. ✅ Password hashing (bcrypt)
2. ✅ CSRF protection
3. ✅ SQL injection prevention (Eloquent ORM)
4. ✅ XSS prevention (Blade escaping)
5. ✅ File upload validation
6. ✅ Input validation & sanitization
7. ✅ Session security
8. ✅ Rate limiting
9. ✅ Mass assignment protection
10. ✅ Soft deletes

**Enkripsi database TIDAK diperlukan** untuk data produk karena bersifat publik. Enkripsi hanya diperlukan untuk data sensitif seperti password (sudah di-hash), data pembayaran, atau data pribadi pelanggan.

**Untuk production**, pastikan semua checklist di atas sudah dilakukan!
