<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JournalResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'academic_year_id' => $this->academic_year_id,
            'subject_id' => $this->subject_id,
            'grade_id' => $this->grade_id,
            'user_id' => $this->user_id,
            'date' => $this->date?->format('Y-m-d'),
            'chapter' => $this->chapter,
            'activity' => $this->activity,
            'notes' => $this->notes,
            'status' => $this->status?->value,
            'main_target_id' => $this->main_target_id,
            'target_id' => $this->target_id,
            'signatures' => SignatureResource::collection($this->whenLoaded('signatures')),
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
