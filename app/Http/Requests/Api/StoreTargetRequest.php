<?php

namespace App\Http\Requests\Api;

class StoreTargetRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'user_id'         => 'required|exists:users,ulid',
            'subject_id'      => 'required|exists:subjects,ulid',
            'grade_id'        => 'required|exists:grades,ulid',
            'academic_year_id'=> 'required|exists:academic_years,ulid',
            'main_target_id'  => 'nullable|exists:main_targets,ulid',
            'target'          => 'required|string',
        ];
    }

    public function attributes(): array
    {
        return [
            'user_id'         => 'Guru',
            'subject_id'      => 'Mapel',
            'grade_id'        => 'Kelas',
            'academic_year_id'=> 'Tahun Ajaran',
            'main_target_id'  => 'Target Utama',
            'target'          => 'Target Detail',
        ];
    }
}
