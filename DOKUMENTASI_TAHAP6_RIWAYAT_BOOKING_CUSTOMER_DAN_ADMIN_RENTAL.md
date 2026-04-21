# Dokumentasi Tahap 6 Backend

## Riwayat Booking Customer + Daftar Booking Admin Rental + Status Transaksi Lengkap

Dokumentasi ini menjelaskan implementasi Tahap 6 backend yang berfokus pada:

- riwayat booking milik customer
- daftar booking milik admin rental
- detail transaksi booking dengan status booking dan payment yang konsisten
- transisi status operasional booking oleh admin rental

## Tujuan Implementasi

Tahap ini dibuat agar:

1. Customer dapat melihat seluruh booking miliknya sendiri.
2. Customer dapat memfilter dan membuka detail booking.
3. Admin rental dapat melihat booking rental miliknya sendiri.
4. Admin rental dapat melakukan transisi status operasional booking secara aman.
5. Status booking dan payment tetap sinkron serta siap menjadi fondasi fitur review tahap berikutnya.

## Aturan Bisnis yang Diterapkan

Aturan yang dijaga dalam implementasi ini:

- customer hanya bisa melihat booking miliknya sendiri
- admin rental hanya bisa melihat booking milik rental_company miliknya sendiri
- transisi operasional dibatasi:
  - confirmed -> ongoing (hanya jika payment_status = verified)
  - ongoing -> completed
- cancel booking hanya diizinkan untuk status:
  - waiting_payment
  - waiting_verification
  - confirmed
- booking status completed tidak bisa dicancel
- payment_status tidak diubah sembarangan saat cancel
- jika payment sudah verified lalu booking dicancel, payment_status tetap verified (refund belum dibahas di tahap ini)

## Daftar File yang Ditambahkan

### Controller

- app/Http/Controllers/Customer/MyBookingController.php
- app/Http/Controllers/AdminRental/BookingController.php

### Request Validation

- app/Http/Requests/CancelBookingRequest.php

### View Customer

- resources/views/customer/bookings/index.blade.php
- resources/views/customer/bookings/show.blade.php

### View Admin Rental

- resources/views/admin-rental/bookings/index.blade.php
- resources/views/admin-rental/bookings/show.blade.php

## Daftar File yang Disesuaikan

- routes/web.php
- app/Models/Booking.php
- resources/views/components/admin-rental-sidebar.blade.php

## Perubahan Routes

### Customer Bookings

- GET /my-bookings -> customer.bookings.index
- GET /my-bookings/{booking:booking_code} -> customer.bookings.show

### Admin Rental Bookings

- GET /admin-rental/bookings -> admin-rental.bookings.index
- GET /admin-rental/bookings/{booking:booking_code} -> admin-rental.bookings.show
- PATCH /admin-rental/bookings/{booking:booking_code}/mark-ongoing -> admin-rental.bookings.mark-ongoing
- PATCH /admin-rental/bookings/{booking:booking_code}/mark-completed -> admin-rental.bookings.mark-completed
- PATCH /admin-rental/bookings/{booking:booking_code}/cancel -> admin-rental.bookings.cancel

Semua route diproteksi dengan middleware role yang sesuai:

- customer flow: auth + role:customer
- admin flow: auth + role:admin_rental

## Detail Implementasi Controller

## 1) Customer - MyBookingController

### index()

Fitur:

- query booking hanya untuk customer login (where customer_id = auth user)
- filter booking_status
- filter payment_status
- search booking_code atau nama kendaraan
- eager loading:
  - vehicle
  - vehicle.primaryImage
  - vehicle.rentalCompany
  - payment
- pagination + withQueryString

### show()

Fitur:

- cek ownership booking milik customer login
- jika bukan miliknya -> abort 404
- tampilkan detail lengkap booking + payment + kendaraan + rental

## 2) Admin Rental - BookingController

### index()

Fitur:

- query booking hanya milik rental company admin login
- filter booking_status
- filter payment_status
- search booking_code, customer_name, atau nama kendaraan
- eager loading:
  - vehicle
  - customer
  - payment
- pagination + withQueryString

### show()

Fitur:

- cek booking milik rental company admin login
- jika bukan miliknya -> abort 404
- tampilkan detail booking + data customer + payment + bukti pembayaran

### markOngoing()

Validasi transisi:

- booking_status harus confirmed
- payment_status harus verified

Jika valid:

- booking_status -> ongoing

### markCompleted()

Validasi transisi:

- booking_status harus ongoing

Jika valid:

- booking_status -> completed

### cancel()

Validasi transisi:

- hanya boleh jika booking_status saat ini:
  - waiting_payment
  - waiting_verification
  - confirmed

Jika valid:

- booking_status -> cancelled
- payment_status tidak diubah
- cancel_reason (opsional) ditambahkan ke note booking

## Penyesuaian Model Booking

Model Booking ditambahkan helper agar status bisa konsisten dipakai di UI:

- statusOptions()
- paymentStatusOptions()
- getBookingStatusLabelAttribute()
- getPaymentStatusLabelAttribute()

Manfaat:

- label status lebih rapi dan konsisten
- dipakai ulang di halaman customer dan admin tanpa duplikasi mapping

## Blade Customer

## customer/bookings/index.blade.php

Menampilkan:

- daftar booking customer
- booking_code
- foto kendaraan
- nama kendaraan
- nama rental
- tanggal sewa
- durasi
- total_amount
- booking_status (badge)
- payment_status (badge)
- tombol detail
- filter dan search

## customer/bookings/show.blade.php

Menampilkan:

- ringkasan booking lengkap
- data kendaraan, rental, penyewa
- jadwal sewa
- rincian biaya
- status booking dan payment
- metode pembayaran
- bukti pembayaran jika ada
- rejection_note jika pembayaran ditolak
- timeline status transaksi

Kondisi khusus:

- jika payment_status = rejected: tampilkan pesan penolakan + tombol upload ulang ke flow pembayaran
- jika booking_status = completed: tampilkan placeholder bahwa fitur review akan tersedia di tahap selanjutnya

## Blade Admin Rental

## admin-rental/bookings/index.blade.php

Menampilkan:

- daftar booking milik rental
- filter booking_status, payment_status, search
- booking_code
- customer
- kendaraan
- pickup / return
- total
- status booking
- status payment
- tanggal booking
- tombol detail

## admin-rental/bookings/show.blade.php

Menampilkan:

- detail booking lengkap
- data payment + bukti pembayaran
- timeline transaksi
- tombol aksi status operasional dengan kondisi tampil berdasarkan status saat ini:
  - mark ongoing
  - mark completed
  - cancel booking

## Timeline Status Transaksi

Pada halaman detail customer dan admin ditampilkan status flow transaksi berbasis kondisi saat ini:

- Booking Dibuat
- Menunggu Pembayaran
- Menunggu Verifikasi
- Dikonfirmasi
- Sedang Berjalan
- Selesai
- Dibatalkan

Tahap ini belum menggunakan tabel history terpisah. Timeline bersifat representasi status state saat ini.

## Validasi Keamanan

Yang dipastikan:

- customer tidak dapat mengakses booking milik user lain
- admin rental tidak dapat mengakses booking milik rental lain
- transisi status tidak valid ditolak dengan pesan error
- payment_status tidak dimodifikasi sembarangan saat update operasional

## Integrasi Navigasi Admin

Sidebar admin rental disesuaikan:

- menu Data Booking diarahkan ke route admin-rental.bookings.index

## Validasi Hasil

Route yang sudah dicek dan aktif:

- my-bookings (index, show)
- admin-rental/bookings (index, show, mark-ongoing, mark-completed, cancel)

File utama yang diubah pada tahap ini telah dicek dan tidak ada error sintaks.

## Dampak ke Tahap Berikutnya

Tahap ini sudah menyiapkan fondasi untuk:

1. fitur review/ulasan customer berbasis booking completed
2. analitik lifecycle booking (confirmed -> ongoing -> completed)
3. fitur CRM di tahap lanjutan berbasis histori transaksi customer

## Catatan Scope Tahap Ini

Hal yang sengaja belum dibuat:

- review
- promo/CRM aktif
- refund flow
- audit log transaksi terpisah

Semua poin di atas ditunda sesuai batasan tahap implementasi.