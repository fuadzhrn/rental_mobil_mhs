<section class="search-area" id="search-box" aria-label="Pencarian kendaraan">
    <div class="container">
        <div class="search-card">
            <div class="search-heading">
                <p class="eyebrow">Katalog Kendaraan</p>
                <h1>Temukan Kendaraan Sesuai Kebutuhan Anda</h1>
                <p>Gunakan pencarian dan filter untuk menemukan kendaraan terbaik dari rental terpercaya.</p>
            </div>

            <form action="{{ route('katalog.index') }}" method="get" class="search-form-horizontal">
                <div class="field-group field-keyword">
                    <label for="kata-kunci">Kata Kunci</label>
                    <input type="text" id="kata-kunci" name="q" value="{{ request('q') }}" placeholder="Contoh: Toyota, SUV, Avanza">
                </div>

                @foreach (['category', 'transmission', 'fuel_type', 'seat_capacity', 'price_min', 'price_max', 'sort'] as $hiddenField)
                    @if (request()->filled($hiddenField))
                        <input type="hidden" name="{{ $hiddenField }}" value="{{ request($hiddenField) }}">
                    @endif
                @endforeach

                <div class="field-group field-submit">
                    <button class="btn btn-primary full-width" type="submit">Cari</button>
                </div>
            </form>
        </div>
    </div>
</section>
