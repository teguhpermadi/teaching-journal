<?php

namespace App\Http\Requests\Api;

class StoreGradeRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'name'            => 'required|string|max:50',
            'level'           => 'required|integer|min:1|max:6',
            'academic_year_id'=> 'required|exists:academic_years,ulid',
        ];
    }

    public function attributes(): array
    {
        return [
            'name'            => 'Nama Kelas',
            'level'           => 'Tingkat',
            'academic_year_id'=> 'Tahun Ajaran',
        ];
    }
}
