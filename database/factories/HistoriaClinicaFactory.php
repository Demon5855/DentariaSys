<?php

namespace Database\Factories;

use App\Models\HistoriaClinica;
use App\Models\Paciente;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\HistoriaClinica>
 */
class HistoriaClinicaFactory extends Factory
{
    public function definition(): array
    {
        $fechaApertura = fake()->dateTimeBetween('-1 year', 'now')->format('Y-m-d');

        return [
            'paciente_id' => Paciente::factory(),
            'fecha_apertura' => $fechaApertura,
            'tipo_vigencia' => 'general',
            'fecha_vencimiento' => HistoriaClinica::calcularFechaVencimiento(
                'general',
                \Carbon\Carbon::parse($fechaApertura),
                null,
                null,
            ),
        ];
    }

    public function vencida(): static
    {
        return $this->state(fn () => [
            'fecha_apertura' => now()->subDays(400)->toDateString(),
            'fecha_vencimiento' => now()->subDays(35)->toDateString(),
        ]);
    }

    public function porEmbarazo(): static
    {
        return $this->state(fn (array $attrs) => [
            'tipo_vigencia' => 'embarazo',
            'fecha_probable_parto' => now()->addMonths(6)->toDateString(),
            'fecha_vencimiento' => now()->addMonths(6)->toDateString(),
        ]);
    }
}
