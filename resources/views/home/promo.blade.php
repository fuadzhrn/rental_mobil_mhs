<section class="section section-soft home-highlight-section" id="promo" aria-label="Promo rental">
    <div class="container section-container-wide">
        <div class="section-heading">
            <p class="eyebrow">Penawaran Spesial</p>
            <h2>Promo Menarik untuk Perjalanan Lebih Hemat</h2>
            <p>Manfaatkan promo pilihan agar pengalaman sewa kendaraan semakin menguntungkan.</p>
        </div>

        <div class="promo-grid">
            @forelse($promos as $promo)
                <article class="promo-card">
                    <p class="promo-label">{{ $promo->promo_code }}</p>
                    <h3>{{ $promo->title }}</h3>
                    <p>
                        @if($promo->discount_type === 'percent')
                            Dapatkan diskon {{ $promo->discount_value }}% untuk setiap transaksi.
                        @else
                            Dapatkan potongan Rp {{ number_format($promo->discount_value, 0, ',', '.') }} untuk setiap transaksi.
                        @endif
                    </p>
                    <div style="font-size: 0.85rem; color: #666; margin-top: 8px;">
                        Berlaku s/d {{ \Carbon\Carbon::parse($promo->end_date)->format('d M Y') }}
                    </div>
                    <a href="#" class="promo-link" data-promo="{{ $promo->promo_code }}">
                        Klaim Promo <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </article>
            @empty
                <p style="grid-column: 1 / -1; text-align: center; padding: 40px 0;">Tidak ada promo aktif saat ini. Pantau terus untuk penawaran menarik!</p>
            @endforelse
        </div>
    </div>
</section>
