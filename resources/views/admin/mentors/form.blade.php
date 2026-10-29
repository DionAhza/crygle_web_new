<x-panel-layout :title="$mentor->exists ? 'Edit Mentor' : 'Mentor Baru'"
                :heading="$mentor->exists ? 'Edit Mentor' : 'Mentor Baru'"
                :subtitle="$mentor->exists ? 'Perbarui data akun mentor.' : 'Daftarkan akun mentor baru dan tetapkan tipenya.'"
                :back="['label' => 'Akun Mentor', 'href' => route('admin.mentors.index')]"
                width="max-w-2xl">
    <x-flash />

    @if ($types->isEmpty())
        <div class="rounded-[20px] border border-amber-200 bg-amber-50 p-6 text-sm text-amber-800">
            Belum ada tipe mentor. <a href="{{ route('admin.mentor-types.index') }}" class="font-semibold underline">Buat tipe mentor</a> terlebih dahulu.
        </div>
    @else
        <form method="POST" action="{{ $mentor->exists ? route('admin.mentors.update', $mentor) : route('admin.mentors.store') }}" class="space-y-6">
            @csrf
            @if ($mentor->exists) @method('PUT') @endif

            <x-card class="space-y-5">
                <x-section-title title="Data Mentor" description="Informasi dasar dan spesialisasi mentor."
                                 icon="M12 12a4 4 0 100-8 4 4 0 000 8zm0 2c-3.3 0-6 1.8-6 4v2h12v-2c0-2.2-2.7-4-6-4z" />

                <x-form-field label="Nama Lengkap" for="name">
                    <x-text-input id="name" name="name" :value="old('name', $mentor->name)" required />
                </x-form-field>

                <div class="grid gap-5 sm:grid-cols-2">
                    <x-form-field label="Email" for="email">
                        <x-text-input id="email" name="email" type="email" :value="old('email', $mentor->email)" required />
                    </x-form-field>

                    <x-form-field label="Tipe Mentor" for="mentor_type_id">
                        <x-select id="mentor_type_id" name="mentor_type_id" required>
                            <option value="">— Pilih tipe —</option>
                            @foreach ($types as $type)
                                <option value="{{ $type->id }}" @selected((int) old('mentor_type_id', $mentor->mentor_type_id) === $type->id)>{{ $type->name }}</option>
                            @endforeach
                        </x-select>
                    </x-form-field>
                </div>
            </x-card>

            <x-card class="space-y-5">
                <x-section-title :title="$mentor->exists ? 'Ganti Password' : 'Password Awal'"
                                 :description="$mentor->exists ? 'Kosongkan jika tidak ingin mengubah password.' : 'Sampaikan password awal ini kepada mentor secara aman.'"
                                 icon="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />

                <div class="grid gap-5 sm:grid-cols-2">
                    <x-form-field :label="$mentor->exists ? 'Password Baru (opsional)' : 'Password Awal'" for="password">
                        <x-text-input id="password" name="password" type="password" autocomplete="new-password" :required="! $mentor->exists" />
                    </x-form-field>
                    <x-form-field label="Konfirmasi Password" for="password_confirmation">
                        <x-text-input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" />
                    </x-form-field>
                </div>

                @unless ($mentor->exists)
                    <p class="rounded-xl bg-brand-50 px-4 py-3 text-xs text-slate-600">Email akun langsung dianggap terverifikasi, sehingga mentor bisa langsung masuk.</p>
                @endunless
            </x-card>

            <div class="flex items-center gap-3">
                <x-btn>Simpan</x-btn>
                <x-btn :href="route('admin.mentors.index')" variant="secondary">Batal</x-btn>
            </div>
        </form>
    @endif
</x-panel-layout>
