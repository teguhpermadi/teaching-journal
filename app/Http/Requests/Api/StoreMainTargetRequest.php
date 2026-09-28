<?php

namespace App\Http\Requests\Api;

class StoreMainTargetRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'academic_year_id'=> 'required|exists:academic_years,ulid',
            'user_id'         => 'required|exists:users,ulid',
            'subject_id'      => 'required|exists:subjects,ulid',
            'grade_id'        => 'required|exists:grades,ulid',
            'main_target'     => 'required|string',
        ];
    }

    public function attributes(): array
    {
        return [
            'academic_year_id'=> 'Tahun Ajaran',
            'user_id'         => 'Guru',
            'subject_id'      => 'Mapel',
            'grade_id'        => 'Kelas',
            'main_target'     => 'Target Utama',
        ];
    }
}
