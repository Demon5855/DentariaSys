<?php

namespace App\Policies;

use App\Models\Odontograma;
use App\Models\User;

class OdontogramaPolicy
{
    public function view(User $user, Odontograma $odontograma): bool
    {
        return $user->can('odontogramas.ver');
    }

    public function create(User $user): bool
    {
        return $user->can('odontogramas.crear');
    }
}
