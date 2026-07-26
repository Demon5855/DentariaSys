<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Paciente>
 */
class PacienteFactory extends Factory
{
    /**
     * Genera cédulas ecuatorianas VÁLIDAS (con dígito verificador correcto),
     * no solo strings de 10 dígitos al azar — para que los tests ejerciten
     * el mismo camino que un dato real pasaría en producción.
     */
    private function cedulaValida(): string
    {
        $provincia = str_pad((string) fake()->numberBetween(1, 24), 2, '0', STR_PAD_LEFT);
        $tercerDigito = (string) fake()->numberBetween(0, 5);
        $base = $provincia . $tercerDigito . fake()->numerify('######');

        $coeficientes = [2, 1, 2, 1, 2, 1, 2, 1, 2];
        $suma = 0;

        foreach ($coeficientes as $i => $coef) {
            $producto = ((int) $base[$i]) * $coef;
            if ($producto >= 10) {
                $producto -= 9;
            }
            $suma += $producto;
        }

        $verificador = (10 - ($suma % 10)) % 10;

        return $base . $verificador;
    }

    public function definition(): array
    {
        $fechaNacimiento = fake()->dateTimeBetween('-80 years', '-19 years')->format('Y-m-d');

        return [
            'tipo_documento' => 'cedula',
            'numero_documento' => $this->cedulaValida(),
            'primer_nombre' => fake()->firstName(),
            'segundo_nombre' => fake()->optional()->firstName(),
            'primer_apellido' => fake()->lastName(),
            'segundo_apellido' => fake()->optional()->lastName(),
            'sexo' => fake()->randomElement(['H', 'M']),
            'fecha_nacimiento' => $fechaNacimiento,
            'telefono' => fake()->optional()->numerify('09########'),
            'direccion' => fake()->optional()->address(),
            'email' => fake()->unique()->optional()->safeEmail(),
            'activo' => true,
        ];
    }

    public function inactivo(): static
    {
        return $this->state(fn () => ['activo' => false]);
    }

    /**
     * Paciente menor de edad, con representante legal completo (requerido
     * por StorePacienteRequest cuando la fecha de nacimiento así lo indica).
     */
    public function menorDeEdad(): static
    {
        return $this->state(fn () => [
            'fecha_nacimiento' => fake()->dateTimeBetween('-17 years', '-1 years')->format('Y-m-d'),
            'representante_nombre' => fake()->name(),
            'representante_documento' => $this->cedulaValida(),
            'representante_parentesco' => fake()->randomElement(['Madre', 'Padre', 'Tutor legal']),
            'representante_telefono' => fake()->numerify('09########'),
        ]);
    }
}
