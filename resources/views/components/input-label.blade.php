@props(['value'])

<label {{ $attributes->merge(['class' => 'block text-xs font-semibold text-slate-700']) }}>
    {{ $value ?? $slot }}
</label>
