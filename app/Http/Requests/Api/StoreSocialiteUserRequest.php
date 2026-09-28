<?php

namespace App\Http\Requests\Api;

class StoreSocialiteUserRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'user_id'     => 'required|exists:users,ulid',
            'provider'    => 'required|string|max:50',
            'provider_id' => 'required|string',
        ];
    }

    public function attributes(): array
    {
        return [
            'user_id'     => 'User',
            'provider'    => 'Provider',
            'provider_id' => 'ID Provider',
        ];
    }
}
