<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $mentor = $request->user();

        $courses = $mentor->courses()->withCount(['modules', 'lessons', 'enrollments'])->latest()->get();

        $pendingSubmissions = Submission::where('status', 'pending')
            ->whereHas('assignment.lesson.module.course', fn ($q) => $q->where('mentor_id', $mentor->id))
            ->count();

        return view('mentor.dashboard', [
            'courses' => $courses,
            'stats' => [
                'courses' => $courses->count(),
                'published' => $courses->where('is_published', true)->count(),
                'students' => $courses->sum('enrollments_count'),
                'pending' => $pendingSubmissions,
            ],
        ]);
    }
}
