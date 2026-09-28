<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'nick_name' => $this->nick_name,
            'city_born' => $this->city_born,
            'birthday' => $this->birthday?->format('Y-m-d'),
            'gender' => $this->gender?->value,
            'nisn' => $this->nisn,
            'nis' => $this->nis,
            'photo' => $this->photo,
            'active' => $this->active,
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
