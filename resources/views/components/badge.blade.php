@props(['tone' => 'slate'])
@php
    $tones = [
        'brand' => 'bg-brand-50 text-brand',
        'green' => 'bg-emerald-100 text-emerald-700',
        'amber' => 'bg-amber-100 text-amber-700',
        'rose' => 'bg-rose-100 text-rose-700',
        'slate' => 'bg-slate-100 text-slate-600',
    ];
@endphp
<span {{ $attributes->merge(['class' => 'inline-flex items-center rounded-full px-2.5 py-0.5 text-[11px] font-semibold '.($tones[$tone] ?? $tones['slate'])]) }}>{{ $slot }}</span>
