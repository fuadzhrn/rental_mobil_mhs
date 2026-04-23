@props([
    'type' => 'info',
    'title' => null,
    'dismissible' => true,
])

@php
    $colors = [
        'info' => 'bg-blue-50 border-blue-200 text-blue-800',
        'success' => 'bg-green-50 border-green-200 text-green-800',
        'warning' => 'bg-yellow-50 border-yellow-200 text-yellow-800',
        'error' => 'bg-red-50 border-red-200 text-red-800',
    ];

    $bgColor = $colors[$type] ?? $colors['info'];

    $icons = [
        'info' => 'ℹ️',
        'success' => '✓',
        'warning' => '⚠️',
        'error' => '✕',
    ];

    $icon = $icons[$type] ?? $icons['info'];
@endphp

<div class="alert-{{ $type }} border rounded-lg p-4 mb-4 {{ $bgColor }}" role="alert" id="alert-{{ uniqid() }}">
    <div class="flex items-start">
        <span class="flex-shrink-0 text-lg mr-3">{{ $icon }}</span>
        <div class="flex-1">
            @if($title)
                <h4 class="font-semibold mb-1">{{ $title }}</h4>
            @endif
            <div class="text-sm">
                {{ $slot }}
            </div>
        </div>
        @if($dismissible)
            <button type="button" class="flex-shrink-0 ml-3 text-gray-400 hover:text-gray-600"
                    onclick="this.closest('.alert-{{ $type }}').remove()">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </button>
        @endif
    </div>
</div>
