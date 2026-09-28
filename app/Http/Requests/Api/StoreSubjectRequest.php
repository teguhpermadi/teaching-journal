<?php

namespace App\Http\Requests\Api;

class StoreSubjectRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'name'            => 'required|string|max:100',
            'code'            => 'nullable|string|max:20',
            'user_id'         => 'nullable|exists:users,ulid',
            'grade_id'        => 'required|exists:grades,ulid',
            'academic_year_id'=> 'required|exists:academic_years,ulid',
            'color'           => 'nullable|string|max:7',
        ];
    }

    public function attributes(): array
    {
        return [
            'name'            => 'Nama Mapel',
            'code'            => 'Kode Mapel',
            'grade_id'        => 'Kelas',
            'academic_year_id'=> 'Tahun Ajaran',
            'color'           => 'Warna',
        ];
    }
}
