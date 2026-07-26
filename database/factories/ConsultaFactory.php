<?php

namespace Database\Factories;

use App\Models\HistoriaClinica;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Consulta>
 */
class ConsultaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'historia_clinica_id' => HistoriaClinica::factory(),
            'profesional_id' => null,
            'fecha' => fake()->dateTimeBetween('-6 months', 'now')->format('Y-m-d'),
            'motivo_consulta' => fake()->sentence(),
            'enfermedad_actual' => fake()->optional()->paragraph(),
            'antecedentes_personales' => fake()->optional()->sentence(),
            'antecedentes_familiares' => fake()->optional()->sentence(),
            'presion_arterial' => '120/80',
            'temperatura' => 36.5,
            'pulso' => fake()->numberBetween(60, 100),
            'frecuencia_respiratoria' => fake()->numberBetween(12, 20),
            'examen_estomatognatico' => 'Sin patología aparente.',
        ];
    }
}
