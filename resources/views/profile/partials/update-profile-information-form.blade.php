<section>
    <x-section-title title="Informasi Profil" description="Perbarui nama dan alamat email akun Anda."
                     icon="M12 12a4 4 0 100-8 4 4 0 000 8zm0 2c-3.3 0-6 1.8-6 4v2h12v-2c0-2.2-2.7-4-6-4z" />

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-5">
        @csrf
        @method('patch')

        <div class="grid gap-5 sm:grid-cols-2">
            <x-form-field label="Nama Lengkap" for="name">
                <x-text-input id="name" name="name" type="text" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            </x-form-field>

            <x-form-field label="Alamat Email" for="email">
                <x-text-input id="email" name="email" type="email" :value="old('email', $user->email)" required autocomplete="username" />
            </x-form-field>
        </div>

        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
            <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                Alamat email Anda belum terverifikasi.
                <button form="send-verification" class="font-semibold underline hover:text-amber-900 focus:outline-none focus:ring-2 focus:ring-amber-400 rounded">Kirim ulang email verifikasi</button>

                @if (session('status') === 'verification-link-sent')
                    <p class="mt-1.5 font-semibold text-emerald-700">Tautan verifikasi baru sudah dikirim ke email Anda.</p>
                @endif
            </div>
        @endif

        <div class="flex items-center gap-4">
            <x-primary-button>Simpan Perubahan</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2500)"
                   class="inline-flex items-center gap-1.5 text-sm font-semibold text-emerald-600">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    Tersimpan
                </p>
            @endif
        </div>
    </form>
</section>
