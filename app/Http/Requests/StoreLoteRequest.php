<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'numero_lote' => ['nullable', 'string', 'max:100'],
            'fecha_caducidad' => ['required', 'date', 'after:today'],
            'fecha_ingreso' => ['required', 'date', 'before_or_equal:today'],
            'proveedor' => ['nullable', 'string', 'max:150'],
            'costo_unitario' => ['nullable', 'numeric', 'min:0'],
            'cantidad_inicial' => ['required', 'integer', 'min:1'],
        ];
    }
}
