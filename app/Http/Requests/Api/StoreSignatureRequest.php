<?php

namespace App\Http\Requests\Api;

class StoreSignatureRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'journal_id'       => 'required|exists:journals,ulid',
            'signer_id'        => 'required|exists:users,ulid',
            'signer_role'      => 'required|string|max:50',
            'signature_path'   => 'nullable|string',
            'signature_base64' => 'nullable|string',
            'signed_at'        => 'nullable|date',
        ];
    }

    public function attributes(): array
    {
        return [
            'journal_id'       => 'Journal',
            'signer_id'        => 'Penandatangan',
            'signer_role'      => 'Peran',
            'signature_path'   => 'Path Tanda Tangan',
            'signed_at'        => 'Tanggal Tanda Tangan',
        ];
    }
}
