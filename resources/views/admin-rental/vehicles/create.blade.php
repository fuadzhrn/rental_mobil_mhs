@extends('layouts.admin')

@section('title', 'Tambah Kendaraan | Admin Rental')
@section('page_title', 'Tambah Kendaraan')

@section('content')
    @if (session('error'))
        <div style="margin-bottom:16px; padding:12px 14px; background:#fef2f2; border:1px solid #fecaca; color:#991b1b; border-radius:10px;">
            {{ session('error') }}
        </div>
    @endif

    <div style="margin-bottom:18px;">
        <a href="{{ route('admin-rental.vehicles.index') }}" style="color:#2563eb; text-decoration:none; font-weight:600;">&larr; Kembali ke Data Kendaraan</a>
    </div>

    <div style="background:#fff; border:1px solid #e5e7eb; border-radius:14px; padding:20px;">
        <form method="POST" action="{{ route('admin-rental.vehicles.store') }}" enctype="multipart/form-data">
            @csrf
            @include('admin-rental.vehicles.form', ['vehicle' => $vehicle, 'submitLabel' => 'Simpan Kendaraan'])
        </form>
    </div>
@endsection
