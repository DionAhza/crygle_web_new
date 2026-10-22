<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Services\EnrollmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    public function store(Request $request, Course $course, EnrollmentService $service): RedirectResponse
    {
        abort_unless($course->is_published, 404);

        $enrollment = $service->enroll($request->user(), $course);

        if ($enrollment->isPaid()) {
            return redirect()->route('student.courses')->with('status', 'Berhasil mendaftar ke kelas "'.$course->title.'".');
        }

        return redirect()->route('student.checkout', $enrollment);
    }
}
