<aside class="catalog-filter" aria-label="Filter kendaraan" aria-hidden="true" hidden>
    <div class="filter-panel-head">
        <div>
            <h3>Filter Kendaraan</h3>
            <p>Saring hasil sesuai kebutuhan Anda.</p>
        </div>

        <button type="button" class="filter-close-btn" data-filter-close aria-label="Tutup filter">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>

    <form class="filter-card" action="{{ route('katalog.index') }}" method="get">
        <input type="hidden" name="q" value="{{ request('q') }}">
        <input type="hidden" name="sort" value="{{ request('sort', 'newest') }}">

        <div class="filter-block">
            <h3>Kategori</h3>
            <select name="category">
                <option value="">Semua Kategori</option>
                @foreach ($categories as $category)
                    <option value="{{ $category }}" @selected(request('category') === $category)>{{ $category }}</option>
                @endforeach
            </select>
        </div>

        <div class="filter-block">
            <h3>Transmisi</h3>
            <select name="transmission">
                <option value="">Semua Transmisi</option>
                @foreach ($transmissions as $transmission)
                    <option value="{{ $transmission }}" @selected(request('transmission') === $transmission)>{{ $transmission }}</option>
                @endforeach
            </select>
        </div>

        <div class="filter-block">
            <h3>Bahan Bakar</h3>
            <select name="fuel_type">
                <option value="">Semua Bahan Bakar</option>
                @foreach ($fuelTypes as $fuelType)
                    <option value="{{ $fuelType }}" @selected(request('fuel_type') === $fuelType)>{{ $fuelType }}</option>
                @endforeach
            </select>
        </div>

        <div class="filter-block">
            <h3>Kapasitas Kursi</h3>
            <select name="seat_capacity">
                <option value="">Semua Kapasitas</option>
                @foreach ($seatCapacities as $seatCapacity)
                    <option value="{{ $seatCapacity }}" @selected((string) request('seat_capacity') === (string) $seatCapacity)>{{ $seatCapacity }} Kursi</option>
                @endforeach
            </select>
        </div>

        <div class="filter-block">
            <h3>Harga per Hari</h3>
            <div class="range-meta" style="display:grid; gap:10px;">
                <input type="number" name="price_min" value="{{ request('price_min') }}" placeholder="Harga minimum">
                <input type="number" name="price_max" value="{{ request('price_max') }}" placeholder="Harga maksimum">
            </div>
        </div>

        <div class="filter-actions">
            <a href="{{ route('katalog.index') }}" class="btn btn-outline full-width">Reset Filter</a>
            <button type="submit" class="btn btn-primary full-width">Terapkan Filter</button>
        </div>
    </form>
</aside>
