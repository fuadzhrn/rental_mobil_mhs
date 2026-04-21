@php
    $isEdit = isset($promo) && $promo->exists;
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

<div style="display:grid; gap:16px; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));">
    <div>
        <label style="display:block; margin-bottom:6px; font-weight:600;">Judul Promo</label>
        <input type="text" name="title" value="{{ old('title', $promo->title ?? '') }}" placeholder="Contoh: Diskon Akhir Tahun" style="width:100%; padding:10px 12px; border:1px solid #d1d5db; border-radius:10px;">
        @error('title')
            <small style="color:#dc2626;">{{ $message }}</small>
        @enderror
    </div>

    <div>
        <label style="display:block; margin-bottom:6px; font-weight:600;">Kode Promo</label>
        <input type="text" name="promo_code" value="{{ old('promo_code', $promo->promo_code ?? '') }}" placeholder="Contoh: SAVE2024" style="width:100%; padding:10px 12px; border:1px solid #d1d5db; border-radius:10px; text-transform:uppercase;" @readonly($isEdit)>
        <small style="color:#6b7280; display:block; margin-top:4px;">Format: huruf, angka, underscore, dan dash. Tidak boleh spasi. {{ $isEdit ? '(Tidak dapat diubah)' : '' }}</small>
        @error('promo_code')
            <small style="color:#dc2626;">{{ $message }}</small>
        @enderror
    </div>

    <div>
        <label style="display:block; margin-bottom:6px; font-weight:600;">Tipe Diskon</label>
        <select name="discount_type" style="width:100%; padding:10px 12px; border:1px solid #d1d5db; border-radius:10px;">
            @foreach (['percent' => 'Persen (%)', 'fixed' => 'Nominal (Rp)'] as $value => $label)
                <option value="{{ $value }}" @selected(old('discount_type', $promo->discount_type ?? '') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('discount_type')
            <small style="color:#dc2626;">{{ $message }}</small>
        @enderror
    </div>

    <div>
        <label style="display:block; margin-bottom:6px; font-weight:600;">Nilai Diskon</label>
        <input type="number" step="0.01" name="discount_value" value="{{ old('discount_value', $promo->discount_value ?? '') }}" placeholder="Contoh: 50 atau 100000" style="width:100%; padding:10px 12px; border:1px solid #d1d5db; border-radius:10px;">
        <small style="color:#6b7280; display:block; margin-top:4px;">Jika persen: 1-100. Jika nominal: dalam Rupiah.</small>
        @error('discount_value')
            <small style="color:#dc2626;">{{ $message }}</small>
        @enderror
    </div>

    <div>
        <label style="display:block; margin-bottom:6px; font-weight:600;">Minimum Transaksi (Rp)</label>
        <input type="number" name="min_transaction" value="{{ old('min_transaction', $promo->min_transaction ?? '') }}" placeholder="Contoh: 500000" style="width:100%; padding:10px 12px; border:1px solid #d1d5db; border-radius:10px;">
        <small style="color:#6b7280; display:block; margin-top:4px;">Opsional. Jika kosong, promo berlaku untuk semua transaksi.</small>
        @error('min_transaction')
            <small style="color:#dc2626;">{{ $message }}</small>
        @enderror
    </div>

    <div>
        <label style="display:block; margin-bottom:6px; font-weight:600;">Tanggal Mulai</label>
        <input type="datetime-local" name="start_date" value="{{ old('start_date', $promo->start_date?->format('Y-m-d\TH:i') ?? '') }}" style="width:100%; padding:10px 12px; border:1px solid #d1d5db; border-radius:10px;">
        @error('start_date')
            <small style="color:#dc2626;">{{ $message }}</small>
        @enderror
    </div>

    <div>
        <label style="display:block; margin-bottom:6px; font-weight:600;">Tanggal Akhir</label>
        <input type="datetime-local" name="end_date" value="{{ old('end_date', $promo->end_date?->format('Y-m-d\TH:i') ?? '') }}" style="width:100%; padding:10px 12px; border:1px solid #d1d5db; border-radius:10px;">
        <small style="color:#6b7280; display:block; margin-top:4px;">Promo tidak berlaku setelah tanggal ini.</small>
        @error('end_date')
            <small style="color:#dc2626;">{{ $message }}</small>
        @enderror
    </div>

    <div>
        <label style="display:block; margin-bottom:6px; font-weight:600;">Kuota Penggunaan</label>
        <input type="number" name="quota" value="{{ old('quota', $promo->quota ?? '') }}" placeholder="Contoh: 50" style="width:100%; padding:10px 12px; border:1px solid #d1d5db; border-radius:10px;">
        <small style="color:#6b7280; display:block; margin-top:4px;">Opsional. Jika kosong, promo bisa digunakan unlimited.</small>
        @error('quota')
            <small style="color:#dc2626;">{{ $message }}</small>
        @enderror
    </div>

    <div>
        <label style="display:block; margin-bottom:6px; font-weight:600;">Status</label>
        <select name="status" style="width:100%; padding:10px 12px; border:1px solid #d1d5db; border-radius:10px;">
            @foreach (['active' => 'Active', 'inactive' => 'Inactive'] as $value => $label)
                <option value="{{ $value }}" @selected(old('status', $promo->status ?? 'active') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('status')
            <small style="color:#dc2626;">{{ $message }}</small>
        @enderror
    </div>
</div>

<div style="margin-top:16px; display:grid; gap:16px;">
    <div>
        <label style="display:block; margin-bottom:6px; font-weight:600;">Deskripsi</label>
        <textarea name="description" rows="3" placeholder="Jelaskan promo ini, syarat & ketentuan, dll" style="width:100%; padding:10px 12px; border:1px solid #d1d5db; border-radius:10px;">{{ old('description', $promo->description ?? '') }}</textarea>
        @error('description')
            <small style="color:#dc2626;">{{ $message }}</small>
        @enderror
    </div>

    <div style="display:flex; align-items:center; gap:12px; padding:12px; background:#f9fafb; border-radius:10px; border:1px solid #e5e7eb;">
        <input type="checkbox" id="loyal_only" name="loyal_only" value="1" @checked(old('loyal_only', $promo->loyal_only ?? false)) style="width:18px; height:18px; cursor:pointer;">
        <label for="loyal_only" style="margin:0; cursor:pointer; font-weight:600;">Promo hanya untuk pelanggan setia (2+ booking di rental ini)</label>
    </div>
    @error('loyal_only')
        <small style="color:#dc2626;">{{ $message }}</small>
    @enderror
</div>

<div style="margin-top:18px; display:flex; gap:10px; flex-wrap:wrap;">
    <button type="submit" style="padding:11px 16px; background:#2563eb; color:#fff; border:0; border-radius:10px; font-weight:700;">{{ $submitLabel ?? 'Simpan' }}</button>
    <a href="{{ route('admin-rental.promos.index') }}" style="padding:11px 16px; border:1px solid #d1d5db; border-radius:10px; color:#111827; text-decoration:none; font-weight:700;">Batal</a>
</div>
