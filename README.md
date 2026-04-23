# WebRental

Platform rental kendaraan berbasis Laravel untuk multi-rental dengan role `customer`, `admin_rental`, dan `super_admin`.

## Ringkasan Fitur

- Auth multi-role: customer, admin rental, super admin
- CRUD kendaraan admin rental
- Katalog dan detail kendaraan customer
- Booking kendaraan
- Pembayaran dan upload bukti transfer
- Verifikasi pembayaran oleh admin rental
- Riwayat booking customer
- Status booking operasional admin rental
- Review customer
- Promo dan CRM admin rental
- Dashboard super admin
- Laporan platform dan rental
- Notification, activity log, policy authorization, dan hardening dasar

## Role

- `customer`: melihat katalog, booking, bayar, review, dan riwayat booking sendiri
- `admin_rental`: mengelola kendaraan, booking, pembayaran, promo, customer, dan laporan rental sendiri
- `super_admin`: mengelola seluruh platform, verifikasi rental, user, komisi, laporan, dan audit

## Akun Demo

Password semua akun demo: `password123`

- `superadmin@example.com` - Super Admin
- `adminrental1@example.com` - Admin Rental 1
- `adminrental2@example.com` - Admin Rental 2
- `customer1@example.com` - Customer demo utama
- `customer2@example.com` - Customer demo tambahan
- `customer3@example.com` - Customer demo tambahan
- `customer4@example.com` - Customer demo tambahan
- `customer5@example.com` - Customer demo tambahan

## Setup Lokal

1. Install dependency.

```bash
composer install
npm install
```

2. Siapkan environment.

```bash
cp .env.example .env
php artisan key:generate
```

3. Konfigurasi database di `.env`.

4. Jalankan migration dan seeder demo.

```bash
php artisan migrate
php artisan db:seed
```

5. Siapkan storage.

```bash
php artisan storage:link
```

6. Jalankan aplikasi.

```bash
php artisan serve
npm run dev
```

## Command Penting

```bash
php artisan migrate
php artisan db:seed
php artisan route:list
php artisan test
php artisan view:clear
php artisan config:clear
php artisan cache:clear
```

## Dokumentasi Tambahan

- [Checklist Testing](TESTING_CHECKLIST.md)
- [System Flow](SYSTEM_FLOW.md)
- [Deployment Guide](DEPLOYMENT.md)

## Catatan Finalisasi

Tahap 12 memfokuskan project pada kesiapan demo, pengujian, dan deployment. Data demo sudah disiapkan agar laporan, booking, pembayaran, dan review bisa langsung dipresentasikan.
