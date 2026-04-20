<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin | VeloraRide</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;600;700;800&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/auth.css') }}">
</head>
<body class="auth-page">
    <main class="auth-main">
        <section class="auth-card" aria-label="Login Admin">
            <div class="auth-brand">
                <span class="brand-dot"></span>
                <p>VeloraRide Admin</p>
            </div>

            <h1>Login Admin</h1>
            <p class="subtitle">Masuk sebagai Super Admin atau Admin Rental.</p>

            @if ($errors->any())
                <div class="alert alert-danger" role="alert">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login.attempt') }}" class="auth-form">
                @csrf

                <div class="field-group">
                    <label for="email">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>
                </div>

                <div class="field-group">
                    <label for="password">Password</label>
                    <input id="password" type="password" name="password" required>
                </div>

                <button type="submit" class="btn-primary">Login</button>
            </form>

            <a href="{{ route('home') }}" class="back-link">Kembali ke Website</a>
        </section>
    </main>
</body>
</html>
