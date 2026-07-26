<?php

namespace Tests\Feature;

use App\Models\HistoriaClinica;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConsultaTest extends TestCase
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

    public function test_registra_una_consulta_dentro_de_la_historia_clinica(): void
    {
        $historiaClinica = HistoriaClinica::factory()->create();

        $response = $this->post(route('consultas.store', $historiaClinica), [
            'fecha' => now()->toDateString(),
            'motivo_consulta' => 'Dolor en molar inferior derecho',
        ]);

        $response->assertRedirect(route('historias.show', $historiaClinica));
        $this->assertDatabaseHas('consultas', [
            'historia_clinica_id' => $historiaClinica->id,
            'motivo_consulta' => 'Dolor en molar inferior derecho',
        ]);
    }

    public function test_requiere_motivo_de_consulta(): void
    {
        $historiaClinica = HistoriaClinica::factory()->create();

        $response = $this->post(route('consultas.store', $historiaClinica), [
            'fecha' => now()->toDateString(),
        ]);

        $response->assertSessionHasErrors('motivo_consulta');
    }

    public function test_muestra_una_consulta(): void
    {
        $historiaClinica = HistoriaClinica::factory()->create();
        $consulta = $historiaClinica->consultas()->create([
            'fecha' => now(),
            'motivo_consulta' => 'Control de rutina',
        ]);

        $response = $this->get(route('consultas.show', $consulta));

        $response->assertOk();
        $response->assertSee('Control de rutina');
    }

    public function test_no_permite_registrar_consulta_sobre_historia_vencida(): void
    {
        $historiaClinica = HistoriaClinica::factory()->vencida()->create();

        $this->get(route('consultas.create', $historiaClinica))
            ->assertRedirect(route('historias.show', $historiaClinica));

        $response = $this->post(route('consultas.store', $historiaClinica), [
            'fecha' => now()->toDateString(),
            'motivo_consulta' => 'Intento sobre historia vencida',
        ]);

        $response->assertRedirect(route('historias.show', $historiaClinica));
        $this->assertDatabaseCount('consultas', 0);
    }
}
