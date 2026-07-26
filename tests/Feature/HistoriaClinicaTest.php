<?php

namespace Tests\Feature;

use App\Models\Paciente;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HistoriaClinicaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);

        $usuario = User::factory()->create();
        $usuario->assignRole('admin');
        $this->actingAs($usuario);
    }

    public function test_abre_una_historia_clinica_y_redirige_a_la_primera_consulta(): void
    {
        $paciente = Paciente::factory()->create();

        $response = $this->post(route('historias.store', $paciente), [
            'fecha_apertura' => now()->toDateString(),
        ]);

        $this->assertDatabaseHas('historia_clinicas', ['paciente_id' => $paciente->id]);
        $response->assertRedirect(route('consultas.create', $paciente->historiaClinica));
    }

    public function test_no_permite_abrir_dos_historias_para_el_mismo_paciente(): void
    {
        $paciente = Paciente::factory()->create();
        $paciente->historiaClinica()->create(['fecha_apertura' => now()]);

        $response = $this->post(route('historias.store', $paciente), [
            'fecha_apertura' => now()->toDateString(),
        ]);

        $this->assertDatabaseCount('historia_clinicas', 1);
        $response->assertRedirect(route('historias.show', $paciente->historiaClinica));
    }
}
