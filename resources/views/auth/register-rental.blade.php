@extends('layouts.auth')

@section('title', 'Daftar Rental Company - VeloraRide')

@section('content')
    <article class="auth-card auth-card--wide register-rental-card" aria-labelledby="auth-title">
        <header class="auth-header">
            <div class="auth-brand">
                <span class="brand-dot"></span>
                <p>VeloraRide Partner</p>
            </div>
            <h2 id="auth-title">Daftar Rental Company</h2>
            <p class="auth-subtitle">Gabung sebagai mitra rental untuk menampilkan armada, menerima booking, dan mengelola bisnis Anda di VeloraRide.</p>
        </header>

        @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                <strong>Terjadi kesalahan validasi.</strong>
                <ul class="auth-error-list">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('rental.register.store') }}" method="POST" enctype="multipart/form-data" class="auth-form register-rental-form" novalidate>
            @csrf

            <section class="section-card">
                <h3 class="section-title">Informasi Perusahaan</h3>
                <div class="form-grid form-grid--two">
                    <div class="field-group field-group--full">
                        <label for="company_name">Nama Perusahaan</label>
                        <input
                            type="text"
                            id="company_name"
                            name="company_name"
                            value="{{ old('company_name') }}"
                            placeholder="PT. Rental Mobil Indonesia"
                            required
                        >
                        @error('company_name')
                            <small class="field-error">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="field-group">
                        <label for="city">Kota</label>
                        <input
                            type="text"
                            id="city"
                            name="city"
                            value="{{ old('city') }}"
                            placeholder="Jakarta"
                            required
                        >
                        @error('city')
                            <small class="field-error">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="field-group field-group--full">
                        <label for="address">Alamat Lengkap</label>
                        <textarea
                            id="address"
                            name="address"
                            rows="3"
                            placeholder="Jl. Contoh No. 123, Kelurahan, Kecamatan"
                            required
                        >{{ old('address') }}</textarea>
                        @error('address')
                            <small class="field-error">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
            </section>

            <section class="section-card">
                <h3 class="section-title">Informasi Kontak</h3>
                <div class="form-grid form-grid--two">
                    <div class="field-group">
                        <label for="email">Email</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="admin@rental.com"
                            required
                        >
                        @error('email')
                            <small class="field-error">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="field-group">
                        <label for="phone">Nomor Telepon</label>
                        <input
                            type="tel"
                            id="phone"
                            name="phone"
                            value="{{ old('phone') }}"
                            placeholder="0812-3456-7890"
                            required
                        >
                        @error('phone')
                            <small class="field-error">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
            </section>

            <section class="section-card">
                <h3 class="section-title">Dokumen & Keamanan</h3>
                <div class="form-grid">
                    <div class="field-group field-group--full">
                        <label for="document">Upload Dokumen SIUP / Izin Usaha</label>
                        <input
                            type="file"
                            id="document"
                            name="document"
                            accept=".pdf,.jpg,.jpeg,.png"
                            required
                        >
                        <small class="field-hint">Format: PDF, JPG, JPEG, PNG. Maksimal 5MB.</small>
                        @error('document')
                            <small class="field-error">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="field-group">
                        <label for="password">Password</label>
                        <div class="password-wrap">
                            <input
                                type="password"
                                id="password"
                                name="password"
                                placeholder="Minimal 8 karakter"
                                required
                            >
                            <button type="button" class="toggle-password" data-target="password" aria-label="Tampilkan password">
                                Lihat
                            </button>
                        </div>
                        @error('password')
                            <small class="field-error">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="field-group">
                        <label for="password_confirmation">Konfirmasi Password</label>
                        <div class="password-wrap">
                            <input
                                type="password"
                                id="password_confirmation"
                                name="password_confirmation"
                                placeholder="Konfirmasi password Anda"
                                required
                            >
                            <button type="button" class="toggle-password" data-target="password_confirmation" aria-label="Tampilkan konfirmasi password">
                                Lihat
                            </button>
                        </div>
                    </div>
                </div>
            </section>

            <div class="auth-option auth-option--panel">
                <label class="check-wrap check-wrap--compact" for="terms">
                    <input type="checkbox" id="terms" name="terms" value="1" {{ old('terms') ? 'checked' : '' }} required>
                    <span>Saya menyetujui syarat dan ketentuan serta kebijakan privasi VeloraRide.</span>
                </label>
                @error('terms')
                    <small class="field-error">{{ $message }}</small>
                @enderror
            </div>

            <button type="submit" class="btn-primary btn-primary--full">Daftar Sebagai Rental Company</button>

            <div class="auth-cta-block auth-cta-block--subtle">
                <p class="auth-switch">Sudah punya akun rental?</p>
                <a href="{{ route('login') }}" class="btn-secondary btn-secondary--outline">Masuk Sekarang</a>
            </div>

            <div class="auth-footer-links">
                <a href="{{ route('home') }}" class="back-link">Kembali ke beranda</a>
            </div>
        </form>

        <aside class="info-box">
            <h3>Proses Pendaftaran</h3>
            <ol>
                <li>Isi form pendaftaran dengan data perusahaan yang valid</li>
                <li>Upload dokumen SIUP atau izin usaha</li>
                <li>Tunggu verifikasi dari super admin VeloraRide</li>
                <li>Setelah disetujui, Anda bisa login dan mengelola kendaraan</li>
            </ol>
        </aside>
@endsection
