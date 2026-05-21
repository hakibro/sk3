@props(['active'])

@php
    $classes =
        $active ?? false
            ? 'bg-emerald-800 text-white shadow-inner border-l-4 border-emerald-300'
            : 'text-emerald-100 hover:bg-emerald-900 hover:text-white transition-all';
@endphp

<a {{ $attributes->merge(['class' => 'flex items-center px-4 py-3 text-sm font-medium rounded-lg ' . $classes]) }}>
    {{ $slot }}
</a>
