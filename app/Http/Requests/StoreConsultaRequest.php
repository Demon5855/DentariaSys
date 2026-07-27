<?php

namespace App\Http\Requests;

use App\Models\Antecedente;
use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Foundation\Http\FormRequest;

class StoreConsultaRequest extends FormRequest
{
    /**
     * Código del ítem "Otros" en el catálogo de antecedentes (D y E). Si se
     * marca esta casilla sin explicar nada en el texto libre, el dato no
     * sirve — por eso withValidator() lo exige.
     */
    private const CODIGO_OTROS = 10;

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

            // Casillas marcadas (X) de las secciones D y E — catálogo
            // compartido, el pivote distingue personal de familiar.
            'antecedentes_personales_marcados' => ['nullable', 'array'],
            'antecedentes_personales_marcados.*' => ['integer', 'exists:antecedentes,id'],
            'antecedentes_familiares_marcados' => ['nullable', 'array'],
            'antecedentes_familiares_marcados.*' => ['integer', 'exists:antecedentes,id'],

            // Línea de texto libre bajo las casillas ("1. ..., 6. ..."),
            // tal como está impreso en el formulario.
            'antecedentes_personales' => ['nullable', 'string'],
            'antecedentes_familiares' => ['nullable', 'string'],

            'presion_arterial' => ['nullable', 'string', 'max:20'],
            'temperatura' => ['nullable', 'numeric', 'between:30,43'],
            'pulso' => ['nullable', 'integer', 'between:20,250'],
            'frecuencia_respiratoria' => ['nullable', 'integer', 'between:5,80'],

            // Sección G: regiones marcadas + línea de texto libre.
            'regiones_afectadas' => ['nullable', 'array'],
            'regiones_afectadas.*' => ['integer', 'exists:regiones_estomatognaticas,id'],
            'examen_estomatognatico' => ['nullable', 'string'],

            // Sección M: uno o más diagnósticos, cada uno con su código
            // CIE, descripción y estado (presuntivo/definitivo). El orden
            // en que llegan en el array define 'orden' — el instructivo
            // deja ese orden al criterio del profesional.
            'diagnosticos' => ['nullable', 'array'],
            'diagnosticos.*.diagnostico_cie10_id' => ['required', 'integer', 'exists:diagnosticos_cie10,id'],
            'diagnosticos.*.descripcion' => ['required', 'string'],
            'diagnosticos.*.estado' => ['required', 'in:presuntivo,definitivo'],
        ];
    }

    /**
     * Reglas que no se expresan bien como reglas declarativas de Laravel:
     *   - Si se marca "Otros" en antecedentes, el texto libre correspondiente
     *     no puede quedar vacío (una casilla "Otros" sin explicación no es
     *     un dato clínico usable).
     *   - Si se marca alguna región del sistema estomatognático, el
     *     instructivo pide describir la patología — exigimos el texto.
     */
    public function withValidator(ValidatorContract $validator): void
    {
        $validator->after(function (ValidatorContract $validator) {
            $codigoOtros = Antecedente::where('codigo', self::CODIGO_OTROS)->value('id');

            $marcoOtrosPersonal = in_array($codigoOtros, $this->input('antecedentes_personales_marcados', []) ?? []);
            if ($marcoOtrosPersonal && ! $this->filled('antecedentes_personales')) {
                $validator->errors()->add(
                    'antecedentes_personales',
                    'Marcaste "Otros" en antecedentes personales: describe cuál en el texto.'
                );
            }

            $marcoOtrosFamiliar = in_array($codigoOtros, $this->input('antecedentes_familiares_marcados', []) ?? []);
            if ($marcoOtrosFamiliar && ! $this->filled('antecedentes_familiares')) {
                $validator->errors()->add(
                    'antecedentes_familiares',
                    'Marcaste "Otros" en antecedentes familiares: describe cuál en el texto.'
                );
            }

            $hayRegionesMarcadas = ! empty($this->input('regiones_afectadas', []));
            if ($hayRegionesMarcadas && ! $this->filled('examen_estomatognatico')) {
                $validator->errors()->add(
                    'examen_estomatognatico',
                    'Marcaste una región afectada: describe la patología encontrada.'
                );
            }
        });
    }
}
