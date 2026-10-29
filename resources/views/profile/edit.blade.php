@php
    $roleLabel = $user->hasRole('admin') ? 'Administrator' : ($user->hasRole('mentor') ? 'Mentor' : 'Siswa');
    $verified = ! ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail) || $user->hasVerifiedEmail();
@endphp
<x-panel-layout title="Pengaturan" heading="Pengaturan Akun" subtitle="Kelola informasi profil, keamanan, dan akun Anda.">
    <div class="grid gap-6 lg:grid-cols-[300px_1fr] lg:items-start">

        {{-- Ringkasan akun --}}
        <aside class="lg:sticky lg:top-6">
            <x-card class="overflow-hidden !p-0">
                <div class="h-20 bg-gradient-to-br from-brand to-[#1b4d82]"></div>
                <div class="-mt-10 px-6 pb-6 text-center">
                    <x-avatar :name="$user->name" :src="$user->avatar_url" size="mx-auto h-20 w-20 text-2xl ring-4 ring-white" />
                    <h2 class="mt-3 truncate text-lg font-bold text-slate-800">{{ $user->name }}</h2>
                    <p class="truncate text-sm text-slate-500">{{ $user->email }}</p>

                    <div class="mt-4 flex flex-wrap items-center justify-center gap-2">
                        <x-badge tone="brand">{{ $roleLabel }}</x-badge>
                        @if ($user->hasRole('mentor') && $user->mentorType)
                            <x-badge tone="slate">{{ $user->mentorType->name }}</x-badge>
                        @endif
                        <x-badge :tone="$verified ? 'green' : 'amber'">{{ $verified ? 'Email terverifikasi' : 'Email belum terverifikasi' }}</x-badge>
                    </div>

                    <dl class="mt-5 space-y-3 border-t border-slate-100 pt-5 text-start text-xs">
                        <div class="flex items-center justify-between gap-3">
                            <dt class="text-slate-500">Bergabung</dt>
                            <dd class="font-semibold text-slate-700">{{ $user->created_at?->translatedFormat('d M Y') ?? '-' }}</dd>
                        </div>
                        @if (filled($user->google_id))
                            <div class="flex items-center justify-between gap-3">
                                <dt class="text-slate-500">Login Google</dt>
                                <dd class="font-semibold text-emerald-600">Terhubung</dd>
                            </div>
                        @endif
                    </dl>
                </div>
            </x-card>
        </aside>

        {{-- Formulir --}}
        <div class="space-y-6">
            <x-card>
                @include('profile.partials.update-profile-information-form')
            </x-card>

            <x-card>
                @include('profile.partials.update-password-form')
            </x-card>

            <x-card class="!border-rose-100">
                @include('profile.partials.delete-user-form')
            </x-card>
        </div>
    </div>
</x-panel-layout>
