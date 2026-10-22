@props(['name', 'label', 'type' => 'text'])

<div>
    <label for="{{ $name }}" class="block text-xs font-medium text-[#202020]">{{ $label }}</label>
    <input id="{{ $name }}" name="{{ $name }}" type="{{ $type }}"
           @if ($type !== 'password') value="{{ old($name) }}" @endif
           {{ $attributes->merge(['class' => 'mt-1.5 block h-11 w-full rounded-lg border border-brand-border bg-white px-3.5 text-sm text-[#202020] placeholder:text-slate-400 focus:border-brand focus:ring-1 focus:ring-brand']) }}>
    <x-input-error :messages="$errors->get($name)" class="mt-1.5" />
</div>
