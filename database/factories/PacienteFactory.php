<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Paciente>
 */
class PacienteFactory extends Factory
{
    public function definition(): array
    {
        return [
            'primer_nombre' => fake()->firstName(),
            'segundo_nombre' => fake()->optional()->firstName(),
            'primer_apellido' => fake()->lastName(),
            'segundo_apellido' => fake()->optional()->lastName(),
            'fecha_nacimiento' => fake()->dateTimeBetween('-80 years', '-2 years')->format('Y-m-d'),
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
}
