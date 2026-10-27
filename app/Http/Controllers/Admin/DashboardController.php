<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\MentorType;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'stats' => [
                'mentors' => User::role('mentor')->count(),
                'students' => User::role('student')->count(),
                'courses' => Course::count(),
                'mentor_types' => MentorType::count(),
            ],
        ]);
    }
}
