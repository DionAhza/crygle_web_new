<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use App\Services\SubmissionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SubmissionDownloadController extends Controller
{
    /** Unduh berkas tugas: hanya siswa pemilik atau mentor pemilik kelas. */
    public function __invoke(Submission $submission, SubmissionService $service): StreamedResponse|RedirectResponse
    {
        Gate::authorize('view', $submission);

        $submission->loadMissing('assignment');

        abort_unless($submission->assignment->submission_type === 'file' && $submission->attachment_url, 404);

        $disk = $service->disk();
        $path = $submission->attachment_url;
        abort_unless(Storage::disk($disk)->exists($path), 404, 'Berkas tidak ditemukan.');

        if (config("filesystems.disks.{$disk}.driver") === 's3') {
            return redirect()->away(Storage::disk($disk)->temporaryUrl($path, now()->addMinutes(5)));
        }

        $name = Str::slug($submission->assignment->title).'-'.$submission->student_id.'-'.$submission->id
            .'.'.pathinfo($path, PATHINFO_EXTENSION);

        return Storage::disk($disk)->download($path, $name);
    }
}
