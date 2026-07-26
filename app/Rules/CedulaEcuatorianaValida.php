<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Valida una cédula ecuatoriana de persona natural: 10 dígitos, código de
 * provincia (01-24), tercer dígito < 6, y dígito verificador por el
 * algoritmo módulo 10 usado por el Registro Civil.
 *
 * No valida cédulas de personas jurídicas (RUC de 13 dígitos) — esas no
 * aplican a un paciente.
 */
class CedulaEcuatorianaValida implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || ! preg_match('/^\d{10}$/', $value)) {
            $fail('El :attribute debe tener exactamente 10 dígitos numéricos.');

            return;
        }

        $provincia = (int) substr($value, 0, 2);

        if ($provincia < 1 || $provincia > 24) {
            $fail('El :attribute no corresponde a un código de provincia válido.');

            return;
        }

        $tercerDigito = (int) $value[2];

        if ($tercerDigito >= 6) {
            $fail('El :attribute no corresponde a una cédula de persona natural.');

            return;
        }

        // Algoritmo módulo 10: coeficientes alternados 2-1 sobre los
        // primeros 9 dígitos; cada producto de dos cifras se reduce
        // restando 9. El décimo dígito debe igualar el verificador.
        $coeficientes = [2, 1, 2, 1, 2, 1, 2, 1, 2];
        $suma = 0;

        foreach ($coeficientes as $posicion => $coeficiente) {
            $producto = ((int) $value[$posicion]) * $coeficiente;

            if ($producto >= 10) {
                $producto -= 9;
            }

            $suma += $producto;
        }

        $digitoVerificador = (10 - ($suma % 10)) % 10;

        if ($digitoVerificador !== (int) $value[9]) {
            $fail('El :attribute no es una cédula válida (dígito verificador incorrecto).');
        }
    }
}
