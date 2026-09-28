<?php

namespace App\Http\Requests\Api;

use App\SemesterEnum;

class StoreAcademicYearRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'year'            => 'required|string|max:10',
            'semester'        => 'required|in:' . implode(',', array_map(fn($s) => $s->value, SemesterEnum::cases())),
            'headmaster_name' => 'nullable|string|max:255',
            'headmaster_nip'  => 'nullable|string|max:20',
            'date_start'      => 'required|date',
            'date_end'        => 'required|date|after_or_equal:date_start',
            'active'          => 'boolean',
        ];
    }

    public function attributes(): array
    {
        return [
            'year'            => 'Tahun Ajaran',
            'semester'        => 'Semester',
            'headmaster_name' => 'Nama Kepala Sekolah',
            'headmaster_nip'  => 'NIP Kepala Sekolah',
            'date_start'      => 'Tanggal Mulai',
            'date_end'        => 'Tanggal Selesai',
        ];
    }
}
