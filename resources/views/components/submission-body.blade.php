@props(['submission', 'type'])

<div class="space-y-2">
    @if ($type === 'file' && $submission->attachment_url)
        <a href="{{ route('submissions.download', $submission) }}" class="inline-flex items-center gap-1 text-sm font-semibold text-brand hover:underline">📎 Unduh berkas</a>
    @elseif ($type === 'repo_link' && $submission->attachment_url)
        {{-- Hanya tautan http(s) yang dijadikan link, untuk berjaga-jaga terhadap data lama. --}}
        @if (preg_match('~^https?://~i', $submission->attachment_url))
            <a href="{{ $submission->attachment_url }}" target="_blank" rel="noopener noreferrer" class="break-all text-sm font-semibold text-brand hover:underline">🔗 {{ $submission->attachment_url }}</a>
        @else
            <span class="break-all text-sm text-slate-600">{{ $submission->attachment_url }}</span>
        @endif
    @endif

    @if ($submission->student_note)
        <p class="whitespace-pre-line rounded-md bg-slate-50 p-3 text-sm text-slate-700">{{ $submission->student_note }}</p>
    @endif
</div>
