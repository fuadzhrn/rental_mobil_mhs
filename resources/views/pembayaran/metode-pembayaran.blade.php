<section class="payment-card" aria-label="Metode pembayaran">
    <div class="section-heading compact">
        <p class="eyebrow">Pembayaran</p>
        <h2>Metode Pembayaran</h2>
    </div>

    <div class="method-grid" id="methodGrid">
        @foreach ($paymentMethods as $methodKey => $method)
            <label class="method-item {{ $activePaymentMethod === $methodKey ? 'is-active' : '' }}" data-method="{{ $methodKey }}">
                <input type="radio" name="payment_method" value="{{ $methodKey }}" {{ $activePaymentMethod === $methodKey ? 'checked' : '' }}>
                <div>
                    <strong>{{ $method['label'] }}</strong>
                    <small>{{ $method['group'] === 'bank' ? 'Transfer bank' : 'E-wallet' }}</small>
                </div>
            </label>
        @endforeach
    </div>
</section>
