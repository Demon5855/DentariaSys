<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePacienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $paciente = $this->route('paciente');

        return [
            'primer_nombre' => ['required', 'string', 'max:50', 'regex:/^[a-zA-ZáéíóúñÑ\s]+$/u'],
            'segundo_nombre' => ['nullable', 'string', 'max:50', 'regex:/^[a-zA-ZáéíóúñÑ\s]+$/u'],
            'primer_apellido' => ['required', 'string', 'max:50', 'regex:/^[a-zA-ZáéíóúñÑ\s]+$/u'],
            'segundo_apellido' => ['nullable', 'string', 'max:50', 'regex:/^[a-zA-ZáéíóúñÑ\s]+$/u'],
            'fecha_nacimiento' => ['required', 'date', 'before_or_equal:today'],
            'telefono' => ['nullable', 'string', 'max:15'],
            'direccion' => ['nullable', 'string', 'max:150'],
            'email' => [
                'nullable', 'email', 'max:100',
                Rule::unique('pacientes', 'email')->ignore($paciente?->id),
            ],
        ];
    }
}
