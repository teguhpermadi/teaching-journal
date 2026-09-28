<?php

namespace App\Http\Requests\Api;

class UpdateSignatureRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'journal_id'       => 'nullable|exists:journals,ulid',
            'signer_id'        => 'nullable|exists:users,ulid',
            'signer_role'      => 'nullable|string|max:50',
            'signature_path'   => 'nullable|string',
            'signature_base64' => 'nullable|string',
            'signed_at'        => 'nullable|date',
        ];
    }
}
