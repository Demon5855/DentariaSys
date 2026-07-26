<?php

namespace Tests\Feature;

use App\Models\Paciente;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);

        // owen-it/laravel-auditing desactiva la auditoría en contexto de
        // consola por defecto (para no registrar cada `tinker` o seeder).
        // `php artisan test` corre como CLI, así que sin esto no se generaría
        // ningún audit aquí — aunque en producción (servidor web real) el
        // comportamiento por defecto sí audita todo con normalidad.
        config(['audit.console' => true]);
    }

    public function test_actualizar_un_paciente_deja_registro_de_auditoria(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $this->actingAs($admin);

        $paciente = Paciente::factory()->create(['telefono' => '0999999999']);

        $this->put(route('pacientes.update', $paciente), [
            'primer_nombre' => $paciente->primer_nombre,
            'primer_apellido' => $paciente->primer_apellido,
            'fecha_nacimiento' => $paciente->fecha_nacimiento->format('Y-m-d'),
            'telefono' => '0888888888',
        ]);

        $paciente->refresh();

        // La creación (vía factory) ya generó su propio audit de tipo 'created'.
        // Por eso filtramos por evento en vez de contar $paciente->audits a secas.
        $this->assertTrue($paciente->audits()->where('event', 'created')->exists());

        $auditoriasDeActualizacion = $paciente->audits()->where('event', 'updated')->get();
        $this->assertCount(1, $auditoriasDeActualizacion);

        $auditoria = $auditoriasDeActualizacion->first();
        $this->assertSame($admin->id, $auditoria->user_id);
        $this->assertSame('0999999999', $auditoria->old_values['telefono']);
        $this->assertSame('0888888888', $auditoria->new_values['telefono']);
    }
}