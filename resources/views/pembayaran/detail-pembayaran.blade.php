@php
    $methodData = $paymentMethods[$activePaymentMethod] ?? reset($paymentMethods);
@endphp

<section class="payment-card" aria-label="Detail pembayaran">
    <div class="section-heading compact">
        <p class="eyebrow">Detail Transaksi</p>
        <h2>Detail Pembayaran</h2>
    </div>

    <div class="payment-detail-grid">
        <div class="detail-item important">
            <span>Nominal Pembayaran</span>
            <strong>Rp {{ number_format($booking->total_amount, 0, ',', '.') }}</strong>
        </div>

        <div class="detail-item important">
            <span>Batas Waktu</span>
            <strong>{{ now()->addHours(24)->format('d M Y, H:i') }} WIB</strong>
        </div>

        <div class="detail-item">
            <span>Nomor Rekening / Tujuan</span>
            <strong id="accountNumber">{{ $methodData['account_number'] }}</strong>
            <button type="button" class="tiny-btn" onclick="navigator.clipboard.writeText(document.getElementById('accountNumber').innerText)">Salin Nomor</button>
        </div>

        <div class="detail-item">
            <span>Nama Penerima</span>
            <strong id="receiverName">{{ $methodData['account_name'] }}</strong>
        </div>

        <div class="detail-item">
            <span>Metode Dipilih</span>
            <strong id="paymentCode">{{ $methodData['label'] }}</strong>
        </div>
    </div>

    <div class="instruction-box">
        <h3>Instruksi Singkat</h3>
        <ol>
            <li>{{ $methodData['instruction'] }}</li>
            <li>Pastikan nominal transfer sesuai total tagihan.</li>
            <li>Upload bukti pembayaran untuk mempercepat verifikasi.</li>
        </ol>
    </div>
</section>
