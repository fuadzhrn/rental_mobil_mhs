<section class="section home-highlight-section" id="katalog" aria-label="Kendaraan unggulan">
    <div class="container section-container-wide">
        <div class="section-heading">
            <p class="eyebrow">Katalog Pilihan</p>
            <h2>Kendaraan Unggulan Minggu Ini</h2>
            <p>Pilih unit terbaik dengan kondisi prima dan harga kompetitif.</p>
        </div>

        <div class="vehicle-grid">
            @forelse($featuredVehicles as $vehicle)
                <article class="vehicle-card">
                    @if($vehicle->main_image)
                        <img src="{{ asset('storage/' . $vehicle->main_image) }}" alt="{{ $vehicle->name }}" class="vehicle-image">
                    @elseif($vehicle->primaryImage)
                        <img src="{{ asset('storage/' . $vehicle->primaryImage->image_path) }}" alt="{{ $vehicle->name }}" class="vehicle-image">
                    @else
                        <img src="https://via.placeholder.com/1000x600?text={{ urlencode($vehicle->name) }}" alt="{{ $vehicle->name }}" class="vehicle-image">
                    @endif
                    <div class="vehicle-body">
                        <h3>{{ $vehicle->name }}</h3>
                        <p class="vehicle-type">{{ $vehicle->category }}</p>
                        <div class="vehicle-meta">
                            <strong>Rp {{ number_format($vehicle->price_per_day, 0, ',', '.') }} <span>/ hari</span></strong>
                            <span class="rating">
                                <i class="fa-solid fa-star"></i> 
                                {{ $vehicle->reviews_avg_rating ? number_format($vehicle->reviews_avg_rating, 1) : 'N/A' }}
                            </span>
                        </div>
                        <a href="{{ route('katalog.show', $vehicle->slug) }}" class="btn btn-outline full-width">Lihat Detail</a>
                    </div>
                </article>
            @empty
                <p style="grid-column: 1 / -1; text-align: center; padding: 40px 0;">Tidak ada kendaraan unggulan saat ini.</p>
            @endforelse
        </div>
    </div>
</section>
