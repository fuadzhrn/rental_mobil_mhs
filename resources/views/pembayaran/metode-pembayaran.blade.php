<section class="payment-card" aria-label="Metode pembayaran">
    <div class="section-heading compact">
        <p class="eyebrow">Pembayaran</p>
        <h2>Metode Pembayaran</h2>
    </div>

    <div class="method-grid" id="methodGrid">
        <label class="method-item is-active" data-method="bank">
            <input type="radio" name="payment_method" checked>
            <div>
                <strong>Transfer Bank</strong>
                <small>BCA, Mandiri, BNI, BRI</small>
            </div>
        </label>

        <label class="method-item" data-method="va">
            <input type="radio" name="payment_method">
            <div>
                <strong>Virtual Account</strong>
                <small>BCA VA, BNI VA, Mandiri VA</small>
            </div>
        </label>

        <label class="method-item" data-method="ewallet">
            <input type="radio" name="payment_method">
            <div>
                <strong>E-Wallet</strong>
                <small>OVO, GoPay, Dana</small>
            </div>
        </label>

        <label class="method-item" data-method="gateway">
            <input type="radio" name="payment_method">
            <div>
                <strong>Payment Gateway</strong>
                <small>Kartu Kredit / Debit</small>
            </div>
        </label>
    </div>
</section>
