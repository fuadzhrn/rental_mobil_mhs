<section class="detail-section" aria-label="Syarat dan ketentuan sewa">
    <div class="container">
        <article class="detail-content-card soft-card">
            <div class="section-heading">
                <p class="eyebrow">Informasi Penting</p>
                <h2>Syarat dan Ketentuan</h2>
            </div>

            @if ($vehicle->terms_conditions)
                <div class="terms-content" style="line-height:1.8;">
                    {!! nl2br(e($vehicle->terms_conditions)) !!}
                </div>
            @else
                <ul class="terms-list">
                    <li>Wajib menunjukkan KTP dan SIM A yang masih aktif saat proses verifikasi.</li>
                    <li>Pembayaran mengikuti ketentuan pada tahap booking yang akan dibangun berikutnya.</li>
                    <li>Kendaraan harus dikembalikan dalam kondisi baik serta level bahan bakar sesuai awal.</li>
                    <li>Penyewa bertanggung jawab atas pelanggaran lalu lintas selama masa sewa berlangsung.</li>
                </ul>
            @endif
        </article>
    </div>
</section>
