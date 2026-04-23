@extends('layouts.admin')

@section('title', 'Log Aktivitas')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Log Aktivitas Sistem</h1>
            <p class="text-gray-600 mt-2">Pantau semua aktivitas dan perubahan sistem</p>
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
            <form method="GET" action="{{ route('super-admin.activity-logs.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Aksi</label>
                    <input type="text" name="action" value="{{ request('action') }}" 
                           placeholder="Cari aksi (misal: vehicle.created)"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pengguna</label>
                    <select name="user_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Semua Pengguna --</option>
                        @foreach($userOptions as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} ({{ ucfirst($user->role) }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Activity Logs Table -->
        @if($activityLogs->count() > 0)
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                Waktu
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                Pengguna
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                Aksi
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                Target
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                Deskripsi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach($activityLogs as $log)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    <span title="{{ $log->created_at->format('d M Y H:i:s') }}">
                                        {{ $log->created_at->diffForHumans() }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if($log->user)
                                        <div class="font-medium text-gray-800">{{ $log->user->name }}</div>
                                        <div class="text-xs text-gray-500">{{ ucfirst($log->user->role) }}</div>
                                    @else
                                        <span class="text-gray-400 italic">Sistem</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span class="px-3 py-1 inline-block rounded-full text-xs font-semibold
                                            {{ match(true) {
                                                str_contains($log->action, 'created') => 'bg-green-100 text-green-800',
                                                str_contains($log->action, 'updated') => 'bg-blue-100 text-blue-800',
                                                str_contains($log->action, 'deleted') => 'bg-red-100 text-red-800',
                                                str_contains($log->action, 'rejected') => 'bg-red-100 text-red-800',
                                                str_contains($log->action, 'cancelled') => 'bg-orange-100 text-orange-800',
                                                default => 'bg-gray-100 text-gray-800',
                                            } }}">
                                        {{ str_replace('.', ' / ', $log->action) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ ucfirst(str_replace('_', ' ', $log->target_type)) }} #{{ $log->target_id }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    <div class="truncate">{{ $log->description }}</div>
                                    @if($log->meta && count($log->meta) > 0)
                                        <details class="text-xs text-gray-500 mt-1 cursor-pointer">
                                            <summary>Details</summary>
                                            <pre class="mt-1 bg-gray-50 p-2 rounded text-xs overflow-auto max-h-24">{{ json_encode($log->meta, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                        </details>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $activityLogs->links() }}
            </div>
        @else
            <x-empty-state
                icon="📋"
                title="Tidak Ada Log Aktivitas"
                message="Belum ada aktivitas yang tercatat."
            />
        @endif
    </div>
</div>
@endsection
