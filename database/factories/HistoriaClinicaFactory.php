<?php

namespace Database\Factories;

use App\Models\Paciente;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\HistoriaClinica>
 */
class HistoriaClinicaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'paciente_id' => Paciente::factory(),
            'fecha_apertura' => fake()->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
        ];
    }
}
