<section class="payment-card" aria-label="Footer pembayaran">
    <div class="section-heading compact">
        <p class="eyebrow">Bantuan</p>
        <h2>Langkah Selanjutnya</h2>
    </div>

    <p style="margin:0; color:#4b5563;">Setelah bukti pembayaran diupload, admin rental akan memverifikasi transaksi sebelum booking dikonfirmasi.</p>

    <div style="display:flex; gap:12px; flex-wrap:wrap; margin-top:16px;">
        <a href="{{ route('katalog.index') }}" class="btn btn-outline">Kembali ke Katalog</a>
        <a href="{{ route('booking.create', $booking->vehicle) }}" class="btn btn-outline">Kembali ke Booking</a>
    </div>
</section>
