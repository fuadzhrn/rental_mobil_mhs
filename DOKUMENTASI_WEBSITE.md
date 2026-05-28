# Dokumentasi Website WebRental untuk Pemula

Dokumen ini menjelaskan website WebRental dengan bahasa sederhana supaya mudah dipahami oleh orang yang baru pertama kali melihat sistem ini.

## 1. WebRental Itu Apa?

WebRental adalah website rental kendaraan. Lewat website ini, orang bisa:

- melihat daftar kendaraan yang tersedia
- membaca detail kendaraan
- booking kendaraan
- mengunggah bukti pembayaran
- melihat status booking
- memberi ulasan setelah selesai memakai kendaraan

Di sisi lain, admin rental dan super admin bisa mengelola data serta memantau aktivitas bisnis dari dashboard masing-masing.

## 2. Siapa Saja Penggunanya?

Website ini punya 3 jenis pengguna utama.

### Customer
Customer adalah pengguna yang menyewa kendaraan.

Yang bisa dilakukan customer:

- daftar akun dan login
- melihat katalog kendaraan
- melihat detail kendaraan
- melakukan booking
- mengunggah bukti pembayaran
- melihat status booking
- membaca riwayat booking
- memberi review setelah booking selesai

### Admin Rental
Admin rental adalah pengelola untuk satu perusahaan rental.

Yang bisa dilakukan admin rental:

- login ke area rental
- menambah, mengubah, dan menghapus kendaraan
- melihat booking yang masuk
- memverifikasi atau menolak pembayaran
- mengubah status booking menjadi ongoing atau completed
- mengelola promo
- melihat laporan rental miliknya sendiri

### Super Admin
Super admin adalah pengelola utama website.

Yang bisa dilakukan super admin:

- login ke area super admin
- melihat semua user
- memverifikasi rental
- melihat laporan seluruh platform
- melihat komisi platform
- melihat audit log atau jejak aktivitas sistem
- memantau semua rental

## 3. Halaman Penting di Website

### Halaman Home
Halaman home adalah halaman utama yang pertama kali dilihat pengunjung.

Di halaman ini biasanya ada:

- banner utama
- keunggulan layanan
- daftar kendaraan unggulan
- promo
- testimoni pelanggan
- form pencarian kendaraan

Tujuan halaman ini adalah membantu pengunjung cepat memahami layanan dan menemukan kendaraan yang cocok.

### Halaman Login dan Register
Halaman ini digunakan agar pengguna bisa masuk ke akun masing-masing.

Fungsinya:

- customer bisa login atau daftar akun baru
- admin rental dan super admin bisa login ke dashboard masing-masing

Setelah login, sistem akan mengarahkan user ke halaman yang sesuai dengan role-nya.

### Halaman Katalog Kendaraan
Halaman katalog menampilkan semua kendaraan yang bisa disewa.

Pengunjung bisa:

- melihat foto kendaraan
- membaca nama dan detail singkat
- membuka halaman detail kendaraan

### Halaman Detail Kendaraan
Halaman ini berisi informasi lengkap kendaraan.

Biasanya berisi:

- foto kendaraan
- merk dan tipe
- harga per hari
- kapasitas kursi
- transmisi
- bahan bakar
- deskripsi kendaraan

Halaman ini membantu customer memutuskan apakah kendaraan cocok untuk dibooking.

### Halaman Booking
Di halaman ini customer memilih kendaraan dan mengisi data booking.

Alurnya:

1. customer memilih kendaraan
2. customer memilih tanggal sewa
3. sistem mengecek ketersediaan kendaraan
4. booking disimpan
5. customer diarahkan ke pembayaran

### Halaman Pembayaran
Setelah booking dibuat, customer akan masuk ke halaman pembayaran.

Di halaman ini customer bisa:

- melihat detail booking
- memilih metode pembayaran
- mengunggah bukti transfer

Setelah upload bukti pembayaran, status akan berubah menjadi uploaded dan menunggu dicek admin rental.

### Halaman My Bookings
Halaman ini berisi daftar booking milik customer.

Customer bisa melihat:

- booking yang sedang berjalan
- booking yang sudah selesai
- status pembayaran
- detail transaksi

### Halaman Review
Jika booking sudah selesai, customer bisa memberi ulasan.

Review ini berguna untuk membantu customer lain melihat pengalaman pengguna sebelumnya.

### Halaman Dashboard Admin Rental
Dashboard ini adalah pusat kerja admin rental.

Di dalamnya admin rental bisa melihat ringkasan seperti:

- total kendaraan
- total booking
- total customer
- total pembayaran terverifikasi
- grafik booking bulanan
- akses cepat ke menu penting

### Halaman Dashboard Super Admin
Dashboard super admin dipakai untuk memantau seluruh sistem.

Biasanya menampilkan:

- total rental
- total user
- total booking
- total revenue
- total komisi
- grafik booking per bulan
- akses cepat ke laporan dan monitoring

### Halaman Laporan
Halaman laporan dipakai untuk membaca data bisnis secara lebih serius.

Laporan ini membantu pengguna memahami:

- berapa booking yang masuk
- pembayaran mana yang sudah verified
- kendaraan mana yang paling laku
- customer mana yang paling aktif
- berapa pendapatan bisnis
- berapa komisi platform

Laporan super admin melihat semua data platform.
Laporan admin rental hanya melihat data milik rental sendiri.

### Halaman Audit Log
Audit log adalah halaman catatan aktivitas sistem.

Halaman ini dipakai untuk melihat:

- siapa yang melakukan aksi
- aksi apa yang dilakukan
- kapan aksi dilakukan
- data apa yang berubah

Tujuannya untuk monitoring dan pengecekan jika ada masalah.

## 4. Alur Sederhana Cara Kerja Website

### Alur Customer

1. customer buka website
2. customer melihat katalog kendaraan
3. customer membuka detail kendaraan
4. customer booking kendaraan
5. customer melakukan pembayaran
6. admin rental mengecek pembayaran
7. jika valid, booking diproses
8. setelah selesai, customer bisa memberi review

### Alur Admin Rental

1. admin rental login
2. admin membuka dashboard
3. admin melihat booking masuk
4. admin mengecek pembayaran
5. admin mengubah status booking bila perlu
6. admin mengelola kendaraan dan promo
7. admin membuka laporan rental

### Alur Super Admin

1. super admin login
2. super admin membuka dashboard utama
3. super admin melihat daftar user dan rental
4. super admin membuka laporan platform
5. super admin memantau audit log
6. super admin memonitor kesehatan sistem secara umum

## 5. Penjelasan Status yang Sering Dipakai

### Status Booking

- waiting_payment: menunggu pembayaran
- waiting_verification: menunggu verifikasi pembayaran
- confirmed: booking sudah dikonfirmasi
- ongoing: kendaraan sedang dipakai
- completed: booking sudah selesai
- cancelled: booking dibatalkan

### Status Pembayaran

- unpaid: belum ada pembayaran
- uploaded: bukti pembayaran sudah diunggah
- verified: pembayaran sudah disetujui admin
- rejected: pembayaran ditolak admin

## 6. Kenapa Ada Role Berbeda?

Role berbeda dibuat supaya setiap orang hanya melihat menu dan data yang sesuai tugasnya.

Contohnya:

- customer tidak bisa masuk ke halaman admin
- admin rental tidak bisa melihat data rental lain
- super admin bisa memantau semua data di platform

Ini membuat sistem lebih rapi, aman, dan mudah dikelola.

## 7. Ringkasan Singkat

Kalau dijelaskan dengan sederhana, WebRental adalah website untuk menyewa kendaraan secara online.

Customer memakai website untuk booking dan membayar.
Admin rental memakai website untuk mengelola kendaraan dan memproses booking.
Super admin memakai website untuk mengawasi seluruh sistem.

## 8. Cocok Dibaca Siapa?

Dokumen ini cocok untuk:

- pemula yang baru pertama kali melihat project ini
- developer baru yang ingin memahami alur website
- pemilik project yang ingin tahu fungsi tiap bagian website
