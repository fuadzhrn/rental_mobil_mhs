<section class="section home-highlight-section" aria-label="Testimoni pelanggan">
    <div class="container section-container-wide">
        <div class="section-heading">
            <p class="eyebrow">Cerita Pelanggan</p>
            <h2>Testimoni dari Pengguna VeloraRide</h2>
            <p>Ribuan pelanggan telah merasakan kemudahan rental kendaraan bersama kami.</p>
        </div>

        <div class="testimonial-grid">
            @forelse($testimonials as $review)
                <article class="testimonial-card">
                    <div class="testimonial-head">
                        <img src="https://i.pravatar.cc/80?u={{ $review->customer->email }}" alt="Foto {{ $review->customer->name }}">
                        <div>
                            <h3>{{ $review->customer->name }}</h3>
                            <p>{{ $review->vehicle->name }}</p>
                        </div>
                    </div>
                    <div class="stars" aria-label="Rating {{ $review->rating }} bintang">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= $review->rating)
                                <i class="fa-solid fa-star"></i>
                            @elseif($i - 0.5 <= $review->rating)
                                <i class="fa-solid fa-star-half-stroke"></i>
                            @else
                                <i class="fa-regular fa-star"></i>
                            @endif
                        @endfor
                    </div>
                    <p class="testimonial-text">"{{ $review->review ?? 'Pengalaman sewa yang memuaskan!' }}"</p>
                </article>
            @empty
                <p style="grid-column: 1 / -1; text-align: center; padding: 40px 0;">Belum ada testimoni pelanggan. Jadilah yang pertama berbagi pengalaman Anda!</p>
            @endforelse
        </div>
    </div>
</section>
