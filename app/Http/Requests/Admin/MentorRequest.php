<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class MentorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole('admin');
    }

    protected function prepareForValidation(): void
    {
        if (is_string($this->email)) {
            $this->merge(['email' => mb_strtolower(trim($this->email))]);
        }
    }

    public function rules(): array
    {
        $mentor = $this->route('mentor');
        $creating = $mentor === null;

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users', 'email')->ignore($mentor)],
            'mentor_type_id' => ['required', 'integer', Rule::exists('mentor_types', 'id')],
            'password' => [$creating ? 'required' : 'nullable', 'confirmed', Password::defaults()],
        ];
    }

    public function attributes(): array
    {
        return ['mentor_type_id' => 'tipe mentor'];
    }
}
