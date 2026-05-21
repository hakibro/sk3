@props(['active', 'icon', 'isCenter' => false, 'badge' => 0])

@php
    $classes = $active ?? false
        ? 'text-emerald-700'
        : 'text-gray-400 hover:text-emerald-700';
@endphp

<a {{ $attributes->merge(['class' => 'flex min-w-0 flex-1 flex-col items-center justify-center gap-1 rounded-xl px-2 py-2 text-center transition-colors duration-200 ' . $classes]) }}>
    <div class="relative flex h-8 w-8 items-center justify-center rounded-full {{ $active ?? false ? 'bg-emerald-100' : '' }}">
        <i class="fas fa-{{ $icon }} text-lg"></i>

        @if ($badge > 0 && !$isCenter)
            <span
                class="absolute -top-2 -right-2 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white">
                {{ $badge }}
            </span>
        @endif
    </div>
    <span class="max-w-full truncate text-[10px] font-bold uppercase tracking-normal">
        {{ $slot }}
    </span>
</a>
