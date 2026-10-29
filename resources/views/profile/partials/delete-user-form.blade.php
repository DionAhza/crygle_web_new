<section>
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-start gap-4">
            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-rose-50 text-rose-600">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
            </span>
            <div>
                <h2 class="text-base font-bold text-slate-800">Hapus Akun</h2>
                <p class="mt-0.5 max-w-xl text-sm text-slate-500">Setelah akun dihapus, seluruh data dan sumber daya Anda akan terhapus permanen. Unduh dulu data yang ingin Anda simpan.</p>
            </div>
        </div>

        <x-btn variant="danger-soft" type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')">Hapus Akun</x-btn>
    </div>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable maxWidth="md">
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-rose-50 text-rose-600">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </span>

            <h2 class="mt-4 text-lg font-bold text-slate-800">Yakin ingin menghapus akun?</h2>
            <p class="mt-1 text-sm text-slate-500">Tindakan ini tidak dapat dibatalkan. Masukkan password Anda untuk mengonfirmasi penghapusan akun secara permanen.</p>

            <div class="mt-5">
                <x-input-label for="password" value="Password" class="sr-only" />
                <x-text-input id="password" name="password" type="password" placeholder="Password" />
                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-1.5" />
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <x-secondary-button x-on:click="$dispatch('close')">Batal</x-secondary-button>
                <x-danger-button>Ya, Hapus Akun</x-danger-button>
            </div>
        </form>
    </x-modal>
</section>
