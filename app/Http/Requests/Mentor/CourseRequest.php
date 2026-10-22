<?php

namespace App\Http\Requests\Mentor;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole('mentor');
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:5000'],
            'price' => ['required', 'numeric', 'min:0', 'max:99999999'],
            'level' => ['required', Rule::in(['basic', 'intermediate', 'advanced'])],
            'thumbnail' => ['nullable', 'image', 'max:2048'],
            'is_published' => ['sometimes', 'boolean'],
        ];
    }
}
