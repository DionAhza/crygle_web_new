<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <div>
                <a href="{{ route('mentor.submissions.index') }}" class="text-xs font-medium text-indigo-600 hover:underline">← Review Tugas</a>
                <h2 class="font-semibold text-xl text-slate-800 leading-tight">{{ $assignment->title }}</h2>
                <p class="text-xs text-slate-500">{{ $course->title }} · {{ $lesson->title }}</p>
            </div>
            <x-submission-status :status="$submission->status" />
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6 px-4">
            <x-flash />

            {{-- Siswa & instruksi --}}
            <section class="rounded-xl bg-white p-5 shadow-sm border border-slate-100">
                <div class="text-sm text-slate-500">Siswa</div>
                <div class="font-semibold text-slate-800">{{ $submission->student->name }} <span class="text-xs font-normal text-slate-400">· {{ $submission->student->email }}</span></div>
                <div class="mt-1 text-xs text-slate-500">Nilai kelulusan: {{ $assignment->passing_score }} · Jenis: {{ ['file' => 'Unggah file', 'repo_link' => 'Tautan repository', 'text' => 'Teks'][$assignment->submission_type] }}</div>

                @if ($instructionsHtml)
                    <details class="mt-4">
                        <summary class="cursor-pointer text-sm font-semibold text-indigo-600">Lihat instruksi tugas</summary>
                        <div class="prose prose-sm prose-slate mt-3 max-w-none">{!! $instructionsHtml !!}</div>
                    </details>
                @endif
            </section>

            {{-- Pengiriman yang dinilai --}}
            <section class="rounded-xl bg-white p-5 shadow-sm border border-slate-100">
                <h3 class="font-semibold text-slate-800">Pengiriman ke-{{ $history->search(fn ($h) => $h->id === $submission->id) + 1 }}
                    <span class="ms-1 text-xs font-normal text-slate-400">{{ $submission->created_at->translatedFormat('d M Y, H:i') }}</span>
                </h3>
                <div class="mt-3">
                    <x-submission-body :submission="$submission" :type="$assignment->submission_type" />
                </div>
            </section>

            {{-- Form penilaian / hasil --}}
            @if ($submission->isPending())
                <form method="POST" action="{{ route('mentor.submissions.review', $submission) }}" class="space-y-5 rounded-xl bg-white p-6 shadow-sm border border-indigo-100">
                    @csrf @method('PATCH')
                    <h3 class="font-semibold text-slate-800">Penilaian</h3>

                    <div>
                        <x-input-label value="Keputusan" />
                        <div class="mt-2 grid gap-3 sm:grid-cols-2">
                            <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-slate-200 p-3 text-sm has-[:checked]:border-emerald-400 has-[:checked]:bg-emerald-50">
                                <input type="radio" name="decision" value="approved" class="text-emerald-600 focus:ring-emerald-500" @checked(old('decision') === 'approved') required>
                                <span><span class="font-semibold">Setujui</span><span class="block text-xs text-slate-500">Materi dihitung selesai, materi berikutnya terbuka.</span></span>
                            </label>
                            <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-slate-200 p-3 text-sm has-[:checked]:border-rose-400 has-[:checked]:bg-rose-50">
                                <input type="radio" name="decision" value="rejected" class="text-rose-600 focus:ring-rose-500" @checked(old('decision') === 'rejected')>
                                <span><span class="font-semibold">Perlu revisi</span><span class="block text-xs text-slate-500">Siswa mengirim ulang; riwayat tetap tersimpan.</span></span>
                            </label>
                        </div>
                        <x-input-error class="mt-1" :messages="$errors->get('decision')" />
                    </div>

                    <div>
                        <x-input-label for="score" :value="'Skor (0–100) · lulus ≥ '.$assignment->passing_score" />
                        <x-text-input id="score" name="score" type="number" min="0" max="100" class="mt-1 block w-32" :value="old('score')" required />
                        <x-input-error class="mt-1" :messages="$errors->get('score')" />
                    </div>

                    <div>
                        <x-input-label for="mentor_feedback" value="Masukan untuk siswa (wajib jika revisi)" />
                        <textarea id="mentor_feedback" name="mentor_feedback" rows="5" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">{{ old('mentor_feedback') }}</textarea>
                        <x-input-error class="mt-1" :messages="$errors->get('mentor_feedback')" />
                    </div>

                    <button class="rounded-lg bg-indigo-600 px-5 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Simpan Penilaian</button>
                </form>
            @else
                <section class="rounded-xl border-l-4 {{ $submission->isApproved() ? 'border-emerald-400 bg-emerald-50' : 'border-rose-400 bg-rose-50' }} p-5 text-sm">
                    <div class="font-semibold text-slate-800">Skor: {{ $submission->score }}/100
                        <span class="ms-1 text-xs font-normal text-slate-500">oleh {{ $submission->reviewer?->name ?? 'Mentor' }} · {{ $submission->reviewed_at?->translatedFormat('d M Y, H:i') }}</span>
                    </div>
                    @if ($submission->mentor_feedback)
                        <p class="mt-1 whitespace-pre-line text-slate-700">{{ $submission->mentor_feedback }}</p>
                    @endif
                </section>
            @endif

            {{-- Riwayat siswa untuk tugas ini --}}
            @if ($history->count() > 1)
                <section class="rounded-xl bg-white p-5 shadow-sm border border-slate-100">
                    <h3 class="font-semibold text-slate-800">Riwayat pengiriman siswa ini</h3>
                    <ol class="mt-3 space-y-2">
                        @foreach ($history as $item)
                            <li class="flex flex-wrap items-center justify-between gap-2 rounded-lg border border-slate-100 px-3 py-2 text-sm {{ $item->id === $submission->id ? 'bg-indigo-50' : '' }}">
                                <span>
                                    @if ($item->id === $submission->id)
                                        <span class="font-semibold">Ke-{{ $loop->iteration }} (ini)</span>
                                    @else
                                        <a href="{{ route('mentor.submissions.show', $item) }}" class="font-semibold text-indigo-600 hover:underline">Ke-{{ $loop->iteration }}</a>
                                    @endif
                                    <span class="text-xs text-slate-400">· {{ $item->created_at->translatedFormat('d M Y, H:i') }}</span>
                                    @if ($item->mentor_feedback && $item->id !== $submission->id)
                                        <span class="block text-xs text-slate-500">Masukan: {{ \Illuminate\Support\Str::limit($item->mentor_feedback, 120) }}</span>
                                    @endif
                                </span>
                                <x-submission-status :status="$item->status" />
                            </li>
                        @endforeach
                    </ol>
                </section>
            @endif
        </div>
    </div>
</x-app-layout>
