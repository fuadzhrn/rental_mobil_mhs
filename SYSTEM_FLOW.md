# System Flow

Dokumentasi ringkas alur sistem WebRental.

## Struktur Role

### Customer
- Registrasi dan login
- Melihat katalog kendaraan
- Melihat detail kendaraan
- Booking kendaraan
- Upload bukti pembayaran
- Melihat status booking dan riwayat
- Memberikan review setelah booking selesai
- Menggunakan promo yang valid

### Admin Rental
- Login ke area rental
- Mengelola kendaraan
- Mengelola booking masuk
- Verifikasi atau menolak pembayaran
- Mengubah status booking menjadi ongoing atau completed
- Mengelola promo dan CRM customer
- Melihat laporan rental sendiri

### Super Admin
- Login ke area super admin
- Verifikasi rental
- Melihat semua user
- Melihat laporan platform
- Melihat komisi platform
- Melihat aktivitas sistem
- Monitoring seluruh rental

## Alur Utama Sistem

### 1. Flow Booking
1. Customer membuka katalog.
2. Customer membuka detail kendaraan.
3. Customer memilih tanggal booking.
4. Sistem mengecek ketersediaan dan bentrok tanggal.
5. Customer mengisi data booking.
6. Booking disimpan dengan status awal yang sesuai.
7. Customer diarahkan ke pembayaran.

### 2. Flow Pembayaran
1. Customer membuka halaman pembayaran booking.
2. Customer memilih metode pembayaran.
3. Customer upload bukti pembayaran.
4. Status pembayaran berubah menjadi `uploaded`.
5. Admin rental mengecek bukti.
6. Admin rental verifikasi atau menolak pembayaran.

### 3. Flow Verifikasi Rental
1. Admin rental melihat daftar booking dan pembayaran masuk.
2. Admin rental memeriksa bukti pembayaran.
3. Jika valid, status berubah menjadi `verified`.
4. Jika tidak valid, status berubah menjadi `rejected`.
5. Customer menerima notifikasi status pembayaran.

### 4. Flow Operasional Booking
1. Setelah pembayaran verified, booking bisa diproses.
2. Admin rental mengubah status menjadi `ongoing`.
3. Setelah kendaraan kembali, admin rental mengubah status menjadi `completed`.
4. Customer bisa memberikan review setelah completed.

### 5. Flow Review
1. Booking selesai.
2. Customer membuka detail booking.
3. Customer mengisi rating dan ulasan.
4. Review disimpan dan ditampilkan pada data rental/kendaraan.

### 6. Flow Promo + CRM
1. Admin rental membuat promo.
2. Promo bisa bersifat umum atau `loyal_only`.
3. Customer loyal mendapatkan akses promo tertentu.
4. Promo diterapkan saat booking jika valid.
5. Data CRM dipakai untuk monitoring customer aktif dan loyal.

### 7. Flow Laporan
1. Super admin membuka laporan platform.
2. Admin rental membuka laporan rental sendiri.
3. Laporan memakai data booking dan payment nyata.
4. Filter tanggal dan status dipakai untuk analisis.

### 8. Flow Komisi
1. Sistem mengambil transaksi dengan `payment_status = verified`.
2. Komisi dihitung dari persentase tetap 10%.
3. Total revenue dan total komisi ditampilkan di laporan.
4. Super admin dapat melihat komisi per transaksi dan per rental.

## Cek Akhir Konsistensi

Sebelum project dianggap selesai, pastikan:

- Route name konsisten
- Role middleware bekerja
- Policy tidak bentrok
- Flash message jelas
- Redirect setelah aksi sesuai role
- Filter laporan tetap terbawa saat pagination
- Upload file tersimpan di path yang benar
- Status booking dan payment sinkron
- Dashboard quick links berfungsi
- Empty state tampil saat data kosong
