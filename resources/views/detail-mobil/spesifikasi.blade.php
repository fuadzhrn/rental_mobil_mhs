<section class="detail-section" aria-label="Spesifikasi kendaraan">
    <div class="container">
        <div class="section-heading">
            <p class="eyebrow">Spesifikasi</p>
            <h2>Spesifikasi Kendaraan</h2>
        </div>

        <div class="spec-grid">
            <article class="spec-item"><i class="fa-solid fa-copyright"></i><span>Brand / Type</span><strong>{{ $vehicle->brand }} / {{ $vehicle->type }}</strong></article>
            <article class="spec-item"><i class="fa-regular fa-calendar"></i><span>Tahun</span><strong>{{ $vehicle->year }}</strong></article>
            <article class="spec-item"><i class="fa-solid fa-gears"></i><span>Transmisi</span><strong>{{ $vehicle->transmission }}</strong></article>
            <article class="spec-item"><i class="fa-solid fa-gas-pump"></i><span>Bahan Bakar</span><strong>{{ $vehicle->fuel_type }}</strong></article>
            <article class="spec-item"><i class="fa-solid fa-user-group"></i><span>Kapasitas</span><strong>{{ $vehicle->seat_capacity }} Penumpang</strong></article>
            <article class="spec-item"><i class="fa-solid fa-suitcase-rolling"></i><span>Kapasitas Bagasi</span><strong>{{ $vehicle->luggage_capacity ?? '-' }}</strong></article>
            <article class="spec-item"><i class="fa-solid fa-palette"></i><span>Warna</span><strong>{{ $vehicle->color ?? '-' }}</strong></article>
            <article class="spec-item"><i class="fa-solid fa-tag"></i><span>Status</span><strong>{{ ucfirst($vehicle->status) }}</strong></article>
        </div>
    </div>
</section>
