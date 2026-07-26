<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_no_existe_ruta_publica_de_registro(): void
    {
        $this->get('/register')->assertNotFound();
    }

    public function test_un_admin_puede_crear_personal_con_rol(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'Nueva Odontóloga',
            'email' => 'odontologa@dentariasys.test',
            'password' => 'contrasena-segura',
            'password_confirmation' => 'contrasena-segura',
            'role' => 'odontologo',
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', ['email' => 'odontologa@dentariasys.test']);

        $creado = User::where('email', 'odontologa@dentariasys.test')->first();
        $this->assertTrue($creado->hasRole('odontologo'));
    }

    public function test_un_usuario_sin_rol_admin_no_puede_gestionar_usuarios(): void
    {
        $odontologo = User::factory()->create();
        $odontologo->assignRole('odontologo');

        $this->actingAs($odontologo)
            ->get(route('admin.users.index'))
            ->assertForbidden();

        $this->actingAs($odontologo)
            ->post(route('admin.users.store'), [
                'name' => 'Intento',
                'email' => 'intento@dentariasys.test',
                'password' => 'contrasena-segura',
                'password_confirmation' => 'contrasena-segura',
                'role' => 'admin',
            ])
            ->assertForbidden();
    }

    public function test_un_admin_no_puede_eliminar_su_propia_cuenta(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->delete(route('admin.users.destroy', $admin));

        $response->assertSessionHasErrors('user');
        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }
}
