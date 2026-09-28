<?php

namespace App\Http\Requests\Api;

class UpdateGradeRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'name'            => 'nullable|string|max:50',
            'level'           => 'nullable|integer|min:1|max:6',
            'academic_year_id'=> 'nullable|exists:academic_years,ulid',
        ];
    }
}
