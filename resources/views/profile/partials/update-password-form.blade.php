<section>
    <x-section-title title="Ubah Password" description="Gunakan password yang panjang dan acak agar akun tetap aman."
                     icon="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-5">
        @csrf
        @method('put')

        <x-form-field label="Password Saat Ini" for="update_password_current_password" name="current_password" bag="updatePassword">
            <x-text-input id="update_password_current_password" name="current_password" type="password" autocomplete="current-password" />
        </x-form-field>

        <div class="grid gap-5 sm:grid-cols-2">
            <x-form-field label="Password Baru" for="update_password_password" name="password" bag="updatePassword">
                <x-text-input id="update_password_password" name="password" type="password" autocomplete="new-password" />
            </x-form-field>

            <x-form-field label="Konfirmasi Password Baru" for="update_password_password_confirmation" name="password_confirmation" bag="updatePassword">
                <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" />
            </x-form-field>
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>Perbarui Password</x-primary-button>

            @if (session('status') === 'password-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2500)"
                   class="inline-flex items-center gap-1.5 text-sm font-semibold text-emerald-600">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    Tersimpan
                </p>
            @endif
        </div>
    </form>
</section>
