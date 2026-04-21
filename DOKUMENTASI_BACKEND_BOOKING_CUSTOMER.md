# Dokumentasi Backend Booking Customer

Dokumentasi ini menjelaskan tahap 4 backend yang sudah dibuat untuk **booking customer** pada website rental kendaraan berbasis Laravel.

## Tujuan Fitur

Tahap ini dibuat agar customer bisa melakukan booking kendaraan secara valid menggunakan data nyata dari database.

Fokus fitur ini:

- hanya customer login yang boleh booking
- kendaraan yang dibooking harus valid dan layak tampil
- customer mengisi data booking melalui form frontend yang sudah ada
- sistem memvalidasi data booking secara backend
- sistem mengecek bentrok tanggal booking kendaraan
- booking yang valid disimpan ke database
- payment awal dibuat otomatis dengan status unpaid
- booking diarahkan ke langkah pembayaran placeholder

## Aturan Bisnis yang Diterapkan

Aturan yang wajib dipatuhi:

- customer wajib login sebelum booking
- rental yang belum diverifikasi tidak boleh dibooking
- kendaraan dengan status selain `active` tidak boleh dibooking
- satu kendaraan tidak boleh dibooking pada tanggal yang sama jika booking aktif masih memblokir
- customer hanya boleh booking kendaraan dari rental dengan status `approved`
- backend pembayaran penuh belum dibuat di tahap ini
- review belum dibuat di tahap ini

Status booking yang dianggap memblokir kendaraan:

- `pending`
- `waiting_payment`
- `waiting_verification`
- `confirmed`
- `ongoing`

Status yang tidak memblokir:

- `cancelled`
- `completed`

## Route yang Dibuat

Route booking customer yang digunakan:

- `GET /booking/{vehicle:slug}` → `booking.create`
- `POST /booking/{vehicle:slug}` → `booking.store`

Catatan:
- route booking hanya tersedia untuk user dengan role `customer`
- guest diarahkan ke login
- admin rental dan super admin tidak masuk flow booking customer
- route booking lama yang umum tanpa slug tidak dipakai untuk create flow

## File Backend yang Ditambahkan

### Controller
- `app/Http/Controllers/Customer/BookingController.php`

### Form Request
- `app/Http/Requests/StoreBookingRequest.php`

### Model yang Disesuaikan
- `app/Models/Booking.php`
- `app/Models/Payment.php`
- `app/Models/Vehicle.php`
- `app/Models/RentalCompany.php`

### Route Update
- `routes/web.php`

## Alur Booking Customer

### 1. Tombol Booking dari Detail Mobil

- Tombol `Booking Sekarang` di detail mobil diarahkan ke route `booking.create`.
- Jika guest klik tombol ini, sistem mengarah ke halaman login.
- Jika user login bukan customer, sistem diarahkan ke home sebagai langkah aman.

### 2. Halaman Booking

- Customer membuka halaman booking berdasarkan slug kendaraan.
- Controller memuat data kendaraan beserta rental company dan foto kendaraan.
- Sistem mengecek bahwa:
  - kendaraan berstatus `active`
  - rental company berstatus `approved`
- Jika tidak valid, sistem mengembalikan `404`.

### 3. Form Data Penyewa

Field yang dipakai dari form booking:

- nama lengkap
- email
- nomor HP
- alamat
- nomor KTP
- nomor SIM
- catatan tambahan

Prefill yang dipakai:

- `customer_name` dari nama user login
- `customer_email` dari email user login
- `customer_phone` dari nomor HP user login jika ada

Jika tidak ada customer profile tambahan, data tetap diambil dari `users` dan input form yang tersedia.

### 4. Detail Pemesanan

Field booking yang diproses:

- pickup_date
- return_date
- pickup_time nullable
- pickup_location
- return_location nullable
- with_driver boolean
- note nullable

Logika yang diterapkan:

- `pickup_date` wajib
- `return_date` wajib
- `return_date` harus lebih besar atau sama dengan `pickup_date`
- `pickup_date` tidak boleh di masa lalu
- duration dihitung otomatis di backend
- subtotal dihitung otomatis dari duration dan harga kendaraan per hari
- discount_amount sementara `0`
- additional_cost sementara `0`
- total_amount dihitung ulang di backend

## Validasi Booking

Validasi utama yang dipakai di `StoreBookingRequest`:

- `customer_name` wajib
- `customer_email` wajib dan format email valid
- `customer_phone` wajib
- `customer_address` wajib
- `identity_number` wajib
- `driver_license_number` nullable
- `pickup_date` wajib dan minimal hari ini
- `return_date` wajib dan tidak boleh lebih kecil dari `pickup_date`
- `pickup_location` wajib
- `pickup_time` nullable dengan format jam yang valid
- `with_driver` boolean
- `note` nullable

Catatan:
- nilai harga, subtotal, total, dan durasi tidak diambil dari frontend
- semua perhitungan harga dihitung ulang di backend

## Logika Bentrok Tanggal

Sebelum booking disimpan, sistem mengecek apakah kendaraan sudah dibooking pada rentang tanggal yang sama.

Logika overlap yang dipakai:

- cek booking lain dengan `vehicle_id` yang sama
- cek booking yang statusnya masih memblokir kendaraan
- overlap dianggap terjadi jika:
  - tanggal mulai booking baru berada di dalam rentang booking lama
  - tanggal selesai booking baru berada di dalam rentang booking lama
  - booking baru sepenuhnya mencakup booking lama

Jika bentrok ditemukan:

- sistem mengembalikan user ke form booking
- sistem menampilkan error bahwa kendaraan tidak tersedia pada rentang tanggal tersebut

## Booking Code

Booking code dibuat otomatis dengan format:

- `BK-YYYYMMDD-XXXXXX`

Contoh:

- `BK-20260421-A1B2C3`

Aturan yang dipakai:

- memakai prefix `BK`
- memakai tanggal hari ini
- memakai random string kapital 6 karakter
- dicek ke database agar unik sebelum dipakai

## Penyimpanan Booking

Jika data valid dan tidak bentrok, sistem menyimpan record booking ke tabel `bookings` dengan data:

- booking_code unik
- customer_id dari user login
- rental_company_id dari vehicle
- vehicle_id dari kendaraan yang dipilih
- pickup_date
- return_date
- pickup_time
- pickup_location
- return_location
- duration_days
- with_driver
- customer_name
- customer_email
- customer_phone
- customer_address
- identity_number
- driver_license_number
- note
- subtotal
- discount_amount
- additional_cost
- total_amount
- booking_status = `waiting_payment`
- payment_status = `unpaid`

## Pembuatan Payment Awal

Setelah booking berhasil dibuat, sistem otomatis membuat data payment awal di tabel `payments`.

Nilai yang disimpan:

- booking_id
- payment_method = `manual_transfer`
- amount = total_amount
- payment_status = `unpaid`
- proof_payment = null
- paid_at = null
- verified_by = null
- verified_at = null

Keputusan ini dipakai karena backend pembayaran penuh belum dibangun, tetapi relasi booking-payment sudah disiapkan dari awal.

## Redirect Setelah Booking Berhasil

Setelah booking berhasil disimpan:

- user diarahkan ke halaman pembayaran placeholder
- notifikasi sukses dikirim ke session

Pendekatan ini dipilih karena paling aman dan paling mudah dikembangkan ke tahap pembayaran berikutnya.

## Partial Blade yang Dipakai

### Booking
- `resources/views/booking/index.blade.php`
- `resources/views/booking/data-kendaraan.blade.php`
- `resources/views/booking/form-data-penyewa.blade.php`
- `resources/views/booking/detail-pemesanan.blade.php`
- `resources/views/booking/promo-voucher.blade.php`
- `resources/views/booking/ringkasan-biaya.blade.php`
- `resources/views/booking/tombol-lanjut-pembayaran.blade.php`

### Detail Mobil
- `resources/views/detail-mobil/tombol-booking.blade.php`

## Penyesuaian Frontend

Frontend booking yang sudah ada tetap dipakai, hanya disambungkan ke backend nyata.

Penyesuaian utama:

- data kendaraan diambil dari vehicle yang dipilih
- data customer login dipakai sebagai default form penyewa
- ringkasan biaya dihitung ulang berdasarkan backend logic
- tombol lanjut pembayaran diubah menjadi tombol submit booking
- promo voucher tetap placeholder karena backend promo belum ada

## Keamanan dan Performa

- flow booking dilindungi middleware `auth` dan `role:customer`
- vehicle divalidasi dengan route model binding `vehicle:slug`
- kendaraan tidak valid diblok dengan `404`
- query bentrok memakai filter status yang jelas
- booking code dicek unik sebelum disimpan
- harga tidak pernah dipercaya dari frontend
- data payment awal selalu dibuat dari transaksi booking yang sama

## Status Pengerjaan

- customer bisa membuka halaman booking dari detail mobil
- guest diarahkan ke login
- hanya customer login yang bisa akses booking
- validasi booking backend sudah aktif
- cek bentrok tanggal sudah aktif
- booking dan payment awal sudah tersimpan otomatis
- pembayaran penuh, upload bukti bayar, dan review masih belum dibuat

## Langkah Menjalankan

1. Pastikan data rental company berstatus `approved` tersedia.
2. Pastikan kendaraan berstatus `active` tersedia.
3. Buka halaman detail mobil dari katalog.
4. Klik tombol booking untuk masuk ke halaman booking.
5. Isi form booking lalu simpan.

## Langkah Berikutnya

Tahap backend berikutnya yang paling masuk akal:

1. membangun backend pembayaran customer
2. menambahkan upload bukti bayar dan verifikasi admin rental
3. menambahkan halaman detail booking customer
4. menambahkan review setelah booking selesai
