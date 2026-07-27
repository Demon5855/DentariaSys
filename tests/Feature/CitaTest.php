<?php

namespace Tests\Feature;

use App\Models\Cita;
use App\Models\Paciente;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CitaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    private function recepcion(): User
    {
        $usuario = User::factory()->create();
        $usuario->assignRole('recepcion');

        return $usuario;
    }

    private function odontologo(): User
    {
        $usuario = User::factory()->create();
        $usuario->assignRole('odontologo');

        return $usuario;
    }

    public function test_recepcion_agenda_una_cita(): void
    {
        $paciente = Paciente::factory()->create();
        $odontologo = $this->odontologo();

        $response = $this->actingAs($this->recepcion())->post(route('citas.store'), [
            'paciente_id' => $paciente->id,
            'profesional_id' => $odontologo->id,
            'fecha_hora' => now()->addDay()->setTime(10, 0)->format('Y-m-d\TH:i'),
            'duracion_minutos' => 30,
            'motivo' => 'Control de rutina',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('citas', ['paciente_id' => $paciente->id, 'estado' => 'pendiente']);
    }

    public function test_rechaza_cita_solapada_con_otra_del_mismo_profesional(): void
    {
        $odontologo = $this->odontologo();
        $paciente = Paciente::factory()->create();
        $inicio = now()->addDay()->setTime(10, 0);

        Cita::factory()->create([
            'paciente_id' => $paciente->id,
            'profesional_id' => $odontologo->id,
            'fecha_hora' => $inicio,
            'duracion_minutos' => 30, // ocupa 10:00–10:30
        ]);

        $response = $this->actingAs($this->recepcion())->post(route('citas.store'), [
            'paciente_id' => Paciente::factory()->create()->id,
            'profesional_id' => $odontologo->id,
            'fecha_hora' => $inicio->copy()->addMinutes(15)->format('Y-m-d\TH:i'), // 10:15, se solapa
            'duracion_minutos' => 30,
        ]);

        $response->assertSessionHasErrors('fecha_hora');
    }

    public function test_permite_cita_consecutiva_sin_solapar(): void
    {
        $odontologo = $this->odontologo();
        $paciente = Paciente::factory()->create();
        $inicio = now()->addDay()->setTime(10, 0);

        Cita::factory()->create([
            'paciente_id' => $paciente->id,
            'profesional_id' => $odontologo->id,
            'fecha_hora' => $inicio,
            'duracion_minutos' => 30, // ocupa 10:00–10:30
        ]);

        $response = $this->actingAs($this->recepcion())->post(route('citas.store'), [
            'paciente_id' => Paciente::factory()->create()->id,
            'profesional_id' => $odontologo->id,
            'fecha_hora' => $inicio->copy()->addMinutes(30)->format('Y-m-d\TH:i'), // 10:30 en punto, no se solapa
            'duracion_minutos' => 30,
        ]);

        $response->assertSessionHasNoErrors();
    }

    public function test_una_cita_cancelada_no_bloquea_el_horario(): void
    {
        $odontologo = $this->odontologo();
        $paciente = Paciente::factory()->create();
        $inicio = now()->addDay()->setTime(10, 0);

        Cita::factory()->create([
            'paciente_id' => $paciente->id,
            'profesional_id' => $odontologo->id,
            'fecha_hora' => $inicio,
            'duracion_minutos' => 30,
            'estado' => 'cancelada',
        ]);

        $response = $this->actingAs($this->recepcion())->post(route('citas.store'), [
            'paciente_id' => Paciente::factory()->create()->id,
            'profesional_id' => $odontologo->id,
            'fecha_hora' => $inicio->format('Y-m-d\TH:i'), // mismo horario exacto
            'duracion_minutos' => 30,
        ]);

        $response->assertSessionHasNoErrors();
    }

    public function test_transicion_valida_pendiente_a_confirmada(): void
    {
        $cita = Cita::factory()->create(['estado' => 'pendiente']);

        $response = $this->actingAs($this->recepcion())
            ->patch(route('citas.cambiar-estado', $cita), ['estado' => 'confirmada']);

        $response->assertSessionHasNoErrors();
        $this->assertSame('confirmada', $cita->fresh()->estado);
    }

    public function test_transicion_invalida_pendiente_a_atendida_directamente(): void
    {
        $cita = Cita::factory()->create(['estado' => 'pendiente']);

        $response = $this->actingAs($this->recepcion())
            ->patch(route('citas.cambiar-estado', $cita), ['estado' => 'atendida']);

        $response->assertSessionHasErrors('estado');
        $this->assertSame('pendiente', $cita->fresh()->estado);
    }

    public function test_una_cita_atendida_es_terminal(): void
    {
        $cita = Cita::factory()->create(['estado' => 'atendida']);

        $response = $this->actingAs($this->recepcion())
            ->patch(route('citas.cambiar-estado', $cita), ['estado' => 'confirmada']);

        $response->assertSessionHasErrors('estado');
    }

    public function test_odontologo_puede_ver_pero_no_gestionar_la_agenda(): void
    {
        $cita = Cita::factory()->create(['estado' => 'pendiente']);
        $odontologo = $this->odontologo();

        $this->actingAs($odontologo)->get(route('citas.index'))->assertOk();

        $this->actingAs($odontologo)
            ->get(route('citas.edit', $cita))
            ->assertForbidden();

        $this->actingAs($odontologo)
            ->patch(route('citas.cambiar-estado', $cita), ['estado' => 'confirmada'])
            ->assertForbidden();
    }

    public function test_auxiliar_no_puede_agendar_citas(): void
    {
        $auxiliar = User::factory()->create();
        $auxiliar->assignRole('auxiliar');

        $this->actingAs($auxiliar)
            ->get(route('citas.create'))
            ->assertForbidden();
    }
}
