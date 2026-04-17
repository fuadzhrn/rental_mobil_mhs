<aside class="catalog-filter" aria-label="Filter kendaraan">
    <form class="filter-card" action="#" method="get">
        <div class="filter-block">
            <h3>Harga per Hari</h3>
            <input type="range" min="250000" max="2000000" value="850000">
            <div class="range-meta">
                <span>Rp 250.000</span>
                <strong>Max Rp 850.000</strong>
            </div>
        </div>

        <div class="filter-block">
            <h3>Jenis Kendaraan</h3>
            <label><input type="checkbox" checked> City Car</label>
            <label><input type="checkbox"> MPV</label>
            <label><input type="checkbox"> SUV</label>
            <label><input type="checkbox"> Sedan</label>
        </div>

        <div class="filter-block">
            <h3>Transmisi</h3>
            <label><input type="radio" name="transmisi" checked> Otomatis</label>
            <label><input type="radio" name="transmisi"> Manual</label>
        </div>

        <div class="filter-block">
            <h3>Kapasitas Kursi</h3>
            <select>
                <option>Semua Kapasitas</option>
                <option>4 - 5 Kursi</option>
                <option>6 - 7 Kursi</option>
                <option>8+ Kursi</option>
            </select>
        </div>

        <div class="filter-block">
            <h3>Bahan Bakar</h3>
            <label><input type="checkbox" checked> Bensin</label>
            <label><input type="checkbox"> Diesel</label>
            <label><input type="checkbox"> Hybrid</label>
            <label><input type="checkbox"> Electric</label>
        </div>

        <div class="filter-block">
            <h3>Rental / Perusahaan</h3>
            <select>
                <option>Semua Rental</option>
                <option>Velora Partner Prime</option>
                <option>UrbanDrive Rental</option>
                <option>Skyline Mobility</option>
            </select>
        </div>

        <div class="filter-block">
            <h3>Status Ketersediaan</h3>
            <label><input type="checkbox" checked> Tersedia</label>
            <label><input type="checkbox" checked> Segera Tersedia</label>
            <label><input type="checkbox"> Tidak Tersedia</label>
        </div>

        <div class="filter-actions">
            <button type="reset" class="btn btn-outline full-width">Reset Filter</button>
            <button type="submit" class="btn btn-primary full-width">Terapkan Filter</button>
        </div>
    </form>
</aside>
