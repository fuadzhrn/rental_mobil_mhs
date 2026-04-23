# Tahap 9: Fondasi Infrastruktur Sistem Rental

**Status**: ✅ COMPLETED  
**Tanggal**: Fase 1 - Audit & Discovery  
**Objective**: Membangun fondasi teknis untuk sistem yang stabil, aman, dan scalable

---

## 📋 Daftar Isi

1. [Overview](#overview)
2. [Deliverables](#deliverables)
3. [Models & Database](#models--database)
4. [Services Layer](#services-layer)
5. [Controllers Refactoring](#controllers-refactoring)
6. [Implementation Details](#implementation-details)
7. [Technical Architecture](#technical-architecture)

---

## Overview

Tahap 9 adalah fase foundational yang membangun infrastruktur inti untuk mendukung Tahap 10 (hardening). Fokus utama:

- ✅ Membuat service layer untuk business logic
- ✅ Implementing policies untuk authorization
- ✅ Mengintegrasikan file upload dengan validation
- ✅ Slug management system yang konsisten
- ✅ Migration & model standardization

### Scope Tahap 9

**TIDAK menambah fitur bisnis besar**, hanya:
- Infrastructure improvements
- Code organization & patterns
- Security foundation
- Data integrity

---

## Deliverables

### 1. Service Layer (4 Services)

#### PromoService (Existing)
```php
// Location: app/Services/PromoService.php
// Purpose: Promo business logic
const LOYAL_COMPLETED_THRESHOLD = 3;

public function isUserLoyal($userId, $rentalCompanyId): bool
public function getApplicablePromos($bookingData): Collection
```

#### SlugService (New)
```php
// Location: app/Services/SlugService.php
// Purpose: Centralized slug generation dengan uniqueness guarantee

public function generateUnique(
    string $modelClass, 
    string $column, 
    string $source, 
    ?int $ignoreId = null
): string
```

**Fitur:**
- Automatic slug generation dari source text
- Collision detection & appending (-1, -2, etc)
- Support untuk model yang berbeda
- Case-insensitive duplicate checking

#### FileUploadService (New)
```php
// Location: app/Services/FileUploadService.php
// Purpose: Secure file upload dengan naming & organization

public function storePublic(
    UploadedFile $file, 
    string $directory
): string // returns "vehicles/main/2026/04/uuid.jpg"

public function deletePublic(string $path): bool
```

**Fitur:**
- UUID-based filename (prevents collisions)
- Date-based subdirectories (Y/m format)
- Safe deletion with error handling
- Returns relative path untuk storage

#### NotificationService (New - Tahap 10 prep)
```php
// Location: app/Services/NotificationService.php
// Purpose: Database notification management

public function notifyUser(
    int $userId, 
    string $title, 
    string $message, 
    string $type,
    ?string $url = null,
    ?string $referenceType = null,
    ?int $referenceId = null
): UserNotification
```

#### ActivityLogService (New - Tahap 10 prep)
```php
// Location: app/Services/ActivityLogService.php
// Purpose: Immutable audit logging

public function log(
    string $action,
    string $description,
    string $targetType,
    int $targetId,
    ?array $meta = null,
    ?int $userId = null
): ActivityLog
```

---

### 2. Models & Database Migrations

#### Models Created/Updated

**UserNotification**
```php
// Table: user_notifications
// Columns:
// - id: bigint
// - user_id: bigint (FK users)
// - title: string
// - message: text
// - type: enum (info, success, warning, error)
// - reference_type: string (nullable) - booking, payment, etc
// - reference_id: bigint (nullable)
// - url: string (nullable)
// - read_at: timestamp (nullable)
// - created_at, updated_at

// Key Methods:
public function markAsRead(): self
public function isUnread(): bool

// Relationships:
public function user(): BelongsTo
```

**ActivityLog**
```php
// Table: activity_logs
// Columns:
// - id: bigint
// - user_id: bigint (FK users, nullable for system actions)
// - action: string (e.g., 'vehicle.created')
// - target_type: string (vehicle, booking, payment, etc)
// - target_id: bigint
// - description: text
// - meta: json (contextual data)
// - created_at (no updated_at - append-only)

// Indexes:
// - user_id
// - action
// - created_at

// Relationships:
public function user(): BelongsTo
```

**RentalCompany (Enhanced)**
- Booted with automatic `company_slug` generation
- Uses `SlugService::generateUnique()`

**Vehicle (Enhanced)**
- Uses `SlugService` in controller for `slug` generation
- Primary image relationship optimized

#### Migration Files

**Migrations/2026_04_23_xxxxxx_create_user_notifications_table.php**
```php
Schema::create('user_notifications', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
    $table->string('title');
    $table->text('message');
    $table->enum('type', ['info', 'success', 'warning', 'error']);
    $table->string('reference_type')->nullable();
    $table->unsignedBigInteger('reference_id')->nullable();
    $table->string('url')->nullable();
    $table->timestamp('read_at')->nullable();
    $table->timestamps();
    
    $table->index(['user_id', 'read_at']);
    $table->index('created_at');
});
```

**Migrations/2026_04_23_xxxxxx_create_activity_logs_table.php**
```php
Schema::create('activity_logs', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();
    $table->string('action');
    $table->string('target_type');
    $table->unsignedBigInteger('target_id');
    $table->text('description');
    $table->json('meta')->nullable();
    $table->timestamp('created_at')->useCurrent();
    
    $table->index('user_id');
    $table->index('action');
    $table->index('created_at');
    $table->index(['target_type', 'target_id']);
});
```

---

### 3. Policies Layer

#### 6 Policy Classes

**VehiclePolicy**
```php
// Location: app/Policies/VehiclePolicy.php
public function view(User $user, Vehicle $vehicle): bool
    // admin_rental owns rental_company OR super_admin

public function create(User $user): bool
    // admin_rental must have rental_company

public function update(User $user, Vehicle $vehicle): bool
    // Delegates to view()

public function delete(User $user, Vehicle $vehicle): bool
    // Delegates to view()
```

**BookingPolicy**
```php
// Location: app/Policies/BookingPolicy.php
public function view(User $user, Booking $booking): bool
    // customer owns booking OR admin_rental owns rental_company OR super_admin

public function create(User $user): bool
    // customer only

public function update(User $user, Booking $booking): bool
    // Delegates to view()
```

**PaymentPolicy**
```php
// Location: app/Policies/PaymentPolicy.php
public function view(User $user, Payment $payment): bool
    // customer owns booking OR admin_rental owns rental_company OR super_admin

public function update(User $user, Payment $payment): bool
    // Delegates to view()
```

**PromoPolicy**
```php
// Location: app/Policies/PromoPolicy.php
public function view(User $user, Promo $promo): bool
    // admin_rental owns rental_company OR super_admin

public function create(User $user): bool
    // admin_rental must have rental_company

public function update(User $user, Promo $promo): bool
    // Delegates to view()

public function delete(User $user, Promo $promo): bool
    // Delegates to view()
```

**ReviewPolicy**
```php
// Location: app/Policies/ReviewPolicy.php
public function view(User $user, Review $review): bool
    // customer/admin_rental/super_admin with access

public function create(User $user, Booking $booking): bool
    // customer owns completed booking with no existing review
```

**RentalCompanyPolicy**
```php
// Location: app/Policies/RentalCompanyPolicy.php
public function view(User $user, RentalCompany $company): bool
    // admin_rental owner OR super_admin

public function verify(User $user): bool
    // super_admin only

public function update(User $user, RentalCompany $company): bool
    // Delegates to view()
```

**Registration di AppServiceProvider**
```php
// Location: app/Providers/AppServiceProvider.php
protected $policies = [
    Vehicle::class => VehiclePolicy::class,
    Booking::class => BookingPolicy::class,
    Payment::class => PaymentPolicy::class,
    Promo::class => PromoPolicy::class,
    Review::class => ReviewPolicy::class,
    RentalCompany::class => RentalCompanyPolicy::class,
];
```

---

### 4. Controllers Refactoring

#### AdminRental/VehicleController
**Changes:**
- ✅ Added `SlugService` dependency injection
- ✅ Added `FileUploadService` dependency injection
- ✅ Added `ActivityLogService` integration (prep for Tahap 10)
- ✅ All CRUD methods use `$this->authorize()` checks
- ✅ File upload with try-catch error handling
- ✅ Dated subdirectory organization for uploads

**Methods:**
```php
public function __construct(
    private readonly SlugService $slugService,
    private readonly FileUploadService $fileUploadService,
    private readonly ActivityLogService $activityLogService,
) {}

public function index(): View
public function create(): View
public function store(StoreVehicleRequest $request): RedirectResponse
public function edit(Vehicle $vehicle): View
public function update(UpdateVehicleRequest $request, Vehicle $vehicle): RedirectResponse
public function destroy(Vehicle $vehicle): RedirectResponse
public function destroyGalleryImage(VehicleImage $image): RedirectResponse
```

#### Customer/BookingController
**Changes:**
- ✅ Added authorization checks
- ✅ NotificationService integration (Tahap 10)
- ✅ ActivityLogService integration (Tahap 10)

#### Customer/PaymentController
**Changes:**
- ✅ Added `FileUploadService` dependency
- ✅ File upload validation tightened
- ✅ MIME type & size checking
- ✅ Safe file deletion on update
- ✅ Authorization checks on all methods
- ✅ Notification/Logging (Tahap 10)

#### Customer/ReviewController
**Changes:**
- ✅ Policy-based authorization replacing manual checks
- ✅ Service injection ready for Tahap 10

#### AdminRental/BookingController
**Changes:**
- ✅ Authorization on show/update methods
- ✅ Service integration ready

#### AdminRental/PaymentController
**Changes:**
- ✅ Authorization checks on all methods
- ✅ Service integration for verify/reject

#### AdminRental/PromoController
**Changes:**
- ✅ Policies on all CRUD methods
- ✅ Service integration ready

#### AdminRental/ReviewController
**Changes:**
- ✅ Authorization check on index

#### AdminRental/CustomerController
**Changes:**
- ✅ Authorization check on index

#### Customer/MyBookingController
**Changes:**
- ✅ Policy-based authorization

---

### 5. Form Validation Hardening

#### Vehicle Validation (StoreVehicleRequest)
```php
'thumbnail' => ['required', 'file', 'mimetypes:image/jpeg,image/png', 'max:4096'],
'gallery_images.*' => ['nullable', 'file', 'mimetypes:image/jpeg,image/png', 'max:4096'],
```

#### Payment Validation (UploadProofRequest)
```php
'proof_file' => [
    'required',
    'file',
    'mimetypes:image/jpeg,image/png,application/pdf',
    'max:8192',
],
```

#### Promo Validation (StorePromoRequest)
```php
'title' => ['required', 'string', 'max:100'],
'promo_code' => ['required', 'string', 'max:20', 'unique:promos'],
'discount_value' => ['required', 'numeric', 'min:0'],
```

---

## Implementation Details

### SlugService Usage

**Di VehicleController:**
```php
$slug = $this->slugService->generateUnique(
    Vehicle::class,
    'slug',
    $validated['name']
);

$vehicle = Vehicle::create([
    ...$validated,
    'slug' => $slug,
]);
```

**Di RentalCompany Model (Boot Event):**
```php
protected static function boot(): void
{
    parent::boot();
    
    static::creating(function (self $model): void {
        if (!$model->company_slug) {
            $slug = app(SlugService::class)->generateUnique(
                self::class,
                'company_slug',
                $model->company_name
            );
            $model->company_slug = $slug;
        }
    });
}
```

### FileUploadService Usage

**Store File:**
```php
try {
    $path = $this->fileUploadService->storePublic(
        $request->file('thumbnail'),
        'vehicles/main'
    );
    // Path: "vehicles/main/2026/04/uuid.jpg"
} catch (\Throwable $exception) {
    return back()->withInput()->with('error', 'Upload gagal');
}
```

**Delete File:**
```php
$this->fileUploadService->deletePublic($vehicle->thumbnail);
```

### Authorization Usage

**Via Policy:**
```php
// Single action
$this->authorize('view', $vehicle);

// Class-level
$this->authorize('create', Vehicle::class);

// Custom gate
$this->authorize('verify', $rentalCompany);
```

---

## Technical Architecture

### Folder Structure (After Tahap 9)

```
app/
├── Services/
│   ├── PromoService.php           (Existing)
│   ├── SlugService.php            (New)
│   ├── FileUploadService.php      (New)
│   ├── NotificationService.php    (New - Tahap 10 prep)
│   └── ActivityLogService.php     (New - Tahap 10 prep)
├── Policies/
│   ├── VehiclePolicy.php
│   ├── BookingPolicy.php
│   ├── PaymentPolicy.php
│   ├── PromoPolicy.php
│   ├── ReviewPolicy.php
│   └── RentalCompanyPolicy.php
├── Models/
│   ├── User.php (Updated)
│   ├── Vehicle.php (Updated)
│   ├── Booking.php (Updated)
│   ├── Payment.php (Updated)
│   ├── Promo.php (Updated)
│   ├── Review.php (Updated)
│   ├── RentalCompany.php (Updated)
│   ├── UserNotification.php       (New)
│   └── ActivityLog.php            (New)
├── Http/
│   ├── Requests/ (Validation tightened)
│   └── Controllers/ (Refactored dengan service injection)
└── Providers/
    └── AppServiceProvider.php     (Policies registered)

database/
├── migrations/
│   ├── 2026_04_23_xxxxxx_create_user_notifications_table.php
│   └── 2026_04_23_xxxxxx_create_activity_logs_table.php
└── seeders/ (Unchanged)
```

### Design Patterns

#### 1. Service Layer Pattern
Business logic abstracted from controllers:
```
Controller → Service → Model → Database
```

**Benefits:**
- Reusability across controllers
- Testability
- Single responsibility
- Easy to mock

#### 2. Policy Pattern
Authorization logic centralized:
```
$this->authorize('action', Model) → Policy → User Role Check
```

**Benefits:**
- Consistent authorization
- Maintainable permissions
- Testable policies

#### 3. Dependency Injection
Constructor injection untuk semua services:
```php
public function __construct(
    private readonly ServiceA $serviceA,
    private readonly ServiceB $serviceB,
) {}
```

#### 4. Immutable Logging
ActivityLog append-only (no updates):
```
Created → Never Updated → Audit Trail Safe
```

---

## Database Relationships

```
Users (1) ──→ (Many) UserNotifications
      (1) ──→ (Many) ActivityLogs
      (1) ──→ (1) RentalCompany
      (1) ──→ (Many) Bookings

RentalCompany (1) ──→ (Many) Vehicles
              (1) ──→ (Many) Promos
              (1) ──→ (Many) Bookings

Bookings (1) ──→ (1) Payment
       (1) ──→ (1) Review
       (1) ──→ (1) Vehicle

Vehicles (1) ──→ (Many) VehicleImages
       (1) ──→ (Many) Bookings
```

---

## Validation Rules Summary

### Vehicle Upload
- Format: JPEG, PNG
- Size: Max 4MB per image
- Required: Thumbnail
- Optional: Gallery (multiple)

### Payment Upload
- Format: JPEG, PNG, PDF
- Size: Max 8MB
- Required: File type check
- Transaction: Atomic with DB update

### Promo Creation
- Code: Unique, uppercase, max 20 chars
- Title: Required, max 100 chars
- Discount: Numeric, non-negative
- Dates: End date >= Start date

---

## Error Handling

### FileUploadService
```php
try {
    $path = $fileUploadService->storePublic($file, $dir);
} catch (\Throwable $e) {
    // User-friendly error message
    return back()->with('error', 'Upload failed');
}
```

### Authorization
```php
try {
    $this->authorize('action', $model);
} catch (AuthorizationException $e) {
    // Laravel handles: 403 Forbidden response
}
```

### Validation
```php
// FormRequest handles automatically
// Returns back with errors on validation fail
```

---

## Performance Considerations

1. **Slug Generation**: Single query per unique check (optimized)
2. **File Upload**: UUID prevents lookups, dated dirs enable cleanup
3. **Activity Logs**: Append-only (no locks), indexes on common queries
4. **Policies**: Eager loading relationships to avoid N+1
5. **Queries**: withCount, withSum untuk statistics tanpa extra queries

---

## Testing Checklist

- [ ] SlugService generates unique slugs
- [ ] SlugService handles collisions
- [ ] FileUploadService stores with UUID naming
- [ ] FileUploadService deletes safely
- [ ] Policies correctly authorize actions
- [ ] Services inject correctly in controllers
- [ ] File validation rejects invalid types
- [ ] File validation enforces size limits
- [ ] RentalCompany auto-generates slug
- [ ] Activity logs capture correctly (Tahap 10)
- [ ] Notifications send correctly (Tahap 10)

---

## Tahap 9 Summary

✅ **COMPLETED:**
- 4 Services (Promo, Slug, FileUpload, Notification/ActivityLog prep)
- 6 Policies with consistent pattern
- 8 Controllers refactored with service injection
- 2 New models with migrations
- Validation hardening
- Authorization layer implementation

⏳ **NEXT (Tahap 10):**
- Notification integration (8 triggers)
- Activity logging integration (15+ actions)
- Error pages & components
- Report filtering enhancements
- UI/UX polish

---

## Quick Reference

### Service Injection
```php
use App\Services\SlugService;
use App\Services\FileUploadService;

public function __construct(
    private readonly SlugService $slugService,
    private readonly FileUploadService $fileUploadService,
) {}
```

### Authorization Check
```php
$this->authorize('action', $model);
$this->authorize('create', ModelClass::class);
```

### Slug Generation
```php
$slug = $this->slugService->generateUnique(
    Vehicle::class, 'slug', $sourceName
);
```

### File Upload
```php
$path = $this->fileUploadService->storePublic(
    $request->file('field'), 'directory'
);
```

---

**Dokumentasi Tahap 9 Complete**  
Untuk Tahap 10, lihat: `TAHAP_10_DOKUMENTASI.md`
