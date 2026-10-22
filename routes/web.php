<?php

use App\Http\Controllers\Mentor\AssignmentController;
use App\Http\Controllers\Mentor\CourseController as MentorCourseController;
use App\Http\Controllers\Mentor\DashboardController as MentorDashboardController;
use App\Http\Controllers\Mentor\LessonController;
use App\Http\Controllers\Mentor\ModuleController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    if (auth()->user()->hasRole('mentor')) {
        return redirect()->route('mentor.dashboard');
    }

    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// ---------------------------------------------------------------- Mentor
Route::middleware(['auth', 'verified', 'role:mentor'])
    ->prefix('mentor')->name('mentor.')->group(function () {
        Route::get('/', MentorDashboardController::class)->name('dashboard');

        Route::resource('courses', MentorCourseController::class);
        Route::post('courses/{course}/modules', [ModuleController::class, 'store'])->name('modules.store');
        Route::patch('modules/{module}', [ModuleController::class, 'update'])->name('modules.update');
        Route::delete('modules/{module}', [ModuleController::class, 'destroy'])->name('modules.destroy');

        Route::get('modules/{module}/lessons/create', [LessonController::class, 'create'])->name('lessons.create');
        Route::post('modules/{module}/lessons', [LessonController::class, 'store'])->name('lessons.store');
        Route::get('lessons/{lesson}/edit', [LessonController::class, 'edit'])->name('lessons.edit');
        Route::put('lessons/{lesson}', [LessonController::class, 'update'])->name('lessons.update');
        Route::delete('lessons/{lesson}', [LessonController::class, 'destroy'])->name('lessons.destroy');

        Route::get('lessons/{lesson}/assignment/create', [AssignmentController::class, 'create'])->name('assignments.create');
        Route::post('lessons/{lesson}/assignment', [AssignmentController::class, 'store'])->name('assignments.store');
        Route::get('assignments/{assignment}/edit', [AssignmentController::class, 'edit'])->name('assignments.edit');
        Route::put('assignments/{assignment}', [AssignmentController::class, 'update'])->name('assignments.update');
        Route::delete('assignments/{assignment}', [AssignmentController::class, 'destroy'])->name('assignments.destroy');
    });

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
