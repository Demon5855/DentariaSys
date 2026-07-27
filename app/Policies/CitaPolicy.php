<?php

namespace App\Policies;

use App\Models\Cita;
use App\Models\User;

class CitaPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('citas.ver');
    }

    public function view(User $user, Cita $cita): bool
    {
        return $user->can('citas.ver');
    }

    public function create(User $user): bool
    {
        return $user->can('citas.crear');
    }

    /**
     * Cubre editar, reagendar y cualquier cambio de estado (confirmar,
     * cancelar, marcar atendida/no-asistió) — todas son la misma acción de
     * negocio: "gestionar la agenda", no vale la pena separarlas en
     * permisos distintos por ahora.
     */
    public function gestionar(User $user, Cita $cita): bool
    {
        return $user->can('citas.gestionar');
    }
}
