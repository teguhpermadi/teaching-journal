<?php

namespace App\Http\Requests\Api;

use App\TeachingStatusEnum;

class StoreJournalRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'academic_year_id'=> 'required|exists:academic_years,ulid',
            'subject_id'      => 'required|exists:subjects,ulid',
            'grade_id'        => 'required|exists:grades,ulid',
            'user_id'         => 'required|exists:users,ulid',
            'date'            => 'required|date',
            'main_target_id'  => 'nullable|string',
            'target_id'       => 'nullable|array',
            'chapter'         => 'nullable|string',
            'activity'        => 'nullable|string',
            'notes'           => 'nullable|string',
            'status'          => 'nullable|in:' . implode(',', array_map(fn($s) => $s->value, TeachingStatusEnum::cases())),
        ];
    }

    public function attributes(): array
    {
        return [
            'academic_year_id'=> 'Tahun Ajaran',
            'subject_id'      => 'Mapel',
            'grade_id'        => 'Kelas',
            'user_id'         => 'Guru',
            'date'            => 'Tanggal',
            'main_target_id'  => 'Target Utama',
            'chapter'         => 'Bab',
            'activity'        => 'Kegiatan',
            'notes'           => 'Catatan',
            'status'          => 'Status',
        ];
    }
}
