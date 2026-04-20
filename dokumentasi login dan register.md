<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Dokumentasi Autentikasi (Login & Register)

Dokumentasi ini menjelaskan sistem autentikasi yang telah dirombak dengan desain premium, form register customer, dan responsive mobile.

### Fitur Utama yang Sudah Diimplementasikan

**Redesign Halaman Login:**
- Layout split premium dengan visual branding di sisi kiri (desktop).
- Warna dan tipografi: Montserrat untuk heading, Poppins untuk body.
- Color palette: primary #6C8CF5, soft section #EEF4FF, aksen dan background premium.
- Animasi fade-up saat page load.
- Ornamental background dengan gradient dan grid pattern.
- Ilustrasi kendaraan sederhana di halaman login (desktop).
- Responsive design: menyembunyikan visual saat tablet/mobile.
- Toggle password untuk UX lebih baik.
- Checkbox "Ingat saya" untuk convenience.

**Form Register Customer Baru:**
- Halaman register di /register hanya untuk customer.
- Field: nama lengkap, email, nomor HP, password, konfirmasi password, setuju terms.
- Validasi Laravel dengan pesan custom.
- Email harus unik (email sudah terdaftar akan ditolak).
- Password minimal 8 karakter, harus confirmed.
- Checkbox syarat dan ketentuan (wajib disetujui).
- Setelah register sukses, user diarahkan ke login dengan flash message sukses.

**Alur Autentikasi Multi-Role:**
- Login endpoint tunggal untuk semua akun: customer, admin_rental, super_admin.
- Setelah login berhasil:
	- `super_admin` → redirect ke `/super-admin/dashboard`
	- `admin_rental` → redirect ke `/admin-rental/dashboard`
	- `customer` → redirect ke `/home` (halaman customer)
- Jika role tidak valid, user otomatis logout dengan error message.

**Mobile Responsive:**
- Layout vertikal di tablet dan mobile (max-width: 768px).
- Visual branding disederhanakan dan card tetap rapi.
- Input size optimal untuk touch (min-height: 46px, font-size: 16px).
- Tombol full-width di mobile.
- Spacing dan padding nyaman untuk pengalaman mobile.

**Keamanan:**
- Password di-hash dengan Hash::make.
- Session regenerate setelah login.
- Session invalidate + token regenerate saat logout.
- Middleware role untuk proteksi dashboard admin.

### Akun Dummy Tersedia

Semua akun menggunakan password: `password123`

- **Super Admin**
	- Email: `superadmin@example.com`
	- Password: `password123`
	- Role: `super_admin`
	- Akses: `/super-admin/dashboard`

- **Admin Rental**
	- Email: `adminrental@example.com`
	- Password: `password123`
	- Role: `admin_rental`
	- Akses: `/admin-rental/dashboard`

- **Customer (Demo)**
	- Email: `customer@example.com`
	- Password: `password123`
	- Role: `customer`
	- Akses: Halaman home customer

### Cara Menjalankan

1. Pastikan konfigurasi database di `.env` sudah benar.
2. Jalankan migration (termasuk migration phone baru):

```bash
php artisan migrate
```

3. Jalankan seeder untuk membuat akun dummy:

```bash
php artisan db:seed
```

4. Clear cache view (opsional, jika ada perubahan view):

```bash
php artisan view:clear
```

5. Jalankan server lokal:

```bash
php artisan serve
```

6. Akses halaman login di browser:

```text
http://127.0.0.1:8000/login
```

### Route Autentikasi

- `GET /login` → Tampilkan form login (form register link tersedia)
- `POST /login` → Submit login (route name: `login.attempt`)
- `GET /register` → Tampilkan form register
- `POST /register` → Submit register (route name: `register.store`)
- `POST /logout` → Logout dan arahkan ke home (route name: `logout`)
- `GET /super-admin/dashboard` → Dashboard super admin (protected auth + role)
- `GET /admin-rental/dashboard` → Dashboard admin rental (protected auth + role)

### File Struktur yang Diubah/Ditambah

**Controller:**
- `app/Http/Controllers/AuthController.php` → Menambahkan method `showRegisterForm()` dan `register()`, plus refactoring `redirectByRole()` untuk support customer role.

**Model:**
- `app/Models/User.php` → Menambahkan `phone` ke fillable array.

**Routes:**
- `routes/web.php` → Menambahkan route GET `/register` dan POST `/register`.

**Views/Blade:**
- `resources/views/layouts/auth.blade.php` (BARU) → Layout reusable untuk login dan register dengan branding premium.
- `resources/views/auth/login.blade.php` (DIUBAH) → Diupdate dengan desain premium, uses layout auth, tambah toggle password dan checkbox remember.
- `resources/views/auth/register.blade.php` (BARU) → Form register customer dengan field lengkap.

**CSS:**
- `public/assets/css/auth.css` (DIUBAH) → Completely redesigned dengan split layout desktop, gradient backgrounds, animations, dan premium styling.
- `public/assets/css/auth-mobile.css` (BARU) → Media queries untuk responsive design (1100px, 768px, 420px breakpoints).

**JavaScript:**
- `public/assets/js/auth.js` (BARU) → Toggle password visibility untuk login dan register.

**Database:**
- `database/migrations/2026_04_21_000004_add_phone_to_users_table.php` (BARU) → Migration untuk menambah kolom `phone` di tabel users.
- `database/seeders/AdminUserSeeder.php` (DIUBAH) → Menambahkan akun dummy customer dan update phone untuk admin.

### Alur Login Detail

1. User buka `/login`.
2. User sudah login? → Redirect ke dashboard sesuai role.
3. User belum login? → Tampilkan form login premium.
4. User klik "Belum punya akun? Daftar" → Ke halaman `/register`.
5. User submit email + password.
6. Validasi:
   - Jika email/password salah → Tampilkan error, form masih visible.
   - Jika email/password benar → Auth::attempt, session regenerate.
7. Redirect berdasarkan role:
   - `super_admin` → `/super-admin/dashboard`
   - `admin_rental` → `/admin-rental/dashboard`
   - `customer` → `/home`
   - Role tidak valid → Logout + error message.

### Alur Register Detail

1. User buka `/login`, klik "Daftar" → Ke `/register`.
2. User belum login? → Tampilkan form register.
3. User sudah login? → Redirect ke dashboard sesuai role.
4. User isi form: nama, email, HP, password, konfirmasi password, checkbox terms.
5. Submit form.
6. Validasi:
   - Nama: required, max 100 karakter.
   - Email: required, valid email format, unik (tidak boleh sudah terdaftar).
   - HP: required, max 20 karakter.
   - Password: required, min 8 karakter.
   - Konfirmasi password: harus cocok dengan password.
   - Terms: checkbox wajib dicentang.
7. Jika validasi gagal → Tampilkan error, form masih visible dengan data lama.
8. Jika validasi berhasil:
   - Buat user baru di database dengan role `customer`.
   - Password di-hash.
   - Redirect ke `/login` dengan flash message: "Pendaftaran berhasil. Silakan login sebagai customer."
9. User login dengan email + password yang baru didaftar.
10. Setelah login, customer diarahkan ke `/home`.

### Link Navigasi

- **Login ke Register:** Pada form login, ada link "Belum punya akun? Daftar".
- **Register ke Login:** Pada form register, ada link "Sudah punya akun? Masuk".
- **Kembali ke Home:** Di kedua halaman, ada link "Kembali ke beranda" untuk kembali ke home tanpa login.

### Logout

- Tombol logout ada di sidebar admin dashboard (setiap role).
- POST ke `/logout` (form submission via sidebar).
- Setelah logout → Redirect ke halaman home.

### Troubleshooting

- **Route login/register/dashboard tidak muncul:**
  ```bash
  php artisan route:list --path=login
  php artisan route:list --path=register
  php artisan route:list --path=dashboard
  ```

- **View belum berubah:**
  ```bash
  php artisan view:clear
  ```

- **Login/Register gagal:**
  - Pastikan migration phone sudah dijalankan: `php artisan migrate --step`
  - Pastikan seeder sudah dijalankan: `php artisan db:seed`
  - Periksa .env database configuration.
  - Periksa browser console untuk error JS (toggle password).

- **Password toggle tidak bekerja:**
  - Pastikan `public/assets/js/auth.js` terbaca (check browser Network tab).
  - Periksa console untuk error JavaScript.

- **Email sudah terdaftar saat register:**
  - Gunakan email baru, atau gunakan akun dummy untuk login.

- **Role tidak dikenali saat login:**
  - Periksa kolom `role` di tabel users.
  - Pastikan role yang tersimpan adalah: `super_admin`, `admin_rental`, atau `customer`.

### Perubahan Desain Highlights

- **Warna:** Primary blue (#6C8CF5) dengan hover state, aksen warm (#F4B183), background soft (#F8FAFC), soft section (#EEF4FF).
- **Tipografi:** Montserrat (bold, heading), Poppins (regular, body).
- **Layout Desktop:** Grid 2 kolom dengan visual kanan, form kiri.
- **Layout Mobile:** Single column, visual disembunyikan, card form responsive.
- **Animasi:** Fade-up saat card muncul.
- **Interactive:** Toggle password, checkbox styling custom, button hover dengan translateY.
- **Accessibility:** Semantic HTML, aria-label, proper focus states.

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

In addition, [Laracasts](https://laracasts.com) contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

You can also watch bite-sized lessons with real-world projects on [Laravel Learn](https://laravel.com/learn), where you will be guided through building a Laravel application from scratch while learning PHP fundamentals.

## Agentic Development

Laravel's predictable structure and conventions make it ideal for AI coding agents like Claude Code, Cursor, and GitHub Copilot. Install [Laravel Boost](https://laravel.com/docs/ai) to supercharge your AI workflow:

```bash
composer require laravel/boost --dev

php artisan boost:install
```

Boost provides your agent 15+ tools and skills that help agents build Laravel applications while following best practices.

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
