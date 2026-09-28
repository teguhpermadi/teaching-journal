<?php

namespace App\Http\Requests\Api;

use Illuminate\Validation\Rule;

class UpdateUserRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'name'     => 'nullable|string|max:255',
            'email'    => 'nullable|email|' . Rule::unique('users')->ignore($this->route('id'), 'ulid'),
            'password' => 'nullable|string|min:8|confirmed',
        ];
    }
}
