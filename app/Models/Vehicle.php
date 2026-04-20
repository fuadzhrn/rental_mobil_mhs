<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehicle extends Model
{
    use HasFactory;

    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';
    public const STATUS_MAINTENANCE = 'maintenance';

    protected $fillable = [
        'rental_company_id',
        'name',
        'slug',
        'brand',
        'type',
        'category',
        'year',
        'transmission',
        'fuel_type',
        'seat_capacity',
        'luggage_capacity',
        'color',
        'price_per_day',
        'description',
        'terms_conditions',
        'status',
        'main_image',
    ];

    protected $casts = [
        'year' => 'integer',
        'seat_capacity' => 'integer',
        'price_per_day' => 'decimal:2',
    ];

    public function rentalCompany(): BelongsTo
    {
        return $this->belongsTo(RentalCompany::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(VehicleImage::class);
    }

    public function primaryImage(): HasOne
    {
        return $this->hasOne(VehicleImage::class)->where('is_primary', true);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeVisibleToCustomers(Builder $query): Builder
    {
        return $query->active()->whereHas('rentalCompany', function (Builder $rentalQuery): void {
            $rentalQuery->approved();
        });
    }
}
