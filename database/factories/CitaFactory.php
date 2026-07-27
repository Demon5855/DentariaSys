<?php

namespace Database\Factories;

use App\Models\Paciente;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Cita>
 */
class CitaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'paciente_id' => Paciente::factory(),
            'profesional_id' => null,
            'fecha_hora' => fake()->dateTimeBetween('now', '+1 month'),
            'duracion_minutos' => 30,
            'estado' => 'pendiente',
            'motivo' => fake()->optional()->sentence(4),
        ];
    }
}
