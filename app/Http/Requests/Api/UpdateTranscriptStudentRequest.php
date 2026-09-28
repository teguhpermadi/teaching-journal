<?php

namespace App\Http\Requests\Api;

class UpdateTranscriptStudentRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'transcript_id'   => 'nullable|exists:transcripts,ulid',
            'academic_year_id'=> 'nullable|exists:academic_years,ulid',
            'subject_id'      => 'nullable|exists:subjects,ulid',
            'grade_id'        => 'nullable|exists:grades,ulid',
            'student_id'      => 'nullable|exists:students,ulid',
            'score'           => 'nullable|numeric|min:0|max:100',
        ];
    }
}
