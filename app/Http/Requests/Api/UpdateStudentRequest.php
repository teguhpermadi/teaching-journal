<?php

namespace App\Http\Requests\Api;

class UpdateStudentRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'name'       => 'nullable|string|max:255',
            'nick_name'  => 'nullable|string|max:50',
            'city_born'  => 'nullable|string|max:100',
            'birthday'   => 'nullable|date',
            'gender'     => 'nullable|in:male,female',
            'nisn'       => 'nullable|string|max:20',
            'nis'        => 'nullable|string|max:20',
            'photo'      => 'nullable|string',
            'active'     => 'boolean',
        ];
    }
}
