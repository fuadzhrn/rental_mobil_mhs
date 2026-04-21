<section class="vehicle-listing" aria-label="Daftar kendaraan katalog">
    <div class="catalog-grid">
        @forelse ($vehicles as $vehicle)
            @php
                $galleryImage = $vehicle->images->first();
                $imagePath = $vehicle->main_image
                    ? asset('storage/' . $vehicle->main_image)
                    : ($galleryImage ? asset('storage/' . $galleryImage->image_path) : null);

                $detailUrl = route('katalog.show', $vehicle);

                $bookingUrl = auth()->guest()
                    ? route('login')
                    : (auth()->user()->role === 'customer' ? route('booking.create', $vehicle) : route('home'));

                $bookingLabel = auth()->guest()
                    ? 'Login untuk Booking'
                    : (auth()->user()->role === 'customer' ? 'Booking Sekarang' : 'Kembali ke Home');
            @endphp

            <article class="vehicle-card">
                @if ($imagePath)
                    <img src="{{ $imagePath }}" alt="{{ $vehicle->name }}" class="vehicle-image">
                @else
                    <div class="vehicle-image" style="display:flex; align-items:center; justify-content:center; background:#e5e7eb; color:#6b7280; min-height:220px;">No Image</div>
                @endif

                <div class="vehicle-body">
                    <div class="vehicle-top">
                        <h3>{{ $vehicle->name }}</h3>
                        <span class="status-badge available">Tersedia</span>
                    </div>
                    <p class="rental-name">{{ $vehicle->rentalCompany?->company_name }}</p>
                    <div class="vehicle-spec">
                        <span><i class="fa-solid fa-car-side"></i> {{ $vehicle->category }}</span>
                        <span><i class="fa-solid fa-gears"></i> {{ $vehicle->transmission }}</span>
                        <span><i class="fa-solid fa-user-group"></i> {{ $vehicle->seat_capacity }} Kursi</span>
                    </div>
                    <div class="vehicle-meta">
                        <strong>Rp {{ number_format($vehicle->price_per_day, 0, ',', '.') }} <span>/ hari</span></strong>
                        <span class="rating"><i class="fa-solid fa-location-dot"></i> {{ $vehicle->rentalCompany?->city }}</span>
                    </div>
                    <div class="vehicle-actions">
                        <a href="{{ $detailUrl }}" class="btn btn-outline">Lihat Detail</a>
                        <a href="{{ $bookingUrl }}" class="btn btn-primary">{{ $bookingLabel }}</a>
                    </div>
                </div>
            </article>
        @empty
            <div style="grid-column:1 / -1; padding:24px; background:#fff; border:1px solid #e5e7eb; border-radius:14px; text-align:center; color:#6b7280;">
                Tidak ada kendaraan yang cocok dengan filter saat ini.
            </div>
        @endforelse
    </div>
</section>
