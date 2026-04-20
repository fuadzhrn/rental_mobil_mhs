@extends('layouts.auth')

@section('title', 'Daftar Customer | VeloraRide')

@section('content')
    <article class="auth-card" aria-labelledby="auth-title">
        <header class="auth-header">
            <div class="auth-brand">
                <span class="brand-dot"></span>
                <p>VeloraRide Account</p>
            </div>
            <h2 id="auth-title">Buat Akun Customer</h2>
            <p class="auth-subtitle">Daftar untuk mulai booking kendaraan, pembayaran, riwayat pesanan, dan ulasan.</p>
        </header>

        @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('register.store') }}" class="auth-form" novalidate>
            @csrf

            <div class="field-group">
                <label for="name">Nama Lengkap</label>
                <input
                    id="name"
                    type="text"
                    name="name"
                    value="{{ old('name') }}"
                    autocomplete="name"
                    required
                >
                @error('name')
                    <small class="field-error">{{ $message }}</small>
                @enderror
            </div>

            <div class="field-group">
                <label for="email">Email</label>
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    autocomplete="email"
                    required
                >
                @error('email')
                    <small class="field-error">{{ $message }}</small>
                @enderror
            </div>

            <div class="field-group">
                <label for="phone">Nomor HP</label>
                <input
                    id="phone"
                    type="text"
                    name="phone"
                    value="{{ old('phone') }}"
                    autocomplete="tel"
                    inputmode="tel"
                    required
                >
                @error('phone')
                    <small class="field-error">{{ $message }}</small>
                @enderror
            </div>

            <div class="field-group">
                <label for="password">Password</label>
                <div class="password-wrap">
                    <input
                        id="password"
                        type="password"
                        name="password"
                        autocomplete="new-password"
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
                        id="password_confirmation"
                        type="password"
                        name="password_confirmation"
                        autocomplete="new-password"
                        required
                    >
                    <button type="button" class="toggle-password" data-target="password_confirmation" aria-label="Tampilkan konfirmasi password">
                        Lihat
                    </button>
                </div>
            </div>

            <div class="auth-option">
                <label class="check-wrap" for="terms">
                    <input id="terms" type="checkbox" name="terms" value="1" {{ old('terms') ? 'checked' : '' }} required>
                    <span>Saya setuju dengan syarat dan ketentuan VeloraRide</span>
                </label>
                @error('terms')
                    <small class="field-error">{{ $message }}</small>
                @enderror
            </div>

            <button type="submit" class="btn-primary">Daftar Sekarang</button>
        </form>

        <p class="auth-switch">
            Sudah punya akun?
            <a href="{{ route('login') }}">Masuk</a>
        </p>

        <a href="{{ route('home') }}" class="back-link">Kembali ke beranda</a>
    </article>
@endsection
