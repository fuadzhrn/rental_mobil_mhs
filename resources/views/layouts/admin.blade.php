<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;600;700;800&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/admin.css') }}">
</head>
<body class="admin-page">
    <div class="admin-shell">
        @if (auth()->user()->role === 'super_admin')
            <x-super-admin-sidebar />
        @else
            <x-admin-rental-sidebar />
        @endif

        <main class="admin-main">
            <header class="admin-topbar">
                <div>
                    <p class="topbar-eyebrow">Area Admin</p>
                    <h1>@yield('page_title', 'Dashboard')</h1>
                </div>

                <div class="topbar-user">
                    <strong>{{ auth()->user()->name }}</strong>
                    <span>{{ auth()->user()->role === 'super_admin' ? 'Super Admin' : 'Admin Rental' }}</span>
                </div>
            </header>

            <section class="admin-content">
                @yield('content')
            </section>
        </main>
    </div>
</body>
</html>
