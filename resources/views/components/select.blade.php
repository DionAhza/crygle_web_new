<select {{ $attributes->merge(['class' => 'block h-11 w-full rounded-lg border border-brand-border bg-white px-3.5 text-sm text-slate-800 focus:border-brand focus:ring-1 focus:ring-brand']) }}>
    {{ $slot }}
</select>
