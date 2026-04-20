<section class="detail-section" aria-label="Informasi rental">
    <div class="container">
        <article class="rental-info-card">
            <div class="section-heading">
                <p class="eyebrow">Profil Rental</p>
                <h2>Informasi Penyedia Rental</h2>
            </div>

            <div class="rental-profile">
                @if ($vehicle->rentalCompany?->logo)
                    <img src="{{ asset('storage/' . $vehicle->rentalCompany->logo) }}" alt="Logo {{ $vehicle->rentalCompany?->company_name }}">
                @else
                    <div style="width:96px; height:96px; border-radius:18px; background:#e5e7eb; display:flex; align-items:center; justify-content:center; color:#6b7280; font-size:12px;">Logo</div>
                @endif
                <div>
                    <h3>{{ $vehicle->rentalCompany?->company_name }}</h3>
                    <p>{{ $vehicle->rentalCompany?->description ?: 'Rental terverifikasi dengan unit kendaraan terawat dan layanan profesional.' }}</p>
                </div>
            </div>

            <div class="rental-meta-grid">
                <div><i class="fa-solid fa-location-dot"></i><span>{{ $vehicle->rentalCompany?->address }}, {{ $vehicle->rentalCompany?->city }}</span></div>
                <div><i class="fa-solid fa-phone"></i><span>{{ $vehicle->rentalCompany?->phone }}</span></div>
                <div><i class="fa-solid fa-envelope"></i><span>{{ $vehicle->rentalCompany?->email }}</span></div>
                <div><i class="fa-solid fa-circle-check"></i><span>{{ ucfirst($vehicle->rentalCompany?->status_verification) }}</span></div>
            </div>

            <div class="rental-stats">
                <article><strong>{{ $vehicle->rentalCompany?->vehicles()->count() ?? 0 }}</strong><span>Unit Aktif</span></article>
                <article><strong>Approved</strong><span>Status Rental</span></article>
                <article><strong>{{ $vehicle->rentalCompany?->city }}</strong><span>Kota Operasional</span></article>
                <article><strong>Terverifikasi</strong><span>Oleh Super Admin</span></article>
            </div>
        </article>
    </div>
</section>
