<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules;

class UserUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        $model = $this->route('user');

        return (bool) ($this->user()?->can('update', $model));
    }

    public function rules(): array
    {
        $user = $this->route('user');

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'roles' => ['sometimes', 'array'],
            'roles.*' => ['exists:roles,id'],
        ];
    }
}
