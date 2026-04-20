# Dokumentasi Backend Katalog Customer + Detail Mobil

Dokumentasi ini menjelaskan tahap 3 backend yang sudah dibuat untuk halaman **Katalog Customer** dan **Detail Mobil**.

## Tujuan Fitur

Tahap ini dibuat agar halaman publik/customer tidak lagi memakai data dummy, melainkan membaca data kendaraan nyata dari database.

Fokus fitur ini:

- menampilkan daftar kendaraan dari database
- menampilkan detail mobil dari database
- menjaga filter data agar hanya kendaraan yang layak tampil yang muncul ke customer
- tetap mempertahankan desain frontend yang sudah ada

## Aturan Bisnis yang Diterapkan

- Katalog dan detail mobil boleh dilihat publik/customer.
- Customer wajib login sebelum booking, tetapi booking belum dibangun di tahap ini.
- Rental yang belum diverifikasi tidak boleh tampil ke customer.
- Hanya kendaraan dengan status `active` yang boleh tampil di katalog.
- Detail mobil hanya boleh diakses jika kendaraan aktif dan rental company sudah `approved`.
- Jika data tidak valid, sistem mengembalikan `404`.

## Route yang Dibuat

Route public/customer yang digunakan:

- `GET /katalog` → `katalog.index`
- `GET /katalog/{vehicle:slug}` → `katalog.show`

Catatan:
- Route detail mobil lama yang statis tidak lagi dipakai.
- Detail mobil sekarang mengikuti slug kendaraan dari database.

## File Backend yang Ditambahkan

### Controller
- `app/Http/Controllers/Customer/KatalogController.php`

### Model yang Disesuaikan
- `app/Models/Vehicle.php`
- `app/Models/RentalCompany.php`
- `app/Models/VehicleImage.php`

### Route Update
- `routes/web.php`

## Relasi Data yang Dipakai

Tahap ini membaca data dari relasi berikut:

- `Vehicle` belongsTo `RentalCompany`
- `Vehicle` hasMany `VehicleImage`
- `Vehicle` punya relasi `primaryImage` untuk foto utama
- `RentalCompany` scope `approved()` untuk rental yang sudah diverifikasi
- `Vehicle` scope `active()` untuk kendaraan aktif
- `Vehicle` scope `visibleToCustomers()` untuk kendaraan yang layak tampil

## Alur Data Katalog

### 1. Halaman Katalog

- Customer membuka `/katalog`.
- Controller mengambil kendaraan yang:
  - status-nya `active`
  - rental company-nya `approved`
- Data di-load dengan eager loading untuk:
  - `rentalCompany`
  - `images`
- Hasil ditampilkan dengan pagination.
- Query string tetap dipertahankan saat pindah halaman.

### 2. Filter dan Sorting

Fitur filter yang disiapkan:

- pencarian berdasarkan nama kendaraan, brand, atau kategori
- filter category
- filter transmission
- filter fuel_type
- filter seat_capacity
- filter harga minimum
- filter harga maksimum
- sorting:
  - terbaru
  - harga terendah
  - harga tertinggi

### 3. List Kendaraan

Setiap kartu kendaraan menampilkan:

- foto utama
- nama kendaraan
- nama rental
- category
- transmission
- fuel_type
- seat_capacity
- price_per_day
- status sederhana: `Tersedia`
- tombol lihat detail

Jika belum ada gambar utama, sistem dapat memakai gambar galeri pertama sebagai fallback.

## Alur Detail Mobil

### 1. Halaman Detail

- Customer membuka `/katalog/{vehicle:slug}`.
- Controller mencari kendaraan berdasarkan slug.
- Sistem memuat relasi:
  - `rentalCompany`
  - `images`
- Detail hanya tampil jika:
  - kendaraan status `active`
  - rental company status `approved`
- Jika tidak valid, sistem mengembalikan `404`.

### 2. Informasi yang Ditampilkan

Halaman detail menampilkan:

- nama kendaraan
- brand
- type
- category
- year
- transmission
- fuel_type
- seat_capacity
- luggage_capacity
- color
- price_per_day
- description
- terms_conditions
- main_image
- gallery images
- informasi rental:
  - company_name
  - city
  - phone
  - email
  - logo jika ada

### 3. Tombol Booking

- Jika guest, tombol booking diarahkan ke halaman login.
- Jika user login sebagai customer, tombol booking diarahkan ke placeholder route booking.
- Jika login bukan customer, tombol diarahkan ke home sebagai langkah aman.

## Partial Blade yang Dipakai

### Katalog
- `resources/views/katalog/index.blade.php`
- `resources/views/katalog/search-bar.blade.php`
- `resources/views/katalog/filter-kendaraan.blade.php`
- `resources/views/katalog/sorting.blade.php`
- `resources/views/katalog/status-ketersediaan.blade.php`
- `resources/views/katalog/daftar-kendaraan.blade.php`
- `resources/views/katalog/pagination.blade.php`

### Detail Mobil
- `resources/views/detail-mobil/index.blade.php`
- `resources/views/detail-mobil/galeri-foto.blade.php`
- `resources/views/detail-mobil/informasi-utama.blade.php`
- `resources/views/detail-mobil/spesifikasi.blade.php`
- `resources/views/detail-mobil/deskripsi.blade.php`
- `resources/views/detail-mobil/syarat-dan-ketentuan.blade.php`
- `resources/views/detail-mobil/informasi-rental.blade.php`
- `resources/views/detail-mobil/rating-dan-ulasan.blade.php`
- `resources/views/detail-mobil/tombol-booking.blade.php`

## Penyesuaian Frontend

Frontend yang sudah ada tetap dipakai, hanya data dummy diganti dengan data nyata dari database.

Penyesuaian utama:

- link katalog di navbar diarahkan ke route baru `katalog.index`
- tombol lihat detail di katalog diarahkan ke route `katalog.show`
- tombol booking di detail mobil memakai pendekatan aman berdasarkan status login
- review tetap placeholder karena backend review belum dibuat

## Keamanan dan Performa

- Query katalog memakai eager loading agar tidak terjadi N+1 query.
- Data customer hanya menampilkan kendaraan dari rental yang sudah approved.
- Detail mobil yang tidak valid diblok dengan `404`.
- Filter query dibatasi agar input tetap aman dan masuk akal.
- Sorting dibatasi pada nilai yang diizinkan.

## Fungsi Reusable yang Ditambahkan

### Pada Model `Vehicle`
- `scopeActive()`
- `scopeVisibleToCustomers()`
- `primaryImage()`

### Pada Model `RentalCompany`
- `scopeApproved()`

Fungsi ini membuat query tahap berikutnya lebih mudah dikembangkan.

## Status Pengerjaan

- Katalog customer sudah membaca data nyata dari database.
- Detail mobil sudah membaca data nyata dari database.
- Filter, sorting, dan pagination sudah disiapkan.
- Tombol detail dan booking sudah diarahkan secara aman.
- Backend booking, pembayaran, dan review masih belum dibuat di tahap ini.

## Langkah Menjalankan

1. Pastikan migration inti sudah dijalankan.
2. Pastikan ada data rental company dengan status `approved`.
3. Pastikan ada kendaraan dengan status `active`.
4. Buka halaman katalog:

```text
http://127.0.0.1:8000/katalog
```

5. Buka detail mobil dari tombol lihat detail di katalog.

## Langkah Berikutnya

Tahap backend berikutnya yang paling masuk akal:

1. membangun backend booking customer
2. menambahkan validasi bentrok tanggal kendaraan
3. membangun backend pembayaran dan verifikasi admin rental
4. menambahkan review setelah booking selesai
