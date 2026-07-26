<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     *
     * ⚠ Este seeder es para DESARROLLO LOCAL. Crea un usuario de prueba con
     * contraseña conocida ('password', el default de Breeze). Nunca lo
     * corras contra una base de datos de producción — para crear el primer
     * administrador real usa: php artisan admin:crear
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            AntecedenteSeeder::class,
            RegionEstomatognaticaSeeder::class,
            CondicionSeeder::class,
        ]);

        $usuarioPrueba = User::factory()->create([
            'name' => 'Usuario de Prueba',
            'email' => 'test@example.com',
        ]);
        $usuarioPrueba->assignRole('odontologo');
    }
}
