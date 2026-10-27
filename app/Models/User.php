<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'password', 'avatar_url', 'mentor_type_id'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /** Tipe/spesialisasi mentor (dinamis, dikelola Admin). Null untuk non-mentor. */
    public function mentorType(): BelongsTo
    {
        return $this->belongsTo(MentorType::class, 'mentor_type_id');
    }

    /** Kelas yang dibuat oleh mentor ini. */
    public function courses(): HasMany
    {
        return $this->hasMany(Course::class, 'mentor_id');
    }

    /** Pendaftaran kelas milik siswa ini. */
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class, 'student_id');
    }

    /** Kelas yang diikuti siswa (melalui enrollments). */
    public function enrolledCourses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'enrollments', 'student_id', 'course_id')
            ->withPivot(['payment_status', 'progress_percent', 'completed_at', 'certificate_code'])
            ->withTimestamps();
    }

    public function lessonCompletions(): HasMany
    {
        return $this->hasMany(LessonCompletion::class, 'student_id');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class, 'student_id');
    }
}
