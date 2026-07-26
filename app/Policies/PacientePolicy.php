<?php

namespace App\Policies;

use App\Models\Paciente;
use App\Models\User;

class PacientePolicy
{
    /**
     * Cada método delega en un permiso de Spatie, no en el rol directamente.
     * Esto permite reasignar quién hace qué (fase futura: agregar un rol
     * "practicante" con permisos a medida) sin tocar esta clase.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('pacientes.ver');
    }

    public function view(User $user, Paciente $paciente): bool
    {
        return $user->can('pacientes.ver');
    }

    public function create(User $user): bool
    {
        return $user->can('pacientes.crear');
    }

    public function update(User $user, Paciente $paciente): bool
    {
        return $user->can('pacientes.editar');
    }

    public function delete(User $user, Paciente $paciente): bool
    {
        return $user->can('pacientes.desactivar');
    }

    public function restore(User $user, Paciente $paciente): bool
    {
        return $user->can('pacientes.desactivar');
    }
}
