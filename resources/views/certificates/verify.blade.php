<x-public-layout title="Verifikasi Sertifikat">
    <div class="mx-auto max-w-2xl">
        @if ($certificate)
            <div class="rounded-2xl border border-emerald-200 bg-white p-8 shadow-sm">
                <div class="flex items-center gap-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-full bg-emerald-100 text-xl text-emerald-700">✓</span>
                    <div>
                        <h1 class="text-xl font-bold text-slate-800">Sertifikat Terverifikasi</h1>
                        <p class="text-sm text-slate-500">Sertifikat ini diterbitkan secara resmi oleh Crygle Academy.</p>
                    </div>
                </div>

                <dl class="mt-6 divide-y divide-slate-100 text-sm">
                    @foreach ([
                        'Nama penerima' => $certificate['studentName'],
                        'Kelas' => $certificate['courseTitle'],
                        'Tingkat' => $certificate['courseLevel'],
                        'Mentor' => $certificate['mentorName'],
                        'Tanggal selesai' => $certificate['completedOn'],
                        'Kode sertifikat' => $certificate['code'],
                    ] as $label => $value)
                        <div class="flex flex-wrap justify-between gap-2 py-3">
                            <dt class="text-slate-500">{{ $label }}</dt>
                            <dd class="font-semibold text-slate-800 {{ $label === 'Kode sertifikat' ? 'font-mono' : '' }}">{{ $value }}</dd>
                        </div>
                    @endforeach
                </dl>
            </div>
        @else
            <div class="rounded-2xl border border-rose-200 bg-white p-8 text-center shadow-sm">
                <h1 class="text-xl font-bold text-slate-800">Sertifikat Tidak Ditemukan</h1>
                <p class="mt-2 text-sm text-slate-500">Kode <span class="font-mono font-semibold text-slate-700">{{ $code }}</span> tidak cocok dengan sertifikat mana pun. Periksa kembali kode pada sertifikat Anda.</p>
            </div>
        @endif
    </div>
</x-public-layout>
