{{-- Panel pengumpulan tugas (Fase 5). Variabel dari learn.blade.php: $course, $lesson, $isCompleted, $submissions, $canSubmit --}}
@php
    $assignment = $lesson->assignment;
    $latest = $submissions->first();
    $type = $assignment->submission_type;
@endphp

<div class="mt-5 space-y-5">

    {{-- Status terkini --}}
    @if ($isCompleted)
        <p class="text-sm font-semibold text-emerald-700">✓ Tugas telah disetujui.</p>
    @elseif ($latest?->isPending())
        <div class="rounded-lg border border-amber-200 bg-amber-50 p-3 text-sm text-amber-800">
            ⏳ Pengiriman Anda sedang menunggu review mentor. Materi berikutnya terbuka setelah tugas disetujui.
        </div>
    @elseif ($latest?->isRejected())
        <div class="rounded-lg border border-rose-200 bg-rose-50 p-3 text-sm text-rose-800">
            <span class="font-semibold">Tugas perlu direvisi.</span> Baca masukan mentor pada riwayat di bawah, perbaiki, lalu kirim ulang. Pengiriman sebelumnya tetap tersimpan.
        </div>
    @endif

    {{-- Formulir (pengiriman pertama atau revisi) --}}
    @if ($canSubmit)
        <form method="POST" action="{{ route('student.submissions.store', [$course, $lesson]) }}" enctype="multipart/form-data" class="space-y-4 rounded-lg border border-slate-200 bg-white p-4">
            @csrf
            <h4 class="text-sm font-semibold text-slate-800">{{ $latest ? 'Kirim Revisi' : 'Kumpulkan Tugas' }}</h4>

            @if ($type === 'file')
                <div>
                    <x-input-label for="attachment" value="Berkas tugas" />
                    <input id="attachment" name="attachment" type="file" required
                           class="mt-1 block w-full text-sm text-slate-600 file:mr-3 file:rounded-lg file:border-0 file:bg-indigo-50 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-indigo-700 hover:file:bg-indigo-100">
                    <p class="mt-1 text-xs text-slate-500">PDF, ZIP, DOC/DOCX, PPT/PPTX, PNG, JPG, atau TXT · maks. {{ round(config('crygle.submission_max_kb') / 1024, 1) }} MB.</p>
                    <x-input-error class="mt-1" :messages="$errors->get('attachment')" />
                </div>
            @elseif ($type === 'repo_link')
                <div>
                    <x-input-label for="attachment_url" value="Tautan repository" />
                    <x-text-input id="attachment_url" name="attachment_url" type="url" class="mt-1 block w-full" placeholder="https://github.com/username/proyek-saya" :value="old('attachment_url')" required />
                    <x-input-error class="mt-1" :messages="$errors->get('attachment_url')" />
                </div>
            @endif

            <div>
                <x-input-label for="student_note" :value="$type === 'text' ? 'Jawaban Anda' : 'Catatan untuk mentor (opsional)'" />
                <textarea id="student_note" name="student_note" rows="{{ $type === 'text' ? 8 : 3 }}" @required($type === 'text')
                          class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">{{ old('student_note') }}</textarea>
                <x-input-error class="mt-1" :messages="$errors->get('student_note')" />
            </div>

            <button class="rounded-lg bg-indigo-600 px-5 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Kirim Tugas</button>
        </form>
    @endif

    {{-- Riwayat pengiriman + masukan mentor --}}
    @if ($submissions->isNotEmpty())
        <div>
            <h4 class="text-sm font-semibold text-slate-800">Riwayat Pengiriman</h4>
            <ol class="mt-2 space-y-3">
                @foreach ($submissions as $item)
                    <li class="rounded-lg border border-slate-200 bg-white p-4">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <div class="text-sm font-semibold text-slate-800">
                                Pengiriman ke-{{ $submissions->count() - $loop->index }}
                                <span class="ms-1 text-xs font-normal text-slate-400">{{ $item->created_at->translatedFormat('d M Y, H:i') }}</span>
                            </div>
                            <x-submission-status :status="$item->status" />
                        </div>

                        <div class="mt-3">
                            <x-submission-body :submission="$item" :type="$type" />
                        </div>

                        @if (! $item->isPending())
                            <div class="mt-3 rounded-md border-l-4 {{ $item->isApproved() ? 'border-emerald-400 bg-emerald-50' : 'border-rose-400 bg-rose-50' }} p-3 text-sm">
                                <div class="font-semibold text-slate-800">
                                    Skor: {{ $item->score }}/100
                                    <span class="ms-1 text-xs font-normal text-slate-500">oleh {{ $item->reviewer?->name ?? 'Mentor' }} · {{ $item->reviewed_at?->translatedFormat('d M Y, H:i') }}</span>
                                </div>
                                @if ($item->mentor_feedback)
                                    <p class="mt-1 whitespace-pre-line text-slate-700">{{ $item->mentor_feedback }}</p>
                                @endif
                            </div>
                        @endif
                    </li>
                @endforeach
            </ol>
        </div>
    @endif
</div>
