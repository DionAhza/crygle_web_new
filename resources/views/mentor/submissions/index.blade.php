<x-panel-layout title="Review Tugas" heading="Review Tugas" subtitle="Tinjau pengiriman tugas siswa dan beri masukan." width="max-w-5xl">
    <x-flash />

    {{-- Tab status --}}
    <div class="mb-5 flex flex-wrap gap-2">
        @foreach (['pending' => 'Menunggu', 'rejected' => 'Perlu revisi', 'approved' => 'Disetujui', 'all' => 'Semua'] as $key => $label)
            <a href="{{ route('mentor.submissions.index', ['status' => $key]) }}"
               class="inline-flex items-center gap-2 rounded-full px-5 py-2 text-sm font-semibold transition {{ $status === $key ? 'bg-brand text-white shadow-md shadow-brand/30' : 'border border-slate-200 bg-white text-slate-600 hover:bg-slate-50' }}">
                {{ $label }}
                <span class="rounded-full px-2 py-0.5 text-[11px] {{ $status === $key ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-500' }}">{{ $counts[$key] }}</span>
            </a>
        @endforeach
    </div>

    <x-card padding="p-0" class="overflow-hidden">
        @forelse ($submissions as $item)
            @php($lesson = $item->assignment->lesson)
            <a href="{{ route('mentor.submissions.show', $item) }}" class="flex flex-wrap items-center justify-between gap-4 border-b border-slate-50 px-6 py-4 transition last:border-0 hover:bg-slate-50/70">
                <div class="flex min-w-0 items-center gap-4">
                    <x-avatar :name="$item->student->name" :src="$item->student->avatar_url" />
                    <div class="min-w-0">
                        <div class="font-semibold text-slate-800">{{ $item->student->name }}
                            <span class="text-xs font-normal text-slate-400">· {{ $item->student->email }}</span>
                        </div>
                        <div class="truncate text-sm text-slate-600">{{ $item->assignment->title }}</div>
                        <div class="truncate text-xs text-slate-400">{{ $lesson->module->course->title }} · {{ $lesson->title }} · dikirim {{ $item->created_at->diffForHumans() }}</div>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    @if ($item->score !== null)
                        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600">Skor {{ $item->score }}</span>
                    @endif
                    <x-submission-status :status="$item->status" />
                </div>
            </a>
        @empty
            <x-empty-state :title="$status === 'pending' ? 'Tidak ada tugas yang menunggu review 🎉' : 'Belum ada pengiriman'"
                           :description="$status === 'pending' ? 'Semua pengiriman siswa sudah Anda tinjau.' : 'Belum ada pengiriman pada kategori ini.'"
                           icon="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        @endforelse
    </x-card>

    <div class="mt-6">{{ $submissions->links() }}</div>
</x-panel-layout>
