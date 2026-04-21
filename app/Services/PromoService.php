<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Promo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

class PromoService
{
    public const LOYAL_COMPLETED_THRESHOLD = 2;

    public function getVisiblePromosForBooking(int $rentalCompanyId, int $customerId, float $subtotal): Collection
    {
        $today = now()->toDateString();
        $isLoyal = $this->isLoyalCustomer($customerId, $rentalCompanyId);

        $promos = Promo::query()
            ->where('rental_company_id', $rentalCompanyId)
            ->where('status', Promo::STATUS_ACTIVE)
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->where(function (Builder $query): void {
                $query->whereNull('quota')
                    ->orWhereColumn('used_count', '<', 'quota');
            })
            ->orderByDesc('id')
            ->get();

        return $promos->map(function (Promo $promo) use ($subtotal, $isLoyal): Promo {
            $canUse = true;
            $reason = null;

            if ($subtotal < (float) $promo->min_transaction) {
                $canUse = false;
                $reason = 'Subtotal belum memenuhi minimal transaksi promo.';
            }

            if ($promo->loyal_only && !$isLoyal) {
                $canUse = false;
                $reason = 'Promo khusus customer loyal.';
            }

            $promo->setAttribute('can_use', $canUse);
            $promo->setAttribute('cannot_use_reason', $reason);
            $promo->setAttribute('estimated_discount', $this->calculateDiscount($promo, $subtotal));

            return $promo;
        });
    }

    public function resolvePromoForBooking(
        ?string $promoCode,
        int $rentalCompanyId,
        int $customerId,
        float $subtotal,
        bool $lockForUpdate = false
    ): array {
        if (!$promoCode) {
            return [
                'promo' => null,
                'discount_amount' => 0.0,
            ];
        }

        $normalizedCode = strtoupper(trim($promoCode));

        $query = Promo::query()->where('promo_code', $normalizedCode);
        if ($lockForUpdate) {
            $query->lockForUpdate();
        }

        $promo = $query->first();

        if (!$promo) {
            throw ValidationException::withMessages([
                'promo_code' => 'Kode promo tidak ditemukan.',
            ]);
        }

        if ((int) $promo->rental_company_id !== $rentalCompanyId) {
            throw ValidationException::withMessages([
                'promo_code' => 'Promo tidak berlaku untuk rental kendaraan ini.',
            ]);
        }

        if ($promo->status !== Promo::STATUS_ACTIVE) {
            throw ValidationException::withMessages([
                'promo_code' => 'Promo sedang tidak aktif.',
            ]);
        }

        $today = Carbon::today();
        if ($today->lt($promo->start_date) || $today->gt($promo->end_date)) {
            throw ValidationException::withMessages([
                'promo_code' => 'Promo tidak berada dalam periode aktif.',
            ]);
        }

        if ($promo->quota !== null && $promo->used_count >= $promo->quota) {
            throw ValidationException::withMessages([
                'promo_code' => 'Kuota promo sudah habis.',
            ]);
        }

        if ($subtotal < (float) $promo->min_transaction) {
            throw ValidationException::withMessages([
                'promo_code' => 'Subtotal belum memenuhi minimal transaksi promo.',
            ]);
        }

        if ($promo->loyal_only && !$this->isLoyalCustomer($customerId, $rentalCompanyId)) {
            throw ValidationException::withMessages([
                'promo_code' => 'Promo ini hanya bisa digunakan customer loyal.',
            ]);
        }

        return [
            'promo' => $promo,
            'discount_amount' => $this->calculateDiscount($promo, $subtotal),
        ];
    }

    public function calculateDiscount(Promo $promo, float $subtotal): float
    {
        if ($subtotal <= 0) {
            return 0;
        }

        $discount = $promo->discount_type === Promo::DISCOUNT_PERCENT
            ? ($subtotal * ((float) $promo->discount_value / 100))
            : (float) $promo->discount_value;

        return min(round($discount, 2), $subtotal);
    }

    public function isLoyalCustomer(int $customerId, int $rentalCompanyId): bool
    {
        $completedCount = Booking::query()
            ->where('customer_id', $customerId)
            ->where('rental_company_id', $rentalCompanyId)
            ->where('booking_status', Booking::BOOKING_COMPLETED)
            ->count();

        return $completedCount >= self::LOYAL_COMPLETED_THRESHOLD;
    }
}
