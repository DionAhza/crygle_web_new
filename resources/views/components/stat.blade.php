@props(['label', 'value', 'icon' => null, 'href' => null, 'tone' => 'brand'])
@php
    $tones = [
        'brand' => 'bg-brand-50 text-brand',
        'green' => 'bg-emerald-50 text-emerald-600',
        'amber' => 'bg-amber-50 text-amber-600',
        'violet' => 'bg-violet-50 text-violet-600',
    ];
    $tag = $href ? 'a' : 'div';
@endphp
<{{ $tag }} @if ($href) href="{{ $href }}" @endif {{ $attributes->merge(['class' => 'flex items-center gap-4 rounded-[20px] border border-slate-100 bg-white p-5 shadow-sm '.($href ? 'transition hover:-translate-y-0.5 hover:border-brand-border hover:shadow-md' : '')]) }}>
    @if ($icon)
        <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl {{ $tones[$tone] ?? $tones['brand'] }}">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="{{ $icon }}"/></svg>
        </span>
    @endif
    <div class="min-w-0">
        <div class="text-xs font-medium text-slate-500">{{ $label }}</div>
        <div class="mt-0.5 text-2xl font-extrabold text-slate-800">{{ $value }}</div>
    </div>
</{{ $tag }}>
