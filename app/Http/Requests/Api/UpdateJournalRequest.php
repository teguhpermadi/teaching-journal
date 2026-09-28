<?php

namespace App\Http\Requests\Api;

use App\TeachingStatusEnum;

class UpdateJournalRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'academic_year_id'=> 'nullable|exists:academic_years,ulid',
            'subject_id'      => 'nullable|exists:subjects,ulid',
            'grade_id'        => 'nullable|exists:grades,ulid',
            'user_id'         => 'nullable|exists:users,ulid',
            'date'            => 'nullable|date',
            'main_target_id'  => 'nullable|string',
            'target_id'       => 'nullable|array',
            'chapter'         => 'nullable|string',
            'activity'        => 'nullable|string',
            'notes'           => 'nullable|string',
            'status'          => 'nullable|in:' . implode(',', array_map(fn($s) => $s->value, TeachingStatusEnum::cases())),
        ];
    }
}
