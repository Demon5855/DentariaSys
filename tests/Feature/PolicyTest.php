<?php

namespace Tests\Feature;

use App\Models\HistoriaClinica;
use App\Models\Paciente;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PolicyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    private function usuario(string $rol): User
    {
        $usuario = User::factory()->create();
        $usuario->assignRole($rol);

        return $usuario;
    }

    public function test_recepcion_puede_ver_y_crear_pacientes(): void
    {
        $recepcion = $this->usuario('recepcion');

        $this->actingAs($recepcion)->get(route('pacientes.index'))->assertOk();

        $this->actingAs($recepcion)->post(route('pacientes.store'), [
            'primer_nombre' => 'Ana',
            'primer_apellido' => 'Torres',
            'fecha_nacimiento' => '1990-01-01',
        ])->assertRedirect(route('pacientes.index'));
    }

    public function test_recepcion_no_puede_abrir_ni_ver_historias_clinicas(): void
    {
        $recepcion = $this->usuario('recepcion');
        $paciente = Paciente::factory()->create();

        $this->actingAs($recepcion)
            ->get(route('historias.create', $paciente))
            ->assertForbidden();

        $historiaClinica = HistoriaClinica::factory()->create(['paciente_id' => $paciente->id]);

        $this->actingAs($recepcion)
            ->get(route('historias.show', $historiaClinica))
            ->assertForbidden();
    }

    public function test_auxiliar_puede_ver_historia_pero_no_registrar_consulta(): void
    {
        $auxiliar = $this->usuario('auxiliar');
        $historiaClinica = HistoriaClinica::factory()->create();

        $this->actingAs($auxiliar)
            ->get(route('historias.show', $historiaClinica))
            ->assertOk();

        $this->actingAs($auxiliar)
            ->get(route('consultas.create', $historiaClinica))
            ->assertForbidden();

        $this->actingAs($auxiliar)
            ->post(route('consultas.store', $historiaClinica), [
                'fecha' => now()->toDateString(),
                'motivo_consulta' => 'Dolor',
            ])
            ->assertForbidden();
    }

    public function test_odontologo_puede_abrir_historia_y_registrar_consulta(): void
    {
        $odontologo = $this->usuario('odontologo');
        $paciente = Paciente::factory()->create();

        $this->actingAs($odontologo)
            ->post(route('historias.store', $paciente), ['fecha_apertura' => now()->toDateString()])
            ->assertRedirect(route('consultas.create', $paciente->historiaClinica));

        $this->actingAs($odontologo)
            ->post(route('consultas.store', $paciente->historiaClinica), [
                'fecha' => now()->toDateString(),
                'motivo_consulta' => 'Control',
            ])
            ->assertRedirect(route('historias.show', $paciente->historiaClinica));
    }

    public function test_recepcion_no_puede_administrar_usuarios(): void
    {
        $recepcion = $this->usuario('recepcion');

        $this->actingAs($recepcion)
            ->get(route('admin.users.index'))
            ->assertForbidden();
    }

    public function test_admin_puede_administrar_usuarios(): void
    {
        $admin = $this->usuario('admin');

        $this->actingAs($admin)
            ->get(route('admin.users.index'))
            ->assertOk();
    }
}
