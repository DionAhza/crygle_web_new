@props(['name', 'src' => null, 'size' => 'h-10 w-10 text-sm'])

<span {{ $attributes->merge(['class' => "flex shrink-0 items-center justify-center overflow-hidden rounded-full bg-brand font-bold text-white $size"]) }}>
    @if ($src)
        <img src="{{ $src }}" alt="" class="h-full w-full object-cover">
    @else
        {{ \Illuminate\Support\Str::of($name)->substr(0, 1)->upper() }}
    @endif
</span>
