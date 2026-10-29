@props(['title', 'description' => null, 'icon' => null])

<header {{ $attributes->merge(['class' => 'flex items-start gap-4']) }}>
    @if ($icon)
        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-brand">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="{{ $icon }}"/></svg>
        </span>
    @endif
    <div class="min-w-0 flex-1">
        <h2 class="text-base font-bold text-slate-800">{{ $title }}</h2>
        @if ($description)
            <p class="mt-0.5 text-sm text-slate-500">{{ $description }}</p>
        @endif
    </div>
    {{ $slot }}
</header>
