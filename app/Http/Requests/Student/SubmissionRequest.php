<?php

namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;

class SubmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole('student');
    }

    /** Aturan validasi mengikuti jenis pengumpulan pada tugas (file / repo_link / text). */
    public function rules(): array
    {
        $type = $this->route('lesson')?->assignment?->submission_type;
        $note = ['nullable', 'string', 'max:5000'];

        return match ($type) {
            'file' => [
                'attachment' => ['required', 'file', 'mimes:pdf,zip,doc,docx,ppt,pptx,png,jpg,jpeg,txt', 'max:'.config('crygle.submission_max_kb')],
                'student_note' => $note,
            ],
            'repo_link' => [
                'attachment_url' => ['required', 'url:http,https', 'max:255'],
                'student_note' => $note,
            ],
            'text' => [
                'student_note' => ['required', 'string', 'min:10', 'max:10000'],
            ],
            default => [],
        };
    }

    public function attributes(): array
    {
        return [
            'attachment' => 'berkas tugas',
            'attachment_url' => 'tautan repository',
            'student_note' => 'jawaban/catatan',
        ];
    }

    public function messages(): array
    {
        return [
            'attachment.mimes' => 'Berkas harus berformat PDF, ZIP, DOC/DOCX, PPT/PPTX, PNG, JPG, atau TXT.',
            'attachment.max' => 'Ukuran berkas maksimal '.round(config('crygle.submission_max_kb') / 1024, 1).' MB.',
            'attachment_url.url' => 'Tautan harus diawali http:// atau https://.',
        ];
    }
}
