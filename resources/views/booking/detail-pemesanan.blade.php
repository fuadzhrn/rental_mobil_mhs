<section class="booking-card" aria-label="Detail pemesanan">
    <div class="section-heading compact">
        <p class="eyebrow">Detail Sewa</p>
        <h2>Detail Pemesanan</h2>
    </div>

    <div class="booking-form-grid two-column">
        <div class="field-group">
            <label for="pickup_date">Tanggal Mulai Sewa</label>
            <input type="date" id="pickup_date" name="pickup_date" value="{{ old('pickup_date', $pickupDate ?? now()->addDay()->toDateString()) }}">
            @error('pickup_date')
                <small class="field-error">{{ $message }}</small>
            @enderror
        </div>

        <div class="field-group">
            <label for="return_date">Tanggal Selesai Sewa</label>
            <input type="date" id="return_date" name="return_date" value="{{ old('return_date', $returnDate ?? now()->addDays(2)->toDateString()) }}">
            @error('return_date')
                <small class="field-error">{{ $message }}</small>
            @enderror
        </div>

        <div class="field-group">
            <label for="pickup_time">Jam Pengambilan</label>
            <input type="time" id="pickup_time" name="pickup_time" value="{{ old('pickup_time', '09:00') }}">
            @error('pickup_time')
                <small class="field-error">{{ $message }}</small>
            @enderror
        </div>

        <div class="field-group">
            <label for="pickup_location">Lokasi Pengambilan</label>
            <input type="text" id="pickup_location" name="pickup_location" value="{{ old('pickup_location') }}" placeholder="Contoh: Bandara Soekarno-Hatta / alamat lengkap">
            @error('pickup_location')
                <small class="field-error">{{ $message }}</small>
            @enderror
        </div>

        <div class="field-group full-span">
            <label for="return_location">Lokasi Pengembalian</label>
            <input type="text" id="return_location" name="return_location" value="{{ old('return_location') }}" placeholder="Opsional">
            @error('return_location')
                <small class="field-error">{{ $message }}</small>
            @enderror
        </div>

        <div class="field-group full-span">
            <label>Opsi Driver</label>
            <div class="radio-pill-group">
                <label class="radio-pill {{ old('with_driver', '0') == '0' ? 'is-active' : '' }}">
                    <input type="radio" name="with_driver" value="0" {{ old('with_driver', '0') == '0' ? 'checked' : '' }}>
                    Tanpa Sopir
                </label>
                <label class="radio-pill {{ old('with_driver') == '1' ? 'is-active' : '' }}">
                    <input type="radio" name="with_driver" value="1" {{ old('with_driver') == '1' ? 'checked' : '' }}>
                    Dengan Sopir
                </label>
            </div>
            @error('with_driver')
                <small class="field-error">{{ $message }}</small>
            @enderror
        </div>

        <div class="field-group full-span">
            <label for="note">Catatan Pemesanan</label>
            <textarea id="note" name="note" rows="3" placeholder="Tambahkan kebutuhan khusus jika ada.">{{ old('note') }}</textarea>
            @error('note')
                <small class="field-error">{{ $message }}</small>
            @enderror
        </div>
    </div>
</section>
