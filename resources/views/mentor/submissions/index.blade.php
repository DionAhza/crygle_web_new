<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">Review Tugas</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <x-flash />

            {{-- Tab status --}}
            <div class="flex flex-wrap gap-2 px-4 sm:px-0">
                @foreach (['pending' => 'Menunggu', 'rejected' => 'Perlu revisi', 'approved' => 'Disetujui', 'all' => 'Semua'] as $key => $label)
                    <a href="{{ route('mentor.submissions.index', ['status' => $key]) }}"
                       class="rounded-full px-4 py-1.5 text-sm font-semibold {{ $status === $key ? 'bg-indigo-600 text-white' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
                        {{ $label }} <span class="{{ $status === $key ? 'text-indigo-100' : 'text-slate-400' }}">({{ $counts[$key] }})</span>
                    </a>
                @endforeach
            </div>

            <div class="rounded-xl bg-white shadow-sm border border-slate-100 overflow-hidden">
                @forelse ($submissions as $item)
                    @php($lesson = $item->assignment->lesson)
                    <a href="{{ route('mentor.submissions.show', $item) }}" class="flex flex-wrap items-center justify-between gap-3 px-5 py-4 border-b border-slate-50 last:border-0 hover:bg-slate-50">
                        <div class="min-w-0">
                            <div class="font-medium text-slate-800">{{ $item->student->name }}
                                <span class="text-xs font-normal text-slate-400">· {{ $item->student->email }}</span>
                            </div>
                            <div class="text-sm text-slate-600">{{ $item->assignment->title }}</div>
                            <div class="text-xs text-slate-400">{{ $lesson->module->course->title }} · {{ $lesson->title }} · dikirim {{ $item->created_at->diffForHumans() }}</div>
                        </div>
                        <div class="flex items-center gap-3">
                            @if ($item->score !== null)
                                <span class="text-sm font-semibold text-slate-600">{{ $item->score }}</span>
                            @endif
                            <x-submission-status :status="$item->status" />
                        </div>
                    </a>
                @empty
                    <p class="p-6 text-sm text-slate-500">
                        {{ $status === 'pending' ? 'Tidak ada tugas yang menunggu review. 🎉' : 'Belum ada pengiriman pada kategori ini.' }}
                    </p>
                @endforelse
            </div>

            <div class="px-4 sm:px-0">{{ $submissions->links() }}</div>
        </div>
    </div>
</x-app-layout>
