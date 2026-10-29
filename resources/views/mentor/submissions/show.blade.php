<x-panel-layout :title="$assignment->title" :heading="$assignment->title" :subtitle="$course->title.' · '.$lesson->title"
                :back="['label' => 'Review Tugas', 'href' => route('mentor.submissions.index')]" width="max-w-4xl">
    <x-slot name="actions">
        <x-submission-status :status="$submission->status" class="!px-4 !py-1.5 !text-sm" />
    </x-slot>

    <x-flash />

    <div class="space-y-6">
        {{-- Siswa & instruksi --}}
        <x-card>
            <div class="flex flex-wrap items-center gap-4">
                <x-avatar :name="$submission->student->name" :src="$submission->student->avatar_url" size="h-12 w-12 text-base" />
                <div class="min-w-0 flex-1">
                    <div class="text-xs text-slate-500">Siswa</div>
                    <div class="font-bold text-slate-800">{{ $submission->student->name }} <span class="text-xs font-normal text-slate-400">· {{ $submission->student->email }}</span></div>
                </div>
                <div class="flex flex-wrap gap-2">
                    <x-badge tone="brand">Lulus ≥ {{ $assignment->passing_score }}</x-badge>
                    <x-badge tone="slate">{{ ['file' => 'Unggah file', 'repo_link' => 'Tautan repository', 'text' => 'Teks'][$assignment->submission_type] }}</x-badge>
                </div>
            </div>

            @if ($instructionsHtml)
                <details class="group mt-5 rounded-xl bg-[#f8fbff] p-4">
                    <summary class="cursor-pointer list-none text-sm font-semibold text-brand">
                        <span class="inline-flex items-center gap-2">
                            <svg class="h-4 w-4 transition group-open:rotate-90" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                            Lihat instruksi tugas
                        </span>
                    </summary>
                    <div class="prose prose-sm prose-slate mt-3 max-w-none">{!! $instructionsHtml !!}</div>
                </details>
            @endif
        </x-card>

        {{-- Pengiriman yang dinilai --}}
        <x-card>
            <div class="flex flex-wrap items-baseline justify-between gap-2">
                <h3 class="text-base font-bold text-slate-800">Pengiriman ke-{{ $history->search(fn ($h) => $h->id === $submission->id) + 1 }}</h3>
                <span class="text-xs text-slate-400">{{ $submission->created_at->translatedFormat('d M Y, H:i') }}</span>
            </div>
            <div class="mt-4">
                <x-submission-body :submission="$submission" :type="$assignment->submission_type" />
            </div>
        </x-card>

        {{-- Form penilaian / hasil --}}
        @if ($submission->isPending())
            <form method="POST" action="{{ route('mentor.submissions.review', $submission) }}">
                @csrf @method('PATCH')
                <x-card class="space-y-6 !border-brand-border">
                    <x-section-title title="Penilaian" description="Putuskan apakah tugas ini disetujui atau perlu revisi."
                                     icon="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />

                    <div>
                        <x-input-label value="Keputusan" />
                        <div class="mt-2 grid gap-3 sm:grid-cols-2">
                            <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-slate-200 p-4 text-sm transition has-[:checked]:border-emerald-400 has-[:checked]:bg-emerald-50">
                                <input type="radio" name="decision" value="approved" class="mt-0.5 text-emerald-600 focus:ring-emerald-500" @checked(old('decision') === 'approved') required>
                                <span><span class="font-semibold text-slate-800">Setujui</span><span class="mt-0.5 block text-xs text-slate-500">Materi dihitung selesai, materi berikutnya terbuka.</span></span>
                            </label>
                            <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-slate-200 p-4 text-sm transition has-[:checked]:border-rose-400 has-[:checked]:bg-rose-50">
                                <input type="radio" name="decision" value="rejected" class="mt-0.5 text-rose-600 focus:ring-rose-500" @checked(old('decision') === 'rejected')>
                                <span><span class="font-semibold text-slate-800">Perlu revisi</span><span class="mt-0.5 block text-xs text-slate-500">Siswa mengirim ulang; riwayat tetap tersimpan.</span></span>
                            </label>
                        </div>
                        <x-input-error class="mt-1.5" :messages="$errors->get('decision')" />
                    </div>

                    <x-form-field :label="'Skor (0–100) · lulus ≥ '.$assignment->passing_score" for="score">
                        <x-text-input id="score" name="score" type="number" min="0" max="100" class="!w-36" :value="old('score')" required />
                    </x-form-field>

                    <x-form-field label="Masukan untuk siswa" for="mentor_feedback" hint="Wajib diisi jika meminta revisi.">
                        <x-textarea id="mentor_feedback" name="mentor_feedback" rows="5">{{ old('mentor_feedback') }}</x-textarea>
                    </x-form-field>

                    <div><x-btn>Simpan Penilaian</x-btn></div>
                </x-card>
            </form>
        @else
            <section class="rounded-[20px] border-s-4 p-6 text-sm {{ $submission->isApproved() ? 'border-emerald-400 bg-emerald-50' : 'border-rose-400 bg-rose-50' }}">
                <div class="font-bold text-slate-800">Skor: {{ $submission->score }}/100
                    <span class="ms-1 text-xs font-normal text-slate-500">oleh {{ $submission->reviewer?->name ?? 'Mentor' }} · {{ $submission->reviewed_at?->translatedFormat('d M Y, H:i') }}</span>
                </div>
                @if ($submission->mentor_feedback)
                    <p class="mt-2 whitespace-pre-line text-slate-700">{{ $submission->mentor_feedback }}</p>
                @endif
            </section>
        @endif

        {{-- Riwayat siswa untuk tugas ini --}}
        @if ($history->count() > 1)
            <x-card>
                <h3 class="text-base font-bold text-slate-800">Riwayat pengiriman siswa ini</h3>
                <ol class="mt-4 space-y-2">
                    @foreach ($history as $item)
                        <li class="flex flex-wrap items-center justify-between gap-2 rounded-xl border px-4 py-3 text-sm {{ $item->id === $submission->id ? 'border-brand-border bg-brand-50' : 'border-slate-100' }}">
                            <span>
                                @if ($item->id === $submission->id)
                                    <span class="font-semibold text-slate-800">Ke-{{ $loop->iteration }} (ini)</span>
                                @else
                                    <a href="{{ route('mentor.submissions.show', $item) }}" class="font-semibold text-brand hover:underline">Ke-{{ $loop->iteration }}</a>
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
            </x-card>
        @endif
    </div>
</x-panel-layout>
