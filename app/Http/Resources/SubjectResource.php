<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubjectResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'user_id' => $this->user_id,
            'grade_id' => $this->grade_id,
            'academic_year_id' => $this->academic_year_id,
            'color' => $this->color,
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
