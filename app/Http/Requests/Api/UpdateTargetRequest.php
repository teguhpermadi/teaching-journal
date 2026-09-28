<?php

namespace App\Http\Requests\Api;

class UpdateTargetRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'user_id'         => 'nullable|exists:users,ulid',
            'subject_id'      => 'nullable|exists:subjects,ulid',
            'grade_id'        => 'nullable|exists:grades,ulid',
            'academic_year_id'=> 'nullable|exists:academic_years,ulid',
            'main_target_id'  => 'nullable|exists:main_targets,ulid',
            'target'          => 'nullable|string',
        ];
    }
}
