<?php

namespace Database\Factories;

use App\Models\Producto;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Lote>
 */
class LoteFactory extends Factory
{
    public function definition(): array
    {
        $cantidad = fake()->numberBetween(10, 100);

        return [
            'producto_id' => Producto::factory(),
            'numero_lote' => fake()->bothify('L-####'),
            'fecha_caducidad' => fake()->dateTimeBetween('+2 months', '+2 years'),
            'fecha_ingreso' => now()->toDateString(),
            'proveedor' => fake()->company(),
            'costo_unitario' => fake()->randomFloat(2, 0.5, 50),
            'cantidad_inicial' => $cantidad,
            'cantidad_actual' => $cantidad,
        ];
    }
}
