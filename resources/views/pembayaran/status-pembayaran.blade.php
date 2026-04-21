<section class="payment-card" aria-label="Status pembayaran">
    <div class="section-heading compact">
        <p class="eyebrow">Progress</p>
        <h2>Status Pembayaran</h2>
    </div>

    <div class="status-steps">
        <article class="step {{ $payment->payment_status === \App\Models\Payment::STATUS_UNPAID ? 'active-pending' : '' }}">
            <span class="dot"></span>
            <div>
                <h3>Belum Bayar</h3>
                <p>Pesanan sudah dibuat dan menunggu pembayaran Anda.</p>
            </div>
        </article>

        <article class="step {{ $payment->payment_status === \App\Models\Payment::STATUS_UPLOADED ? 'active-verification' : '' }}">
            <span class="dot"></span>
            <div>
                <h3>Menunggu Verifikasi</h3>
                <p>Bukti pembayaran telah diupload dan menunggu konfirmasi admin rental.</p>
            </div>
        </article>

        <article class="step {{ $payment->payment_status === \App\Models\Payment::STATUS_VERIFIED ? 'success' : '' }}">
            <span class="dot"></span>
            <div>
                <h3>Pembayaran Berhasil</h3>
                <p>Status aktif setelah pembayaran diverifikasi oleh admin rental.</p>
            </div>
        </article>
    </div>

    @if ($payment->payment_status === \App\Models\Payment::STATUS_REJECTED)
        <div style="margin-top:16px; padding:14px; border-radius:14px; background:#fef2f2; color:#991b1b;">
            <strong>Pembayaran ditolak.</strong>
            <p style="margin:6px 0 0;">{{ $payment->rejection_note ?? 'Silakan upload ulang bukti pembayaran yang valid.' }}</p>
        </div>
    @endif

    <p style="margin-top:12px; color:#6b7280; font-size:13px;">Status booking: {{ $booking->booking_status }}</p>
</section>
