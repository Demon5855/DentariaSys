<?php

namespace App\Policies;

use App\Models\HistoriaClinica;
use App\Models\User;

class HistoriaClinicaPolicy
{
    public function view(User $user, HistoriaClinica $historiaClinica): bool
    {
        return $user->can('historias.ver');
    }

    /**
     * "Crear" aquí es abrir la carpeta. El instructivo del 033 es explícito:
     * el formulario debe ser llenado por profesionales odontólogos. Recepción
     * y auxiliar quedan fuera de esta acción aunque puedan ver el resultado.
     */
    public function create(User $user): bool
    {
        return $user->can('historias.abrir');
    }
}
