<?php

namespace Tests\Feature;

use App\Models\HistoriaClinica;
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

    public function test_abre_una_historia_general_y_redirige_a_la_primera_consulta(): void
    {
        $paciente = Paciente::factory()->create();

        $response = $this->post(route('historias.store', $paciente), [
            'fecha_apertura' => now()->toDateString(),
            'tipo_vigencia' => 'general',
        ]);

        $historia = $paciente->fresh()->historiaClinicaVigente;

        $this->assertNotNull($historia);
        $this->assertSame('general', $historia->tipo_vigencia);
        $this->assertSame(
            now()->addDays(365)->toDateString(),
            $historia->fecha_vencimiento->toDateString(),
        );
        $response->assertRedirect(route('consultas.create', $historia));
    }

    public function test_abre_una_historia_por_embarazo_con_vencimiento_en_la_fpp(): void
    {
        $paciente = Paciente::factory()->create();
        $fpp = now()->addMonths(5)->toDateString();

        $this->post(route('historias.store', $paciente), [
            'fecha_apertura' => now()->toDateString(),
            'tipo_vigencia' => 'embarazo',
            'fecha_probable_parto' => $fpp,
        ]);

        $historia = $paciente->fresh()->historiaClinicaVigente;

        $this->assertSame('embarazo', $historia->tipo_vigencia);
        $this->assertSame($fpp, $historia->fecha_vencimiento->toDateString());
    }

    public function test_requiere_fecha_probable_de_parto_si_la_vigencia_es_embarazo(): void
    {
        $paciente = Paciente::factory()->create();

        $response = $this->post(route('historias.store', $paciente), [
            'fecha_apertura' => now()->toDateString(),
            'tipo_vigencia' => 'embarazo',
        ]);

        $response->assertSessionHasErrors('fecha_probable_parto');
    }

    public function test_no_permite_abrir_otra_historia_mientras_haya_una_vigente(): void
    {
        $paciente = Paciente::factory()->create();
        $historiaVigente = HistoriaClinica::factory()->create(['paciente_id' => $paciente->id]);

        $response = $this->post(route('historias.store', $paciente), [
            'fecha_apertura' => now()->toDateString(),
            'tipo_vigencia' => 'general',
        ]);

        $this->assertDatabaseCount('historia_clinicas', 1);
        $response->assertRedirect(route('historias.show', $historiaVigente));
    }

    public function test_permite_abrir_una_historia_nueva_si_la_anterior_ya_vencio(): void
    {
        $paciente = Paciente::factory()->create();
        HistoriaClinica::factory()->vencida()->create(['paciente_id' => $paciente->id]);

        $response = $this->post(route('historias.store', $paciente), [
            'fecha_apertura' => now()->toDateString(),
            'tipo_vigencia' => 'general',
        ]);

        $this->assertDatabaseCount('historia_clinicas', 2);
        $response->assertRedirect(route('consultas.create', $paciente->fresh()->historiaClinicaVigente));
    }
}
