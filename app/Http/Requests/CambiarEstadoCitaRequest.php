<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Foundation\Http\FormRequest;

class CambiarEstadoCitaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'estado' => ['required', 'in:pendiente,confirmada,atendida,cancelada,no_asistio'],
            'notas' => ['nullable', 'string'],
        ];
    }

    public function withValidator(ValidatorContract $validator): void
    {
        $validator->after(function (ValidatorContract $validator) {
            $cita = $this->route('cita');

            if ($cita && ! $cita->puedeTransicionarA($this->input('estado'))) {
                $validator->errors()->add(
                    'estado',
                    "No se puede pasar de \"{$cita->estado}\" a \"{$this->input('estado')}\"."
                );
            }
        });
    }
}
