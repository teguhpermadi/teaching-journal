<?php

namespace App\Http\Requests\Api;

class UpdateSocialiteUserRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'user_id'     => 'nullable|exists:users,ulid',
            'provider'    => 'nullable|string|max:50',
            'provider_id' => 'nullable|string',
        ];
    }
}
