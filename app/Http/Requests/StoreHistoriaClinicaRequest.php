<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreHistoriaClinicaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'fecha_apertura' => ['required', 'date', 'before_or_equal:today'],
            'tipo_vigencia' => ['required', 'in:general,embarazo,escolar'],

            // Instructivo 033: "en embarazadas la duración... es igual al
            // período de gestación" y "en escolares... corresponde al año
            // lectivo". exclude_unless hace dos cosas a la vez: si el tipo
            // de vigencia no coincide, el campo ni se valida ni se guarda.
            'fecha_probable_parto' => [
                'exclude_unless:tipo_vigencia,embarazo',
                'required', 'date', 'after:fecha_apertura',
            ],
            'fecha_fin_periodo_lectivo' => [
                'exclude_unless:tipo_vigencia,escolar',
                'required', 'date', 'after:fecha_apertura',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'fecha_probable_parto.required' => 'Indica la fecha probable de parto para calcular la vigencia.',
            'fecha_fin_periodo_lectivo.required' => 'Indica la fecha de fin del período lectivo para calcular la vigencia.',
        ];
    }
}
