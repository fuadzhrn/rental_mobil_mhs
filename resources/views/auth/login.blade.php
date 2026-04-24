@extends('layouts.auth')

@section('title', 'Masuk | VeloraRide')

@section('content')
    <article class="auth-card" aria-labelledby="auth-title">
        <header class="auth-header">
            <div class="auth-brand">
                <span class="brand-dot"></span>
                <p>VeloraRide Account</p>
            </div>
            <h2 id="auth-title">Selamat Datang Kembali</h2>
            <p class="auth-subtitle">Masuk untuk mengakses akun customer, admin rental, atau super admin.</p>
        </header>

        @if (session('success'))
            <div class="alert alert-success" role="status">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.attempt') }}" class="auth-form" novalidate>
            @csrf

            <div class="field-group">
                <label for="email">Email</label>
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    autocomplete="email"
                    required
                    autofocus
                >
                @error('email')
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
                        autocomplete="current-password"
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

            <div class="auth-option">
                <label class="check-wrap" for="remember">
                    <input id="remember" type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }}>
                    <span>Ingat saya di perangkat ini</span>
                </label>
            </div>

            <button type="submit" class="btn-primary">Masuk Sekarang</button>
        </form>

        <div class="auth-cta-block" aria-label="Akses pendaftaran customer">
            <p class="auth-switch">Belum punya akun customer?</p>
            <a href="{{ route('register') }}" class="btn-secondary">Daftar Customer</a>
        </div>

        <div class="auth-cta-block auth-cta-block--subtle" aria-label="Akses pendaftaran rental">
            <p class="auth-switch">Punya usaha rental mobil?</p>
            <a href="{{ route('rental.register.show') }}" class="btn-secondary btn-secondary--outline">Daftar Rental Company</a>
        </div>

        <a href="{{ route('home') }}" class="back-link">Kembali ke beranda</a>
    </article>
@endsection
