<?php

namespace Database\Factories;

use App\Models\HistoriaClinica;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Odontograma>
 */
class OdontogramaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'historia_clinica_id' => HistoriaClinica::factory(),
            'odontologo_id' => User::factory(),
            'tipo' => 'inicial',
            'denticion' => 'permanente',
            'fecha' => now()->toDateString(),
            'firmado_at' => now(),
            'cpod_c' => 0, 'cpod_p' => 0, 'cpod_o' => 0,
            'ceod_c' => 0, 'ceod_e' => 0, 'ceod_o' => 0,
        ];
    }
}
