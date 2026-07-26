<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreConsultaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'fecha' => ['required', 'date', 'before_or_equal:today'],
            'motivo_consulta' => ['required', 'string'],
            'enfermedad_actual' => ['nullable', 'string'],
            'antecedentes_personales' => ['nullable', 'string'],
            'antecedentes_familiares' => ['nullable', 'string'],
            'presion_arterial' => ['nullable', 'string', 'max:20'],
            'temperatura' => ['nullable', 'numeric', 'between:30,43'],
            'pulso' => ['nullable', 'integer', 'between:20,250'],
            'frecuencia_respiratoria' => ['nullable', 'integer', 'between:5,80'],
            'examen_estomatognatico' => ['nullable', 'string'],
        ];
    }
}
