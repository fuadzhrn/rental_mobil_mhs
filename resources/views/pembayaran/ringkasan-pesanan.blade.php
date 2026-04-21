@php
    $vehicleImage = $booking->vehicle->main_image
        ? asset('storage/' . $booking->vehicle->main_image)
        : ($booking->vehicle->primaryImage?->image_path
            ? asset('storage/' . $booking->vehicle->primaryImage->image_path)
            : null);
@endphp

<section class="payment-card order-summary-card" aria-label="Ringkasan pesanan">
    <div class="section-heading compact">
        <p class="eyebrow">Ringkasan Booking</p>
        <h2>Ringkasan Pesanan</h2>
    </div>

    <div class="order-summary-head">
        @if ($vehicleImage)
            <img src="{{ $vehicleImage }}" alt="{{ $booking->vehicle->name }}">
        @else
            <div style="width:88px; height:88px; border-radius:16px; background:#e5e7eb;"></div>
        @endif
        <div>
            <h3>{{ $booking->vehicle->name }}</h3>
            <p>{{ $booking->vehicle->rentalCompany?->company_name }}</p>
            <span class="status-badge available">{{ ucfirst($booking->booking_status) }}</span>
        </div>
    </div>

    <div class="order-summary-list">
        <div><span>Tanggal mulai</span><strong>{{ $booking->pickup_date->format('d M Y') }}</strong></div>
        <div><span>Tanggal selesai</span><strong>{{ $booking->return_date->format('d M Y') }}</strong></div>
        <div><span>Durasi</span><strong>{{ $booking->duration_days }} Hari</strong></div>
        <div><span>Lokasi pickup</span><strong>{{ $booking->pickup_location }}</strong></div>
        <div><span>Lokasi return</span><strong>{{ $booking->return_location ?? '-' }}</strong></div>
        <div><span>Nama penyewa</span><strong>{{ $booking->customer_name }}</strong></div>
    </div>

    <div class="summary-total-box">
        <span>Total Tagihan</span>
        <strong>Rp {{ number_format($booking->total_amount, 0, ',', '.') }}</strong>
    </div>
</section>
