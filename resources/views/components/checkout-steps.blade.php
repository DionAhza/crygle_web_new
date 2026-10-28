@props(['active' => 2])
@php
    $steps = [
        1 => ['Login', 'M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l3 3m0 0-3 3m3-3H2.25'],
        2 => ['Pembayaran', 'M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z'],
        3 => ['Review', 'M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z'],
    ];
@endphp
<ol class="mx-auto flex max-w-md items-start" aria-label="Langkah pembayaran">
    @foreach ($steps as $n => [$label, $icon])
        <li class="flex flex-col items-center">
            <span class="flex h-6 w-6 items-center justify-center rounded-md {{ $n <= $active ? 'bg-brand text-white' : 'bg-blue-100 text-brand' }}">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}"/></svg>
            </span>
            <span class="mt-1.5 text-[11px] font-medium {{ $n === $active ? 'text-slate-800' : 'text-slate-500' }}">{{ $label }}</span>
        </li>
        @if (! $loop->last)
            <li aria-hidden="true" class="mx-2 mt-3 flex-1 border-t-2 border-dotted {{ $n < $active ? 'border-brand' : 'border-blue-300' }}"></li>
        @endif
    @endforeach
</ol>
