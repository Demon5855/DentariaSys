<?php

namespace Tests\Feature;

use App\Models\Antecedente;
use App\Models\HistoriaClinica;
use App\Models\RegionEstomatognatica;
use App\Models\User;
use Database\Seeders\AntecedenteSeeder;
use Database\Seeders\RegionEstomatognaticaSeeder;
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
        $this->seed(AntecedenteSeeder::class);
        $this->seed(RegionEstomatognaticaSeeder::class);

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

    public function test_guarda_antecedentes_personales_y_familiares_marcados(): void
    {
        $historiaClinica = HistoriaClinica::factory()->create();
        $asma = Antecedente::where('codigo', 6)->firstOrFail();
        $diabetes = Antecedente::where('codigo', 7)->firstOrFail();

        $response = $this->post(route('consultas.store', $historiaClinica), [
            'fecha' => now()->toDateString(),
            'motivo_consulta' => 'Control',
            'antecedentes_personales_marcados' => [$asma->id],
            'antecedentes_personales' => '6. Asma leve, controlada con inhalador',
            'antecedentes_familiares_marcados' => [$diabetes->id],
            'antecedentes_familiares' => '7. Madre diabética',
        ]);

        $response->assertRedirect(route('historias.show', $historiaClinica));

        $consulta = $historiaClinica->consultas()->first();

        $this->assertTrue($consulta->antecedentesPersonalesMarcados->contains($asma));
        $this->assertTrue($consulta->antecedentesFamiliaresMarcados->contains($diabetes));
        $this->assertFalse($consulta->antecedentesPersonalesMarcados->contains($diabetes));
    }

    public function test_marcar_otros_sin_describir_falla(): void
    {
        $historiaClinica = HistoriaClinica::factory()->create();
        $otros = Antecedente::where('codigo', 10)->firstOrFail();

        $response = $this->post(route('consultas.store', $historiaClinica), [
            'fecha' => now()->toDateString(),
            'motivo_consulta' => 'Control',
            'antecedentes_personales_marcados' => [$otros->id],
            // sin 'antecedentes_personales' de texto
        ]);

        $response->assertSessionHasErrors('antecedentes_personales');
        $this->assertDatabaseCount('consultas', 0);
    }

    public function test_marcar_region_estomatognatica_sin_describir_falla(): void
    {
        $historiaClinica = HistoriaClinica::factory()->create();
        $lengua = RegionEstomatognatica::where('numero', 5)->firstOrFail();

        $response = $this->post(route('consultas.store', $historiaClinica), [
            'fecha' => now()->toDateString(),
            'motivo_consulta' => 'Control',
            'regiones_afectadas' => [$lengua->id],
            // sin 'examen_estomatognatico'
        ]);

        $response->assertSessionHasErrors('examen_estomatognatico');
    }

    public function test_guarda_region_estomatognatica_afectada_con_descripcion(): void
    {
        $historiaClinica = HistoriaClinica::factory()->create();
        $lengua = RegionEstomatognatica::where('numero', 5)->firstOrFail();

        $this->post(route('consultas.store', $historiaClinica), [
            'fecha' => now()->toDateString(),
            'motivo_consulta' => 'Control',
            'regiones_afectadas' => [$lengua->id],
            'examen_estomatognatico' => '5. Úlcera en borde lateral izquierdo',
        ]);

        $consulta = $historiaClinica->consultas()->first();

        $this->assertTrue($consulta->regionesAfectadas->contains($lengua));
    }

    public function test_consulta_sin_hallazgos_no_tiene_regiones_ni_antecedentes_marcados(): void
    {
        $historiaClinica = HistoriaClinica::factory()->create();

        $this->post(route('consultas.store', $historiaClinica), [
            'fecha' => now()->toDateString(),
            'motivo_consulta' => 'Control de rutina',
        ]);

        $consulta = $historiaClinica->consultas()->first();

        $this->assertCount(0, $consulta->antecedentesPersonalesMarcados);
        $this->assertCount(0, $consulta->antecedentesFamiliaresMarcados);
        $this->assertCount(0, $consulta->regionesAfectadas);
    }
}
