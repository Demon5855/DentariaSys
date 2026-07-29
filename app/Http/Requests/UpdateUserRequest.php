<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('usuarios.gestionar') ?? false;
    }

    public function rules(): array
    {
        $usuario = $this->route('user');

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users', 'email')->ignore($usuario?->id)],
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'role' => ['required', 'string', 'exists:roles,name'],
        ];
    }
}
