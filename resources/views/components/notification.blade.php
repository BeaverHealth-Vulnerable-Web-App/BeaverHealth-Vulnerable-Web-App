@props(['type' => 'success', 'message' => ''])

@php
    $styles = [
        'success' => 'bg-green-100 dark:bg-green-900 border border-green-200 dark:border-green-700 text-green-800 dark:text-green-300',
        'error' => 'bg-red-100 dark:bg-red-900 border border-red-200 dark:border-red-700 text-red-800 dark:text-red-300',
    ];
@endphp

@if ($message)
    <div id="notification" 
         x-data="{ show: true }" 
         x-show="show" 
         x-transition:leave="opacity-0 transition ease-in-out duration-500" 
         class="absolute top-0 right-0 mt-2 mr-2 p-2 rounded text-sm {{ $styles[$type] ?? 'bg-gray-100 border-gray-500 text-gray-700' }}"
         x-init="setTimeout(() => show = false, {{ $type === 'error' ? 5000 : 3000 }})">
        <span id="notification-text">{{ $message }}</span>
    </div>
@endif
