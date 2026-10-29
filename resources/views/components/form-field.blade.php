@props(['label', 'for' => null, 'name' => null, 'hint' => null, 'bag' => 'default'])
@php $field = $name ?? $for; @endphp

<div {{ $attributes }}>
    <label @if ($for) for="{{ $for }}" @endif class="block text-xs font-semibold text-slate-700">{{ $label }}</label>
    <div class="mt-1.5">{{ $slot }}</div>
    @if ($hint)
        <p class="mt-1.5 text-xs text-slate-500">{{ $hint }}</p>
    @endif
    @if ($field)
        <x-input-error :messages="$errors->getBag($bag)->get($field)" class="mt-1.5" />
    @endif
</div>
