<section class="booking-card" aria-label="Detail pemesanan">
    <div class="section-heading compact">
        <p class="eyebrow">Detail Sewa</p>
        <h2>Detail Pemesanan</h2>
    </div>

    <div class="booking-form-grid two-column">
        <div class="field-group">
            <label for="mulai-sewa">Tanggal Mulai Sewa</label>
            <input type="date" id="mulai-sewa" value="2026-04-22">
        </div>

        <div class="field-group">
            <label for="selesai-sewa">Tanggal Selesai Sewa</label>
            <input type="date" id="selesai-sewa" value="2026-04-24">
        </div>

        <div class="field-group">
            <label for="durasi-sewa">Durasi Sewa</label>
            <select id="durasi-sewa">
                <option>2 Hari</option>
                <option>3 Hari</option>
                <option>5 Hari</option>
                <option>7 Hari</option>
            </select>
        </div>

        <div class="field-group">
            <label for="jam-pickup">Jam Pengambilan</label>
            <input type="time" id="jam-pickup" value="09:00">
        </div>

        <div class="field-group full-span">
            <label for="pickup">Lokasi Pengambilan</label>
            <select id="pickup">
                <option>Cabang Jakarta Selatan</option>
                <option>Cabang Jakarta Pusat</option>
                <option>Bandara Soekarno-Hatta</option>
            </select>
        </div>

        <div class="field-group full-span">
            <label for="return">Lokasi Pengembalian</label>
            <select id="return">
                <option>Cabang Jakarta Selatan</option>
                <option>Cabang Jakarta Pusat</option>
                <option>Bandara Soekarno-Hatta</option>
            </select>
        </div>

        <div class="field-group full-span">
            <label>Opsi Driver</label>
            <div class="radio-pill-group">
                <label class="radio-pill"><input type="radio" name="driver" checked> Tanpa Sopir</label>
                <label class="radio-pill"><input type="radio" name="driver"> Dengan Sopir</label>
            </div>
        </div>

        <div class="field-group full-span">
            <label for="catatan-booking">Catatan Pemesanan</label>
            <textarea id="catatan-booking" rows="3" placeholder="Tambahkan kebutuhan khusus jika ada."></textarea>
        </div>
    </div>
</section>
