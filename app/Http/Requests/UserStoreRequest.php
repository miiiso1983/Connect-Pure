<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules;

class UserStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) ($this->user()?->can('create', \App\Models\User::class));
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'roles' => ['sometimes', 'array'],
            'roles.*' => ['exists:roles,id'],
        ];
    }
}
