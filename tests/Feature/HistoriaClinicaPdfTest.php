<?php

namespace Tests\Feature;

use App\Models\Condicion;
use App\Models\DiagnosticoCie10;
use App\Models\HistoriaClinica;
use App\Models\User;
use Database\Seeders\CondicionSeeder;
use Database\Seeders\DiagnosticoCie10Seeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HistoriaClinicaPdfTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
        $this->seed(CondicionSeeder::class);
        $this->seed(DiagnosticoCie10Seeder::class);
    }

    private function odontologo(): User
    {
        $usuario = User::factory()->create();
        $usuario->assignRole('odontologo');

        return $usuario;
    }

    public function test_descarga_el_pdf_de_una_historia_recien_abierta_sin_datos(): void
    {
        $historiaClinica = HistoriaClinica::factory()->create();
        $odontologo = $this->odontologo();

        $response = $this->actingAs($odontologo)->get(route('historias.pdf', $historiaClinica));

        $response->assertOk();
        $this->assertStringContainsString('application/pdf', $response->headers->get('content-type'));
    }

    public function test_descarga_el_pdf_con_consulta_diagnostico_y_tratamiento(): void
    {
        $historiaClinica = HistoriaClinica::factory()->create();
        $odontologo = $this->odontologo();
        $caries = DiagnosticoCie10::where('codigo', 'K02.9')->firstOrFail();

        $this->actingAs($odontologo)->post(route('consultas.store', $historiaClinica), [
            'fecha' => now()->toDateString(),
            'motivo_consulta' => 'Dolor dental',
            'diagnosticos' => [
                ['diagnostico_cie10_id' => $caries->id, 'descripcion' => 'Caries pieza 36', 'estado' => 'definitivo'],
            ],
            'tratamientos' => [
                ['procedimiento' => 'Obturación pieza 36', 'estado' => 'alta'],
            ],
        ]);

        $caries2 = Condicion::where('clave', 'caries')->firstOrFail();
        $this->actingAs($odontologo)->post(route('odontogramas.store', $historiaClinica), [
            'tipo' => 'inicial',
            'denticion' => 'permanente',
            'fecha' => now()->toDateString(),
            'hallazgos' => [
                ['pieza' => 36, 'condicion_id' => $caries2->id, 'superficie' => 'oclusal'],
            ],
        ]);

        $response = $this->actingAs($odontologo)->get(route('historias.pdf', $historiaClinica));

        $response->assertOk();
        $this->assertStringContainsString('application/pdf', $response->headers->get('content-type'));
    }

    public function test_recepcion_no_puede_descargar_el_pdf(): void
    {
        $recepcion = User::factory()->create();
        $recepcion->assignRole('recepcion');
        $historiaClinica = HistoriaClinica::factory()->create();

        $this->actingAs($recepcion)
            ->get(route('historias.pdf', $historiaClinica))
            ->assertForbidden();
    }

    public function test_auxiliar_puede_descargar_el_pdf(): void
    {
        $auxiliar = User::factory()->create();
        $auxiliar->assignRole('auxiliar');
        $historiaClinica = HistoriaClinica::factory()->create();

        $this->actingAs($auxiliar)
            ->get(route('historias.pdf', $historiaClinica))
            ->assertOk();
    }
}
