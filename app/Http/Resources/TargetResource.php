<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TargetResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'subject_id' => $this->subject_id,
            'grade_id' => $this->grade_id,
            'academic_year_id' => $this->academic_year_id,
            'main_target_id' => $this->main_target_id,
            'target' => $this->target,
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
