<?php

namespace Tests\Feature;

use App\Models\Condicion;
use App\Models\HistoriaClinica;
use App\Models\User;
use Database\Seeders\CondicionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OdontogramaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
        $this->seed(CondicionSeeder::class);
    }

    private function odontologo(): User
    {
        $usuario = User::factory()->create();
        $usuario->assignRole('odontologo');

        return $usuario;
    }

    public function test_registra_un_odontograma_con_caries_en_superficie(): void
    {
        $historiaClinica = HistoriaClinica::factory()->create();
        $caries = Condicion::where('clave', 'caries')->firstOrFail();

        $response = $this->actingAs($this->odontologo())
            ->post(route('odontogramas.store', $historiaClinica), [
                'tipo' => 'inicial',
                'denticion' => 'permanente',
                'fecha' => now()->toDateString(),
                'hallazgos' => [
                    ['pieza' => 16, 'condicion_id' => $caries->id, 'superficie' => 'oclusal'],
                ],
            ]);

        $odontograma = $historiaClinica->odontogramas()->firstOrFail();
        $response->assertRedirect(route('odontogramas.show', $odontograma));

        $this->assertDatabaseHas('odontograma_piezas', ['odontograma_id' => $odontograma->id, 'pieza' => 16]);
        $this->assertSame(1, $odontograma->cpod_c);
        $this->assertSame(0, $odontograma->cpod_p);
        $this->assertSame(0, $odontograma->cpod_o);
    }

    public function test_caries_sin_superficie_falla_validacion(): void
    {
        $historiaClinica = HistoriaClinica::factory()->create();
        $caries = Condicion::where('clave', 'caries')->firstOrFail();

        $response = $this->actingAs($this->odontologo())
            ->post(route('odontogramas.store', $historiaClinica), [
                'tipo' => 'inicial',
                'denticion' => 'permanente',
                'fecha' => now()->toDateString(),
                'hallazgos' => [
                    ['pieza' => 16, 'condicion_id' => $caries->id], // sin 'superficie'
                ],
            ]);

        $response->assertSessionHasErrors('hallazgos.0.superficie');
    }

    public function test_corona_con_superficie_falla_validacion(): void
    {
        $historiaClinica = HistoriaClinica::factory()->create();
        $corona = Condicion::where('clave', 'corona')->firstOrFail();

        $response = $this->actingAs($this->odontologo())
            ->post(route('odontogramas.store', $historiaClinica), [
                'tipo' => 'inicial',
                'denticion' => 'permanente',
                'fecha' => now()->toDateString(),
                'hallazgos' => [
                    ['pieza' => 16, 'condicion_id' => $corona->id, 'superficie' => 'oclusal'], // corona es de pieza, no de superficie
                ],
            ]);

        $response->assertSessionHasErrors('hallazgos.0.superficie');
    }

    public function test_precedencia_perdida_sobre_caries_sobre_obturado(): void
    {
        $historiaClinica = HistoriaClinica::factory()->create();
        $caries = Condicion::where('clave', 'caries')->firstOrFail();
        $perdida = Condicion::where('clave', 'perdida_caries')->firstOrFail();

        // Pieza 16 con caries Y pérdida a la vez: debe contar como P, no C.
        $this->actingAs($this->odontologo())->post(route('odontogramas.store', $historiaClinica), [
            'tipo' => 'inicial',
            'denticion' => 'permanente',
            'fecha' => now()->toDateString(),
            'hallazgos' => [
                ['pieza' => 16, 'condicion_id' => $caries->id, 'superficie' => 'oclusal'],
                ['pieza' => 16, 'condicion_id' => $perdida->id],
            ],
        ]);

        $odontograma = $historiaClinica->odontogramas()->firstOrFail();

        $this->assertSame(0, $odontograma->cpod_c);
        $this->assertSame(1, $odontograma->cpod_p);
        $this->assertSame(0, $odontograma->cpod_o);
    }

    public function test_endodoncia_por_realizar_cuenta_como_cariada_y_realizada_como_obturada(): void
    {
        $historiaClinica = HistoriaClinica::factory()->create();
        $endoInd = Condicion::where('clave', 'endodoncia_ind')->firstOrFail();
        $endoReal = Condicion::where('clave', 'endodoncia_real')->firstOrFail();

        $this->actingAs($this->odontologo())->post(route('odontogramas.store', $historiaClinica), [
            'tipo' => 'inicial',
            'denticion' => 'permanente',
            'fecha' => now()->toDateString(),
            'hallazgos' => [
                ['pieza' => 14, 'condicion_id' => $endoInd->id],
                ['pieza' => 15, 'condicion_id' => $endoReal->id],
            ],
        ]);

        $odontograma = $historiaClinica->odontogramas()->firstOrFail();

        $this->assertSame(1, $odontograma->cpod_c, 'endodoncia por realizar debe contar como cariada');
        $this->assertSame(1, $odontograma->cpod_o, 'endodoncia realizada debe contar como obturada');
    }

    public function test_protesis_total_excluye_terceros_molares_del_indice(): void
    {
        $historiaClinica = HistoriaClinica::factory()->create();
        $protesisTotal = Condicion::where('clave', 'protesis_total')->firstOrFail();

        $this->actingAs($this->odontologo())->post(route('odontogramas.store', $historiaClinica), [
            'tipo' => 'inicial',
            'denticion' => 'permanente',
            'fecha' => now()->toDateString(),
            'hallazgos' => [
                ['pieza' => 18, 'condicion_id' => $protesisTotal->id], // tercer molar: NO cuenta
                ['pieza' => 14, 'condicion_id' => $protesisTotal->id], // sí cuenta
            ],
        ]);

        $odontograma = $historiaClinica->odontogramas()->firstOrFail();

        $this->assertSame(1, $odontograma->cpod_p, 'solo la pieza 14 debe contar; la 18 es tercer molar');
    }

    public function test_ceod_usa_e_en_vez_de_p_para_perdida_en_dentadura_temporal(): void
    {
        $historiaClinica = HistoriaClinica::factory()->create();
        $perdida = Condicion::where('clave', 'perdida_caries')->firstOrFail();

        $this->actingAs($this->odontologo())->post(route('odontogramas.store', $historiaClinica), [
            'tipo' => 'inicial',
            'denticion' => 'temporal',
            'fecha' => now()->toDateString(),
            'hallazgos' => [
                ['pieza' => 55, 'condicion_id' => $perdida->id],
            ],
        ]);

        $odontograma = $historiaClinica->odontogramas()->firstOrFail();

        $this->assertSame(1, $odontograma->ceod_e);
        $this->assertSame(0, $odontograma->ceod_c);
        $this->assertSame(0, $odontograma->cpod_p, 'no debe contarse en el índice de definitivas');
    }

    public function test_movilidad_y_recesion_solo_se_aceptan_en_piezas_permanentes(): void
    {
        $historiaClinica = HistoriaClinica::factory()->create();

        $response = $this->actingAs($this->odontologo())->post(route('odontogramas.store', $historiaClinica), [
            'tipo' => 'inicial',
            'denticion' => 'temporal',
            'fecha' => now()->toDateString(),
            'periodontal' => [
                ['pieza' => 55, 'movilidad' => 1], // pieza temporal: inválida aquí
            ],
        ]);

        $response->assertSessionHasErrors('periodontal.0.pieza');
    }

    public function test_no_permite_dos_odontogramas_iniciales_en_la_misma_historia(): void
    {
        $historiaClinica = HistoriaClinica::factory()->create();
        $odontologo = $this->odontologo();

        $this->actingAs($odontologo)->post(route('odontogramas.store', $historiaClinica), [
            'tipo' => 'inicial', 'denticion' => 'permanente', 'fecha' => now()->toDateString(),
        ]);

        $response = $this->actingAs($odontologo)->post(route('odontogramas.store', $historiaClinica), [
            'tipo' => 'inicial', 'denticion' => 'permanente', 'fecha' => now()->toDateString(),
        ]);

        $response->assertSessionHasErrors('tipo');
        $this->assertDatabaseCount('odontogramas', 1);
    }

    public function test_permite_odontograma_evolutivo_despues_del_inicial(): void
    {
        $historiaClinica = HistoriaClinica::factory()->create();
        $odontologo = $this->odontologo();

        $this->actingAs($odontologo)->post(route('odontogramas.store', $historiaClinica), [
            'tipo' => 'inicial', 'denticion' => 'permanente', 'fecha' => now()->toDateString(),
        ]);

        $response = $this->actingAs($odontologo)->post(route('odontogramas.store', $historiaClinica), [
            'tipo' => 'evolutivo', 'denticion' => 'permanente', 'fecha' => now()->toDateString(),
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseCount('odontogramas', 2);
    }

    public function test_no_existe_ruta_de_edicion(): void
    {
        $historiaClinica = HistoriaClinica::factory()->create();
        $odontologo = $this->odontologo();

        $this->actingAs($odontologo)->post(route('odontogramas.store', $historiaClinica), [
            'tipo' => 'inicial', 'denticion' => 'permanente', 'fecha' => now()->toDateString(),
        ]);

        $odontograma = $historiaClinica->odontogramas()->firstOrFail();

        $this->assertFalse(\Illuminate\Support\Facades\Route::has('odontogramas.update'));
        $this->assertFalse(\Illuminate\Support\Facades\Route::has('odontogramas.edit'));
    }

    public function test_no_permite_registrar_odontograma_sobre_historia_vencida(): void
    {
        $historiaClinica = HistoriaClinica::factory()->vencida()->create();
        $odontologo = $this->odontologo();

        $this->actingAs($odontologo)
            ->get(route('odontogramas.create', $historiaClinica))
            ->assertRedirect(route('historias.show', $historiaClinica));

        $response = $this->actingAs($odontologo)->post(route('odontogramas.store', $historiaClinica), [
            'tipo' => 'inicial', 'denticion' => 'permanente', 'fecha' => now()->toDateString(),
        ]);

        $response->assertRedirect(route('historias.show', $historiaClinica));
        $this->assertDatabaseCount('odontogramas', 0);
    }

    public function test_recepcion_no_puede_ver_ni_crear_odontogramas(): void
    {
        $recepcion = User::factory()->create();
        $recepcion->assignRole('recepcion');
        $historiaClinica = HistoriaClinica::factory()->create();

        $this->actingAs($recepcion)
            ->get(route('odontogramas.create', $historiaClinica))
            ->assertForbidden();
    }

    public function test_auxiliar_puede_ver_pero_no_crear_odontograma(): void
    {
        $auxiliar = User::factory()->create();
        $auxiliar->assignRole('auxiliar');
        $odontograma = \App\Models\Odontograma::factory()->create();

        $this->actingAs($auxiliar)
            ->get(route('odontogramas.show', $odontograma))
            ->assertOk();

        $this->actingAs($auxiliar)
            ->get(route('odontogramas.create', $odontograma->historiaClinica))
            ->assertForbidden();
    }
}
