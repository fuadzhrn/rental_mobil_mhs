@extends('layouts.admin')

@section('title', 'Sesi Kadaluarsa')

@section('content')
<div class="container mx-auto px-4 py-12">
    <div class="flex justify-center">
        <div class="w-full max-w-md">
            <div class="bg-white rounded-lg shadow-md p-8 text-center">
                <div class="mb-4">
                    <svg class="w-16 h-16 mx-auto text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h1 class="text-4xl font-bold text-gray-800 mb-2">419</h1>
                <p class="text-xl text-gray-600 mb-2">Sesi Kadaluarsa</p>
                <p class="text-gray-500 mb-8">
                    Sesi Anda telah kadaluarsa karena tidak ada aktivitas. Silakan masuk kembali.
                </p>
                <a href="{{ route('login') }}" class="inline-block px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Masuk Kembali
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
