<?php

namespace App\Http\Requests\Mentor;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReviewSubmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole('mentor');
    }

    public function rules(): array
    {
        return [
            'decision' => ['required', Rule::in(['approved', 'rejected'])],
            'score' => ['required', 'integer', 'min:0', 'max:100'],
            'mentor_feedback' => ['nullable', 'string', 'max:5000', 'required_if:decision,rejected'],
        ];
    }

    public function attributes(): array
    {
        return [
            'decision' => 'keputusan',
            'score' => 'skor',
            'mentor_feedback' => 'masukan untuk siswa',
        ];
    }

    public function messages(): array
    {
        return [
            'mentor_feedback.required_if' => 'Masukan wajib diisi saat menolak tugas agar siswa tahu apa yang perlu diperbaiki.',
        ];
    }

    /** Keputusan harus konsisten dengan nilai kelulusan tugas. */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($validator->errors()->has('score') || $validator->errors()->has('decision')) {
                return;
            }

            $passing = $this->route('submission')->assignment->passing_score;
            $score = (int) $this->input('score');

            if ($this->input('decision') === 'approved' && $score < $passing) {
                $validator->errors()->add('score', "Skor minimal untuk menyetujui adalah {$passing}. Naikkan skor atau pilih \"Perlu revisi\".");
            }

            if ($this->input('decision') === 'rejected' && $score >= $passing) {
                $validator->errors()->add('score', "Skor {$score} sudah memenuhi nilai kelulusan ({$passing}). Pilih \"Setujui\" atau turunkan skor.");
            }
        });
    }
}
