<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    use HasFactory;

    public const BOOKING_PENDING = 'pending';
    public const BOOKING_WAITING_PAYMENT = 'waiting_payment';
    public const BOOKING_WAITING_VERIFICATION = 'waiting_verification';
    public const BOOKING_CONFIRMED = 'confirmed';
    public const BOOKING_ONGOING = 'ongoing';
    public const BOOKING_COMPLETED = 'completed';
    public const BOOKING_CANCELLED = 'cancelled';

    public const PAYMENT_UNPAID = 'unpaid';
    public const PAYMENT_UPLOADED = 'uploaded';
    public const PAYMENT_VERIFIED = 'verified';
    public const PAYMENT_REJECTED = 'rejected';

    public static function statusOptions(): array
    {
        return [
            self::BOOKING_WAITING_PAYMENT => 'Menunggu Pembayaran',
            self::BOOKING_WAITING_VERIFICATION => 'Menunggu Verifikasi',
            self::BOOKING_CONFIRMED => 'Dikonfirmasi',
            self::BOOKING_ONGOING => 'Sedang Berjalan',
            self::BOOKING_COMPLETED => 'Selesai',
            self::BOOKING_CANCELLED => 'Dibatalkan',
        ];
    }

    public static function paymentStatusOptions(): array
    {
        return [
            self::PAYMENT_UNPAID => 'Belum Bayar',
            self::PAYMENT_UPLOADED => 'Uploaded',
            self::PAYMENT_VERIFIED => 'Verified',
            self::PAYMENT_REJECTED => 'Rejected',
        ];
    }

    public function getBookingStatusLabelAttribute(): string
    {
        return self::statusOptions()[$this->booking_status] ?? ucfirst((string) $this->booking_status);
    }

    public function getPaymentStatusLabelAttribute(): string
    {
        return self::paymentStatusOptions()[$this->payment_status] ?? ucfirst((string) $this->payment_status);
    }

    public function bookingStatusLabel(): string
    {
        return $this->booking_status_label;
    }

    public function paymentStatusLabel(): string
    {
        return $this->payment_status_label;
    }

    protected $fillable = [
        'booking_code',
        'customer_id',
        'rental_company_id',
        'vehicle_id',
        'promo_id',
        'pickup_date',
        'return_date',
        'pickup_time',
        'pickup_location',
        'return_location',
        'duration_days',
        'with_driver',
        'customer_name',
        'customer_email',
        'customer_phone',
        'customer_address',
        'identity_number',
        'driver_license_number',
        'note',
        'subtotal',
        'discount_amount',
        'additional_cost',
        'total_amount',
        'booking_status',
        'payment_status',
    ];

    protected $casts = [
        'pickup_date' => 'date',
        'return_date' => 'date',
        'pickup_time' => 'datetime:H:i',
        'duration_days' => 'integer',
        'with_driver' => 'boolean',
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'additional_cost' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function getRouteKeyName(): string
    {
        return 'booking_code';
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function rentalCompany(): BelongsTo
    {
        return $this->belongsTo(RentalCompany::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function promo(): BelongsTo
    {
        return $this->belongsTo(Promo::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }
}
