<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Producto>
 */
class ProductoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nombre' => fake()->unique()->words(3, true),
            'codigo_barras' => fake()->unique()->ean13(),
            'unidad_medida' => 'unidad',
            'categoria' => null,
            'stock_minimo' => 10,
            'activo' => true,
        ];
    }
}
