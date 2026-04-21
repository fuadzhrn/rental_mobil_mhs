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
    $summaryTotalAmount = $summarySubtotal + $summaryAdditionalCost;
@endphp

<aside class="booking-card cta-card" aria-label="Lanjut pembayaran">
    <p class="mobile-total-info">
        <span>Total Pembayaran</span>
        <strong>Rp {{ number_format($summaryTotalAmount, 0, ',', '.') }}</strong>
    </p>

    <button type="submit" form="booking-form" class="btn btn-primary full-width">Simpan Booking &amp; Lanjut Pembayaran</button>
    <a href="{{ route('katalog.index') }}" class="btn btn-outline full-width">Kembali ke Katalog</a>
    <p style="margin-top:12px; color:#6b7280; font-size:13px;">Setelah booking tersimpan, sistem akan mengarahkan Anda ke langkah pembayaran placeholder.</p>
</aside>
