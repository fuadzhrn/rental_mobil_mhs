<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Promo extends Model
{
    use HasFactory;

    public const DISCOUNT_PERCENT = 'percent';
    public const DISCOUNT_FIXED = 'fixed';

    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';

    protected $fillable = [
        'rental_company_id',
        'title',
        'promo_code',
        'description',
        'discount_type',
        'discount_value',
        'min_transaction',
        'start_date',
        'end_date',
        'quota',
        'used_count',
        'loyal_only',
        'status',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'min_transaction' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'quota' => 'integer',
        'used_count' => 'integer',
        'loyal_only' => 'boolean',
    ];

    public function rentalCompany(): BelongsTo
    {
        return $this->belongsTo(RentalCompany::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function getDiscountLabelAttribute(): string
    {
        if ($this->discount_type === self::DISCOUNT_PERCENT) {
            return rtrim(rtrim(number_format((float) $this->discount_value, 2, '.', ''), '0'), '.') . '%';
        }

        return 'Rp ' . number_format((float) $this->discount_value, 0, ',', '.');
    }
}
