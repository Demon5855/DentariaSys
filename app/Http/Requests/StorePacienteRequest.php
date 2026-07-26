<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePacienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Roles y policies llegan en la fase 2. Por ahora, cualquier
        // usuario autenticado (ya filtrado por el middleware 'auth').
        return true;
    }

    public function rules(): array
    {
        return [
            'primer_nombre' => ['required', 'string', 'max:50', 'regex:/^[a-zA-ZáéíóúñÑ\s]+$/u'],
            'segundo_nombre' => ['nullable', 'string', 'max:50', 'regex:/^[a-zA-ZáéíóúñÑ\s]+$/u'],
            'primer_apellido' => ['required', 'string', 'max:50', 'regex:/^[a-zA-ZáéíóúñÑ\s]+$/u'],
            'segundo_apellido' => ['nullable', 'string', 'max:50', 'regex:/^[a-zA-ZáéíóúñÑ\s]+$/u'],
            'fecha_nacimiento' => ['required', 'date', 'before_or_equal:today'],
            'telefono' => ['nullable', 'string', 'max:15'],
            'direccion' => ['nullable', 'string', 'max:150'],
            'email' => ['nullable', 'email', 'max:100', 'unique:pacientes,email'],
        ];
    }
}
