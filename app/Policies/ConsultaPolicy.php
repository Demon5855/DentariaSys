<?php

namespace App\Policies;

use App\Models\Consulta;
use App\Models\User;

class ConsultaPolicy
{
    public function view(User $user, Consulta $consulta): bool
    {
        return $user->can('consultas.ver');
    }

    public function create(User $user): bool
    {
        return $user->can('consultas.crear');
    }
}
