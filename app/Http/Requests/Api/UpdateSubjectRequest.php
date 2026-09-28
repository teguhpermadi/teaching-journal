<?php

namespace App\Http\Requests\Api;

class UpdateSubjectRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'name'            => 'nullable|string|max:100',
            'code'            => 'nullable|string|max:20',
            'user_id'         => 'nullable|exists:users,ulid',
            'grade_id'        => 'nullable|exists:grades,ulid',
            'academic_year_id'=> 'nullable|exists:academic_years,ulid',
            'color'           => 'nullable|string|max:7',
        ];
    }
}
