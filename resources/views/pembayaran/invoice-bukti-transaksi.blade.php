<section class="payment-card invoice-card" aria-label="Invoice bukti transaksi">
    <div class="section-heading compact">
        <p class="eyebrow">Dokumen</p>
        <h2>Invoice / Bukti Transaksi</h2>
    </div>

    <div class="invoice-list">
        <div><span>Nomor Invoice</span><strong>{{ $booking->booking_code }}</strong></div>
        <div><span>Tanggal Transaksi</span><strong>{{ optional($payment->paid_at ?? $booking->created_at)->format('d M Y') }}</strong></div>
        <div><span>Nama Penyewa</span><strong>{{ $booking->customer_name }}</strong></div>
        <div><span>Nama Kendaraan</span><strong>{{ $booking->vehicle->name }}</strong></div>
        <div><span>Nama Rental</span><strong>{{ $booking->vehicle->rentalCompany?->company_name }}</strong></div>
        <div><span>Metode Pembayaran</span><strong>{{ $paymentMethods[$payment->payment_method]['label'] ?? $payment->payment_method }}</strong></div>
        <div>
            <span>Status Transaksi</span>
            <strong><span class="invoice-status-chip">{{ $payment->status_label }}</span></strong>
        </div>
    </div>

    <div class="invoice-total">
        <span>Total Pembayaran</span>
        <strong>Rp {{ number_format($booking->total_amount, 0, ',', '.') }}</strong>
    </div>

    <div class="invoice-actions">
        <a href="{{ route('pembayaran.receipt', $booking) }}" class="btn btn-primary full-width">Lihat Bukti Transaksi</a>
        <a href="{{ route('pembayaran.invoice', $booking) }}" class="btn btn-outline full-width">Lihat Invoice</a>
    </div>
</section>
