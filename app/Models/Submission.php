<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['assignment_id', 'student_id', 'reviewer_id', 'attachment_url', 'student_note', 'status', 'score', 'mentor_feedback', 'reviewed_at'])]
class Submission extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'score' => 'integer',
            'reviewed_at' => 'datetime',
        ];
    }

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }
}
