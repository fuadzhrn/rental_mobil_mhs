<section class="sorting-bar" aria-label="Urutkan hasil kendaraan">
    <div class="sorting-summary">
        <button type="button" class="filter-toggle-btn" data-filter-open>
            <i class="fa-solid fa-sliders"></i>
            <span>Filter</span>
        </button>

        <p>Menampilkan <strong>{{ $summaryCount }} kendaraan</strong></p>
    </div>

    <form class="sorting-actions" action="{{ route('katalog.index') }}" method="get">
        <input type="hidden" name="q" value="{{ request('q') }}">
        <input type="hidden" name="category" value="{{ request('category') }}">
        <input type="hidden" name="transmission" value="{{ request('transmission') }}">
        <input type="hidden" name="fuel_type" value="{{ request('fuel_type') }}">
        <input type="hidden" name="seat_capacity" value="{{ request('seat_capacity') }}">
        <input type="hidden" name="price_min" value="{{ request('price_min') }}">
        <input type="hidden" name="price_max" value="{{ request('price_max') }}">

        <label for="urutkan">Urutkan:</label>
        <select id="urutkan" name="sort" onchange="this.form.submit()">
            <option value="newest" @selected($sort === 'newest')>Terbaru</option>
            <option value="price_low" @selected($sort === 'price_low')>Harga Terendah</option>
            <option value="price_high" @selected($sort === 'price_high')>Harga Tertinggi</option>
        </select>
    </form>
</section>
