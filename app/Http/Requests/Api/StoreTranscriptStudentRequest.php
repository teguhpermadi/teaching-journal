<?php

namespace App\Http\Requests\Api;

class StoreTranscriptStudentRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'transcript_id'   => 'required|exists:transcripts,ulid',
            'academic_year_id'=> 'required|exists:academic_years,ulid',
            'subject_id'      => 'required|exists:subjects,ulid',
            'grade_id'        => 'required|exists:grades,ulid',
            'student_id'      => 'required|exists:students,ulid',
            'score'           => 'nullable|numeric|min:0|max:100',
        ];
    }

    public function attributes(): array
    {
        return [
            'transcript_id'   => 'Transkrip',
            'academic_year_id'=> 'Tahun Ajaran',
            'subject_id'      => 'Mapel',
            'grade_id'        => 'Kelas',
            'student_id'      => 'Siswa',
            'score'           => 'Nilai',
        ];
    }
}
