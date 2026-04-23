@extends('layouts.admin')

@section('title', 'Notifikasi')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Notifikasi</h1>
                <p class="text-gray-600 mt-2">Kelola notifikasi Anda</p>
            </div>
            @if($notifications->total() > 0)
                <form action="{{ route('notifications.read-all') }}" method="POST" style="display: inline;">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        Tandai Semua Dibaca
                    </button>
                </form>
            @endif
        </div>

        <!-- Alerts -->
        @if($errors->any())
            <x-alert type="error" title="Kesalahan">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </x-alert>
        @endif

        @if(session('success'))
            <x-alert type="success" title="Berhasil">
                {{ session('success') }}
            </x-alert>
        @endif

        <!-- Filter -->
        <div class="mb-6 bg-white rounded-lg shadow-sm p-4">
            <form method="GET" action="{{ route('notifications.index') }}" class="flex gap-4 items-center">
                <div class="flex-1">
                    <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="all" {{ request('status', 'all') === 'all' ? 'selected' : '' }}>
                            Semua Notifikasi
                        </option>
                        <option value="unread" {{ request('status') === 'unread' ? 'selected' : '' }}>
                            Belum Dibaca
                        </option>
                        <option value="read" {{ request('status') === 'read' ? 'selected' : '' }}>
                            Sudah Dibaca
                        </option>
                    </select>
                </div>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Filter
                </button>
            </form>
        </div>

        <!-- Notifications List -->
        @if($notifications->count() > 0)
            <div class="space-y-4">
                @foreach($notifications as $notification)
                    <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 {{ $notification->read_at ? 'border-gray-300' : 'border-blue-500' }}
                             {{ !$notification->read_at ? 'bg-blue-50' : '' }}">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <h3 class="font-semibold text-gray-800">{{ $notification->title }}</h3>
                                    <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full
                                            {{ match($notification->type) {
                                                'success' => 'bg-green-100 text-green-800',
                                                'warning' => 'bg-yellow-100 text-yellow-800',
                                                'error' => 'bg-red-100 text-red-800',
                                                default => 'bg-blue-100 text-blue-800',
                                            } }}">
                                        {{ ucfirst($notification->type) }}
                                    </span>
                                    @if(!$notification->read_at)
                                        <span class="inline-block w-3 h-3 bg-blue-500 rounded-full"></span>
                                    @endif
                                </div>
                                <p class="text-gray-600 mb-3">{{ $notification->message }}</p>
                                <p class="text-sm text-gray-500">
                                    {{ $notification->created_at->format('d M Y H:i') }}
                                </p>
                            </div>
                            <div class="flex gap-2 ml-4">
                                @if(!$notification->read_at)
                                    <form action="{{ route('notifications.read', $notification) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="px-3 py-1 text-sm bg-blue-100 text-blue-700 rounded hover:bg-blue-200 transition">
                                            Tandai Dibaca
                                        </button>
                                    </form>
                                @endif
                                @if($notification->url)
                                    <a href="{{ $notification->url }}" class="px-3 py-1 text-sm bg-gray-100 text-gray-700 rounded hover:bg-gray-200 transition">
                                        Lihat
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $notifications->links() }}
            </div>
        @else
            <x-empty-state
                icon="📭"
                title="Tidak Ada Notifikasi"
                message="Anda tidak memiliki notifikasi saat ini."
            />
        @endif
    </div>
</div>
@endsection
