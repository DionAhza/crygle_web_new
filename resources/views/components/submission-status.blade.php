@props(['status'])

@php
    $map = [
        'pending' => ['Menunggu review', 'bg-amber-100 text-amber-700'],
        'approved' => ['Disetujui', 'bg-emerald-100 text-emerald-700'],
        'rejected' => ['Perlu revisi', 'bg-rose-100 text-rose-700'],
    ];
    [$label, $class] = $map[$status] ?? [$status, 'bg-slate-100 text-slate-600'];
@endphp

<span {{ $attributes->merge(['class' => "inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold {$class}"]) }}>{{ $label }}</span>
