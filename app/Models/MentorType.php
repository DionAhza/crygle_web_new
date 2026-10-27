<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

#[Fillable(['name', 'slug'])]
class MentorType extends Model
{
    use HasFactory;

    /** Slug dibuat otomatis dari nama dan dijamin unik. */
    public static function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'tipe';
        $slug = $base;
        $i = 2;

        while (static::where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }

    public function mentors(): HasMany
    {
        return $this->hasMany(User::class, 'mentor_type_id');
    }
}
