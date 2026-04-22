@extends('layouts.admin')

@section('title', 'Dashboard Super Admin')
@section('page_title', 'Dashboard Super Admin')

@section('content')
    <p class="page-description">
        Selamat datang di area Super Admin. Komisi saat ini dihitung sebesar {{ $commissionRate }}% dari transaksi verified.
    </p>

    <div class="stat-grid">
        @foreach ($summary as $item)
            <article class="stat-card">
                <p>{{ $item['label'] }}</p>
                <h3>{{ $item['value'] }}</h3>
            </article>
        @endforeach
    </div>

    <section style="margin-top:16px; background:#fff; border:1px solid #e5e7eb; border-radius:14px; padding:14px;">
        <h3 style="margin:0 0 10px; font-family:'Montserrat', sans-serif;">Akses Cepat</h3>
        <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(180px,1fr)); gap:10px;">
            @foreach ($quickLinks as $item)
                <a href="{{ $item['route'] }}" style="display:block; text-decoration:none; background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:12px; color:#0f172a; font-weight:600;">
                    {{ $item['label'] }}
                </a>
            @endforeach
        </div>
    </section>
@endsection
