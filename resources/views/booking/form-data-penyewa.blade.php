<section class="booking-card" aria-label="Form data penyewa">
    <div class="section-heading compact">
        <p class="eyebrow">Formulir</p>
        <h2>Data Penyewa</h2>
    </div>

    <div class="booking-form-grid">
        <div class="field-group">
            <label for="customer_name">Nama Lengkap</label>
            <input type="text" id="customer_name" name="customer_name" value="{{ old('customer_name', $customer->name ?? '') }}" placeholder="Masukkan nama lengkap">
            @error('customer_name')
                <small class="field-error">{{ $message }}</small>
            @enderror
        </div>

        <div class="field-group">
            <label for="customer_email">Email</label>
            <input type="email" id="customer_email" name="customer_email" value="{{ old('customer_email', $customer->email ?? '') }}" placeholder="nama@email.com">
            @error('customer_email')
                <small class="field-error">{{ $message }}</small>
            @enderror
        </div>

        <div class="field-group">
            <label for="customer_phone">Nomor HP</label>
            <input type="tel" id="customer_phone" name="customer_phone" value="{{ old('customer_phone', $customer->phone ?? '') }}" placeholder="08xxxxxxxxxx">
            @error('customer_phone')
                <small class="field-error">{{ $message }}</small>
            @enderror
        </div>

        <div class="field-group full-span">
            <label for="customer_address">Alamat</label>
            <textarea id="customer_address" name="customer_address" rows="3" placeholder="Masukkan alamat lengkap">{{ old('customer_address') }}</textarea>
            @error('customer_address')
                <small class="field-error">{{ $message }}</small>
            @enderror
        </div>

        <div class="field-group">
            <label for="identity_number">Nomor KTP</label>
            <input type="text" id="identity_number" name="identity_number" value="{{ old('identity_number') }}" placeholder="Nomor identitas">
            @error('identity_number')
                <small class="field-error">{{ $message }}</small>
            @enderror
        </div>

        <div class="field-group">
            <label for="driver_license_number">Nomor SIM</label>
            <input type="text" id="driver_license_number" name="driver_license_number" value="{{ old('driver_license_number') }}" placeholder="Nomor SIM aktif">
            @error('driver_license_number')
                <small class="field-error">{{ $message }}</small>
            @enderror
        </div>
    </div>
</section>
