@props(['variant' => 'primary', 'size' => 'md', 'href' => null])
@php
    $sizes = ['sm' => 'px-4 py-2 text-xs', 'md' => 'px-6 py-2.5 text-sm'];
    $variants = [
        'primary' => 'bg-brand text-white shadow-md shadow-brand/30 hover:bg-brand-dark',
        'secondary' => 'border border-slate-200 bg-white text-slate-700 hover:bg-slate-50',
        'soft' => 'bg-brand-50 text-brand hover:bg-blue-100',
        'danger' => 'bg-rose-600 text-white hover:bg-rose-700',
        'danger-soft' => 'border border-rose-200 bg-white text-rose-600 hover:bg-rose-50',
    ];
    $class = 'inline-flex items-center justify-center gap-2 rounded-full font-semibold transition focus:outline-none focus:ring-2 focus:ring-brand/40 focus:ring-offset-2 disabled:opacity-50 '
        .($sizes[$size] ?? $sizes['md']).' '.($variants[$variant] ?? $variants['primary']);
@endphp
@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $class]) }}>{{ $slot }}</a>
@else
    <button {{ $attributes->merge(['type' => 'submit', 'class' => $class]) }}>{{ $slot }}</button>
@endif
