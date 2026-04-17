<section class="payment-card invoice-card" aria-label="Invoice bukti transaksi">
    <div class="section-heading compact">
        <p class="eyebrow">Dokumen</p>
        <h2>Invoice / Bukti Transaksi</h2>
    </div>

    <div class="invoice-list">
        <div><span>No. Invoice</span><strong>INV-VLR-2026-0417</strong></div>
        <div><span>Tanggal Transaksi</span><strong>17 Apr 2026</strong></div>
        <div><span>Nama Penyewa</span><strong>Rendy Saputra</strong></div>
        <div><span>Kendaraan</span><strong>BMW 320i Sport</strong></div>
        <div><span>Rental</span><strong>Velora Signature Fleet</strong></div>
        <div><span>Metode Pembayaran</span><strong>Transfer Bank BCA</strong></div>
        <div><span>Status Transaksi</span><strong class="status-text">Menunggu Verifikasi</strong></div>
    </div>

    <div class="invoice-total">
        <span>Total Pembayaran</span>
        <strong>Rp 3.950.000</strong>
    </div>

    <div class="invoice-actions">
        <a href="{{ route('pembayaran.invoice') }}" class="btn btn-outline full-width">Download Invoice</a>
        <a href="{{ route('pembayaran.cetak') }}" class="btn btn-primary full-width">Cetak Bukti Transaksi</a>
    </div>
</section>
