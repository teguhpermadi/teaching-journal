<?php

namespace App\Http\Requests\Api;

use App\SemesterEnum;

class UpdateAcademicYearRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'year'            => 'nullable|string|max:10',
            'semester'        => 'nullable|in:' . implode(',', array_map(fn($s) => $s->value, SemesterEnum::cases())),
            'headmaster_name' => 'nullable|string|max:255',
            'headmaster_nip'  => 'nullable|string|max:20',
            'date_start'      => 'nullable|date',
            'date_end'        => 'nullable|date|after_or_equal:date_start',
            'active'          => 'boolean',
        ];
    }
}
