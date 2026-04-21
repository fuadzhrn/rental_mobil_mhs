<section class="booking-card promo-voucher-card" aria-label="Promo voucher">
    <div class="section-heading compact">
        <p class="eyebrow">Promo</p>
        <h2>Gunakan Voucher</h2>
    </div>

    <div class="voucher-box">
        <div class="field-group voucher-input-group">
            <label for="promo_code">Kode Promo</label>
            <input type="text" id="promo_code" name="promo_code" value="{{ old('promo_code', '') }}" placeholder="Masukkan kode promo (opsional)" style="text-transform: uppercase;">
            @error('promo_code')
                <small style="color:#dc2626;">{{ $message }}</small>
            @enderror
        </div>
    </div>

    @if (isset($availablePromos) && $availablePromos->count() > 0)
        <div style="margin-top:16px;">
            <p style="margin:0 0 10px; font-weight:600; font-size:14px;">Promo Tersedia</p>
            <div style="display:grid; gap:10px;">
                @foreach ($availablePromos as $promo)
                    <div style="padding:12px; background:#f9fafb; border:1px solid #e5e7eb; border-radius:10px;">
                        <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:6px;">
                            <div>
                                <p style="margin:0; font-weight:600; color:#111827;">{{ strtoupper($promo->promo_code) }}: {{ $promo->title }}</p>
                                <p style="margin:4px 0 0; font-size:13px; color:#6b7280;">{{ $promo->discount_label }}</p>
                            </div>
                            @if ($promo->can_use)
                                <button type="button" class="btn-apply-promo" data-code="{{ strtoupper($promo->promo_code) }}" style="padding:6px 12px; background:#2563eb; color:#fff; border:0; border-radius:6px; font-size:12px; font-weight:600; cursor:pointer;">Pakai</button>
                            @endif
                        </div>
                        <p style="margin:6px 0 0; font-size:12px; color:#6b7280;">
                            {{ $promo->description }}
                        </p>
                        @if (!$promo->can_use)
                            <p style="margin:6px 0 0; font-size:12px; color:#dc2626; font-weight:600;">
                                ❌ {{ $promo->cannot_use_reason }}
                            </p>
                        @endif
                        @if ($promo->quota)
                            <p style="margin:6px 0 0; font-size:12px; color:#6b7280;">
                                Kuota: {{ $promo->quota - $promo->used_count }} / {{ $promo->quota }} tersisa
                            </p>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <p style="margin-top:12px; color:#6b7280; font-size:13px;">Diskon dihitung ulang 100% di backend. Nilai estimasi diskon bersifat informasi.</p>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.btn-apply-promo').forEach(button => {
                button.addEventListener('click', function() {
                    const code = this.getAttribute('data-code');
                    document.getElementById('promo_code').value = code;
                });
            });
        });
    </script>
</section>
