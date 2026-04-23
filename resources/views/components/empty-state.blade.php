@props([
    'icon' => '📭',
    'title' => 'Tidak Ada Data',
    'message' => 'Mulai dengan membuat item baru.',
    'link' => null,
    'linkText' => 'Buat Item',
])

<div class="flex flex-col items-center justify-center py-12 text-center">
    <div class="text-6xl mb-4">{{ $icon }}</div>
    <h3 class="text-xl font-semibold text-gray-800 mb-2">{{ $title }}</h3>
    <p class="text-gray-500 mb-6 max-w-sm">{{ $message }}</p>
    @if($link)
        <a href="{{ $link }}" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
            {{ $linkText }}
        </a>
    @endif
</div>
