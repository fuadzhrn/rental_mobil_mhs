@extends('layouts.admin')

@section('title', 'Dashboard Super Admin')
@section('page_title', 'Dashboard Super Admin')

@section('content')
    <p class="page-description">
        Selamat datang di area Super Admin. Gunakan menu di samping untuk mengelola rental, user, laporan, dan komisi.
    </p>

    <div class="stat-grid">
        @foreach ($summary as $item)
            <article class="stat-card">
                <p>{{ $item['label'] }}</p>
                <h3>{{ $item['value'] }}</h3>
            </article>
        @endforeach
    </div>
@endsection
