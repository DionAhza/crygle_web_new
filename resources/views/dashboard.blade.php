<x-panel-layout title="Dashboard" heading="Dashboard">
    <x-card>
        <x-empty-state title="Selamat datang di Crygle Academy" description="Akun Anda belum memiliki peran. Hubungi administrator untuk mendapatkan akses.">
            <x-btn :href="route('profile.edit')" size="sm">Buka Pengaturan</x-btn>
        </x-empty-state>
    </x-card>
</x-panel-layout>
