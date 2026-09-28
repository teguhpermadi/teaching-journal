<?php

namespace App\Http\Requests\Api;

class StoreTranscriptRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'grade_id'        => 'required|exists:grades,ulid',
            'subject_id'      => 'required|exists:subjects,ulid',
            'journal_id'      => 'nullable|exists:journals,ulid',
            'academic_year_id'=> 'required|exists:academic_years,ulid',
            'user_id'         => 'required|exists:users,ulid',
            'title'           => 'required|string|max:255',
            'description'     => 'nullable|string',
        ];
    }

    public function attributes(): array
    {
        return [
            'grade_id'        => 'Kelas',
            'subject_id'      => 'Mapel',
            'journal_id'      => 'Journal',
            'academic_year_id'=> 'Tahun Ajaran',
            'user_id'         => 'Guru',
            'title'           => 'Judul',
            'description'     => 'Deskripsi',
        ];
    }
}
