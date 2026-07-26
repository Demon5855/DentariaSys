<?php

namespace Tests\Feature;

use App\Models\Paciente;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PacienteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs(User::factory()->create());
    }

    public function test_lista_solo_pacientes_activos_por_defecto(): void
    {
        Paciente::factory()->count(2)->create();
        Paciente::factory()->inactivo()->create();

        $response = $this->get(route('pacientes.index'));

        $response->assertOk();
        $response->assertViewHas('totalActivos', 2);
        $response->assertViewHas('totalInactivos', 1);
    }

    public function test_busca_por_nombre_o_apellido(): void
    {
        $coincide = Paciente::factory()->create(['primer_nombre' => 'Valentina']);
        Paciente::factory()->create(['primer_nombre' => 'Roberto']);

        $response = $this->get(route('pacientes.index', ['buscar' => 'valen']));

        $response->assertOk();
        $response->assertSee($coincide->nombre_completo);
        $response->assertDontSee('Roberto');
    }

    public function test_crea_un_paciente_valido(): void
    {
        $datos = [
            'primer_nombre' => 'Ana',
            'primer_apellido' => 'Torres',
            'fecha_nacimiento' => '1990-05-10',
        ];

        $response = $this->post(route('pacientes.store'), $datos);

        $response->assertRedirect(route('pacientes.index'));
        $this->assertDatabaseHas('pacientes', ['primer_nombre' => 'Ana', 'primer_apellido' => 'Torres']);
    }

    public function test_rechaza_nombre_con_numeros(): void
    {
        $response = $this->post(route('pacientes.store'), [
            'primer_nombre' => 'Ana123',
            'primer_apellido' => 'Torres',
            'fecha_nacimiento' => '1990-05-10',
        ]);

        $response->assertSessionHasErrors('primer_nombre');
    }

    public function test_muestra_el_perfil_de_un_paciente(): void
    {
        $paciente = Paciente::factory()->create();

        $response = $this->get(route('pacientes.show', $paciente));

        $response->assertOk();
        $response->assertSee($paciente->nombre_completo);
    }

    public function test_desactivar_y_reactivar_paciente(): void
    {
        $paciente = Paciente::factory()->create();

        $this->delete(route('pacientes.destroy', $paciente))
            ->assertRedirect(route('pacientes.index'));
        $this->assertDatabaseHas('pacientes', ['id' => $paciente->id, 'activo' => false]);

        $this->patch(route('pacientes.restore', $paciente))
            ->assertRedirect(route('pacientes.index', ['estado' => 'inactivos']));
        $this->assertDatabaseHas('pacientes', ['id' => $paciente->id, 'activo' => true]);
    }
}
