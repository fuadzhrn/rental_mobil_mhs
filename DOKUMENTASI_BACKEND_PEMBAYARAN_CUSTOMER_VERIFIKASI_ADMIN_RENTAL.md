# Dokumentasi Backend Pembayaran Customer + Verifikasi Admin Rental

Dokumentasi ini menjelaskan implementasi Tahap 5 backend untuk flow pembayaran customer dan verifikasi pembayaran oleh admin rental pada platform rental kendaraan berbasis Laravel.

## Tujuan Tahap Ini

Tahap ini dibuat untuk menyambungkan alur:

- booking customer yang sudah terbentuk
- pembayaran oleh customer pemilik booking
- verifikasi atau penolakan pembayaran oleh admin rental pemilik rental terkait

Fokus utama:

- keamanan akses berdasarkan role dan kepemilikan data
- sinkronisasi status booking dan payment
- upload bukti pembayaran ke storage public
- menyiapkan jembatan ke tahap riwayat booking dan review di fase berikutnya

## Ringkasan Fitur yang Dibuat

### Customer Payment Flow

- customer dapat membuka halaman pembayaran berdasarkan booking code
- customer hanya bisa mengakses booking miliknya sendiri
- customer dapat memilih metode pembayaran
- customer dapat upload bukti pembayaran (jpg/jpeg/png/pdf)
- customer dapat melihat status pembayaran real-time dari data payment
- customer dapat melihat invoice dan bukti transaksi sederhana dari data nyata

### Admin Rental Verification Flow

- admin rental dapat melihat daftar pembayaran booking rental miliknya
- admin rental dapat membuka detail pembayaran per booking
- admin rental dapat verify pembayaran yang statusnya uploaded
- admin rental dapat reject pembayaran dengan alasan wajib
- verifikasi dan penolakan mengubah status payment dan booking secara sinkron

## Aturan Bisnis yang Diterapkan

Aturan yang diimplementasikan pada tahap ini:

1. Customer hanya bisa akses pembayaran booking miliknya sendiri.
2. Admin rental hanya bisa akses pembayaran booking milik rental company miliknya.
3. Upload bukti pembayaran hanya menerima file valid dan ukuran maksimal 5MB.
4. Saat customer upload bukti pembayaran:
   - payment_status payment menjadi uploaded
   - payment_status booking menjadi uploaded
   - booking_status booking menjadi waiting_verification
5. Saat admin rental verify pembayaran:
   - payment_status payment menjadi verified
   - verified_by dan verified_at diisi
   - payment_status booking menjadi verified
   - booking_status booking menjadi confirmed
6. Saat admin rental reject pembayaran:
   - payment_status payment menjadi rejected
   - rejection_note wajib diisi
   - payment_status booking menjadi rejected
   - booking_status booking menjadi waiting_payment
7. Customer yang pembayaran sudah verified tidak dapat mengubah pembayaran lagi.
8. Jika customer upload ulang setelah rejected:
   - file lama dihapus dari storage/public
   - file baru disimpan
   - status kembali ke jalur verifikasi (uploaded / waiting_verification)

## File yang Ditambahkan

### Controller

- app/Http/Controllers/Customer/PaymentController.php
- app/Http/Controllers/AdminRental/PaymentController.php

### Form Request

- app/Http/Requests/UploadPaymentProofRequest.php
- app/Http/Requests/RejectPaymentRequest.php

### Config

- config/payment_methods.php

### View Customer Pembayaran

- resources/views/pembayaran/index.blade.php
- resources/views/pembayaran/ringkasan-pesanan.blade.php
- resources/views/pembayaran/metode-pembayaran.blade.php
- resources/views/pembayaran/detail-pembayaran.blade.php
- resources/views/pembayaran/upload-bukti-pembayaran.blade.php
- resources/views/pembayaran/status-pembayaran.blade.php
- resources/views/pembayaran/invoice-bukti-transaksi.blade.php
- resources/views/pembayaran/footer.blade.php
- resources/views/pembayaran/print.blade.php

### View Admin Rental Pembayaran

- resources/views/admin-rental/payments/index.blade.php
- resources/views/admin-rental/payments/show.blade.php

## File yang Disesuaikan

- routes/web.php
- app/Http/Controllers/Customer/BookingController.php
- app/Models/Booking.php
- app/Models/Payment.php
- resources/views/components/admin-rental-sidebar.blade.php

## Perubahan Route

### Route Customer Payment

- GET /pembayaran/{booking:booking_code} -> pembayaran.show
- POST /pembayaran/{booking:booking_code}/upload -> pembayaran.upload
- GET /pembayaran/{booking:booking_code}/invoice -> pembayaran.invoice
- GET /pembayaran/{booking:booking_code}/bukti-transaksi -> pembayaran.receipt

Proteksi route:

- middleware auth
- middleware role:customer
- ownership check booking di controller

### Route Admin Rental Verification

- GET /admin-rental/payments -> admin-rental.payments.index
- GET /admin-rental/payments/{booking:booking_code} -> admin-rental.payments.show
- PATCH /admin-rental/payments/{booking:booking_code}/verify -> admin-rental.payments.verify
- PATCH /admin-rental/payments/{booking:booking_code}/reject -> admin-rental.payments.reject

Proteksi route:

- middleware auth
- middleware role:admin_rental
- ownership check booking terhadap rental_company admin di controller

## Perubahan Model

### Booking

- ditambahkan route key berbasis booking_code melalui getRouteKeyName()
- relasi payment dipakai untuk sinkronisasi status

### Payment

- ditambahkan accessor status_label untuk tampilan status di view invoice/detail

## Validasi yang Diterapkan

### UploadPaymentProofRequest

Validasi:

- payment_method wajib dan harus ada di config payment_methods
- proof_payment wajib
- proof_payment harus file
- proof_payment mimes: jpg, jpeg, png, pdf
- proof_payment max 5120KB (5MB)

### RejectPaymentRequest

Validasi:

- rejection_note wajib
- rejection_note max 1000 karakter

## Metode Pembayaran

Metode pembayaran dibuat melalui file konfigurasi:

- config/payment_methods.php

Metode yang disiapkan:

- transfer_bank_bca
- transfer_bank_bni
- transfer_bank_bri
- transfer_bank_mandiri
- ewallet_ovo
- ewallet_gopay
- ewallet_dana

Setiap metode memuat:

- label
- group
- account_name
- account_number
- instruction

Pendekatan ini dipilih karena:

- sederhana
- mudah dipelihara
- mudah diganti ke data dinamis di tahap selanjutnya

## Alur Pembayaran Customer

1. Customer login membuka halaman pembayaran dengan booking code.
2. Sistem cek booking milik customer aktif.
3. Halaman menampilkan:
   - ringkasan pesanan nyata dari booking dan vehicle
   - pilihan metode pembayaran
   - detail pembayaran berdasarkan metode terpilih
   - upload bukti pembayaran
   - status pembayaran aktual
4. Customer upload bukti pembayaran.
5. Sistem menyimpan file ke storage/public dan update status booking + payment.
6. Customer menunggu verifikasi admin rental.

## Alur Verifikasi Admin Rental

1. Admin rental membuka daftar pembayaran di panel admin rental.
2. Sistem hanya menampilkan booking/payment milik rental company admin tersebut.
3. Admin membuka detail pembayaran dan bukti transfer.
4. Jika status payment uploaded:
   - admin bisa verify
   - admin bisa reject dengan alasan
5. Sistem update status payment dan booking sesuai aksi admin.

## Sinkronisasi Status Booking dan Payment

### Saat Upload Bukti

- payments.payment_status = uploaded
- payments.paid_at = now()
- bookings.payment_status = uploaded
- bookings.booking_status = waiting_verification

### Saat Verify

- payments.payment_status = verified
- payments.verified_by = admin_rental_login_id
- payments.verified_at = now()
- bookings.payment_status = verified
- bookings.booking_status = confirmed

### Saat Reject

- payments.payment_status = rejected
- payments.rejection_note = alasan wajib
- bookings.payment_status = rejected
- bookings.booking_status = waiting_payment

## Keputusan Saat Payment Rejected

Pendekatan yang dipakai:

- bukti lama tetap ada sampai customer upload ulang
- saat upload ulang, file lama dihapus dari storage/public dan diganti file baru

Alasan:

- menjaga kebersihan storage
- mencegah file orphan
- tetap aman untuk flow upload ulang customer

## Integrasi ke Flow Booking

Setelah booking berhasil dibuat, redirect di booking store diarahkan ke:

- pembayaran.show berdasarkan booking yang baru dibuat

Ini membuat transisi booking -> pembayaran menjadi langsung dan konsisten.

## Perubahan UI yang Tetap Minimal

Frontend pembayaran existing tetap dipakai dan tidak dirombak besar.
Penyesuaian yang dilakukan hanya untuk menghubungkan data nyata dan aksi backend, meliputi:

- binding data booking/payment ke partial pembayaran
- form upload proof dengan action backend nyata
- flash message sukses/error
- status chip berdasarkan data payment nyata
- invoice/bukti transaksi dari data nyata

## Akses Keamanan yang Diterapkan

- customer flow: auth + role:customer + owner booking check
- admin flow: auth + role:admin_rental + owner rental check
- jika akses tidak valid: abort 404 (aman)

## Validasi Hasil Implementasi

Route yang sudah tervalidasi:

- booking route (create/store)
- pembayaran customer route (show/upload/invoice/receipt)
- admin-rental payments route (index/show/verify/reject)

Semua file utama yang disentuh pada tahap ini sudah dicek dan tidak ditemukan error sintaks.

## Catatan Deployment Lokal

Pastikan symlink storage sudah dibuat agar file bukti pembayaran bisa diakses dari browser:

php artisan storage:link

## Langkah Backend Berikutnya

Setelah tahap ini, tahap paling logis adalah:

1. Riwayat booking customer (my bookings).
2. Detail booking customer per status.
3. Flow booking ongoing -> completed.
4. Review customer yang hanya aktif ketika booking completed.
