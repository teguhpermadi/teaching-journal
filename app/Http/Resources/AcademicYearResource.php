<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AcademicYearResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'year' => $this->year,
            'semester' => $this->semester->value,
            'headmaster_name' => $this->headmaster_name,
            'headmaster_nip' => $this->headmaster_nip,
            'date_start' => $this->date_start?->format('Y-m-d'),
            'date_end' => $this->date_end?->format('Y-m-d'),
            'active' => $this->active,
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
