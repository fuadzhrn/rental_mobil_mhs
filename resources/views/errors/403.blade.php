@extends('layouts.admin')

@section('title', 'Akses Ditolak')

@section('content')
<div class="container mx-auto px-4 py-12">
    <div class="flex justify-center">
        <div class="w-full max-w-md">
            <div class="bg-white rounded-lg shadow-md p-8 text-center">
                <div class="mb-4">
                    <svg class="w-16 h-16 mx-auto text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4v2m0 0v2M9 5h6M5 9a4 4 0 014-4h6a4 4 0 014 4v10a4 4 0 01-4 4H9a4 4 0 01-4-4V9z"/>
                    </svg>
                </div>
                <h1 class="text-4xl font-bold text-gray-800 mb-2">403</h1>
                <p class="text-xl text-gray-600 mb-2">Akses Ditolak</p>
                <p class="text-gray-500 mb-8">
                    Anda tidak memiliki izin untuk mengakses halaman ini.
                </p>
                <a href="{{ route('home') }}" class="inline-block px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Kembali ke Beranda
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
