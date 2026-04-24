@extends('layouts.auth')

@section('title', 'Pendaftaran Berhasil - VeloraRide')

@section('content')
<div class="auth-container success-container">
    <div class="auth-card success-card">
        <div class="success-icon">
            <i class="fa-solid fa-circle-check"></i>
        </div>

        <div class="auth-header text-center">
            <h1>Pendaftaran Berhasil!</h1>
            <p>Terima kasih telah mendaftar sebagai rental company di VeloraRide</p>
        </div>

        <div class="success-content">
            <div class="info-box success-info">
                <h5><i class="fa-solid fa-hourglass-end"></i> Status Pendaftaran</h5>
                <p><strong>Status:</strong> <span class="badge badge-warning">Menunggu Verifikasi</span></p>
                <p><strong>Nama Perusahaan:</strong> {{ $rentalCompany->company_name }}</p>
                <p><strong>Email:</strong> {{ $rentalCompany->email }}</p>
                <p style="margin-bottom: 0;">
                    <strong>ID Pendaftaran:</strong> <code>#{{ str_pad($rentalCompany->id, 6, '0', STR_PAD_LEFT) }}</code>
                </p>
            </div>

            <div class="timeline-info">
                <h5 style="margin-bottom: 20px;">Apa yang Selanjutnya?</h5>
                <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-marker active">
                            <i class="fa-solid fa-check"></i>
                        </div>
                        <div class="timeline-content">
                            <h6>Pendaftaran Dikirim</h6>
                            <p>Data perusahaan Anda telah kami terima dan masuk dalam antrian verifikasi.</p>
                        </div>
                    </div>

                    <div class="timeline-item">
                        <div class="timeline-marker">
                            <i class="fa-solid fa-clock"></i>
                        </div>
                        <div class="timeline-content">
                            <h6>Sedang Diverifikasi</h6>
                            <p>Tim super admin VeloraRide akan memeriksa dokumen dan data perusahaan Anda dalam waktu 1-3 hari kerja.</p>
                        </div>
                    </div>

                    <div class="timeline-item">
                        <div class="timeline-marker">
                            <i class="fa-solid fa-envelope"></i>
                        </div>
                        <div class="timeline-content">
                            <h6>Menerima Notifikasi</h6>
                            <p>Kami akan mengirimkan email ke <strong>{{ $rentalCompany->email }}</strong> dengan hasil verifikasi.</p>
                        </div>
                    </div>

                    <div class="timeline-item">
                        <div class="timeline-marker">
                            <i class="fa-solid fa-sign-in-alt"></i>
                        </div>
                        <div class="timeline-content">
                            <h6>Dapat Login</h6>
                            <p>Setelah disetujui, Anda bisa langsung login menggunakan email dan password yang telah didaftarkan.</p>
                        </div>
                    </div>

                    <div class="timeline-item">
                        <div class="timeline-marker">
                            <i class="fa-solid fa-car"></i>
                        </div>
                        <div class="timeline-content">
                            <h6>Kelola Kendaraan</h6>
                            <p>Mulai tambahkan kendaraan, atur promo, dan kelola booking dari dashboard admin rental Anda.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="alert alert-info" role="alert">
                <i class="fa-solid fa-lightbulb"></i>
                <strong>Tips:</strong> Jangan lupa cek email inbox (termasuk folder spam) untuk notifikasi dari VeloraRide. Pastikan dokumen yang diunggah jelas dan sesuai format yang diminta.
            </div>

            <div class="button-group">
                <a href="{{ route('home') }}" class="success-action success-action-primary">
                    <i class="fa-solid fa-home"></i> Kembali ke Beranda
                </a>
                <a href="{{ route('login') }}" class="success-action success-action-secondary">
                    <i class="fa-solid fa-sign-in-alt"></i> Login (Jika Sudah Disetujui)
                </a>
            </div>
        </div>

        <div class="contact-support">
            <p><strong>Ada pertanyaan?</strong> Hubungi kami di <a href="mailto:support@veloraride.com">support@veloraride.com</a> atau <a href="tel:+62812345678">+62 812-345-678</a></p>
        </div>
    </div>
</div>

<style>
.success-card {
    text-align: center;
}

.success-icon {
    font-size: 60px;
    color: #10b981;
    margin-bottom: 20px;
    animation: scaleIn 0.5s ease-out;
}

@keyframes scaleIn {
    from {
        transform: scale(0);
        opacity: 0;
    }
    to {
        transform: scale(1);
        opacity: 1;
    }
}

.timeline {
    margin: 30px 0;
    text-align: left;
}

.timeline-item {
    display: flex;
    margin-bottom: 20px;
    gap: 15px;
}

.timeline-marker {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #f0f0f0;
    border: 2px solid #ddd;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 18px;
    color: #666;
    transition: all 0.3s ease;
}

.timeline-marker.active {
    background: #10b981;
    color: white;
    border-color: #10b981;
}

.timeline-item:not(:last-child) .timeline-marker::after {
    content: '';
    position: absolute;
    width: 2px;
    height: 40px;
    background: #ddd;
    left: 19px;
    top: 40px;
}

.timeline-content h6 {
    margin: 0 0 5px;
    color: #1f2937;
    font-weight: 600;
}

.timeline-content p {
    margin: 0;
    color: #666;
    font-size: 0.9rem;
}

.success-info {
    background: #f0fdf4;
    border-left: 4px solid #10b981;
    text-align: left;
}

.success-info p {
    margin-bottom: 8px;
    color: #1f2937;
}

.success-info code {
    background: #fff;
    padding: 4px 8px;
    border-radius: 4px;
    color: #6b7280;
    font-family: monospace;
}

.badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 500;
}

.badge-warning {
    background: #fef3c7;
    color: #92400e;
}

.button-group {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 12px;
    margin: 30px 0;
}

.success-action {
    min-height: 50px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    border-radius: 14px;
    font-family: 'Montserrat', sans-serif;
    font-weight: 600;
    text-decoration: none;
    transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease, border-color 0.2s ease;
    padding: 0 18px;
    text-align: center;
}

.success-action:hover {
    transform: translateY(-1px);
}

.success-action-primary {
    background: linear-gradient(140deg, #6c8cf5 0%, #5a79e6 100%);
    color: #ffffff;
    box-shadow: 0 12px 20px rgba(108, 140, 245, 0.22);
    border: 1px solid rgba(108, 140, 245, 0.12);
}

.success-action-primary:hover {
    color: #ffffff;
    box-shadow: 0 14px 24px rgba(108, 140, 245, 0.28);
}

.success-action-secondary {
    background: #ffffff;
    color: #5a79e6;
    border: 1px solid rgba(108, 140, 245, 0.28);
    box-shadow: 0 10px 18px rgba(31, 41, 55, 0.06);
}

.success-action-secondary:hover {
    background: #eef4ff;
    color: #5a79e6;
}

.contact-support {
    margin-top: 30px;
    padding-top: 20px;
    border-top: 1px solid #e5e7eb;
    color: #666;
    font-size: 0.9rem;
}

.contact-support a {
    color: #6c8cf5;
    text-decoration: none;
}

.contact-support a:hover {
    text-decoration: underline;
}

@media (max-width: 768px) {
    .success-icon {
        font-size: 48px;
    }

    .button-group {
        grid-template-columns: 1fr;
        margin: 20px 0;
    }

    .success-action {
        min-height: 48px;
        width: 100%;
    }
}
</style>
@endsection
