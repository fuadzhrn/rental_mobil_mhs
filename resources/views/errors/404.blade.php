@extends('layouts.admin')

@section('title', 'Halaman Tidak Ditemukan')

@section('content')
<div class="container mx-auto px-4 py-12">
    <div class="flex justify-center">
        <div class="w-full max-w-md">
            <div class="bg-white rounded-lg shadow-md p-8 text-center">
                <div class="mb-4">
                    <svg class="w-16 h-16 mx-auto text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h1 class="text-4xl font-bold text-gray-800 mb-2">404</h1>
                <p class="text-xl text-gray-600 mb-2">Halaman Tidak Ditemukan</p>
                <p class="text-gray-500 mb-8">
                    Halaman yang Anda cari tidak ada atau telah dihapus.
                </p>
                <a href="{{ route('home') }}" class="inline-block px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Kembali ke Beranda
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
