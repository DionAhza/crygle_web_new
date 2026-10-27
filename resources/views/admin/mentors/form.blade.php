<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">{{ $mentor->exists ? 'Edit Mentor' : 'Mentor Baru' }}</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <x-flash />

            @if ($types->isEmpty())
                <div class="rounded-xl border border-amber-200 bg-amber-50 p-5 text-sm text-amber-800">
                    Belum ada tipe mentor. <a href="{{ route('admin.mentor-types.index') }}" class="font-semibold underline">Buat tipe mentor</a> terlebih dahulu.
                </div>
            @else
                <form method="POST" action="{{ $mentor->exists ? route('admin.mentors.update', $mentor) : route('admin.mentors.store') }}" class="space-y-5 rounded-xl border border-slate-100 bg-white p-6 shadow-sm">
                    @csrf
                    @if ($mentor->exists) @method('PUT') @endif

                    <div>
                        <x-input-label for="name" value="Nama Lengkap" />
                        <x-text-input id="name" name="name" class="mt-1 block w-full" :value="old('name', $mentor->name)" required />
                    </div>
                    <div>
                        <x-input-label for="email" value="Email" />
                        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $mentor->email)" required />
                    </div>
                    <div>
                        <x-input-label for="mentor_type_id" value="Tipe Mentor" />
                        <select id="mentor_type_id" name="mentor_type_id" required class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="">— Pilih tipe —</option>
                            @foreach ($types as $type)
                                <option value="{{ $type->id }}" @selected((int) old('mentor_type_id', $mentor->mentor_type_id) === $type->id)>{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid gap-5 sm:grid-cols-2">
                        <div>
                            <x-input-label for="password" :value="$mentor->exists ? 'Password Baru (opsional)' : 'Password Awal'" />
                            <x-text-input id="password" name="password" type="password" autocomplete="new-password" class="mt-1 block w-full" :required="! $mentor->exists" />
                        </div>
                        <div>
                            <x-input-label for="password_confirmation" value="Konfirmasi Password" />
                            <x-text-input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" class="mt-1 block w-full" />
                        </div>
                    </div>
                    @unless ($mentor->exists)
                        <p class="text-xs text-slate-500">Sampaikan password awal ini kepada mentor secara aman. Email akun langsung dianggap terverifikasi.</p>
                    @endunless

                    <div class="flex items-center gap-3 pt-2">
                        <button class="rounded-full bg-[#2D68A8] px-6 py-2.5 text-sm font-semibold text-white hover:bg-blue-800">Simpan</button>
                        <a href="{{ route('admin.mentors.index') }}" class="text-sm text-slate-600 hover:underline">Batal</a>
                    </div>
                </form>
            @endif
        </div>
    </div>
</x-app-layout>
