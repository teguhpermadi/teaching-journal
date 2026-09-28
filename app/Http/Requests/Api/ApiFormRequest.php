<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

abstract class ApiFormRequest extends FormRequest
{
    /** Called automatically via Laravel validation pipeline */
    public function authorize(): bool
    {
        return true;
    }

    /** Override in child */
    abstract public function rules(): array;

    /** Custom error messages (optional) */
    public function messages(): array
    {
        return [];
    }

    /** Custom attribute names for validation errors */
    public function attributes(): array
    {
        return [];
    }
}
