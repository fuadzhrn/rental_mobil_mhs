@extends('layouts.admin')

@section('title', 'Dashboard Admin Rental')
@section('page_title', 'Dashboard Admin Rental')

@section('content')
    <p class="page-description">
        Selamat datang di area Admin Rental. Pantau kendaraan, booking, customer, dan promo dari dashboard ini.
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
