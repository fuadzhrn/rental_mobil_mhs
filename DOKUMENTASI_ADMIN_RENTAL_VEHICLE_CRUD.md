# Dokumentasi CRUD Data Kendaraan Admin Rental

Dokumentasi ini menjelaskan tahap 2 backend yang sudah dibuat untuk fitur **Data Kendaraan** milik admin rental.

## Tujuan Fitur

Fitur ini dibuat agar admin rental dapat mengelola kendaraan milik rental company-nya sendiri dengan aman dan terstruktur.

## Ruang Lingkup

Fitur yang sudah dibuat mencakup:

- list kendaraan
- tambah kendaraan
- simpan kendaraan
- edit kendaraan
- update kendaraan
- hapus kendaraan
- upload foto utama kendaraan
- upload banyak foto galeri kendaraan
- hapus foto galeri satu per satu

Fitur ini khusus untuk role:

- `admin_rental`

## Aturan Akses

- Hanya user dengan role `admin_rental` yang bisa mengakses CRUD kendaraan.
- Admin rental hanya bisa melihat kendaraan milik rental company miliknya sendiri.
- Admin rental hanya bisa membuat, mengubah, dan menghapus kendaraan milik rental company miliknya sendiri.
- Jika admin belum punya rental company, sistem menampilkan pesan yang jelas.
- Jika kendaraan bukan milik rental admin login, sistem mengembalikan `404`.

## Route yang Dibuat

Route kendaraan admin rental sudah ditambahkan di bawah prefix `/admin-rental`.

- `GET /admin-rental/vehicles` → list kendaraan
- `GET /admin-rental/vehicles/create` → form tambah kendaraan
- `POST /admin-rental/vehicles` → simpan kendaraan baru
- `GET /admin-rental/vehicles/{vehicle}/edit` → form edit kendaraan
- `PUT /admin-rental/vehicles/{vehicle}` → update kendaraan
- `DELETE /admin-rental/vehicles/{vehicle}` → hapus kendaraan
- `DELETE /admin-rental/vehicles/gallery/{image}` → hapus gambar galeri

Route name yang dipakai:

- `admin-rental.vehicles.index`
- `admin-rental.vehicles.create`
- `admin-rental.vehicles.store`
- `admin-rental.vehicles.edit`
- `admin-rental.vehicles.update`
- `admin-rental.vehicles.destroy`
- `admin-rental.vehicles.gallery.destroy`

## File Backend yang Ditambahkan

### Controller
- `app/Http/Controllers/AdminRental/VehicleController.php`

### Form Request
- `app/Http/Requests/StoreVehicleRequest.php`
- `app/Http/Requests/UpdateVehicleRequest.php`

### Blade Views
- `resources/views/admin-rental/vehicles/index.blade.php`
- `resources/views/admin-rental/vehicles/create.blade.php`
- `resources/views/admin-rental/vehicles/edit.blade.php`
- `resources/views/admin-rental/vehicles/form.blade.php`

### Route Update
- `routes/web.php`

### Sidebar Update
- `resources/views/components/admin-rental-sidebar.blade.php`

## Model yang Dipakai

Fitur ini menggunakan model yang sudah ada:

- `App\Models\Vehicle`
- `App\Models\VehicleImage`
- `App\Models\RentalCompany`
- `App\Models\User`

## Alur CRUD Kendaraan

### 1. List Kendaraan

- Admin rental membuka halaman Data Kendaraan.
- Sistem mengambil data kendaraan berdasarkan `rental_company_id` milik user login.
- Data ditampilkan dengan pagination.
- Bisa melakukan pencarian sederhana berdasarkan nama, brand, atau kategori.

### 2. Tambah Kendaraan

- Admin klik tombol tambah kendaraan.
- Form tampil dengan input field kendaraan.
- `rental_company_id` tidak dipilih manual, tetapi diambil otomatis dari user login.
- Slug dibuat otomatis dari nama kendaraan.
- Jika upload foto utama dilakukan, file disimpan ke `storage/public`.
- Jika upload galeri dilakukan, file disimpan ke `storage/public` dan record masuk ke tabel `vehicle_images`.

### 3. Edit Kendaraan

- Admin membuka form edit kendaraan.
- Sistem memastikan kendaraan tersebut milik rental company milik admin login.
- Data lama ditampilkan kembali di form.
- Admin bisa mengganti data utama.
- Admin bisa mengganti foto utama.
- Admin bisa menambah gambar galeri baru.
- Admin bisa menghapus gambar galeri lama.

### 4. Update Kendaraan

- Validasi dilakukan ulang.
- Slug diperbarui dari nama baru dan dijaga tetap unik.
- Jika foto utama diganti, file lama dihapus dari storage bila ada.
- Jika gambar galeri dihapus, file dan record juga dihapus.

### 5. Hapus Kendaraan

- Sistem memastikan kendaraan yang dihapus masih berada dalam lingkup rental company milik admin login.
- File main image dihapus jika ada.
- Semua gallery image juga dihapus dari storage dan database.
- Record kendaraan dihapus.

### 6. Hapus Gambar Galeri

- Admin bisa menghapus satu gambar galeri saja dari halaman edit.
- Sistem akan menghapus file gambar dan record database-nya.

## Validasi Input

### Store Vehicle

Validasi yang diterapkan:

- `name` wajib
- `brand` wajib
- `type` wajib
- `category` wajib
- `year` wajib, numerik, dan masuk akal
- `transmission` wajib
- `fuel_type` wajib
- `seat_capacity` wajib, numerik
- `luggage_capacity` nullable, numerik
- `color` nullable
- `price_per_day` wajib, numerik, minimal 0
- `description` nullable
- `terms_conditions` nullable
- `status` wajib, hanya `active`, `inactive`, atau `maintenance`
- `main_image` nullable, harus gambar
- `gallery_images.*` nullable, harus gambar

### Update Vehicle

Validasi update sama seperti create, ditambah:

- `delete_gallery_images[]` untuk memilih gambar galeri yang akan dihapus

## Keamanan Data

Pengamanan yang diterapkan:

- Akses route diproteksi dengan middleware `auth` + `role:admin_rental`.
- Query kendaraan dibatasi berdasarkan `rental_company_id` milik user login.
- Akses ke kendaraan milik rental lain ditolak dengan `404`.
- Upload gambar disimpan ke `storage/public`.
- Hapus kendaraan juga membersihkan file gambar terkait.

## Storage Gambar

Karena file gambar disimpan di `storage/public`, jalankan perintah berikut jika belum dilakukan:

```bash
php artisan storage:link
```

## Flash Message yang Dipakai

Fitur ini sudah menampilkan flash message sederhana untuk:

- berhasil tambah kendaraan
- berhasil update kendaraan
- berhasil hapus kendaraan
- gagal akses karena rental company belum ada

## Struktur Data Kendaraan

### Tabel `vehicles`

Field yang digunakan:

- `name`
- `slug`
- `brand`
- `type`
- `category`
- `year`
- `transmission`
- `fuel_type`
- `seat_capacity`
- `luggage_capacity`
- `color`
- `price_per_day`
- `description`
- `terms_conditions`
- `status`
- `main_image`

### Tabel `vehicle_images`

Field yang digunakan:

- `vehicle_id`
- `image_path`
- `is_primary`

## Sidebar Admin Rental

Menu sidebar admin rental sudah dihubungkan ke halaman Data Kendaraan:

- `Dashboard`
- `Data Kendaraan`

## Catatan Teknis

- Slug dibuat otomatis dari nama kendaraan.
- Slug update dibuat tetap unik agar aman dipakai di URL atau relasi data berikutnya.
- Main image dan gallery image dipisahkan agar lebih fleksibel untuk tahap katalog customer nanti.
- Sistem ini disiapkan supaya nanti gampang disambungkan ke fitur booking dan katalog customer.

## Langkah Menjalankan

1. Pastikan migration sudah dijalankan.
2. Pastikan role `admin_rental` sudah bisa login.
3. Jalankan storage link jika belum ada:

```bash
php artisan storage:link
```

4. Buka menu admin rental.
5. Masuk ke **Data Kendaraan**.
6. Tambah kendaraan dan upload gambar sesuai kebutuhan.

## Status Pengerjaan

- CRUD kendaraan admin rental sudah selesai.
- Akses data sudah dibatasi per rental company.
- Upload gambar utama dan galeri sudah siap.
- Fitur ini siap dipakai sebagai dasar untuk tahap katalog customer dan booking.

## Langkah Berikutnya

Tahap berikutnya yang paling masuk akal:

1. membuat detail kendaraan untuk admin rental
2. menyiapkan katalog customer berdasarkan kendaraan yang approved
3. membangun booking backend yang terhubung ke kendaraan ini
