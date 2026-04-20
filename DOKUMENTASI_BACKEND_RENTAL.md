# Dokumentasi Fondasi Backend Rental

Bagian ini menjelaskan struktur backend inti yang sudah dibuat untuk platform rental kendaraan multi-rental, e-booking, e-commerce, dan CRM.

## Tujuan Fondasi Backend

- Menyediakan struktur database inti yang rapi dan siap dikembangkan.
- Menjaga relasi data antar customer, admin rental, super admin, rental company, kendaraan, booking, dan pembayaran.
- Menyimpan data transaksi dengan struktur yang mudah dipakai untuk fitur berikutnya.

## Tabel Inti yang Sudah Dibuat

- `users`
  - Menyimpan akun customer, admin_rental, dan super_admin.
  - Sudah mendukung kolom `role` dan `phone`.
- `rental_companies`
  - Menyimpan data perusahaan rental milik admin rental.
  - Menyimpan status verifikasi dari super admin.
- `vehicles`
  - Menyimpan data kendaraan milik rental company.
- `vehicle_images`
  - Menyimpan galeri gambar kendaraan.
- `bookings`
  - Menyimpan transaksi booking customer.
- `payments`
  - Menyimpan data pembayaran dan proses verifikasi pembayaran.

## Relasi Antar Tabel

- Satu admin rental memiliki satu rental company.
- Satu rental company memiliki banyak kendaraan.
- Satu kendaraan memiliki banyak gambar.
- Satu customer memiliki banyak booking.
- Satu booking milik satu customer, satu kendaraan, dan satu rental company.
- Satu booking memiliki satu payment.
- Satu payment diverifikasi oleh satu user admin rental.
- Satu rental company diverifikasi oleh satu user super admin.

## Status yang Digunakan

### rental_companies.status_verification
- `pending`
- `approved`
- `rejected`

### vehicles.status
- `active`
- `inactive`
- `maintenance`

### bookings.booking_status
- `pending`
- `waiting_payment`
- `waiting_verification`
- `confirmed`
- `ongoing`
- `completed`
- `cancelled`

### bookings.payment_status
- `unpaid`
- `uploaded`
- `verified`
- `rejected`

### payments.payment_status
- `unpaid`
- `uploaded`
- `verified`
- `rejected`

## File Backend yang Ditambahkan

### Model
- `app/Models/RentalCompany.php`
- `app/Models/Vehicle.php`
- `app/Models/VehicleImage.php`
- `app/Models/Booking.php`
- `app/Models/Payment.php`
- Update model user: `app/Models/User.php`

### Migration
- `database/migrations/2026_04_21_000004_add_phone_to_users_table.php`
- `database/migrations/2026_04_21_000005_create_rental_companies_table.php`
- `database/migrations/2026_04_21_000006_create_vehicles_table.php`
- `database/migrations/2026_04_21_000007_create_vehicle_images_table.php`
- `database/migrations/2026_04_21_000008_create_bookings_table.php`
- `database/migrations/2026_04_21_000009_create_payments_table.php`

## Urutan Migration

Urutan migration dibuat agar foreign key aman saat dijalankan:

1. Tambah kolom `role` pada `users`
2. Tambah kolom `phone` pada `users`
3. Buat `rental_companies`
4. Buat `vehicles`
5. Buat `vehicle_images`
6. Buat `bookings`
7. Buat `payments`

## Aturan Bisnis yang Sudah Siap Ditangani

- Customer wajib login sebelum booking.
- Satu kendaraan tidak boleh dibooking pada tanggal yang sama.
- Pembayaran diverifikasi oleh admin rental.
- Customer hanya bisa memberi ulasan jika booking sudah completed.
- Rental yang belum diverifikasi tidak boleh tampil di platform customer.

## Status Pengerjaan

- Migration sudah dijalankan dan tabel inti sudah terbentuk.
- Model dan relasi dasar sudah disiapkan.
- Fondasi ini siap dipakai untuk controller, service, middleware, dan fitur frontend berikutnya.

## Langkah Berikutnya

1. Buat seeder data contoh rental company, kendaraan, dan booking.
2. Tambahkan validasi booking agar tanggal kendaraan tidak bentrok.
3. Tambahkan query/filter agar customer hanya melihat rental company yang sudah approved.
4. Bangun controller dan halaman admin/customer di atas struktur data ini.
