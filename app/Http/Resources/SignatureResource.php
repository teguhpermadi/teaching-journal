<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SignatureResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'journal_id' => $this->journal_id,
            'signer_id' => $this->signer_id,
            'signer_role' => $this->signer_role,
            'signature_url' => $this->signature_url,
            'is_signed' => $this->is_signed,
            'signed_at' => $this->signed_at?->toISOString(),
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
