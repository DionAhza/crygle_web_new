@props(['padding' => 'p-6'])

<div {{ $attributes->merge(['class' => 'rounded-[20px] border border-slate-100 bg-white shadow-sm '.$padding]) }}>{{ $slot }}</div>
