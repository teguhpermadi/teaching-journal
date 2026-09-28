<?php

namespace App\Http\Requests\Api;

class UpdateMainTargetRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'academic_year_id'=> 'nullable|exists:academic_years,ulid',
            'user_id'         => 'nullable|exists:users,ulid',
            'subject_id'      => 'nullable|exists:subjects,ulid',
            'grade_id'        => 'nullable|exists:grades,ulid',
            'main_target'     => 'nullable|string',
        ];
    }
}
