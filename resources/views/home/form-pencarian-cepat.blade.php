<section class="quick-search-section" id="form-cari" aria-label="Form pencarian cepat kendaraan">
    <div class="container">
        <form class="quick-search-card" action="{{ route('search') }}" method="post">
            @csrf
            <div class="field-group">
                <label for="lokasi">Lokasi</label>
                <select id="lokasi" name="lokasi">
                    <option value="">-- Pilih Lokasi --</option>
                    <option>Jakarta</option>
                    <option>Bandung</option>
                    <option>Surabaya</option>
                    <option>Yogyakarta</option>
                </select>
            </div>

            <div class="field-group">
                <label for="tanggal-sewa">Tanggal Sewa</label>
                <input type="date" id="tanggal-sewa" name="tanggal_sewa" value="{{ date('Y-m-d') }}">
            </div>

            <div class="field-group">
                <label for="durasi">Durasi</label>
                <select id="durasi" name="durasi">
                    <option value="">-- Pilih Durasi --</option>
                    <option value="1">1 Hari</option>
                    <option value="2">2 Hari</option>
                    <option value="3">3 Hari</option>
                    <option value="5">5 Hari</option>
                    <option value="7">7 Hari</option>
                </select>
            </div>

            <div class="field-group">
                <label for="jenis">Jenis Kendaraan</label>
                <select id="jenis" name="jenis_kendaraan">
                    <option value="">-- Pilih Jenis --</option>
                    @foreach($vehicleCategories as $category)
                        <option value="{{ $category }}">{{ $category }}</option>
                    @endforeach
                </select>
            </div>

            <div class="field-group field-submit">
                <button type="submit" class="btn btn-primary full-width">Cari</button>
            </div>
        </form>
    </div>
</section>
