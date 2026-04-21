<section class="detail-main-info-card" aria-label="Informasi utama kendaraan">
    <p class="eyebrow">Detail Kendaraan Premium</p>
    <h1>{{ $vehicle->name }}</h1>

    <div class="badges-row">
        <span class="badge category">{{ $vehicle->category }}</span>
        <span class="badge status available">Tersedia</span>
    </div>

    <p class="rental-company">Disediakan oleh <strong>{{ $vehicle->rentalCompany?->company_name }}</strong></p>

    <div class="meta-grid">
        <div class="meta-item"><i class="fa-solid fa-location-dot"></i><span>{{ $vehicle->rentalCompany?->city }}</span></div>
        <div class="meta-item"><i class="fa-solid fa-gears"></i><span>Transmisi {{ $vehicle->transmission }}</span></div>
        <div class="meta-item"><i class="fa-solid fa-user-group"></i><span>{{ $vehicle->seat_capacity }} Penumpang</span></div>
        <div class="meta-item"><i class="fa-solid fa-gas-pump"></i><span>{{ $vehicle->fuel_type }}</span></div>
    </div>

    <div class="price-highlight">
        <p>Harga sewa mulai dari</p>
        <strong>Rp {{ number_format($vehicle->price_per_day, 0, ',', '.') }} <span>/ hari</span></strong>
    </div>

    <div style="margin-top:14px; background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:10px 12px;">
        <p style="margin:0; font-size:13px; color:#64748b;">Rating kendaraan</p>
        <p style="margin:6px 0 0; font-weight:700; color:#0f172a;">
            {{ $totalReviews > 0 ? number_format($averageRating, 1) : '-' }} / 5
            <span style="font-weight:500; color:#64748b;">({{ $totalReviews }} ulasan)</span>
        </p>
    </div>
</section>
