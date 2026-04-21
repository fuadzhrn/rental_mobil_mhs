@php
    $primaryImagePath = $vehicle->primaryImage?->image_path ?: $vehicle->images->first()?->image_path;
    $vehicleImage = $vehicle->main_image
        ? asset('storage/' . $vehicle->main_image)
        : ($primaryImagePath ? asset('storage/' . $primaryImagePath) : null);
@endphp

<section class="booking-card vehicle-summary-card" aria-label="Data kendaraan yang dipilih">
    <div class="section-heading compact">
        <p class="eyebrow">Kendaraan Dipilih</p>
        <h2>Data Kendaraan</h2>
    </div>

    <div class="vehicle-summary">
        @if ($vehicleImage)
            <img src="{{ $vehicleImage }}" alt="{{ $vehicle->name }}">
        @else
            <div style="width:100%; min-height:220px; display:flex; align-items:center; justify-content:center; background:#e5e7eb; color:#6b7280; border-radius:20px;">
                No Image
            </div>
        @endif

        <div class="vehicle-summary-content">
            <div class="vehicle-summary-top">
                <h3>{{ $vehicle->name }}</h3>
                <span class="status-badge available">{{ ucfirst($vehicle->status) }}</span>
            </div>
            <p class="rental-name">{{ $vehicle->rentalCompany?->company_name }}</p>
            <div class="summary-meta-row">
                <span><i class="fa-solid fa-car-side"></i> {{ $vehicle->category }}</span>
                <span><i class="fa-solid fa-location-dot"></i> {{ $vehicle->rentalCompany?->city ?? '-' }}</span>
                <span><i class="fa-solid fa-star"></i> Rental terverifikasi</span>
            </div>
            <div class="summary-price">
                <strong>Rp {{ number_format($vehicle->price_per_day, 0, ',', '.') }}</strong>
                <span>/ hari</span>
            </div>
        </div>
    </div>
</section>
