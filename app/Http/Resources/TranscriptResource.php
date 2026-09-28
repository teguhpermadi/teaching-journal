<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TranscriptResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'grade_id' => $this->grade_id,
            'subject_id' => $this->subject_id,
            'journal_id' => $this->journal_id,
            'academic_year_id' => $this->academic_year_id,
            'user_id' => $this->user_id,
            'title' => $this->title,
            'description' => $this->description,
            'transcript_students' => TranscriptStudentResource::collection($this->whenLoaded('transcriptStudents')),
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
