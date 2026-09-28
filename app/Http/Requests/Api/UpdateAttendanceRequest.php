<?php

namespace App\Http\Requests\Api;

class UpdateAttendanceRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'student_id'=> 'nullable|exists:students,ulid',
            'date'      => 'nullable|date',
            'status'    => 'nullable|string',
        ];
    }
}
