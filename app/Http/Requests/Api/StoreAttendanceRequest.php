<?php

namespace App\Http\Requests\Api;

class StoreAttendanceRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'student_id'=> 'required|exists:students,ulid',
            'date'      => 'required|date',
            'status'    => 'required|string',
        ];
    }

    public function attributes(): array
    {
        return [
            'student_id'=> 'Siswa',
            'date'      => 'Tanggal',
            'status'    => 'Status',
        ];
    }
}
