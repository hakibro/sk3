@props(['active', 'icon', 'isCenter' => false, 'badge' => 0])

@php
    $classes = $active ?? false ? 'text-indigo-600' : 'text-gray-400 hover:text-indigo-500';
@endphp

<a
    {{ $attributes->merge(['class' => 'flex flex-col items-center w-full relative transition-colors duration-200 ' . $classes]) }}>
    <div
        class="{{ $isCenter ? 'bg-indigo-600 text-white p-4 rounded-full -mt-10 shadow-lg border-4 border-gray-50' : 'relative' }}">
        <i class="fas fa-{{ $icon }} {{ $isCenter ? 'text-xl' : 'text-lg' }}"></i>

        @if ($badge > 0 && !$isCenter)
            <span
                class="absolute -top-2 -right-2 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white">
                {{ $badge }}
            </span>
        @endif
    </div>
    <span class="text-[10px] mt-1 font-bold uppercase tracking-tighter {{ $isCenter ? 'mt-2' : '' }}">
        {{ $slot }}
    </span>
</a>
