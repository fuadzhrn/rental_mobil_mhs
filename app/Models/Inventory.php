<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Inventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'rental_company_id',
        'vehicle_id',
        'total',
        'reserved',
        'available',
        'last_checked_by',
        'notes',
    ];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function rentalCompany(): BelongsTo
    {
        return $this->belongsTo(RentalCompany::class);
    }

    public function lastCheckedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'last_checked_by');
    }
}
