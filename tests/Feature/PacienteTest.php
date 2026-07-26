<?php

namespace Tests\Feature;

use App\Models\Paciente;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PacienteTest extends TestCase
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
            'tipo_documento' => 'cedula',
            'numero_documento' => '1710034065',
            'primer_nombre' => 'Ana',
            'primer_apellido' => 'Torres',
            'sexo' => 'M',
            'fecha_nacimiento' => '1990-05-10',
        ];

        $response = $this->post(route('pacientes.store'), $datos);

        $response->assertRedirect(route('pacientes.index'));
        $this->assertDatabaseHas('pacientes', ['primer_nombre' => 'Ana', 'primer_apellido' => 'Torres']);
    }

    public function test_rechaza_nombre_con_numeros(): void
    {
        $response = $this->post(route('pacientes.store'), [
            'tipo_documento' => 'cedula',
            'numero_documento' => '1710034065',
            'primer_nombre' => 'Ana123',
            'primer_apellido' => 'Torres',
            'sexo' => 'M',
            'fecha_nacimiento' => '1990-05-10',
        ]);

        $response->assertSessionHasErrors('primer_nombre');
    }

    public function test_rechaza_cedula_con_digito_verificador_invalido(): void
    {
        $response = $this->post(route('pacientes.store'), [
            'tipo_documento' => 'cedula',
            'numero_documento' => '1710034066', // dígito verificador correcto es 5, no 6
            'primer_nombre' => 'Ana',
            'primer_apellido' => 'Torres',
            'sexo' => 'M',
            'fecha_nacimiento' => '1990-05-10',
        ]);

        $response->assertSessionHasErrors('numero_documento');
    }

    public function test_documento_temporal_se_genera_automaticamente(): void
    {
        $response = $this->post(route('pacientes.store'), [
            'tipo_documento' => 'temporal',
            'primer_nombre' => 'Sin',
            'primer_apellido' => 'Documento',
            'sexo' => 'H',
            'fecha_nacimiento' => '2000-01-01',
        ]);

        $response->assertRedirect(route('pacientes.index'));

        $paciente = Paciente::where('primer_nombre', 'Sin')->first();
        $this->assertNotNull($paciente->numero_documento);
        $this->assertSame(17, strlen($paciente->numero_documento));
    }

    public function test_menor_de_edad_requiere_representante_legal(): void
    {
        $response = $this->post(route('pacientes.store'), [
            'tipo_documento' => 'cedula',
            'numero_documento' => '1710034065',
            'primer_nombre' => 'Niño',
            'primer_apellido' => 'Prueba',
            'sexo' => 'H',
            'fecha_nacimiento' => now()->subYears(10)->toDateString(),
        ]);

        $response->assertSessionHasErrors([
            'representante_nombre', 'representante_documento',
            'representante_parentesco', 'representante_telefono',
        ]);
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
