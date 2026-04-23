# Testing Checklist

Checklist manual dan prioritas feature test untuk WebRental.

## Strategi Testing Singkat

- Prioritas utama: checklist manual per role
- Prioritas kedua: feature test untuk akses dan perhitungan penting
- Fokus pada alur bisnis utama, bukan unit test yang terlalu detail

## Checklist Customer

- [ ] Register customer baru
- [ ] Login customer
- [ ] Melihat home
- [ ] Mencari kendaraan di katalog
- [ ] Membuka detail mobil
- [ ] Klik booking
- [ ] Submit booking
- [ ] Cek bentrok tanggal booking
- [ ] Lanjut ke pembayaran
- [ ] Pilih metode pembayaran
- [ ] Upload bukti pembayaran
- [ ] Melihat status pembayaran
- [ ] Melihat riwayat booking
- [ ] Melihat detail booking
- [ ] Memberi review setelah booking completed
- [ ] Menggunakan promo valid
- [ ] Gagal memakai promo tidak valid
- [ ] Loyal customer dapat memakai promo `loyal_only`
- [ ] Customer tidak bisa melihat booking customer lain

## Checklist Admin Rental

- [ ] Login admin rental
- [ ] Melihat dashboard
- [ ] CRUD kendaraan
- [ ] Upload gambar utama kendaraan
- [ ] Upload galeri kendaraan
- [ ] Melihat booking masuk
- [ ] Melihat pembayaran masuk
- [ ] Verifikasi pembayaran
- [ ] Menolak pembayaran
- [ ] Ubah booking menjadi ongoing
- [ ] Ubah booking menjadi completed
- [ ] CRUD promo
- [ ] Melihat data customer / CRM
- [ ] Melihat loyal customer
- [ ] Melihat laporan rental
- [ ] Admin rental tidak bisa akses data rental lain

## Checklist Super Admin

- [ ] Login super admin
- [ ] Melihat dashboard
- [ ] Melihat rental pending
- [ ] Approve rental
- [ ] Reject rental
- [ ] Melihat semua user
- [ ] Melihat laporan
- [ ] Melihat komisi
- [ ] Melihat laporan booking
- [ ] Melihat laporan pembayaran
- [ ] Super admin tetap dibatasi oleh flow role jika mencoba akses area role lain

## End-to-End Flow

### Flow 1
- [ ] Rental didaftarkan
- [ ] Rental diapprove super admin
- [ ] Admin rental tambah kendaraan
- [ ] Customer login
- [ ] Customer booking kendaraan
- [ ] Customer bayar
- [ ] Admin rental verifikasi
- [ ] Admin rental ubah booking ke ongoing
- [ ] Admin rental ubah booking ke completed
- [ ] Customer memberi review

### Flow 2
- [ ] Customer booking
- [ ] Customer upload bukti pembayaran
- [ ] Admin rental reject pembayaran
- [ ] Customer upload ulang
- [ ] Admin rental verify
- [ ] Booking lanjut sampai selesai

### Flow 3
- [ ] Customer loyal menggunakan promo `loyal_only`

### Flow 4
- [ ] Super admin melihat dampak transaksi pada laporan dan komisi

## Prioritas Feature Test

### 1. Access Control
- Super admin bisa akses laporan platform
- Customer tidak bisa akses area super admin
- Admin rental tidak bisa akses laporan super admin

### 2. Commission Report
- Transaksi verified dihitung 10% komisi
- Laporan komisi menampilkan nominal yang benar

### 3. Booking Visibility
- Customer bisa melihat booking milik sendiri
- Customer tidak bisa melihat booking milik customer lain

## Command Test

```bash
php artisan test
php artisan test --filter=ReportAccessTest
php artisan test --filter=CommissionReportTest
php artisan test --filter=CustomerBookingVisibilityTest
```

## Cek Akhir Sebelum Done

- [ ] Route naming konsisten
- [ ] Redirect sesuai role
- [ ] Flash message konsisten
- [ ] Status booking dan payment sinkron
- [ ] Policy dan middleware konsisten
- [ ] Pagination membawa query string
- [ ] Empty state tampil
- [ ] Laporan memakai payment verified
- [ ] Storage link tersedia
- [ ] Deployment command sudah dicoba di staging
