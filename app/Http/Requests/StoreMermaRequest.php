<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Foundation\Http\FormRequest;

class StoreMermaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'lote_id' => ['required', 'integer', 'exists:lotes,id'],
            'tipo' => ['required', 'in:merma,ajuste'],
            'cantidad' => ['required', 'integer', 'min:1'],
            'motivo' => ['required', 'string', 'max:255'],
        ];
    }

    public function withValidator(ValidatorContract $validator): void
    {
        $validator->after(function (ValidatorContract $validator) {
            $lote = $this->route('lote');

            if ($lote && $this->filled('cantidad') && $this->input('cantidad') > $lote->cantidad_actual) {
                $validator->errors()->add(
                    'cantidad',
                    "Este lote solo tiene {$lote->cantidad_actual} unidades disponibles."
                );
            }
        });
    }
}
