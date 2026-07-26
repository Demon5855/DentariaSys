<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CrearAdminCommand extends Command
{
    /**
     * Interactivo a propósito: un seeder con contraseña fija es exactamente
     * el error que ya tuvimos con la base de datos de Postgres en el
     * historial de git. La primera cuenta de administrador no debe vivir
     * en un archivo versionado.
     */
    protected $signature = 'admin:crear';

    protected $description = 'Crea la primera cuenta de administrador del sistema';

    public function handle(): int
    {
        \Spatie\Permission\Models\Role::findOrCreate('admin');

        if (User::role('admin')->exists()) {
            $this->warn('Ya existe al menos un administrador. Usa el panel /admin/usuarios para crear más cuentas.');

            return self::FAILURE;
        }

        $name = $this->ask('Nombre completo');
        $email = $this->ask('Correo electrónico');
        $password = $this->secret('Contraseña (no se mostrará en pantalla)');
        $passwordConfirmation = $this->secret('Confirma la contraseña');

        $validador = Validator::make(
            compact('name', 'email', 'password', 'passwordConfirmation'),
            [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255', 'unique:users,email'],
                'password' => ['required', 'string', 'min:8'],
            ]
        );

        if ($validador->fails()) {
            foreach ($validador->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        if ($password !== $passwordConfirmation) {
            $this->error('Las contraseñas no coinciden.');

            return self::FAILURE;
        }

        $usuario = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'email_verified_at' => now(),
        ]);

        $usuario->assignRole('admin');

        $this->info("Administrador '{$usuario->name}' creado exitosamente.");

        return self::SUCCESS;
    }
}
