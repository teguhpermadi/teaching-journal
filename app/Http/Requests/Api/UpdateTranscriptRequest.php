<?php

namespace App\Http\Requests\Api;

class UpdateTranscriptRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'grade_id'        => 'nullable|exists:grades,ulid',
            'subject_id'      => 'nullable|exists:subjects,ulid',
            'journal_id'      => 'nullable|exists:journals,ulid',
            'academic_year_id'=> 'nullable|exists:academic_years,ulid',
            'user_id'         => 'nullable|exists:users,ulid',
            'title'           => 'nullable|string|max:255',
            'description'     => 'nullable|string',
        ];
    }
}
