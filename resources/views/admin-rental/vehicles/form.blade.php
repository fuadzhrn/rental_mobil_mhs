@php
    $isEdit = isset($vehicle) && $vehicle->exists;
    $mainImagePreview = old('main_image_preview');
@endphp

@if ($errors->any())
    <div style="margin-bottom:16px; padding:12px 14px; background:#fef2f2; border:1px solid #fecaca; color:#991b1b; border-radius:10px;">
        <strong>Terjadi kesalahan input.</strong>
        <ul style="margin:8px 0 0; padding-left:18px;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div style="display:grid; gap:16px; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));">
    <div>
        <label>Nama Kendaraan</label>
        <input type="text" name="name" value="{{ old('name', $vehicle->name ?? '') }}" style="width:100%; padding:10px 12px; border:1px solid #d1d5db; border-radius:10px;">
    </div>

    <div>
        <label>Brand</label>
        <input type="text" name="brand" value="{{ old('brand', $vehicle->brand ?? '') }}" style="width:100%; padding:10px 12px; border:1px solid #d1d5db; border-radius:10px;">
    </div>

    <div>
        <label>Type</label>
        <input type="text" name="type" value="{{ old('type', $vehicle->type ?? '') }}" style="width:100%; padding:10px 12px; border:1px solid #d1d5db; border-radius:10px;">
    </div>

    <div>
        <label>Category</label>
        <input type="text" name="category" value="{{ old('category', $vehicle->category ?? '') }}" style="width:100%; padding:10px 12px; border:1px solid #d1d5db; border-radius:10px;">
    </div>

    <div>
        <label>Tahun</label>
        <input type="number" name="year" value="{{ old('year', $vehicle->year ?? '') }}" style="width:100%; padding:10px 12px; border:1px solid #d1d5db; border-radius:10px;">
    </div>

    <div>
        <label>Transmission</label>
        <input type="text" name="transmission" value="{{ old('transmission', $vehicle->transmission ?? '') }}" style="width:100%; padding:10px 12px; border:1px solid #d1d5db; border-radius:10px;">
    </div>

    <div>
        <label>Fuel Type</label>
        <input type="text" name="fuel_type" value="{{ old('fuel_type', $vehicle->fuel_type ?? '') }}" style="width:100%; padding:10px 12px; border:1px solid #d1d5db; border-radius:10px;">
    </div>

    <div>
        <label>Seat Capacity</label>
        <input type="number" name="seat_capacity" value="{{ old('seat_capacity', $vehicle->seat_capacity ?? '') }}" style="width:100%; padding:10px 12px; border:1px solid #d1d5db; border-radius:10px;">
    </div>

    <div>
        <label>Luggage Capacity</label>
        <input type="number" name="luggage_capacity" value="{{ old('luggage_capacity', $vehicle->luggage_capacity ?? '') }}" style="width:100%; padding:10px 12px; border:1px solid #d1d5db; border-radius:10px;">
    </div>

    <div>
        <label>Color</label>
        <input type="text" name="color" value="{{ old('color', $vehicle->color ?? '') }}" style="width:100%; padding:10px 12px; border:1px solid #d1d5db; border-radius:10px;">
    </div>

    <div>
        <label>Price Per Day</label>
        <input type="number" step="0.01" name="price_per_day" value="{{ old('price_per_day', $vehicle->price_per_day ?? '') }}" style="width:100%; padding:10px 12px; border:1px solid #d1d5db; border-radius:10px;">
    </div>

    <div>
        <label>Status</label>
        <select name="status" style="width:100%; padding:10px 12px; border:1px solid #d1d5db; border-radius:10px;">
            @foreach (['active' => 'Active', 'inactive' => 'Inactive', 'maintenance' => 'Maintenance'] as $value => $label)
                <option value="{{ $value }}" @selected(old('status', $vehicle->status ?? 'active') === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
</div>

<div style="margin-top:16px; display:grid; gap:16px;">
    <div>
        <label>Description</label>
        <textarea name="description" rows="4" style="width:100%; padding:10px 12px; border:1px solid #d1d5db; border-radius:10px;">{{ old('description', $vehicle->description ?? '') }}</textarea>
    </div>

    <div>
        <label>Terms & Conditions</label>
        <textarea name="terms_conditions" rows="4" style="width:100%; padding:10px 12px; border:1px solid #d1d5db; border-radius:10px;">{{ old('terms_conditions', $vehicle->terms_conditions ?? '') }}</textarea>
    </div>

    <div>
        <label>Upload Main Image</label>
        <input type="file" name="main_image" accept="image/*" style="display:block; margin-top:6px;">
        @if (!empty($vehicle?->main_image))
            <div style="margin-top:10px;">
                <img src="{{ asset('storage/' . $vehicle->main_image) }}" alt="Main image" style="width:180px; height:120px; object-fit:cover; border-radius:10px; border:1px solid #e5e7eb;">
            </div>
        @endif
    </div>

    <div>
        <label>Upload Gallery Images</label>
        <input type="file" name="gallery_images[]" accept="image/*" multiple style="display:block; margin-top:6px;">
    </div>

    @if ($isEdit && isset($vehicle->images) && $vehicle->images->isNotEmpty())
        <div>
            <label>Gallery Lama</label>
            <div style="display:flex; flex-wrap:wrap; gap:12px; margin-top:10px;">
                @foreach ($vehicle->images as $image)
                    <label style="width:150px; border:1px solid #e5e7eb; border-radius:12px; padding:10px;">
                        <img src="{{ asset('storage/' . $image->image_path) }}" alt="Gallery image" style="width:100%; height:90px; object-fit:cover; border-radius:8px; margin-bottom:8px;">
                        <div style="display:flex; align-items:center; gap:6px; font-size:14px; color:#374151;">
                            <input type="checkbox" name="delete_gallery_images[]" value="{{ $image->id }}">
                            Hapus
                        </div>
                    </label>
                @endforeach
            </div>
        </div>
    @endif
</div>

<div style="margin-top:18px; display:flex; gap:10px; flex-wrap:wrap;">
    <button type="submit" style="padding:11px 16px; background:#2563eb; color:#fff; border:0; border-radius:10px; font-weight:700;">{{ $submitLabel ?? 'Simpan' }}</button>
    <a href="{{ route('admin-rental.vehicles.index') }}" style="padding:11px 16px; border:1px solid #d1d5db; border-radius:10px; color:#111827; text-decoration:none; font-weight:700;">Batal</a>
</div>
