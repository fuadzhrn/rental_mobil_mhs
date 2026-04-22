<?php

namespace App\Models;

use App\Services\SlugService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RentalCompany extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::creating(function (self $rentalCompany): void {
            if (!$rentalCompany->company_slug && $rentalCompany->company_name) {
                $rentalCompany->company_slug = app(SlugService::class)->generateUnique(self::class, 'company_slug', $rentalCompany->company_name);
            }
        });

        static::updating(function (self $rentalCompany): void {
            if ($rentalCompany->isDirty('company_name')) {
                $rentalCompany->company_slug = app(SlugService::class)->generateUnique(self::class, 'company_slug', (string) $rentalCompany->company_name, $rentalCompany->id);
            }
        });
    }

    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'user_id',
        'company_name',
        'company_slug',
        'description',
        'address',
        'city',
        'phone',
        'email',
        'logo',
        'status_verification',
        'verified_by',
        'verified_at',
        'rejection_note',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function promos(): HasMany
    {
        return $this->hasMany(Promo::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status_verification', self::STATUS_APPROVED);
    }
}
