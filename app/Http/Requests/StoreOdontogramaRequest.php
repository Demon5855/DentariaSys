<?php

namespace App\Http\Requests;

use App\Models\Condicion;
use App\Models\HistoriaClinica;
use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Foundation\Http\FormRequest;

class StoreOdontogramaRequest extends FormRequest
{
    /** Notación FDI válida: 11-18, 21-28, 31-38, 41-48 (permanentes), 51-55, 61-65, 71-75, 81-85 (temporales). */
    public static function piezasFdiValidas(): array
    {
        $piezas = [];
        foreach ([1, 2, 3, 4] as $cuadrante) {
            foreach (range(1, 8) as $posicion) {
                $piezas[] = $cuadrante * 10 + $posicion;
            }
        }
        foreach ([5, 6, 7, 8] as $cuadrante) {
            foreach (range(1, 5) as $posicion) {
                $piezas[] = $cuadrante * 10 + $posicion;
            }
        }

        return $piezas;
    }

    /** Solo dientes definitivos (11-48) — movilidad y recesión no aplican a temporales. */
    public static function piezasPermanentesValidas(): array
    {
        return array_filter(self::piezasFdiValidas(), fn ($p) => $p >= 11 && $p <= 48);
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $piezasValidas = implode(',', self::piezasFdiValidas());
        $piezasPermanentes = implode(',', self::piezasPermanentesValidas());

        return [
            'tipo' => ['required', 'in:inicial,evolutivo'],
            'denticion' => ['required', 'in:permanente,temporal,mixta'],
            'fecha' => ['required', 'date', 'before_or_equal:today'],

            'hallazgos' => ['nullable', 'array'],
            'hallazgos.*.pieza' => ['required', 'integer', "in:{$piezasValidas}"],
            'hallazgos.*.condicion_id' => ['required', 'integer', 'exists:condiciones,id'],
            'hallazgos.*.superficie' => [
                'nullable', 'string',
                'in:vestibular,palatina,lingual,mesial,distal,oclusal,incisal',
            ],

            // Instructivo: "Registrar el índice de movilidad y recesión
            // solo en dientes definitivos".
            'periodontal' => ['nullable', 'array'],
            'periodontal.*.pieza' => ['required', 'integer', "in:{$piezasPermanentes}"],
            'periodontal.*.movilidad' => ['nullable', 'integer', 'between:0,3'],
            'periodontal.*.recesion' => ['nullable', 'integer', 'between:0,4'],
        ];
    }

    /**
     * Dos reglas que no se expresan bien como reglas declarativas:
     *   - Consistencia condición/superficie: una condición de nivel
     *     'superficie' (caries, obturado) necesita cara; una de nivel
     *     'pieza' (corona, endodoncia...) NO debe traer cara.
     *   - Solo puede existir un odontograma 'inicial' por historia clínica;
     *     los siguientes deben registrarse como 'evolutivo'.
     */
    public function withValidator(ValidatorContract $validator): void
    {
        $validator->after(function (ValidatorContract $validator) {
            $condicionesPorId = Condicion::whereIn(
                'id',
                collect($this->input('hallazgos', []))->pluck('condicion_id')->filter()->unique()
            )->get()->keyBy('id');

            foreach ($this->input('hallazgos', []) as $indice => $hallazgo) {
                $condicion = $condicionesPorId->get($hallazgo['condicion_id'] ?? null);
                if (! $condicion) {
                    continue; // ya lo marcó 'exists' arriba
                }

                if ($condicion->nivel === 'superficie' && empty($hallazgo['superficie'])) {
                    $validator->errors()->add(
                        "hallazgos.{$indice}.superficie",
                        "La condición \"{$condicion->nombre}\" requiere indicar la cara afectada."
                    );
                }

                if ($condicion->nivel === 'pieza' && ! empty($hallazgo['superficie'])) {
                    $validator->errors()->add(
                        "hallazgos.{$indice}.superficie",
                        "La condición \"{$condicion->nombre}\" no se marca por cara, es de pieza completa."
                    );
                }
            }

            $historiaClinica = $this->route('historiaClinica');
            if ($historiaClinica instanceof HistoriaClinica
                && $this->input('tipo') === 'inicial'
                && $historiaClinica->odontogramas()->where('tipo', 'inicial')->exists()
            ) {
                $validator->errors()->add(
                    'tipo',
                    'Esta historia ya tiene un odontograma inicial. Registra este como evolutivo.'
                );
            }
        });
    }
}
