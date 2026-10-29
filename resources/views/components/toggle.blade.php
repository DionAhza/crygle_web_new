@props(['name', 'label', 'description' => null, 'checked' => false])

<label class="flex cursor-pointer items-center justify-between gap-4 rounded-xl border border-slate-200 p-4 transition has-[:checked]:border-brand-border has-[:checked]:bg-brand-50">
    <span class="min-w-0">
        <span class="block text-sm font-semibold text-slate-800">{{ $label }}</span>
        @if ($description)
            <span class="mt-0.5 block text-xs text-slate-500">{{ $description }}</span>
        @endif
    </span>
    <input type="checkbox" name="{{ $name }}" value="1" class="peer sr-only" @checked($checked)>
    <span class="relative h-6 w-11 shrink-0 rounded-full bg-slate-300 transition peer-checked:bg-brand peer-focus-visible:ring-2 peer-focus-visible:ring-brand/40 peer-focus-visible:ring-offset-2 after:absolute after:start-0.5 after:top-0.5 after:h-5 after:w-5 after:rounded-full after:bg-white after:shadow after:transition peer-checked:after:translate-x-5"></span>
</label>
