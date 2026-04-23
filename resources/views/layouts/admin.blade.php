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
    @stack('styles')
</head>
<body class="admin-page">
    @php
        $authUser = auth()->user();
        $isSuperAdmin = $authUser?->role === 'super_admin';
    @endphp

    <div class="admin-shell">
        @if ($isSuperAdmin)
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

                <div style="display:flex; align-items:center; gap:10px;">
                    @if ($authUser)
                        <a href="{{ route('notifications.index') }}" style="text-decoration:none; color:#0f172a; font-weight:600; border:1px solid #dbe1eb; border-radius:10px; padding:8px 10px;">
                            Notifikasi
                            @if (($layoutUnreadNotificationsCount ?? 0) > 0)
                                <span style="background:#ef4444; color:#fff; border-radius:999px; padding:2px 8px; font-size:12px; margin-left:6px;">{{ $layoutUnreadNotificationsCount }}</span>
                            @endif
                        </a>
                    @endif

                    <div class="topbar-user">
                        <strong>{{ $authUser?->name ?? 'Guest' }}</strong>
                        <span>{{ $authUser ? ($isSuperAdmin ? 'Super Admin' : 'Admin Rental') : 'Unauthorized' }}</span>
                    </div>
                </div>
            </header>

            <section class="admin-content">
                @if (session('success'))
                    <div class="admin-flash admin-flash-success" role="status">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="admin-flash admin-flash-error" role="alert">
                        {{ session('error') }}
                    </div>
                @endif

                @if (session('warning'))
                    <div class="admin-flash admin-flash-warning" role="alert">
                        {{ session('warning') }}
                    </div>
                @endif

                @if (session('info'))
                    <div class="admin-flash admin-flash-info" role="status">
                        {{ session('info') }}
                    </div>
                @endif

                @yield('content')
            </section>
        </main>
    </div>
    @stack('scripts')
</body>
</html>
