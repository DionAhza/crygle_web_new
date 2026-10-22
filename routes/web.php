<?php

use App\Http\Controllers\CatalogController;
use App\Http\Controllers\MidtransNotificationController;
use App\Http\Controllers\Mentor\AssignmentController;
use App\Http\Controllers\Mentor\CourseController as MentorCourseController;
use App\Http\Controllers\Mentor\DashboardController as MentorDashboardController;
use App\Http\Controllers\Mentor\LessonController;
use App\Http\Controllers\Mentor\ModuleController;
use App\Http\Controllers\Mentor\SubmissionController as MentorSubmissionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Student\CheckoutController;
use App\Http\Controllers\Student\LearnController;
use App\Http\Controllers\Student\EnrollmentController as StudentEnrollmentController;
use App\Http\Controllers\Student\MyCoursesController;
use App\Http\Controllers\Student\SubmissionController as StudentSubmissionController;
use App\Http\Controllers\SubmissionDownloadController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// ---------------------------------------------------------------- Katalog (publik)
Route::get('/kelas', [CatalogController::class, 'index'])->name('catalog.index');
Route::get('/kelas/{course}', [CatalogController::class, 'show'])->name('catalog.show');

// Webhook Midtrans (tanpa auth, tanpa CSRF; diverifikasi lewat signature)
Route::post('/midtrans/notification', MidtransNotificationController::class)->name('midtrans.notification');

Route::get('/dashboard', function () {
    if (auth()->user()->hasRole('mentor')) {
        return redirect()->route('mentor.dashboard');
    }

    if (auth()->user()->hasRole('student')) {
        return redirect()->route('student.courses');
    }

    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// ---------------------------------------------------------------- Ruang belajar
// Akses diatur di controller: siswa terdaftar (berurutan), preview gratis, atau mentor pemilik.
Route::middleware(['auth', 'verified'])->prefix('belajar')->name('learn.')->group(function () {
    Route::get('{course}', [LearnController::class, 'index'])->name('index');
    Route::get('{course}/materi/{lesson}', [LearnController::class, 'show'])->name('show');
    Route::post('{course}/materi/{lesson}/selesai', [LearnController::class, 'complete'])->name('complete');
});

// Unduh berkas tugas: otorisasi (siswa pemilik / mentor pemilik kelas) dicek lewat SubmissionPolicy.
Route::get('/tugas/{submission}/unduh', SubmissionDownloadController::class)
    ->middleware(['auth', 'verified'])->name('submissions.download');

// ---------------------------------------------------------------- Siswa
Route::middleware(['auth', 'verified', 'role:student'])->name('student.')->group(function () {
    Route::get('/kelas-saya', MyCoursesController::class)->name('courses');
    Route::post('/kelas/{course}/daftar', [StudentEnrollmentController::class, 'store'])->name('enroll');
    Route::get('/checkout/{enrollment}', [CheckoutController::class, 'show'])->name('checkout');
    Route::post('/checkout/{enrollment}/simulate', [CheckoutController::class, 'simulate'])->name('checkout.simulate');
    Route::post('/belajar/{course}/materi/{lesson}/tugas', [StudentSubmissionController::class, 'store'])->name('submissions.store');
});

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

        Route::get('submissions', [MentorSubmissionController::class, 'index'])->name('submissions.index');
        Route::get('submissions/{submission}', [MentorSubmissionController::class, 'show'])->name('submissions.show');
        Route::patch('submissions/{submission}', [MentorSubmissionController::class, 'review'])->name('submissions.review');
    });

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
