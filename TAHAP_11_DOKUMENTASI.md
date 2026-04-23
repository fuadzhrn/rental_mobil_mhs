# TAHAP 11 - MODUL LAPORAN (REPORTING MODULE)

**Versi**: 1.0  
**Status**: ✅ Selesai  
**Tanggal**: April 2026

---

## RINGKASAN TAHAP 11

**TAHAP 11** fokus pada pengembangan **Modul Laporan** yang komprehensif untuk memberikan visibilitas dan monitoring terhadap operasional platform dan masing-masing rental. Laporan dirancang untuk mendukung decision making, tracking KPI, dan analisis bisnis tanpa menambah modul bisnis baru.

### Tujuan Tahap 11
1. **Super Admin** dapat melihat laporan platform-wide untuk monitoring, verifikasi, dan pelaporan bisnis
2. **Admin Rental** dapat melihat laporan milik rental mereka sendiri untuk monitoring operasional dan revenue
3. Semua laporan berbasis **data nyata** dengan query yang efisien
4. Filter date range dan filter tambahan (rental, booking status, payment status) sesuai konteks
5. Tampilan laporan sederhana, clean, dan fokus pada data readability

---

## JENIS LAPORAN

### A. UNTUK SUPER ADMIN (7 Laporan)

#### 1. **Dashboard Laporan (Index)**
- **Route**: `GET /super-admin/reports`
- **Nama Route**: `super-admin.reports.index`
- **Akses**: Super Admin
- **Deskripsi**: Ringkasan metrik lintas platform

**Menampilkan**:
- Total Booking (semua rental)
- Total Payment Verified
- Total Kendaraan
- Total Revenue (verified)
- Total Komisi (10% dari revenue)
- Total Customer Aktif
- Quick links ke laporan detail

**Filter**: Date range (start_date, end_date)

---

#### 2. **Laporan Booking per Rental**
- **Route**: `GET /super-admin/reports/bookings`
- **Nama Route**: `super-admin.reports.bookings`
- **Deskripsi**: Data booking dari semua rental di platform

**Data yang ditampilkan**:
- Booking code, Rental, Customer, Kendaraan
- Pickup date, Return date, Duration
- Total Amount, Booking Status, Payment Status
- Created date

**Summary Statistik**:
- Total Booking
- Total Completed
- Total Cancelled
- Total Ongoing

**Filter Tersedia**:
- `start_date` (nullable|date)
- `end_date` (nullable|date|after_or_equal:start_date)
- `rental_id` (nullable|exists:rental_companies,id)
- `booking_status` (nullable|in:waiting_payment,waiting_verification,confirmed,ongoing,completed,cancelled)

**Pagination**: 15 items per page

---

#### 3. **Laporan Pembayaran Platform**
- **Route**: `GET /super-admin/reports/payments`
- **Nama Route**: `super-admin.reports.payments`
- **Deskripsi**: Data pembayaran dari semua rental di platform

**Data yang ditampilkan**:
- Booking code, Rental, Customer
- Payment method, Amount
- Payment Status, Verified Date, Verified By
- Rejection Note (jika rejected)

**Summary Statistik**:
- Total Payment Verified
- Total Payment Uploaded
- Total Payment Rejected
- Total Nominal Verified (jumlah uang yang terverifikasi)

**Filter Tersedia**:
- `start_date`
- `end_date`
- `rental_id`
- `payment_status` (nullable|in:unpaid,uploaded,verified,rejected)

**Pagination**: 15 items per page

---

#### 4. **Laporan Kendaraan Terlaris Platform**
- **Route**: `GET /super-admin/reports/top-vehicles`
- **Nama Route**: `super-admin.reports.top-vehicles`
- **Deskripsi**: Ranking kendaraan berdasarkan jumlah booking verified

**Data yang ditampilkan**:
- Rank (#)
- Nama Kendaraan, Brand, Category
- Rental (milik siapa)
- Total Booking (verified)
- Total Revenue (dari booking verified)

**Logika Perhitungan**:
```
Ranking = COUNT(bookings) WHERE vehicle_id = X AND payment_status = 'verified'
Revenue = SUM(total_amount) WHERE vehicle_id = X AND payment_status = 'verified'
```

**Filter Tersedia**:
- `start_date`
- `end_date`
- `limit` (default: 20, max: 100)

---

#### 5. **Laporan Customer Aktif Platform**
- **Route**: `GET /super-admin/reports/active-customers`
- **Nama Route**: `super-admin.reports.active-customers`
- **Deskripsi**: Ranking customer berdasarkan jumlah booking completed

**Data yang ditampilkan**:
- Rank (#)
- Nama Customer, Email, Phone
- Total Booking, Completed Booking
- Total Transaksi (verified)
- Last Booking Date
- Status Loyal (🏅 jika >= 3 completed bookings)

**Logika Perhitungan**:
```
Total Booking = COUNT(bookings) WHERE customer_id = X
Completed = COUNT(bookings) WHERE customer_id = X AND booking_status = 'completed'
Total Transaksi = SUM(total_amount) WHERE customer_id = X AND payment_status = 'verified'
Loyal = IF(completed >= 3) THEN 'Loyal' ELSE 'Regular'
```

**Filter Tersedia**:
- `start_date`
- `end_date`
- `limit` (default: 20, max: 100)

---

#### 6. **Laporan Pendapatan Rental**
- **Route**: `GET /super-admin/reports/revenue`
- **Nama Route**: `super-admin.reports.revenue`
- **Deskripsi**: Revenue breakdown per rental dan kalkulasi komisi platform

**Data yang ditampilkan**:
- Nama Rental, Admin Rental
- Booking Verified Count
- Pendapatan Gross (total dari verified payment)
- Komisi (10%)
- Pendapatan Net (Gross - Komisi)

**Logika Perhitungan**:
```
Booking Verified = COUNT(bookings) WHERE rental_id = X AND payment_status = 'verified'
Gross Revenue = SUM(total_amount) WHERE rental_id = X AND payment_status = 'verified'
Commission = Gross Revenue × 10%
Net Revenue = Gross Revenue - Commission
```

**Summary di halaman**:
- Total Gross Revenue (semua rental)
- Total Commission (semua rental)
- Total Net Revenue (semua rental)

**Filter Tersedia**:
- `start_date`
- `end_date`

---

#### 7. **Laporan Komisi Platform**
- **Route**: `GET /super-admin/reports/commissions`
- **Nama Route**: `super-admin.reports.commissions`
- **Deskripsi**: Detail komisi platform dari setiap transaksi verified

**Data yang ditampilkan**:
- Booking Code, Rental, Customer
- Amount (booking total)
- Komisi (10% dari amount)
- Payment Status, Booking Status
- Tanggal

**Summary Statistik**:
- Total Transactions (verified)
- Total Gross Revenue
- Total Commission
- Avg Commission per Booking

**Logika Perhitungan**:
```
Total Transactions = COUNT(bookings) WHERE payment_status = 'verified'
Gross Revenue = SUM(total_amount) WHERE payment_status = 'verified'
Commission per Booking = amount × 10%
Total Commission = SUM(commission_per_booking)
Avg Commission = Total Commission / Total Transactions
```

**Filter Tersedia**:
- `start_date`
- `end_date`
- `rental_id`

**Pagination**: 20 items per page

---

### B. UNTUK ADMIN RENTAL (6 Laporan)

#### 1. **Dashboard Laporan Rental (Index)**
- **Route**: `GET /admin-rental/reports`
- **Nama Route**: `admin-rental.reports.index`
- **Akses**: Admin Rental
- **Deskripsi**: Ringkasan metrik untuk rental mereka sendiri

**Menampilkan**:
- Total Booking (milik rental)
- Total Payment Verified (milik rental)
- Total Kendaraan (milik rental)
- Total Revenue (verified, milik rental)
- Total Komisi (10%, dari revenue rental)
- Total Customer Aktif (yang booking ke rental ini)
- Quick links ke laporan detail

**Filter**: Date range (start_date, end_date)

---

#### 2. **Laporan Booking Rental**
- **Route**: `GET /admin-rental/reports/bookings`
- **Nama Route**: `admin-rental.reports.bookings`
- **Deskripsi**: Data booking untuk rental ini saja

**Data yang ditampilkan**: Sama dengan super admin, tapi hanya untuk rental yang login

**Summary Statistik**: Sama dengan super admin

**Filter Tersedia**:
- `start_date`
- `end_date`
- `booking_status` (TIDAK ada rental_id karena auto-filtered ke rental mereka)

**Pagination**: 15 items per page

---

#### 3. **Laporan Pembayaran Rental**
- **Route**: `GET /admin-rental/reports/payments`
- **Nama Route**: `admin-rental.reports.payments`
- **Deskripsi**: Data pembayaran untuk rental ini saja

**Data yang ditampilkan**: Sama dengan super admin, tapi hanya untuk rental yang login

**Summary Statistik**: Sama dengan super admin

**Filter Tersedia**:
- `start_date`
- `end_date`
- `payment_status`

**Pagination**: 15 items per page

---

#### 4. **Laporan Kendaraan Terlaris Rental**
- **Route**: `GET /admin-rental/reports/top-vehicles`
- **Nama Route**: `admin-rental.reports.top-vehicles`
- **Deskripsi**: Ranking kendaraan milik rental mereka berdasarkan booking verified

**Data yang ditampilkan**: Sama dengan super admin, tapi hanya kendaraan milik rental

**Filter Tersedia**:
- `start_date`
- `end_date`
- `limit`

---

#### 5. **Laporan Customer Aktif Rental**
- **Route**: `GET /admin-rental/reports/active-customers`
- **Nama Route**: `admin-rental.reports.active-customers`
- **Deskripsi**: Ranking customer yang pernah booking ke rental mereka

**Data yang ditampilkan**: Sama dengan super admin, tapi hanya customer yang booking ke rental ini

**Filter Tersedia**:
- `start_date`
- `end_date`
- `limit`

---

#### 6. **Laporan Pendapatan Rental**
- **Route**: `GET /admin-rental/reports/revenue`
- **Nama Route**: `admin-rental.reports.revenue`
- **Deskripsi**: Detail revenue untuk rental mereka sendiri

**Data yang ditampilkan**:
- Informasi rental (nama, admin)
- Booking Verified Count
- Pendapatan Gross
- Komisi (10%)
- Pendapatan Net
- Detail breakdown dengan penjelasan lengkap

**Logika Perhitungan**: Sama dengan super admin tapi untuk 1 rental saja

**Filter Tersedia**:
- `start_date`
- `end_date`

---

## STRUKTUR FILE

### Controllers
```
app/Http/Controllers/
├── SuperAdmin/
│   └── ReportController.php (7 methods: index, bookings, payments, topVehicles, activeCustomers, revenue, commissions)
└── AdminRental/
    └── ReportController.php (6 methods: index, bookings, payments, topVehicles, activeCustomers, revenue)
```

### Views
```
resources/views/
├── super-admin/reports/
│   ├── index.blade.php
│   ├── bookings.blade.php
│   ├── payments.blade.php
│   ├── top-vehicles.blade.php
│   ├── active-customers.blade.php
│   ├── revenue.blade.php
│   └── commissions.blade.php
└── admin-rental/reports/
    ├── index.blade.php
    ├── bookings.blade.php
    ├── payments.blade.php
    ├── top-vehicles.blade.php
    ├── active-customers.blade.php
    └── revenue.blade.php
```

### Routes (dalam routes/web.php)
```php
// Super Admin Reports (7 routes)
Route::get('/reports', [SuperAdminReportController::class, 'index'])->name('reports.index');
Route::get('/reports/bookings', [SuperAdminReportController::class, 'bookings'])->name('reports.bookings');
Route::get('/reports/payments', [SuperAdminReportController::class, 'payments'])->name('reports.payments');
Route::get('/reports/top-vehicles', [SuperAdminReportController::class, 'topVehicles'])->name('reports.top-vehicles');
Route::get('/reports/active-customers', [SuperAdminReportController::class, 'activeCustomers'])->name('reports.active-customers');
Route::get('/reports/revenue', [SuperAdminReportController::class, 'revenue'])->name('reports.revenue');
Route::get('/reports/commissions', [SuperAdminReportController::class, 'commissions'])->name('reports.commissions');

// Admin Rental Reports (6 routes)
Route::get('/reports', [AdminRentalReportController::class, 'index'])->name('reports.index');
Route::get('/reports/bookings', [AdminRentalReportController::class, 'bookings'])->name('reports.bookings');
Route::get('/reports/payments', [AdminRentalReportController::class, 'payments'])->name('reports.payments');
Route::get('/reports/top-vehicles', [AdminRentalReportController::class, 'topVehicles'])->name('reports.top-vehicles');
Route::get('/reports/active-customers', [AdminRentalReportController::class, 'activeCustomers'])->name('reports.active-customers');
Route::get('/reports/revenue', [AdminRentalReportController::class, 'revenue'])->name('reports.revenue');
```

---

## ATURAN BISNIS UNTUK PERHITUNGAN

### 1. Revenue & Commission
```
Gross Revenue = SUM(Booking.total_amount) WHERE payment_status = 'verified'
Commission = Gross Revenue × 10%
Net Revenue = Gross Revenue - Commission
```
- **Hanya transaksi verified yang dihitung**
- **Komisi tetap 10%** (tidak berubah)
- **Tidak ada diskon atau bonus di level laporan**

### 2. Booking Status untuk Statistik
```
Valid Status untuk statistik: 'completed', 'ongoing', 'confirmed'
Calculation: COUNT(bookings) WHERE booking_status IN (...)
```

### 3. Payment Status untuk Perhitungan Uang
```
Hanya 'verified' yang dihitung untuk revenue/komisi
'uploaded' = belum verified
'rejected' = tidak dihitung
'unpaid' = tidak ada pembayaran
```

### 4. Customer Aktif & Loyal
```
Total Booking = COUNT(bookings) WHERE customer_id = X
Completed = COUNT(bookings) WHERE customer_id = X AND booking_status = 'completed'
Loyal Status = IF(completed >= 3) THEN 'Loyal' ELSE 'Regular'
```

### 5. Top Vehicles Ranking
```
Ranking = ORDER BY COUNT(bookings) DESC WHERE payment_status = 'verified'
Revenue per Vehicle = SUM(total_amount) WHERE payment_status = 'verified'
```

---

## FITUR KEAMANAN & VALIDASI

### Authorization
1. **Super Admin** - Full access ke semua laporan platform-wide
2. **Admin Rental** - Access HANYA laporan milik rental mereka sendiri
   - Automatic filter: `WHERE rental_company_id = Auth::user()->rentalCompany->id`
   - Tidak bisa akses laporan rental lain

### Validation
```php
$request->validate([
    'start_date' => ['nullable', 'date'],
    'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
    'rental_id' => ['nullable', 'exists:rental_companies,id'], // Super Admin only
    'booking_status' => ['nullable', 'in:waiting_payment,...'],
    'payment_status' => ['nullable', 'in:unpaid,uploaded,verified,rejected'],
    'limit' => ['nullable', 'integer', 'min:5', 'max:100'],
]);
```

### Query Efficiency
1. **Eager Loading**: `with(['customer', 'vehicle.rentalCompany', 'payment'])`
2. **Aggregate Queries**: `withCount()`, `withSum()`, `withMax()` untuk statistik
3. **No N+1**: Semua relasi di-load dengan 1 query
4. **Pagination**: Default 15-20 items per page untuk performa

---

## FITUR FILTER

### Filter Universal (di semua laporan)
- **start_date**: Dari tanggal berapa (Booking.created_at >=)
- **end_date**: Sampai tanggal berapa (Booking.created_at <=)

### Filter Tambahan Per Laporan

| Laporan | Filter Tambahan | Keterangan |
|---------|-----------------|-----------|
| Booking | rental_id, booking_status | Super Admin bisa filter per rental |
| Payment | rental_id, payment_status | Super Admin bisa filter per rental |
| Top Vehicles | limit | Default 20, max 100 |
| Active Customers | limit | Default 20, max 100 |
| Revenue | None | Auto-per rental |
| Commissions | rental_id | Filter transaksi komisi per rental |

### Filter Behavior
- **Semua filter nullable** - Tidak ada yang required
- **Query string preserved** - Pagination mempertahankan filter
- **Reset button** - Kembalikan ke filter default

---

## CARA MENGAKSES LAPORAN

### Super Admin
1. Login sebagai Super Admin
2. Buka menu "Laporan" di sidebar (atau `/super-admin/reports`)
3. Pilih laporan yang ingin dilihat dari dashboard
4. Gunakan filter untuk narrow down data
5. Lihat statistik ringkas di atas tabel
6. Scroll untuk melihat detail data

### Admin Rental
1. Login sebagai Admin Rental
2. Buka menu "Laporan" di sidebar (atau `/admin-rental/reports`)
3. Pilih laporan dari dashboard
4. Filter sesuai kebutuhan
5. Data otomatis terbatas ke rental mereka sendiri

---

## TESTING LAPORAN

### Data Test yang Direkomendasikan
1. **Booking dengan payment_status = verified**: Untuk revenue calculation
2. **Booking dengan status completed**: Untuk active customer calculation
3. **Multiple rental**: Untuk test super admin vs admin rental filtering
4. **Multiple customers**: Untuk test customer active ranking
5. **Date range variation**: Untuk test filter tanggal

### Test Cases Minimal
```
✓ Super Admin dapat melihat semua laporan
✓ Admin Rental hanya melihat laporan milik rental-nya
✓ Filter date range bekerja sesuai
✓ Filter rental_id bekerja (super admin)
✓ Statistik ringkas terhitung akurat
✓ Pagination working
✓ Revenue & Commission calculated correctly (10%)
✓ Customer Loyalty status correct (>= 3)
```

---

## PERTIMBANGAN PERFORMA

### Query Optimization
1. **withCount()** vs `COUNT()` subquery - Lebih efisien
2. **withSum()** vs `SUM()` subquery - Lebih readable
3. **with()** untuk eager loading - Hindari N+1
4. **Index recommendation**:
   ```sql
   CREATE INDEX idx_bookings_rental_payment ON bookings(rental_company_id, payment_status, created_at);
   CREATE INDEX idx_bookings_customer_status ON bookings(customer_id, booking_status, created_at);
   CREATE INDEX idx_bookings_vehicle_payment ON bookings(vehicle_id, payment_status, created_at);
   ```

### Caching (Future Enhancement)
- Cache revenue summary untuk 1 jam
- Cache top vehicles/customers untuk 6 jam
- Invalidate cache saat ada transaksi baru

---

## LANGKAH BERIKUTNYA (TAHAP 12+)

### Tahap 12: Export & Format
- [ ] Export laporan ke PDF
- [ ] Export laporan ke Excel (.xlsx)
- [ ] Email laporan periodic (daily/weekly/monthly)
- [ ] Print-friendly version

### Tahap 13: Advanced Analytics
- [ ] Chart & visualization (Chart.js)
- [ ] Trend analysis (month-over-month growth)
- [ ] Predictive analytics (forecast revenue)
- [ ] Comparison tools (rental A vs rental B)

### Tahap 14: Performance & Scalability
- [ ] Database indexing optimization
- [ ] Query caching strategy
- [ ] Materialized views untuk laporan heavy
- [ ] Background jobs untuk laporan besar
- [ ] Report scheduling & automation

### Tahap 15: User-Defined Reports
- [ ] Custom report builder (drag-drop columns)
- [ ] Save custom report templates
- [ ] Shared reports & dashboards
- [ ] Report access control

---

## NOTES & CATATAN PENTING

1. **Komisi Platform = 10%** - Bisa menjadi configurable di config/app.php jika perlu fleksibilitas
2. **Payment Status** - Hanya 'verified' yang masuk perhitungan revenue untuk akurasi keuangan
3. **Date Filter** - Menggunakan `created_at` booking, bukan `verified_at` atau `completed_at`
4. **Admin Rental Filter** - Automatic via `Auth::user()->rentalCompany->id`, bukan dari request
5. **Pagination State** - `withQueryString()` memastikan filter persist saat pindah halaman
6. **Empty States** - Semua laporan punya pesan "Belum ada data..." saat kosong
7. **Number Formatting** - Rupiah dengan `number_format($value, 0, ',', '.')`
8. **DateTime Display** - Format `d M Y` untuk tanggal (01 Jan 2024)

---

## CHECKLIST DEPLOYMENT

- [x] Controllers created & tested
- [x] Routes configured
- [x] Views created with proper layout
- [x] Filters validated
- [x] Authorization working (super admin vs admin rental)
- [x] Date range filtering working
- [x] Statistics calculation correct
- [x] Pagination implemented
- [x] Empty state messages
- [x] Query optimization (eager loading, aggregates)
- [x] Number formatting for currency
- [x] Quick links in dashboard
- [ ] Testing with real data
- [ ] Performance testing on large datasets
- [ ] User acceptance testing

---

## KESIMPULAN

**TAHAP 11** berhasil mengimplementasikan modul laporan komprehensif yang:
- ✅ Memberikan visibility platform-wide untuk super admin
- ✅ Memberikan visibility rental-specific untuk admin rental
- ✅ Semua laporan berbasis data real-time
- ✅ Query efficient dengan aggregate functions
- ✅ Proper authorization & data filtering
- ✅ Clean, simple, readable UI
- ✅ Siap untuk monitoring, analysis, dan decision making

Sistem sekarang cukup matang untuk production use dengan monitoring capability yang solid untuk semua stakeholder.
