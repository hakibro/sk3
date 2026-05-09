@props(['active'])

@php
    $classes =
        $active ?? false
            ? 'bg-indigo-800 text-white shadow-inner border-l-4 border-indigo-300'
            : 'text-indigo-100 hover:bg-indigo-800 hover:text-white transition-all';
@endphp

<a {{ $attributes->merge(['class' => 'flex items-center px-4 py-3 text-sm font-medium rounded-lg ' . $classes]) }}>
    {{ $slot }}
</a>
