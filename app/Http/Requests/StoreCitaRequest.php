<?php

namespace App\Http\Requests;

use App\Models\Cita;
use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Foundation\Http\FormRequest;

class StoreCitaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'paciente_id' => ['required', 'integer', 'exists:pacientes,id'],
            'profesional_id' => ['nullable', 'integer', 'exists:users,id'],
            'fecha_hora' => ['required', 'date'],
            'duracion_minutos' => ['required', 'integer', 'between:5,480'],
            'motivo' => ['nullable', 'string', 'max:255'],
            'notas' => ['nullable', 'string'],
        ];
    }

    /**
     * El solapamiento de horarios no se expresa bien como regla
     * declarativa: hay que traer las citas activas del mismo profesional
     * ese día y comparar rangos en PHP (ver Cita::seSolapaCon).
     */
    public function withValidator(ValidatorContract $validator): void
    {
        $validator->after(function (ValidatorContract $validator) {
            if (! $this->filled('profesional_id') || ! $this->filled('fecha_hora') || ! $this->filled('duracion_minutos')) {
                return;
            }

            $nueva = new Cita([
                'fecha_hora' => $this->input('fecha_hora'),
                'duracion_minutos' => $this->input('duracion_minutos'),
            ]);

            $candidatas = Cita::delMismoDiaYProfesional(
                (int) $this->input('profesional_id'),
                $nueva->fecha_hora,
            )->when($this->route('cita'), fn ($query, $citaActual) => $query->whereKeyNot($citaActual->id))
                ->get();

            if ($candidatas->contains(fn (Cita $candidata) => $nueva->seSolapaCon($candidata))) {
                $validator->errors()->add(
                    'fecha_hora',
                    'Este profesional ya tiene una cita activa que se solapa con este horario.'
                );
            }
        });
    }
}
