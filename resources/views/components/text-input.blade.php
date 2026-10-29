@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'block h-11 w-full rounded-lg border border-brand-border bg-white px-3.5 text-sm text-slate-800 placeholder:text-slate-400 focus:border-brand focus:ring-1 focus:ring-brand disabled:cursor-not-allowed disabled:bg-slate-50 disabled:text-slate-400']) }}>
