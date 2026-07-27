<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:150'],
            'codigo_barras' => ['nullable', 'string', 'max:64', 'unique:productos,codigo_barras'],
            'unidad_medida' => ['required', 'string', 'max:20'],
            'categoria' => ['nullable', 'string', 'max:100'],
            'stock_minimo' => ['required', 'integer', 'min:0'],
        ];
    }
}
