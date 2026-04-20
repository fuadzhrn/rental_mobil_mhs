<section class="detail-section" aria-label="Deskripsi kendaraan">
    <div class="container">
        <article class="detail-content-card">
            <div class="section-heading">
                <p class="eyebrow">Deskripsi</p>
                <h2>Deskripsi Kendaraan</h2>
            </div>

            <p>
                {{ $vehicle->description ?: 'Deskripsi kendaraan belum diisi oleh admin rental. Informasi kendaraan akan diperbarui sesuai data armada yang tersedia.' }}
            </p>

            <ul class="key-points">
                <li>Unit ini dikelola langsung oleh rental company yang sudah terverifikasi.</li>
                <li>Status kendaraan aktif sehingga dapat ditampilkan ke customer pada katalog.</li>
                <li>Detail booking penuh akan dihubungkan pada tahap backend berikutnya.</li>
            </ul>
        </article>
    </div>
</section>
