# DOKUMENTASI TAHAP 7
## Backend Review/Ulasan Customer

Tanggal: 21 April 2026
Project: WebRental (Laravel)

---

## 1. Tujuan Tahap Ini
Tahap ini fokus pada alur:
completed booking -> customer membuat review -> review tampil di detail mobil.

Cakupan yang dikerjakan:
1. Customer bisa membuat ulasan hanya untuk booking miliknya yang sudah completed.
2. Satu booking hanya boleh punya satu review.
3. Riwayat booking customer menampilkan status review dan tombol Beri Ulasan saat memenuhi syarat.
4. Halaman detail mobil menampilkan data review nyata dari database (rata-rata, jumlah, daftar ulasan).
5. Admin rental bisa melihat daftar review yang masuk untuk rental miliknya (list sederhana).

Cakupan yang tidak dikerjakan:
1. Promo/CRM.
2. Super admin module review.
3. Balas review.
4. Edit/hapus review.

---

## 2. Implementasi Database

### 2.1 Migration Baru
Dibuat tabel reviews dengan relasi kuat ke booking, customer, vehicle, rental_company.

File:
- database/migrations/2026_04_21_000010_create_reviews_table.php

Struktur penting:
1. booking_id dibuat unique untuk menjamin satu booking maksimal satu review.
2. rating disimpan sebagai unsignedTinyInteger.
3. review dibuat nullable agar customer tetap bisa memberi rating walau tanpa komentar.
4. Index tambahan untuk query listing dan ringkasan rating.

---

## 3. Model dan Relasi

### 3.1 Model Baru
File:
- app/Models/Review.php

Relasi:
1. Review belongsTo Booking
2. Review belongsTo User sebagai customer
3. Review belongsTo Vehicle
4. Review belongsTo RentalCompany

### 3.2 Penyesuaian Model Existing

File:
- app/Models/Booking.php
  - Tambah relasi hasOne review()

- app/Models/Vehicle.php
  - Tambah relasi hasMany reviews()

- app/Models/RentalCompany.php
  - Tambah relasi hasMany reviews()

- app/Models/User.php
  - Tambah relasi hasMany reviews() dengan foreign key customer_id

---

## 4. Controller dan Request

### 4.1 Customer Review Controller
File:
- app/Http/Controllers/Customer/ReviewController.php

Method:
1. create(Booking $booking)
2. store(StoreReviewRequest $request, Booking $booking)

Aturan yang ditegakkan di controller:
1. Booking harus milik customer login. Jika bukan, response 404.
2. Booking harus status completed.
3. Booking tidak boleh sudah punya review.
4. Data relasi review diambil dari booking valid di server, bukan dari input frontend.

Setelah store sukses:
- Redirect ke detail booking customer dengan flash success.

### 4.2 Form Request
File:
- app/Http/Requests/StoreReviewRequest.php

Validasi:
1. rating: required, integer, min 1, max 5.
2. review: nullable, string, max 1000.

Alasan review nullable:
- User bisa cepat memberikan rating tanpa wajib menulis komentar panjang.

### 4.3 Admin Rental Review Controller
File:
- app/Http/Controllers/AdminRental/ReviewController.php

Method:
1. index()

Fungsi:
1. Menampilkan review yang hanya milik rental company dari admin login.
2. Query menggunakan filter rental_company_id agar admin tidak melihat data rental lain.

---

## 5. Routing

File:
- routes/web.php

Route customer review:
1. GET /my-bookings/{booking:booking_code}/review -> customer.reviews.create
2. POST /my-bookings/{booking:booking_code}/review -> customer.reviews.store

Proteksi:
- middleware auth + role:customer

Route admin rental review:
1. GET /admin-rental/reviews -> admin-rental.reviews.index

Proteksi:
- middleware auth + role:admin_rental

---

## 6. Perubahan Query Data

### 6.1 Detail Mobil
File:
- app/Http/Controllers/Customer/KatalogController.php

Pada method show(Vehicle $vehicle):
1. Ambil hanya review kendaraan yang terkait booking completed.
2. Hitung average rating.
3. Hitung total review.
4. Hitung breakdown jumlah bintang 1 sampai 5.
5. Ambil daftar review terbaru dengan eager loading customer.

Hasil dikirim ke view detail mobil:
- reviews
- averageRating
- totalReviews
- ratingBreakdown

### 6.2 Riwayat Booking Customer
File:
- app/Http/Controllers/Customer/MyBookingController.php

Perubahan:
1. Eager load relasi review pada index.
2. Eager load relasi review pada show.

Tujuan:
- Menghindari N+1 saat cek status sudah direview atau belum.

---

## 7. Perubahan Blade

### 7.1 Halaman Form Review Customer
File baru:
- resources/views/customer/reviews/create.blade.php

Isi utama:
1. Info kendaraan, rental, booking code, tanggal sewa.
2. Input rating 1-5.
3. Textarea komentar (opsional).
4. Submit ke customer.reviews.store.

### 7.2 Riwayat Booking Customer (Index)
File:
- resources/views/customer/bookings/index.blade.php

Perubahan:
1. Tambah kolom Ulasan.
2. Jika completed + belum direview -> status Belum Direview.
3. Jika completed + sudah direview -> status Sudah Direview.
4. Tombol Beri Ulasan tampil hanya jika completed dan belum direview.

### 7.3 Detail Booking Customer (Show)
File:
- resources/views/customer/bookings/show.blade.php

Perubahan:
1. Jika completed + belum direview -> tampil CTA Beri Ulasan.
2. Jika completed + sudah direview -> tampil ringkasan ulasan yang sudah dibuat.

### 7.4 Detail Mobil - Section Ulasan
File:
- resources/views/detail-mobil/rating-dan-ulasan.blade.php

Perubahan:
1. Ganti placeholder menjadi data review nyata.
2. Tampil rata-rata rating dan total review.
3. Tampil breakdown bintang.
4. Tampil daftar review terbaru dengan nama customer disamarkan sebagian.
5. Tampil empty state jika belum ada review.

Catatan teknis:
- Untuk menghindari error kompilasi style dinamis pada Blade, visual bar distribusi memakai elemen progress.

### 7.5 Detail Mobil - Informasi Utama
File:
- resources/views/detail-mobil/informasi-utama.blade.php

Perubahan:
1. Tambah ringkasan rating sederhana (avg dan jumlah review).

### 7.6 Admin Rental - List Ulasan
File baru:
- resources/views/admin-rental/reviews/index.blade.php

Perubahan:
1. Tabel sederhana berisi tanggal, kendaraan, customer, booking, rating, cuplikan ulasan.

### 7.7 Sidebar Admin Rental
File:
- resources/views/components/admin-rental-sidebar.blade.php

Perubahan:
1. Tambah menu Data Ulasan ke halaman admin-rental.reviews.index.

---

## 8. Validasi Aturan Bisnis yang Dipenuhi

1. Customer hanya bisa review booking miliknya sendiri.
2. Customer hanya bisa review jika booking completed.
3. Satu booking hanya satu review (double guard: unique DB + guard controller).
4. Data review terhubung ke booking, vehicle, rental_company, customer.
5. Detail mobil menampilkan review nyata dari database.
6. Admin rental hanya melihat review rental miliknya.

---

## 9. Verifikasi yang Dilakukan

Perintah yang dijalankan:
1. php artisan route:list --path=my-bookings; php artisan route:list --path=review; php artisan route:list --path=admin-rental/reviews
2. php artisan route:list --path=admin-rental/reviews

Hasil:
1. Route customer review create/store terdaftar.
2. Route admin rental reviews index terdaftar.
3. Pemeriksaan error file penting tidak menemukan error setelah perbaikan akhir.

---

## 10. Ringkasan Alur End-to-End

1. Booking customer mencapai status completed.
2. Di riwayat booking/detail booking muncul tombol Beri Ulasan.
3. Customer submit rating dan komentar opsional.
4. Backend validasi kepemilikan booking, status completed, dan review belum ada.
5. Review disimpan dan terhubung ke booking, vehicle, rental_company, customer.
6. Detail mobil otomatis menampilkan ringkasan dan daftar ulasan terbaru.
7. Admin rental melihat ulasan masuk pada menu Data Ulasan.

---

## 11. Langkah Lanjutan yang Disarankan

1. Tambah unit/feature test untuk skenario akses review (ownership, completed, duplicate).
2. Tambah endpoint agregasi rating jika nanti dipakai modul promo/CRM.
3. Tambah moderation sederhana untuk kualitas konten ulasan sebelum tahap promo.
