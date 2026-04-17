<section class="quick-search-section" id="form-cari" aria-label="Form pencarian cepat kendaraan">
    <div class="container">
        <form class="quick-search-card" action="#" method="get">
            <div class="field-group">
                <label for="lokasi">Lokasi</label>
                <select id="lokasi" name="lokasi">
                    <option>Jakarta</option>
                    <option>Bandung</option>
                    <option>Surabaya</option>
                    <option>Yogyakarta</option>
                </select>
            </div>

            <div class="field-group">
                <label for="tanggal-sewa">Tanggal Sewa</label>
                <input type="date" id="tanggal-sewa" name="tanggal_sewa" value="2026-04-20">
            </div>

            <div class="field-group">
                <label for="durasi">Durasi</label>
                <select id="durasi" name="durasi">
                    <option>1 Hari</option>
                    <option>2 Hari</option>
                    <option>3 Hari</option>
                    <option>5 Hari</option>
                    <option>7 Hari</option>
                </select>
            </div>

            <div class="field-group">
                <label for="jenis">Jenis Kendaraan</label>
                <select id="jenis" name="jenis_kendaraan">
                    <option>City Car</option>
                    <option>MPV</option>
                    <option>SUV</option>
                    <option>Luxury</option>
                </select>
            </div>

            <div class="field-group field-submit">
                <button type="submit" class="btn btn-primary full-width">Cari</button>
            </div>
        </form>
    </div>
</section>
