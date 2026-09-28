<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TranscriptStudentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'transcript_id' => $this->transcript_id,
            'academic_year_id' => $this->academic_year_id,
            'subject_id' => $this->subject_id,
            'grade_id' => $this->grade_id,
            'student_id' => $this->student_id,
            'score' => $this->score,
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
