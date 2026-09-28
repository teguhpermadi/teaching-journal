<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttendanceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'student_id' => $this->student_id,
            'date' => $this->date?->format('Y-m-d'),
            'status' => $this->status?->value,
            'student' => $this->whenLoaded('student')?->only('id', 'name', 'nisn'),
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
