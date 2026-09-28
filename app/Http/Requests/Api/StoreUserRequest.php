<?php

namespace App\Http\Requests\Api;

use Illuminate\Validation\Rule;

class StoreUserRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ];
    }

    public function attributes(): array
    {
        return [
            'name'     => 'Nama',
            'email'    => 'Email',
            'password' => 'Kata Sandi',
        ];
    }
}
