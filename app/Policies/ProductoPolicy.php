<?php

namespace App\Policies;

use App\Models\Producto;
use App\Models\User;

class ProductoPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('inventario.ver');
    }

    public function view(User $user, Producto $producto): bool
    {
        return $user->can('inventario.ver');
    }

    public function create(User $user): bool
    {
        return $user->can('inventario.gestionar');
    }

    public function update(User $user, Producto $producto): bool
    {
        return $user->can('inventario.gestionar');
    }

    /**
     * Cubre agregar lotes y registrar movimientos manuales (entrada,
     * merma, ajuste) — es la misma acción de negocio "gestionar
     * inventario", no vale la pena separarla en permisos distintos.
     */
    public function gestionar(User $user): bool
    {
        return $user->can('inventario.gestionar');
    }
}
