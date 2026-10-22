<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MyCoursesController extends Controller
{
    public function __invoke(Request $request): View
    {
        $enrollments = $request->user()->enrollments()
            ->with('course.mentor:id,name')
            ->latest()
            ->get();

        return view('student.courses', [
            'active' => $enrollments->where('payment_status', 'paid'),
            'unpaid' => $enrollments->where('payment_status', '!=', 'paid'),
        ]);
    }
}
