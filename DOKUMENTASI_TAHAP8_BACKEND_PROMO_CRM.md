# DOKUMENTASI TAHAP 8: BACKEND PROMO + CRM

## Status Implementasi
✅ **COMPLETE** - Semua fitur Tahap 8 sudah diimplementasikan dan siap testing.

---

## 1. Overview Tahap 8

Tahap 8 menambahkan dua modul besar:
1. **Promo Module**: CRUD promo per rental company dengan validasi ketat, integrasi booking flow, dan perhitungan diskon otomatis
2. **CRM Module**: Dashboard customer dengan tracking loyalitas, riwayat booking, reviews, dan targeting promo

### Aturan Bisnis Utama
- **Loyalitas**: Customer dianggap setia jika telah menyelesaikan ≥2 booking di rental yang sama
- **Promo Validation**: Validasi ketat di backend dengan transaction locking untuk prevent race condition
- **Diskon Calculation**: 100% dihitung di backend, frontend hanya menampilkan estimasi
- **Access Control**: Admin rental hanya bisa mengelola promo & customer miliknya

---

## 2. Database Layer

### Migrations Created

#### 1. `database/migrations/2026_04_21_000011_create_promos_table.php`
```sql
CREATE TABLE promos (
  id BIGINT PRIMARY KEY
  rental_company_id BIGINT (FK to rental_companies)
  title VARCHAR (255)
  promo_code VARCHAR (50) UNIQUE INDEX
  description TEXT NULLABLE
  discount_type ENUM ('percent', 'fixed')
  discount_value DECIMAL (10,2)
  min_transaction DECIMAL (12,2) NULLABLE
  start_date DATETIME
  end_date DATETIME
  quota INT NULLABLE (unlimited jika null)
  used_count INT DEFAULT 0
  loyal_only BOOLEAN DEFAULT 0
  status ENUM ('active', 'inactive')
  timestamps
  
  INDEXES:
  - (rental_company_id, status)
  - (rental_company_id, start_date, end_date)
)
```

#### 2. `database/migrations/2026_04_21_000012_add_promo_foreign_key_to_bookings_table.php`
```sql
ALTER TABLE bookings ADD COLUMN promo_id BIGINT NULLABLE
ADD FOREIGN KEY (promo_id) REFERENCES promos(id) ON DELETE SET NULL
```

---

## 3. Model Layer

### Promo Model
**Location**: `app/Models/Promo.php`

**Constants**:
```php
DISCOUNT_PERCENT = 'percent'
DISCOUNT_FIXED = 'fixed'
STATUS_ACTIVE = 'active'
STATUS_INACTIVE = 'inactive'
```

**Relations**:
- `rentalCompany()` - BelongsTo RentalCompany
- `bookings()` - HasMany Booking

**Accessor**:
- `discount_label` - Format display diskon (e.g., "50%" atau "Rp 100.000")

### Booking Model Update
**Added**:
- `promo()` - BelongsTo Promo (nullable)
- `promo_id` - Foreign key field (dalam fillable)

### RentalCompany Model Update
**Added**:
- `promos()` - HasMany Promo
- `bookings()` - HasMany Booking

---

## 4. Service Layer

### PromoService
**Location**: `app/Services/PromoService.php`

**Key Methods**:

#### `getVisiblePromosForBooking(string $promoCode, int $rentalCompanyId, int $customerId, float $subtotal, bool $lockForUpdate = false)`
- Filter promo yang bisa digunakan customer
- Return collection dengan attributes:
  - `can_use` (boolean)
  - `cannot_use_reason` (string, jika can_use = false)
  - `estimated_discount` (float)
- Validasi: status active, dalam periode, belum expired, loyal_only check, quota check

#### `resolvePromoForBooking(string $promoCode, int $rentalCompanyId, int $customerId, float $subtotal, bool $lockForUpdate = false)`
- Strict validation dengan transaction lock jika `$lockForUpdate = true`
- Throw `ValidationException` jika invalid
- Return array: `['promo' => Promo, 'discount_amount' => float]`

#### `calculateDiscount(Promo $promo, float $subtotal)`
- Handle percent vs fixed discount
- Cap diskon tidak boleh melebihi subtotal

#### `isLoyalCustomer(int $customerId, int $rentalCompanyId)`
- Check apakah customer telah menyelesaikan ≥ 2 bookings di rental
- `LOYAL_COMPLETED_THRESHOLD = 2`

---

## 5. Request Validation Layer

### StorePromoRequest
**Location**: `app/Http/Requests/StorePromoRequest.php`

**Validasi**:
- `title`: required, string, max 255
- `promo_code`: required, alpha_dash, max 50, unique:promos (per rental_company)
- `description`: nullable, string
- `discount_type`: required, in:percent,fixed
- `discount_value`: required, numeric, > 0
- `min_transaction`: nullable, numeric, >= 0
- `start_date`: required, date_format:Y-m-d H:i, before:end_date
- `end_date`: required, date_format:Y-m-d H:i
- `quota`: nullable, integer, >= 1
- `loyal_only`: nullable, boolean
- `status`: required, in:active,inactive

### UpdatePromoRequest
- Sama seperti StorePromoRequest
- `promo_code` unique ignore current promo id

### StoreBookingRequest Update
- `promo_code`: nullable, string, max 50
- Validasi di controller melalui PromoService

---

## 6. Controller Layer

### PromoController
**Location**: `app/Http/Controllers/AdminRental/PromoController.php`

**Methods** (7 total):
1. `index()` - List dengan filter search & status, pagination 10 item
2. `create()` - Show form create
3. `store()` - Save promo baru, set rental_company_id dari logged-in admin
4. `edit()` - Show form edit
5. `update()` - Update promo (promo_code read-only)
6. `destroy()` - Soft/hard delete promo
7. `toggle()` - Toggle status active/inactive

**Access Control**:
- `getRentalCompanyOrAbort()` - Ensure admin punya rental company
- `ensurePromoBelongsToRental()` - Check promo milik rental admin

**Data Normalization**:
- Promo code otomatis uppercase saat save

### CustomerController
**Location**: `app/Http/Controllers/AdminRental/CustomerController.php`

**Methods** (2 public):
1. `index()` - List customer dengan aggregates dan filter loyal/non-loyal
2. `show()` - Detail customer dengan booking history, reviews, vehicle history

**Index Query Features**:
- `withCount(['bookings as booking_count', 'bookings as completed_booking_count'])`
- `withSum('bookings as total_transaction_amount', 'total_amount')`
- `withMax('bookings as last_booking_at', 'created_at')`
- `withAvg('reviews as average_rating_given', 'rating')`
- Filter loyal: `whereIn($loyalCustomerIdSubQuery)` via robust subquery
- Filter non-loyal: `whereNotIn($loyalCustomerIdSubQuery)`
- Search: name & email
- Pagination: 10 item per halaman

**Show Query Features**:
- Bookings: eager load vehicle, payment, review, promo (paginated 8 per halaman)
- Reviews: all reviews customer di rental (sorted latest)
- Vehicles: distinct list of vehicles customer pernah rental
- Stats: bookingCount, completedCount, totalTransactionAmount, lastBookingDate

---

## 7. Route Registration

**Location**: `routes/web.php`

### Promo Routes (7 routes)
```
GET    /admin-rental/promos                    → PromoController@index       (admin-rental.promos.index)
GET    /admin-rental/promos/create             → PromoController@create      (admin-rental.promos.create)
POST   /admin-rental/promos                    → PromoController@store       (admin-rental.promos.store)
GET    /admin-rental/promos/{promo}/edit       → PromoController@edit        (admin-rental.promos.edit)
PUT    /admin-rental/promos/{promo}            → PromoController@update      (admin-rental.promos.update)
DELETE /admin-rental/promos/{promo}            → PromoController@destroy     (admin-rental.promos.destroy)
PATCH  /admin-rental/promos/{promo}/toggle    → PromoController@toggle      (admin-rental.promos.toggle)
```

### Customer CRM Routes (2 routes)
```
GET    /admin-rental/customers                 → CustomerController@index    (admin-rental.customers.index)
GET    /admin-rental/customers/{customer}      → CustomerController@show     (admin-rental.customers.show)
```

### Booking Integration
- StoreBookingRequest validation include promo_code
- BookingController@store inject PromoService
- In transaction: call resolvePromoForBooking with lock
- Increment promo.used_count saat booking saved
- Handle ValidationException → return ke form with errors

---

## 8. View Layer

### Admin Promo CRUD (4 files)

#### `resources/views/admin-rental/promos/index.blade.php`
- Table dengan kolom: code, title, discount type/value, min transaction, kuota, terpakai, periode, loyal only, status, aksi
- Search by title & promo code
- Filter by status (active/inactive)
- Pagination dengan withQueryString()
- Edit & Delete buttons per row

#### `resources/views/admin-rental/promos/form.blade.php`
- Reusable form untuk create & edit
- Fields: title, promo_code (read-only saat edit), discount_type, discount_value, min_transaction, start_date, end_date, kuota, status, description, loyal_only checkbox
- Validasi display errors from Form Request
- Submit button text dinamis (Simpan/Update)

#### `resources/views/admin-rental/promos/create.blade.php`
- Wrapper page untuk create
- Include form.blade.php dengan action POST ke store

#### `resources/views/admin-rental/promos/edit.blade.php`
- Wrapper page untuk edit
- Include form.blade.php dengan action PUT ke update

---

### Admin CRM Customer (2 files)

#### `resources/views/admin-rental/customers/index.blade.php`
- Table dengan kolom: nama, email, HP, total booking, booking selesai, total transaksi, rata-rata rating, booking terakhir, status loyalitas, aksi
- Search by name & email
- Filter: Semua Customer, Loyal (2+ booking), Non-Loyal (<2 booking)
- Show badge 🏆 untuk loyal customer
- Pagination
- Detail button link ke show page

#### `resources/views/admin-rental/customers/show.blade.php`
- 4-col stat cards: total booking, completed booking, total transaction, average rating
- 2-column layout:
  - **Left**: Data pribadi, riwayat booking (10 terbaru paginated), review yang diberikan
  - **Right**: Informasi loyalitas (status + syarat), kendaraan yang pernah dirental, rekomendasi target promo
- Booking history: code (link to detail), vehicle, tanggal, total, status
- Reviews: vehicle, booking code, rating (⭐), review text, waktu
- Vehicles: nama, brand, category (link to edit)

---

### Booking Integration (2 files updated)

#### `resources/views/booking/promo-voucher.blade.php`
- Input untuk promo_code (text field, uppercase)
- List available promos dari controller dengan:
  - Kode promo, judul, discount label
  - Tombol "Pakai" jika promo dapat digunakan
  - Pesan error jika tidak dapat digunakan (quota, expiry, etc)
  - Kuota status (X dari Y tersisa)
- Auto-fill promo code saat click "Pakai" button
- Info text: "Diskon dihitung 100% di backend"

#### `resources/views/booking/ringkasan-biaya.blade.php`
- Updated calculation untuk menampilkan estimated discount
- Jika promo_code ada di request, estimasi diskon ditampilkan
- Diskon row highlight green jika ada
- Info text: "Nilai estimasi, final dihitung di backend"

---

### Sidebar Update
**Location**: `resources/views/components/admin-rental-sidebar.blade.php`
- Updated link "Data Customer" → `route('admin-rental.customers.index')`
- Updated link "Promo" → `route('admin-rental.promos.index')`
- Active state check untuk kedua menu

---

## 9. Booking Flow Integration

### Customer Booking Create Page
**Flow**:
1. BookingController@create:
   - Get `$availablePromos` via `$this->promoService->getVisiblePromosForBooking()`
   - Pass ke view untuk render daftar promo yang bisa dipakai

2. User input promo code → ringkasan-biaya.blade.php kalkulasi estimasi diskon

3. Form submit → BookingController@store:
   - Validasi promo_code via StoreBookingRequest
   - Call `$this->promoService->resolvePromoForBooking()` dalam DB transaction dengan lockForUpdate
   - If valid → set `booking.promo_id` dan hitung `booking.discount_amount`
   - Increment `promo.used_count`
   - If ValidationException → return back with errors

---

## 10. Key Implementation Details

### Promo Code Normalization
- Automatic uppercase saat save
- Display uppercase di UI

### Validation di Backend
- StorePromoRequest: validasi format & unique per rental
- resolvePromoForBooking: validasi saat booking dengan lock untuk prevent race condition
- PromoService: bisnis logic terpusat & reusable

### Loyalitas Filter Approach
```php
// Robust subquery untuk reliable distinct count
$loyalCustomerIdSubQuery = Booking::query()
    ->select('customer_id')
    ->where('rental_company_id', $rentalCompanyId)
    ->where('booking_status', Booking::BOOKING_COMPLETED)
    ->groupBy('customer_id')
    ->havingRaw('COUNT(*) >= ?', [2]);

// Gunakan whereIn/whereNotIn (lebih reliable dari having)
$customers->whereIn('id', $loyalCustomerIdSubQuery)  // loyal
$customers->whereNotIn('id', $loyalCustomerIdSubQuery)  // non-loyal
```

### Transaction Locking
```php
DB::transaction(function () {
    $promo = Promo::where('id', $promoId)->lockForUpdate()->first();
    // Validate promo (status, dates, quota)
    $promo->increment('used_count');
    // Save booking dengan promo_id
});
```

---

## 11. Testing Checklist

### Promo Admin Module
- [ ] Create promo (valid & invalid input)
- [ ] Edit promo (cannot change promo_code)
- [ ] Delete promo
- [ ] Toggle status active/inactive
- [ ] Search by title & promo code
- [ ] Filter by status
- [ ] Pagination works
- [ ] Only see own rental's promos
- [ ] Promo code normalized uppercase

### CRM Customer Module
- [ ] List customers dengan aggregates correct
- [ ] Filter loyal (2+ completed bookings)
- [ ] Filter non-loyal
- [ ] Search by name & email
- [ ] View customer detail
- [ ] Booking history paginated correctly
- [ ] Reviews displayed correct
- [ ] Vehicles list correct
- [ ] Loyalty status calculated correct
- [ ] Only see own rental's customers

### Booking Integration
- [ ] See available promos list di create page
- [ ] List shows correct promo details & estimated discount
- [ ] Click "Pakai" auto-fill promo code
- [ ] Invalid promo shows error reason
- [ ] Expired promo shows "tidak dapat digunakan"
- [ ] Loyal-only promo filtered correctly
- [ ] Quota checks work
- [ ] ringkasan-biaya shows estimated discount
- [ ] Backend recalculate final discount saat save
- [ ] promo.used_count increment saat booking saved
- [ ] Promo validation dengan lock prevent race condition

---

## 12. File Manifest

### Created Files (15)
1. `database/migrations/2026_04_21_000011_create_promos_table.php`
2. `database/migrations/2026_04_21_000012_add_promo_foreign_key_to_bookings_table.php`
3. `app/Models/Promo.php`
4. `app/Services/PromoService.php`
5. `app/Http/Requests/StorePromoRequest.php`
6. `app/Http/Requests/UpdatePromoRequest.php`
7. `app/Http/Controllers/AdminRental/PromoController.php`
8. `app/Http/Controllers/AdminRental/CustomerController.php`
9. `resources/views/admin-rental/promos/index.blade.php`
10. `resources/views/admin-rental/promos/form.blade.php`
11. `resources/views/admin-rental/promos/create.blade.php`
12. `resources/views/admin-rental/promos/edit.blade.php`
13. `resources/views/admin-rental/customers/index.blade.php`
14. `resources/views/admin-rental/customers/show.blade.php`
15. `resources/views/booking/promo-voucher.blade.php` (updated)

### Modified Files (8)
1. `app/Models/Booking.php` - Added promo() relation
2. `app/Models/RentalCompany.php` - Added promos() & bookings() relations
3. `app/Http/Requests/StoreBookingRequest.php` - Added promo_code validation
4. `app/Http/Controllers/Customer/BookingController.php` - Integrated PromoService
5. `routes/web.php` - Registered promo & customer routes
6. `resources/views/components/admin-rental-sidebar.blade.php` - Updated menu links
7. `resources/views/booking/ringkasan-biaya.blade.php` - Display estimated discount
8. `app/Models/User.php` - Removed invalid promos() relation (fixed)

---

## 13. Next Steps (Out of Scope for Tahap 8)

- [ ] Run migration di database real
- [ ] Integration testing booking promo flow end-to-end
- [ ] Super admin module untuk approve/manage all promos
- [ ] Email notification untuk customer dapat promo baru
- [ ] Advanced CRM: email campaign, promo targeting automation
- [ ] Analytics dashboard untuk promo performance

---

## 14. Notes

- Semua promo validation 100% di backend, frontend hanya estimasi
- Admin rental access control enforce di setiap controller action
- Loyalitas logic konsisten di promo filter & CRM display
- Service pattern keep business logic terpusat & testable
- Blade templates follow existing admin rental pattern untuk consistency
