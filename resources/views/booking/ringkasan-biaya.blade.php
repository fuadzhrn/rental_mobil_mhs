@php
    use Carbon\Carbon;

    $summaryPickupDate = old('pickup_date', $pickupDate ?? now()->addDay()->toDateString());
    $summaryReturnDate = old('return_date', $returnDate ?? now()->addDays(2)->toDateString());
    try {
        $pickupCarbon = Carbon::parse($summaryPickupDate);
        $returnCarbon = Carbon::parse($summaryReturnDate);
        $summaryDurationDays = max(1, $pickupCarbon->diffInDays($returnCarbon) + 1);
    } catch (Throwable $exception) {
        $summaryDurationDays = $durationDays ?? 1;
    }

    $summaryPricePerDay = (float) $vehicle->price_per_day;
    $summarySubtotal = $summaryDurationDays * $summaryPricePerDay;
    $summaryAdditionalCost = 0;
    
    // Estimasi diskon berdasarkan promo code yang diinput
    $summaryDiscountAmount = 0;
    $estimatedPromoApplied = false;
    $promoCode = old('promo_code', '');
    
    if (!empty($promoCode) && isset($availablePromos)) {
        $appliedPromo = $availablePromos->firstWhere(function($p) use ($promoCode) {
            return strtoupper($p->promo_code) === strtoupper($promoCode);
        });
        
        if ($appliedPromo && $appliedPromo->can_use) {
            if ($appliedPromo->discount_type === 'percent') {
                $summaryDiscountAmount = ($appliedPromo->discount_value / 100) * $summarySubtotal;
            } else {
                $summaryDiscountAmount = min($appliedPromo->discount_value, $summarySubtotal);
            }
            $estimatedPromoApplied = true;
        }
    }
    
    $summaryTotalAmount = $summarySubtotal + $summaryAdditionalCost - $summaryDiscountAmount;
@endphp

<section class="booking-card cost-summary-card" aria-label="Ringkasan biaya">
    <div class="section-heading compact">
        <p class="eyebrow">Ringkasan</p>
        <h2>Biaya Pemesanan</h2>
    </div>

    <div class="cost-list">
        <div><span>Harga sewa per hari</span><strong>Rp {{ number_format($summaryPricePerDay, 0, ',', '.') }}</strong></div>
        <div><span>Jumlah hari</span><strong>{{ $summaryDurationDays }} Hari</strong></div>
        <div><span>Subtotal</span><strong>Rp {{ number_format($summarySubtotal, 0, ',', '.') }}</strong></div>
        <div><span>Biaya tambahan</span><strong>Rp {{ number_format($summaryAdditionalCost, 0, ',', '.') }}</strong></div>
        @if ($summaryDiscountAmount > 0)
            <div style="background:#ecfdf5; border-left:4px solid #059669; padding:8px;">
                <span style="color:#065f46;">Diskon {{ $estimatedPromoApplied ? '(estimasi)' : '' }}</span>
                <strong style="color:#059669;">- Rp {{ number_format($summaryDiscountAmount, 0, ',', '.') }}</strong>
            </div>
        @else
            <div><span>Diskon voucher</span><strong>- Rp {{ number_format(0, 0, ',', '.') }}</strong></div>
        @endif
    </div>

    <div class="total-box">
        <span>Total Pembayaran</span>
        <strong>Rp {{ number_format($summaryTotalAmount, 0, ',', '.') }}</strong>
    </div>

    @if ($estimatedPromoApplied)
        <p style="margin-top:12px; padding:10px; background:#fef3c7; color:#b45309; border-radius:8px; font-size:13px;">
            ℹ️ Diskon promo ditampilkan sebagai estimasi. Nilai final akan dihitung ulang di backend saat booking disimpan.
        </p>
    @else
        <p style="margin-top:12px; color:#6b7280; font-size:13px;">Total final dihitung ulang di backend saat booking disimpan.</p>
    @endif
</section>
