<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MentorTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole('admin');
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:80', Rule::unique('mentor_types', 'name')->ignore($this->route('mentor_type'))],
        ];
    }

    public function messages(): array
    {
        return ['name.unique' => 'Tipe mentor dengan nama tersebut sudah ada.'];
    }
}
