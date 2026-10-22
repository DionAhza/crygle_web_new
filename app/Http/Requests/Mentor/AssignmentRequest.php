<?php

namespace App\Http\Requests\Mentor;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AssignmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole('mentor');
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:150'],
            'instructions' => ['nullable', 'string', 'max:20000'],
            'submission_type' => ['required', Rule::in(['file', 'repo_link', 'text'])],
            'passing_score' => ['required', 'integer', 'min:0', 'max:100'],
        ];
    }
}
