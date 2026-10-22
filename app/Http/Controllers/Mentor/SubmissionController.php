<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Mentor\ReviewSubmissionRequest;
use App\Models\Submission;
use App\Services\SubmissionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SubmissionController extends Controller
{
    private const STATUSES = ['pending', 'approved', 'rejected'];

    public function index(Request $request): View
    {
        $mentor = $request->user();
        $status = $request->query('status', 'pending');
        $status = in_array($status, [...self::STATUSES, 'all'], true) ? $status : 'pending';

        $counts = Submission::forMentor($mentor)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $submissions = Submission::forMentor($mentor)
            ->with(['student:id,name,email', 'assignment.lesson.module.course'])
            ->when($status !== 'all', fn ($q) => $q->where('status', $status))
            // Antrean review: yang paling lama menunggu tampil lebih dulu.
            ->when($status === 'pending', fn ($q) => $q->oldest('id'), fn ($q) => $q->latest('id'))
            ->paginate(15)
            ->withQueryString();

        return view('mentor.submissions.index', [
            'submissions' => $submissions,
            'status' => $status,
            'counts' => [
                'pending' => (int) ($counts['pending'] ?? 0),
                'approved' => (int) ($counts['approved'] ?? 0),
                'rejected' => (int) ($counts['rejected'] ?? 0),
                'all' => (int) $counts->sum(),
            ],
        ]);
    }

    public function show(Submission $submission): View
    {
        Gate::authorize('review', $submission);

        $submission->load(['student:id,name,email', 'assignment.lesson.module.course', 'reviewer:id,name']);
        $assignment = $submission->assignment;

        $history = Submission::where('assignment_id', $submission->assignment_id)
            ->where('student_id', $submission->student_id)
            ->with('reviewer:id,name')
            ->orderBy('id')
            ->get();

        return view('mentor.submissions.show', [
            'submission' => $submission,
            'assignment' => $assignment,
            'lesson' => $assignment->lesson,
            'course' => $assignment->lesson->module->course,
            'history' => $history,
            'instructionsHtml' => $assignment->instructions
                ? Str::markdown($assignment->instructions, ['html_input' => 'strip', 'allow_unsafe_links' => false])
                : null,
        ]);
    }

    public function review(ReviewSubmissionRequest $request, Submission $submission, SubmissionService $service): RedirectResponse
    {
        Gate::authorize('review', $submission);

        $reviewed = $service->review(
            $submission,
            $request->user(),
            $request->validated('decision'),
            (int) $request->validated('score'),
            $request->validated('mentor_feedback'),
        );

        return redirect()->route('mentor.submissions.index')->with('status', $reviewed->isApproved()
            ? 'Tugas disetujui. Materi berikutnya terbuka untuk siswa.'
            : 'Tugas dikembalikan untuk direvisi. Riwayat pengiriman tetap tersimpan.');
    }
}
