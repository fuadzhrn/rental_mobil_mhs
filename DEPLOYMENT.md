# Deployment Guide

Panduan deployment Laravel untuk project WebRental.

## Kebutuhan Server

- PHP 8.2 atau lebih baru
- Composer
- Web server: Nginx atau Apache
- MySQL / MariaDB
- Extension PHP umum:
  - `ctype`
  - `curl`
  - `dom`
  - `fileinfo`
  - `filter`
  - `mbstring`
  - `openssl`
  - `pdo_mysql`
  - `tokenizer`
  - `xml`
  - `zip`

## Langkah Deployment

1. Upload source code ke server.
2. Install dependency production.

```bash
composer install --optimize-autoloader --no-dev
```

3. Salin file environment.

```bash
cp .env.example .env
```

4. Edit `.env` produksi.

```env
APP_NAME=WebRental
APP_ENV=production
APP_DEBUG=false
APP_URL=https://domain-anda.com
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=webrental
DB_USERNAME=webrental_user
DB_PASSWORD=rahasia
```

5. Generate application key jika belum ada.

```bash
php artisan key:generate
```

6. Jalankan migration.

```bash
php artisan migrate --force
```

7. Jika butuh data demo untuk presentasi, jalankan seeder.

```bash
php artisan db:seed --force
```

8. Buat storage symlink.

```bash
php artisan storage:link
```

9. Optimalkan cache Laravel.

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Permission Folder

Pastikan folder ini writable oleh web server:

- `storage/`
- `bootstrap/cache/`

Contoh di Linux:

```bash
chmod -R 775 storage bootstrap/cache
```

## Setelah Deploy

- Pastikan `.env` tidak bisa diakses publik
- Pastikan `public/index.php` menjadi document root
- Pastikan `public/storage` tersedia
- Pastikan database backup dibuat sebelum deploy

## Catatan Keamanan

- `APP_DEBUG=false` di production
- Gunakan password database yang kuat
- Jangan upload `.env` ke repository publik
- Gunakan HTTPS
- Backup database secara rutin
- Jika seeder demo dipakai di staging, hindari di production final kecuali memang diperlukan

## Command Operasional

```bash
php artisan migrate --force
php artisan db:seed --force
php artisan storage:link
php artisan optimize
```

## Troubleshooting

- Jika view lama masih muncul, jalankan:

```bash
php artisan optimize:clear
```

- Jika route tidak terbaca, jalankan:

```bash
php artisan route:clear
```

- Jika cache konfigurasi bermasalah, jalankan:

```bash
php artisan config:clear
php artisan view:clear
```
