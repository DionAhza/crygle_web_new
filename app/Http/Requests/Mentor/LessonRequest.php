<?php

namespace App\Http\Requests\Mentor;

use Illuminate\Foundation\Http\FormRequest;

class LessonRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole('mentor');
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:150'],
            'video_url' => ['nullable', 'url', 'max:500'],
            'duration_seconds' => ['nullable', 'integer', 'min:0', 'max:86400'],
            'content_text' => ['nullable', 'string', 'max:100000'],
            'sort_order' => ['nullable', 'integer', 'min:1'],
            'is_free_preview' => ['sometimes', 'boolean'],
        ];
    }
}
